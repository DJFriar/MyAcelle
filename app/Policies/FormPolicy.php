<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\Form;

class FormPolicy
{
    use HandlesAuthorization;

    public function list(User $user)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return true;
    }

    public function create(User $user)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return true;
    }

    public function read(User $user, Form $form)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return $user->customer->id == $form->customer_id;
    }

    public function update(User $user, Form $form)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return $user->customer->id == $form->customer_id;
    }

    public function delete(User $user, Form $form)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return $user->customer->id == $form->customer_id;
    }

    public function publish(User $user, Form $form)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return $user->customer->id == $form->customer_id && $form->status == Form::STATUS_DRAFT;
    }

    public function unpublish(User $user, Form $form)
    {
        if (app_profile('form.disable') === true) {
            return false;
        }

        return $user->customer->id == $form->customer_id && $form->status == Form::STATUS_PUBLISHED;
    }
}
