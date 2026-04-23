<?php

namespace App\Enums;

enum LogModule: string
{
    case MASTER_DATA = 'master_data';
    case PURCHASING = 'purchasing';
    case UTILITY   = 'utility'; 

    case AUTH = 'auth';
}
