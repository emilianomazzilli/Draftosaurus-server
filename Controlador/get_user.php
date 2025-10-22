<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
header('Content-Type: application/json');

$foto = $_SESSION['foto'] ?? null;
if ($foto) {
  if (strpos($foto, '../') !== 0 && strpos($foto, '/') !== 0) {
    $foto = '../' . $foto;
  }
}

$email = $_SESSION['email'] ?? null;

if (isset($_SESSION['usuario'])) {
  echo json_encode([
    'id'     => $_SESSION['user_id'] ?? null,
    'nombre' => $_SESSION['usuario'],
    'foto'   => $foto,
    'email'  => $email
  ]);
} else {
  echo json_encode(['nombre' => 'Invitado']);
}
