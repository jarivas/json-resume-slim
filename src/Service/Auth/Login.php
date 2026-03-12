<?php

namespace App\Service\Auth;

use App\Service\Service;
use App\Model\Tokens;
use DateTime;
use Ulid\Ulid;

class Login extends Service
{
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
    }

    public function execute(): array
    {
        $token = bin2hex(random_bytes(16));
        $expiresAt = (new DateTime())->modify('+1 hour')
            ->format('Y-m-d H:i:s');

        $tokenRecord = new Tokens();
        $tokenRecord->id = Ulid::generate()->__toString();
        $tokenRecord->token = $token;
        $tokenRecord->expires_at = $expiresAt;
        $tokenRecord->save();

        return ['token' => $token, 'expires_at' => $expiresAt];
    }
}