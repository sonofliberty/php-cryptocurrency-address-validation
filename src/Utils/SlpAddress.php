<?php

namespace Merkeleon\PhpCryptocurrencyAddressValidation\Utils;

class SlpAddress extends CashAddress
{
    const EXPAND_PREFIX         = [19, 9, 13, 16, 12, 5, 12, 5, 4, 7, 5, 18, 0];
    const EXPAND_PREFIX_TESTNET = [19, 12, 16, 20, 5, 19, 20, 0];

    /**
     * Decodes Cash Address.
     * @param  string $inputNew New address to be decoded.
     * @param  boolean $shouldFixErrors Whether to fix typing errors.
     * @param  boolean &$isTestnetAddressResult Is pointer, set to whether it's
     * a testnet address.
     * @return array $decoded Returns decoded byte array if it can be decoded.
     * @return string $correctedAddress Returns the corrected address if there's
     * a typing error.
     * @throws CashAddressException
     */
    static public function decodeNewAddr($inputNew, $shouldFixErrors, &$isTestnetAddressResult)
    {
        $inputNew = strtolower($inputNew);
        if (strpos($inputNew, ":") === false)
        {
            $afterPrefix            = 0;
            $data                   = self::EXPAND_PREFIX;
            $isTestnetAddressResult = false;
        }
        else if (substr($inputNew, 0, 13) === "simpleledger:")
        {
            $afterPrefix            = 13;
            $data                   = self::EXPAND_PREFIX;
            $isTestnetAddressResult = false;
        }
//        else if (substr($inputNew, 0, 8) === "slptest:")
//        {
//            $afterPrefix            = 8;
//            $data                   = self::EXPAND_PREFIX_TESTNET;
//            $isTestnetAddressResult = true;
//        }
        else
        {
            throw new CashAddressException('Unknown address type');
        }
        for ($values = []; $afterPrefix < strlen($inputNew); $afterPrefix++)
        {
            if (!array_key_exists($inputNew[$afterPrefix], self::BECH_ALPHABET))
            {
                throw new CashAddressException('Unexpected character in address!');
            }
            array_push($values, self::BECH_ALPHABET[$inputNew[$afterPrefix]]);
        }
        $data     = array_merge($data, $values);
        $checksum = self::polyMod($data);
        if ($checksum != 0)
        {
            // Checksum is wrong!
            // Try to fix up to two errors
            if ($shouldFixErrors)
            {
                $syndromes = [];
                for ($p = 0; $p < sizeof($data); $p++)
                {
                    for ($e = 1; $e < 32; $e++)
                    {
                        $data[$p] ^= $e;
                        $c        = self::polyMod($data);
                        if ($c == 0)
                        {
                            return self::rebuildAddress($data);
                        }
                        $syndromes[$c ^ $checksum] = $p * 32 + $e;
                        $data[$p]                  ^= $e;
                    }
                }
                foreach ($syndromes as $s0 => $pe)
                {
                    if (array_key_exists($s0 ^ $checksum, $syndromes))
                    {
                        $data[$pe >> 5]                         ^= $pe % 32;
                        $data[$syndromes[$s0 ^ $checksum] >> 5] ^= $syndromes[$s0 ^ $checksum] % 32;

                        return self::rebuildAddress($data);
                    }
                }
                // Can't correct errors!
                throw new CashAddressException('Can\'t correct typing errors!');
            }
        }

        return $values;
    }
}