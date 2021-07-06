<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application\Model;


class AddressModel
{
    /**
     * @var int|null
     */
    public ?int $id;

    /**
     * @var string
     */
    public string $street;

    /**
     * @var string
     */
    public string $houseNo;

    /**
     * @var string
     */
    public string $zipCode;

    /**
     * @var string
     */
    public string $city;

    /**
     * (in "real life" this would be a separate table in db and not a string
     * @var string
     */
    public string $country;
}