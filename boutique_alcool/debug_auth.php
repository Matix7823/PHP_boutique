<?php
/**
 * debug_auth.php - Outil de Diagnostic & Réinitialisation Admin
 * Ce script permet de forcer la mise à jour du mot de passe administrateur
 * et de vérifier la compatibilité du hachage avec le serveur actuel.
 */

// Importation de la connexion et des fonctions (nécessaire pour accéder à $pdo et aux sessions)
require_once 'config/db.php';
require_once 'includes/functions.php';

// Style minimaliste pour l'interface de diagnostic
echo "<style>body { font-family: sans-serif; line-height: 1.6; padding: 20px; background: #f4f4f4; }</style>";
echo "<h1>🛠 Diagnostic & Réinitialisation Admin</h1>";

// Configuration des identifiants à réinitialiser
$email = 'admin@domaine.fr';
$pass_clair = 'admin123';

/**
 * 1. GÉNÉRATION DU HASH
 * On utilise password_hash avec l'algorithme par défaut (BCRYPT).
 * Cette étape génère une empreinte unique sécurisée du mot de passe clair.
 */
$hash_tout_neuf = password_hash($pass_clair, PASSWORD_DEFAULT);

/**
 * 2. MISE À JOUR (UPDATE du CRUD)
 * On injecte le nouveau hash directement en base de données pour l'utilisateur admin.
 * Cela permet de réparer l'accès si le hash précédent était corrompu ou incompatible.
 */
try {
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->execute([$hash_tout_neuf, $email]);
    echo "<div style='color: green; font-weight: bold;'>✅ ÉTAPE 1 : Base de données mise à jour avec le nouveau hash.</div>";
} catch (Exception $e) {
    die("<div style='color: red;'>❌ ERREUR SQL : " . $e->getMessage() . "</div>");
}

/**
 * 3. VÉRIFICATION (READ)
 * On récupère l'utilisateur qui vient d'être mis à jour pour effectuer un test réel.
 */
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div style='color: red;'>❌ ÉTAPE 2 : L'utilisateur $email est introuvable. Vérifiez la table 'users'.</div>";
} else {
    echo "<div style='color: blue;'>ℹ️ ÉTAPE 2 : Utilisateur trouvé en base. Rôle : " . $user['role'] . "</div>";

    /**
     * 4. TEST DE VALIDATION
     * On compare le mot de passe "clair" avec le "hash" récupéré de la base.
     * C'est la fonction password_verify qui gère le décryptage de l'empreinte.
     */
    if (password_verify($pass_clair, $user['password'])) {
        echo "<div style='background: lightgreen; padding: 15px; border: 2px solid green; margin-top: 20px;'>";
        echo "✅ **TEST RÉUSSI !** La correspondance entre le mot de passe et le hash est parfaite.<br>";
        echo "🚀 <a href='login.php' style='font-size: 20px;'>Vous pouvez maintenant vous connecter ici</a>";
        echo "</div>";
        
        // Optionnel : Connexion automatique pour le test
        $_SESSION['user'] = $user;
    } else {
        echo "<div style='color: red; font-weight: bold;'>❌ TEST ÉCHOUÉ : La vérification PHP a échoué. Problème de configuration serveur ?</div>";
    }
}