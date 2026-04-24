<?php

namespace App\Enums;

enum IdType: string
{
    case NationalId = 'national_id';
    case Passport = 'passport';
    case Iqama = 'iqama';
    case EmiratesId = 'emirates_id';
    case Other = 'other';
}
