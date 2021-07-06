<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application\Model;

use Application\Model\AddressModel as Address;

class OwnerModel extends Model
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
     * @var AddressModel
     */
    public Address $address;
}