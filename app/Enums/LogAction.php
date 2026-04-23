<?php

namespace App\Enums;

enum LogAction: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case APPROVE = 'approve';
    case REJECT = 'reject';
    case PRINT = 'print';
    case EXPORT = 'export';
    case LOGIN = 'login';
}
