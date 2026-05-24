<?php
// config-database.php

function getDBConnection(): PDO {
    // Forzamos la cadena de conexión URI oficial de Supabase para el Pooler de Ohio (Puerto 6543)
    // El formato pasa el ID del proyecto directamente antes del host, asegurando que se encuentre el tenant.
    $user = 'postgres';
    $pass = '**Ucv123456**/';
    $host = 'postgres.qrfaqadirfmzvxbaijqp@aws-0-us-east-2.pooler.supabase.com';
    $port = '6543';
    $name = 'postgres';

    // Construcción del DSN usando la estructura de host con Tenant integrado
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

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