<?php

namespace App\Events;

use App\Models\Requisition;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Web\RequiredProductResource;
use App\Http\Resources\Web\RequisitionDispatchResource;

class NewRequisitionMade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $requisition;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Requisition $requisition)
    {
        $this->requisition = $requisition;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('new-requisition');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    
    public function broadcastWith()
    {
        return [
            'id' => $this->requisition->id,
            'subject' => $this->requisition->subject,
            'description' => $this->requisition->description,
            'status' => $this->requisition->status,
            'merchant_id' => $this->requisition->merchant_id,
            'created_at' => $this->requisition->created_at->diffForHumans(),
            'products' => RequiredProductResource::collection($this->requisition->products),
            'delivery' =>  $this->requisition->delivery ? $this->requisition->delivery : NULL,
            'agent' => $this->requisition->agent ? $this->requisition->agent : NULL,
            'dispatch' => $this->requisition->status ? new RequisitionDispatchResource($this->requisition->dispatch) : NULL,
        ];
    }
}
