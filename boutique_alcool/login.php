<?php
/**
 * Page de Connexion - Domaine Prestige
 * Gère l'accès sécurisé des membres et des administrateurs au site.
 */
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Connexion';

// 1. REDIRECTION INTELLIGENTE
// Si l'utilisateur est déjà connecté, on lui évite de revoir le formulaire.
// On le renvoie vers l'admin s'il est gestionnaire, sinon vers l'accueil.
if (isLogged()) {
    isAdmin() ? header('Location: admin/index.php') : header('Location: index.php');
    exit;
}

$error = null;

/**
 * TRAITEMENT DE LA CONNEXION
 * S'exécute lors de la soumission du formulaire via POST.
 */
if (isPost()) {
    // Récupération et nettoyage des données entrantes.
    // L'email est "nettoyé" via clean() mais le mot de passe est gardé intact pour ne pas casser les caractères spéciaux.
    $email = trim(post('email')); 
    $password = $_POST['password']; 
    
    if (!empty($email) && !empty($password)) {
        
        // 2. RECHERCHE DE L'UTILISATEUR (SQL Préparé)
        // On cherche une ligne correspondante à l'email fourni.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // 3. VÉRIFICATION DU MOT DE PASSE HACHÉ
        // Utilisation de password_verify() qui compare le texte clair avec le hash stocké.
        // C'est une sécurité majeure : même en cas de vol de BDD, les MDP sont illisibles.
        if ($user && password_verify($password, $user['password'])) {
            
            // 4. INITIALISATION DE LA SESSION
            // On stocke les informations de l'utilisateur dans $_SESSION pour qu'il soit reconnu sur tout le site.
            $_SESSION['user'] = $user;
            
            // Message de bienvenue temporaire via le système Flash
            setFlash('success', "Heureux de vous revoir, " . e($user['nom']));
            
            // Redirection selon le rôle (Admin vers Back-office, Client vers Front-office)
            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            // Erreur générique volontaire pour ne pas aider un pirate à savoir si l'email existe.
            $error = "Identifiants incorrects ou compte inexistant.";
        }
    } else {
        $error = "Veuillez renseigner tous les champs.";
    }
}

require_once 'includes/header.php';
?>

<style>
    /* Design luxueux avec superposition de dégradé sur une image de cave */
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
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .text-white-fixed { color: #ffffff !important; opacity: 1 !important; }
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
                            <label for="email" class="small mb-1">Adresse Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="small mb-1">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn w-100 py-3 fw-bold text-uppercase" style="letter-spacing: 2px; background-color: #c9a961; color: #000; border: none;">
                            Se Connecter
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