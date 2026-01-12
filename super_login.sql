-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12-Jan-2026 às 13:14
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `super_login`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_changed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `admins`
--

INSERT INTO `admins` (`id`, `username`, `nome`, `email`, `ultimo_login`, `password_hash`, `must_change_password`, `ativo`, `created_at`, `password_changed_at`) VALUES
(1, 'admin', 'admin', 'admin@gmail.com', '2026-01-10 22:12:36', '$2y$10$v5m.q1lMJujAihpOl1FGJushXjNLDBfJwVPL.ulsi7BZsg59HSIbq', 0, 1, '2026-01-09 23:44:39', '2026-01-09 23:53:23'),
(5, 'VCorreia', 'Victor', 'victor.a.correia@gmail.com', '2026-01-10 16:59:46', '$2y$10$f.8zBHJOKVZfWXKSA41VsuN9AoN5FtDkx4evsslCi3crcxsfiLt7q', 0, 1, '2026-01-10 14:20:12', '2026-01-10 14:21:31');

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin_tokens`
--

CREATE TABLE `admin_tokens` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `system_key` varchar(50) NOT NULL,
  `token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `admin_tokens`
--

INSERT INTO `admin_tokens` (`id`, `admin_id`, `system_key`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 1, 'worklog', 'be0bf1781af505af89627b8984af66c9f8cbb83ea2ccc6ee1e15d8c9884b22f2', '2026-01-10 00:43:51', 0, '2026-01-10 00:41:51'),
(2, 1, 'worklog', '9181e5ff2abc2c6d9af4c4bdd118b8d09c47911e5451f72056b471f1b1559b19', '2026-01-10 00:48:35', 0, '2026-01-10 00:46:35'),
(3, 1, 'worklog', '10d44913dde15e3e0a391bf87c86d6afb0c27b936c882c12d26bf5d3961a46b3', '2026-01-10 00:49:55', 0, '2026-01-10 00:47:55'),
(4, 1, 'worklog', '0eb34162d23da380969ddfa47fa58f2942d37ef96a29aa9d3e6b1d88b611af73', '2026-01-10 00:50:24', 0, '2026-01-10 00:48:24'),
(5, 1, 'worklog', '11f15635f19815d3ccf394095ef5828f517c7379d74f9b502a49a0a1185b3677', '2026-01-10 00:51:05', 0, '2026-01-10 00:49:05'),
(6, 1, 'worklog', '2a87b9e960e68fc7e420b85a079c11dd6d158038ed787e330de9c5693c8aa906', '2026-01-10 00:51:56', 0, '2026-01-10 00:49:56'),
(7, 1, 'worklog', '5ef052bd714877d61a69cf5d384971a10ad38dcc2efe81919821bec53c4d81d2', '2026-01-10 00:58:47', 0, '2026-01-10 00:56:47'),
(8, 1, 'worklog', '95f348f54ac5bb465ef402e9039d11c0d2d88ca8b208530c84c10efc35037a95', '2026-01-10 00:59:27', 0, '2026-01-10 00:57:27'),
(9, 1, 'worklog', 'bfe745972c7505b3d81b111e283d8207182d4fcb63cb74fe11a6ed13c8c5e2f1', '2026-01-10 01:06:26', 0, '2026-01-10 01:04:26'),
(10, 1, 'worklog', '0f57a5d3e2a2371aea86c621330391a29a466442b323cde435cd9ee7fd14e653', '2026-01-10 01:07:59', 0, '2026-01-10 01:05:59'),
(11, 1, 'worklog', '60fd90d02bfb51c296b83789113bbe9174b2b9d2488f84e8ffa65fd8d58793ab', '2026-01-10 01:10:56', 0, '2026-01-10 01:08:56'),
(12, 1, 'worklog', 'c1114d7e4bb794041790120441790829d8c115a70219c6a3eeaebb7e76f38cc7', '2026-01-10 01:11:45', 0, '2026-01-10 01:09:45'),
(13, 1, 'worklog', 'd3a8643b0491537fbc29cae73b0926e78bfaeb9bdbd2b640eab5d32ec1a0794a', '2026-01-10 01:15:29', 0, '2026-01-10 01:13:29'),
(14, 1, 'worklog', '1ceec2d2853380c6646d443478e0d1c7eb2e81f130e7700bbbd3abdfe70b2962', '2026-01-10 01:20:47', 0, '2026-01-10 01:18:47'),
(15, 1, 'worklog', 'aea2a2031695b07786a61b598edc2b27e425775ea462a1035c8a690cc8886305', '2026-01-10 01:22:54', 0, '2026-01-10 01:20:54'),
(16, 1, 'worklog', '9dc3be1e964267b4517baf78f8e097dd97d521234f51e713b6baf4a3c2531f61', '2026-01-10 01:29:17', 0, '2026-01-10 01:27:17'),
(17, 1, 'worklog', '9af59282c3be2993fe207644e3c017432666e3dad0e0f08f085e8920c6f3f82b', '2026-01-10 01:29:40', 0, '2026-01-10 01:27:40'),
(18, 1, 'worklog', '1dc53199c9426fd9666a076a08513af7172774df2c7cb5e91a43b71293483cab', '2026-01-10 01:29:56', 0, '2026-01-10 01:27:56'),
(19, 1, 'worklog', 'd8f5d7e609c64f0531e29d75ff07dcfba807921d4ca4e92a666df4949c3b7d1c', '2026-01-10 01:31:30', 0, '2026-01-10 01:29:30'),
(20, 1, 'worklog', '7782a8d3420c7f04d767eee850b159136db06a4aadab3a6a7638920a42452947', '2026-01-10 01:38:40', 0, '2026-01-10 01:36:40'),
(21, 1, 'worklog', '0e26758a8c99bfbe65a20a8d4ef94dc289ef9edb545237f01e3c1c522edf0b99', '2026-01-10 01:40:44', 0, '2026-01-10 01:38:44'),
(22, 1, 'worklog', 'bc3769e9e126d70416239d651e5076be91eb22af34abd161bb634655aad89655', '2026-01-10 01:41:10', 0, '2026-01-10 01:39:10'),
(23, 1, 'worklog', '740086fdb6ec05ee024efd058c66abc0a95139ca44139fe5a2ffccdd31f8c626', '2026-01-10 01:42:12', 0, '2026-01-10 01:40:12'),
(24, 1, 'worklog', 'b3aa0b4046e9fbf10a15fc394022bf7d3083345f6517b392e84796d44726f0b9', '2026-01-10 01:59:45', 0, '2026-01-10 01:57:45'),
(25, 1, 'worklog', 'c8ec4dc56b56bd690dd47475c88da52d364c6ab8908ac1a0f5a74be764a61364', '2026-01-10 10:27:59', 0, '2026-01-10 10:25:59'),
(26, 1, 'worklog', '7695c82a6ab23f2956589bc6daa9e2a1659563e9546fe5e401dba7424482e820', '2026-01-10 10:32:47', 0, '2026-01-10 10:30:47'),
(27, 1, 'worklog', '4c2cb53268edf1f98b2bc2781350d797b77f60dc53aa2d7aa62ec8ba4acde5cc', '2026-01-10 10:34:02', 1, '2026-01-10 10:32:02'),
(28, 1, 'worklog', '861160e3b0c361e1da804382d1d98f463acad0936d4c4e0bec124305782a0dc6', '2026-01-10 10:34:42', 1, '2026-01-10 10:32:42'),
(29, 1, 'sae', '95b976f01ce52baf81296095be0c7b32b2d4f8900bd45f2979043fd0d9ff3661', '2026-01-10 10:35:36', 0, '2026-01-10 10:33:36'),
(30, 1, 'crewgest', 'e995d06d6aa07c8e23353717df8567c8c6c7096872252c3e7643872ce6d4d1b0', '2026-01-10 10:36:00', 0, '2026-01-10 10:34:00'),
(31, 1, 'sae', '9ebad81ff4f911f8115063e1fa6c2e0a7d118c954cece8dc92cdf26e44de7d55', '2026-01-10 10:41:27', 0, '2026-01-10 10:39:27'),
(32, 1, 'sae', '87d10fec345914296462cba8e6cbebc6aaa2d25e15320413e60eb0ccf7c310ff', '2026-01-10 10:46:53', 1, '2026-01-10 10:44:53'),
(33, 1, 'crewgest', '202e6af6003b775950fdf2a0c271f3d7b28be53062312f2374b8976ddd3a4ce5', '2026-01-10 10:53:40', 0, '2026-01-10 10:51:40'),
(34, 1, 'crewgest', '67d770f4ceb76dbfa37c411a3c05683137b8401a574786185a6f2002b3ff750d', '2026-01-10 10:54:57', 1, '2026-01-10 10:52:57'),
(35, 1, 'crewgest', 'c122785f5841a53db785689b916ef5ac2f14c08dc8aab4983d4291efa97949fb', '2026-01-10 10:55:24', 1, '2026-01-10 10:53:24'),
(36, 5, 'crewgest', 'c2ceb7224be819c47543088f2d4f728ed9b7918bb645ba2f5c7baf4f0b61c6ea', '2026-01-10 14:23:43', 1, '2026-01-10 14:21:43'),
(37, 1, 'crewgest', '62791923b78dbfcc131f3c1ca6d1c5d32bd26ce5f8335ced3c611b7fecf5f72c', '2026-01-10 14:29:41', 1, '2026-01-10 14:27:41'),
(38, 5, 'sae', '95b98c3c6906fbf7a0b416f7ac4c2b7f5f2b29ec507effa03e5106fc8fa8ad36', '2026-01-10 17:01:55', 1, '2026-01-10 16:59:55'),
(39, 1, 'crewgest', '6b64c03591321561860fbe3d2735d4d5f9bab9aebcb3f53a9366e67c190209e6', '2026-01-10 22:14:47', 1, '2026-01-10 22:12:47'),
(40, 1, 'sae', 'c5a44038d6d64ca3da0eec1fdb283c0fb6daf88f986fefcbe055b8ed97a4998c', '2026-01-10 22:43:22', 1, '2026-01-10 22:41:22'),
(41, 1, 'worklog', 'ee20ec835d5ba08548b8a20579f9df83e154f40124190cd359f5406ac5ef4953', '2026-01-11 21:44:37', 1, '2026-01-11 21:42:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin_user_map`
--

CREATE TABLE `admin_user_map` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `system_key` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `admin_user_map`
--

INSERT INTO `admin_user_map` (`id`, `admin_id`, `system_key`, `user_id`, `created_at`) VALUES
(1, 1, 'worklog', 6, '2026-01-09 23:44:43'),
(2, 1, 'sae', 1, '2026-01-10 00:54:25'),
(3, 1, 'crewgest', 1, '2026-01-10 00:54:27'),
(4, 5, 'sae', 2, '2026-01-10 14:20:14'),
(5, 5, 'crewgest', 8, '2026-01-10 14:20:16'),
(6, 5, 'worklog', 2, '2026-01-10 14:20:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `admin_tokens`
--
ALTER TABLE `admin_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Índices para tabela `admin_user_map`
--
ALTER TABLE `admin_user_map`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_admin_system` (`admin_id`,`system_key`);

--
-- Índices para tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `admin_tokens`
--
ALTER TABLE `admin_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `admin_user_map`
--
ALTER TABLE `admin_user_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `admin_tokens`
--
ALTER TABLE `admin_tokens`
  ADD CONSTRAINT `admin_tokens_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `admin_user_map`
--
ALTER TABLE `admin_user_map`
  ADD CONSTRAINT `admin_user_map_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
