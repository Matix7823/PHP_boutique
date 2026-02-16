<?php
/**
 * Page de modification d'un article - Domaine Prestige
 * Permet d'éditer l'intégralité de la fiche technique sans textes fantômes.
 */
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) { redirect('../login.php'); } // [cite: 26, 27]

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = "";

// Récupération des données pour pré-remplir les champs 
$stmt = $pdo->prepare("SELECT i.*, s.quantite_stock FROM items i LEFT JOIN stock s ON i.id = s.id_item WHERE i.id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { redirect('items.php'); }

// Traitement de la mise à jour [cite: 30]
if (isPost() && isset($_POST['update'])) {
    try {
        $pdo->beginTransaction();
        $sql = "UPDATE items SET nom=?, description=?, prix=?, image=?, type_vin=?, appellation=?, millesime=?, cepage=?, degre_alcool=?, temperature_service=?, garde=?, elevage=? WHERE id=?";
        $pdo->prepare($sql)->execute([
            post('nom'), post('description'), post('prix'), post('image'), post('type_vin'), 
            post('appellation'), post('millesime'), post('cepage'), post('degre_alcool'), 
            post('temperature_service'), post('garde'), post('elevage'), $id
        ]);
        $pdo->prepare("UPDATE stock SET quantite_stock = ? WHERE id_item = ?")->execute([post('quantite'), $id]);
        $pdo->commit();
        setFlash('success', 'La fiche a été mise à jour avec succès.');
        redirect('items.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erreur : " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<style>
    .edit-container { background: #0f0f0f; border: 1px solid #333; padding: 30px; color: #fff; }
    .form-control, .form-select { 
        background: #000 !important; 
        border: 1px solid #444 !important; 
        color: #fff !important; 
        border-radius: 0;
    }
    .form-control:focus { border-color: #c9a961 !important; box-shadow: none; }
    label { color: #c9a961; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 5px; }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-serif m-0">Édition de la Référence</h2>
        <a href="items.php" class="btn btn-outline-light btn-sm">Annuler</a>
    </div>

    <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>

    <div class="edit-container">
        <form method="POST" class="row g-4">
            <div class="col-md-6">
                <label>Nom de la Cuvée</label>
                <input type="text" name="nom" class="form-control" value="<?= e($item['nom']) ?>" required>
            </div>
            <div class="col-md-3">
                <label>Prix de vente (€)</label>
                <input type="number" step="0.01" name="prix" class="form-control" value="<?= e($item['prix']) ?>" required>
            </div>
            <div class="col-md-3">
                <label>Unités en Stock</label>
                <input type="number" name="quantite" class="form-control" value="<?= e($item['quantite_stock']) ?>" required>
            </div>

            <div class="col-md-4">
                <label>Type de Vin</label>
                <select name="type_vin" class="form-select">
                    <option value="Rouge" <?= $item['type_vin'] == 'Rouge' ? 'selected' : '' ?>>Rouge</option>
                    <option value="Blanc" <?= $item['type_vin'] == 'Blanc' ? 'selected' : '' ?>>Blanc</option>
                    <option value="Rosé" <?= $item['type_vin'] == 'Rosé' ? 'selected' : '' ?>>Rosé</option>
                    <option value="Champagne" <?= $item['type_vin'] == 'Champagne' ? 'selected' : '' ?>>Champagne</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Appellation</label>
                <input type="text" name="appellation" class="form-control" value="<?= e($item['appellation']) ?>">
            </div>
            <div class="col-md-4">
                <label>Millésime</label>
                <input type="number" name="millesime" class="form-control" value="<?= e($item['millesime']) ?>">
            </div>

            <div class="col-md-6">
                <label>Cépages</label>
                <input type="text" name="cepage" class="form-control" value="<?= e($item['cepage']) ?>">
            </div>
            <div class="col-md-3">
                <label>Alcool (%)</label>
                <input type="text" name="degre_alcool" class="form-control" value="<?= e($item['degre_alcool']) ?>">
            </div>
            <div class="col-md-3">
                <label>T° Service</label>
                <input type="text" name="temperature_service" class="form-control" value="<?= e($item['temperature_service']) ?>">
            </div>

            <div class="col-md-6">
                <label>Potentiel de Garde</label>
                <input type="text" name="garde" class="form-control" value="<?= e($item['garde']) ?>">
            </div>
            <div class="col-md-6">
                <label>Élevage</label>
                <input type="text" name="elevage" class="form-control" value="<?= e($item['elevage']) ?>">
            </div>

            <div class="col-12">
                <label>Lien de l'Image</label>
                <input type="text" name="image" class="form-control" value="<?= e($item['image']) ?>">
            </div>

            <div class="col-12">
                <label>Description Commerciale</label>
                <textarea name="description" class="form-control" rows="5"><?= e($item['description']) ?></textarea>
            </div>

            <div class="col-12 mt-5">
                <button type="submit" name="update" class="btn btn-gold w-100 py-3 fw-bold">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>