<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * BinLookup resource
 *
 * @property int $bank_code Bank code
 * @property string $bank_name Bank name
 * @property string|null $bank_image Bank image URL
 * @property string|null $bin_number BIN number
 * @property string|null $domestic_intl Domestic or International indicator
 * @property string|null $card_brand Card brand (visa, mastercard, etc.)
 * @property string|null $card_brand_image_url Card brand image URL
 * @property string|null $card_type Card type (credit, debit)
 *
 * @package Tahsilat\Resource
 */
class BinLookup extends ApiResource
{
}
