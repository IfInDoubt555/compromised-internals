<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Support\SectionExtractor;
use Parsedown;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HistoryController extends Controller
{
    /**
     * Format a single podium result row into a neat string like:
     * "Walter Schock / Rolf Moll · Mercedes-Benz 220 SE · 3h42m"
     */
    private function fmtResult(?array $r): ?string
    {
        if (!$r) return null;

        $bits = [];
        if (!empty($r['crew']))  $bits[] = e($r['crew']);
        if (!empty($r['car']))   $bits[] = e($r['car']);
        if (!empty($r['time']))  $bits[] = e($r['time']);
        if (!empty($r['notes'])) $bits[] = e($r['notes']);

        return count($bits) ? implode(' · ', $bits) : null;
    }

    /**
     * Show a single item from a tab+decade JSON file.
     * URL example: /history/events/1960s/123
     */
    public function show(string $tab, string $decade, int $id): View
    {
        $validTabs = ['events', 'cars', 'drivers'];
        if (!in_array($tab, $validTabs, true)) {
            abort(404, 'Invalid tab type.');
        }

        // Normalize decade to "####s"
        if (preg_match('/^\d{4}$/', $decade)) {
            $decade .= 's';
        } elseif (!preg_match('/^\d{4}s$/', $decade)) {
            abort(404, 'Invalid decade segment.');
        }

        $filePath = public_path("data/{$tab}-{$decade}.json");
        if (!File::exists($filePath)) {
            abort(404, "Data file not found for {$tab}-{$decade}.");
        }

        /** @var array<int, array<string,mixed>>|null $data */
        $data = json_decode(File::get($filePath), true);
        if (!is_array($data)) {
            abort(500, 'Failed to decode data file.');
        }

        /** @var Collection<int, array<string,mixed>> $collection */
        $collection = collect($data)->values();

        /** @var array<string,mixed>|null $item */
        $item = $collection->firstWhere('id', (int) $id);
        if (!$item) {
            abort(404, 'Item not found.');
        }

        // Prev/next
        $currentIndex = $collection->search(fn ($e) => (int) ($e['id'] ?? 0) === (int) $id);
        $previousItem = $currentIndex !== false ? ($collection[$currentIndex - 1] ?? null) : null;
        $nextItem     = $currentIndex !== false ? ($collection[$currentIndex + 1] ?? null) : null;

        // Fallback: markdown → HTML
        if (empty($item['details_html']) && !empty($item['details'])) {
            $parsedown = new Parsedown();
            $item['details_html'] = $parsedown->text($item['details']);
        }

        // ---------- Results block (events only) ----------
        $winner = $second = $third = $resultsNarrative = null;
        $secResults = false;

        if ($tab === 'events') {
            /** @var array<string,mixed>|null $winnerRaw */
            $winnerRaw = Arr::get($item, 'results.winner');
            /** @var array<string,mixed>|null $secondRaw */
            $secondRaw = Arr::get($item, 'results.second');
            /** @var array<string,mixed>|null $thirdRaw */
            $thirdRaw  = Arr::get($item, 'results.third');

            $winner = $this->fmtResult($winnerRaw);
            $second = $this->fmtResult($secondRaw);
            $third  = $this->fmtResult($thirdRaw);

            /** @var string|null $resultsNarrative */
            $resultsNarrative = Arr::get($item, 'results.narrative_html');

            $secResults = (bool) (
                $winner || $second || $third ||
                (isset($resultsNarrative) && trim(strip_tags($resultsNarrative)) !== '')
            );
        }

        // ---------- Section extraction ----------
        $type = match ($tab) {
            'drivers' => 'drivers',
            'cars'    => 'cars',
            default   => 'events',
        };
        $sections = SectionExtractor::parse($item['details_html'] ?? null, $type);

        return view('history.show', [
            'item'             => $item,
            'tab'              => $tab,
            'decade'           => $decade,                     // "1990s"
            'themeDecade'      => (int) substr($decade, 0, 4), // 1990
            'previousItem'     => $previousItem,
            'nextItem'         => $nextItem,
            'winner'           => $winner,
            'second'           => $second,
            'third'            => $third,
            'resultsNarrative' => $resultsNarrative,
            'secResults'       => $secResults,
            'sections'         => $sections,
        ]);
    }

    public function index(Request $request): View
    {
        $decadeIn = $request->query('decade', '1960s');
        $tab      = $request->query('tab', 'events');
        /** @var int|string|null $year */
        $year     = $request->query('year');

        // Normalize decade to "####s"
        if (preg_match('/^\d{4}$/', $decadeIn)) {
            $decade = $decadeIn.'s';
        } elseif (preg_match('/^\d{4}s$/', $decadeIn)) {
            $decade = $decadeIn;
        } else {
            $decade = '1960s';
        }

        $decades     = $this->allDecades();
        $items       = $this->listItems($tab, $decade, $year !== null ? (int) $year : null);
        $years       = $this->yearsFor($decade, $tab);
        $themeDecade = (int) substr($decade, 0, 4);

        return view('history.bookmarks', [
            'decades'     => $decades,
            'decade'      => $decade,
            'themeDecade' => $themeDecade,
            'year'        => $year,
            'tab'         => $tab,
            'items'       => $items,
            'years'       => $years,
        ]);
    }

    /** Unique years available for a decade (per tab). @return array<int,int> */
    private function yearsFor(string $decade, string $tab = 'events'): array
    {
        /** @var array<int,int> $years */
        $years = $this->listItems($tab, $decade)
            ->pluck('year')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $years;
    }

    /** Available decades. @return array<int,string> */
    private function allDecades(): array
    {
        /** @var array<int,string> $decades */
        $decades = ['1960s','1970s','1980s','1990s','2000s','2010s','2020s'];
        return $decades;
    }

    /**
     * Load items for a given tab+decade from JSON and optionally filter by year.
     * Expects files like:
     *   public/data/events-1960s.json
     *   public/data/cars-1960s.json
     *   public/data/drivers-1960s.json
     *
     * @return Collection<int, array<string,mixed>>
     */
    private function listItems(string $tab, string $decade, ?int $year = null): Collection
    {
        $validTabs = ['events', 'cars', 'drivers'];
        if (!in_array($tab, $validTabs, true)) {
            /** @var Collection<int, array<string,mixed>> $empty */
            $empty = collect();
            return $empty;
        }

        $path = public_path("data/{$tab}-{$decade}.json");
        if (!File::exists($path)) {
            /** @var Collection<int, array<string,mixed>> $empty */
            $empty = collect();
            return $empty;
        }

        /** @var array<int, array<string,mixed>> $decoded */
        $decoded = json_decode(File::get($path), true) ?: [];
        /** @var Collection<int, array<string,mixed>> $rows */
        $rows = collect($decoded);

        // EVENTS: keep original ordering
        if ($tab === 'events') {
            if ($year !== null) {
                $rows = $rows->filter(fn ($r) => (int)($r['year'] ?? 0) === $year);
            }
            return $rows->values();
        }

        // CARS/DRIVERS: stable sort
        if ($year !== null) {
            $rows = $rows->where('year', $year);
        }

        return $rows->sortBy([
            fn ($row) => (int)($row['year'] ?? 0),
            fn ($row) => (string)($row['title'] ?? $row['name'] ?? $row['model'] ?? $row['driver'] ?? ''),
        ])->values();
    }
}