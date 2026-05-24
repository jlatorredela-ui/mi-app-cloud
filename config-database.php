<?php
// config-database.php

function getDBConnection(): PDO {
    // Definimos los componentes limpios
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; // El tenant completo requerido por el pooler
    $pass = '**Ucv123456**/'; 
    $host = 'aws-0-us-east-2.pooler.supabase.com'; // Pooler oficial de tu región Ohio
    $port = '6543';
    $name = 'postgres';

    // SOLUCIÓN DEFINITIVA: Creamos una URI de conexión Postgres estructurada.
    // Esto obliga a PHP y a Render a enviar el Tenant ID de forma directa y compacta.
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;user=$user;password=$pass;sslmode=require";

    try {
        // Al enviar todo empaquetado en el DSN, el constructor de PDO no necesita parámetros sueltos
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}