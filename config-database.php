<?php
// config-database.php

function getDBConnection(): PDO {
    // Usamos el host oficial del pooler en la región correcta (Ohio: us-east-2)
    $host = 'aws-0-us-east-2.pooler.supabase.com'; 
    $port = '6543'; 
    $name = 'postgres';
    
    // El usuario estricto combinando la autenticación del Pooler con tu ID de proyecto
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; 
    $pass = '**Ucv123456**/'; 

    // Agregamos opciones específicas de PostgreSQL para forzar un manejo de conexión limpia
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