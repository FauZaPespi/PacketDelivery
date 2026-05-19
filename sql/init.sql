-- Packet Delivery - Schéma de base de données
-- Atelier Web - TPI 2025-2026

-- Table Employe (employés administratifs et livreurs)
CREATE TABLE IF NOT EXISTS Employe (
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    motDePasse VARCHAR(255) NOT NULL,
    estLivreur BOOLEAN NOT NULL DEFAULT FALSE,
    INDEX idx_email (email),
    INDEX idx_est_livreur (estLivreur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table RouteLivraison
CREATE TABLE IF NOT EXISTS RouteLivraison (
    id_route INT AUTO_INCREMENT PRIMARY KEY,
    id_employe_livreur INT NOT NULL,
    date_livraison DATE NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe_livreur) REFERENCES Employe(id_employe)
        ON DELETE CASCADE,
    UNIQUE KEY uk_livreur_date (id_employe_livreur, date_livraison),
    INDEX idx_date_livraison (date_livraison)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table Paquet
CREATE TABLE IF NOT EXISTS Paquet (
    id_paquet INT AUTO_INCREMENT PRIMARY KEY,
    numero_postal VARCHAR(50) NOT NULL UNIQUE,
    nom_destinataire VARCHAR(100) NOT NULL,
    prenom_destinataire VARCHAR(100) NOT NULL,
    adresse_destinataire VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    date_livraison DATE NOT NULL,
    ordre_route INT DEFAULT NULL,
    statut ENUM('Pas encore livré', 'En cours de livraison', 'Livré') NOT NULL DEFAULT 'Pas encore livré',
    id_employe_createur INT NOT NULL,
    id_employe_livreur INT DEFAULT NULL,
    id_route INT DEFAULT NULL,
    date_heure_livraison DATETIME DEFAULT NULL,
    FOREIGN KEY (id_employe_createur) REFERENCES Employe(id_employe)
        ON DELETE RESTRICT,
    FOREIGN KEY (id_employe_livreur) REFERENCES Employe(id_employe)
        ON DELETE SET NULL,
    FOREIGN KEY (id_route) REFERENCES RouteLivraison(id_route)
        ON DELETE SET NULL,
    INDEX idx_date_livraison (date_livraison),
    INDEX idx_statut (statut),
    INDEX idx_id_employe_livreur (id_employe_livreur),
    INDEX idx_numero_postal (numero_postal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de test - Employés
INSERT INTO Employe (nom, prenom, email, motDePasse, estLivreur) VALUES
('Dupont', 'Élie', 'elie.dupont@packetdelivery.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE),
('Colette', 'Célère', 'celere.colette@packetdelivery.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE),
('Célère', 'Jacques', 'jacques.celere@packetdelivery.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('Bernard', 'Luc', 'luc.bernard@packetdelivery.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('Martin', 'Alice', 'alice.martin@packetdelivery.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Données de test - Paquets (mot de passe: password pour tous)
INSERT INTO Paquet (numero_postal, nom_destinataire, prenom_destinataire, adresse_destinataire, latitude, longitude, date_livraison, ordre_route, statut, id_employe_createur, id_employe_livreur) VALUES
('230850', 'Girard', 'Marie', 'Rue du Mont-Blanc 12, 1201 Genève', 46.2044, 6.1432, DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL, 'Pas encore livré', 1, NULL),
('230431', 'Weber', 'Thomas', 'Rue de la Confédération 5, 1204 Genève', 46.2046, 6.1469, CURDATE(), 1, 'Livré', 1, 3),
('230432', 'Rossi', 'Laura', 'Avenue de la Gare 20, 1201 Genève', 46.2100, 6.1400, CURDATE(), 2, 'Livré', 1, 3),
('240001', 'Muller', 'Pierre', 'Rue de la Cité 15, 1204 Genève', 46.2035, 6.1490, DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL, 'Pas encore livré', 2, NULL),
('240006', 'Dubois', 'Sophie', 'Boulevard des Philosophes 8, 1205 Genève', 46.1918, 6.1423, DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL, 'Pas encore livré', 2, NULL),
('240012', 'Klein', 'Hans', 'Rue des Alpes 25, 1204 Genève', 46.2055, 6.1445, DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL, 'Pas encore livré', 1, NULL);