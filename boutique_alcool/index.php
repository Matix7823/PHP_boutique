<?php
/**
 * Page d'accueil - Domaine Prestige
 * Ce fichier présente l'identité de la marque, une sélection de produits phares
 * et le concept du vignoble.
 */

// Importation de la configuration de la base de données et des outils PHP
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Accueil';

/**
 * RÉCUPÉRATION DES PRODUITS PHARES
 * On utilise une jointure (LEFT JOIN) pour lier les bouteilles à leur stock.
 * COALESCE assure de retourner 0 au lieu de NULL si aucune ligne de stock n'est trouvée.
 * On trie par 'nouveaute' pour mettre en avant les derniers crus arrivés.
 */
$sql = "SELECT i.*, COALESCE(s.quantite_stock, 0) as stock 
        FROM items i 
        LEFT JOIN stock s ON i.id = s.id_item 
        WHERE i.disponible = 1 
        ORDER BY i.nouveaute DESC, i.id DESC 
        LIMIT 4";

$stmt = $pdo->query($sql);
$featured_wines = $stmt->fetchAll();

// Inclusion de l'en-tête (navigation)
require_once 'includes/header.php';
?>

<header style="
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?q=80&w=2070');
    background-size: cover; background-position: center;
    min-height: 90vh; display: flex; align-items: center; justify-content: center; text-align: center;">
    
    <div class="container">
        <div class="d-inline-block p-5" style="background: rgba(0,0,0,0.7); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
            <p class="text-uppercase mb-2" style="color: #c9a961; font-weight: 600; letter-spacing: 3px;">Vignoble & Tradition</p>
            <h1 class="display-2 mb-4 text-white font-serif">DOMAINE PRESTIGE</h1>
            <p class="lead mb-5 mx-auto text-white" style="max-width: 600px; opacity: 0.9;">
                Cultiver l'excellence depuis 1892. <br>Des crus d'exception, de notre cave à votre table.
            </p>
            
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="articles.php" class="btn btn-lg px-4" style="background: #c9a961; color: #000; font-weight: bold; border-radius: 0;">
                    <i class="fas fa-wine-bottle me-2"></i>DÉCOUVRIR LA CAVE
                </a>
                <a href="qui-sommes-nous.php" class="btn btn-outline-light btn-lg px-4 rounded-0">
                    NOTRE HISTOIRE
                </a>
            </div>
        </div>
    </div>
</header>

<section class="py-5" style="background-color: #1a1a1a;">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="font-serif text-white display-5">Nos Cuvées d'Exception</h2>
            <p style="color: #c9a961; text-transform: uppercase; letter-spacing: 2px;">Sélection du Sommelier</p>
            <hr class="mx-auto mt-3" style="width: 60px; border-color: #c9a961; opacity: 1; border-width: 2px;">
        </div>

        <div class="row g-4">
            <?php foreach($featured_wines as $wine): ?>
            <div class="col-lg-3 col-md-6">
                <div class="wine-card h-100" style="background: #000; border: 1px solid #333; transition: 0.3s; display: flex; flex-direction: column;">
                    
                    <a href="detail.php?id=<?= $wine['id'] ?>" class="text-decoration-none">
                        <div class="position-relative bg-white" style="height: 320px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <?php if($wine['nouveaute']): ?>
                                 <span class="badge position-absolute top-0 end-0 m-3 fw-bold" style="background: #c9a961; color: #000; z-index: 2;">NOUVEAU</span>
                            <?php endif; ?>
                            <img src="<?= e($wine['image']) ?>" alt="<?= e($wine['nom']) ?>" style="max-height: 90%; max-width: 90%; transition: transform 0.5s;">
                        </div>
                    </a>
                    
                    <div class="p-4 text-center flex-grow-1 d-flex flex-column">
                        <small class="text-uppercase mb-2" style="color: #c9a961; letter-spacing: 1px; font-weight: bold;">
                            <?= e($wine['type_vin']) ?>
                        </small>
                        
                        <h3 class="h5 font-serif mb-2">
                            <a href="detail.php?id=<?= $wine['id'] ?>" class="text-white text-decoration-none hover-gold"><?= e($wine['nom']) ?></a>
                        </h3>
                        
                        <p class="small mb-3 text-muted">
                            <?= truncate(e($wine['description']), 60) ?>
                        </p>
                        
                        <div class="mt-auto">
                            <div class="fw-bold fs-4 mb-3 font-serif" style="color: #c9a961;">
                                <?= formatPrice($wine['prix']) ?>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <?php if($wine['stock'] > 0): ?>
                                <form action="panier.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= $wine['id'] ?>">
                                    <button class="btn btn-sm w-100 fw-bold rounded-0" style="background: #c9a961; color: #000; border: none;">
                                        <i class="fas fa-shopping-basket me-2"></i> AJOUTER
                                    </button>
                                </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm rounded-0" disabled>ÉPUISÉ</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="articles.php" class="btn btn-outline-light px-5 btn-lg rounded-0" style="border-color: #c9a961; color: #c9a961;">
                VOIR TOUTE LA CAVE <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<section id="about" class="py-5" style="background-color: #000; border-top: 1px solid #222;">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1560493676-04071c5f467b?w=800" class="img-fluid" alt="Vigneron" style="filter: grayscale(30%) brightness(0.8);">
                    <div class="position-absolute bottom-0 start-0 bg-black p-4 border-start border-warning border-4 m-3 shadow">
                        <h4 class="text-white m-0 font-serif">Louis Prestige</h4>
                        <span class="text-uppercase small" style="color: #c9a961;">Maître de Chai</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="font-serif mb-4 text-white display-6">L'Excellence du Terroir</h2>
                <p class="lead mb-4" style="color: #c9a961; font-style: italic;">
                    "Un grand vin commence toujours par un grand raisin."
                </p>
                <p class="mb-4 text-white-50" style="line-height: 1.8;">
                    Producteurs récoltants depuis 4 générations, nous cultivons nos vignes dans le respect 
                    de la tradition. Nos chais souterrains offrent les conditions idéales pour l'élevage 
                    de nos grands crus. Chaque bouteille est le reflet de notre passion pour la terre.
                </p>
                <a href="qui-sommes-nous.php" class="btn btn-outline-light rounded-0 mt-3">
                    NOTRE HISTOIRE
                </a>
            </div>
        </div>
    </div>
</section>

<?php 
// Inclusion du pied de page
require_once 'includes/footer.php'; 
?>