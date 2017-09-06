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
    'frontend/group-get-data' => 'frontend/groups/group-get-data',
    'frontend/group-send-message' => 'frontend/groups/group-send-message',

    /* Users actions */
    'frontend/user-create' => 'frontend/users/user-create',
    'frontend/user-list' => 'frontend/users/user-list',
    'frontend/user-remove' => 'frontend/users/user-remove',
    'frontend/user-get-data' => 'frontend/users/user-get-data',
    'frontend/user-update' => 'frontend/users/user-update',

    /* Customers actions */
    'frontend/customer-create' => 'frontend/customers/customer-create',
    'frontend/customer-update' => 'frontend/customers/customer-update',
    'frontend/customer-list' => 'frontend/customers/customer-list',

    /* Others actions */
    'frontend/<action>' => 'frontend/frontend/<action>',
    'api/<action>' => 'api/api/<action>',
    '<action>' => 'site/index',
];