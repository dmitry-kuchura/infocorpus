<?php

return [
    /* Groups actions */
    'frontend/group-create' => 'frontend/group/group-create',
    'frontend/group-list' => 'frontend/group/group-list',
    'frontend/group-alert' => 'frontend/group/group-alert',
    'frontend/group-change-status' => 'frontend/group/group-change-status',
    'frontend/group-change-allow' => 'frontend/group/group-change-allow',
    'frontend/group-delete' => 'frontend/group/group-delete',
    'frontend/group-cancel' => 'frontend/group/group-cancel',
    'frontend/group-send-message' => 'frontend/group/group-send-message',

    'frontend/<action>' => 'frontend/frontend/<action>',
    'api/<action>' => 'api/api/<action>',
    '<action>' => 'site/index'
];