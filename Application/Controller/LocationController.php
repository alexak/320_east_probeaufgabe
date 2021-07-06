<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application\Controller;

use \Application\Helper\DatabaseHelper;
use Application\Model\Model;
use Application\Model\LocationModel;



class LocationController extends DatabaseHelper implements ControllerInterface
{

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `location` (
                `id` INT NOT NULL AUTO_INCREMENT , 
                `datacenter_name` VARCHAR(125), 
                `rack_id` INT NULL,  
                `position` INT NULL,  
                `owner_id` INT NULL, 
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB; ';
        $this->executeOnDatabaseConnection($sql);
    }

    /**
     * @inheritDoc
     * @return Model
     */
    public function create(Model $model): Model
    {
        /** @var LocationModel $locationModel */
        $locationModel = $model;
        $this->connectToDatabase();
        $sql= 'INSERT INTO location (datacenter_name, rack_id, position, owner_id) VALUES (?, ?, ?, ?)';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('siii', $datacenterName, $rackId, $position, $ownerId);
        $datacenterName = $locationModel->datacenterName;
        $rackId = $locationModel->rackId;
        $position = $locationModel->position;
        $ownerId = $locationModel->ownerId;

        $statement->execute();
        $locationModel->id = $statement->insert_id;
        $statement->close();
        $this->mysqli->close();

        return $locationModel;
    }

    /**
     * function that createes or updates the model in database (post request)
     * @param Model $model
     * @return void
     */
    public function update(Model $model): void
    {
        /** @var LocationModel $locationModel */
        $locationModel = $model;
        $this->connectToDatabase();
        $sql= 'UPDATE location SET datacenter_name=?, rack_id=?, position=?, owner_id=? WHERE id=? ';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('siiii', $datacenterName, $rackId, $position, $ownerId, $id);
        $datacenterName = $locationModel->datacenterName;
        $rackId = $locationModel->rackId;
        $position = $locationModel->position;
        $ownerId = $locationModel->ownerId;
        $id = $locationModel->id;

        $statement->execute();
        $statement->close();
    }

    /**
     * @inheritDoc
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): ?Model
    {
        $this->select('SELECT * FROM location WHERE id=' .$id);
        if(0 < $this->mysqliResult->num_rows) {
            $locationModel = $this->arrayDatasToModel($this->mysqliResult->fetch_assoc());
            $this->mysqliResult->free_result();
        } else {
            $locationModel = null;
        }

        return $locationModel;
    }

    /**
     * function that fetches a dataset (get requedst
     * @return Model[]
     */
    public function getAll(): array
    {
        $this->select('SELECT * FROM location');
        $locationModels = [];
        while($arrayDatas = $this->mysqliResult->fetch_assoc()) {
            $locationModels[] = $this->arrayDatasToModel($arrayDatas);
        }
        $this->mysqliResult->free_result();

        return $locationModels;
    }

    /**
     * function that deletes a dataset by Id (get request)
     * @return void
     */
    public function delete(int $id): void
    {
        $this->executeOnDatabaseConnection('DELETE FROM location WHERE id=' .$id);
    }

    /**
     * translates array from database to server model object
     * @param array $locationDatas
     * @return LocationModel
     */
    private function arrayDatasToModel(array $locationDatas): LocationModel
    {
        $locationModel = new LocationModel();
        $locationModel->id = (int) $locationDatas['id'];
        $locationModel->datacenterName = $locationDatas['datacenter_name'];
        $locationModel->rackId = (int) $locationDatas['rack_id'];
        $locationModel->position = (int) $locationDatas['position'];
        $locationModel->ownerId = (int) $locationDatas['owner_id'];

        return $locationModel;
    }
}