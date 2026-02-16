# 🍷 Domaine Prestige - Site E-commerce de Vins Fins

**Domaine Prestige** est une application web dynamique de vente de vins de luxe développée en PHP. Ce projet a été réalisé dans le cadre du cursus **Bachelor 2** pour démontrer la maîtrise du développement back-end, de la gestion de base de données SQL et de la sécurité des échanges.

---

## 🚀 Fonctionnalités

### Front-Office (Client)

* **Accueil Dynamique** : Mise en avant des nouveautés et des crus d'exception.
* **Catalogue Complet** : Système de filtrage par type (Rouge, Blanc, Rosé, Champagne) et par tranche de prix.
* **Recherche Intégrée** : Barre de recherche textuelle sur les noms et appellations.
* **Fiches Produits** : Détails techniques complets (cépages, garde, température) et suggestions de produits similaires.
* **Panier Sécurisé** : Gestion des quantités, calcul automatique des frais de port et protection contre les failles CSRF.
* **Espace Client** : Inscription et connexion avec hachage de sécurité.

### Back-Office (Administration)

* **Dashboard** : Vue d'ensemble des statistiques du site.
* **Gestion CRUD** : Création, modification et suppression des articles et des stocks.
* **Gestion Utilisateurs** : Visualisation et contrôle des comptes clients et administrateurs.

---

## 🛠 Stack Technique

* **Langage :** PHP 8.2 (Procédural avec fonctions modulaires)
* **Base de données :** MySQL / MariaDB
* **Serveur Local :** XAMPP / WAMP / MAMP
* **Frontend :** HTML5, CSS3 (Bootstrap 5), FontAwesome pour les icônes.
* **Sécurité :** Requêtes préparées (PDO), protection CSRF, hachage `password_hash`.

---

## 📂 Structure du Projet

```text
├── admin/              # Espace d'administration (CRUD)
├── config/             # Configuration de la base de données (PDO)
├── includes/           # Fonctions réutilisables, header et footer
├── assets/             # Images, CSS et scripts JS
├── detail.php          # Vue détaillée d'un produit
├── articles.php        # Catalogue complet avec filtres
├── panier.php          # Gestion du panier d'achat
├── index.php           # Page d'accueil
└── install_db.php      # Script d'installation de la base de données

```

---

## ⚙️ Installation

1. **Cloner le projet** dans votre dossier `htdocs` (XAMPP).
2. **Démarrer Apache et MySQL** via le panneau de contrôle XAMPP.
3. **Importer la base de données** :
* Créez une base nommée `domaine_prestige` dans phpMyAdmin.
* Importez le fichier `domaine_prestige.sql` ou lancez le script `install_db.php` depuis votre navigateur (`localhost/votre_projet/install_db.php`).


4. **Accès Admin par défaut** :
* **Identifiant :** `admin@domaine.fr`
* **Mot de passe :** `admin123`