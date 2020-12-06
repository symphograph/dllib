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
        ]
];