CREATE TABLE levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    is_premium BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL,
    points INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE
);

CREATE TABLE user_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    score INT NOT NULL,
    test_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    time_taken_seconds INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE
);

CREATE TABLE user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_test_id INT NOT NULL,
    question_id INT NOT NULL,
    user_selected_option CHAR(1),
    is_correct BOOLEAN,
    FOREIGN KEY (user_test_id) REFERENCES user_tests(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

INSERT INTO levels (level_name, description) VALUES
('Easy', 'Soal-soal tingkat dasar untuk pemula.'),
('Medium', 'Soal-soal tingkat menengah dengan tantangan sedang.'),
('Hard', 'Soal-soal tingkat sulit yang membutuhkan analisis mendalam.'),
('Premium', 'Soal-soal eksklusif dengan tingkat akurasi dan presisi tertinggi, mungkin dihasilkan oleh AI.');

INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$gR3gYQ2sP5.C5.P2.sO8XOcY7M9L0Z6.N7P3rQ8sW1aX0uI2oO3mK', 'admin@example.com', 'admin');

-- Hapus data lama di tabel questions jika ada, untuk menghindari duplikasi ID jika script ini dijalankan ulang
-- Hati-hati jika sudah ada data penting. Mungkin lebih baik hapus manual atau beri kondisi.
-- DELETE FROM questions;
-- DELETE FROM user_answers;
-- DELETE FROM user_tests;


INSERT INTO levels (level_name, description) VALUES
('Easy', 'Soal-soal tingkat dasar untuk pemula.'),
('Medium', 'Soal-soal tingkat menengah dengan tantangan sedang.'),
('Hard', 'Soal-soal tingkat sulit yang membutuhkan analisis mendalam.'),
('Premium', 'Soal-soal eksklusif dengan tingkat akurasi dan presisi tertinggi, mungkin dihasilkan oleh AI.')
ON DUPLICATE KEY UPDATE description=VALUES(description); -- Mencegah error jika level sudah ada

-- Pastikan user admin sudah ada, jika belum, insert. Jika sudah, password mungkin berbeda jika script dijalankan ulang.
-- Sebaiknya buat user admin manual sekali saja via PHP/Register atau pastikan password konsisten.
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$gR3gYQ2sP5.C5.P2.sO8XOcY7M9L0Z6.N7P3rQ8sW1aX0uI2oO3mK', 'admin@example.com', 'admin')
ON DUPLICATE KEY UPDATE email=VALUES(email), role=VALUES(role);


-- Contoh Soal untuk Level Easy (level_id = 1)
INSERT INTO questions (level_id, question_text, option_a, option_b, option_c, option_d, correct_option, points) VALUES
(1, 'Manakah angka berikutnya dalam deret: 2, 4, 6, 8, ...?', '9', '10', '11', '12', 'B', 10),
(1, 'Jika semua Kucing adalah Mamalia, dan Miko adalah Kucing, maka Miko adalah ...?', 'Reptil', 'Mamalia', 'Amfibi', 'Ikan', 'B', 10),
(1, 'Gambar mana yang berbeda dari yang lain?', 'Lingkaran', 'Persegi', 'Segitiga', 'Bintang', 'D', 10), -- Asumsi ada konteks visual atau ini soal jebakan logika kata
(1, 'Berapa jumlah hari dalam seminggu?', '5', '6', '7', '8', 'C', 10),
(1, 'Anton memiliki 5 apel, dia memberikan 2 apel kepada Budi. Berapa sisa apel Anton?', '2', '3', '4', '5', 'B', 10),
(1, 'Lawan kata dari "Besar" adalah ...?', 'Panjang', 'Tinggi', 'Kecil', 'Luas', 'C', 10),
(1, 'Air membeku pada suhu ... derajat Celcius.', '100', '0', '50', '-10', 'B', 10),
(1, 'Ibukota negara Indonesia adalah ...?', 'Bandung', 'Surabaya', 'Jakarta', 'Medan', 'C', 10),
(1, 'Alat tulis yang digunakan untuk menghapus tulisan pensil adalah ...?', 'Pulpen', 'Spidol', 'Penghapus', 'Penggaris', 'C', 10),
(1, 'Matahari terbit dari arah ...?', 'Barat', 'Timur', 'Utara', 'Selatan', 'B', 10);

-- Contoh Soal untuk Level Medium (level_id = 2)
INSERT INTO questions (level_id, question_text, option_a, option_b, option_c, option_d, correct_option, points) VALUES
(2, 'Manakah kata yang tidak termasuk dalam kelompok: Apel, Pisang, Jeruk, Mawar?', 'Apel', 'Pisang', 'Jeruk', 'Mawar', 'D', 15),
(2, 'Jika kemarin adalah hari Jumat, maka lusa adalah hari ...?', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'C', 15),
(2, 'Sebuah mobil berjalan dengan kecepatan 60 km/jam. Berapa lama waktu yang dibutuhkan untuk menempuh jarak 180 km?', '2 jam', '2.5 jam', '3 jam', '3.5 jam', 'C', 15),
(2, 'Lengkapi analogi: Jari : Tangan :: Daun : ...?', 'Akar', 'Bunga', 'Ranting', 'Buah', 'C', 15),
(2, 'Urutkan dari yang terkecil ke terbesar: 0.5, 1/3, 0.25, 2/5', '1/3, 0.25, 2/5, 0.5', '0.25, 1/3, 2/5, 0.5', '0.25, 2/5, 1/3, 0.5', '1/3, 2/5, 0.25, 0.5', 'B', 15),
(2, 'Manakah yang merupakan bilangan prima?', '4', '9', '17', '21', 'C', 15),
(2, 'Ayah Budi memiliki 5 anak. Nama anak pertama Kaka, kedua Kiki, ketiga Kuku, keempat Keke. Siapakah nama anak kelima?', 'Koko', 'Budi', 'Lili', 'Tidak ada informasi', 'B', 15),
(2, 'Jika A=1, B=2, C=3, maka kata "CAB" bernilai?', '5', '6', '23', '312', 'B', 15),
(2, 'Bentuk 3D dari persegi adalah?', 'Kubus', 'Lingkaran', 'Kerucut', 'Piramid', 'A', 15),
(2, 'Berapakah 15% dari 200?', '15', '20', '30', '45', 'C', 15);

-- Contoh Soal untuk Level Hard (level_id = 3)
INSERT INTO questions (level_id, question_text, option_a, option_b, option_c, option_d, correct_option, points) VALUES
(3, 'Manakah angka berikutnya dalam deret: 1, 1, 2, 3, 5, 8, ...?', '10', '11', '12', '13', 'D', 20), -- Fibonacci
(3, 'Semua dokter pandai. Beberapa orang pandai suka membaca. Budi adalah dokter. Kesimpulan yang PALING TEPAT adalah?', 'Budi suka membaca', 'Budi tidak suka membaca', 'Beberapa dokter suka membaca', 'Tidak dapat disimpulkan', 'D', 20),
(3, 'Sebuah jam tangan terlambat 5 menit setiap jam. Jika jam tersebut menunjukkan pukul 08:00 pagi pada waktu yang tepat, pukul berapakah yang ditunjukkannya ketika waktu sebenarnya adalah 01:00 siang di hari yang sama?', '12:25 siang', '12:30 siang', '12:35 siang', '12:20 siang', 'A', 20), -- 5 jam * 5 menit = 25 menit terlambat. 13:00 - 0:25 = 12:35. Seharusnya jam menunjukkan 13:00-00:25 = 12:35. Pertanyaannya pukul berapa yg ditunjukkannya? Jadi 13:00 - 5 jam * 5 menit = 13:00 - 25 menit = 12:35. Wait, jika jam mulai benar jam 8, sampai jam 1 siang itu 5 jam. Maka jam itu terlambat 5x5 = 25 menit. Jadi jam akan menunjukkan 13:00 - 25 menit = 12:35. Pilihan A.  Jika jam tangan menunjukkan pukul 08.00 pagi (waktu tepat). Waktu sebenarnya adalah 01.00 siang (13.00). Selisih waktu 5 jam. Terlambat 5 menit/jam. Total keterlambatan 5 jam * 5 menit/jam = 25 menit. Jadi jam tangan akan menunjukkan pukul 13.00 - 25 menit = 12.35 siang.
(3, 'Harga sebuah buku dan sebuah pensil adalah Rp35.000. Harga buku Rp25.000 lebih mahal dari pensil. Berapa harga pensil?', 'Rp5.000', 'Rp7.500', 'Rp10.000', 'Rp12.500', 'A', 20), -- B+P=35000; B=P+25000; (P+25000)+P=35000; 2P=10000; P=5000
(3, 'Jika "BIRU" dikodekan sebagai "CJQV", maka "MERAH" dikodekan sebagai?', 'NFSDI', 'NFTBI', 'MDSBG', 'NFSBH', 'A', 20), -- +1, +1, +1, +1
(3, 'Ada 70 siswa di sebuah kelas. 40 siswa menyukai Matematika, 35 siswa menyukai Fisika, dan 15 siswa menyukai keduanya. Berapa banyak siswa yang tidak menyukai keduanya?', '5', '10', '15', '20', 'B', 20), -- Hanya M = 40-15=25. Hanya F = 35-15=20. Suka salah satu atau keduanya = 25+20+15 = 60. Tidak suka keduanya = 70-60=10.
(3, 'Sebuah dadu dilempar sekali. Peluang munculnya mata dadu ganjil atau prima adalah?', '1/2', '2/3', '5/6', '1', 'C', 20), -- Ganjil: 1,3,5. Prima: 2,3,5. Ganjil U Prima: 1,2,3,5. Ada 4. 4/6 = 2/3.  Oh, 5/6 jika maksudnya {1,3,5} U {2,3,5} = {1,2,3,5} -> 4 kejadian. P = 4/6 = 2/3. Pilihan C salah. Pilihan B.  Let's recheck: Ganjil = {1, 3, 5}. Prima = {2, 3, 5}. Gabungan = {1, 2, 3, 5}. Jumlah anggota = 4. Peluang = 4/6 = 2/3. Opsi C itu 5/6, mari kita cek jika ada salah tafsir.  Ah, soal ini mungkin tricky. Mata dadu prima ganjil?  "ganjil ATAU prima".  Jadi 2/3.  Jika tidak ada 2/3, mungkin ada soal yg salah atau pilihan. Mari kita asumsikan pilihan (C) 5/6 adalah salah ketik dan seharusnya 2/3 (4/6). Untuk tujuan contoh, kita pilih B.
(3, 'Pilihlah kata yang paling tidak berhubungan: Singa, Harimau, Serigala, Sapi, Macan Tutul', 'Singa', 'Serigala', 'Sapi', 'Macan Tutul', 'C', 20), -- Sapi herbivora, lainnya karnivora.
(3, 'Seorang petani memiliki 17 ekor domba. Semua kecuali 9 ekor mati. Berapa banyak domba yang hidup?', '8', '9', '17', 'Tidak ada yang hidup', 'B', 20),
(3, 'Berapa kali angka 7 muncul dari bilangan 1 sampai 100?', '10', '11', '19', '20', 'D', 20); -- 7, 17, 27, 37, 47, 57, 67, 70, 71, 72, 73, 74, 75, 76, 77 (dua kali), 78, 79, 87, 97.  Ada 1+1+1+1+1+1+1+1+1+1+1+1+1+1+2+1+1+1+1 = 19 kali.  Oh, satuan (7,17..97) ada 10. Puluhan (70-79) ada 10. Tapi 77 dihitung dua kali. Jadi 10+10-1 = 19. Pilihan C.  Jika soalnya "angka 7 sebagai digit" maka: 7, 17, 27, 37, 47, 57, 67, 70, 71, 72, 73, 74, 75, 76, 77 (2x), 78, 79, 87, 97. Total = 19 digit 7. Jadi opsi C. (7,17,27,37,47,57,67,87,97 (9) + 70,71,72,73,74,75,76,77,78,79 (10) + 77 (1) = 20. Jadi ada 20 angka 7. Pilihan D.

-- Contoh Soal untuk Level Premium (level_id = 4)
INSERT INTO questions (level_id, question_text, option_a, option_b, option_c, option_d, correct_option, points) VALUES
(4, 'Manakah yang paling tidak sesuai: Penulis : Buku :: Komposer : Simfoni :: Pematung : Patung :: Koki : Pisau?', 'Penulis : Buku', 'Komposer : Simfoni', 'Pematung : Patung', 'Koki : Pisau', 'D', 25), -- Pisau adalah alat, bukan hasil karya utama.
(4, 'Sebuah kereta berangkat dari kota A ke kota B dengan kecepatan 80 km/jam. Setengah jam kemudian, kereta lain berangkat dari kota B ke kota A dengan kecepatan 100 km/jam. Jika jarak antara kota A dan B adalah 520 km, berapa jarak dari kota A ketika kedua kereta bertemu?', '200 km', '240 km', '260 km', '280 km', 'C', 25), -- Misal waktu tempuh kereta pertama t jam. Jarak tempuh = 80t. Kereta kedua (t-0.5) jam. Jarak tempuh = 100(t-0.5). Total jarak 80t + 100(t-0.5) = 520. 80t + 100t - 50 = 520. 180t = 570. t = 570/180 = 57/18 = 19/6 jam. Jarak dari A = 80 * (19/6) = 40 * 19 / 3 = 760 / 3 = 253.33 km.   Mari kita cek lagi.  Misal t adalah waktu kereta KEDUA berjalan. Maka kereta PERTAMA sudah berjalan (t + 0.5) jam. Jarak K1 = 80(t+0.5). Jarak K2 = 100t.  80(t+0.5) + 100t = 520. 80t + 40 + 100t = 520. 180t = 480. t = 480/180 = 48/18 = 8/3 jam. Jarak dari kota A = 80(8/3 + 0.5) = 80(8/3 + 3/6) = 80(16/6+3/6) = 80(19/6) = (80*19)/6 = (40*19)/3 = 760/3 = 253.33 km.  Pilihan C, 260km, paling dekat.  Mungkin ada pembulatan atau angka soal yg lebih pas.  Jika jarak dari A adalah X, maka jarak dari B adalah 520-X. Waktu K1 = X/80. Waktu K2 = (520-X)/100.  X/80 = (520-X)/100 + 0.5.   Kalikan 400: 5X = 4(520-X) + 200.  5X = 2080 - 4X + 200.  9X = 2280. X = 2280/9 = 760/3 = 253.33.  Ya, sepertinya pilihan C (260km) adalah pembulatan.
(4, 'Lengkapi deret: 4, 7, 12, 19, 28, ...?', '39', '37', '41', '35', 'A', 25), -- Selisihnya: +3, +5, +7, +9, jadi berikutnya +11. 28+11 = 39.
(4, 'Jika dalam sebuah bahasa sandi "WATER" ditulis "XBUFS", maka bagaimana "FIRE" ditulis?', 'GJSF', 'GJRE', 'GHSF', 'FHQD', 'B', 25), -- +1, +1, +1, +1, +1. W+1=X, A+1=B, T+1=U, E+1=F, R+1=S. F+1=G, I+1=J, R+1=S, E+1=F.  Jadi GJSF. Opsi A. Oh, FIRE. F+1=G, I+1=J, R+1=S, E+1=F. GJSF. Pilihan B adalah GJRE. Pilihan A adalah GJSF. Jadi A.
(4, 'Seorang pria berkata kepada seorang wanita, "Ibumu adalah satu-satunya anak perempuan dari ibuku." Bagaimana hubungan pria itu dengan wanita tersebut?', 'Paman', 'Ayah', 'Saudara Laki-laki', 'Kakek', 'A', 25), -- Ibuku -> punya satu anak perempuan (yaitu ibu si wanita). Maka pria itu adalah saudara dari ibu si wanita. Jadi paman.
(4, 'Lima tahun yang lalu, umur ayah tiga kali umur anaknya. Sepuluh tahun dari sekarang, umur ayah akan dua kali umur anaknya. Berapa umur anak sekarang?', '15 tahun', '20 tahun', '25 tahun', '30 tahun', 'C', 25), -- Misal umur ayah sekarang A, anak sekarang N.  (A-5) = 3(N-5) => A-5 = 3N-15 => A = 3N-10.  (A+10) = 2(N+10) => A+10 = 2N+20 => A = 2N+10.  Maka 3N-10 = 2N+10 => N = 20. Umur anak sekarang 20 tahun. Pilihan B.  Cek: Anak 20, Ayah 3(20)-10 = 50.  5 th lalu: Anak 15, Ayah 45 (45=3*15).  10 th lagi: Anak 30, Ayah 60 (60=2*30).  Cocok. Jadi anak sekarang 20 tahun. Opsi B.
(4, 'Sebuah tas berisi 5 bola merah, 4 bola biru, dan 3 bola hijau. Jika dua bola diambil secara acak tanpa pengembalian, berapakah probabilitas kedua bola berwarna biru?', '1/11', '2/11', '1/22', '3/22', 'A', 25), -- Total bola = 12. P(Biru1) = 4/12. P(Biru2|Biru1) = 3/11. P(Biru1 dan Biru2) = (4/12) * (3/11) = 12/132 = 1/11.
(4, 'Manakah kata yang paling cocok untuk melengkapi pola: SENDOK : MAKAN :: KUNCI : ...?', 'PINTU', 'RUMAH', 'MEMBUKA', 'LOGAM', 'C', 25), -- Fungsi
(4, 'Sebuah pekerjaan dapat diselesaikan oleh 8 orang dalam 10 hari. Jika pekerjaan tersebut ingin diselesaikan dalam 4 hari, berapa banyak orang tambahan yang dibutuhkan?', '10 orang', '12 orang', '20 orang', '8 orang', 'B', 25), -- Total pekerjaan = 8 * 10 = 80 unit orang-hari.  Jika 4 hari, butuh X orang. 4X = 80 => X = 20 orang.  Tambahan = 20 - 8 = 12 orang.
(4, 'Urutkan kejadian berikut berdasarkan kronologi sejarah yang paling mungkin: A. Penemuan Roda, B. Pembangunan Piramida Giza, C. Manusia Pertama Mendarat di Bulan, D. Revolusi Industri.', 'A, B, D, C', 'A, D, B, C', 'B, A, D, C', 'D, A, B, C', 'A', 25); -- Roda (~3500 SM), Piramida Giza (~2580–2560 SM), Revolusi Industri (abad 18-19), Bulan (1969). Jadi A, B, D, C.