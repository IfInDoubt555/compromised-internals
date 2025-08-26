<?php

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CalendarExportController extends Controller
{
    public function year(Request $request, int $year)
    {
        $ics = $this->buildIcsForYear($year, $request->string('champ')->toString());
        return response($ics, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="rallies-' . $year . '.ics"',
        ]);
    }

    public function download(Request $request, int $year)
    {
        $ics = $this->buildIcsForYear($year, $request->string('champ')->toString());
        return response($ics, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="rallies-' . $year . '.ics"',
        ]);
    }

    protected function buildIcsForYear(int $year, ?string $championship = null): string
    {
        // get events that start or end in the year
        $query = RallyEvent::with(['days', 'stages'])->where(function ($q) use ($year) {
            $q->whereYear('start_date', $year)
              ->orWhereYear('end_date',   $year);
        });

        if ($championship) {
            $query->where('championship', $championship);
        }

        $events = $query->orderBy('start_date')->get();

        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'PRODID:-//Compromised Internals//Rally Calendar//EN';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:PUBLISH';
        $lines[] = 'X-WR-CALNAME:Rally ' . $year . ($championship ? " ({$championship})" : '');
        $lines[] = 'X-WR-TIMEZONE:UTC';

        $dtstamp = gmdate('Ymd\THis\Z');

        foreach ($events as $ev) {
            // Canonical event URL (prefer slug; fallback to legacy id path)
            $eventUrl = $ev->slug
                ? route('calendar.show', $ev->slug)
                : url("/calendar/events/{$ev->id}");

            // ----- Main multi-day rally all-day event -----
            $start = $ev->start_date?->copy();
            $end   = $ev->end_date?->copy();

            // DTEND for all-day is exclusive; add a day if available
            $dtStart = $start?->format('Ymd');
            $dtEnd   = $end?->copy()->addDay()->format('Ymd') ?? $start?->copy()->addDay()->format('Ymd');

            $summary = $this->escape($ev->name . ($ev->championship ? " ({$ev->championship})" : ''));
            $desc    = $this->escape(trim(collect([
                $ev->description,
                $ev->official_url ? "Official: {$ev->official_url}" : null,
                $eventUrl,
            ])->filter()->implode("\n")));

            $location = $this->escape($ev->location ?? '');

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:event-' . $ev->id . '@compromised-internals.com';
            $lines[] = 'DTSTAMP:' . $dtstamp;
            if ($dtStart) $lines[] = 'DTSTART;VALUE=DATE:' . $dtStart;
            if ($dtEnd)   $lines[] = 'DTEND;VALUE=DATE:'   . $dtEnd;
            $lines[] = 'SUMMARY:' . $summary;
            if ($location !== '') $lines[] = 'LOCATION:' . $location;
            $lines[] = 'DESCRIPTION:' . $desc;
            if ($ev->championship) $lines[] = 'CATEGORIES:' . $this->escape($ev->championship);
            $lines[] = 'URL:' . $this->escape($eventUrl);
            $lines[] = 'END:VEVENT';

            // ----- Optional: per-stage events if we have timing -----
            foreach ($ev->stages as $stage) {
                $startAt = null;

                if (isset($stage->start_at) && $stage->start_at) {
                    $startAt = Carbon::parse($stage->start_at);
                } elseif (isset($stage->start_time) && $stage->start_time) {
                    $dayDate = optional($ev->days->firstWhere('id', $stage->rally_event_day_id))->date
                               ?? $ev->start_date;
                    if ($dayDate) {
                        $startAt = Carbon::parse($dayDate . ' ' . $stage->start_time, config('app.timezone'));
                    }
                }

                if (!$startAt) {
                    continue;
                }

                $endAt = (clone $startAt)->addHour();

                $stageTitle = trim('SS' . ($stage->ss_number ?? '?') . ' — ' . ($stage->name ?? 'Stage'));
                $summaryS   = $this->escape($ev->name . ': ' . $stageTitle);
                $descS      = $this->escape(trim(collect([
                    $ev->location ? "Location: {$ev->location}" : null,
                    $ev->championship ? "Championship: {$ev->championship}" : null,
                    "Event: {$eventUrl}",
                ])->filter()->implode("\n")));

                $lines[] = 'BEGIN:VEVENT';
                $lines[] = 'UID:stage-' . (isset($stage->id) ? $stage->id : (string) Str::uuid()) . '@compromised-internals.com';
                $lines[] = 'DTSTAMP:' . $dtstamp;
                $lines[] = 'DTSTART:' . $startAt->copy()->utc()->format('Ymd\THis\Z');
                $lines[] = 'DTEND:'   . $endAt->copy()->utc()->format('Ymd\THis\Z');
                $lines[] = 'SUMMARY:' . $summaryS;
                $lines[] = 'DESCRIPTION:' . $descS;
                if ($ev->championship) $lines[] = 'CATEGORIES:' . $this->escape($ev->championship . ',Stage');
                $lines[] = 'URL:' . $this->escape($eventUrl);
                $lines[] = 'END:VEVENT';
            }
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    protected function escape(string $text): string
    {
        // Basic iCal escaping: backslashes, commas, semicolons, and newlines
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(',', '\,',   $text);
        $text = str_replace(';', '\;',   $text);
        $text = str_replace(["\r\n", "\n", "\r"], '\\n', $text);
        return $text;
    }
}