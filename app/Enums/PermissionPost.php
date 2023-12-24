<?php

namespace App\Enums;

enum PermissionPost: string
{
    case CREATE_POST = 'create_post';

    case EDIT_POST = 'edit_post';

    case UPDATE_POST = 'update_post';

    case DELETE_POST = 'delete_post';

    case VIEW_POST = 'view_post';
}
