<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Inscription';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = clean($_POST['nom']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['password_confirm'];

    if ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hash])) {
                header('Location: login.php');
                exit;
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
                        <i class="fas fa-user-plus fa-2x mb-3" style="color: var(--gold);"></i>
                        <h2 class="text-white font-serif">Devenir Membre</h2>
                        <p style="color: #ccc; font-size: 0.9rem;">Rejoignez le club privé Domaine Prestige</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger text-center py-2 mb-4">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="text-white small text-uppercase mb-1">Nom complet</label>
                            <input type="text" name="nom" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff;" required>
                        </div>

                        <div class="mb-3">
                            <label class="text-white small text-uppercase mb-1">Email</label>
                            <input type="email" name="email" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff;" required>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="text-white small text-uppercase mb-1">Mot de passe</label>
                                <input type="password" name="password" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff;" required>
                            </div>
                            <div class="col-6">
                                <label class="text-white small text-uppercase mb-1">Confirmation</label>
                                <input type="password" name="password_confirm" class="form-control" style="background: #1a1a1a; border: 1px solid #444; color: #fff;" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 mb-3">Créer mon compte</button>
                    </form>

                    <div class="text-center border-top border-secondary pt-3 mt-3">
                        <p style="color: #ccc; font-size: 0.9rem; margin-bottom: 5px;">Déjà inscrit ?</p>
                        <a href="login.php" class="text-white" style="text-decoration: underline;">Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>