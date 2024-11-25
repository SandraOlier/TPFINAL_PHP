<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "movies_cac24145";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión no válida: .\n" . $e->getMessage();
}



?>