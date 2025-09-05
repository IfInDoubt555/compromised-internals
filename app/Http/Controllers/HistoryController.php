<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\SectionExtractor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Parsedown;

final class HistoryController extends Controller
{
    /**
     * Format a single podium result row into a neat string like:
     * "Walter Schock / Rolf Moll · Mercedes-Benz 220 SE · 3h42m"
     *
     * @param array<string, mixed>|null $r
     */
    private function fmtResult(?array $r): ?string
    {
        if ($r === null) {
            return null;
        }

        $bits = [];
        if (!empty($r['crew']))  { $bits[] = e((string) $r['crew']); }
        if (!empty($r['car']))   { $bits[] = e((string) $r['car']); }
        if (!empty($r['time']))  { $bits[] = e((string) $r['time']); }
        if (!empty($r['notes'])) { $bits[] = e((string) $r['notes']); }

        return $bits !== [] ? implode(' · ', $bits) : null;
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
        if (preg_match('/^\d{4}$/', $decade) === 1) {
            $decade .= 's';
        } elseif (preg_match('/^\d{4}s$/', $decade) !== 1) {
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
        if ($item === null) {
            abort(404, 'Item not found.');
        }

        // Prev/next
        $currentIndex = $collection->search(
            static fn ($e): bool => (int) ($e['id'] ?? 0) === (int) $id
        );
        $previousItem = $currentIndex !== false ? ($collection[$currentIndex - 1] ?? null) : null;
        $nextItem     = $currentIndex !== false ? ($collection[$currentIndex + 1] ?? null) : null;

        // Fallback: markdown → HTML
        if (empty($item['details_html']) && !empty($item['details'])) {
            $parsedown = new Parsedown();
            $item['details_html'] = $parsedown->text((string) $item['details']);
        }

        // ---------- Results block (events only) ----------
        $winner = $second = $third = $resultsNarrative = null;
        $secResults = false;

        if ($tab === 'events') {
            /** @var array<string,mixed>|null $winnerRaw */
            $winnerRaw = $item['results']['winner'] ?? null;
            /** @var array<string,mixed>|null $secondRaw */
            $secondRaw = $item['results']['second'] ?? null;
            /** @var array<string,mixed>|null $thirdRaw */
            $thirdRaw  = $item['results']['third'] ?? null;

            $winner = $this->fmtResult($winnerRaw);
            $second = $this->fmtResult($secondRaw);
            $third  = $this->fmtResult($thirdRaw);

            /** @var string|null $resultsNarrative */
            $resultsNarrative = $item['results']['narrative_html'] ?? null;

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

        /** @var view-string $view */
        $view = 'history.show';
        return view($view, [
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
        $decadeIn = (string) $request->query('decade', '1960s');
        $tab      = (string) $request->query('tab', 'events');
        /** @var int|string|null $year */
        $year     = $request->query('year');

        // Normalize decade to "####s"
        if (preg_match('/^\d{4}$/', $decadeIn) === 1) {
            $decade = $decadeIn . 's';
        } elseif (preg_match('/^\d{4}s$/', $decadeIn) === 1) {
            $decade = $decadeIn;
        } else {
            $decade = '1960s';
        }

        $decades     = $this->allDecades();
        $items       = $this->listItems($tab, $decade, $year !== null ? (int) $year : null);
        $years       = $this->yearsFor($decade, $tab);
        $themeDecade = (int) substr($decade, 0, 4);

        /** @var view-string $view */
        $view = 'history.bookmarks';
        return view($view, [
            'decades'     => $decades,
            'decade'      => $decade,
            'themeDecade' => $themeDecade,
            'year'        => $year,
            'tab'         => $tab,
            'items'       => $items,
            'years'       => $years,
        ]);
    }

    /**
     * Unique years available for a decade (per tab).
     *
     * @return array<int, int>
     */
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

    /**
     * Available decades.
     *
     * @return array<int, string>
     */
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
                $rows = $rows->filter(static fn ($r): bool => (int) ($r['year'] ?? 0) === $year);
            }
            return $rows->values();
        }

        // CARS/DRIVERS: stable sort
        if ($year !== null) {
            $rows = $rows->where('year', $year);
        }

        return $rows->sortBy([
            static fn ($row) => (int) ($row['year'] ?? 0),
            static fn ($row) => (string) ($row['title'] ?? $row['name'] ?? $row['model'] ?? $row['driver'] ?? ''),
        ])->values();
    }
}