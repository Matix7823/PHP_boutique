<?php
/**
 * Fonctions utilitaires - Domaine Prestige
 * Ce fichier centralise la logique de sécurité, la gestion du panier, 
 * le formatage des données et les outils système du site.
 */

// ==========================================
// 1. SÉCURITÉ & AUTHENTIFICATION
// ==========================================

/**
 * Protection XSS : Échappe les caractères spéciaux pour empêcher l'injection de scripts malveillants.
 * Utilisée lors de chaque affichage (echo) de donnée provenant de la base ou de l'utilisateur.
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Nettoyage : Supprime les balises HTML et les espaces inutiles.
 */
function clean($string) {
    return trim(strip_tags($string ?? ''));
}

/**
 * Vérifie si une session utilisateur est active.
 */
function isLogged() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Récupère les informations de l'utilisateur en session.
 */
function getCurrentUser() {
    return isLogged() ? $_SESSION['user'] : null;
}

/**
 * Sécurité RBAC (Role-Based Access Control) : Vérifie si le compte possède les privilèges 'admin'.
 */
function isAdmin() {
    return isLogged() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Middleware de connexion : Force la redirection vers login.php si l'utilisateur n'est pas authentifié.
 */
function requireLogin() {
    if (!isLogged()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Déconnexion : Détruit les variables de session liées à l'utilisateur et au panier.
 */
function logoutUser() {
    unset($_SESSION['user']);
    unset($_SESSION['user_id']);
    unset($_SESSION['panier']);
}

/**
 * Protection CSRF : Génère un jeton (token) unique stocké en session.
 * Ce jeton doit être envoyé dans chaque formulaire POST pour valider l'origine de la requête.
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validation CSRF : Compare le jeton reçu par le formulaire avec celui stocké en session.
 * Utilise hash_equals pour prévenir les attaques par analyse temporelle.
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    return isset($_SESSION['csrf_token']) && $token && hash_equals($_SESSION['csrf_token'], $token);
}

// ==========================================
// 2. GESTION DU PANIER
// ==========================================

/**
 * Initialise la structure du panier en session si elle n'existe pas.
 */
function initPanier() {
    if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
}

/**
 * Ajoute un article au panier ou incrémente sa quantité s'il est déjà présent.
 */
function addToPanier($id, $quantite = 1) {
    initPanier();
    $id = (int)$id;
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id] += (int)$quantite;
    } else {
        $_SESSION['panier'][$id] = (int)$quantite;
    }
}

/**
 * Modifie manuellement la quantité d'un article ou le supprime si la quantité tombe à 0.
 */
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

/**
 * Retire un article spécifique du panier.
 */
function removeFromPanier($id) {
    $id = (int)$id;
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
}

/**
 * Vide intégralement le panier.
 */
function clearPanier() {
    $_SESSION['panier'] = [];
}

/**
 * Compte le nombre total d'articles (somme des quantités) dans le panier.
 */
function getPanierCount() {
    initPanier();
    return array_sum($_SESSION['panier']);
}

/**
 * Récupère les données complètes des articles du panier depuis la base de données.
 * Utilise une jointure pour obtenir le stock réel et calcule les sous-totaux.
 */
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

/**
 * Calcule le montant total TTC du panier.
 */
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

/**
 * Système de notifications "Flash" : Enregistre un message temporaire en session.
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Récupère et détruit le message flash de la session (affichage unique).
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Génère le code HTML (Bootstrap) pour afficher le message flash.
 */
function displayFlash() {
    $flash = getFlash();
    if (!$flash) return '';
    $class = ($flash['type'] === 'success') ? 'alert-success' : 'alert-danger';
    return '<div class="alert ' . $class . ' alert-dismissible fade show rounded-0" role="alert">
                ' . e($flash['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Formate un nombre au format monétaire européen (ex: 1 250,50 €).
 */
function formatPrice($price) {
    return number_format((float)$price, 2, ',', ' ') . ' €';
}

/**
 * Formate une date SQL (Y-m-d) au format français (d/m/Y).
 */
function formatDate($date) {
    if (!$date) return 'N/C';
    return date('d/m/Y', strtotime($date));
}

/**
 * Tronque une chaîne de caractères pour les aperçus.
 */
function truncate($text, $length = 100, $suffix = '...') {
    $text = $text ?? '';
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

// ==========================================
// 4. PAGINATION 
// ==========================================

/**
 * Calcule les paramètres de pagination (offset, nombre de pages).
 */
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

/**
 * Génère le code HTML de la barre de pagination.
 */
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

/**
 * Vérifie si la requête actuelle est de type POST.
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Récupère et nettoie une donnée envoyée via POST.
 * Cas particulier : Les mots de passe ne sont pas "nettoyés" pour ne pas corrompre les caractères spéciaux.
 */
function post($key, $default = null) {
    if (!isset($_POST[$key])) return $default;
    if (strpos($key, 'password') !== false) {
        return trim($_POST[$key]);
    }
    return clean($_POST[$key]);
}

/**
 * Récupère et nettoie une donnée envoyée via l'URL (GET).
 */
function get($key, $default = null) {
    return isset($_GET[$key]) ? clean($_GET[$key]) : $default;
}

/**
 * Redirection HTTP rapide.
 */
function redirect($url) {
    header("Location: $url");
    exit;
}