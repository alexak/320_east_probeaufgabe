<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */
declare(strict_types=1);

namespace Tests;

use Application\Autoloader;
use Application\Controller\OwnerController;
use Application\Model\AddressModel;
use Application\Model\OwnerModel;
use Application\Helper\DatabaseHelper;
use PHPUnit\Framework\TestCase;

require '../Application/Autoloader.php';

final class OwnerControllerTest extends TestCase
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

    public function testOwnerController(): void
    {
        $ownerController = new OwnerController();

        /** @var OwnerModel $referenceOwnerModel */
        $referenceOwnerModel = $ownerController->create($this->getOwnerModel());

        /** @var OwnerModel $testingOwnerModel */
        $testingOwnerModel = $ownerController->getById($referenceOwnerModel->id);

        // test created (and saved) model should be identical with the fetched one
        $this->assertEquals($referenceOwnerModel, $testingOwnerModel);

        // update owner model and test if it has been correctly returned from database
        $referenceOwnerModel = $this->getModifiedOwnerModel($referenceOwnerModel);
        $ownerController->update($referenceOwnerModel);
        $testingOwnerModel = $ownerController->getById($referenceOwnerModel->id);
        $this->assertEquals($referenceOwnerModel, $testingOwnerModel);

        // test delete
        $ownerController->delete($referenceOwnerModel->id);
        $testingOwnerModel = $ownerController->getById($referenceOwnerModel->id);
        $this->assertNull($testingOwnerModel);
    }


    /**
     * function that creates a dummy owner for testings
     * @return OwnerModel
     */
    private function getOwnerModel(): OwnerModel
    {
        $addressModel = new AddressModel();
        $addressModel->street = 'address street';
        $addressModel->houseNo = 'no';
        $addressModel->zipCode = 'zip';
        $addressModel->city = 'city';
        $addressModel->country = 'country';

        $ownerModel = new OwnerModel();
        $ownerModel->name = 'owner name';
        $ownerModel->address = $addressModel;

        return $ownerModel;
    }

    /**
     * function that creates a dummy owner for testings
     * @param OwnerModel $ownerModel
     * @return OwnerModel
     */
    private function getModifiedOwnerModel(OwnerModel $ownerModel): OwnerModel
    {
        $addressModel = $ownerModel->address;
        $addressModel->street = 'modified address street';
        $addressModel->houseNo = 'no2';
        $addressModel->zipCode = 'zip2';
        $addressModel->city = 'modified city';
        $addressModel->country = 'modified country';

        $ownerModel->name = 'modified owner name';
        $ownerModel->address = $addressModel;

        return $ownerModel;
    }
}