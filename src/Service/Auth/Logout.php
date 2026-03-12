<?php

namespace App\Service\Auth;

use App\Service\Service;
use App\Model\Tokens;

class Logout extends Service
{
    public function execute(): array
    {
        $token = $this->request->getHeaderLine('Authorization');

        $model = Tokens::first([
            'token' => $token,
        ]);

        if (is_bool($model)) {
            return ['message' => 'Invalid token'];
        }

        return $model->delete() ? ['message' => 'Logged out successfully']
            : ['message' => 'Failed to log out'];
    }
}