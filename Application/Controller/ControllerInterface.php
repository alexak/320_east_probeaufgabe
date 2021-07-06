<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */

namespace Application\Controller;

use Application\Model\Model;

interface ControllerInterface
{
    /**
     * function that initializes database ..
     * @return void
     */
    public function init(): void;

    /**
     * function that createes or updates the model in database (post request)
     * @param Model $model
     * @return Model
     */
    public function create(Model $model): Model;

    /**
     * function that createes or updates the model in database (post request)
     * @param Model $model
     * @return void
     */
    public function update(Model $model): void;

    /**
     * function that fetches a dataset by Id (get requedst)
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): ?Model;

    /**
     * function that fetches a dataset by Id (get requedst
     * @return Model[]
     */
    public function getAll(): array;

    /**
     * function that deletes a dataset by Id (get request)
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

}

