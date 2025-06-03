<?php

namespace Tahsilat\Resource;

/**
 * Token resource
 *
 * @property string $access_token Access token value
 * @property bool $supports_3d Whether 3D secure is supported
 * @property bool $supports_2d Whether 2D payments are supported
 * @property bool $supports_white_label Whether white label is supported
 * @property bool $supports_installment Whether installments are supported
 * @property string $expires_at Token expiration date
 *
 * @package Tahsilat\Resource
 */
class Token extends ApiResource
{
}