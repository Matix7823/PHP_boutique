<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Assurer que le panier est initialisé dès le début
initPanier();

$page_title = 'Mon Panier';

// --- LOGIQUE PHP : TRAITEMENT DES ACTIONS ---
if (isPost()) {
    $csrf_token = post('csrf_token');
    
    // Vérification stricte du Token CSRF
    if (!verifyCsrfToken($csrf_token)) {
        setFlash('danger', 'Token de sécurité invalide. Veuillez réessayer.');
        header('Location: panier.php'); 
        exit;
    }

    $action = post('action');
    $id = (int)post('id', 0);

    switch ($action) {
        case 'add':
            $quantity = max(1, (int)post('quantity', 1));
            addToPanier($id, $quantity);
            setFlash('success', 'Article ajouté avec succès à votre cave.');
            break;
            
        case 'update':
            $quantity = max(0, (int)post('quantity', 1));
            updatePanierQuantity($id, $quantity);
            setFlash('success', 'Quantité mise à jour.');
            break;
            
        case 'remove':
            removeFromPanier($id);
            setFlash('success', 'Article retiré du panier.');
            break;
            
        case 'clear':
            clearPanier();
            setFlash('success', 'Votre panier a été entièrement vidé.');
            break;
    }
    
    // Redirection propre après traitement pour éviter le renvoi de formulaire au rafraîchissement
    header('Location: panier.php');
    exit;
}

// Récupération des données pour l'affichage
$panier_items = getPanierItems($pdo);
$total = getPanierTotal($pdo);

// Calcul des frais (Exemple : Offerts dès 150€)
$frais_livraison = ($total > 150 || $total == 0) ? 0 : 15.00;
$total_final = $total + $frais_livraison;

require_once 'includes/header.php';
?>

<section style="background: #0a0a0a; padding: 3rem 0 1.5rem; border-bottom: 1px solid #222;">
    <div class="container text-center">
        <h1 class="font-serif text-white display-4 mb-4">Mon Panier</h1>
        <div class="d-flex justify-content-center align-items-center mb-4" style="max-width: 600px; margin: 0 auto;">
            <div class="text-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 45px; height: 45px; background-color: #c9a961; color: #000; font-weight: bold;">1</div>
                <small class="d-block mt-2 text-warning text-uppercase fw-bold" style="font-size: 0.7rem;">Panier</small>
            </div>
            <div class="flex-grow-1 mx-3" style="height: 2px; background: #333;"></div>
            <div class="text-center opacity-50">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 45px; height: 45px; border: 2px solid #555; color: #555; font-weight: bold;">2</div>
                <small class="d-block mt-2 text-white text-uppercase" style="font-size: 0.7rem;">Livraison</small>
            </div>
            <div class="flex-grow-1 mx-3" style="height: 2px; background: #333;"></div>
            <div class="text-center opacity-50">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 45px; height: 45px; border: 2px solid #555; color: #555; font-weight: bold;">3</div>
                <small class="d-block mt-2 text-white text-uppercase" style="font-size: 0.7rem;">Paiement</small>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background-color: #000; min-height: 60vh; padding: 60px 0;">
    <div class="container">
        <?php if (empty($panier_items)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-4x mb-4 opacity-25" style="color: #fff;"></i>
                <h3 class="text-white mb-3 font-serif">Votre panier est actuellement vide</h3>
                <p class="text-muted mb-4">Parcourez notre sélection de crus d'exception pour remplir votre cave.</p>
                <a href="articles.php" class="btn btn-gold btn-lg px-5" style="background: #c9a961; color: #000; border-radius: 0;">Découvrir nos vins</a>
            </div>
        <?php else: ?>
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="bg-dark p-4 border border-secondary">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                            <h4 class="text-white m-0 font-serif">Sélection (<?= getPanierCount() ?> bouteilles)</h4>
                            <form action="panier.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment vider votre panier ?');">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none small">
                                    <i class="fas fa-trash-alt me-1"></i> Tout vider
                                </button>
                            </form>
                        </div>

                        <?php foreach ($panier_items as $item): ?>
                        <div class="row align-items-center mb-4 pb-4 border-bottom border-secondary" style="border-color: #333 !important;">
                            <div class="col-3 col-md-2">
                                <div class="bg-white p-2 d-flex align-items-center justify-content-center" style="height: 110px; border-radius: 2px;">
                                    <img src="<?= e($item['image']) ?>" alt="<?= e($item['nom']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                </div>
                            </div>
                            
                            <div class="col-9 col-md-4">
                                <span class="text-warning text-uppercase small fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                                    <?= e($item['type_vin']) ?>
                                </span>
                                <h5 class="mt-1 mb-1">
                                    <a href="detail.php?id=<?= $item['id'] ?>" class="text-white text-decoration-none font-serif"><?= e($item['nom']) ?></a>
                                </h5>
                                <span class="text-white opacity-50 small"><?= formatPrice($item['prix']) ?> / unité</span>
                            </div>

                            <div class="col-6 col-md-3 mt-3 mt-md-0">
                                <form action="panier.php" method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <div class="input-group input-group-sm" style="width: 100px;">
                                        <input type="number" name="quantity" class="form-control bg-black text-white text-center border-secondary fw-bold" 
                                               value="<?= $item['quantite'] ?>" min="1" max="<?= $item['stock_reel'] ?>" onchange="this.form.submit()">
                                    </div>
                                </form>
                            </div>

                            <div class="col-5 col-md-2 mt-3 mt-md-0 text-end">
                                <span class="fw-bold fs-5" style="color: #c9a961; font-family: 'Cormorant Garamond', serif;">
                                    <?= formatPrice($item['sous_total']) ?>
                                </span>
                            </div>

                            <div class="col-1 col-md-1 mt-3 mt-md-0 text-end">
                                <form action="panier.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-link text-white opacity-50 p-0" title="Supprimer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="p-4 border border-secondary bg-dark shadow-lg" style="border-top: 3px solid #c9a961 !important;">
                        <h4 class="font-serif text-white mb-4 border-bottom border-secondary pb-3">Récapitulatif</h4>
                        <div class="d-flex justify-content-between mb-3 text-white opacity-75">
                            <span>Sous-total</span>
                            <span><?= formatPrice($total) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 text-white opacity-75">
                            <span>Livraison</span>
                            <span><?= $frais_livraison == 0 ? '<span class="text-success fw-bold">Offerte</span>' : formatPrice($frais_livraison) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top border-secondary pt-4 mt-2 mb-4">
                            <span class="text-white fs-5 text-uppercase" style="letter-spacing: 1px;">Total TTC</span>
                            <span class="fs-2 fw-bold" style="color: #c9a961; font-family: 'Cormorant Garamond', serif;">
                                <?= formatPrice($total_final) ?>
                            </span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="checkout.php" class="btn btn-gold py-3 text-uppercase fw-bold" style="background: #c9a961; color:#000; border:none; border-radius:0;">
                                Commander <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <a href="articles.php" class="btn btn-outline-light py-2 text-uppercase small" style="border-radius:0;">
                                Continuer mes achats
                            </a>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="small text-muted mb-0"><i class="fas fa-lock me-2"></i> Paiement 100% sécurisé</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>