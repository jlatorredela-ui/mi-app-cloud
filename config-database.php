<?php
// config-database.php

function getDBConnection(): PDO {
    // Captura las variables de entorno de Render
    $host = getenv('DB_HOST') ?: 'aws-0-sa-east-1.pooler.supabase.com';
    $port = getenv('DB_PORT') ?: '6543';
    $name = getenv('DB_NAME') ?: 'postgres';
    $user = getenv('DB_USER') ?: 'postgres';
    $pass = getenv('DB_PASS') ?: '**Ucv123456**/'; 

    // SOLUCIÓN DEFINITIVA: Formateamos el usuario con tu ID real de Supabase (qrfaqadirfmzvxbaijqp)
    if ($host === 'aws-0-sa-east-1.pooler.supabase.com') {
        $proyecto_id = "qrfaqadirfmzvxbaijqp"; // Tu ID real de Supabase
        $user_dsn = "{$user}.{$proyecto_id}";
    } else {
        $user_dsn = $user;
    }

    // Construcción del DSN corregido para el pooler cloud
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;user=$user_dsn;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}