<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{RallyEvent, RallyStage, RallyEventDay};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StageController extends Controller
{
    public function index(RallyEvent $event)
    {
        $event->load([
            'days'   => fn ($q) => $q->orderBy('date'),
            'stages' => fn ($q) => $q
                // SD first, then SS by number, then by start time
                ->orderByRaw("CASE WHEN stage_type = 'SD' THEN 0 ELSE 1 END")
                ->orderBy('ss_number')
                ->orderBy('start_time_local'),
        ]);

        // Next SS number should ignore SD stages
        $max = $event->stages->where('stage_type', 'SS')->max('ss_number');
        $next = ((int) ($max ?? 0)) + 1;

        return view('admin.events.stages.index', compact('event', 'next'));
    }

    public function store(Request $request, RallyEvent $event)
    {
        $data = $this->validated($request, $event->id);
        $data['rally_event_id']   = $event->id;
        $data['is_super_special'] = $request->boolean('is_super_special');

        // For Shakedown, ensure ss_number fields are null
        if (($data['stage_type'] ?? 'SS') === 'SD') {
            $data['ss_number'] = null;
            $data['second_ss_number'] = null;
        }

        RallyStage::create($data);

        return back()->with('status', 'Stage created.');
    }

    public function edit(RallyEvent $event, RallyStage $stage)
    {
        abort_if($stage->rally_event_id !== $event->id, 404);
        $days = $event->days()->orderBy('date')->get();

        return view('admin.events.stages.edit', compact('event', 'stage', 'days'));
    }

    public function update(Request $request, RallyEvent $event, RallyStage $stage)
    {
        abort_if($stage->rally_event_id !== $event->id, 404);

        $data = $this->validated($request, $event->id, $stage->id);
        $data['is_super_special'] = $request->boolean('is_super_special');

        if (($data['stage_type'] ?? 'SS') === 'SD') {
            $data['ss_number'] = null;
            $data['second_ss_number'] = null;
        }

        $stage->update($data);

        return redirect()
            ->route('admin.events.stages.index', $event)
            ->with('status', 'Stage updated.');
    }

    public function destroy(RallyEvent $event, RallyStage $stage)
    {
        abort_if($stage->rally_event_id !== $event->id, 404);
        $stage->delete();

        return back()->with('status', 'Stage deleted.');
    }

    /**
     * Validate + normalize request data.
     */
    private function validated(Request $r, int $eventId, ?int $stageId = null): array
    {
        $data = $r->validate([
            // NEW: type (SS or SD / Shakedown)
            'stage_type' => ['required', Rule::in(['SS', 'SD'])],

            'rally_event_day_id' => [
                'nullable',
                Rule::exists('rally_event_days', 'id')->where('rally_event_id', $eventId),
            ],

            // SS number is only required for "SS"
            'ss_number' => [
                'nullable', 'integer', 'min:1', 'required_if:stage_type,SS',
                Rule::unique('rally_stages', 'ss_number')
                    ->where(fn ($q) => $q->where('rally_event_id', $eventId))
                    ->ignore($stageId),
            ],

            'name'         => ['required', 'string', 'max:255'],
            'distance_km'  => ['nullable', 'numeric', 'min:0', 'max:999.99'],

            'start_time_local'        => ['nullable', 'date'],
            'second_pass_time_local'  => ['nullable', 'date', 'after_or_equal:start_time_local'],

            // NEW: second run fields (optional)
            'second_ss_number' => ['nullable', 'integer', 'min:1', 'different:ss_number'],
            'second_rally_event_day_id' => [
                'nullable',
                Rule::exists('rally_event_days', 'id')->where('rally_event_id', $eventId),
            ],

            'map_image_url'  => ['nullable', 'string', 'max:500'],
            'map_embed_url'  => ['nullable', 'string', 'max:1000'],
            'gpx_path'       => ['nullable', 'string', 'max:500'],
            'is_super_special' => ['sometimes', 'boolean'],
        ]);

        // Infer primary day from start time if missing
        if (empty($data['rally_event_day_id']) && !empty($data['start_time_local'])) {
            $d = Carbon::parse($data['start_time_local'])->toDateString();
            $dayId = RallyEventDay::where('rally_event_id', $eventId)
                ->whereDate('date', $d)
                ->value('id');
            if ($dayId) {
                $data['rally_event_day_id'] = $dayId;
            }
        }

        // Infer second day from second time if missing
        if (empty($data['second_rally_event_day_id']) && !empty($data['second_pass_time_local'])) {
            $d2 = Carbon::parse($data['second_pass_time_local'])->toDateString();
            $dayId2 = RallyEventDay::where('rally_event_id', $eventId)
                ->whereDate('date', $d2)
                ->value('id');
            if ($dayId2) {
                $data['second_rally_event_day_id'] = $dayId2;
            }
        }

        // Default type safety
        $data['stage_type'] = $data['stage_type'] ?? 'SS';

        return $data;
    }
}