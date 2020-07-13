<?php

namespace Merkeleon\PhpCryptocurrencyAddressValidation\Validation;

use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\CashAddressException;
use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\SlpAddress;
use Merkeleon\PhpCryptocurrencyAddressValidation\Validation;

class SLP extends Validation
{
    public function validate($address)
    {
        try {
            $testnet = false;
            $decoded = SlpAddress::decodeNewAddr($address, true, $testnet);

            return is_array($decoded);
        } catch (CashAddressException $exception) {
            return false;
        }
    }
}