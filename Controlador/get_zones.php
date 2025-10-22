<?php
// Controlador/get_zones.php
header('Content-Type: application/json; charset=utf-8');

// Ruta del archivo donde persistimos las zonas
$file = __DIR__ . '/../Datos/zones.json';

if (is_file($file)) {
  readfile($file);
  exit;
}

// Si no existe, devolvemos un arreglo vacío (el front usará DEFAULT_ZONES como fallback)
echo json_encode([]);
