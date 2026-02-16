<?php
/**
 * Dashboard Administratif - Domaine Prestige
 * Point d'entrée du Back-office pour la gestion CRUD.
 */
require_once '../config/db.php';
require_once '../includes/functions.php';

// Sécurité : Authentification réservée aux administrateurs
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Statistiques pour le bonus Qualité du code
$count_items = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$count_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$page_title = "Administration";
require_once '../includes/header.php'; 
?>

<style>
    :root { --gold-prestige: #c9a961; --dark-deep: #050505; }
    body { background-color: var(--dark-deep); color: #ffffff; font-family: 'Lato', sans-serif; }
    
    .admin-header { 
        background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=1500&q=80'); 
        background-size: cover; 
        background-position: center; 
        padding: 100px 0 60px; 
        border-bottom: 1px solid #333; 
    }
    
    .stat-pill { 
        background: rgba(201, 169, 97, 0.2); 
        border: 1px solid var(--gold-prestige); 
        color: var(--gold-prestige); 
        padding: 5px 20px; 
        border-radius: 50px; 
        font-size: 0.8rem; 
        text-transform: uppercase; 
        font-weight: 700;
    }

    .menu-card { 
        background: #0f0f0f; 
        border: 1px solid #333; 
        padding: 40px; 
        transition: 0.4s; 
        height: 100%; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .menu-card:hover { border-color: var(--gold-prestige); transform: translateY(-5px); }

    .icon-box { 
        width: 80px; height: 80px; 
        background: #1a1a1a; 
        display: flex; align-items: center; justify-content: center; 
        margin: 0 auto 25px; 
        border-radius: 50%; 
        border: 1px solid #444; 
        color: var(--gold-prestige);
    }
    
    .card-description { color: #ffffff !important; line-height: 1.6; min-height: 75px; }
    .stat-label { color: var(--gold-prestige) !important; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; }
    
    .btn-admin { 
        border: 2px solid var(--gold-prestige); 
        color: var(--gold-prestige); 
        font-weight: 700; 
        text-transform: uppercase; 
        padding: 12px 30px; 
        background: transparent; 
        transition: 0.3s; 
        text-decoration: none;
    }
    .btn-admin:hover { background: var(--gold-prestige); color: #000; }
    
    .exit-link { color: #ffffff !important; font-weight: 600; text-decoration: none; border-bottom: 1px solid #c9a961; padding-bottom: 2px; }
</style>

<header class="admin-header text-center">
    <div class="container">
        <div class="stat-pill mb-3">Accès Administrateur - Domaine Prestige</div>
        <h1 class="display-3 font-serif mb-3" style="color: var(--gold-prestige);">Tableau de Bord</h1>
        <p class="lead text-white mx-auto" style="max-width: 600px;">Gérez votre catalogue et vos membres avec une visibilité totale.</p>
    </div>
</header>

<div class="container" style="margin-top: -40px;">
    <div class="row g-4 justify-content-center">
        
        <div class="col-lg-5">
            <div class="menu-card text-center shadow-lg">
                <div>
                    <div class="icon-box">
                        <i class="fas fa-wine-bottle fa-2x"></i>
                    </div>
                    <h3 class="font-serif text-white mb-3">La Cave</h3>
                    <p class="card-description mb-4">
                        Contrôlez l'ensemble de votre catalogue. Ajoutez de nouveaux crus, 
                        modifiez les fiches ou gérez vos stocks.
                    </p>
                </div>
                
                <div>
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <div class="px-4 border-end border-secondary">
                            <span class="d-block h3 mb-0 text-white fw-bold"><?= (int)$count_items ?></span>
                            <span class="stat-label">Vins Inscrits</span>
                        </div>
                        <div class="px-4">
                            <span class="d-block h3 mb-0 text-white fw-bold">Actif</span>
                            <span class="stat-label">État Stock</span>
                        </div>
                    </div>
                    <a href="items.php" class="btn btn-admin w-100">Gérer le Catalogue</a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="menu-card text-center shadow-lg">
                <div>
                    <div class="icon-box">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3 class="font-serif text-white mb-3">Les Membres</h3>
                    <p class="card-description mb-4">
                        Supervisez votre communauté d'amateurs. Visualisez les profils 
                        et gérez la sécurité des comptes.
                    </p>
                </div>
                
                <div>
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <div class="px-4">
                            <span class="d-block h3 mb-0 text-white fw-bold"><?= (int)$count_users ?></span>
                            <span class="stat-label">Clients Enregistrés</span>
                        </div>
                    </div>
                    <a href="users.php" class="btn btn-admin w-100">Gérer les Membres</a>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center mt-5 pt-5 pb-5">
        <a href="../index.php" class="exit-link">
            <i class="fas fa-arrow-left me-2"></i>Retourner sur le site public
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>