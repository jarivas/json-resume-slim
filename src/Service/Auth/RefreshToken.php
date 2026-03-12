<?php

namespace App\Service\Auth;

use App\Helper\Service\Auth;
use App\Service\Service;

class RefreshToken extends Service
{
    use Auth;


    public function execute(): array
    {
        $model = $this->getToken();

        if (!$model->delete()) {
            return ['message' => 'Failed to refresh token'];
        }

        return $this->createToken();

    }//end execute()


}//end class
