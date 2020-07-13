<?php

namespace Merkeleon\PhpCryptocurrencyAddressValidation\Validation;

use Merkeleon\PhpCryptocurrencyAddressValidation\Base58Validation;
use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\CashAddress;
use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\CashAddressException;

class BCH extends Base58Validation
{
    // more info at https://en.bitcoin.it/wiki/List_of_address_prefixes
    protected $base58PrefixToHexVersion = [
        '1' => '00',
        '3' => '05'
    ];

    public function validate($address)
    {
        try {
            $testnet = false;
            $decoded = CashAddress::decodeNewAddr($address, true, $testnet);

            return is_array($decoded);
        } catch (CashAddressException $exception) {
            return parent::validate($address);
        }
    }
}
