<?php

namespace App\Services\Schema;

use App\Models\RallyEvent;
use Carbon\Carbon;

class EventSchemaBuilder
{
    public function build(RallyEvent $event): array
    {
        $schema = [
            '@context'               => 'https://schema.org',
            '@type'                  => 'SportsEvent',
            'name'                   => $event->name,
            'description'            => strip_tags($event->description ?? ''),
            'url'                    => route('calendar.show', $event->slug),
            'startDate'              => optional($event->start_date)->toDateString(),
            'endDate'                => optional($event->end_date)->toDateString(),
            'eventStatus'            => 'https://schema.org/EventScheduled',
            'eventAttendanceMode'    => 'https://schema.org/OfflineEventAttendanceMode',
            'isAccessibleForFree'    => true,
            'image'                  => $event->image_url ?? asset('images/calendar-og.png'),
            'location'               => [
                '@type'   => 'Place',
                'name'    => $event->location ?? '',
                'address' => $event->location ?? '',
            ],
        ];

        if (!empty($event->official_url)) {
            $schema['sameAs'] = [$event->official_url];
        }

        $subEvents = [];
        foreach ($event->stages as $s) {
            $startAt = $s->start_time_local
                ?: (!empty($s->start_at) ? Carbon::parse($s->start_at) : null);

            if (!$startAt && !empty($s->start_time)) {
                $dayDate = optional($s->day)->date ?? $event->start_date;
                if ($dayDate) {
                    $startAt = Carbon::parse($dayDate . ' ' . $s->start_time, config('app.timezone'));
                }
            }
            if (!$startAt) {
                continue;
            }

            $endAt = (clone $startAt)->addHour(); // fallback duration
            $label = ($s->stage_type ?? 'SS') === 'SD'
                ? trim(($s->name ?: 'Shakedown') . ' (SD)')
                : (function ($s) {
                    $nums = 'SS ' . ($s->ss_number ?? '?');
                    if (!empty($s->second_ss_number)) $nums .= '/' . $s->second_ss_number;
                    if (!empty($s->is_super_special)) $nums .= ' /S';
                    return trim(($s->name ?: 'Special Stage') . " ({$nums})");
                })($s);

            $subEvents[] = [
                '@type'               => 'SportsEvent',
                'name'                => $label,
                'startDate'           => $startAt->toIso8601String(),
                'endDate'             => $endAt->toIso8601String(),
                'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
                'location'            => ['@type' => 'Place', 'name' => $event->location ?? ''],
                'url'                 => route('calendar.show', $event->slug) . '#ss-' . ($s->id ?? 'x'),
            ];
        }

        if ($subEvents) {
            $schema['subEvent'] = $subEvents;
        }

        return $schema;
    }
}