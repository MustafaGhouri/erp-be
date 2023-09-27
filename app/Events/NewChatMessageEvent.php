<?php
// namespace App\Events;

// use App\Models\Chat;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class NewChatMessageEvent implements ShouldBroadcast
// {
//     use Dispatchable, SerializesModels;

//     public $chat;

//     public function __construct(Chat $chat)
//     {
//         $this->chat = $chat;
//     }

//     public function broadcastOn()
//     {
//         return ['chat'];
//     }

//     public function broadcastAs()
//     {
//         return 'new-message';
//     }
// }
 
namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public $chat;

       public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
 
    {   
        return new PrivateChannel('chat.' . $this->chat->receiver_id);
 
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
