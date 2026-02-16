<?php
/**
 * Script d'Installation de la Base de Données - Domaine Prestige
 * Ce fichier automatise la création des tables et l'insertion des données initiales.
 * Utilisation de la syntaxe Nowdoc pour gérer le SQL complexe contenant des symboles $.
 */

// Configuration des identifiants de connexion au serveur local (XAMPP/WAMP)
$host = 'localhost';
$user = 'root';
$pass = ''; // Généralement vide par défaut sur XAMPP

try {
    /**
     * CONNEXION INITIALE AU SERVEUR MYSQL
     * Note : On ne spécifie pas de base de données ici car nous allons la créer.
     * PDO::MYSQL_ATTR_MULTI_STATEMENTS permet d'exécuter plusieurs blocs SQL en une fois.
     */
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true
    ]);

    /**
     * Très important pour ton oral : Cette syntaxe permet d'écrire du texte multi-ligne
     * sans que PHP n'interprète les symboles '$' (utilisés dans les mots de passe hachés).
     */
    $sql = <<<'SQL'
    -- Suppression et recréation de la base de données pour un état propre
    DROP DATABASE IF EXISTS domaine_prestige;
    CREATE DATABASE IF NOT EXISTS domaine_prestige CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    USE domaine_prestige;

    -- ==========================================================
    -- STRUCTURE DES TABLES
    -- ==========================================================

    -- Table 1: USERS (Gestion des comptes et rôles)
    CREATE TABLE users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100),
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        telephone VARCHAR(20),
        adresse TEXT,
        code_postal VARCHAR(10),
        ville VARCHAR(100),
        pays VARCHAR(100) DEFAULT 'France',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Table 2: ITEMS (Catalogue détaillé des vins)
    CREATE TABLE items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        prix DECIMAL(10,2) NOT NULL,
        image VARCHAR(500),
        date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        appellation VARCHAR(255),
        millesime YEAR,
        cepage VARCHAR(255),
        degre_alcool DECIMAL(4,2),
        volume INT DEFAULT 75,
        elevage VARCHAR(255),
        garde VARCHAR(100),
        temperature_service VARCHAR(50),
        type_vin ENUM('Rouge', 'Blanc', 'Rosé', 'Champagne', 'Autre') DEFAULT 'Rouge',
        region VARCHAR(100),
        disponible BOOLEAN DEFAULT TRUE,
        nouveaute BOOLEAN DEFAULT FALSE,
        promotion BOOLEAN DEFAULT FALSE,
        prix_promo DECIMAL(10,2),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Table 3: STOCK (Gestion logistique)
    CREATE TABLE stock (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_item INT NOT NULL,
        quantite_stock INT NOT NULL DEFAULT 0,
        seuil_alerte INT DEFAULT 5,
        derniere_maj TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        -- Clé étrangère pour assurer l'intégrité référentielle
        FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Table 4: ORDERS (Suivi des lignes de commandes)
    CREATE TABLE orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_user INT NOT NULL,
        id_item INT NOT NULL,
        quantite INT NOT NULL DEFAULT 1,
        prix_unitaire DECIMAL(10,2) NOT NULL,
        sous_total DECIMAL(10,2) NOT NULL,
        date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        statut ENUM('en_attente', 'validee', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
        FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Table 5: INVOICE (Facturation finale)
    CREATE TABLE invoice (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_user INT NOT NULL,
        date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        montant DECIMAL(10,2) NOT NULL,
        adresse_facturation TEXT NOT NULL,
        ville VARCHAR(100) NOT NULL,
        code_postal VARCHAR(10) NOT NULL,
        pays VARCHAR(100) DEFAULT 'France',
        mode_paiement ENUM('carte', 'paypal', 'virement') DEFAULT 'carte',
        statut_paiement ENUM('en_attente', 'paye', 'rembourse', 'echoue') DEFAULT 'en_attente',
        numero_facture VARCHAR(50) UNIQUE,
        FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- ==========================================================
    -- INSERTION DES DONNÉES INITIALES
    -- ==========================================================

    -- Création d'un administrateur par défaut (Mot de passe : admin123)
    INSERT INTO users (nom, prenom, email, password, role, telephone, adresse, ville, code_postal) VALUES
    ('Admin', 'Domaine', 'admin@domaine.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '05 56 00 00 00', 'Route des Grands Crus', 'Bordeaux', '33000');

    -- Ajout de références de vins pour peupler le catalogue
    INSERT INTO items (nom, description, prix, image, type_vin, appellation, millesime, cepage, degre_alcool, elevage, garde, temperature_service, region, nouveaute, date_publication) VALUES
    ('Château Prestige Grand Cru 2018', 'Un grand vin rouge de Bordeaux aux arômes complexes.', 68.50, 'https://images.unsplash.com/photo-1586370434639-0fe43b2d32d6?w=400', 'Rouge', 'AOC Bordeaux Supérieur', 2018, 'Merlot 60%, Cabernet 30%', 14.0, '18 mois fûts', '10-15 ans', '16-18°C', 'Bordeaux', TRUE, NOW()),
    ('Domaine Prestige Blanc 2020', 'Vin blanc sec élégant et raffiné.', 36.00, 'https://images.unsplash.com/photo-1595475207225-428b62bda833?w=400', 'Blanc', 'AOC Bordeaux Blanc', 2020, 'Sauvignon 70%', 12.5, '8 mois sur lies', '5-8 ans', '10-12°C', 'Bordeaux', FALSE, NOW()),
    ('Cuvée Centenaire 2015', 'Notre cuvée d''exception.', 125.00, 'https://images.unsplash.com/photo-1574739782594-11574c0d39e7?w=400', 'Rouge', 'AOC Pomerol', 2015, 'Merlot 85%', 14.5, '24 mois fûts neufs', '20-30 ans', '17-18°C', 'Bordeaux', TRUE, NOW()),
    ('Rosé de Prestige 2021', 'Rosé fruité idéal pour l''été.', 19.50, 'https://images.unsplash.com/photo-1559563362-c667ba5f5480?w=400', 'Rosé', 'AOC Bordeaux Rosé', 2021, 'Merlot 60%', 12.0, 'Cuve inox', '2 ans', '8°C', 'Bordeaux', FALSE, NOW());

    -- Initialisation des stocks pour les produits insérés ci-dessus
    INSERT INTO stock (id_item, quantite_stock, seuil_alerte) VALUES
    (1, 45, 10), (2, 78, 15), (3, 12, 2), (4, 100, 20);
SQL;

    // Exécution du gros bloc de commandes SQL
    $pdo->exec($sql);
    
    // Feedback visuel pour confirmer la réussite
    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h1 style='color: green;'>✅ INSTALLATION RÉUSSIE !</h1>";
    echo "<p>La base de données <strong>domaine_prestige</strong> a été créée et peuplée avec succès.</p>";
    echo "<a href='index.php' style='display: inline-block; padding: 10px 20px; background: #c9a961; color: black; text-decoration: none; font-weight: bold;'>RETOURNER À L'ACCUEIL</a>";
    echo "</div>";

} catch (PDOException $e) {
    // En cas d'erreur de connexion ou de syntaxe SQL
    die("<h1 style='color: red;'>❌ ÉCHEC DE L'INSTALLATION</h1>" . $e->getMessage());
}
?>