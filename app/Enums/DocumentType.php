<?php

namespace App\Enums;

enum DocumentType: string
{
    case Passport = 'passport';
    case DriversLicense = 'drivers_license';
    case NationalId = 'national_id';
    case GovernmentIssuedId = 'government_issued_id';

    public function label(): string
    {
        return match ($this) {
            self::Passport => 'Passport',
            self::DriversLicense => "Driver's license",
            self::NationalId => 'National ID',
            self::GovernmentIssuedId => 'Government-issued ID',
        };
    }
}
