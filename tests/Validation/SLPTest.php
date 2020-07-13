<?php

namespace Tests\Validation;

use Merkeleon\PhpCryptocurrencyAddressValidation\Validation;

class SLPTest extends BaseValidationTestCase
{
    public function getValidationInstance(): Validation
    {
        return Validation::make('SLP');
    }

    public function addressProvider()
    {
        return [
            ['simpleledger:qq88yw75nshu53y0y2ednylalwl7jl29agunqcjy80', true], // valid slpaddr
            ['qq88yw75nshu53y0y2ednylalwl7jl29agunqcjy80', true], // valid slpaddr w/o prefix
            ['simpleledger:qq88yw75nshu53y0y2ednylalwl7jl29agunqcjy81', false], // invalid checksum
            ['bitcoincash:qq88yw75nshu53y0y2ednylalwl7jl29agsgtr8ye3', false], // cash addr
            ['qq88yw75nshu53y0y2ednylalwl7jl29agsgtr8ye3', false], // cash addr w/o prefix
            ['simpleledger:qq88yw75nshu53y0y2ednylalwl7jl29agsgtr8ye3', false], // cash addr w/ slp prefix
            ['12KPJnRWGFTk3nTNVUxbfvy4d6ErzyFxpZ', false], // legacy
        ];
    }
}
