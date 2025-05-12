<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.emails.index', compact('messages'));
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
}
