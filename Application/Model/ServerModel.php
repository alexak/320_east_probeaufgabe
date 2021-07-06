<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application\Model;

use Application\Model\Model;

class ServerModel extends Model
{
    /**
     * @var int|null
     */
    public ?int $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $type;

    /**
     * @var string
     */
    public string $hwDescription;

    /**
     * @var int|null
     */
    public ?int $locationId;
}