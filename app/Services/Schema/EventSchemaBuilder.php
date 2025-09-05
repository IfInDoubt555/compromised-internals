<?php

declare(strict_types=1);

namespace App\Services\Schema;

use App\Models\RallyEvent;
use App\Models\RallyEventDay;
use App\Models\RallyStage;
use Carbon\Carbon;

/**
 * Builds JSON-LD for a rally event.
 */
final class EventSchemaBuilder
{
    /**
     * Build Schema.org JSON-LD for a RallyEvent.
     *
     * @return array<string, mixed>
     */
    public function build(RallyEvent $event): array
    {
        // Eager-load relations if they weren't loaded
        $event->loadMissing([
            'days' => static fn ($q) => $q->orderBy('date'),
            'days.stages' => static fn ($q) => $q->orderBy('start_time_local'),
        ]);

        $daysOut = [];

        /** @var RallyEventDay $day */
        foreach ($event->days as $day) {
            $stagesOut = [];

            /** @var RallyStage $stage */
            foreach ($day->stages as $stage) {
                // Derive start/end DateTimes
                $start = self::toUtcIso8601($stage->start_time_local);
                $end   = self::toUtcIso8601(
                    $stage->second_pass_time_local ?: null,
                    $stage->start_time_local
                );

                $stagesOut[] = [
                    '@type'      => 'SportsEvent',
                    'name'       => $this->stageTitle($stage),
                    'startDate'  => $start,
                    'endDate'    => $end,
                    'location'   => [
                        '@type' => 'Place',
                        'name'  => $stage->location ?: ($day->name ?? ($event->location ?? '')),
                    ],
                    // Avoid magic id access for static analyzers
                    'identifier' => $stage->getKey(),
                ];
            }

            $daysOut[] = [
                '@type'     => 'Event',
                'name'      => $day->name ?? ($day->date?->toDateString() ?? ''),
                'startDate' => $day->date?->toIso8601String(),
                'subEvent'  => $stagesOut,
            ];
        }

        $address = $event->city
            ? trim($event->city . ', ' . (string) $event->country)
            : (string) ($event->country ?? '');

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'SportsEvent',
            'name'       => (string) $event->name,
            'startDate'  => $event->start_date?->toIso8601String(),
            'endDate'    => $event->end_date?->toIso8601String(),
            'location'   => [
                '@type'   => 'Place',
                'name'    => (string) ($event->location ?? ''),
                'address' => $address,
            ],
            // Avoid magic id access
            'identifier' => $event->getKey(),
            'subEvent'   => $daysOut,
            // Adjust route name if yours differs
            'url'        => $event->slug
                ? route('calendar.show', $event->slug)
                : url('/calendar/events/' . $event->getKey()),
        ];
    }

    /**
     * Make a user-friendly stage title.
     */
    private function stageTitle(RallyStage $stage): string
    {
        $num  = $stage->ss_number !== null ? ('SS' . $stage->ss_number) : ($stage->stage_type ?: 'Stage');
        $name = trim((string) ($stage->name ?? ''));
        return trim($num . ($name !== '' ? (' — ' . $name) : ''));
    }

    /**
     * Convert a local datetime string (or Carbon) to UTC ISO8601.
     *
     * @param  string|\DateTimeInterface|null  $value
     * @param  string|\DateTimeInterface|null  $fallbackFrom  If $value missing, add +1h to this
     * @return string|null
     */
    private static function toUtcIso8601($value, $fallbackFrom = null): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->utc()->toIso8601String();
        }
        if (is_string($value) && $value !== '') {
            return Carbon::parse($value, config('app.timezone'))->utc()->toIso8601String();
        }

        // fallback: +1 hour from a base (used for end time)
        if ($fallbackFrom instanceof \DateTimeInterface) {
            return Carbon::instance($fallbackFrom)->utc()->addHour()->toIso8601String();
        }
        if (is_string($fallbackFrom) && $fallbackFrom !== '') {
            return Carbon::parse($fallbackFrom, config('app.timezone'))->utc()->addHour()->toIso8601String();
        }

        return null;
    }
}