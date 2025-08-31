<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Board;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThreadAdminController extends Controller
{
    public function index()
    {
        return view('admin.threads.index', [
            'drafts'    => Thread::draft()->latest('updated_at')->with('board','user')->get(),
            'scheduled' => Thread::scheduled()->orderBy('scheduled_for')->with('board','user')->get(),
            'published' => Thread::published()->latest('published_at')->with('board','user')->limit(50)->get(),
        ]);
    }

    public function edit(Thread $thread)
    {
        return view('admin.threads.edit', [
            'thread' => $thread,
            'boards' => Board::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Thread $thread)
    {
        $data = $request->validate([
            'title'         => ['required','string','max:255'],
            'slug'          => ['nullable','string','max:255'],
            'body'          => ['required','string'],
            'board_id'      => ['required','exists:boards,id'],
            'status'        => ['required','in:draft,scheduled,published'],
            'scheduled_for' => ['nullable','date'],
        ]);

        $scheduled = $data['scheduled_for'] ?? null;
        if ($scheduled) {
            $scheduled = Carbon::parse($scheduled, config('app.timezone'))->utc();
        }

        if ($data['status'] === 'published') {
            $data['published_at']  = now()->utc();
            $data['scheduled_for'] = null;
        } elseif ($data['status'] === 'scheduled') {
            $data['scheduled_for'] = $scheduled;
            $data['published_at']  = null;
        } else {
            $data['scheduled_for'] = null;
            $data['published_at']  = null;
        }

        $thread->fill($data)->save();

        return redirect()->route('admin.threads.edit', $thread)->with('status','Thread saved.');
    }
}