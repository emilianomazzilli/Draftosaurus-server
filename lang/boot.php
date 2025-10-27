<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Detectar idioma desde ?lang=es|en
if (isset($_GET['lang']) && in_array($_GET['lang'], ['es','en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'es';

// Variable para guardar las traducciones cargadas
$translations = [];

function t(string $key, string $fallback = ''): string {
    global $translations, $lang;
    
    // Separar "menu.title" en: $file = "menu" y $subkey = "title"
    $parts = explode('.', $key, 2);
    if (count($parts) !== 2) return $fallback ?: $key;
    
    $file = $parts[0];    // menu
    $subkey = $parts[1];  // title
    
    // Si no hemos cargado ese archivo todavía, lo cargamos
    if (!isset($translations[$file])) {
        $path = __DIR__ . "/$lang/$file.php";  // lang/es/menu.php
        $translations[$file] = file_exists($path) ? include $path : [];
    }
    
    // Devolver la traducción
    return $translations[$file][$subkey] ?? ($fallback ?: $key);
}