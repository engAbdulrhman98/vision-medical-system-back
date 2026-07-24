<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index(Request $request)
    {
        $conversations = $request->user()
            ->conversations()
            ->with(['users', 'latestMessage'])
            ->latest('updated_at')
            ->paginate(15);

        return ConversationResource::collection($conversations);
    }

    /**
     * Start a new conversation.
     * Prevents duplicate direct conversations between same 2 participants.
     */
    public function store(StoreConversationRequest $request)
    {
        $validated = $request->validated();
        $isGroup = $validated['is_group'] ?? false;
        $userIds = array_unique(array_merge($validated['participant_ids'], [$request->user()->id]));

        // If direct conversation (not group) between 2 users, check if already exists
        if (!$isGroup && count($userIds) === 2) {
            $otherUserId = $validated['participant_ids'][0];
            
            $existing = Conversation::where('is_group', false)
                ->whereHas('users', function ($q) use ($request) {
                    $q->where('users.id', $request->user()->id);
                })
                ->whereHas('users', function ($q) use ($otherUserId) {
                    $q->where('users.id', $otherUserId);
                })
                ->first();

            if ($existing) {
                $existing->load(['users', 'latestMessage']);
                return response()->json([
                    'message' => 'Conversation already exists',
                    'conversation' => new ConversationResource($existing),
                ]);
            }
        }

        // Create new conversation
        $conversation = Conversation::create([
            'name' => $validated['name'] ?? null,
            'is_group' => $isGroup,
        ]);

        $conversation->users()->attach($userIds);
        $conversation->load(['users', 'latestMessage']);

        return response()->json([
            'message' => 'Conversation started successfully',
            'conversation' => new ConversationResource($conversation),
        ], 201);
    }

    /**
     * Display the specified conversation.
     */
    public function show(Request $request, Conversation $conversation)
    {
        // Authorize that current user is a participant
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->load(['users', 'latestMessage']);
        
        return new ConversationResource($conversation);
    }

    /**
     * Get paginated messages of the conversation.
     */
    public function messages(Request $request, Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->latest()
            ->paginate(25);

        return MessageResource::collection($messages);
    }

    /**
     * Send a message to the conversation.
     */
    public function sendMessage(StoreMessageRequest $request, Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = $conversation->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        // Touch parent conversation to float it to top on index queries
        $conversation->touch();
        $message->load('user');

        return response()->json([
            'message' => 'Message sent successfully',
            'message_data' => new MessageResource($message),
        ], 201);
    }

    /**
     * Mark all incoming messages in this conversation as read.
     */
    public function markAsRead(Request $request, Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Conversation marked as read',
        ]);
    }

    /**
     * Leave or delete the conversation.
     */
    public function destroy(Request $request, Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Detach user
        $conversation->users()->detach($request->user()->id);

        // If no participants left, delete conversation & its messages
        if ($conversation->users()->count() === 0) {
            $conversation->delete();
        }

        return response()->json([
            'message' => 'Left conversation successfully',
        ]);
    }
}
