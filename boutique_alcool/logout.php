<?php
/**
 * logout.php - Script de déconnexion
 * Ce fichier gère la fermeture sécurisée de la session utilisateur
 * et le nettoyage des données temporaires.
 */

// Importation de la configuration et des fonctions pour utiliser logoutUser() et setFlash()
require_once 'config/db.php';
require_once 'includes/functions.php';

// 1. INITIALISATION
// On démarre la session uniquement si elle n'est pas déjà active pour pouvoir agir dessus.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 2. NETTOYAGE DES VARIABLES
 * Appel de la fonction personnalisée logoutUser() définie dans functions.php.
 * Elle supprime spécifiquement $_SESSION['user'], $_SESSION['user_id'] et le panier.
 */
logoutUser();

/**
 * 3. DESTRUCTION DE LA SESSION
 * session_destroy() efface toutes les données associées à la session actuelle sur le serveur.
 * C'est une mesure de sécurité indispensable pour qu'un autre utilisateur n'utilise pas le même navigateur.
 */
session_destroy();

/**
 * 4. GESTION DU MESSAGE DE SORTIE
 * Problème technique : session_destroy() a tout effacé, y compris la possibilité d'afficher un message.
 * Solution : On redémarre une toute nouvelle session vide uniquement pour stocker le message flash.
 */
session_start();
setFlash('success', 'Vous avez été déconnecté avec succès. À bientôt au Domaine Prestige !');

/**
 * 5. REDIRECTION FINALE
 * On renvoie l'utilisateur vers la page d'accueil.
 * L'absence de slash initial ('index.php') garantit que la redirection reste relative au dossier actuel.
 */
redirect('index.php');
exit;