<?php

namespace App\Enums;

enum PermissionComments: string
{
    case ADD_COMMENT = 'add_comment';

    case EDIT_COMMENT = 'edit_comment';

    case UPDATE_COMMENT = 'update_comment';

    case DELETE_COMMENT = 'delete_comment';

    case CONFIRM_COMMENT = 'confirm_comment';

    case VIEW_COMMENT = 'view_comment';

    case USER_BLOCK = 'user_block';

}
