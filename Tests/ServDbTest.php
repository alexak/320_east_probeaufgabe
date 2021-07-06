<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */
declare(strict_types=1);

namespace Tests;

use Application\Autoloader;
use Application\Service\ServDb;
use Application\Helper\DatabaseHelper;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

require '../Application/Autoloader.php';

final class ServDbTest extends TestCase
{
     const URL = 'dev.320east-probeaufgabe.local';                          //@todo: adapt URL here (in real life this should be done with a config file

     public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Autoloader::register();
    }

    /**
     * Test, if the project database is initialized correctly
     * @throws \Exception
     */
    public function testSetUp(): void
    {
        $servDb = new ServDb();
        $servDb->init();

        // database created and available?
        $databaseHelper = new DatabaseHelper();
        $databaseHelper->connectToDatabase();
        $this->addToAssertionCount(1);

        // table "server" has been created and is available
        $databaseHelper->select($this->getTableCheckSql('server'));
        self::assertSame(1, $databaseHelper->getMysqliResult()->num_rows);

        // table "location" has been created and is available
        $databaseHelper->select($this->getTableCheckSql('location'));
        self::assertSame(1, $databaseHelper->getMysqliResult()->num_rows);

        // table "owner" has been created and is available
        $databaseHelper->select($this->getTableCheckSql('owner'));
        self::assertSame(1, $databaseHelper->getMysqliResult()->num_rows);

        // table "owner" has been created and is available
        $databaseHelper->select($this->getTableCheckSql('address'));
        self::assertSame(1, $databaseHelper->getMysqliResult()->num_rows);
    }

    /**
     * constructs a query to check if a table is given in Mysql database
     * @param $tableName
     * @return string
     */
    private function getTableCheckSql($tableName): string {
        return "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = '" .DatabaseHelper::DB_DATABSE ."' AND table_name='" .$tableName ."';";
    }

    /**
     * test with different controller names
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testControllerNames(): void
    {
        $httpClient = new Client();

        // invalid controller name
        $response = $httpClient->request('GET', self::URL, [
            'query' => ['controller' => 'inExistantController']
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $httpClient = new Client();
        $response = $httpClient->request('GET', self::URL, [
            'query' => ['controller' => 'server']
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $httpClient = new Client();
        $response = $httpClient->request('GET', self::URL, [
            'query' => ['controller' => 'location']
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $httpClient = new Client();
        $response = $httpClient->request('GET', self::URL, [
            'query' => ['controller' => 'owner']
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * test with valid controller name but invalid action
     */
    public function testActionNames(): void
    {
        $httpClient = new Client();

        // invalid action name
        $response = $httpClient->request('GET', self::URL, [
            'query' => [
                'controller' => 'server',
                'action' => 'invalidActionName'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        // delete action without Id
        $response = $httpClient->request('GET', self::URL, [
            'query' => [
                'controller' => 'server',
                'action' => 'delete'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        // getById action without Id
        $response = $httpClient->request('GET', self::URL, [
            'query' => [
                'controller' => 'server',
                'action' => 'getById'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());


        // getById action with empty id
        $response = $httpClient->request('GET', self::URL, [
            'query' => [
                'controller' => 'server',
                'action' => 'getById',
                'id' => '',
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        // createOrUpdate action
        $response = $httpClient->request('GET', self::URL, [
            'query' => [
                'controller' => 'server',
                'action' => 'createOrUpdate'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

}