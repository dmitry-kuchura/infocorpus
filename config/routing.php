<?php

return [
    /* Groups actions */
    'frontend/group-create' => 'frontend/groups/group-create',
    'frontend/group-list' => 'frontend/groups/group-list',
    'frontend/group-alert' => 'frontend/groups/group-alert',
    'frontend/group-change-status' => 'frontend/groups/group-change-status',
    'frontend/group-change-allow' => 'frontend/groups/group-change-allow',
    'frontend/group-delete' => 'frontend/groups/group-delete',
    'frontend/group-cancel' => 'frontend/groups/group-cancel',
    'frontend/group-send-message' => 'frontend/groups/group-send-message',

    /* Users actions */
    'frontend/create-user' => 'frontend/users/crate-user',
    'frontend/users-list' => 'frontend/users/users-list',
    'frontend/remove-user' => 'frontend/users/remove-user',
    'frontend/get-user-data' => 'frontend/users/get-user-data',
    'frontend/user-update' => 'frontend/users/user-update',

    /* Customers actions */
    'frontend/create-customer' => 'frontend/customers/create-customer',
    'frontend/update-customer' => 'frontend/customers/update-customer',
    'frontend/list-customers' => 'frontend/customers/list-customers',

    /* Others actions */
    'frontend/<action>' => 'frontend/frontend/<action>',
    'api/<action>' => 'api/api/<action>',
    '<action>' => 'site/index'
];