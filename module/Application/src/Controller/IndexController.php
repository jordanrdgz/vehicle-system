<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Model\User;
use Application\Controller\Traits\ValidateToken;

class IndexController extends AbstractActionController
{
    use ValidateToken;

    private $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function indexAction()
    {
        $view = new ViewModel();
        $view->setTemplate( 'application/index/login' );

        return $view;
    }

    public function addVehicleAction()
    {
        //$this->accessTokenValidation();

        $view = new ViewModel();
        $view->setTemplate( 'application/index/add-vehicle' );
        //$view->setTerminal(true);

        return $view;
    }

    public function showVehiclesAction()
    {
        //$this->accessTokenValidation();

        $view = new ViewModel();
        $view->setTemplate( 'application/index/show-vehicles' );
        //$view->setTerminal(true);

        return $view;
    }

    public function loginAction()
    {
        header('Content-Type: application/json');

        $postParams = $this->params()->fromPost();
        $email = $postParams['email'];
        $password = $postParams['password'];

        $userObj = new User($this->dbAdapter);
        $user = $userObj->getByEmail($email);

        if(!$user) {
            echo json_encode([
                'status' => 'error',
                'message' => 'User do not exist on the database'
            ]);
            exit;
        }

        $passVerification = $userObj->verifyPasswordMd5($user['password'], $password);

        if(!$passVerification) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Incorrect Password'
            ]);
            exit;
        }

        $accessToken = $userObj->createAccessToken($user['user_id'], $email);

        echo json_encode([
            'status' => 'success',
            'access_token' => $accessToken,
            'redirect_url' => $this->url()->fromRoute('application', ['action' => 'showVehicles'])
        ]);
        exit();
    }

    public function logoutAction()
    {
        $this->accessTokenValidation();

        $userObj = new User($this->dbAdapter);
        $userObj->removeAccessToken($this->accessToken);

        echo json_encode([
            'status' => 'success',
            'redirect_url' => $this->url()->fromRoute('home')
        ]);
        exit();
    }
}
