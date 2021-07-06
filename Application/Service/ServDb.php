<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */

namespace Application\Service;

use Application\Controller\ControllerInterface;
use Application\Helper\DatabaseHelper;
use Application\Controller\ServerController;
use Application\Controller\LocationController;
use Application\Controller\OwnerController;
use Application\Model\AddressModel;
use Application\Model\ServerModel;
use Application\Model\LocationModel;
use Application\Model\OwnerModel;
use Application\Model\Model;


class ServDb extends DatabaseHelper
{
    /**
     * function that creates the database tables for the needed controllers
     */
    public function init(): void
    {
        $this->initDb();

        $serverController = new ServerController();
        $serverController->init();

        $locationController = new LocationController();
        $locationController->init();

        $ownerController = new OwnerController();
        $ownerController->init();
    }

    /**
     * main function for this service. In "real life", routing would be done by framework
     */
    public function run(): void
    {
        if (isset($_GET['controller'])) {
            $controller = null;
            $model = null;
            switch ($_GET['controller']) {
                case 'server':
                    $controller = new ServerController();
                    $model = $this->getServerModel();
                    break;
                case 'location':
                    $controller = new LocationController();
                    $model = $_POST['dataset'] ? $this->getLocationModel() : null;
                    break;
                case 'owner':
                    $controller = new OwnerController();
                    $model = $_POST['dataset'] ? $this->getOwnerModel() : null;
                    break;
            }
            if ($controller) {
                $result = $this->executeControllerAction($controller, $model);
                //return the json response :
                header('Content-Type: application/json');  // <-- header declaration
                echo json_encode($result, true);    // <--- encode
            }
        }
    }

    /**
     * Creates a server model from POST variables
     * @return Model
     */
    private function getServerModel(): Model
    {
        $serverModel = new ServerModel();
        $requestDatas = json_decode(file_get_contents('php://input'), true);
        if (isset($requestDatas['dataset']) && !empty($requestDatas['dataset'])) {
            $serverModel->id = $requestDatas['dataset']['id'] ?? null;
            $serverModel->name = $requestDatas['dataset']['name'] ?? '';
            $serverModel->type = $requestDatas['dataSet']['type'] ?? '';
            $serverModel->hwDescription = $requestDatas['dataSet']['hwDescription'] ?? '';
            $serverModel->locationId =  $requestDatas['dataSet']['locationId'] ?? null;
        }

        return $serverModel;
    }

    /**
     * creates a location model from POST variables
     * @return Model
     */
    private function getLocationModel(): Model
    {
        $locationModel = new LocationModel();
        $requestDatas = json_decode(file_get_contents('php://input'), true);
        if (isset($requestDatas['dataset']) && !empty($requestDatas['dataset'])) {
            $locationModel->id = $requestDatas['dataset']['id'] ?? null;
            $locationModel->datacenterName = $requestDatas['dataset']['datacenterName'] ?? '';
            $locationModel->rackId = $requestDatas['dataset']['rackId'] ?? null;
            $locationModel->position = $requestDatas['dataset']['position'] ?? null;
            $locationModel->ownerId = $requestDatas['dataset']['ownerId'] ?? null;
        }

        return $locationModel;
    }

    /**
     * creates a owner model from POST variables
     * @return Model
     */
    private function getOwnerModel(): Model
    {
        $ownerModel = new OwnerModel();
        $requestDatas = json_decode(file_get_contents('php://input'), true);
        if (isset($requestDatas['dataset']) && !empty($requestDatas['dataset'])) {
            $addressModel = new AddressModel();
            $addressModel->id = $requestDatas['dataset']['address']['id'] ?? null;
            $addressModel->street = $requestDatas['dataset']['address']['street'] ?? '';
            $addressModel->houseNo = $requestDatas['dataset']['address']['houseNo'] ?? '';
            $addressModel->zipCode = $requestDatas['dataset']['address']['zipCode'] ?? '';
            $addressModel->city = $requestDatas['dataset']['address']['city'] ?? '';
            $addressModel->country = $requestDatas['dataset']['address']['country'] ?? '';

            $ownerModel = new OwnerController();
            $ownerModel->id = $requestDatas['dataset']['id'] ?? null;
            $ownerModel->name = $requestDatas['dataset']['name'] ?? '';
            $ownerModel->address = $addressModel;
        }

        return $ownerModel;
    }

    /**
     * @param ControllerInterface $controller
     * @param Model|null $model
     * @return string
     */
    private function executeControllerAction(ControllerInterface $controller, Model $model = null): string
    {
        $returnValue = '';
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'create':
                    $model = $controller->create($model);
                    $returnValue = json_encode($model);
                    break;
                case 'update':
                    $controller->update($model);
                    break;
                case 'getById':
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        $model = $controller->getById($_GET['id']);
                        $returnValue = json_encode($model);
                    }
                    break;
                case 'getAll':
                    $models =$controller->getAll();
                    $returnValue = json_encode($models);
                    break;
                case 'delete':
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        $controller->delete($_GET['id']);
                    }
                    break;
            }
        }

        return $returnValue;
    }
}