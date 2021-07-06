<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */
declare(strict_types=1);

namespace Tests;

use Application\Autoloader;
use Application\Controller\OwnerController;
use Application\Controller\ServerController;
use Application\Model\Model;
use Application\Model\OwnerModel;
use Application\Service\ServDb;
use Application\Helper\DatabaseHelper;
use Application\Model\ServerModel;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

require '../Application/Autoloader.php';

final class ServerControllerTest extends TestCase
{
    const URL = 'dev.320east-probeaufgabe.local/index.php?';                          //@todo: adapt URL here (in real life this should be done with a config file

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

    /**
     * integratiokn test of server controller
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testIntegrationServerController(): void
    {
        $httpClient = new Client();

        // createOrUpdate action -- creating a new server entry in db
        $response = $httpClient->request('POST', self::URL .'controller=server&action=create', [
            'debug' => true,
            'content-type' => 'application/json',
            'body' => json_encode(
                [
                    'dataset' => [
                        'name' => 'Server model name',
                        'type' => 'Server model type',
                        'hwDescription' => 'Server model hw description',
                        'locationId' => 1
                    ]
                ])
        ]);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testServerController(): void
    {
        $serverController = new ServerController();

        /** @var ServerModel $referenceServerModel */
        $referenceServerModel = $serverController->create($this->getServerModel());

        /** @var ServerModel $testingServerModel */
        $testingServerModel = $serverController->getById($referenceServerModel->id);

        // test created (and saved) model should be identical with the fetched one
        $this->assertEquals($referenceServerModel, $testingServerModel);

        // update owner model and test if it has been correctly returned from database
        $referenceServerModel = $this->getModifiedServerModel($referenceServerModel);
        $serverController->update($referenceServerModel);
        $testingServerModel = $serverController->getById($referenceServerModel->id);
        $this->assertEquals($referenceServerModel, $testingServerModel);

        // test delete
        $serverController->delete($referenceServerModel->id);
        $testingServerModel = $serverController->getById($referenceServerModel->id);
        $this->assertNull($testingServerModel);
    }

    private function getServerModel(): Model
    {
        /** @var ServerModel */
        $serverModel = new ServerModel();
        $serverModel->name = 'server model name';
        $serverModel->type = 'server model type';
        $serverModel->hwDescription = 'server model hw description';
        $serverModel->locationId = 1;

        return $serverModel;
    }

    private function getModifiedServerModel(ServerModel $serverModel): Model
    {
        /** @var ServerModel */
        $serverModel->name = 'updated server model name';
        $serverModel->type = 'updated server model type';
        $serverModel->hwDescription = 'updated server model hw description';
        $serverModel->locationId = 1;

        return $serverModel;
    }
}