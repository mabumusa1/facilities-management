<?php

namespace App\Enums;

enum LeaseNoticeType: string
{
    case RentIncrease = 'rent_increase';
    case RenewalOffer = 'renewal_offer';
    case MoveOutReminder = 'move_out_reminder';
    case FreeForm = 'free_form';
}
