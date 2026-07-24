<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected User $user3;

    protected function setUp(): void
    {
        parent::setUp();

        // Create 3 test users
        $this->user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@example.com',
        ]);

        $this->user2 = User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
        ]);

        $this->user3 = User::factory()->create([
            'name' => 'User Three',
            'email' => 'user3@example.com',
        ]);
    }

    /**
     * Test guest cannot access conversation endpoints.
     */
    public function test_guest_cannot_access_conversations()
    {
        $this->getJson('/api/conversations')->assertStatus(401);
        $this->postJson('/api/conversations', ['participant_ids' => [$this->user2->id]])->assertStatus(401);
    }

    /**
     * Test starting a conversation between user1 and user2.
     */
    public function test_user_can_start_conversation()
    {
        $response = $this->actingAs($this->user1, 'sanctum')
            ->postJson('/api/conversations', [
                'participant_ids' => [$this->user2->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'conversation' => [
                    'id',
                    'name',
                    'is_group',
                    'participants',
                ],
            ]);

        $conversationId = $response->json('conversation.id');

        $this->assertDatabaseHas('conversations', [
            'id' => $conversationId,
            'is_group' => false,
        ]);

        // Check if both users are attached
        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $conversationId,
            'user_id' => $this->user1->id,
        ]);
        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $conversationId,
            'user_id' => $this->user2->id,
        ]);
    }

    /**
     * Test duplicates are prevented and existing conversation is returned.
     */
    public function test_starting_existing_conversation_returns_existing()
    {
        // Start conversation first
        $conv = Conversation::create(['is_group' => false]);
        $conv->users()->attach([$this->user1->id, $this->user2->id]);

        $response = $this->actingAs($this->user1, 'sanctum')
            ->postJson('/api/conversations', [
                'participant_ids' => [$this->user2->id],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Conversation already exists')
            ->assertJsonPath('conversation.id', $conv->id);
    }

    /**
     * Test sending messages in a conversation.
     */
    public function test_user_can_send_message_in_conversation()
    {
        $conv = Conversation::create(['is_group' => false]);
        $conv->users()->attach([$this->user1->id, $this->user2->id]);

        $response = $this->actingAs($this->user1, 'sanctum')
            ->postJson("/api/conversations/{$conv->id}/messages", [
                'body' => 'Hello User Two!',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'message_data' => [
                    'id',
                    'body',
                    'user_id',
                ],
            ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'user_id' => $this->user1->id,
            'body' => 'Hello User Two!',
            'read_at' => null,
        ]);
    }

    /**
     * Test a non-participant cannot read or send messages in the conversation.
     */
    public function test_non_participant_cannot_access_conversation()
    {
        $conv = Conversation::create(['is_group' => false]);
        $conv->users()->attach([$this->user1->id, $this->user2->id]);

        // user3 is not in conversation
        $this->actingAs($this->user3, 'sanctum')
            ->getJson("/api/conversations/{$conv->id}")
            ->assertStatus(403);

        $this->actingAs($this->user3, 'sanctum')
            ->getJson("/api/conversations/{$conv->id}/messages")
            ->assertStatus(403);

        $this->actingAs($this->user3, 'sanctum')
            ->postJson("/api/conversations/{$conv->id}/messages", ['body' => 'Hack message'])
            ->assertStatus(403);
    }

    /**
     * Test marking a conversation messages as read.
     */
    public function test_user_can_mark_conversation_as_read()
    {
        $conv = Conversation::create(['is_group' => false]);
        $conv->users()->attach([$this->user1->id, $this->user2->id]);

        // Message from user1
        $message = Message::create([
            'conversation_id' => $conv->id,
            'user_id' => $this->user1->id,
            'body' => 'Unread message from User One',
        ]);

        // user2 reads it
        $response = $this->actingAs($this->user2, 'sanctum')
            ->postJson("/api/conversations/{$conv->id}/read");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Conversation marked as read');

        $this->assertNotNull($message->fresh()->read_at);
    }
}
