<?php
/**
 * Page de gestion des utilisateurs (Back-office) - Domaine Prestige
 * Permet à l'administrateur de visualiser la liste des inscrits et de supprimer des comptes.
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

/**
 * SECURITÉ : Contrôle d'accès
 * On vérifie que la personne connectée a bien le rôle 'admin'.
 */
if (!isAdmin()) { 
    redirect('../login.php'); 
}

/**
 * LOGIQUE DE SUPPRESSION (D du CRUD)
 * S'exécute lorsqu'un formulaire de suppression est soumis.
 */
if (isPost() && post('action') === 'delete') {
    $id_to_delete = (int)post('id');
    $current_admin_id = $_SESSION['user']['id'];

    // SECURITÉ CRUCIALE : Empêcher un admin de supprimer son propre compte par erreur
    // Cela évite de se retrouver bloqué sans accès au back-office.
    if ($id_to_delete !== $current_admin_id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        setFlash('success', 'L\'utilisateur a été supprimé avec succès.');
    } else {
        setFlash('danger', 'Sécurité : Vous ne pouvez pas supprimer votre propre compte administrateur.');
    }
    
    // Redirection pour rafraîchir la liste et éviter de renvoyer le formulaire
    redirect('users.php');
}

/**
 * RÉCUPÉRATION DES DONNÉES (R du CRUD)
 * On récupère les informations essentielles des utilisateurs triées par date.
 */
$users = $pdo->query("SELECT id, nom, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white font-serif"><i class="fas fa-users me-2" style="color: #c9a961;"></i>Gestion des Utilisateurs</h2>
        <a href="index.php" class="btn btn-outline-light btn-sm">Retour Dashboard</a>
    </div>

    <div class="table-responsive bg-dark p-3 border border-secondary shadow-sm">
        <table class="table table-dark table-hover mb-0">
            <thead class="table-light text-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date d'inscription</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr class="align-middle">
                    <td><?= $u['id'] ?></td>
                    <td class="fw-bold"><?= e($u['nom']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] == 'admin' ? 'bg-danger' : 'bg-info text-dark' ?>">
                            <?= e($u['role']) ?>
                        </span>
                    </td>
                    <td><?= formatDate($u['created_at']) ?></td>
                    
                    <td class="text-center">
                        <?php if($u['id'] != $_SESSION['user']['id']): ?>
                            <form action="users.php" method="POST" onsubmit="return confirm('Attention : Confirmer la suppression définitive de cet utilisateur ?')">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-secondary">Moi (Connecté)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Inclusion du pied de page
require_once '../includes/footer.php'; 
?>