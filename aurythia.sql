-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 29 août 2026 à 12:25
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `aurythia`
--

-- --------------------------------------------------------

--
-- Structure de la table `bijou`
--

CREATE TABLE `bijou` (
  `idbij` char(5) NOT NULL,
  `description` varchar(100) NOT NULL,
  `prixvent` int(11) NOT NULL,
  `poids` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `composotion`
--

CREATE TABLE `composotion` (
  `idbij` varchar(5) NOT NULL,
  `idmet` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE `evaluation` (
  `idExp` char(3) NOT NULL,
  `idbij` varchar(5) NOT NULL,
  `finition` int(11) NOT NULL,
  `originalite` char(1) NOT NULL,
  `commentaire` varchar(200) NOT NULL,
  `dateEV` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `expert`
--

CREATE TABLE `expert` (
  `idExp` char(3) NOT NULL,
  `nomExp` varchar(50) NOT NULL,
  `mail` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `metal`
--

CREATE TABLE `metal` (
  `idmet` char(1) NOT NULL,
  `nomMet` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bijou`
--
ALTER TABLE `bijou`
  ADD PRIMARY KEY (`idbij`);

--
-- Index pour la table `composotion`
--
ALTER TABLE `composotion`
  ADD PRIMARY KEY (`idbij`,`idmet`),
  ADD KEY `idbij` (`idbij`,`idmet`),
  ADD KEY `idmet` (`idmet`);

--
-- Index pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`idExp`,`idbij`),
  ADD KEY `idExp` (`idExp`,`idbij`),
  ADD KEY `idbij` (`idbij`);

--
-- Index pour la table `expert`
--
ALTER TABLE `expert`
  ADD PRIMARY KEY (`idExp`);

--
-- Index pour la table `metal`
--
ALTER TABLE `metal`
  ADD PRIMARY KEY (`idmet`),
  ADD UNIQUE KEY `nomMet` (`nomMet`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `composotion`
--
ALTER TABLE `composotion`
  ADD CONSTRAINT `composotion_ibfk_1` FOREIGN KEY (`idbij`) REFERENCES `bijou` (`idbij`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `composotion_ibfk_2` FOREIGN KEY (`idmet`) REFERENCES `metal` (`idmet`);

--
-- Contraintes pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`idbij`) REFERENCES `composotion` (`idbij`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evaluation_ibfk_2` FOREIGN KEY (`idExp`) REFERENCES `expert` (`idExp`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
