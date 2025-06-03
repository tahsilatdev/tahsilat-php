<?php

namespace Tahsilat\Resource;

/**
 * Customer resource
 *
 * @property int $id Customer ID
 * @property string $name Customer first name
 * @property string $lastname Customer last name
 * @property string $email Customer email
 * @property string $phone Customer phone
 * @property string $country Customer country
 * @property string $city Customer city
 * @property string $district Customer district
 * @property string $address Customer address
 * @property string $zip_code Customer zip code
 * @property array $metadata Customer metadata
 * @property string $created_at Creation timestamp
 *
 * @package Tahsilat\Resource
 */
class Customer extends ApiResource
{
}