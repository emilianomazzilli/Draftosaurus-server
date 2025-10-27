<?php
// Controlador/save_zones.php
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
if ($raw === false || $raw === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Sin cuerpo']);
  exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
  exit;
}

// Validación mínima de campos esperados
foreach ($data as $z) {
  if (!isset($z['id'], $z['name'], $z['left'], $z['top'], $z['width'], $z['height'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Estructura de zona inválida']);
    exit;
  }
}

$path = __DIR__ . '/../Datos';
if (!is_dir($path)) {
  @mkdir($path, 0775, true);
}

$file = $path . '/zones.json';
if (@file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'No se pudo escribir el archivo']);
  exit;
}

echo json_encode(['ok' => true]);