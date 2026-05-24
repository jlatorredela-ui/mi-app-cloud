<?php
// config-database.php

function getDBConnection(): PDO {
    // CAMBIO CLAVE: Usamos la dirección directa (db.ID.supabase.co) 
    // en lugar del pooler (aws...pooler.supabase.com)
    $host = 'db.qrfaqadirfmzvxbaijqp.supabase.co'; 
    $port = '5432'; 
    $db   = 'postgres';
    $user = 'postgres'; 
    $pass = 'TU_CONTRASEÑA_REAL'; // ASEGÚRATE DE PONERLA BIEN AQUÍ

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}