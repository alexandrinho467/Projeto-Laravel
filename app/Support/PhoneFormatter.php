<?php
namespace App\Support;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneFormatter
{
    public static function toE164(?string $raw, string $defaultRegion = 'AE'): ?string
    {
        if (empty($raw)) return $raw;

        try {
            $util   = PhoneNumberUtil::getInstance();
            $parsed = $util->parse($raw, $defaultRegion);

            if (!$util->isValidNumber($parsed)) {
                return $raw;
            }

            return $util->format($parsed, PhoneNumberFormat::E164);
        } catch (NumberParseException) {
            return $raw;
        }
    }
}
