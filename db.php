<?php
// 1. Database details from your Neon Console
$host = 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_O69odUBLsJvg'; 
$endpoint = 'ep-shy-heart-alnkzh60'; // Your specific Endpoint ID

try {
    // 2. THE CRITICAL FIX: 
    // We must send the endpoint ID as part of the password string 
    // for the Pooler to authenticate correctly.
    $final_pass = "endpoint=$endpoint;$pass";

    // 3. Set the DSN with SSL required (Neon requirement)
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";

    $pdo = new PDO($dsn, $user, $final_pass);
    
    // Set error mode so we see exactly what goes wrong later
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // This will now show a much clearer error if it still fails
    die("The system is having trouble reaching the database. <br> Error: " . $e->getMessage());
}
?>