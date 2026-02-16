<?php
/**
 * Fonctions utilitaires - Domaine Prestige
 * Ce fichier centralise la logique de sécurité, du panier et du formatage.
 * Conforme aux normes Bachelor 2 (Validation, Sécurité, CRUD).
 */

// ==========================================
// 1. SÉCURITÉ & AUTHENTIFICATION
// ==========================================

/**
 * Échappe les données pour éviter les failles XSS
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Nettoie les données d'entrée (trim et retrait balises HTML)
 */
function clean($string) {
    return trim(strip_tags($string ?? ''));
}

/**
 * Vérifie si l'utilisateur est connecté via la session
 */
function isLogged() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Récupère l'utilisateur actuellement connecté
 */
function getCurrentUser() {
    return isLogged() ? $_SESSION['user'] : null;
}

/**
 * Vérifie si l'utilisateur possède le rôle 'admin'
 */
function isAdmin() {
    return isLogged() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Redirige vers la connexion si l'accès nécessite d'être authentifié
 */
function requireLogin() {
    if (!isLogged()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Déconnecte l'utilisateur et nettoie la session
 */
function logoutUser() {
    unset($_SESSION['user']);
    unset($_SESSION['user_id']);
    unset($_SESSION['panier']);
}

/**
 * Génère un jeton unique pour prévenir les failles CSRF
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide le jeton CSRF fourni lors d'une requête POST
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    return isset($_SESSION['csrf_token']) && $token && hash_equals($_SESSION['csrf_token'], $token);
}

// ==========================================
// 2. GESTION DU PANIER
// ==========================================

function initPanier() {
    if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
}

function addToPanier($id, $quantite = 1) {
    initPanier();
    $id = (int)$id;
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id] += (int)$quantite;
    } else {
        $_SESSION['panier'][$id] = (int)$quantite;
    }
}

function updatePanierQuantity($id, $quantite) {
    initPanier();
    $id = (int)$id;
    $quantite = (int)$quantite;
    if ($quantite <= 0) {
        removeFromPanier($id);
    } else {
        $_SESSION['panier'][$id] = $quantite;
    }
}

function removeFromPanier($id) {
    $id = (int)$id;
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
}

function clearPanier() {
    $_SESSION['panier'] = [];
}

function getPanierCount() {
    initPanier();
    return array_sum($_SESSION['panier']);
}

function getPanierItems($pdo) {
    initPanier();
    if (empty($_SESSION['panier'])) return [];
    
    $ids = array_keys($_SESSION['panier']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT i.*, s.quantite_stock as stock_reel 
                           FROM items i 
                           LEFT JOIN stock s ON i.id = s.id_item 
                           WHERE i.id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll();
    
    foreach ($items as &$item) {
        $item['quantite'] = $_SESSION['panier'][$item['id']];
        $item['sous_total'] = $item['prix'] * $item['quantite'];
    }
    return $items;
}

function getPanierTotal($pdo) {
    $items = getPanierItems($pdo);
    $total = 0;
    foreach ($items as $item) {
        $total += $item['sous_total'];
    }
    return $total;
}

// ==========================================
// 3. MESSAGES FLASH & FORMATAGE
// ==========================================

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function displayFlash() {
    $flash = getFlash();
    if (!$flash) return '';
    $class = ($flash['type'] === 'success') ? 'alert-success' : 'alert-danger';
    return '<div class="alert ' . $class . ' alert-dismissible fade show rounded-0" role="alert">
                ' . e($flash['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

function formatPrice($price) {
    return number_format((float)$price, 2, ',', ' ') . ' €';
}

/**
 * Correction : Ajout de formatDate pour corriger ton erreur Admin
 */
function formatDate($date) {
    if (!$date) return 'N/C';
    return date('d/m/Y', strtotime($date));
}

/**
 * Correction : Utilisation correcte de la variable $text
 */
function truncate($text, $length = 100, $suffix = '...') {
    $text = $text ?? '';
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

// ==========================================
// 4. PAGINATION 
// ==========================================

function paginate($total, $perPage = 12, $currentPage = 1) {
    $totalPages = (int)ceil($total / $perPage);
    $currentPage = max(1, min((int)$currentPage, $totalPages ?: 1));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total, 'per_page' => $perPage, 'current_page' => $currentPage,
        'total_pages' => $totalPages, 'offset' => $offset,
        'has_prev' => $currentPage > 1, 'has_next' => $currentPage < $totalPages
    ];
}

function displayPagination($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
    
    $html = '<nav><ul class="pagination justify-content-center">';
    if ($pagination['has_prev']) {
        $html .= '<li class="page-item"><a class="page-link bg-dark text-warning border-secondary" href="' . $baseUrl . $separator . 'page=' . ($pagination['current_page'] - 1) . '">Précédent</a></li>';
    }
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = $i === $pagination['current_page'] ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link ' . ($active ? 'bg-warning text-dark' : 'bg-dark text-white') . ' border-secondary" href="' . $baseUrl . $separator . 'page=' . $i . '">' . $i . '</a></li>';
    }
    if ($pagination['has_next']) {
        $html .= '<li class="page-item"><a class="page-link bg-dark text-warning border-secondary" href="' . $baseUrl . $separator . 'page=' . ($pagination['current_page'] + 1) . '">Suivant</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}

// ==========================================
// 5. UTILITAIRES SYSTÈME
// ==========================================

function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function post($key, $default = null) {
    if (!isset($_POST[$key])) return $default;
    if (strpos($key, 'password') !== false) {
        return trim($_POST[$key]);
    }
    return clean($_POST[$key]);
}

function get($key, $default = null) {
    return isset($_GET[$key]) ? clean($_GET[$key]) : $default;
}

function redirect($url) {
    header("Location: $url");
    exit;
}