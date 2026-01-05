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
    /** @var string|null Access token value */
    public $access_token;

    /** @var bool|null Whether 3D secure is supported */
    public $supports_3d;

    /** @var bool|null Whether 2D payments are supported */
    public $supports_2d;

    /** @var bool|null Whether white label is supported */
    public $supports_white_label;

    /** @var bool|null Whether installments are supported */
    public $supports_installment;

    /** @var string|null Token expiration date */
    public $expires_at;
}