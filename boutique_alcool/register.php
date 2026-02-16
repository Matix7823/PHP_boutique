<?php
/**
 * Page d'Inscription - Domaine Prestige
 * Gère la création de nouveaux comptes clients avec validation et hachage.
 */
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Inscription';

// --- LOGIQUE DE TRAITEMENT DU FORMULAIRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupération et nettoyage des données (Protection XSS via clean())
    $nom = clean($_POST['nom']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['password_confirm'];

    // 2. VALIDATIONS DE SÉCURITÉ
    if ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        // 3. VÉRIFICATION DE L'UNICITÉ DE L'EMAIL
        // On vérifie en base de données si cet email n'est pas déjà enregistré
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé par un autre membre.";
        } else {
            // 4. HACHAGE DU MOT DE PASSE (Sécurité Maximale)
            // On ne stocke jamais le mot de passe en clair. 
            // password_hash utilise l'algorithme BCRYPT par défaut.
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // 5. INSERTION EN BASE DE DONNÉES (Requête préparée)
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nom, $email, $hash])) {
                // En cas de succès, on redirige vers la page de connexion
                setFlash('success', 'Votre compte a été créé ! Vous pouvez vous connecter.');
                header('Location: login.php');
                exit;
            } else {
                $error = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?w=1920') center/cover; min-height: 90vh; display: flex; align-items: center;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="p-5" style="background-color: #121212; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-2x mb-3" style="color: #c9a961;"></i>
                        <h2 class="text-white font-serif">Devenir Membre</h2>
                        <p style="color: #ccc; font-size: 0.9rem;">Rejoignez le club privé Domaine Prestige</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger text-center py-2 mb-4 rounded-0 small">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="text-white small text-uppercase mb-1">Nom complet</label>
                            <input type="text" name="nom" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff; border-radius: 0;" value="<?= isset($nom) ? e($nom) : '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="text-white small text-uppercase mb-1">Email</label>
                            <input type="email" name="email" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff; border-radius: 0;" value="<?= isset($email) ? e($email) : '' ?>" required>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="text-white small text-uppercase mb-1">Mot de passe</label>
                                <input type="password" name="password" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff; border-radius: 0;" required>
                            </div>
                            <div class="col-6">
                                <label class="text-white small text-uppercase mb-1">Confirmation</label>
                                <input type="password" name="password_confirm" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff; border-radius: 0;" required>
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 mb-3 fw-bold" style="background: #c9a961; color: #000; border-radius: 0; padding: 12px;">
                            CRÉER MON COMPTE
                        </button>
                    </form>

                    <div class="text-center border-top border-secondary pt-3 mt-3">
                        <p style="color: #ccc; font-size: 0.9rem; margin-bottom: 5px;">Déjà inscrit ?</p>
                        <a href="login.php" class="fw-bold" style="color: #c9a961; text-decoration: none;">Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Inclusion du pied de page
require_once 'includes/footer.php'; 
?>