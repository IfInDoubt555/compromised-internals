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

        // Optional: Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Optional: Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'resolved') {
                $query->where('resolved', true);
            } elseif ($request->status === 'open') {
                $query->where('resolved', false);
            }
        }

        // Optional: Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $query->orderBy($sortField, $sortDirection);

        $messages = $query->paginate(10)->withQueryString();

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
