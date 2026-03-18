<?php
// 1. Your Neon connection details
$host = 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_O69odUBLsJvg';
$endpoint = 'ep-shy-heart-alnkzh60';

try {
    // 2. THE OFFICIAL NEON FIX: 
    // We add the endpoint ID directly into the DSN string using the 'options' parameter.
    // This forces the server to route you correctly BEFORE it checks the password.
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;options=endpoint%3D$endpoint;sslmode=require";

    // 3. Connect using just the normal password
    $pdo = new PDO($dsn, $user, $pass);
    
    // Set error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("The system is having trouble reaching the database. <br> Error: " . $e->getMessage());
}
?>