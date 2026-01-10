<?php
return [
    'sae' => [
        'name'        => 'SAE - Sistema de Apoio à Enfermaria',
        'login_url'   => 'http://localhost/enfermaria/public/index.php?route=sso',
        'dsn'         => 'mysql:host=localhost;dbname=enfermaria;charset=utf8mb4',
        'db_user'     => 'root',
        'db_pass'     => '',
        'users_table' => 'users',
        'email_field' => 'email',
    ],

    'crewgest' => [
        'name'        => 'CrewGest - Gestão de Fardas',
        'login_url'   => 'http://localhost/economato/sso_login.php',
        'dsn'         => 'mysql:host=localhost;dbname=econo_app;charset=utf8mb4',
        'db_user'     => 'root',
        'db_pass'     => '',
        'users_table' => 'utilizadores',
        'email_field' => 'email',
    ],

    'worklog' => [
        'name'        => 'WorkLog - CMMS',
        'login_url'   => 'http://localhost/work_log/sso_login.php',
        'dsn'         => 'mysql:host=localhost;dbname=cmms;charset=utf8mb4',
        'db_user'     => 'root',
        'db_pass'     => '',
        'users_table' => 'users',
        'email_field' => 'email',
    ],
];
