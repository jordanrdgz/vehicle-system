<?php

namespace Application\Model;

class User
{
    protected $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function getByEmail( $email )
    {
        $sql = "
            SELECT
                u.*
            FROM
                vehicles.user u
            WHERE
                LOWER( u.email ) = LOWER(:email)
            LIMIT
                1
        ";

        $res = $this->dbAdapter->query( $sql )->execute(
            [
                'email' => trim($email),
            ]
        );

        return $res->current();
    }

    public function verifyPasswordMd5( $password, $passwordInput )
    {
        $hashedPassword = md5( trim( $passwordInput ) );

        if( $password == $hashedPassword ){
            return true;
        }

        return false;
    }

    public function createAccessToken($userId, $email)
    {
        $tokenExpiration = date("Y-m-d H:i:s", strtotime("+1 month"));
        $accessToken = base64_encode($userId . '#' . $email . '#' . $tokenExpiration);

        $this->updateAccessToken($userId, $accessToken);

        return $accessToken;
    }

    private function decodeAccessToken($accessToken)
    {
        $decodedAccessToken = explode('#', base64_decode($accessToken));

        return [
            'user_id' => $decodedAccessToken[0],
            'email' => $decodedAccessToken[1],
            'exp' => $decodedAccessToken[2]
        ];
    }

    private function updateAccessToken($userId, $accessToken)
    {
        $sql = "
            UPDATE
                vehicles.user u
            SET
                access_token = :access_token
            WHERE 
                user_id = :user_id
        ";

        $this->dbAdapter->query( $sql )->execute(
            [
                'user_id' => $userId,
                'access_token' => trim($accessToken),
            ]
        );
    }

    public function validateAccessToken($accessToken)
    {
        $decodedAccessToken = $this->decodeAccessToken($accessToken);
        $currentAccessToken = $this->getAccessToken($decodedAccessToken['user_id']);

        $now = new \DateTime();
        $tokenExpiration = new \DateTime($decodedAccessToken['exp']);

        if ($now > $tokenExpiration) {
            return false;
        }

        if($currentAccessToken != $accessToken) {
            return false;
        }

        return true;
    }

    private function getAccessToken($userId)
    {
        $sql = "
            SELECT
                u.access_token
            FROM
                vehicles.user u
            WHERE 
                user_id = :user_id
        ";

        $res = $this->dbAdapter->query( $sql )->execute(
            [
                'user_id' => $userId,
            ]
        );

        return $res->current()['access_token'];
    }


    public function removeAccessToken($accessToken)
    {
        $decodedAccessToken = $this->decodeAccessToken($accessToken);
        $this->updateAccessToken($decodedAccessToken['user_id'], '');
    }
}