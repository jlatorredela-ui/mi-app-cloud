<?php
// config-database.php

function getDBConnection(): PDO {
    // 1. Datos limpios de tu servidor de Supabase en Ohio
    $host = 'aws-0-us-east-2.pooler.supabase.com'; 
    $port = '6543'; 
    $name = 'postgres';
    
    // 2. Credenciales aisladas para que PHP no duplique ni rompa la cadena
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; 
    $pass = '**Ucv123456**/'; 

    // 3. El DSN solo debe llevar la ruta de conexión a la red
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        // 4. Pasamos el usuario y contraseña de forma nativa en el constructor
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