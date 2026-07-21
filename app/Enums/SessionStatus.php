<?php

namespace App\Enums;

enum SessionStatus: string
{
    case Active = 'Active';
    case Ended = 'Ended';
}
