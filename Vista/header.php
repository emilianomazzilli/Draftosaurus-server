<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="<?= isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es' ?>" 
      data-theme="<?= isset($_SESSION['darkMode']) && $_SESSION['darkMode'] ? 'dark' : 'light' ?>">