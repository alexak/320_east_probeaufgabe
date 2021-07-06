<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */

namespace Application\Controller;

use Application\Helper\DatabaseHelper;
use Application\Model\AddressModel;
use Application\Model\Model;
use Application\Model\OwnerModel;

class OwnerController extends DatabaseHelper implements ControllerInterface
{

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $this->initOwnerTable();
        $this->initAddressTable();
    }

    /**
     * @inheritDoc
     * @return Model
     */
    public function create(Model $model): Model
    {
        /** @var OwnerModel $ownerModel */
        $ownerModel = $model;
        $addressModel = $this->createAddress($ownerModel->address);
        $ownerModel->id = $this->insertOwner($ownerModel, $addressModel);

        return $ownerModel;
    }

    /**
     * function that createes or updates the model in database (post request)
     * @param Model $model
     */
    public function update(Model $model): void
    {
        /** @var OwnerModel $ownerModel */
        $ownerModel = $model;
        $this->updateOwner($ownerModel);
        $this->updateAddress($ownerModel);
    }

    /**
     * @inheritDoc
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): ?Model
    {
        $sql = 'SELECT owner.id as owner_id, owner.name as owner_name, 
                address.id as address_id, address.street as address_street, address.house_no as address_house_no, address.zip_code as address_zip_code, address.city as address_city, address.country as address_country
                FROM owner
                JOIN address ON owner.address_id = address.id 
                WHERE owner.id=' .$id;
        $this->select($sql);
        if(0 < $this->mysqliResult->num_rows) {
            $ownerModel = $this->arrayDatasToModel($this->mysqliResult->fetch_assoc());
            $this->mysqliResult->free_result();
        } else {
            $ownerModel = null;
        }

        return $ownerModel;
    }

    /**
     * function that deletes a dataset by Id (get request)
     * @return void
     */
    public function delete(int $id): void
    {
        $this->select('SELECT address_id FROM owner WHERE id=' .$id);
        if (0 < $this->mysqliResult->num_rows) {
            $esultDatas = $this->mysqliResult->fetch_assoc();
            $this->executeOnDatabaseConnection('DELETE FROM address WHERE id=' .$esultDatas['address_id']);
            $this->mysqliResult->free_result();
        }
        $this->executeOnDatabaseConnection('DELETE FROM owner WHERE id=' .$id);
    }

    /**
     * function that fetches a dataset by Id (get requedst
     * @return Model[]
     */
    public function getAll(): array
    {
        $sql = 'SELECT owner.id as owner_id, owner.name as owner_name, 
                address.id as address_id, address.street as address_street, address.house_no as address_house_no, address.zip_code as address_zip_code, address.city as address_city, address.country as address_country
                FROM owner
                JOIN address ON owner.address_id = address.id';
        $this->select($sql);
        $ownerModels = [];
        if(0 < $this->mysqliResult->num_rows) {
            while($ownerDatas = $this->mysqliResult->fetch_assoc()){
                $ownerModels[] = $this->arrayDatasToModel($ownerDatas);
            }
            $this->mysqliResult->free_result();
        }

        return $ownerModels;
    }

    /**
     * function that creates owner table in db
     */
    private function initOwnerTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `owner` ( 
                `id` INT NOT NULL AUTO_INCREMENT,
                 `name` VARCHAR(125),
                 `address_id` INT, 
                 PRIMARY KEY (`id`)
             ) ENGINE = InnoDB; ';
        $this->executeOnDatabaseConnection($sql);
    }

    /**
     * function that creates address table in db
     */
    private function initAddressTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `address` (
            `id` INT NOT NULL AUTO_INCREMENT,
             `street` VARCHAR(125), 
             `house_no` VARCHAR(4),
             `zip_code` VARCHAR(5),
             `city` VARCHAR(255),
             `country` VARCHAR(255), PRIMARY KEY (`id`)
         ) ENGINE = InnoDB; ';
        $this->executeOnDatabaseConnection($sql);
    }

    /**
     * @inheritDoc
     * @param AddressModel addressModel
     * @return AddressModel
     */
    private function createAddress(AddressModel $addressModel): AddressModel
    {
        $this->connectToDatabase();
        $sql= 'INSERT INTO address (street, house_no, zip_code, city, country) VALUES (?, ?, ?, ?, ?)';
        $statement = $this->mysqli->prepare( $sql);

        $statement->bind_param('sssss', $street, $houseNo, $zipCode, $city, $country);
        $street = $addressModel->street;
        $houseNo = $addressModel->houseNo;
        $zipCode = $addressModel->zipCode;
        $city = $addressModel->city;
        $country = $addressModel->country;

        $statement->execute();
        $addressModel->id = $statement->insert_id;
        $statement->close();
        $this->mysqli->close();

        return $addressModel;
    }

    /**
     * creates a new entry in owner table, returns Id of the newly created row
     * @inheritDoc
     * @param OwnerModel $ownerModel
     * @param AddressModel $addressModel
     * @return int
     */
    private function insertOwner(OwnerModel $ownerModel, AddressModel $addressModel): int
    {
        $this->connectToDatabase();
        $sql= 'INSERT INTO owner (name, address_id) VALUES (?, ?)';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('si', $name, $addressId);
        $name = $ownerModel->name;
        $addressId = $addressModel->id;

        $statement->execute();
        $ownerId= $statement->insert_id;
        $statement->close();
        $this->mysqli->close();

        return $ownerId;
    }

    /**
     * function that updates the owner in table
     * @param OwnerModel $ownerModel
     */
    private function updateOwner(OwnerModel $ownerModel): void
    {
        $this->connectToDatabase();
        $sql= 'UPDATE owner SET name=? WHERE id=? ';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('si', $name, $id);
        $name = $ownerModel->name;
        $id = $ownerModel->id;

        $statement->execute();
        $statement->close();
        $this->mysqli->close();
    }

    /**
     * function that uodates addrees of the owner in database
     * @param OwnerModel $ownerModel
     */
    private function updateAddress(OwnerModel $ownerModel): void
    {
        $addressModel = $ownerModel->address;
        $this->connectToDatabase();
        $sql= 'UPDATE address SET street=?, house_no=?, zip_code=?, city=?, country=? WHERE id=? ';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('sssssi', $street, $houseNo, $zipCode, $city, $country, $id);
        $street = $addressModel->street;
        $houseNo = $addressModel->houseNo;
        $zipCode = $addressModel->zipCode;
        $city = $addressModel->city;
        $country = $addressModel->country;
        $id = $addressModel->id;

        $statement->execute();
        $statement->close();
        $this->mysqli->close();
    }

    /**
     * function that conversts array from mysql response to model
     * @param array $ownerDatas
     * @return ?OwnerModel
     */
    private function arrayDatasToModel(array $ownerDatas): ?OwnerModel
    {
        $addressModel = new AddressModel();
        $addressModel->id = (int)$ownerDatas['address_id'];
        $addressModel->street = $ownerDatas['address_street'];
        $addressModel->houseNo = $ownerDatas['address_house_no'];
        $addressModel->zipCode = $ownerDatas['address_zip_code'];
        $addressModel->city = $ownerDatas['address_city'];
        $addressModel->country = $ownerDatas['address_country'];

        $ownerModel = new OwnerModel();
        $ownerModel->id = (int)$ownerDatas['owner_id'];
        $ownerModel->name = $ownerDatas['owner_name'];
        $ownerModel->address = $addressModel;

        return $ownerModel;
    }
}