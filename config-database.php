<?php
// config-database.php

function getDBConnection(): PDO {
    // HOST DIRECTO: Para IPv6 es mejor evitar el 'pooler' y usar la dirección directa
    $host = 'db.qrfaqadirfmzvxbaijqp.supabase.co'; 
    $port = '5432'; 
    $db   = 'postgres';
    $user = 'postgres'; 
    $pass = 'TU_CONTRASEÑA_REAL'; // Pon tu contraseña aquí

    // Construcción del DSN
    // Al usar sslmode=verify-full o require, forzamos la seguridad necesaria en IPv6
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Esto es crucial: deshabilitar la emulación prepara al driver
            // para manejar conexiones nativas IPv6 más modernas
            PDO::ATTR_EMULATE_PREPARES   => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión IPv6/Directa: " . $e->getMessage());
    }
}