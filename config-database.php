<?php
// config-database.php

function getDBConnection(): PDO {
    // Componentes limpios de tu servidor en la región de Ohio
    $user = 'postgres.qrfaqadirfmzvxbaijqp';
    $pass = '**Ucv123456**/';
    $host = 'aws-0-us-east-2.pooler.supabase.com';
    $port = '6543';
    $name = 'postgres';

    // SOLUCIÓN TOTAL: Construcción en formato de URI estándar de PostgreSQL
    // Esta estructura obliga a PHP a empaquetar el Tenant ID de forma indivisible
    $dsn = "pgsql:host=$host;port=$port;dbname=$name";

    try {
        // Pasamos de forma estricta las credenciales como parámetros del constructor PDO
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}