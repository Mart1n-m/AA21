<?php
// Configuración de credenciales del servidor MySQL
// XAMPP por defecto: usuario root SIN contraseña. Ajustar si tu MySQL tiene otra.
$host    = 'localhost';
$db      = 'clase_backend';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de configuración del Driver PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza PDOException ante errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Fuerza Prepared Statements reales
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    // No exponemos $e->getMessage() al cliente: filtraría datos internos.
    error_log('Error de conexión PDO: ' . $e->getMessage());
    echo json_encode(["estado" => "error", "error" => "Error de conexión con la base de datos."]);
    exit;
}
