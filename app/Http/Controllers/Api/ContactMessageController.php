<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Http\Resources\ContactMessageResource;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        return ContactMessageResource::collection(ContactMessage::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => false,
        ]);

        activity()
            ->log('تم استلام رسالة تواصل جديدة من العميل: ' . $request->name);

        return response()->json([
            'message' => 'Your message has been sent successfully',
            'contact' => new ContactMessageResource($message)
        ], 201);
    }

    public function markAsRead(ContactMessage $contact)
    {
        $contact->update(['is_read' => true]);

        activity()
            ->causedBy(auth()->user())
            ->log('تم تعليم رسالة العميل كـ مقروءة: ' . $contact->name);

        return response()->json([
            'message' => 'Message marked as read successfully',
            'contact' => new ContactMessageResource($contact)
        ]);
    }

    public function destroy(ContactMessage $contact)
    {
        $name = $contact->name;
        $contact->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('تم حذف رسالة العميل: ' . $name);

        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }
}
