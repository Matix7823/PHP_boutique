<?php
// debug_auth.php - Version Fusionnée et Corrigée
require_once 'config/db.php';
require_once 'includes/functions.php';

echo "<style>body { font-family: sans-serif; line-height: 1.6; padding: 20px; background: #f4f4f4; }</style>";
echo "<h1>🛠 Diagnostic & Réinitialisation Admin</h1>";

$email = 'admin@domaine.fr';
$pass_clair = 'admin123';

// 1. GÉNÉRATION D'UN NOUVEAU HASH LOCAL
$hash_tout_neuf = password_hash($pass_clair, PASSWORD_DEFAULT);

// 2. MISE À JOUR FORCÉE EN BASE DE DONNÉES
try {
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->execute([$hash_tout_neuf, $email]);
    echo "<div style='color: green; font-weight: bold;'>✅ ÉTAPE 1 : Base de données mise à jour avec le hash généré par ton serveur.</div>";
} catch (Exception $e) {
    die("<div style='color: red;'>❌ ERREUR SQL : " . $e->getMessage() . "</div>");
}

// 3. VÉRIFICATION IMMÉDIATE
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div style='color: red;'>❌ ÉTAPE 2 : L'utilisateur $email est introuvable. Vérifie le nom de ta base !</div>";
} else {
    echo "<div style='color: blue;'>ℹ️ ÉTAPE 2 : Utilisateur trouvé. Rôle : " . $user['role'] . "</div>";

    if (password_verify($pass_clair, $user['password'])) {
        echo "<div style='background: lightgreen; padding: 15px; border: 2px solid green; margin-top: 20px;'>";
        echo "✅ **TEST RÉUSSI !** Le mot de passe '$pass_clair' fonctionne avec le hash stocké.<br>";
        echo "🚀 <a href='login.php' style='font-size: 20px;'>Clique ici pour te connecter sur le site</a>";
        echo "</div>";
        
        // Optionnel : On le connecte direct pour tester
        $_SESSION['user'] = $user;
    } else {
        echo "<div style='color: red; font-weight: bold;'>❌ TEST ÉCHOUÉ : La vérification PHP refuse toujours le mot de passe.</div>";
    }
}