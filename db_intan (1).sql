-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Mar 2025 pada 15.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_intan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(5) NOT NULL,
  `task_id` int(10) NOT NULL,
  `description` varchar(30) NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `deadline` date NOT NULL,
  `priority` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `subtasks`
--

INSERT INTO `subtasks` (`id`, `task_id`, `description`, `completed`, `deadline`, `priority`) VALUES
(4, 11, 'subtask', 1, '2025-03-08', '3'),
(5, 13, 'subtask', 0, '2025-03-14', '1'),
(6, 13, 'subtask2', 0, '2025-03-19', '1'),
(7, 13, 'subtask3', 0, '2025-03-28', '3'),
(8, 13, 'berubah', 0, '2025-03-28', '1'),
(9, 15, 'subtask', 1, '2025-03-12', '3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `id` int(5) NOT NULL,
  `user_id` int(5) NOT NULL,
  `title` varchar(20) NOT NULL,
  `creat_at` int(15) NOT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `creat_at`, `status`) VALUES
(1, 1, 'tugas rumah', 0, 'Not Done'),
(2, 4, 'list makanan buka pu', 0, 'Not Done'),
(3, 4, 'apa', 0, 'Not Done'),
(4, 4, 'ppppp', 0, 'Not Done'),
(11, 2, 'task baru di edit', 0, 'Done'),
(13, 2, 'task', 0, 'Not Done'),
(14, 5, 'masak', 0, 'Done'),
(15, 5, 'makan', 0, 'Done');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'aprl', '$2y$10$SGMdYwA2loH8LS1iS4lwTO5zNHnzPw2nV.Nh0t2/CA.C3p3mpuoka'),
(3, 'iin', '$2y$10$SMe8i2fRpQdesnVR2K2CaO9GmTSKec4ak2Xv0mGjJU8yXrsmr3e/C'),
(4, 'titi', '$2y$10$IM6OYHUrfQlgJqF9vt04eu2ndJM5e/PkG1uodFO69.Y6CMb1hSn8u'),
(5, 'hanssnoturtype', '$2y$10$Ki5YHkpSXGgV/wvWeWEybusxffwaE0TcgNEjWx7wgvxmtsNA3WuDS');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
