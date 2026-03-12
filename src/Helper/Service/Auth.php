<?php

namespace App\Helper\Service;

use Slim\Exception\HttpForbiddenException;
use App\Model\Tokens;
use Ulid\Ulid;
use DateTime;

trait Auth
{


    /**
     * Summary of createToken
     * @return array{expires_at: string, token: string}
     */
    public function createToken(): array
    {
        $token = bin2hex(random_bytes(16));
        $expiresAt = (new DateTime())->modify('+1 hour')
            ->format('Y-m-d H:i:s');

        $model = new Tokens();
        $model->id = Ulid::generate()->__toString();
        $model->token = $token;
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $model->expires_at = $expiresAt;
        $model->insert();

        return [
            'token'      => $token,
            'expires_at' => $expiresAt,
        ];

    }//end createToken()


    public function getToken(): Tokens
    {
        $token = getToken($this->request);

        $model = Tokens::first(
            [
                [
                    'token',
                    '=',
                    $token,
                ],
            ]
        );

        if (is_bool($model)) {
            throw new HttpForbiddenException($this->request, 'Unauthorized');
        }

        return $model;

    }//end getToken()


}//end trait
