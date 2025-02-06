-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 06 Feb 2025 pada 11.31
-- Versi server: 5.7.44-log
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panel_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `accounts`
--

CREATE TABLE `accounts` (
  `trojan_url_ws` text,
  `trojan_url_grpc` text,
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `protocol` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `expiration_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `vmess_url_tls` text,
  `vmess_url_non_tls` text,
  `vless_url_tls` text,
  `vless_url_non_tls` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cloudflare_captcha`
--

CREATE TABLE `cloudflare_captcha` (
  `id` int(11) NOT NULL,
  `site_key` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `cloudflare_captcha`
--

INSERT INTO `cloudflare_captcha` (`id`, `site_key`, `secret_key`, `created_at`, `updated_at`, `status`) VALUES
(1, '0x4AAAAAAA6_aKxZr9UmsChw', '0x4AAAAAAA6_aN22zo4MUWekWS8BUN2XA7A', '2025-01-31 15:49:36', '2025-02-06 03:30:02', 'nonaktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `duitku_key`
--

CREATE TABLE `duitku_key` (
  `id` int(11) NOT NULL,
  `merchant_id` varchar(255) DEFAULT NULL,
  `client_key` varchar(255) DEFAULT NULL,
  `server_key` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kupon`
--

CREATE TABLE `kupon` (
  `id` int(11) NOT NULL,
  `kode_kupon` varchar(255) NOT NULL,
  `saldo` int(11) NOT NULL,
  `status` enum('aktif','tidak aktif') DEFAULT 'aktif',
  `expired_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `kupon`
--

INSERT INTO `kupon` (`id`, `kode_kupon`, `saldo`, `status`, `expired_at`, `created_at`) VALUES
(3, 'KUPON100%', 10000, 'aktif', '2025-03-01 15:30:00', '2025-02-05 08:26:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `midtrans_key`
--

CREATE TABLE `midtrans_key` (
  `id` int(11) NOT NULL,
  `merchant_id` varchar(255) NOT NULL,
  `client_key` varchar(255) NOT NULL,
  `server_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `midtrans_key`
--

INSERT INTO `midtrans_key` (`id`, `merchant_id`, `client_key`, `server_key`, `created_at`, `updated_at`) VALUES
(1, 'G070021655', 'SB-Mid-client-DS71rRal61dqoFO812', 'SB-Mid-server-CbLE5J6zwLT0AOSBn7SrLb7D12', '2025-01-30 07:08:22', '2025-02-05 08:31:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `payment_gateways`
--

INSERT INTO `payment_gateways` (`id`, `name`, `status`) VALUES
(1, 'Midtrans', 'active'),
(2, 'Duitku', 'inactive');

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_kupon`
--

CREATE TABLE `riwayat_kupon` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kode_kupon` varchar(255) NOT NULL,
  `tanggal_penggunaan` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `riwayat_kupon`
--

INSERT INTO `riwayat_kupon` (`id`, `user_id`, `kode_kupon`, `tanggal_penggunaan`) VALUES
(1, 5, 'DISCOUNT10', '2025-02-04 13:36:38'),
(2, 5, 'DISCOUNT20', '2025-02-04 13:37:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `server_name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `ip_address` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `status` enum('Available','Unavailable') DEFAULT 'Available',
  `jumlah_akun_dibuat` int(11) DEFAULT '0',
  `jumlah_akun_maksimal` int(11) DEFAULT '70'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `order_id`, `amount`, `payment_method`, `status`, `created_at`) VALUES
(16, 5, 'ORDER-1738324628', 25000, NULL, 'completed', '2025-01-31 11:57:13'),
(17, 5, 'ORDER-1738325289', 20000, 'gopay', 'completed', '2025-01-31 12:08:09'),
(18, 5, 'ORDER-1738339170', 10000, NULL, 'pending', '2025-01-31 15:59:30'),
(19, 5, 'ORDER-1738340048', 100000, NULL, 'pending', '2025-01-31 16:14:09'),
(20, 5, 'ORDER-1738475985', 25000, NULL, 'pending', '2025-02-02 05:59:45'),
(21, 5, 'ORDER-1738476044', 50000, NULL, 'pending', '2025-02-02 06:00:44'),
(22, 5, 'ORDER-1738476096', 10000, NULL, 'pending', '2025-02-02 06:01:36'),
(23, 5, 'ORDER-1738476149', 20000, NULL, 'pending', '2025-02-02 06:02:29'),
(24, 5, 'ORDER-1738476216', 10000, 'bank_transfer', 'completed', '2025-02-02 06:03:36'),
(25, 5, 'ORDER-1738483870', 20000, 'bank_transfer', 'completed', '2025-02-02 08:11:10'),
(26, 5, 'ORDER-1738671628', 12000, 'gopay', 'completed', '2025-02-04 12:20:29'),
(27, 5, 'ORDER-1738671703', 12000, 'gopay', 'completed', '2025-02-04 12:21:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('user','admin') DEFAULT 'user',
  `telegram_id` varchar(50) DEFAULT NULL,
  `telegram_username` varchar(50) DEFAULT NULL,
  `two_step_enabled` tinyint(1) DEFAULT '0',
  `notification_status` tinyint(1) DEFAULT '1',
  `saldo` int(11) DEFAULT '0',
  `verification_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `role`, `telegram_id`, `telegram_username`, `two_step_enabled`, `notification_status`, `saldo`, `verification_code`, `is_verified`) VALUES
(2, 'admin', '$2y$10$C7MBwYU2E4JALjXoD0zuJ.7TPZnj1Yly.nrL6Ui5UwfSB.ByYMe9q', 'admin@example.com', '2025-01-15 08:18:59', 'admin', '', '', 0, 1, 90000, NULL, 0),
(5, 'andrax', '$2y$10$wmphBQW7ivfV3TbRVzzCN.mH9d8tXtlCFRGweN2da2Y//1eE1D5Uu', 'andraxv6001@gmail.com', '2025-01-20 11:10:38', 'user', '', '', 0, 1, 284000, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `server_id` (`server_id`);

--
-- Indeks untuk tabel `cloudflare_captcha`
--
ALTER TABLE `cloudflare_captcha`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `duitku_key`
--
ALTER TABLE `duitku_key`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kupon`
--
ALTER TABLE `kupon`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kupon` (`kode_kupon`);

--
-- Indeks untuk tabel `midtrans_key`
--
ALTER TABLE `midtrans_key`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `riwayat_kupon`
--
ALTER TABLE `riwayat_kupon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT untuk tabel `cloudflare_captcha`
--
ALTER TABLE `cloudflare_captcha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `duitku_key`
--
ALTER TABLE `duitku_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kupon`
--
ALTER TABLE `kupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `midtrans_key`
--
ALTER TABLE `midtrans_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `riwayat_kupon`
--
ALTER TABLE `riwayat_kupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `accounts_ibfk_2` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`);

--
-- Ketidakleluasaan untuk tabel `riwayat_kupon`
--
ALTER TABLE `riwayat_kupon`
  ADD CONSTRAINT `riwayat_kupon_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
