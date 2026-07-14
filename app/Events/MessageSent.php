<?php
 
namespace App\Events;
 
use App\Models\ComplaintMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    /**
     * The complaint message instance.
     */
    public $message;
 
    /**
     * Create a new event instance.
     */
    public function __construct(ComplaintMessage $message)
    {
        $this->message = $message;
    }
 
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('consultation.' . $this->message->consultation_request_id),
        ];
    }
 
    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'attachment_path' => $this->message->attachment_path ? asset('storage/' . $this->message->attachment_path) : null,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'is_citizen' => $this->message->sender->role === 'user',
            'created_at' => $this->message->created_at->format('Y-m-d H:i:s'),
            'formatted_time' => $this->message->created_at->format('M d, Y, h:i A'),
        ];
    }
}
