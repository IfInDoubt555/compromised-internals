<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{RallyEvent,RallyStage,RallyEventDay};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StageController extends Controller
{
    public function index(RallyEvent $event)
    {
        $event->load(['days' => fn($q)=>$q->orderBy('date'), 'stages' => fn($q)=>$q->orderBy('ss_number')]);
        return view('admin.events.stages.index', compact('event'));
    }

    public function store(Request $request, RallyEvent $event)
    {
        $data = $this->validated($request, $event->id);
        $data['rally_event_id'] = $event->id;
        RallyStage::create($data);
        return back()->with('status', 'Stage created.');
    }

    public function edit(RallyEvent $event, RallyStage $stage)
    {
        abort_if($stage->rally_event_id !== $event->id, 404);
        $days = $event->days()->orderBy('date')->get();
        return view('admin.events.stages.edit', compact('event','stage','days'));
    }

    public function update(Request $request, RallyEvent $event, RallyStage $stage)
    {
        abort_if($stage->rally_event_id !== $event->id, 404);
        $data = $this->validated($request, $event->id, $stage->id);
        $stage->update($data);
        return redirect()->route('admin.events.stages.index', $event)->with('status', 'Stage updated.');
    }

    public function destroy(RallyEvent $event, RallyStage $stage)
    {
        abort_if($stage->rally_event_id !== $event->id, 404);
        $stage->delete();
        return back()->with('status', 'Stage deleted.');
    }

    private function validated(Request $r, int $eventId, ?int $stageId = null): array
    {
        return $r->validate([
            'rally_event_day_id' => [
                'nullable',
                Rule::exists('rally_event_days', 'id')->where('rally_event_id', $eventId)
            ],
            'ss_number' => ['required','integer','min:1', Rule::unique('rally_stages','ss_number')->where('rally_event_id',$eventId)->ignore($stageId)],
            'name' => ['required','string','max:255'],
            'distance_km' => ['nullable','numeric','min:0','max:999.99'],
            'is_super_special' => ['sometimes','boolean'],
            'start_time_local' => ['nullable','date'],
            'second_pass_time_local' => ['nullable','date','after_or_equal:start_time_local'],
            'map_image_url' => ['nullable','string','max:500'],
            'map_embed_url' => ['nullable','string','max:1000'],
            'gpx_path' => ['nullable','string','max:500'],
        ]);
    }
}