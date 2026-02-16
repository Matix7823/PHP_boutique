<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// 1. On démarre la session pour pouvoir la manipuler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. On utilise la fonction de déconnexion globale
// Elle va unset $_SESSION['user'] et $_SESSION['user_id']
logoutUser();

// 3. On détruit la session actuelle pour nettoyer le navigateur
session_destroy();

// 4. TRÈS IMPORTANT : On redémarre une session flash VIDE 
// juste pour transporter le message vers la page d'accueil
session_start();
setFlash('success', 'Vous avez été déconnecté avec succès. À bientôt !');

// 5. Redirection relative (sans le slash initial pour éviter l'erreur 404)
redirect('index.php');
exit;