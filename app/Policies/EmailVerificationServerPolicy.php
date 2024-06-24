<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\EmailVerificationServer;

class EmailVerificationServerPolicy
{
    use HandlesAuthorization;

    // @important: in case of non-saas, it falls upon admin case
    public function read(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('email_verification_server_read') != 'no';
                break;
            case 'customer':
                $can = $user->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnEmailVerificationServer(); // non-saas safe
                break;
        }

        return $can;
    }

    public function readAll(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('email_verification_server_read') == 'all';
                break;
            case 'customer':
                // in non-saas mode, there is no such case
                $can = false;
                break;
        }

        return $can;
    }

    public function create(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('email_verification_server_create') == 'yes';
                break;
            case 'customer':
                // in non-saas mode, there is no such case
                $max = get_tmp_quota($user->customer, 'email_verification_servers_max');
                $can = $user->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnEmailVerificationServer()
                    && ($user->customer->emailVerificationServersCount() < $max || $max == -1);
                break;
        }

        return $can;
    }

    public function update(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnEmailVerificationServer() && $user->customer->id == $server->customer_id; // non-saas safe
                break;
        }

        return $can;
    }

    public function delete(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnEmailVerificationServer()
                    && $user->customer->id == $server->customer_id;
                break;
        }

        return $can;
    }

    public function disable(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnEmailVerificationServer()
                    && $user->customer->id == $server->customer_id;
                break;
        }

        return $can && $server->status != EmailVerificationServer::STATUS_INACTIVE;
    }

    public function enable(User $user, EmailVerificationServer $server, $role)
    {
        // config/limit.php
        if (app_profile('email_verfication_server.disable') === true) {
            return false;
        }

        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnEmailVerificationServer()
                    && $user->customer->id == $server->customer_id;
                break;
        }

        return $can && $server->status != EmailVerificationServer::STATUS_ACTIVE;
    }
}
