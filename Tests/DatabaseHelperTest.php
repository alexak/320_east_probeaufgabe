<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */
declare(strict_types=1);

namespace Tests;

use Application\Autoloader;
use Application\Helper\DatabaseHelper;
use PHPUnit\Framework\TestCase;

require '../Application/Autoloader.php';

final class DatabaseHelperTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Autoloader::register();
    }

    /**
     * Happy path testing connection to database server
     * @return void
     */
    public function testDatabaseServerConnectionHappyPath(): void
    {
        $databaseHelper = new DatabaseHelper();
        $databaseHelper->connectToDatabaseServer();
        $this->addToAssertionCount(1);
    }

    /**
     * Happy path testing connection to database server
     * @return void
     */
    public function testDatabaseConnectionHappyPath(): void
    {
        $databaseHelper = new DatabaseHelper();
        $databaseHelper->initDb();
        $databaseHelper->connectToDatabase();
        $this->addToAssertionCount(1);
    }
}