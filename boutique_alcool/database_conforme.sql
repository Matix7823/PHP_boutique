-- 1. RECRÉATION TOTALE DE LA BASE
DROP DATABASE IF EXISTS domaine_prestige;
CREATE DATABASE domaine_prestige CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE domaine_prestige;

-- ================================================
-- 2. STRUCTURE DES TABLES (5 Tables Obligatoires)
-- ================================================

-- Table 1: USERS (id, nom, email, password, role)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    telephone VARCHAR(20),
    adresse TEXT,
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 2: ITEMS (id, nom, description, prix, image, etc.)
CREATE TABLE items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    image VARCHAR(500),
    type_vin ENUM('Rouge', 'Blanc', 'Rosé', 'Champagne') DEFAULT 'Rouge',
    appellation VARCHAR(255),
    millesime INT,
    cepage VARCHAR(255),
    degre_alcool VARCHAR(10),
    garde VARCHAR(100),
    temperature_service VARCHAR(50),
    disponible BOOLEAN DEFAULT TRUE,
    nouveaute BOOLEAN DEFAULT FALSE,
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 3: STOCK (id, id_item, quantite_stock)
CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_item INT NOT NULL,
    quantite_stock INT NOT NULL DEFAULT 0,
    seuil_alerte INT DEFAULT 5,
    FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 4: ORDERS (id, id_user, id_item, quantite)
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_item INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    sous_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'validee', 'annulee') DEFAULT 'en_attente',
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 5: INVOICE (id, id_user, date_transaction, montant)
CREATE TABLE invoice (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant DECIMAL(10,2) NOT NULL,
    numero_facture VARCHAR(50) UNIQUE,
    adresse_facturation TEXT,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- 3. INSERTION DES DONNÉES PAR DÉFAUT
-- ================================================

-- Admin par défaut (MDP: admin123)
INSERT INTO users (nom, prenom, email, password, role) VALUES
('Maître de Chai', 'Admin', 'admin@domaineprestige.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Quelques vins de test
INSERT INTO items (nom, description, prix, type_vin, appellation, millesime, nouveaute, image) VALUES
('Château Prestige 2018', 'Un rouge puissant et boisé.', 68.50, 'Rouge', 'AOC Bordeaux', 2018, TRUE, 'https://images.unsplash.com/photo-1586370434639-0fe43b2d32d6?w=400'),
('Blanc Excellence 2020', 'Un blanc sec d une grande fraîcheur.', 36.00, 'Blanc', 'AOC Entre-Deux-Mers', 2020, FALSE, 'https://images.unsplash.com/photo-1595475207225-428b62bda831?w=400'),
('Rosé Élégance 2021', 'Idéal pour vos soirées d été.', 24.00, 'Rosé', 'AOC Bordeaux Rosé', 2021, TRUE, 'https://images.unsplash.com/photo-1580218821985-edc5f3c1f0eb?w=400');

-- Initialisation du stock pour ces 3 vins
INSERT INTO stock (id_item, quantite_stock) VALUES (1, 45), (2, 30), (3, 60);

-- ================================================
-- 4. AUTOMATISATION (Triggers)
-- ================================================

DELIMITER //

-- Met à jour le stock automatiquement après une commande
CREATE TRIGGER after_order_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    UPDATE stock SET quantite_stock = quantite_stock - NEW.quantite 
    WHERE id_item = NEW.id_item;
END//

-- Génère un numéro de facture propre automatiquement (ex: FAC-2026-0001)
CREATE TRIGGER before_invoice_insert
BEFORE INSERT ON invoice
FOR EACH ROW
BEGIN
    DECLARE last_id INT;
    SELECT IFNULL(MAX(id), 0) + 1 INTO last_id FROM invoice;
    IF NEW.numero_facture IS NULL THEN
        SET NEW.numero_facture = CONCAT('FAC-', YEAR(NOW()), '-', LPAD(last_id, 4, '0'));
    END IF;
END//

DELIMITER ;