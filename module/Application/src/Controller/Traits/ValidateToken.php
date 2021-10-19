<?php

namespace Application\Controller\Traits;

use Application\Model\User;

trait ValidateToken
{
    public $accessToken;

    private function accessTokenValidation()
    {
        $accessTokenHeader = $this->getRequest()->getHeader('Authorization');
        $this->accessToken = null;
        $userObj = new User($this->dbAdapter);

        $redirectUrl = $this->url()->fromRoute('home');

        if ($accessTokenHeader) {
            if (preg_match('/Bearer\s(\S+)/', $accessTokenHeader->getFieldValue(), $matches)) {
                $this->accessToken = $matches[1];
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing Access Token',
                'redirect_url' => $redirectUrl
            ]);
            exit;
        }

        if(!$userObj->validateAccessToken($this->accessToken)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid Access Token',
                'redirect_url' => $redirectUrl
            ]);
            exit;
        }
    }
}