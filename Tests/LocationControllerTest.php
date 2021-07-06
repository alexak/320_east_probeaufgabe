<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */
declare(strict_types=1);

namespace Tests;

use Application\Autoloader;
use Application\Controller\LocationController;
use Application\Model\LocationModel;
use Application\Model\OwnerModel;
use Application\Helper\DatabaseHelper;
use PHPUnit\Framework\TestCase;

require '../Application/Autoloader.php';

final class LocationControllerTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Autoloader::register();
    }

    public function setUp(): void
    {
        $databaseHelper = new DatabaseHelper();
        $databaseHelper->connectToDatabase();
        $this->addToAssertionCount(1);
    }

    public function testLocationController(): void
    {
        $locationController = new LocationController();

        /** @var LocationModel $referenceLocationModel */
        $referenceLocationModel = $locationController->create($this->getLocationModel());

        /** @var LocationModel $testingLocationModel */
        $testingLocationModel = $locationController->getById($referenceLocationModel->id);

        // test created (and saved) model should be identical with the fetched one
        $this->assertEquals($referenceLocationModel, $testingLocationModel);

        // update owner model and test if it has been correctly returned from database
        $referenceLocationModel = $this->getModifiedLocationModel($referenceLocationModel);
        $locationController->update($referenceLocationModel);
        $testingLocationModel = $locationController->getById($referenceLocationModel->id);
        $this->assertEquals($referenceLocationModel, $testingLocationModel);

        // test delete
        $locationController->delete($referenceLocationModel->id);
        $testingLocationModel = $locationController->getById($referenceLocationModel->id);
        $this->assertNull($testingLocationModel);
    }

    /**
     * function that creates a dummy owner for testings
     * @return LocationModel
     */
    private function getLocationModel(): LocationModel
    {
        $locationModel = new LocationModel();
        $locationModel->datacenterName = 'datacenter name';
        $locationModel->ownerId = 1;
        $locationModel->rackId = 23;
        $locationModel->position = 5;
        $locationModel->ownerId = 12;

        return $locationModel;
    }

    /**
     * function that creates a dummy owner for testings
     * @return LocationModel
     */
    private function getModifiedLocationModel(LocationModel $locationModel): LocationModel
    {
        $locationModel->datacenterName = 'updated datacenter name';
        $locationModel->ownerId = 2;
        $locationModel->rackId = 24;
        $locationModel->position = 6;
        $locationModel->ownerId = 13;

        return $locationModel;
    }
}