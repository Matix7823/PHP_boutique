<?php
/**
 * Gestion du catalogue (Back-office) - Domaine Prestige
 * Permet l'affichage, l'ajout et la suppression des produits (CRUD complet).
 */
require_once '../config/db.php';
require_once '../includes/functions.php';

// Vérification stricte des droits d'accès admin
if (!isAdmin()) { 
    redirect('../login.php'); 
}

$success = "";
$error = "";

/**
 * LOGIQUE D'AJOUT
 */
if (isPost() && isset($_POST['add'])) {
    // Vérification du token CSRF pour la sécurité
    if (!verifyCsrfToken(post('csrf_token'))) {
        $error = "Erreur de sécurité : Jeton invalide.";
    } else {
        try {
            $pdo->beginTransaction();

            $sql_item = "INSERT INTO items (
                nom, description, prix, image, type_vin, appellation, 
                millesime, cepage, degre_alcool, temperature_service, 
                garde, elevage, disponible
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $pdo->prepare($sql_item);
            $stmt->execute([
                post('nom'), post('description'), post('prix'), post('image'), 
                post('type_vin'), post('appellation'), post('millesime'),
                post('cepage'), post('degre_alcool'), post('temperature_service'),
                post('garde'), post('elevage')
            ]);

            $last_id = $pdo->lastInsertId();

            // Gestion de la table stock obligatoire
            $sql_stock = "INSERT INTO stock (id_item, quantite_stock) VALUES (?, ?)";
            $stmt_stock = $pdo->prepare($sql_stock);
            $stmt_stock->execute([$last_id, (int)post('quantite', 0)]);

            $pdo->commit();
            setFlash('success', "Le nouveau cru a été ajouté au catalogue.");
            redirect('items.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erreur système : " . $e->getMessage();
        }
    }
}

/**
 * LOGIQUE DE SUPPRESSION
 */
if (isPost() && isset($_POST['delete'])) {
    if (!verifyCsrfToken(post('csrf_token'))) {
        setFlash('error', "Erreur de sécurité : Jeton invalide.");
    } else {
        $id = (int)post('id');
        $pdo->prepare("DELETE FROM items WHERE id = ?")->execute([$id]);
        setFlash('success', "La référence a été retirée du catalogue.");
    }
    redirect('items.php');
}

// Récupération des articles avec détails et stock (Read)
$items = $pdo->query("SELECT i.*, s.quantite_stock FROM items i LEFT JOIN stock s ON i.id = s.id_item ORDER BY i.id DESC")->fetchAll();

require_once '../includes/header.php'; 
?>

<style>
    :root { --gold-smooth: #c9a961; --dark-luxury: #0a0a0a; }
    body { background-color: #050505; color: #ffffff; }
    .luxury-card { background: #0f0f0f; border: 1px solid #333; border-radius: 0; }
    
    /* Labels et Titres Or brillant */
    label, .accordion-button { 
        color: var(--gold-smooth) !important; 
        font-weight: 700 !important; 
        text-transform: uppercase; 
        font-size: 0.85rem;
    }
    
    /* Inputs Blanc sur Noir (Pas de texte gris) */
    .form-control, .form-select { 
        background: #000 !important; 
        border: 1px solid #444 !important; 
        color: #ffffff !important; 
        border-radius: 0; 
        padding: 0.75rem; 
    }
    .form-control:focus { border-color: var(--gold-smooth) !important; box-shadow: none; }

    /* Tableau Haute Visibilité */
    .table { color: #ffffff !important; border-color: #333; }
    .table thead { background: #1a1a1a; color: var(--gold-smooth) !important; text-transform: uppercase; }
    
    .btn-gold { background: var(--gold-smooth); color: #000; border: none; font-weight: 700; border-radius: 0; }
    .btn-gold:hover { background: #ffffff; color: #000; }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-end mb-5 border-bottom border-secondary pb-4">
        <div>
            <h1 class="font-serif display-5 mb-1" style="color: var(--gold-smooth);">Catalogue de la Cave</h1>
            <p class="text-white mb-0 fw-bold">Gestion des références et des stocks (Back-office)</p>
        </div>
        <a href="index.php" class="btn btn-outline-light btn-sm px-4 fw-bold">RETOUR DASHBOARD</a>
    </div>

    <div class="row g-5">
        <div class="col-lg-4">
            <div class="luxury-card p-4 sticky-top" style="top: 100px;">
                <h4 class="font-serif mb-4 text-white border-bottom border-secondary pb-2">Nouveau Vin</h4>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <div class="mb-3">
                        <label>Nom de la Cuvée</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label>Prix (€)</label>
                            <input type="number" step="0.01" name="prix" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label>Stock Initial</label>
                            <input type="number" name="quantite" class="form-control" value="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type_vin" class="form-select">
                            <option value="Rouge">Rouge</option>
                            <option value="Blanc">Blanc</option>
                            <option value="Rosé">Rosé</option>
                            <option value="Champagne">Champagne</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Image (URL)</label>
                        <input type="text" name="image" class="form-control">
                    </div>
                    <button type="submit" name="add" class="btn btn-gold w-100 py-3 mt-3">INSCRIRE AU CATALOGUE</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="luxury-card overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">Référence</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="wine-img-container me-3">
                                        <img src="<?= e($item['image']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white"><?= e($item['nom']) ?></div>
                                        <small style="color: var(--gold-smooth); font-weight: 600;"><?= e($item['type_vin']) ?> — <?= e($item['millesime']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold"><?= formatPrice($item['prix']) ?></td>
                            <td>
                                <span class="badge rounded-0 p-2 <?= $item['quantite_stock'] > 5 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= (int)$item['quantite_stock'] ?> UNITÉS
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group gap-2">
                                    <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning fw-bold">MODIFIER</a>
                                    <form method="POST" onsubmit="return confirm('Retirer définitivement ?');" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <button type="submit" name="delete" class="btn btn-sm btn-outline-danger fw-bold">RETIRER</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>