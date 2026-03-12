<?php

namespace App\Service\Auth;

use App\Helper\Service\Auth;
use App\Service\Service;

class Login extends Service
{
    use Auth;


    public function validate(): bool
    {
        if (empty($this->data['username']) || empty($this->data['password'])) {
            return false;
        }

        $username = $this->data['username'];
        $password = $this->data['password'];
        $validUsername = env('USERNAME');
        $validPassword = env('PASSWORD');

        return ($username == $validUsername && $password == $validPassword);

    }//end validate()


    public function execute(): array
    {
        return $this->createToken();

    }//end execute()


}//end class
