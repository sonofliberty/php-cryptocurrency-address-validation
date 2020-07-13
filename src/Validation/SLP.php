<?php

namespace Merkeleon\PhpCryptocurrencyAddressValidation\Validation;

use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\CashAddress;
use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\CashAddressException;
use Merkeleon\PhpCryptocurrencyAddressValidation\Validation;

class SLP extends Validation
{
    public function validate($address)
    {
        if (substr($address, 0, 13) !== "simpleledger:") {
            $address = "simpleledger:$address";
        }

        try {
            $testnet = false;
            $decoded = CashAddress::decodeNewAddr($address, true, $testnet);

            return is_array($decoded);
        } catch (CashAddressException $exception) {
            return false;
        }
    }
}