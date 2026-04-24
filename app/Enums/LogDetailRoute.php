<?php

namespace App\Enums;

enum LogDetailRoute: string
{
    case USER = 'utility.users.show';
    case PURCHASE_REQUEST_INDEX = 'purchasing.purchase-requests.index';
    case PURCHASE_ORDER_INDEX = 'purchasing.purchase-orders.index';
}