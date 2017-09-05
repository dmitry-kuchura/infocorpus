<?php

return [
    'adminEmail' => 'admin@example.com',
    'carStatus' => [
        0 => 'В гараже',
        1 => 'На патруле',
        2 => 'На вызове',
    ],
    'photo' => [
        [
            'path' => 'big',
            'width' => 400,
            'height' => 400,
            'resize' => 1,
            'crop' => 1,
        ],
        [
            'path' => 'small',
            'width' => 250,
            'height' => 250,
            'resize' => 1,
            'crop' => 1,
        ],
        [
            'path' => 'original',
            'resize' => 0,
            'crop' => 0,
        ],
    ],
];
