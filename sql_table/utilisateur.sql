-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 12:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projetweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `ID_Utilisateur` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Prenom` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Date_inscription` date NOT NULL,
  `Statut` varchar(50) NOT NULL,
  `Photo_URL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`ID_Utilisateur`, `Nom`, `Prenom`, `Email`, `Mot_de_passe`, `Role`, `Date_inscription`, `Statut`, `Photo_URL`) VALUES
(10, 'Yassinn', 'Saadli', 'saadliwassieoff@gmail.com', '$2y$10$ReXL9Hp3f0CO/pQTQHhJqO1Xqnb36.HUp4fFLvGX1wG/Oq4EGtGIq', 'client', '2025-04-24', 'actif', ''),
(11, 'saadli', 'wassim', 'saadliwassieos@gmail.com', '$2y$10$Sbggon9mftlCkB9LA4PB6uLNYj13s1w42jdDhqv6gaUuePHBC/qVy', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppf'),
(12, 'hama', 'saadli', 'hama@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppp'),
(13, 'saadli', 'wassim', 'kingo@gmail.com', '$2y$10$066WwHZ2DIg4MSrCaARmR.JPJktlbbdNRJzpnGXVP6gQB.4GNtsfK', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppd'),
(14, 'Yassin', 'Saadli', 'saadliwassieossus@gmail.com', '$2y$10$RHuraCKghWuL5QB/r4hvUOjp4OO55gUTvvJWPHYyd59wXqF7b8hsi', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppd'),
(15, 'Yassin', 'Saadli', 'saadliwassissszeo@gmail.com', '$2y$10$XR8JHcUAyNqzQcTz63AtHeYJbJRdchrmtKRNTKsqRaelbjak9hIa2', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppdd'),
(16, 'kingg', 'kingo', 'Kings@gmail.com', '$2y$10$RziuJa/XLiX9elMDQlSbU.s13jdUakJN3v620gxTRQfysQNpG1jqS', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.pfffhpd'),
(17, 'was', 'saadli', 'was@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppds'),
(18, 'saadli', 'wassim', 'lol@gmail.com', 'Waassim2003@', 'client', '2025-04-24', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppp'),
(19, 'siga', 'saadli', 'siga@gmail.com', 'Waassim2004@', 'admin', '2025-04-24', 'actif', ''),
(21, 'Yassin', 'Saadli', 'saadliwassieoooo@gmail.com', 'Waassim2003@', 'client', '2025-04-25', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppp'),
(22, 'emnaaa', 'emna', 'emnaaaa@gmail.com', 'Emna2004@@@@@', 'client', '2025-04-25', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppp'),
(23, 'lwes', 'saadli', 'lwes@gmail.com', 'Waassim2003@', 'client', '2025-04-25', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppd'),
(24, 'emna', 'karray', 'emnakarray@gmail.com', 'Waassim2003@', 'admin', '2025-04-25', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppd'),
(25, 'emnaaaaa', 'karraaay', 'emnaka@gmail.com', 'Waassim2003@', 'client', '2025-04-25', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phppd'),
(26, 'Saadli', 'Waasim', 'saadliwassim@gmail.com', 'Waassim2003@', 'client', '2025-04-25', 'actif', 'http://localhost/prjweb2025/views/front/inscription.phpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`ID_Utilisateur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `ID_Utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
