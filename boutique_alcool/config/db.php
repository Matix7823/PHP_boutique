<?php
/**
 * Configuration de la base de données
 */

// 1. PARAMÈTRES DE CONNEXION
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'alcool_shop'); 
define('DB_USER', 'root');
define('DB_PASS', ''); 

// 2. MODE DEBUG
define('DEBUG', true);

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 3. GESTION HTTPS
if ($_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1') {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// 4. CONNEXION PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    if (DEBUG) {
        die("❌ Erreur de connexion BDD : " . $e->getMessage());
    } else {
        die("❌ Erreur de connexion au serveur.");
    }
}

// 5. DÉMARRAGE DE SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 6. TIMEZONE
date_default_timezone_set('Europe/Paris');
?>