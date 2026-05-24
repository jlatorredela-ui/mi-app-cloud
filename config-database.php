<?php
// config-database.php

function getDBConnection(): PDO {
    $host = 'aws-1-us-east-2.pooler.supabase.com';
    $port = '5432';
    $db   = 'postgres';
    $user = 'postgres.qrfaqadirfmzvxbaijqp';
    $pass = 'UCV2026jorg'; // <--- ESCRIBE TU CONTRASEÑA REAL AQUÍ

    // Construcción del DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // IMPORTANTE: Esto ayuda a evitar errores de persistencia
            PDO::ATTR_PERSISTENT         => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión: " . $e->getMessage());
    }
}