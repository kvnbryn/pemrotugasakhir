    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level_name` (`level_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `levels` (`id`, `level_name`, `description`) VALUES
(1, 'Easy', 'Soal-soal tingkat dasar untuk pemula.'),
(2, 'Medium', 'Soal-soal tingkat menengah dengan tantangan sedang.'),
(3, 'Hard', 'Soal-soal tingkat sulit yang membutuhkan analisis mendalam.'),
(4, 'Premium', 'Soal-soal eksklusif dengan tingkat akurasi dan presisi tertinggi, mungkin dihasilkan oleh AI.')
ON DUPLICATE KEY UPDATE `level_name` = VALUES(`level_name`);

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`username`, `password`, `email`, `role`, `is_premium`) VALUES
('admin', '$2y$10$gR3gYQ2sP5.C5.P2.sO8XOcY7M9L0Z6.N7P3rQ8sW1aX0uI2oO3mK', 'admin@example.com', 'admin', 1)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL,
  `question_number` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `level_id` (`level_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `user_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `test_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_taken_seconds` int(11) DEFAULT NULL,
  `total_questions_attempted` int(11) NOT NULL DEFAULT 0,
  `correct_answers` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `level_id` (`level_id`),
  CONSTRAINT `user_tests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_tests_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `user_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_test_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_selected_option` char(1) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_test_id` (`user_test_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`user_test_id`) REFERENCES `user_tests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `questions`;

INSERT INTO `questions` (`level_id`, `question_number`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `points`) VALUES
(1, 1, 'Manakah angka berikutnya dalam deret: 2, 4, 6, 8, ...?', '9', '10', '11', '12', 'B', 10),
(1, 2, 'Jika semua Kucing adalah Mamalia, dan Miko adalah Kucing, maka Miko adalah ...?', 'Reptil', 'Mamalia', 'Amfibi', 'Ikan', 'B', 10),
(1, 3, 'Gambar mana yang berbeda dari yang lain?', 'Lingkaran', 'Persegi', 'Segitiga', 'Bintang', 'D', 10),
(1, 4, 'Berapa jumlah hari dalam seminggu?', '5', '6', '7', '8', 'C', 10),
(1, 5, 'Anton memiliki 5 apel, dia memberikan 2 apel kepada Budi. Berapa sisa apel Anton?', '2', '3', '4', '5', 'B', 10);

INSERT INTO `questions` (`level_id`, `question_number`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `points`) VALUES
(2, 1, 'Manakah kata yang tidak termasuk dalam kelompok: Apel, Pisang, Jeruk, Mawar?', 'Apel', 'Pisang', 'Jeruk', 'Mawar', 'D', 15),
(2, 2, 'Jika kemarin adalah hari Jumat, maka lusa adalah hari ...?', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'C', 15),
(2, 3, 'Sebuah mobil berjalan dengan kecepatan 60 km/jam. Berapa lama waktu yang dibutuhkan untuk menempuh jarak 180 km?', '2 jam', '2.5 jam', '3 jam', '3.5 jam', 'C', 15),
(2, 4, 'Lengkapi analogi: Jari : Tangan :: Daun : ...?', 'Akar', 'Bunga', 'Ranting', 'Buah', 'C', 15),
(2, 5, 'Ayah Budi memiliki 5 anak. Nama anak pertama Kaka, kedua Kiki, ketiga Kuku, keempat Keke. Siapakah nama anak kelima?', 'Koko', 'Budi', 'Lili', 'Tidak ada informasi', 'B', 15);

INSERT INTO `questions` (`level_id`, `question_number`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `points`) VALUES
(3, 1, 'Manakah angka berikutnya dalam deret: 1, 1, 2, 3, 5, 8, ...?', '10', '11', '12', '13', 'D', 20),
(3, 2, 'Semua dokter pandai. Beberapa orang pandai suka membaca. Budi adalah dokter. Kesimpulan yang PALING TEPAT adalah?', 'Budi suka membaca', 'Budi tidak suka membaca', 'Beberapa dokter suka membaca', 'Tidak dapat disimpulkan', 'D', 20),
(3, 3, 'Harga sebuah buku dan sebuah pensil adalah Rp35.000. Harga buku Rp25.000 lebih mahal dari pensil. Berapa harga pensil?', 'Rp5.000', 'Rp7.500', 'Rp10.000', 'Rp12.500', 'A', 20),
(3, 4, 'Ada 70 siswa di sebuah kelas. 40 siswa menyukai Matematika, 35 siswa menyukai Fisika, dan 15 siswa menyukai keduanya. Berapa banyak siswa yang tidak menyukai keduanya?', '5', '10', '15', '20', 'B', 20),
(3, 5, 'Seorang petani memiliki 17 ekor domba. Semua kecuali 9 ekor mati. Berapa banyak domba yang hidup?', '8', '9', '17', 'Tidak ada yang hidup', 'B', 20);

INSERT INTO `questions` (`level_id`, `question_number`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `points`) VALUES
(4, 1, 'Manakah yang paling tidak sesuai: Penulis : Buku :: Komposer : Simfoni :: Pematung : Patung :: Koki : Pisau?', 'Penulis : Buku', 'Komposer : Simfoni', 'Pematung : Patung', 'Koki : Pisau', 'D', 25),
(4, 2, 'Sebuah pekerjaan dapat diselesaikan oleh 8 orang dalam 10 hari. Jika pekerjaan tersebut ingin diselesaikan dalam 4 hari, berapa banyak orang tambahan yang dibutuhkan?', '10 orang', '12 orang', '20 orang', '8 orang', 'B', 25),
(4, 3, 'Lengkapi deret: 4, 7, 12, 19, 28, ...?', '39', '37', '41', '35', 'A', 25),
(4, 4, 'Seorang pria berkata kepada seorang wanita, "Ibumu adalah satu-satunya anak perempuan dari ibuku." Bagaimana hubungan pria itu dengan wanita tersebut?', 'Paman', 'Ayah', 'Saudara Laki-laki', 'Kakek', 'A', 25),
(4, 5, 'Urutkan kejadian berikut berdasarkan kronologi sejarah yang paling mungkin: A. Penemuan Roda, B. Pembangunan Piramida Giza, C. Manusia Pertama Mendarat di Bulan, D. Revolusi Industri.', 'A, B, D, C', 'A, D, B, C', 'B, A, D, C', 'D, A, B, C', 'A', 25);

COMMIT;
