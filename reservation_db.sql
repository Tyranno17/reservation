-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 19 mai 2023 à 21:28
-- Version du serveur : 10.10.2-MariaDB
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `reservation_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `vehicule_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `vehicule_id` (`vehicule_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `date_debut`, `date_fin`, `utilisateur_id`, `vehicule_id`, `service_id`) VALUES
(1, '2023-05-22 09:00:00', '2023-05-22 12:00:00', 3, 4, 1),
(2, '2023-05-25 09:00:00', '2023-05-25 17:00:00', 7, 2, 3),
(8, '2023-05-24 09:00:00', '2023-05-24 18:00:00', 1, 3, 1),
(7, '2023-05-23 09:00:00', '2023-05-23 16:00:00', 3, 2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `nom`, `description`) VALUES
(1, 'SIEGE', 'VÃ©hicule du SIEGE'),
(2, 'SOINS', 'VÃ©hicule des SOINS'),
(3, 'HUMNA', 'VÃ©hicule HUMNA');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `date_creation` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `prenom`, `nom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'Patrice', 'ROBERT', 'patrice.robert@escale-larochelle.com', '$2y$10$heDimpRfG87zJf.onF73uu5zr3lODp7geqQxuk7k9OpGyOtcCgSAK', 'user', '2023-04-03 21:10:10'),
(2, 'admin', 'admin', 'admin@gmail.com', '$2y$10$Ih963tCfxUziiNW1H3SBR.9lQePUtPU2WQKB73B4MQWJXfaNrk42u', 'admin', '2023-04-04 08:17:12'),
(3, 'toto', 'toto', 'toto@gmail.com', '$2y$10$ly.YPXlzbO21ojuMciKV3exFvm/SuNb/C36cD99O63.827gQoY/Ri', 'user', '2023-04-05 07:24:47'),
(5, 'titi', 'titi', 'titi@gmail.com', '$2y$10$WuS.QYonrYq5dU2V5ic3q.yjD28UJoAjcrrp1Zp4/HlicvTnMW6k.', 'user', '2023-04-12 09:59:10'),
(6, 'tonton', 'tonton', 'tonton@gmail.com', '$2y$10$SCVBPClo2lZtM7RESzewS.ZYD9SbesmkIMgraLo.joQegXpeRm3cm', 'user', '2023-04-12 10:16:48'),
(7, 'tata', 'tata', 'tata@gmail.com', '$2y$10$IDG1RUngA/oGko0zBZw2MuxOaYET663byh0kogKL0BMje5rSZUhvC', 'user', '2023-04-12 10:22:28'),
(8, 'tutu', 'tutu', 'tutu@gmail.com', '$2y$10$iD02T10uuf1KAnM.v9bsgeanOuNTTNYRP54QjrbWKIixwNGdHmyaO', 'user', '2023-04-12 11:34:20'),
(9, 'tete', 'tete', 'tete@gmail.com', '$2y$10$/9wUV1HbGsefOc.anXKN0OCugPiBWi3b9Pi8Pt64/ScZ4IBCdBxnm', 'user', '2023-04-12 11:42:00'),
(10, 'tyty', 'tyty', 'tyty@gmail.com', '$2y$10$uxhR0XbyFj.lDCbL1lLuWeq0zRnag3O7RjEiAJcxPcovtxEU.YG1m', 'user', '2023-04-12 11:46:31'),
(11, 'yoyo', 'yoyo', 'yoyo@gmail.com', '$2y$10$bZjzvB8inwR9Q4ql0UI5/OTYa1sLtLOok9fwGsuLL2XmWpod4vgie', 'user', '2023-04-12 13:22:34');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

DROP TABLE IF EXISTS `vehicule`;
CREATE TABLE IF NOT EXISTS `vehicule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marque` varchar(255) NOT NULL,
  `modele` varchar(255) NOT NULL,
  `annee` int(11) NOT NULL,
  `immatriculation` varchar(255) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `immatriculation` (`immatriculation`) USING HASH,
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `marque`, `modele`, `annee`, `immatriculation`, `service_id`) VALUES
(2, 'RENAULT', 'CLIO 3', 2021, 'AA-275-QA', 3),
(3, 'RENAULT', 'KANGOO', 2021, 'TT-573-QS', 1),
(4, 'RENAULT', 'TWINGO', 2023, 'TT-569-DA', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
