<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=super_login;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '', // domínio base!
    'secure' => true,          // se estiveres em HTTPS
    'httponly' => true,
    'samesite' => 'Lax',      // importante para redirects
]);
session_name('SUPERLOGINSESSID');
session_start();



