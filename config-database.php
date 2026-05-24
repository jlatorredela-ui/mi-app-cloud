<?php
// config/database.php

function getDBConnection(): PDO {
    // Captura las variables de entorno de Render; si no existen, usa tus datos de Supabase por defecto
    $host = getenv('DB_HOST') ?: 'aws-0-sa-east-1.pooler.supabase.com';
    $port = getenv('DB_PORT') ?: '6543';
    $name = getenv('DB_NAME') ?: 'postgres';
    $user = getenv('DB_USER') ?: '**Ucv123456**/';
    
    // CAMBIA ESTO: Pon aquí la contraseña que definiste al crear el proyecto en Supabase
    $pass = getenv('DB_PASS') ?: '**Ucv123456**/'; 

    // Conexión obligatoria usando sslmode=require para entornos Cloud de Supabase
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Reportar errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retornar arreglos asociativos
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}