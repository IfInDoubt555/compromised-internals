<?php

declare(strict_types=1);

namespace App\Services\Schema;

use App\Models\RallyEvent;
use App\Models\RallyStage;
use App\Models\RallyEventDay;

/**
 * Builds JSON-LD for a rally event.
 */
final class EventSchemaBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(RallyEventDay $event): array
    {
        // Ensure relations are available if caller forgot to eager load
        $event->loadMissing([
            'days' => function ($q) {
                $q->orderBy('date');
            },
            'days.stages' => function ($q) {
                $q->orderBy('start_time');
            },
        ]);

        $days = [];
        foreach ($event->days as $day) {
            $stages = [];

            /** @var RallyStage $stage */
            foreach ($day->stages as $stage) {
                /** @var EventDay|null $stageDay */
                $stageDay = $stage->getRelationValue('day'); // avoid $stage->day direct access for PHPStan

                $stages[] = [
                    '@type'      => 'SportsEvent',
                    'name'       => $stage->name,
                    'startDate'  => optional($stage->start_time)->toIso8601String(),
                    'endDate'    => optional($stage->end_time)->toIso8601String(),
                    'location'   => [
                        '@type' => 'Place',
                        'name'  => $stage->location ?? ($stageDay?->name ?? $event->location),
                    ],
                    'identifier' => $stage->id,
                ];
            }

            $days[] = [
                '@type'     => 'Event',
                'name'      => $day->name ?? $day->date?->toDateString(),
                'startDate' => optional($day->date)->toIso8601String(),
                'subEvent'  => $stages,
            ];
        }

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'SportsEvent',
            'name'       => $event->title,
            'startDate'  => optional($event->start_date)->toIso8601String(),
            'endDate'    => optional($event->end_date)->toIso8601String(),
            'location'   => [
                '@type' => 'Place',
                'name'  => $event->location,
                'address' => $event->city ? "{$event->city}, {$event->country}" : $event->country,
            ],
            'identifier' => $event->id,
            'subEvent'   => $days,
            'url'        => route('events.show', $event), // adjust if your route differs
        ];
    }
}