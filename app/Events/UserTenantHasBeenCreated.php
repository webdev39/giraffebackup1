<?php

namespace App\Events;

use App\Models\UserTenant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserTenantHasBeenCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var UserTenant */
    public $userTenant;

    /** @var bool */
    public $createSeedData;

    /**
     * UserTenantHasBeenCreated constructor.
     *
     * @param UserTenant $userTenant
     * @param bool       $createSeedData
     */
    public function __construct(UserTenant $userTenant, bool $createSeedData = false)
    {
        $this->userTenant       = $userTenant;
        $this->createSeedData   = $createSeedData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
