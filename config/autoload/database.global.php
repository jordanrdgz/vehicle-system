<?php

return [
    'db' => [
        'adapters' => [
            'dbSite' => [
        		'driver'    => 'Pdo_Mysql',
        		'dsn'       => "mysql:host=127.0.0.1;dbname=vehicles",
        		'username'  => 'root',
        		'password'  => '123456',
            ],
        ]
    ],
];
