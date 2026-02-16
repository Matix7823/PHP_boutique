<?php
/**
 * Page de Connexion - Domaine Prestige
 * Gère l'accès sécurisé des membres et administrateurs.
 */
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Connexion';

// 1. Redirection automatique si déjà connecté
if (isLogged()) {
    isAdmin() ? header('Location: admin/index.php') : header('Location: index.php');
    exit;
}

$error = null;

if (isPost()) {
    // On récupère l'email avec post() qui applique clean()
    // On ajoute un trim supplémentaire par précaution
    $email = trim(post('email')); 
    $password = $_POST['password']; 
    
    if (!empty($email) && !empty($password)) {
        // 2. Recherche de l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // 3. Vérification du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            // Initialisation de la session complète
            $_SESSION['user'] = $user;
            
            setFlash('success', "Heureux de vous revoir, " . e($user['nom']));
            
            // Redirection intelligente
            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = "Identifiants incorrects ou compte inexistant.";
        }
    } else {
        $error = "Veuillez renseigner tous les champs.";
    }
}

require_once 'includes/header.php';
?>

<style>
    .login-container {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1516594915697-87eb3b1c14ea?w=1920') center/cover;
        min-height: 85vh;
        display: flex;
        align-items: center;
    }
    .login-card {
        background-color: #000;
        border: 1px solid #c9a961;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,1);
    }
    .form-control {
        background: #0a0a0a !important;
        border: 1px solid #444 !important;
        color: #fff !important;
        border-radius: 0;
        height: 50px;
    }
    .form-control:focus {
        border-color: #c9a961 !important;
        box-shadow: none;
        background: #111 !important;
    }
    label {
        color: #c9a961 !important;
        font-weight: 700;
        letter-spacing: 1px;
        cursor: pointer;
    }
    .text-white-fixed {
        color: #ffffff !important;
        opacity: 1 !important;
    }
    .btn-gold:hover {
        background-color: #ffffff !important;
        color: #000 !important;
        transition: 0.3s;
    }
</style>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="login-card">
                    
                    <div class="text-center mb-5">
                        <i class="fas fa-wine-glass-alt fa-3x mb-3" style="color: #c9a961;"></i>
                        <h2 class="text-white-fixed font-serif">DOMAINE PRESTIGE</h2>
                        <p class="text-white-fixed small mt-2">Authentification requise pour accéder à votre cave</p>
                    </div>

                    <?php if($error): ?>
                        <div class="alert alert-danger text-center py-2 mb-4 rounded-0 small fw-bold">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="small text-uppercase mb-1">Adresse Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>" required autocomplete="email">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="small text-uppercase mb-1">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-3 fw-bold text-uppercase" style="letter-spacing: 2px; background-color: #c9a961; color: #000; border: none;">
                            Connexion
                        </button>
                    </form>

                    <div class="text-center border-top border-secondary pt-4 mt-4">
                        <p class="text-white-fixed small mb-1">Vous n'avez pas encore de compte ?</p>
                        <a href="register.php" class="fw-bold" style="color: #c9a961; text-decoration: none;">CRÉER UN COMPTE MEMBRE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>