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

    'frontend/<action>' => 'frontend/frontend/<action>',
    'api/<action>' => 'api/api/<action>',
    '<action>' => 'site/index'
];