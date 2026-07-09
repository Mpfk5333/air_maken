-- air_maken.sql - Script SQL de création de la base de données et chargement initial
CREATE DATABASE IF NOT EXISTS `air_maken` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `air_maken`;

-- --------------------------------------------------------
-- Table `utilisateurs`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `telephone` VARCHAR(30) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `date_inscription` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `statut` ENUM('actif', 'bloque') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table `administrateurs`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `administrateurs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin', 'agent') DEFAULT 'agent',
  `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table `services`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(150) NOT NULL,
  `categorie` VARCHAR(50) NOT NULL, -- 'billetterie', 'hotel', 'vehicule', 'visa', 'assurance', 'voyage', 'evenement'
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `prix_indicatif` DECIMAL(10,2) DEFAULT NULL,
  `statut` ENUM('actif', 'inactif') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table `reservations`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_utilisateur` INT NOT NULL,
  `id_service` INT DEFAULT NULL,
  `type_service` VARCHAR(50) NOT NULL,
  `details` JSON NOT NULL,
  `date_demande` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE DEFAULT NULL,
  `statut` ENUM('en_attente', 'confirmee', 'refusee', 'annulee') DEFAULT 'en_attente',
  `motif_refus` TEXT DEFAULT NULL,
  `montant` DECIMAL(10,2) DEFAULT NULL,
  FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_service`) REFERENCES `services`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table `messages_contact`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages_contact` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(30) DEFAULT NULL,
  `sujet` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `date_envoi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `statut` ENUM('non_lu', 'lu', 'traite') DEFAULT 'non_lu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table `contenus`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contenus` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page` ENUM('accueil', 'apropos') NOT NULL,
  `section` VARCHAR(100) NOT NULL,
  `contenu` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- INSERTIONS DE TEST / DE DÉPART
-- --------------------------------------------------------

-- Compte client : client@airmaken.com / Password123!
INSERT INTO `utilisateurs` (`nom`, `prenom`, `email`, `telephone`, `mot_de_passe`, `statut`) VALUES
('Client', 'Test', 'client@airmaken.com', '+240 111 222 333', '$2y$10$DVR6YSUYyuh3zmt.XdEUjOdewL9wALzPVCy2Ntq2.IHtYkly47hri', 'actif');

-- Comptes admins: super_admin et agent / Password123!
INSERT INTO `administrateurs` (`nom`, `email`, `mot_de_passe`, `role`) VALUES
('Super Admin', 'admin@airmaken.com', '$2y$10$DVR6YSUYyuh3zmt.XdEUjOdewL9wALzPVCy2Ntq2.IHtYkly47hri', 'super_admin'),
('Agent Maken', 'agent@airmaken.com', '$2y$10$DVR6YSUYyuh3zmt.XdEUjOdewL9wALzPVCy2Ntq2.IHtYkly47hri', 'agent');

-- Services par défaut
INSERT INTO `services` (`nom`, `categorie`, `description`, `image`, `prix_indicatif`, `statut`) VALUES
('Billetterie Aérienne', 'billetterie', 'Réservation et émission de billets d\'avion pour tous vos vols nationaux et internationaux au meilleur tarif.', 'billetterie.jpg', NULL, 'actif'),
('Assistance Visa', 'visa', 'Accompagnement complet dans l\'obtention de vos visas d\'entrée et de sortie (préparation de dossier, rendez-vous).', 'visa.jpg', 75000.00, 'actif'),
('Réservation d\'Hôtels', 'hotel', 'Trouvez et réservez des hôtels confortables adaptés à votre budget dans le monde entier.', 'hotel.jpg', NULL, 'actif'),
('Location de Véhicules', 'vehicule', 'Mise à disposition de voitures de tourisme ou de véhicules tout-terrain avec ou sans chauffeur.', 'vehicule.jpg', 50000.00, 'actif'),
('Assurance Voyage', 'assurance', 'Couverture médicale et rapatriement pour voyager l\'esprit tranquille partout dans le monde.', 'assurance.jpg', 25000.00, 'actif'),
('Voyages Organisés', 'voyage', 'Séjours touristiques clés en main avec hébergement, excursions et guide pour découvrir de nouveaux horizons.', 'voyage.jpg', NULL, 'actif');

-- Contenus éditables par défaut (Accueil et À propos)
INSERT INTO `contenus` (`page`, `section`, `contenu`, `image`) VALUES
('accueil', 'hero_title', 'Votre destination commence avec nous', 'hero-bg.jpg'),
('accueil', 'hero_subtitle', 'Voyagez l\'esprit léger avec AIR MAKEN, votre agence de voyage de confiance en Guinée Équatoriale.', NULL),
('accueil', 'presentation', 'AIR MAKEN est une agence de voyages et de services touristiques basée en Guinée Équatoriale, spécialisée dans l\'organisation de déplacements nationaux et internationaux pour les particuliers, les entreprises et les institutions. Nous offrons une large gamme de services de haute qualité pour garantir la réussite de chacun de vos projets de voyage.', 'about-preview.jpg'),
('accueil', 'stats', '{"clients": "15k+", "experience": "10+", "destinations": "50+", "support": "24/7"}', NULL),
('apropos', 'histoire', 'Fondée avec la volonté de simplifier et de professionnaliser le secteur du voyage en Guinée Équatoriale, AIR MAKEN est devenue en quelques années un partenaire incontournable pour les voyageurs exigeants. Grâce à notre réseau de partenaires mondiaux et notre équipe dévouée, nous rendons le monde accessible à nos clients.', 'histoire.jpg'),
('apropos', 'mission', 'Offrir des services de voyage d\'excellence, alliant sécurité, confort, rapidité et tarifs compétitifs, afin de faire de chaque voyage une expérience inoubliable.', NULL),
('apropos', 'valeurs', 'Professionnalisme, Réactivité, Écoute client, Transparence et Engagement envers l\'excellence du service.', NULL);
