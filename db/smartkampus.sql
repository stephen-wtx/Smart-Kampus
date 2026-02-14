-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 04, 2026 at 01:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartkampus`
--

-- --------------------------------------------------------

--
-- Table structure for table `calendario_academico`
--

CREATE TABLE `calendario_academico` (
  `id` int(11) NOT NULL,
  `nome_ficheiro` varchar(255) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `data_publicacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendario_academico`
--

INSERT INTO `calendario_academico` (`id`, `nome_ficheiro`, `caminho`, `data_publicacao`) VALUES
(3, 'Diagrama de atividade - Visualizar Salas Livres.pdf', '1769944837_Diagrama de atividade - Visualizar Salas Livres.pdf', '2026-02-01 13:20:37');

-- --------------------------------------------------------

--
-- Table structure for table `exames`
--

CREATE TABLE `exames` (
  `id` int(11) NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `data` date NOT NULL,
  `curso` varchar(100) NOT NULL,
  `ano` varchar(5) NOT NULL,
  `semestre` varchar(5) NOT NULL,
  `disciplina` varchar(100) NOT NULL,
  `turno` enum('Diurno','Noturno') NOT NULL,
  `sala` varchar(100) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `duracao` int(11) NOT NULL COMMENT 'Duração em minutos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exames`
--

INSERT INTO `exames` (`id`, `dia_semana`, `data`, `curso`, `ano`, `semestre`, `disciplina`, `turno`, `sala`, `hora_inicio`, `hora_fim`, `duracao`, `created_at`) VALUES
(6, 'Quarta-feira', '2026-02-04', 'Economia e Gestão', '2º', 'I', 'Contabilidade II', 'Diurno', 'Cipriano Parite 2', '10:00:00', '11:00:00', 60, '2026-02-03 00:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `curso` varchar(100) NOT NULL,
  `ano` varchar(5) NOT NULL,
  `semestre` enum('I','II') NOT NULL,
  `disciplina` varchar(100) NOT NULL,
  `turno` enum('Diurno','Noturno') NOT NULL,
  `sala` varchar(100) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `criado_por` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `horarios`
--

INSERT INTO `horarios` (`id`, `dia_semana`, `curso`, `ano`, `semestre`, `disciplina`, `turno`, `sala`, `hora_inicio`, `hora_fim`, `criado_por`, `criado_em`) VALUES
(20, 'Quarta-feira', 'Economia e Gestão', '2', 'I', 'Introducao a Informatica', 'Diurno', 'Cipriano Parite 1', '09:00:00', '11:00:00', NULL, '2026-02-02 16:40:01'),
(21, 'Sexta-feira', 'Meio Ambiente', '2', 'I', 'Matematica', 'Diurno', 'Laboratório de Línguas', '10:11:00', '11:11:00', 2, '2026-02-03 09:09:07');

-- --------------------------------------------------------

--
-- Table structure for table `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `docente_nome` varchar(100) NOT NULL,
  `curso` varchar(150) NOT NULL,
  `disciplina` varchar(150) NOT NULL,
  `turno` varchar(50) NOT NULL,
  `sala` varchar(100) NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `finalidade` varchar(255) DEFAULT NULL,
  `estado` enum('pendente','aprovada','rejeitada') DEFAULT 'pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salas`
--

CREATE TABLE `salas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `estado` enum('livre','ocupada') NOT NULL DEFAULT 'livre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salas`
--

INSERT INTO `salas` (`id`, `nome`, `estado`) VALUES
(1, 'Nelson Mandela 1', 'livre'),
(2, 'Nelson Mandela 2', 'livre'),
(3, 'Nkwame Nkrumah', 'livre'),
(4, 'Martin Luther King', 'livre'),
(5, 'Santo Agostinho', 'livre'),
(6, 'Dom Jaime Gonsalves', 'livre'),
(7, 'Josefina Bakhita 1', 'livre'),
(8, 'Josefina Bakhita 2', 'livre'),
(9, 'Blase Pascal', 'livre'),
(10, 'Sala de Informatica', 'livre'),
(11, 'Laboratorio de SIG', 'livre'),
(12, 'Cipriano Parite 1', 'livre'),
(13, 'Cipriano Parite 2', 'livre'),
(14, 'Laboratorio de Linguas', 'livre'),
(15, 'Sao Tomas de Aquino', 'livre'),
(16, 'Roberto Busa', 'livre'),
(17, 'Rosario Policarpo Napica', 'livre'),
(18, 'Beato Newman', 'livre'),
(19, 'Francisco de Assis', 'livre'),
(20, 'Sao Francisco de Victoria', 'livre'),
(21, 'Max Plank', 'livre'),
(22, 'Sala Magna 1', 'livre'),
(23, 'Sala Magna 2', 'livre');

-- --------------------------------------------------------

--
-- Table structure for table `testes`
--

CREATE TABLE `testes` (
  `id` int(11) NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `data` date NOT NULL,
  `curso` varchar(100) NOT NULL,
  `ano` varchar(5) NOT NULL,
  `semestre` varchar(5) NOT NULL,
  `disciplina` varchar(100) NOT NULL,
  `turno` enum('Diurno','Noturno') NOT NULL,
  `sala` varchar(100) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `duracao` int(11) NOT NULL COMMENT 'Duração em minutos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testes`
--

INSERT INTO `testes` (`id`, `dia_semana`, `data`, `curso`, `ano`, `semestre`, `disciplina`, `turno`, `sala`, `hora_inicio`, `hora_fim`, `duracao`, `created_at`) VALUES
(7, 'Quarta-feira', '2026-02-04', 'Economia e Gestão', '2º', 'I', 'Contabilidade I', 'Diurno', 'Francisco de Assis', '10:00:00', '11:00:00', 60, '2026-02-03 06:51:58'),
(8, 'Quinta-feira', '2026-02-05', 'Economia e Gestão', '2º', 'I', 'Gestao', 'Diurno', 'Francisco de Assis', '10:00:00', '11:00:00', 60, '2026-02-03 06:52:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `oauth_provider` varchar(20) NOT NULL,
  `oauth_uid` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('estudante','docente','admin') NOT NULL DEFAULT 'estudante',
  `picture` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `oauth_provider`, `oauth_uid`, `name`, `email`, `role`, `picture`, `created_at`, `last_login`) VALUES
(2, 'google', '116705155560157601791', 'Stefeen Eugénio', '706220195@ucm.ac.mz', 'estudante', 'https://lh3.googleusercontent.com/a/ACg8ocIuOlAqFH8k3U2iJm_NuY8IBwdlANX57FNFGtW70cEeFrnYPQ=s96-c', '2025-12-26 10:28:23', '2026-02-04 09:49:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendario_academico`
--
ALTER TABLE `calendario_academico`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exames`
--
ALTER TABLE `exames`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sala_dia` (`sala`,`dia_semana`),
  ADD KEY `idx_curso_ano` (`curso`,`ano`,`semestre`);

--
-- Indexes for table `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `testes`
--
ALTER TABLE `testes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sala_dia` (`sala`,`dia_semana`),
  ADD KEY `idx_curso_ano` (`curso`,`ano`,`semestre`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `email_2` (`email`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendario_academico`
--
ALTER TABLE `calendario_academico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exames`
--
ALTER TABLE `exames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `testes`
--
ALTER TABLE `testes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
