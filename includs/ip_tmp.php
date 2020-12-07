<?php
return (object) [
    'ip' => $_SERVER['REMOTE_ADDR'],
    'myip' => ($_SERVER['REMOTE_ADDR'] === '127.0 0.1'),
    'server_ip' => ($_SERVER['REMOTE_ADDR'] === '127.0 0.1'),
    'yandex_key' => '4878c37eb34cedcf',
    'connects' =>
    [
        'dev.domen.ru'=>
        [
            'Host' => 'localhost',
            'Name' => 'dbaseName',
            'User' => 'root',
            'Pass' => ''
        ],

        'test.domen.ru'=>
        [
            'Host' => 'localhost',
            'Name' => 'dbaseName',
            'User' => 'root',
            'Pass' => ''
        ],

        'domen.ru'=>
        [
            'Host' => 'localhost',
            'Name' => 'dbaseName',
            'User' => 'root',
            'Pass' => ''
        ]
    ],

    'mailru_secrets' =>
    [
        'test.domen.ru' =>
        [
            'app_id' => 000000,
            'app_private' => 'xxxxx...',
            'app_secret' => 'xxxxx...'
        ],

        'domen.ru' =>
        [
            'app_id' => 000000,
            'app_private' => 'xxxxx...',
            'app_secret' => 'xxxxx...'
        ],
    ],

    'broken_avas' =>
    [

    ]
];