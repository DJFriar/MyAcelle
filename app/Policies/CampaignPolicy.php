<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\Campaign;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function read(User $user, Campaign $item)
    {
        $can = $item->customer_id == $user->customer->id;

        return $can;
    }

    public function create(User $user, Campaign $item)
    {
        $max = get_tmp_quota($user->customer, 'campaign_max');

        $can = $max > $user->customer->campaigns()->count() || $max == -1;

        // config/limit.php
        $limit = app_profile('campaign.limit');
        if (!is_null($limit)) {
            $campaignsCount = $user->customer->campaignsCount();
            $can = $can && ($campaignsCount < $limit);
        } else {
            // ignore limit because it is null
        }

        return $can;
    }

    public function overview(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function update(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id
            && (in_array($item->status, [
                Campaign::STATUS_NEW,
                Campaign::STATUS_QUEUING,
                Campaign::STATUS_QUEUED,
                Campaign::STATUS_ERROR,
                Campaign::STATUS_PAUSED,
                Campaign::STATUS_SCHEDULED,
            ]));
    }

    public function delete(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id && in_array($item->status, [
            Campaign::STATUS_NEW,
            Campaign::STATUS_QUEUING,
            Campaign::STATUS_QUEUED,
            Campaign::STATUS_ERROR,
            Campaign::STATUS_PAUSED,
            Campaign::STATUS_DONE,
            Campaign::STATUS_SENDING,
            Campaign::STATUS_SCHEDULED,
        ]);
    }

    public function pause(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id && in_array($item->status, [
            Campaign::STATUS_QUEUING,
            Campaign::STATUS_QUEUED,
            Campaign::STATUS_SENDING,
            Campaign::STATUS_SCHEDULED,
        ]);
    }

    public function run(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id && in_array($item->status, [
            Campaign::STATUS_NEW,
        ]);
    }

    public function restart(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id && in_array($item->status, [
            Campaign::STATUS_PAUSED,
            Campaign::STATUS_ERROR,
            Campaign::STATUS_SCHEDULED,
        ]);
    }

    public function sort(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function copy(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function preview(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function image(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function resend(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id && ($item->isDone() || $item->isPaused());
    }

    public function send_test_email(User $user, Campaign $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id && in_array($item->status, [
            Campaign::STATUS_QUEUING,
            Campaign::STATUS_QUEUED,
            Campaign::STATUS_SENDING,
            Campaign::STATUS_ERROR,
            Campaign::STATUS_PAUSED,
            Campaign::STATUS_DONE,
            Campaign::STATUS_SCHEDULED,
        ]);
    }
}
