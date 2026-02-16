<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Nos Vins';

// 1. RÉCUPÉRATION ET NETTOYAGE DES FILTRES
$filter_type  = get('type', '');
$filter_price = get('price', '');
$search       = get('search', '');
$page         = max(1, (int)get('page', 1));
$per_page     = 12;

// 2. CONSTRUCTION DE LA REQUÊTE DE BASE
$sql_base = " FROM items i LEFT JOIN stock s ON i.id = s.id_item WHERE i.disponible = 1";
$params = [];

// Filtre par Type de vin
if ($filter_type) {
    $sql_base .= " AND i.type_vin = :type";
    $params[':type'] = $filter_type;
}

// Filtre par Tranche de Prix
if ($filter_price) {
    switch ($filter_price) {
        case 'low':  $sql_base .= " AND i.prix < 30"; break;
        case 'mid':  $sql_base .= " AND i.prix BETWEEN 30 AND 50"; break;
        case 'high': $sql_base .= " AND i.prix > 50"; break;
    }
}

// RECHERCHE (Correction de l'erreur HY093 : paramètres uniques)
if ($search) {
    $sql_base .= " AND (i.nom LIKE :s1 OR i.description LIKE :s2 OR i.appellation LIKE :s3)";
    $params[':s1'] = "%$search%";
    $params[':s2'] = "%$search%";
    $params[':s3'] = "%$search%";
}

// 3. COMPTER LE TOTAL POUR LA PAGINATION
$count_stmt = $pdo->prepare("SELECT COUNT(*) " . $sql_base);
$count_stmt->execute($params);
$total_items = $count_stmt->fetchColumn();

// Calcul des données de pagination via functions.php
$pagination = paginate($total_items, $per_page, $page);

// 4. REQUÊTE FINALE AVEC TRI ET LIMITES
$sql = "SELECT i.*, COALESCE(s.quantite_stock, 0) as stock " . $sql_base . " 
        ORDER BY i.nouveaute DESC, i.id DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', (int)$pagination['per_page'], PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$pagination['offset'], PDO::PARAM_INT);

$stmt->execute();
$wines = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('https://images.unsplash.com/photo-1559563362-c667ba5f5480?w=1920') center/cover; padding: 100px 0; text-align: center; border-bottom: 2px solid #c9a961;">
    <div class="container">
        <h1 class="display-3 text-white font-serif mb-2" style="letter-spacing: 3px;">LA CAVE</h1>
        <p class="lead text-white-50 text-uppercase" style="letter-spacing: 5px;">Sélection Prestige</p>
    </div>
</section>

<section style="background: #111; padding: 2rem 0; border-bottom: 1px solid #333;">
    <div class="container">
        <form action="articles.php" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-warning"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Rechercher un vin..." value="<?= e($search) ?>">
                </div>
            </div>
            
            <div class="col-md-3">
                <select name="type" class="form-select bg-dark border-secondary text-white" onchange="this.form.submit()">
                    <option value="">Toutes les couleurs</option>
                    <option value="Rouge" <?= $filter_type == 'Rouge' ? 'selected' : '' ?>>Rouge</option>
                    <option value="Blanc" <?= $filter_type == 'Blanc' ? 'selected' : '' ?>>Blanc</option>
                    <option value="Rosé" <?= $filter_type == 'Rosé' ? 'selected' : '' ?>>Rosé</option>
                    <option value="Champagne" <?= $filter_type == 'Champagne' ? 'selected' : '' ?>>Champagne</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="price" class="form-select bg-dark border-secondary text-white" onchange="this.form.submit()">
                    <option value="">Tous les prix</option>
                    <option value="low" <?= $filter_price == 'low' ? 'selected' : '' ?>>Entrée de gamme (< 30€)</option>
                    <option value="mid" <?= $filter_price == 'mid' ? 'selected' : '' ?>>Milieu de gamme (30€-50€)</option>
                    <option value="high" <?= $filter_price == 'high' ? 'selected' : '' ?>>Prestige (> 50€)</option>
                </select>
            </div>
            
            <div class="col-md-2 text-end text-white">
                <span class="badge bg-warning text-dark p-2"><?= $total_items ?> références</span>
            </div>
        </form>
    </div>
</section>

<section class="py-5" style="background-color: #000; min-height: 60vh;">
    <div class="container">
        <?php if (count($wines) > 0): ?>
            <div class="row g-4">
                <?php foreach($wines as $wine): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 bg-dark border-secondary wine-card">
                        <div class="position-relative bg-white p-4" style="height: 280px; display: flex; align-items: center; justify-content: center;">
                            <?php if($wine['nouveaute']): ?>
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 fw-bold" style="z-index: 10;">NOUVEAU</span>
                            <?php endif; ?>
                            <a href="detail.php?id=<?= $wine['id'] ?>">
                                <img src="<?= e($wine['image']) ?>" alt="<?= e($wine['nom']) ?>" style="max-height: 230px; max-width: 100%;" class="wine-img">
                            </a>
                        </div>
                        
                        <div class="card-body d-flex flex-column text-center">
                            <small class="text-warning text-uppercase mb-2" style="letter-spacing: 2px; font-size: 0.7rem;"><?= e($wine['type_vin']) ?></small>
                            <h5 class="font-serif text-white mb-3">
                                <a href="detail.php?id=<?= $wine['id'] ?>" class="text-decoration-none text-white"><?= e($wine['nom']) ?></a>
                            </h5>
                            
                            <div class="mt-auto">
                                <p class="h4 mb-3" style="color: #c9a961; font-family: 'Cormorant Garamond', serif;">
                                    <?= formatPrice($wine['prix']) ?>
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="detail.php?id=<?= $wine['id'] ?>" class="btn btn-outline-light btn-sm rounded-0">Détails</a>
                                    
                                    <?php if($wine['stock'] > 0): ?>
                                        <form action="panier.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="id" value="<?= $wine['id'] ?>">
                                            <button type="submit" class="btn btn-gold btn-sm w-100 rounded-0" style="background: #c9a961; color: #000; border: none; font-weight: bold;">
                                                <i class="fas fa-shopping-cart me-1"></i> AJOUTER
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100 rounded-0" disabled>RUPTURE</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($pagination['total_pages'] > 1): ?>
            <div class="mt-5 d-flex justify-content-center">
                <?php 
                    $params_get = $_GET;
                    unset($params_get['page']);
                    $query_string = http_build_query($params_get);
                    // On nettoie la base URL pour displayPagination
                    $clean_url = 'articles.php' . ($query_string ? '?' . $query_string : '');
                    echo displayPagination($pagination, $clean_url); 
                ?>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-4"></i>
                <h2 class="text-white font-serif">Aucun vin trouvé</h2>
                <a href="articles.php" class="btn btn-warning mt-3 rounded-0">Réinitialiser tout</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>