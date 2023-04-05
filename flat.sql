-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 05 avr. 2023 à 07:54
-- Version du serveur : 5.7.33
-- Version de PHP : 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `flat`
--

-- --------------------------------------------------------

--
-- Structure de la table `comptes`
--

CREATE TABLE `comptes` (
  `id_compte` int(2) NOT NULL,
  `nom_compte` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_compte` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_compte` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_compte` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass_compte` blob,
  `img_compte` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_compte` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comptes`
--

INSERT INTO `comptes` (`id_compte`, `nom_compte`, `prenom_compte`, `email_compte`, `login_compte`, `pass_compte`, `img_compte`, `statut_compte`) VALUES
(2, 'Guerbeau--Gicquel', 'Pierre', 'guerbeau-pierre@gmail.com', 'admin', 0x64303333653232616533343861656235363630666332313430616563333538353063346461393937, NULL, ''),
(3, 'Guerbeau', 'FranÃ§ois', 'pierre.guerbea@gmail.com', 'Grizzlywawa', 0x64303333653232616533343861656235363630666332313430616563333538353063346461393937, NULL, ''),
(4, 'tut', 'U', 'tutu@orange.fr', 'Coucou', 0x34656365326239623231323964313038313565663938396130306437623762323865626365373933, NULL, ''),
(5, 'Chirac', 'Jacques', 'j.chirac@gmail.com', 'labite', 0x36643836363130366139333835303564653366376563626438303061356366636566616637613930, NULL, ''),
(6, 'Gicquel', 'Sylvie', 'sylvie.gicquel@orange.fr', 'sylvie', 0x64303333653232616533343861656235363630666332313430616563333538353063346461393937, NULL, 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `contacts`
--

CREATE TABLE `contacts` (
  `id_contact` int(5) NOT NULL,
  `nom_contact` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_contact` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_contact` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_contact` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_contact` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contacts`
--

INSERT INTO `contacts` (`id_contact`, `nom_contact`, `prenom_contact`, `email_contact`, `message_contact`, `date_contact`) VALUES
(1, 'arno', 'arno', 'arno@sdzsdsdf.com', 'adfdf', '2023-03-16 15:39:16'),
(2, 'arno', 'arno', 'arno@sdzsdsdf.com', 'fefzvfzv', '2023-03-16 16:17:59'),
(3, 'arno', 'arno', 'arno@sdzsdsdf.com', 'fefzvfzv', '2023-03-16 16:23:35'),
(4, 'arno', 'arno', 'arno@sdzsdsdf.com', 'fefzvfzv', '2023-03-16 16:23:51'),
(5, 'arno', 'arno', 'arno@sdzsdsdf.com', 'fefzvfzv', '2023-03-16 16:23:53');

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
  `id_page` int(3) NOT NULL,
  `titre_page` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu_page` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_page` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_page` date NOT NULL,
  `visible` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `pages`
--

INSERT INTO `pages` (`id_page`, `titre_page`, `contenu_page`, `img_page`, `date_page`, `visible`) VALUES
(1, 'La vie, c\'est nul', '\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi non interdum sapien. Fusce dapibus dui augue, vitae eleifend urna consequat volutpat. Morbi ut molestie diam. Sed augue tellus, viverra ac mollis quis, bibendum vitae sapien. Praesent blandit purus at efficitur fringilla.', NULL, '2023-03-31', 1),
(3, 'Le vice, c\'est bien', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sodales, nibh a dignissim tristique, elit sapien congue odio, vel facilisis ipsum nisl vel mauris. Maecenas accumsan volutpat auctor. Sed vitae lacinia magna. Quisque iaculis eget mi eget varius. Fusce sit amet aliquet magna, dignissim consectetur dui. Phasellus id tempor turpis.</p>', NULL, '2023-03-31', 1),
(4, 'Le vit, c\'est cool', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id enim et arcu tristique condimentum. Donec id egestas augue, at consectetur massa. Quisque pellentesque purus diam, dapibus bibendum nibh suscipit non. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse in nisi felis. Curabitur quis tellus.</p>', NULL, '2023-03-31', 1),
(5, 'Les vis, c\'est pourri', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean elementum, elit at bibendum mattis, libero lectus aliquet eros, ut mollis lectus tortor et massa. Nam nec neque orci. Sed molestie augue egestas nibh suscipit, et feugiat est ornare. Sed a egestas quam, quis ornare orci. Morbi vestibulum mi et libero.</p>', NULL, '2023-03-31', 1);

-- --------------------------------------------------------

--
-- Structure de la table `rubriques`
--

CREATE TABLE `rubriques` (
  `id_rubrique` int(2) NOT NULL,
  `nom_rubrique` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre_rubrique` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lien_rubrique` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_rubrique` date NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `slider` tinyint(1) NOT NULL DEFAULT '0',
  `rang` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `slider`
--

CREATE TABLE `slider` (
  `id_slider` int(2) NOT NULL,
  `alt_slider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legende_slider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_slider` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comptes`
--
ALTER TABLE `comptes`
  ADD PRIMARY KEY (`id_compte`),
  ADD UNIQUE KEY `email_compte` (`email_compte`);

--
-- Index pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id_contact`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id_page`);

--
-- Index pour la table `rubriques`
--
ALTER TABLE `rubriques`
  ADD PRIMARY KEY (`id_rubrique`);

--
-- Index pour la table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id_slider`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comptes`
--
ALTER TABLE `comptes`
  MODIFY `id_compte` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id_contact` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
  MODIFY `id_page` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `rubriques`
--
ALTER TABLE `rubriques`
  MODIFY `id_rubrique` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `slider`
--
ALTER TABLE `slider`
  MODIFY `id_slider` int(2) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
