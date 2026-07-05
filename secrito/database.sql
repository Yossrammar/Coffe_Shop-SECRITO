CREATE DATABASE IF NOT EXISTS secrito CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE secrito;

CREATE TABLE IF NOT EXISTS clients (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    prenom     VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    telephone  VARCHAR(30),
    adresse    TEXT,
    mot_de_passe VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom          VARCHAR(100) DEFAULT 'Admin'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produits (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    description TEXT,
    prix        DECIMAL(8,2) NOT NULL,
    categorie   ENUM('brunch','sucree','sale','fresh') NOT NULL,
    image       VARCHAR(255),
    actif       TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS commandes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    client_id    INT,
    nom_client   VARCHAR(100),
    telephone    VARCHAR(30),
    adresse      TEXT,
    total        DECIMAL(8,2) NOT NULL,
    livraison    DECIMAL(8,2) DEFAULT 3.00,
    statut       ENUM('en_attente','confirmee','en_livraison','livree','annulee') DEFAULT 'en_attente',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS commande_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id  INT,
    nom_produit VARCHAR(150) NOT NULL,
    prix_unit   DECIMAL(8,2) NOT NULL,
    quantite    INT NOT NULL DEFAULT 1,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id)  REFERENCES produits(id)  ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    telephone  VARCHAR(30),
    message    TEXT NOT NULL,
    lu         TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO admins (email, mot_de_passe, nom) VALUES
('admin@secrito.tn', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Secrito');

INSERT INTO produits (nom, description, prix, categorie, image) VALUES
('Douceur quotidienne','Un café avec deux mini croissants gourmands : un croissant farci camembert avec une crème de poulet, un croissant avec une mousse de vanille, crème de noisette et des fruits de saison en cube.',22.00,'brunch','Douceur quotidienne.png'),
('Arabesque','Deux mini brik — Mokli à l\'ancienne — Bezzin poulpe — Assida zgougou — Bsissa pistache SECRITO — Laklouka — Mélange SECRITO avec tahina et rob — Zammit et fruit de saison.',55.00,'brunch','Arabesque.png'),
('Gourmand','Wrap poulet pané — Mousse de chocolat croquante — Dattes gourmandes — Trois mini croissants crème : Pistache, Noisette, Vanille — 2 mini crêpes poulet crème & crème de thon — œuf crème champignons.',47.00,'brunch','Gourmand.png'),
('Salé','Oeuf bénédicte saumon — Croissant salé — Salade grecque — Assiette de fromages — Citronnade à la SECRITO — Crème de thon — Pomme de terre et champignons.',45.00,'brunch','Salé.png'),
('Healthy','Toast salé avocat, saumon, oeuf poché — Toast sucré ricotta SECRITO, fruits de saison, noix et miel — Shot bomb — Salade avec légumes grillés — Deux boules sucrées healthy.',35.00,'brunch','Healthy.png'),
('Fleur de Chocolats','2 fleurs de chocolat Secrito.',14.00,'sucree','Fleur de Chocolats.png'),
('Pistache Secrito',NULL,19.00,'sucree','Pistache secrito.png'),
('Noisettes Secrito',NULL,18.00,'sucree','Noisettes secrito.png'),
('Tiramisu Signature Secrito',NULL,20.00,'sucree','Tiramisu.png'),
('Crêpe Caramel Beurre Salé',NULL,15.00,'sucree','Crêpe Caramel Beurre Salé.png'),
('Crêpe Nutella',NULL,17.00,'sucree','Crêpe Nutella.png'),
('Crème brûlée',NULL,21.00,'sucree','Crème brulée.png'),
('Wrap Viande Hachée spicy','Un wrap généreux garni de bœuf haché maison, légèrement pimenté pour relever les saveurs.',20.00,'sale','Wrap Viande Hachée spicy.png'),
('Omelette','TARTUFFO : Truffes — LA SPAGNIOLA : Végétarienne — NORVÉGIENNE : Saumon.',28.00,'sale','omllette.png'),
('Crevette Panée','Riz Blanc, fruit de saison, carottes râpées marinées, edamame, chou cabus, sauce soja et mayonnaise épicée.',28.00,'sale','crevette pané.png'),
('Crevette Grillée','Riz Blanc, fruit de saison, carottes râpées marinées, edamame, chou cabus, sauce soja et mayonnaise épicée.',30.00,'sale','Crevette Grillé.png'),
('Saumon','Riz Blanc, fruit de saison, carottes râpées marinées, edamame, chou cabus, sauce soja et mayonnaise épicée.',28.00,'sale','Saumon.png'),
('Café latte glacé rose',NULL,16.00,'fresh','café latte glacée rose.png'),
('Café latte glacé pistache',NULL,19.00,'fresh','Café latte glacée pistache.png'),
('Fraise rose',NULL,15.00,'fresh','fraise rose.png'),
('Mangue Passion',NULL,16.00,'fresh','Mangue Passion.png'),
('Jus d\'Orange Pressé Minute',NULL,8.00,'fresh','Jus d\'Orange Pressé Minute.png'),
('Jus d\'Agrumes Signature','Orange, pamplemousse, bergamote.',13.00,'fresh','Jus d\'Agrumes Signature.png');
