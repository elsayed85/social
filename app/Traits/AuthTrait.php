<?php

namespace App\Traits;


trait AuthTrait
{
    public function findUsername()
    {
        $login = request()->input('login_key');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return $fieldType;
    }
}
