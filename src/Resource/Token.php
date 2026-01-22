<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Token resource
 *
 * @property string|null $access_token Access token value
 * @property bool|null $supports_3d Whether 3D secure is supported
 * @property bool|null $supports_2d Whether 2D payments are supported
 * @property bool|null $supports_white_label Whether white label is supported
 * @property bool|null $supports_installment Whether installments are supported
 * @property string|null $expires_at Token expiration date
 *
 * @package Tahsilat\Resource
 */
class Token extends ApiResource
{
    /** @var string|null Access token value */
    public ?string $access_token = null;

    /** @var bool|null Whether 3D secure is supported */
    public ?bool $supports_3d = null;

    /** @var bool|null Whether 2D payments are supported */
    public ?bool $supports_2d = null;

    /** @var bool|null Whether white label is supported */
    public ?bool $supports_white_label = null;

    /** @var bool|null Whether installments are supported */
    public ?bool $supports_installment = null;

    /** @var string|null Token expiration date */
    public ?string $expires_at = null;
}
