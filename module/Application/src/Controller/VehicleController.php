<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Application\Model\Sedan;
use Application\Model\Motorcycle;
use Application\Controller\Traits\ValidateToken;

class VehicleController extends AbstractActionController
{
    use ValidateToken;

    private $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function addAction()
    {
        $this->accessTokenValidation();

        header('Content-Type: application/json');

        $postData = $this->params()->fromPost();

        $vehicleType = $postData['vehicle_type'];
        switch($vehicleType){
            case 'sedan':
                $vehicle = new Sedan($this->dbAdapter);
                break;
            case 'motorcycle':
                $vehicle = new Motorcycle($this->dbAdapter);
                break;
            default:
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Vehicle Type'
                ]);
                exit;
        }

        $vehicle->setType($postData['sub_type']);
        $vehicle->setHorsepower($postData['horsepower']);
        $vehicle->setTotalTires($postData['total_tires']);
        $vehicle->setModel($postData['model']);
        $vehicle->setColor($postData['color']);

        if($vehicle->validate()){
            $newVehicle = $vehicle->insert();
            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully added Vehicle',
                'data' => $newVehicle,
                'redirect_url' => $this->url()->fromRoute('application', ['action' => 'showVehicles'])
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'errors' => $vehicle->getValidationErrors()
            ]);
        }
        exit();
    }

    public function indexAction()
    {
        $this->accessTokenValidation();

        header('Content-Type: application/json');

        $queryData = $this->params()->fromQuery();

        $vehicleType = $queryData['vehicle_type'];
        switch($vehicleType){
            case 'sedan':
                $vehicle = new Sedan($this->dbAdapter);
                break;
            case 'motorcycle':
                $vehicle = new Motorcycle($this->dbAdapter);
                break;
            default:
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Vehicle Type'
                ]);
                exit;
        }

        $vehicles = $vehicle->getAll();

        echo json_encode([
            'status' => 'success',
            'table_headers' => $vehicles ? array_keys($vehicles[0]) : [],
            'data' => $vehicles
        ]);
        exit();
    }

    public function typesAction()
    {
        $this->accessTokenValidation();

        header('Content-Type: application/json');

        $queryData = $this->params()->fromQuery();

        $vehicleType = $queryData['vehicle_type'];
        switch($vehicleType){
            case 'sedan':
                $vehicleTypes = Sedan::getTypes();
                break;
            case 'motorcycle':
                $vehicleTypes = Motorcycle::getTypes();
                break;
            default:
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Vehicle Type'
                ]);
                exit;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $vehicleTypes
        ]);
        exit();
    }
}
