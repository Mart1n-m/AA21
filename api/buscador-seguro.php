<?php
// ✅ CÓDIGO SEGURO: Consulta preparada con PDO (parámetro enlazado)
header('Content-Type: application/json; charset=utf-8');
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["estado" => "error", "error" => "Método no permitido."]);
    exit;
}

$q = trim($_GET['q'] ?? '');

// Escapamos los comodines propios de LIKE (% y _) para que el usuario los busque como texto literal.
$qLiteral = addcslashes($q, '\\%_');
$patron   = '%' . $qLiteral . '%';

try {
    // El SQL y los datos viajan por separado: el motor nunca interpreta $q como código.
    // Con ATTR_EMULATE_PREPARES = false no se puede repetir un mismo marcador nombrado, por eso :q1 y :q2.
    $sql = "SELECT id, nombre, categoria, precio, stock
            FROM productos
            WHERE nombre LIKE :q1 OR categoria LIKE :q2
            ORDER BY nombre ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':q1', $patron, PDO::PARAM_STR);
    $stmt->bindValue(':q2', $patron, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
        "estado" => "exito",
        "total"  => count($resultado),
        "data"   => $resultado
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log('Error en buscador-seguro: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(["estado" => "error", "error" => "Error interno al consultar productos."]);
}
