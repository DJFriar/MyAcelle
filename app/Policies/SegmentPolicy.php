<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\Segment;

class SegmentPolicy
{
    use HandlesAuthorization;

    public function list(User $user)
    {
        if (app_profile('list.disable_segment') === true) {
            return false;
        }

        return true;
    }

    public function create(User $user, Segment $item)
    {
        if (app_profile('list.disable_segment') === true) {
            return false;
        }

        $customer = $user->customer;
        $max_per_list = get_tmp_quota($customer, 'segment_per_list_max');

        return $customer->id == $item->mailList->customer_id
                && ($max_per_list > $item->mailList->segments()->count()
                || $max_per_list == -1);
    }

    public function update(User $user, Segment $item)
    {
        if (app_profile('list.disable_segment') === true) {
            return false;
        }

        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id;
    }

    public function delete(User $user, Segment $item)
    {
        if (app_profile('list.disable_segment') === true) {
            return false;
        }

        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id;
    }

    public function export(User $user, Segment $item)
    {
        if (app_profile('list.disable_segment') === true) {
            return false;
        }

        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id;
    }
}
