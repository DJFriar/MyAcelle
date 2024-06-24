<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\PlanGeneral;

class PlanPolicy
{
    use HandlesAuthorization;

    public function read(User $user, PlanGeneral $item)
    {
        $can = $user->admin->getPermission('plan_read') != 'no';

        return $can;
    }

    public function readAll(User $user, PlanGeneral $item)
    {
        $can = $user->admin->getPermission('plan_read') == 'all';

        return $can;
    }

    public function create(User $user, PlanGeneral $item)
    {
        $can = $user->admin->getPermission('plan_create') == 'yes';

        // config/limit.php
        $limit = app_profile('plan.limit');
        if (!is_null($limit)) {
            $planCount = PlanGeneral::count();
            $can = $can && ($planCount < $limit);
        } else {
            // ignore limit because it is null
        }

        return $can;
    }

    public function update(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function delete(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function disable(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'inactive';
    }

    public function enable(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'active' && $item->isValid();
    }

    public function visibleOn(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && !$item->visible && $item->isActive();
    }

    public function visibleOff(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->visible;
    }

    public function copy(User $user, PlanGeneral $item)
    {
        $ability = $user->admin->getPermission('plan_copy');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }
}
