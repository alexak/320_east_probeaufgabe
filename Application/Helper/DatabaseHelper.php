<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */

declare(strict_types = 1);

namespace Application\Helper;

use phpDocumentor\Reflection\Types\Void_;

class DatabaseHelper
{

    // in "real" life this should be provided from a config file, handled by the framework
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = 'alex1234';
    const DB_DATABSE = '320east_probeaufgabe';

    protected ?\mysqli $mysqli;
    protected ?\mysqli_result $mysqliResult;

    /**
     * connects to database server
     * @return void
     */
    public function connectToDatabaseServer(): void
    {
        $this->mysqli = mysqli_init();
        try {
            $this->mysqli->real_connect(self::DB_HOST, self::DB_USER, self::DB_PASS);
        }  catch (\Exception $exception) {
            $this->throwConnectionError();
        }
    }

    /**
     * connects to database
     * @return void
     */
    public function connectToDatabase(): void
    {
        $this->mysqli = mysqli_init();
        try {
            $this->mysqli->real_connect(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_DATABSE);
        } catch (\Exception $exception) {
            $this->throwConnectionError();
        }
    }

    /**
     * creates a new database if non existant
     */
    public function initDb(): void
    {
        $this->mysqli = mysqli_init();
        $this->connectToDatabaseServer();
        $this->mysqli->query('CREATE DATABASE IF NOT EXISTS ' .self::DB_DATABSE .';');
        $this->mysqli->close();
    }

    /**
     * executes the given sql, no result
     * @return void
     */
    public function executeOnDatabaseServerConnection($sql): void
    {
        if (!empty($sql)) {
            $this->mysqli = mysqli_init();
            $this->connectToDatabaseServer();
            if (!$this->mysqli->query($sql)) {
                $errno = $this->mysqli->errno;
                $error = $this->mysqli->error;
                $this->mysqli = null;
                throw new \Exception('Database execution error: ' .$errno .'-' .$error, $errno );
            }
            $this->mysqli->close();
        }
    }

    /**
     * executes the given sql, no result
     * @return void
     */
    public function executeOnDatabaseConnection($sql): void
    {
        if (!empty($sql)) {
            $this->mysqli = mysqli_init();
            $this->connectToDatabase();
            if (!$this->mysqli->query($sql)) {
                $errno = $this->mysqli->errno;
                $error = $this->mysqli->error;
                $this->mysqli = null;
                throw new \Exception('Database execution error: ' .$errno .'-' .$error, $errno );
            }
            $this->mysqli->close();
        }
    }

    /**
     * fetches a select from database
     */
    public function select($sql): void
    {
        $this->mysqli = mysqli_init();
        $this->connectToDatabase();
        $this->mysqliResult = $this->mysqli->query($sql);
        $this->mysqli->close();
    }

    /**
     * @return void
     */
    private function throwConnectionError(): void
    {
        $errno = $this->mysqli->connect_errno;
        $error = $this->mysqli->connect_error;
        $this->mysqli = null;
        throw new \Exception('Error while connecting: ' .$errno . '-' .$error );
    }

    /**
     * @return \mysqli_result
     */
    public function getMysqliResult(): \mysqli_result
    {
        return $this->mysqliResult;
    }
}