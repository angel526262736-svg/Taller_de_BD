<!-- config.php: Archivo de configuración para la conexión a la BD -->
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Cambia por tu usuario de BD
define('DB_PASS', ''); // Cambia por tu contraseña de BD
define('DB_NAME', 'universidad_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
