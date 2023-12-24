<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'admin';

    case CREATOR = 'creator';

    case GUEST = 'guest';
}
