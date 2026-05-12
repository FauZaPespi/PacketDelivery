-- 1. CRÉATION DES TABLES

CREATE TABLE Employe (
    id_employe INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    estLivreur BOOLEAN NOT NULL
);

CREATE TABLE RouteLivraison (
    id_route INT PRIMARY KEY AUTO_INCREMENT,
    dateRoute DATE NOT NULL,
    id_employe_livreur INT NOT NULL,
    CONSTRAINT fk_route_livreur FOREIGN KEY (id_employe_livreur) REFERENCES Employe(id_employe)
);

CREATE TABLE Paquet (
    numeroPostal VARCHAR(50) PRIMARY KEY,
    nomDestinataire VARCHAR(100) NOT NULL,
    prenomDestinataire VARCHAR(100) NOT NULL,
    adresseDestinataire VARCHAR(255) NOT NULL,
    latitudeAdresse DECIMAL(10, 8),
    longitudeAdresse DECIMAL(11, 8),
    dateLivraison DATE,
    ordreRouteLivraison INT,
    statutLivraison VARCHAR(50) NOT NULL,
    id_route INT,
    id_employe_createur INT NOT NULL,
    id_employe_livreur INT,
    CONSTRAINT fk_paquet_route FOREIGN KEY (id_route) REFERENCES RouteLivraison(id_route),
    CONSTRAINT fk_paquet_createur FOREIGN KEY (id_employe_createur) REFERENCES Employe(id_employe),
    CONSTRAINT fk_paquet_livreur FOREIGN KEY (id_employe_livreur) REFERENCES Employe(id_employe)
);

-- 2. INSERTION DES DONNÉES DE TEST

-- Ajout des employés (1 administratif, 2 livreurs)
INSERT INTO Employe (nom, prenom, email, motDePasse, estLivreur) VALUES
('Dupont', 'Jean', 'jean.dupont@entreprise.com', '$argon2id$v=19$m=65536,t=4,p=1$eFBaTnp1cXpBbTdsZXJnTA$kMmaCHNdh2bVVVob03gPh7+jLsjPvFqgEBbHAPvnaPc', FALSE),  -- id: 1 (Administratif) mdp: motDePasseJean
('Martin', 'Alice', 'alice.martin@entreprise.com', '$argon2id$v=19$m=65536,t=4,p=1$ZXRuS3h3d2RmU0tQMjMyYQ$1G1+upSPOc2ojMphkZx5i9cZDbiAWBKqBABmnEkkO4U', TRUE), -- id: 2 (Livreur) mdp: motDePasseAlice
('Bernard', 'Luc', 'luc.bernard@entreprise.com', '$argon2id$v=19$m=65536,t=4,p=1$N3ZRVXR3dGZKZmlvQUVVZA$vs1EfC10/QsZifZzZZ59H3uQyB1WZWulXKB0U8Ty6rc', TRUE);   -- id: 3 (Livreur) mdp: motDePasseLuc

-- Ajout des routes de livraison (assignées aux livreurs Alice et Luc)
INSERT INTO RouteLivraison (dateRoute, id_employe_livreur) VALUES
('2026-05-01', 2), -- id_route: 1 (assignée à Alice)
('2026-05-02', 3); -- id_route: 2 (assignée à Luc)

-- Ajout des paquets (Localisés dans le canton de Genève)
-- Les paquets sont créés par l'employé administratif (id 1)
INSERT INTO Paquet (
    numeroPostal, nomDestinataire, prenomDestinataire, adresseDestinataire, 
    latitudeAdresse, longitudeAdresse, dateLivraison, ordreRouteLivraison, 
    statutLivraison, id_route, id_employe_createur, id_employe_livreur
) VALUES
(
    'PKG-1201-CH', 'Bovet', 'Marc', 'Rue de la Faucille 2, 1201 Genève', 
    46.211512, 6.142834, '2026-05-01', 1, 
    'En transit', 1, 1, 2
),
(
    'PKG-1219-CH', 'Muller', 'Sophie', 'Avenue de Châtelaine 50, 1219 Vernier', 
    46.205389, 6.103175, '2026-05-01', 2, 
    'En transit', 1, 1, 2
),
(
    'PKG-1227-CH', 'Rousseau', 'Claude', 'Route de Veyrier 20, 1227 Carouge', 
    46.186123, 6.141567, '2026-05-02', 1, 
    'En préparation', 2, 1, 3
);