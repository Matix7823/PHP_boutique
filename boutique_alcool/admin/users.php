<?php
/**
 * Page de gestion des utilisateurs (Back-office)
 * Permet de visualiser la liste et de supprimer des comptes.
 */
require_once '../config/db.php';
require_once '../includes/functions.php';

// Vérification de l'authentification admin [cite: 26, 27]
if (!isAdmin()) { 
    redirect('../login.php'); 
}

// --- LOGIQUE DE SUPPRESSION --- [cite: 35]
if (isPost() && post('action') === 'delete') {
    $id_to_delete = (int)post('id');
    $current_admin_id = $_SESSION['user']['id'];

    // Sécurité : Empêcher un admin de supprimer son propre compte
    if ($id_to_delete !== $current_admin_id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        setFlash('success', 'Utilisateur supprimé avec succès.');
    } else {
        setFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
    }
    redirect('users.php');
}

// Récupération de la liste des utilisateurs [cite: 34]
$users = $pdo->query("SELECT id, nom, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white font-serif"><i class="fas fa-users me-2"></i>Gestion des Utilisateurs</h2>
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
                            <form action="users.php" method="POST" onsubmit="return confirm('Confirmer la suppression définitive ?')">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-secondary">Moi (Admin)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>