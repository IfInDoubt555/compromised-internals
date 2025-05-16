<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $archived = $request->boolean('archived', false);
        $query = ContactMessage::where('archived', $archived);

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('resolved', $status === 'resolved');
        }

        $sort = $request->input('sort');
        $direction = $request->input('direction', 'desc');

        // Validate sort field
        if (in_array($sort, ['name', 'email', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', $direction);
        }

        $messages = $query->paginate(10)->appends($request->except('page'));

        return view('admin.emails.index', compact('messages', 'archived'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        return view('admin.emails.show', compact('message'));
    }

    public function toggleResolved($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->resolved = !$message->resolved;
        $message->save();

        return redirect()->back()->with('success', 'Message status updated.');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'category' => 'nullable|string|max:50',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->category = $request->category;
        $message->save();

        return redirect()->back()->with('success', 'Category updated.');
    }
    public function archive(ContactMessage $message)
    {
        $message->archived = !$message->archived;
        $message->save();

        return back()->with('status', 'Message ' . ($message->archived ? 'archived' : 'restored') . '.');
    }
}
