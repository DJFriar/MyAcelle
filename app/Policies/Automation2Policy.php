<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\Automation2;

class Automation2Policy
{
    use HandlesAuthorization;

    public function list(User $user)
    {
        if (app_profile('automation.disable') === true) {
            return false;
        }

        return true;
    }

    public function create(User $user, Automation2 $automation)
    {
        $customer = $user->customer;
        $max = get_tmp_quota($customer, 'automation_max');

        $can = $max > $customer->automationsCount() || $max == -1;

        return $can;
    }

    public function view(User $user, Automation2 $automation)
    {
        if (app_profile('automation.disable') === true) {
            return false;
        }

        return $automation->customer_id == $user->customer->id;
    }

    public function update(User $user, Automation2 $automation)
    {
        if (app_profile('automation.disable') === true) {
            return false;
        }

        return $automation->customer_id == $user->customer->id;
    }


    public function enable(User $user, Automation2 $automation)
    {
        if (app_profile('automation.disable') === true) {
            return false;
        }

        return $automation->customer_id == $user->customer->id &&
            in_array($automation->status, [
                Automation2::STATUS_INACTIVE
            ]);
    }

    public function disable(User $user, Automation2 $automation)
    {
        if (app_profile('automation.disable') === true) {
            return false;
        }

        return $automation->customer_id == $user->customer->id &&
            in_array($automation->status, [
                Automation2::STATUS_ACTIVE
            ]);
    }

    public function delete(User $user, Automation2 $automation)
    {
        if (app_profile('automation.disable') === true) {
            return false;
        }

        return $automation->customer_id == $user->customer->id &&
            in_array($automation->status, [
                Automation2::STATUS_ACTIVE,
                Automation2::STATUS_INACTIVE
            ]);
    }
}
