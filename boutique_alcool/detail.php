<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// 1. Récupération et validation de l'ID
$id = (int)get('id', 0);
if ($id <= 0) { header('Location: articles.php'); exit; }

// 2. Requête détaillée avec jointure de stock
$sql = "SELECT i.*, COALESCE(s.quantite_stock, 0) as stock 
        FROM items i 
        LEFT JOIN stock s ON i.id = s.id_item 
        WHERE i.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$wine = $stmt->fetch();

if (!$wine) { header('Location: articles.php'); exit; }

$page_title = $wine['nom'];

// 3. Récupération des suggestions (même type de vin)
$sql_sim = "SELECT i.*, COALESCE(s.quantite_stock, 0) as stock 
            FROM items i 
            LEFT JOIN stock s ON i.id = s.id_item 
            WHERE i.id != :id AND i.type_vin = :type 
            ORDER BY RAND() LIMIT 4";
$stmt_sim = $pdo->prepare($sql_sim);
$stmt_sim->execute([':id' => $id, ':type' => $wine['type_vin']]);
$similar_wines = $stmt_sim->fetchAll();

require_once 'includes/header.php';
?>

<section style="background: #1a1a1a; padding: 2rem 0; border-bottom: 1px solid #333;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="index.php" style="color: #ccc; text-decoration: none;">Accueil</a></li>
                <li class="breadcrumb-item"><a href="articles.php" style="color: #ccc; text-decoration: none;">Nos Vins</a></li>
                <li class="breadcrumb-item active" style="color: #c9a961;" aria-current="page"><?= e($wine['nom']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="section-padding py-5" style="background-color: #050505;">
    <div class="container">
        <div class="row g-5">
            
            <div class="col-lg-5">
                <div class="p-5 border border-secondary position-relative" style="background: #fff; border-radius: 4px;">
                    <?php if(!empty($wine['nouveaute'])): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 fs-6">Nouveauté</span>
                    <?php endif; ?>
                    <img src="<?= e($wine['image']) ?>" alt="<?= e($wine['nom']) ?>" class="img-fluid d-block mx-auto" style="max-height: 500px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));">
                </div>
                
                <div class="mt-4 p-4" style="background: #121212; border: 1px solid #333;">
                    <h5 style="color: #c9a961; margin-bottom: 1rem;"><i class="fas fa-shield-alt me-2"></i>Garanties Domaine</h5>
                    <ul class="list-unstyled m-0" style="color: #eee;">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Produit au domaine</li>
                        <li class="mb-2"><i class="fas fa-truck text-success me-2"></i>Livraison sécurisée 48h</li>
                        <li class="mb-0"><i class="fas fa-lock text-success me-2"></i>Paiement 100% sécurisé</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="ps-lg-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="text-uppercase badge border border-warning text-warning" style="letter-spacing: 2px;">
                            <?= e($wine['type_vin']) ?>
                        </span>
                        <?php if($wine['stock'] > 0): ?>
                            <span class="text-success"><i class="fas fa-circle me-1 small"></i> En Stock (<?= $wine['stock'] ?>)</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Rupture de stock</span>
                        <?php endif; ?>
                    </div>

                    <h1 class="display-4 text-white font-serif mt-3 mb-2"><?= e($wine['nom']) ?></h1>
                    <p class="text-light fs-5 mb-4"><?= e($wine['appellation'] ?? 'AOC Bordeaux') ?> - <?= e($wine['millesime'] ?? '2020') ?></p>
                    
                    <div class="mb-4">
                        <?php if(!empty($wine['promotion'])): ?>
                            <span class="text-decoration-line-through text-muted fs-4 me-2"><?= formatPrice($wine['prix']) ?></span>
                            <span class="display-5 fw-bold" style="color: #e74c3c; font-family: 'Cormorant Garamond', serif;">
                                <?= formatPrice($wine['prix_promo']) ?>
                            </span>
                        <?php else: ?>
                            <span class="display-5 fw-bold" style="color: #c9a961; font-family: 'Cormorant Garamond', serif;">
                                <?= formatPrice($wine['prix']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-5 text-light" style="font-size: 1.1rem; line-height: 1.8;">
                        <p><?= nl2br(e($wine['description'])) ?></p>
                    </div>
                    
                    <div class="mb-5 p-4" style="background: #121212; border-left: 3px solid #c9a961;">
                        <h4 class="text-white font-serif mb-4">Fiche Technique</h4>
                        <div class="row g-4 text-light">
                            <?php if(!empty($wine['cepage'])): ?>
                            <div class="col-md-6">
                                <strong class="d-block text-uppercase text-muted small mb-1">Cépages</strong>
                                <span><?= e($wine['cepage']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($wine['degre_alcool'])): ?>
                            <div class="col-md-6">
                                <strong class="d-block text-uppercase text-muted small mb-1">Degré</strong>
                                <span><?= e($wine['degre_alcool']) ?>% vol.</span>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($wine['temperature_service'])): ?>
                            <div class="col-md-6">
                                <strong class="d-block text-uppercase text-muted small mb-1">Température</strong>
                                <span><?= e($wine['temperature_service']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($wine['garde'])): ?>
                            <div class="col-md-6">
                                <strong class="d-block text-uppercase text-muted small mb-1">Garde</strong>
                                <span><?= e($wine['garde']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($wine['stock'] > 0): ?>
                    <form action="panier.php" method="POST" class="d-flex gap-3 align-items-center p-4" style="background: #1a1a1a; border: 1px solid #333;">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?= $wine['id'] ?>">
                        
                        <div class="quantity-selector">
                            <label class="text-white small d-block mb-1">Quantité</label>
                            <input type="number" name="quantity" value="1" min="1" max="<?= $wine['stock'] ?>" class="form-control text-center bg-black text-white border-secondary" style="width: 80px;">
                        </div>
                        
                        <button type="submit" class="btn btn-gold btn-lg flex-grow-1" style="background: #c9a961; color: #000; border: none; font-weight: bold; border-radius: 0;">
                            <i class="fas fa-shopping-basket me-2"></i> AJOUTER AU PANIER
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-dark border-danger text-danger text-center rounded-0">
                            <i class="fas fa-info-circle me-2"></i> Ce cru est victime de son succès. Indisponible actuellement.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if(count($similar_wines) > 0): ?>
<section class="section-padding py-5" style="background: #121212;">
    <div class="container">
        <h3 class="text-white font-serif mb-4 border-bottom border-secondary pb-2">Vous aimerez aussi</h3>
        <div class="row g-4">
            <?php foreach($similar_wines as $sim): ?>
            <div class="col-md-3">
                <div class="card h-100 border-secondary" style="background: #1e1e1e;">
                    <a href="detail.php?id=<?= $sim['id'] ?>" class="d-block p-3 bg-white text-center" style="height: 220px;">
                        <img src="<?= e($sim['image']) ?>" alt="<?= e($sim['nom']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    </a>
                    <div class="card-body p-3 text-center">
                        <span class="badge bg-dark text-warning border border-warning mb-2"><?= e($sim['type_vin']) ?></span>
                        <h6 class="font-serif mb-2 text-truncate">
                            <a href="detail.php?id=<?= $sim['id'] ?>" class="text-white text-decoration-none"><?= e($sim['nom']) ?></a>
                        </h6>
                        <p class="text-warning fw-bold mb-0"><?= formatPrice($sim['prix']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>