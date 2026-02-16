<?php
/**
 * Header - Domaine Prestige
 * Gère la navigation, l'état de session et l'affichage des alertes flash.
 */

// 1. Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Déterminer le préfixe de chemin (utile si on est dans /admin/)
$root = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';

// 3. Récupération des données globales
$current_user = isLogged() ? getCurrentUser() : null;
$panier_count = getPanierCount();

// 4. Gestion de la page active
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Domaine Prestige - Cave à vin d'exception.">
    <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?>Domaine Prestige</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body class="bg-black text-white">

<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #000; border-bottom: 1px solid #c9a961;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= $root ?>index.php">
            <i class="fas fa-wine-glass-alt me-2" style="color: #c9a961;"></i>
            <span class="font-serif fw-bold" style="color: #fff; letter-spacing: 1px;">DOMAINE PRESTIGE</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-white <?= $current_page == 'index.php' ? 'fw-bold' : '' ?>" href="<?= $root ?>index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= $current_page == 'articles.php' ? 'fw-bold' : '' ?>" href="<?= $root ?>articles.php">Nos Vins</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= $current_page == 'qui-sommes-nous.php' ? 'fw-bold' : '' ?>" href="<?= $root ?>qui-sommes-nous.php">Le Domaine</a>
                </li>
                
                <li class="nav-item ms-lg-3 my-2 my-lg-0">
                    <a class="btn btn-outline-warning position-relative p-2" href="<?= $root ?>panier.php" style="border-radius: 0;">
                        <i class="fas fa-shopping-basket"></i>
                        <?php if($panier_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= (int)$panier_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if(isLogged()): ?>
                    <?php if(isAdmin()): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link fw-bold" href="<?= $root ?>admin/index.php" style="color: #c9a961;">ADMIN</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white" href="<?= $root ?>logout.php" title="Déconnexion">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white" href="<?= $root ?>login.php">
                            <i class="far fa-user me-1"></i> Connexion
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <?= displayFlash() ?>
</div>