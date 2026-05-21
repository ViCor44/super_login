<?php
return [

    // ─── Credenciais centrais do Super Login ─────────────────────────────────
    // Usadas pelo sso_login.php para validar tokens.
    // Alterar apenas se mudares a BD ou as credenciais do super_login.
    '_config' => [
        'dsn'  => 'mysql:host=localhost;dbname=super_login;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
    ],

    // ─── Sistemas ─────────────────────────────────────────────────────────────
    // Para adicionar um novo sistema, basta copiar um bloco abaixo,
    // ajustar os valores e colocar o sso_login.php na pasta do sistema.

    'sae' => [
        'name'         => 'SAE - Sistema de Apoio à Enfermaria',
        // Aponta para o ficheiro standalone SSO (rota MVC não registada)
        'login_url'    => 'http://191.188.126.13/enfermaria/sso_login.php',
        'dsn'          => 'mysql:host=localhost;dbname=enfermaria;charset=utf8mb4',
        'db_user'      => 'root',
        'db_pass'      => '',
        'users_table'  => 'users',
        'email_field'  => 'email',
        'id_field'     => 'id',
        'redirect_ok'  => '/enfermaria/public/index.php?route=dashboard',
        // Variáveis de sessão: 'nome_sessão' => 'coluna_na_bd'
        'session_vars' => [
            'user_id'   => 'id',
            'user_name' => 'full_name',
            'role'      => 'role',
            'last_login'=> 'last_login',
        ],
    ],

    'crewgest' => [
        'name'         => 'CrewGest - Gestão de Fardas',
        'login_url'    => 'http://191.188.126.13/economato/sso_login.php',
        'dsn'          => 'mysql:host=localhost;dbname=econo_app;charset=utf8mb4',
        'db_user'      => 'root',
        'db_pass'      => '',
        'users_table'  => 'utilizadores',
        'email_field'  => 'email',
        'id_field'     => 'id',
        'redirect_ok'  => '/economato/public/index.php',
        // Variáveis de sessão: 'nome_sessão' => 'coluna_na_bd'
        'session_vars' => [
            'user_id'      => 'id',
            'user_email'   => 'email',
            'user_role_id' => 'role_id',
            'user_name'    => 'nome',
        ],
    ],

    'worklog' => [
        'name'         => 'WorkLog - CMMS',
        'login_url'    => 'http://191.188.126.13/work_log/sso_login.php',
        'dsn'          => 'mysql:host=localhost;dbname=cmms;charset=utf8mb4',
        'db_user'      => 'root',
        'db_pass'      => '',
        'users_table'  => 'users',
        'email_field'  => 'email',
        'id_field'     => 'id',
        'redirect_ok'  => '/work_log/index.php',
    ],

    'freezer' => [
        'name'         => 'Freezer Monitor',
        'login_url'    => 'http://191.188.126.13/freezer-monitor/sso_login.php',
        'dsn'          => 'mysql:host=localhost;dbname=freezer_monitor;charset=utf8mb4',
        'db_user'      => 'root',
        'db_pass'      => '',
        'users_table'  => 'users',
        'email_field'  => 'email',
        'id_field'     => 'id',
        'redirect_ok'  => '/freezer-monitor/public/dashboard',
        'session_vars' => [
            'user_id'       => 'id',
            'user_name'     => 'name',
            'user_email'    => 'email',
            'user_role'     => 'role',
            'user_approved' => 'approved',
        ],
    ],

    'repositorio' => [
        'name'         => 'Repositório de Documentos',
        'login_url'    => 'http://191.188.126.13/repositorio/sso_login.php',
        'dsn'          => 'mysql:host=localhost;dbname=parque_repositorio;charset=utf8mb4',
        'db_user'      => 'root',
        'db_pass'      => '',
        'users_table'  => 'users',
        'email_field'  => 'email',
        'id_field'     => 'id',
        'redirect_ok'  => '/repositorio/public/documentos',
        'session_vars' => [
            'user.id'   => 'id',
            'user.nome' => 'nome',
            'user.role' => 'role_nome',
        ],
    ],

    'autoprotecao' => [
        'name'         => 'Sistema de Autoprotecção',
        'login_url'    => 'http://191.188.126.13/sistema-autoprotecao/sso_login.php',
        'dsn'          => 'mysql:host=localhost;dbname=sistema_autoprotecao;charset=utf8mb4',
        'db_user'      => 'root',
        'db_pass'      => '',
        'users_table'  => 'utilizadores',
        'email_field'  => 'email',
        'id_field'     => 'id',
        'redirect_ok'  => '/sistema-autoprotecao/public/index.php',
        'session_vars' => [
            'utilizador_id'     => 'id',
            'utilizador_nome'   => 'nome',
            'utilizador_email'  => 'email',
            'utilizador_funcao' => 'funcao',
        ],
    ],
];
