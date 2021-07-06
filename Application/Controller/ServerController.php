<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application\Controller;

use Application\Helper\DatabaseHelper;
use Application\Model\Model;
use Application\Model\ServerModel;



class ServerController  extends DatabaseHelper implements ControllerInterface
{

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `server` ( 
                `id` INT NOT NULL AUTO_INCREMENT , 
                `name` VARCHAR(125) NOT NULL , 
                `type` VARCHAR(125) NOT NULL , 
                `hw_description` VARCHAR(255) NOT NULL , 
                `location_id` INT NULL , PRIMARY KEY (`id`)
            ) ENGINE = InnoDB; ';
        $this->executeOnDatabaseConnection($sql);
    }

    /**
     * @inheritDoc
     * @return Model
     */
    public function create(Model $model): Model
    {
        /** @var ServerModel $serverModel */
        $serverModel = $model;
        $this->connectToDatabase();
        $sql= 'INSERT INTO server (name, type, hw_description, location_id) VALUES (?, ?, ?, ?)';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('sssi', $name, $type, $hwDescription, $locationId);
        $name = $serverModel->name;
        $type = $serverModel->type;
        $hwDescription = $serverModel->hwDescription;
        $locationId = $serverModel->locationId;

        $statement->execute();
        $serverModel->id = $statement->insert_id;
        $statement->close();
        $this->mysqli->close();

        return $serverModel;
    }

    /**
     * function that createes or updates the model in database (post request)
     * @param Model $model
     * @return void
     */
    public function update(Model $model): void
    {
        /** @var ServerModel $serverModel */
        $serverModel = $model;
        $this->connectToDatabase();
        $sql= 'UPDATE server SET name=?, type=?, hw_description=?, location_id=? WHERE id=? ';
        $statement = $this->mysqli->prepare( $sql);
        $statement->bind_param('sssii', $name, $type, $hwDescription, $locationId, $id);
        $name = $serverModel->name;
        $type = $serverModel->type;
        $hwDescription = $serverModel->hwDescription;
        $locationId = $serverModel->locationId;
        $id = $serverModel->id;

        $statement->execute();
        $statement->close();
        $this->mysqli->close();
    }

    /**
     * @inheritDoc
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): ?Model
    {
        $this->select('SELECT * FROM server WHERE id=' .$id);
        if(0 < $this->mysqliResult->num_rows) {
            $serverModel = $this->arrayDatasToModel($this->mysqliResult->fetch_assoc());
            $this->mysqliResult->free_result();
            $this->mysqliResult->close();
        } else {
            $serverModel = null;
        }

        return $serverModel;
    }

    /**
     * function that fetches a dataset (get requedst
     * @return Model[]
     */
    public function getAll(): array
    {
        $this->select('SELECT * FROM server');
        $serverModels = [];
        while($arrayDatas = $this->mysqliResult->fetch_assoc()){
            $serverModels[] = $this->arrayDatasToModel($arrayDatas);
        }
        $this->mysqliResult->free_result();

        return $serverModels;
    }

    /**
     * function that deletes a dataset by Id (get request)
     * @return void
     */
    public function delete(int $id): void
    {
        $this->executeOnDatabaseConnection('DELETE FROM server WHERE id=' .$id);
    }

    /**
     * translates array from database to server model object
     * @param array $serverDatas
     * @return ServerModel
     */
    private function arrayDatasToModel(array $serverDatas): ServerModel
    {
        $serverModel = new ServerModel();
        $serverModel->id = (int)$serverDatas['id'];
        $serverModel->name = $serverDatas['name'];
        $serverModel->type = $serverDatas['type'];
        $serverModel->hwDescription = $serverDatas['hw_description'];
        $serverModel->locationId = $serverDatas['location_id'];

        return $serverModel;
    }
}