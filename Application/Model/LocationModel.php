<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application\Model;

use Application\Model\Model;

class LocationModel extends Model
{
    /**
     * @var int|null
     */
    public ?int $id;

    /**
     * @var string
     */
    public string $datacenterName;

    /**
     * @var int|null
     */
    public ?int $rackId;

    /**
     * @var int|null
     */
    public ?int $position;

    /**
     * @var int|null
     */
    public ?int $ownerId;
}