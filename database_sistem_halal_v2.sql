-- =====================================================================
-- DATABASE: Sistem Pendampingan dan Sertifikasi Halal LPH UINSA   (VERSI 2)
-- Kelompok 5 - Sistem Informasi UIN Sunan Ampel Surabaya
-- Target: MySQL 8 / MariaDB 10.5+ (XAMPP/Laragon), phpMyAdmin > Import
-- Isi: DDL 18 tabel, 3 trigger, dan data contoh (dummy) yang mengikuti desain Figma terbaru
--
-- PERINGATAN: baris DROP DATABASE di bawah MENGHAPUS database lama beserta isinya.
-- Akun contoh: semua password = Password123 (hanya untuk uji coba, WAJIB diganti).
--
-- RINGKASAN PERUBAHAN DARI VERSI 1 (berdasarkan frame Figma terbaru)
--  users              + kode_auditor, no_registrasi_auditor, keahlian, ketersediaan
--  pelaku_usaha       + nib, skala_usaha, kota_provinsi; nama_pemilik -> nama_penanggung_jawab
--  sinkronisasi_sihalal  status: Selesai / Sebagian / Gagal
--  pengajuan          + jenis_pengajuan; format nomor REG-2026-01284
--  produk             + merek_dagang, varian_produk, kemasan_berat_bersih, lokasi_produksi
--  bahan              + jenis_bahan; produsen -> merek_produsen
--  dokumen            status: Tersedia / Perlu Diperbarui / Tidak Lengkap / Belum Tersedia
--  pembayaran         + nominal, metode, nama pengirim, file bukti, 3 centang pemeriksaan
--  penugasan          + catatan_penugasan; 1 pengajuan = 1 penugasan; trigger cek peran
--  pemeriksaan        jenis_pemeriksaan dihapus (cukup putaran_ke); UNIQUE per putaran
--  catatan_perbaikan  + batas_waktu_revisi; status: Menunggu Perbaikan / Dalam Perbaikan / ...
--  dokumen_revisi     + id_dokumen_awal, revisi_ke, tanggal_diterima_lph
--  riwayat_revisi     + putaran_revisi
--  laporan            + hasil_laporan, hasil_pemeriksaan_ulang, waktu_disimpan
--  notifikasi         + status_pengiriman, waktu_dikirim, email_tujuan
-- =====================================================================
DROP DATABASE IF EXISTS sistem_halal_lph;
CREATE DATABASE sistem_halal_lph CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistem_halal_lph;

-- ---------------------------------------------------------------------
-- 1. USERS (Admin LPH dan Auditor; UMKM tidak punya akun)
-- ---------------------------------------------------------------------
CREATE TABLE users (
  id_user               INT AUTO_INCREMENT PRIMARY KEY,
  nama                  VARCHAR(100) NOT NULL COMMENT 'termasuk gelar, mis. Dr. Ahmad Fauzi, M.Si.',
  username              VARCHAR(50)  NOT NULL UNIQUE,
  email                 VARCHAR(100) NOT NULL UNIQUE,
  password_hash         VARCHAR(255) NOT NULL,
  role                  ENUM('Admin LPH','Auditor') NOT NULL,
  no_telepon            VARCHAR(20),
  kode_auditor          VARCHAR(20)  NULL UNIQUE COMMENT 'khusus Auditor, mis. AUD-001',
  no_registrasi_auditor VARCHAR(30)  NULL COMMENT 'khusus Auditor, mis. AH-2024-018',
  keahlian              VARCHAR(100) NULL COMMENT 'khusus Auditor, mis. Teknologi pangan',
  ketersediaan          ENUM('Tersedia','Tidak Tersedia') NULL COMMENT 'khusus Auditor',
  status_aktif          TINYINT(1) NOT NULL DEFAULT 1,
  created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pelaku_usaha (
  id_pelaku_usaha       INT AUTO_INCREMENT PRIMARY KEY,
  nama_usaha            VARCHAR(150) NOT NULL,
  nama_penanggung_jawab VARCHAR(100) NOT NULL,
  nib                   VARCHAR(20)  NULL COMMENT 'Nomor Induk Berusaha',
  skala_usaha           ENUM('Usaha Mikro','Usaha Kecil','Usaha Menengah') NULL,
  alamat                TEXT,
  kota_provinsi         VARCHAR(100) NULL COMMENT 'mis. Surabaya / Jawa Timur',
  no_telepon            VARCHAR(20),
  email                 VARCHAR(100),
  jenis_usaha           VARCHAR(100),
  created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE sinkronisasi_sihalal (
  id_sinkronisasi        INT AUTO_INCREMENT PRIMARY KEY,
  id_user                INT NOT NULL,
  waktu_sinkronisasi     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  jumlah_data_baru       INT NOT NULL DEFAULT 0,
  jumlah_data_diperbarui INT NOT NULL DEFAULT 0,
  jumlah_data_gagal      INT NOT NULL DEFAULT 0,
  status_sinkronisasi    ENUM('Selesai','Sebagian','Gagal') NOT NULL,
  pesan_kesalahan        TEXT,
  CONSTRAINT fk_sinkron_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;

CREATE TABLE pengajuan (
  id_pengajuan      INT AUTO_INCREMENT PRIMARY KEY,
  nomor_pengajuan   VARCHAR(30) NOT NULL UNIQUE COMMENT 'format REG-2026-01284',
  id_pelaku_usaha   INT NOT NULL,
  id_sinkronisasi   INT NULL,
  jenis_pengajuan   VARCHAR(50) NOT NULL DEFAULT 'Sertifikasi Halal Reguler',
  tanggal_pengajuan DATE NOT NULL,
  status_pengajuan  ENUM('Menunggu Verifikasi','Ditugaskan','Sedang Diperiksa','Perlu Perbaikan','Selesai')
                    NOT NULL DEFAULT 'Menunggu Verifikasi',
  sumber_data       ENUM('SiHalal','Manual') NOT NULL,
  created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pengajuan_pelaku  FOREIGN KEY (id_pelaku_usaha) REFERENCES pelaku_usaha(id_pelaku_usaha),
  CONSTRAINT fk_pengajuan_sinkron FOREIGN KEY (id_sinkronisasi) REFERENCES sinkronisasi_sihalal(id_sinkronisasi),
  INDEX idx_pengajuan_status (status_pengajuan)
) ENGINE=InnoDB;

CREATE TABLE produk (
  id_produk            INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan         INT NOT NULL,
  nama_produk          VARCHAR(150) NOT NULL,
  merek_dagang         VARCHAR(100) NULL,
  jenis_produk         VARCHAR(100),
  varian_produk        VARCHAR(100) NULL,
  kemasan_berat_bersih VARCHAR(100) NULL COMMENT 'mis. Standing pouch / 100 gram',
  lokasi_produksi      VARCHAR(255) NULL,
  deskripsi            TEXT,
  CONSTRAINT fk_produk_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bahan (
  id_bahan       INT AUTO_INCREMENT PRIMARY KEY,
  id_produk      INT NOT NULL,
  nama_bahan     VARCHAR(150) NOT NULL,
  merek_produsen VARCHAR(150) COMMENT 'kolom Merek / Produsen di Figma',
  jenis_bahan    ENUM('Bahan Baku','Bahan Penolong','Bahan Tambahan') NULL,
  keterangan     TEXT,
  CONSTRAINT fk_bahan_produk FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE dokumen (
  id_dokumen     INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan   INT NOT NULL,
  id_user        INT NULL COMMENT 'yang mengunggah',
  nama_dokumen   VARCHAR(150) NOT NULL,
  jenis_dokumen  VARCHAR(100) NOT NULL DEFAULT 'Persyaratan',
  nama_file      VARCHAR(255),
  path_file      VARCHAR(255),
  ukuran_file    INT COMMENT 'dalam KB',
  tanggal_unggah TIMESTAMP NULL,
  status_dokumen ENUM('Tersedia','Perlu Diperbarui','Tidak Lengkap','Belum Tersedia') NOT NULL DEFAULT 'Belum Tersedia',
  CONSTRAINT fk_dokumen_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan) ON DELETE CASCADE,
  CONSTRAINT fk_dokumen_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB COMMENT='Setiap pengajuan punya 6 baris dokumen persyaratan (dibuat saat pengajuan masuk)';

CREATE TABLE pembayaran (
  id_pembayaran        INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan         INT NOT NULL UNIQUE,
  id_user              INT NULL COMMENT 'Admin LPH yang memverifikasi',
  informasi_pembayaran VARCHAR(255) NULL COMMENT 'ringkasan di daftar, mis. Bukti transfer tersedia',
  nominal_tagihan      DECIMAL(12,2) NOT NULL DEFAULT 0,
  nominal_pembayaran   DECIMAL(12,2) NULL,
  metode_pembayaran    VARCHAR(50) NULL COMMENT 'mis. Transfer bank manual',
  nama_pengirim        VARCHAR(100) NULL,
  tanggal_pembayaran   DATETIME NULL,
  nama_file_bukti      VARCHAR(255) NULL,
  path_file_bukti      VARCHAR(255) NULL,
  ukuran_file_bukti    INT NULL COMMENT 'dalam KB',
  cek_nominal_sesuai   TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'centang: Nominal pembayaran sesuai tagihan',
  cek_identitas_sesuai TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'centang: Identitas pengirim sesuai pengajuan',
  cek_bukti_terbaca    TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'centang: Bukti pembayaran terbaca dengan jelas',
  status_pembayaran    ENUM('Terverifikasi','Belum Terverifikasi') NOT NULL DEFAULT 'Belum Terverifikasi',
  catatan_verifikasi   TEXT NULL,
  tanggal_verifikasi   DATETIME NULL COMMENT 'waktu pemeriksaan/verifikasi terakhir',
  CONSTRAINT fk_bayar_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan) ON DELETE CASCADE,
  CONSTRAINT fk_bayar_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;

CREATE TABLE penugasan (
  id_penugasan      INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan      INT NOT NULL,
  id_auditor        INT NOT NULL,
  id_admin          INT NOT NULL,
  tanggal_penugasan TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  catatan_penugasan TEXT NULL,
  status_penugasan  ENUM('Ditugaskan','Selesai') NOT NULL DEFAULT 'Ditugaskan',
  UNIQUE KEY uq_penugasan_pengajuan (id_pengajuan),
  CONSTRAINT fk_tugas_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan),
  CONSTRAINT fk_tugas_auditor FOREIGN KEY (id_auditor) REFERENCES users(id_user),
  CONSTRAINT fk_tugas_admin FOREIGN KEY (id_admin) REFERENCES users(id_user)
) ENGINE=InnoDB;

CREATE TABLE pemeriksaan (
  id_pemeriksaan      INT AUTO_INCREMENT PRIMARY KEY,
  id_penugasan        INT NOT NULL,
  putaran_ke          INT NOT NULL DEFAULT 1 COMMENT '1 = pemeriksaan awal, 2 dst = pemeriksaan ulang',
  tanggal_pemeriksaan DATE NULL,
  catatan_pemeriksaan TEXT,
  hasil_pemeriksaan   ENUM('Sesuai','Tidak Sesuai') NULL COMMENT 'di Figma pemeriksaan ulang memakai label Belum Sesuai',
  status_pemeriksaan  ENUM('Draf','Final') NOT NULL DEFAULT 'Draf',
  UNIQUE KEY uq_pemeriksaan_putaran (id_penugasan, putaran_ke),
  CONSTRAINT fk_periksa_tugas FOREIGN KEY (id_penugasan) REFERENCES penugasan(id_penugasan)
) ENGINE=InnoDB;

CREATE TABLE checklist_pemeriksaan (
  id_checklist      INT AUTO_INCREMENT PRIMARY KEY,
  id_pemeriksaan    INT NOT NULL,
  kategori          ENUM('Dokumen','Data Produk dan Bahan') NOT NULL,
  butir_pemeriksaan VARCHAR(200) NOT NULL,
  hasil             ENUM('Sesuai','Tidak Sesuai') NULL COMMENT 'NULL = belum dicek',
  keterangan        TEXT,
  CONSTRAINT fk_check_periksa FOREIGN KEY (id_pemeriksaan) REFERENCES pemeriksaan(id_pemeriksaan) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE catatan_perbaikan (
  id_catatan             INT AUTO_INCREMENT PRIMARY KEY,
  id_pemeriksaan         INT NOT NULL,
  kategori_temuan        VARCHAR(100) NOT NULL COMMENT 'mis. Bahan dan komposisi, Proses produksi, Dokumen pendukung',
  detail_ketidaksesuaian TEXT NOT NULL,
  instruksi_perbaikan    TEXT NOT NULL,
  tanggal_pencatatan     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  batas_waktu_revisi     DATE NULL COMMENT 'Batas pengiriman dokumen revisi',
  status_tindak_lanjut   ENUM('Menunggu Perbaikan','Dalam Perbaikan','Revisi Diterima','Sudah Sesuai') NOT NULL DEFAULT 'Menunggu Perbaikan',
  CONSTRAINT fk_catatan_periksa FOREIGN KEY (id_pemeriksaan) REFERENCES pemeriksaan(id_pemeriksaan)
) ENGINE=InnoDB;

CREATE TABLE dokumen_revisi (
  id_dokumen_revisi     INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan          INT NOT NULL,
  id_catatan            INT NULL,
  id_dokumen_awal       INT NULL COMMENT 'dokumen awal yang direvisi (tetap tersimpan sebagai arsip)',
  id_user               INT NULL COMMENT 'Auditor yang memasukkan',
  revisi_ke             TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Revisi 01, 02, dst',
  nama_dokumen          VARCHAR(150) NOT NULL,
  nama_file             VARCHAR(255),
  path_file             VARCHAR(255),
  ukuran_file           INT COMMENT 'dalam KB',
  keterangan_revisi     TEXT,
  tanggal_diterima_lph  DATETIME NULL COMMENT 'waktu dokumen diterima LPH dari pelaku usaha',
  tanggal_unggah        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status_dokumen_revisi ENUM('Menunggu Pemeriksaan','Sudah Diperiksa','Perlu Perbaikan Kembali')
                        NOT NULL DEFAULT 'Menunggu Pemeriksaan',
  CONSTRAINT fk_drev_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan),
  CONSTRAINT fk_drev_catatan FOREIGN KEY (id_catatan) REFERENCES catatan_perbaikan(id_catatan),
  CONSTRAINT fk_drev_dokumen_awal FOREIGN KEY (id_dokumen_awal) REFERENCES dokumen(id_dokumen),
  CONSTRAINT fk_drev_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;

CREATE TABLE riwayat_revisi (
  id_riwayat        INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan      INT NOT NULL,
  id_catatan        INT NULL,
  id_dokumen_revisi INT NULL,
  id_pemeriksaan    INT NULL,
  id_user           INT NOT NULL,
  putaran_revisi    TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Linimasa Revisi 01, 02, dst',
  jenis_entri       ENUM('Catatan Perbaikan','Dokumen Revisi','Pemeriksaan Ulang','Catatan Tambahan') NOT NULL,
  ringkasan         TEXT,
  status_tahap      VARCHAR(50) COMMENT 'mis. Perbaikan diminta, Diterima dan disimpan, Sesuai',
  waktu_dicatat     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rrev_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan),
  CONSTRAINT fk_rrev_catatan FOREIGN KEY (id_catatan) REFERENCES catatan_perbaikan(id_catatan),
  CONSTRAINT fk_rrev_drev FOREIGN KEY (id_dokumen_revisi) REFERENCES dokumen_revisi(id_dokumen_revisi),
  CONSTRAINT fk_rrev_periksa FOREIGN KEY (id_pemeriksaan) REFERENCES pemeriksaan(id_pemeriksaan),
  CONSTRAINT fk_rrev_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB COMMENT='Append-only (NF-08): dijaga trigger di bagian bawah';

CREATE TABLE riwayat_status (
  id_riwayat_status INT AUTO_INCREMENT PRIMARY KEY,
  id_pengajuan      INT NOT NULL,
  id_user           INT NULL,
  status_lama       ENUM('Menunggu Verifikasi','Ditugaskan','Sedang Diperiksa','Perlu Perbaikan','Selesai') NULL,
  status_baru       ENUM('Menunggu Verifikasi','Ditugaskan','Sedang Diperiksa','Perlu Perbaikan','Selesai') NOT NULL,
  waktu_perubahan   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  keterangan        VARCHAR(255),
  CONSTRAINT fk_rstat_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan),
  CONSTRAINT fk_rstat_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;

CREATE TABLE laporan (
  id_laporan              INT AUTO_INCREMENT PRIMARY KEY,
  nomor_laporan           VARCHAR(30) NOT NULL UNIQUE COMMENT 'format LHP-2026-0019',
  id_pengajuan            INT NOT NULL UNIQUE,
  id_auditor              INT NOT NULL COMMENT 'Auditor yang menyimpan laporan',
  tanggal_pemeriksaan     DATE NULL COMMENT 'tanggal pemeriksaan awal',
  tanggal_laporan         DATE NOT NULL,
  ringkasan_hasil         TEXT,
  temuan                  TEXT,
  kesimpulan              TEXT,
  hasil_pemeriksaan_ulang TEXT NULL,
  hasil_laporan           ENUM('Selesai','Perlu Tindak Lanjut') NOT NULL DEFAULT 'Selesai',
  status_laporan          ENUM('Draf','Tersimpan') NOT NULL DEFAULT 'Draf',
  waktu_disimpan          DATETIME NULL,
  CONSTRAINT fk_lap_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan),
  CONSTRAINT fk_lap_auditor FOREIGN KEY (id_auditor) REFERENCES users(id_user)
) ENGINE=InnoDB;

CREATE TABLE notifikasi (
  id_notifikasi     INT AUTO_INCREMENT PRIMARY KEY,
  id_penerima       INT NOT NULL,
  id_pengajuan      INT NULL,
  kategori          ENUM('Penugasan Auditor','Perubahan Status Pemeriksaan','Catatan Perbaikan','Dokumen Revisi Diterima') NOT NULL,
  judul             VARCHAR(150) NOT NULL,
  isi               TEXT,
  waktu_dibuat      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sudah_dibaca      TINYINT(1) NOT NULL DEFAULT 0,
  waktu_dibaca      TIMESTAMP NULL,
  status_pengiriman ENUM('Terkirim','Gagal') NOT NULL DEFAULT 'Terkirim',
  waktu_dikirim     DATETIME NULL,
  email_tujuan      VARCHAR(100) NULL,
  CONSTRAINT fk_notif_penerima FOREIGN KEY (id_penerima) REFERENCES users(id_user),
  CONSTRAINT fk_notif_pengajuan FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan)
) ENGINE=InnoDB;

CREATE TABLE data_administrasi (
  id_data_admin      INT AUTO_INCREMENT PRIMARY KEY,
  id_user            INT NOT NULL,
  jenis_data         VARCHAR(100) NOT NULL,
  informasi_data     TEXT,
  tanggal_diperbarui TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_dadm_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- DATA CONTOH (dummy). Nama usaha dan nomor pengajuan mengikuti frame Figma;
-- tanggal dan status dibuat saling konsisten (tidak persis sama dengan Figma,
-- karena data contoh di Figma sendiri tidak konsisten antar halaman).
-- Semua password akun = Password123 (ganti sebelum dipakai sungguhan).
-- ---------------------------------------------------------------------
INSERT INTO users (id_user,nama,username,email,password_hash,role,no_telepon,kode_auditor,no_registrasi_auditor,keahlian,ketersediaan) VALUES
(1,'Nur Aisyah','admin.lph','admin@example.com','$2y$12$1aF5A88P7dEHKRtjdFz//uRMWMoXfSTXPp2dd3IBLxVGAwlUPgZc2','Admin LPH','081200000001',NULL,NULL,NULL,NULL),
(2,'Dwi Rahmawati','admin.dwi','dwi.rahmawati@example.com','$2y$12$1aF5A88P7dEHKRtjdFz//uRMWMoXfSTXPp2dd3IBLxVGAwlUPgZc2','Admin LPH','081200000002',NULL,NULL,NULL,NULL),
(3,'Dr. Ahmad Fauzi, M.Si.','auditor.fauzi','ahmad.fauzi@example.com','$2y$12$1aF5A88P7dEHKRtjdFz//uRMWMoXfSTXPp2dd3IBLxVGAwlUPgZc2','Auditor','081200000003','AUD-001','AH-2024-018','Teknologi pangan','Tersedia'),
(4,'Siti Aminah, M.Si.','auditor.siti','siti.aminah@example.com','$2y$12$1aF5A88P7dEHKRtjdFz//uRMWMoXfSTXPp2dd3IBLxVGAwlUPgZc2','Auditor','081200000004','AUD-002','AH-2024-021','Keamanan pangan','Tersedia'),
(5,'Nur Aini, S.T.P., M.Si.','auditor.nuraini','nur.aini@example.com','$2y$12$1aF5A88P7dEHKRtjdFz//uRMWMoXfSTXPp2dd3IBLxVGAwlUPgZc2','Auditor','081200000005','AUD-003','AH-2024-025','Pengolahan pangan','Tersedia'),
(6,'H. M. Yusuf, M.Si.','auditor.yusuf','yusuf@example.com','$2y$12$1aF5A88P7dEHKRtjdFz//uRMWMoXfSTXPp2dd3IBLxVGAwlUPgZc2','Auditor','081200000006','AUD-004','AH-2024-030','Rantai pasok bahan halal','Tidak Tersedia');

INSERT INTO pelaku_usaha (id_pelaku_usaha,nama_usaha,nama_penanggung_jawab,nib,skala_usaha,alamat,kota_provinsi,no_telepon,email,jenis_usaha) VALUES
(1,'CV Berkah Pangan Nusantara','Ahmad Fauzan','9120104567890','Usaha Kecil','Jl. Jemursari No. 45, Wonocolo','Surabaya / Jawa Timur','0812-3456-7890','berkahpangan.sby@example.com','Makanan Olahan'),
(2,'UD Sari Rasa Surabaya','Rina Kusuma','9120204561234','Usaha Mikro','Jl. Rungkut Asri No. 5, Rungkut','Surabaya / Jawa Timur','0812-1111-2201','sarirasa@example.com','Bumbu dan Saus'),
(3,'PT Amanah Boga Indonesia','Hendra Wijaya','9120304567788','Usaha Menengah','Jl. Gubeng Kertajaya No. 8, Gubeng','Surabaya / Jawa Timur','0812-1111-2202','amanahboga@example.com','Bakery'),
(4,'Dapur Bu Zahra','Zahra Fitriani','9120404561122','Usaha Mikro','Jl. Ketintang No. 20, Gayungan','Surabaya / Jawa Timur','0812-1111-2203','dapurzahra@example.com','Katering'),
(5,'CV Madu Murni Sejahtera','Bambang Santoso','9120504563344','Usaha Kecil','Jl. Raya Kenjeran No. 77, Bulak','Surabaya / Jawa Timur','0812-1111-2204','madumurni@example.com','Madu dan Olahan'),
(6,'UMK Rasa Rempah','Dewi Anggraini','9120604565566','Usaha Mikro','Jl. Wonokromo No. 12, Wonokromo','Surabaya / Jawa Timur','0812-1111-2205','rasarempah@example.com','Bumbu dan Saus'),
(7,'UD Barokah Jaya','Muhammad Barok','9120704567788','Usaha Kecil','Jl. Pahlawan No. 31, Krembangan','Surabaya / Jawa Timur','0812-1111-2206','barokahjaya@example.com','Makanan Ringan'),
(8,'Kopi Lereng Arjuno','Joko Lereng','9120804569900','Usaha Kecil','Jl. Raya Tretes No. 14, Prigen','Pasuruan / Jawa Timur','0812-1111-2207','lerengarjuno@example.com','Minuman'),
(9,'UD Sumber Rezeki','Siti Aminah','9120305678912','Usaha Mikro','Jl. Rungkut Kidul No. 18','Surabaya / Jawa Timur','0813-5678-9021','sumberrezeki.sby@example.com','Makanan Ringan');

INSERT INTO sinkronisasi_sihalal (id_sinkronisasi,id_user,waktu_sinkronisasi,jumlah_data_baru,jumlah_data_diperbarui,jumlah_data_gagal,status_sinkronisasi,pesan_kesalahan) VALUES
(1,1,'2026-11-04 09:00:00',4,0,0,'Selesai',NULL),
(2,1,'2026-11-12 09:00:00',4,1,1,'Sebagian','Satu data tidak lengkap, perlu ditinjau.'),
(3,2,'2026-11-20 10:30:00',0,0,0,'Gagal','Layanan SiHalal tidak merespons.');

INSERT INTO pengajuan (id_pengajuan,nomor_pengajuan,id_pelaku_usaha,id_sinkronisasi,tanggal_pengajuan,status_pengajuan,sumber_data) VALUES
(1,'REG-2026-01277',8,1,'2026-11-02','Selesai','SiHalal'),
(2,'REG-2026-01278',7,1,'2026-11-03','Perlu Perbaikan','SiHalal'),
(3,'REG-2026-01279',6,1,'2026-11-03','Menunggu Verifikasi','SiHalal'),
(4,'REG-2026-01280',5,1,'2026-11-03','Sedang Diperiksa','SiHalal'),
(5,'REG-2026-01281',4,2,'2026-11-10','Menunggu Verifikasi','SiHalal'),
(6,'REG-2026-01282',3,2,'2026-11-10','Selesai','SiHalal'),
(7,'REG-2026-01283',2,2,'2026-11-11','Ditugaskan','SiHalal'),
(8,'REG-2026-01284',1,2,'2026-11-11','Perlu Perbaikan','SiHalal'),
(9,'REG-2026-01285',9,NULL,'2026-11-20','Menunggu Verifikasi','Manual');

INSERT INTO produk (id_produk,id_pengajuan,nama_produk,merek_dagang,jenis_produk,varian_produk,kemasan_berat_bersih,lokasi_produksi,deskripsi) VALUES
(1,1,'Kopi Arabika Bubuk','Lereng Arjuno','Minuman','Original','Standing pouch / 250 gram','Jl. Raya Tretes No. 14, Prigen','Kopi arabika bubuk sangrai'),
(2,2,'Kerupuk Udang','Barokah','Makanan ringan','Udang','Plastik / 200 gram','Jl. Pahlawan No. 31, Surabaya','Kerupuk udang mentah siap goreng'),
(3,3,'Sambal Bawang','Rasa Rempah','Bumbu dan saus','Pedas','Botol / 150 ml','Jl. Wonokromo No. 12, Surabaya','Sambal bawang botol'),
(4,4,'Madu Murni','Madu Murni','Madu kemasan','Hutan','Botol / 250 ml','Jl. Raya Kenjeran No. 77, Surabaya','Madu murni kemasan botol'),
(5,5,'Katering Harian','Dapur Zahra','Katering','Nasi kotak','Kotak / 1 porsi','Jl. Ketintang No. 20, Surabaya','Paket nasi kotak harian'),
(6,6,'Roti dan Kue','Amanah Boga','Roti dan kue','Roti tawar','Plastik / 400 gram','Jl. Gubeng Kertajaya No. 8, Surabaya','Roti tawar dan kue kering'),
(7,7,'Bumbu Masak','Sari Rasa','Bumbu masak','Bumbu dasar','Sachet / 50 gram','Jl. Rungkut Asri No. 5, Surabaya','Bumbu masak instan'),
(8,8,'Keripik Pisang Original','Berkah Rasa','Makanan ringan siap konsumsi','Original','Standing pouch / 100 gram','Jl. Jemursari No. 45, Surabaya','Keripik pisang renyah dari pisang kepok pilihan, digoreng dengan minyak nabati dan dikemas dalam kemasan tertutup.'),
(9,9,'Kerupuk Bawang','Sumber Rasa','Makanan ringan','Bawang','Plastik / 150 gram','Jl. Rungkut Kidul No. 18, Surabaya','Kerupuk bawang renyah');

INSERT INTO bahan (id_bahan,id_produk,nama_bahan,merek_produsen,jenis_bahan,keterangan) VALUES
(1,1,'Biji kopi arabika','Petani lokal, Pasuruan','Bahan Baku','Bahan utama'),
(2,2,'Udang','Pemasok lokal, Sidoarjo','Bahan Baku','Bahan utama'),
(3,2,'Tepung tapioka','Rose Brand / PT Budi Starch','Bahan Baku','Bahan pengikat'),
(4,2,'Perisa udang','PT Aditif Pangan','Bahan Tambahan','Perlu bukti kehalalan'),
(5,3,'Cabai dan bawang','Pasar Induk','Bahan Baku','Bahan utama'),
(6,4,'Madu murni','Peternak lebah lokal','Bahan Baku','Bahan utama'),
(7,5,'Daging ayam','Supplier Ayam Segar','Bahan Baku','Bahan utama'),
(8,6,'Tepung terigu','PT Boga Pangan','Bahan Baku','Bahan utama'),
(9,6,'Margarin','PT Margarin Nusantara','Bahan Penolong','Perlu sertifikat halal yang masih berlaku'),
(10,7,'Rempah giling','Pasar Induk','Bahan Baku','Bahan utama'),
(11,8,'Pisang kepok','Petani lokal, Lumajang','Bahan Baku','Buah segar tanpa bahan tambahan'),
(12,8,'Minyak goreng sawit','Bimoli / PT Salim Ivomas','Bahan Penolong','Minyak nabati dalam kemasan'),
(13,8,'Gula pasir','Gulaku / PT Sugar Group','Bahan Tambahan','Gula kristal putih'),
(14,8,'Garam konsumsi','Dolphin / PT Susanti Megah','Bahan Tambahan','Garam beryodium'),
(15,9,'Tepung tapioka','Rose Brand / PT Budi Starch','Bahan Baku','Bahan utama'),
(16,9,'Bawang putih','Pemasok lokal Surabaya','Bahan Baku','Bawang putih segar'),
(17,9,'Garam konsumsi','Dolphin / PT Susanti Megah','Bahan Tambahan','Garam beryodium');

INSERT INTO dokumen (id_dokumen,id_pengajuan,id_user,nama_dokumen,jenis_dokumen,nama_file,path_file,ukuran_file,tanggal_unggah,status_dokumen) VALUES
(1,1,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01277.pdf','/uploads/01277/nib_01277.pdf',1200,'2026-11-02 10:00:00','Tersedia'),
(2,1,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01277.jpg','/uploads/01277/ktp_01277.jpg',420,'2026-11-02 10:00:00','Tersedia'),
(3,1,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01277.pdf','/uploads/01277/penyelia_01277.pdf',680,'2026-11-02 10:00:00','Tersedia'),
(4,1,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01277.pdf','/uploads/01277/manual_sjph_01277.pdf',2400,'2026-11-02 10:00:00','Tersedia'),
(5,1,1,'Daftar Produk','Persyaratan','daftar_produk_01277.pdf','/uploads/01277/daftar_produk_01277.pdf',850,'2026-11-02 10:00:00','Tersedia'),
(6,1,1,'Daftar Bahan','Persyaratan','daftar_bahan_01277.pdf','/uploads/01277/daftar_bahan_01277.pdf',920,'2026-11-02 10:00:00','Tersedia'),
(7,2,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01278.pdf','/uploads/01278/nib_01278.pdf',1200,'2026-11-03 10:00:00','Tersedia'),
(8,2,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01278.jpg','/uploads/01278/ktp_01278.jpg',420,'2026-11-03 10:00:00','Tersedia'),
(9,2,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01278.pdf','/uploads/01278/penyelia_01278.pdf',680,'2026-11-03 10:00:00','Tersedia'),
(10,2,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01278.pdf','/uploads/01278/manual_sjph_01278.pdf',2400,'2026-11-03 10:00:00','Tersedia'),
(11,2,1,'Daftar Produk','Persyaratan','daftar_produk_01278.pdf','/uploads/01278/daftar_produk_01278.pdf',850,'2026-11-03 10:00:00','Tersedia'),
(12,2,1,'Daftar Bahan','Persyaratan','daftar_bahan_01278.pdf','/uploads/01278/daftar_bahan_01278.pdf',920,'2026-11-03 10:00:00','Tersedia'),
(13,3,NULL,'Nomor Induk Berusaha (NIB)','Persyaratan',NULL,NULL,NULL,NULL,'Belum Tersedia'),
(14,3,NULL,'Identitas Penanggung Jawab (KTP)','Persyaratan',NULL,NULL,NULL,NULL,'Belum Tersedia'),
(15,3,NULL,'Penetapan Penyelia Halal','Persyaratan',NULL,NULL,NULL,NULL,'Belum Tersedia'),
(16,3,NULL,'Manual Sistem Jaminan Produk Halal','Persyaratan',NULL,NULL,NULL,NULL,'Belum Tersedia'),
(17,3,NULL,'Daftar Produk','Persyaratan',NULL,NULL,NULL,NULL,'Belum Tersedia'),
(18,3,NULL,'Daftar Bahan','Persyaratan',NULL,NULL,NULL,NULL,'Belum Tersedia'),
(19,4,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01280.pdf','/uploads/01280/nib_01280.pdf',1200,'2026-11-03 10:00:00','Tersedia'),
(20,4,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01280.jpg','/uploads/01280/ktp_01280.jpg',420,'2026-11-03 10:00:00','Tersedia'),
(21,4,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01280.pdf','/uploads/01280/penyelia_01280.pdf',680,'2026-11-03 10:00:00','Tersedia'),
(22,4,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01280.pdf','/uploads/01280/manual_sjph_01280.pdf',2400,'2026-11-03 10:00:00','Tersedia'),
(23,4,1,'Daftar Produk','Persyaratan','daftar_produk_01280.pdf','/uploads/01280/daftar_produk_01280.pdf',850,'2026-11-03 10:00:00','Tersedia'),
(24,4,1,'Daftar Bahan','Persyaratan','daftar_bahan_01280.pdf','/uploads/01280/daftar_bahan_01280.pdf',920,'2026-11-03 10:00:00','Tersedia'),
(25,5,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01281.pdf','/uploads/01281/nib_01281.pdf',1200,'2026-11-10 10:00:00','Tersedia'),
(26,5,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01281.jpg','/uploads/01281/ktp_01281.jpg',420,'2026-11-10 10:00:00','Tersedia'),
(27,5,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01281.pdf','/uploads/01281/penyelia_01281.pdf',680,'2026-11-10 10:00:00','Tersedia'),
(28,5,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01281.pdf','/uploads/01281/manual_sjph_01281.pdf',2400,'2026-11-10 10:00:00','Tersedia'),
(29,5,1,'Daftar Produk','Persyaratan','daftar_produk_01281.pdf','/uploads/01281/daftar_produk_01281.pdf',850,'2026-11-10 10:00:00','Tersedia'),
(30,5,1,'Daftar Bahan','Persyaratan','daftar_bahan_01281.pdf','/uploads/01281/daftar_bahan_01281.pdf',920,'2026-11-10 10:00:00','Tidak Lengkap'),
(31,6,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01282.pdf','/uploads/01282/nib_01282.pdf',1200,'2026-11-10 10:00:00','Tersedia'),
(32,6,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01282.jpg','/uploads/01282/ktp_01282.jpg',420,'2026-11-10 10:00:00','Tersedia'),
(33,6,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01282.pdf','/uploads/01282/penyelia_01282.pdf',680,'2026-11-10 10:00:00','Tersedia'),
(34,6,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01282.pdf','/uploads/01282/manual_sjph_01282.pdf',2400,'2026-11-10 10:00:00','Tersedia'),
(35,6,1,'Daftar Produk','Persyaratan','daftar_produk_01282.pdf','/uploads/01282/daftar_produk_01282.pdf',850,'2026-11-10 10:00:00','Tersedia'),
(36,6,1,'Daftar Bahan','Persyaratan','daftar_bahan_01282.pdf','/uploads/01282/daftar_bahan_01282.pdf',920,'2026-11-10 10:00:00','Tersedia'),
(37,7,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01283.pdf','/uploads/01283/nib_01283.pdf',1200,'2026-11-11 10:00:00','Tersedia'),
(38,7,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01283.jpg','/uploads/01283/ktp_01283.jpg',420,'2026-11-11 10:00:00','Tersedia'),
(39,7,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01283.pdf','/uploads/01283/penyelia_01283.pdf',680,'2026-11-11 10:00:00','Tersedia'),
(40,7,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01283.pdf','/uploads/01283/manual_sjph_01283.pdf',2400,'2026-11-11 10:00:00','Tersedia'),
(41,7,1,'Daftar Produk','Persyaratan','daftar_produk_01283.pdf','/uploads/01283/daftar_produk_01283.pdf',850,'2026-11-11 10:00:00','Tersedia'),
(42,7,1,'Daftar Bahan','Persyaratan','daftar_bahan_01283.pdf','/uploads/01283/daftar_bahan_01283.pdf',920,'2026-11-11 10:00:00','Tersedia'),
(43,8,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01284.pdf','/uploads/01284/nib_01284.pdf',1200,'2026-11-11 10:00:00','Tersedia'),
(44,8,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01284.jpg','/uploads/01284/ktp_01284.jpg',420,'2026-11-11 10:00:00','Tersedia'),
(45,8,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01284.pdf','/uploads/01284/penyelia_01284.pdf',680,'2026-11-11 10:00:00','Tersedia'),
(46,8,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01284.pdf','/uploads/01284/manual_sjph_01284.pdf',2400,'2026-11-11 10:00:00','Perlu Diperbarui'),
(47,8,1,'Daftar Produk','Persyaratan','daftar_produk_01284.pdf','/uploads/01284/daftar_produk_01284.pdf',850,'2026-11-11 10:00:00','Tersedia'),
(48,8,1,'Daftar Bahan','Persyaratan','daftar_bahan_01284.pdf','/uploads/01284/daftar_bahan_01284.pdf',920,'2026-11-11 10:00:00','Tersedia'),
(49,9,1,'Nomor Induk Berusaha (NIB)','Persyaratan','nib_01285.pdf','/uploads/01285/nib_01285.pdf',1200,'2026-11-20 10:00:00','Tersedia'),
(50,9,1,'Identitas Penanggung Jawab (KTP)','Persyaratan','ktp_01285.jpg','/uploads/01285/ktp_01285.jpg',420,'2026-11-20 10:00:00','Tersedia'),
(51,9,1,'Penetapan Penyelia Halal','Persyaratan','penyelia_01285.pdf','/uploads/01285/penyelia_01285.pdf',680,'2026-11-20 10:00:00','Tersedia'),
(52,9,1,'Manual Sistem Jaminan Produk Halal','Persyaratan','manual_sjph_01285.pdf','/uploads/01285/manual_sjph_01285.pdf',2400,'2026-11-20 10:00:00','Tersedia'),
(53,9,1,'Daftar Produk','Persyaratan','daftar_produk_01285.pdf','/uploads/01285/daftar_produk_01285.pdf',850,'2026-11-20 10:00:00','Tersedia'),
(54,9,1,'Daftar Bahan','Persyaratan','daftar_bahan_01285.pdf','/uploads/01285/daftar_bahan_01285.pdf',920,'2026-11-20 10:00:00','Tersedia');

INSERT INTO pembayaran (id_pembayaran,id_pengajuan,id_user,informasi_pembayaran,nominal_tagihan,nominal_pembayaran,metode_pembayaran,nama_pengirim,tanggal_pembayaran,nama_file_bukti,path_file_bukti,ukuran_file_bukti,cek_nominal_sesuai,cek_identitas_sesuai,cek_bukti_terbaca,status_pembayaran,catatan_verifikasi,tanggal_verifikasi) VALUES
(1,1,1,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Joko Lereng','2026-11-04 10:00:00','bukti_transfer_01277.pdf','/uploads/pembayaran/bukti_transfer_01277.pdf',420,1,1,1,'Terverifikasi','Nominal pembayaran sesuai tagihan. Identitas pengirim sesuai dan bukti transfer terbaca dengan jelas.','2026-11-05 09:30:00'),
(2,2,2,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Muhammad Barok','2026-11-05 09:00:00','bukti_transfer_01278.pdf','/uploads/pembayaran/bukti_transfer_01278.pdf',420,1,1,1,'Terverifikasi','Sesuai.','2026-11-06 09:00:00'),
(3,3,NULL,'Bukti transfer belum diterima',3000000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,'Belum Terverifikasi','Menunggu bukti pembayaran.',NULL),
(4,4,1,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Bambang Santoso','2026-11-06 08:30:00','bukti_transfer_01280.pdf','/uploads/pembayaran/bukti_transfer_01280.pdf',420,1,1,1,'Terverifikasi','Sesuai.','2026-11-06 14:00:00'),
(5,5,2,'Nominal belum sesuai',3000000,2750000,'Transfer bank manual','Zahra Fitriani','2026-11-19 15:20:00','bukti_transfer_01281.pdf','/uploads/pembayaran/bukti_transfer_01281.pdf',420,0,1,1,'Belum Terverifikasi','Nominal pembayaran Rp2.750.000 belum sesuai dengan tagihan Rp3.000.000. Mohon lengkapi kekurangan Rp250.000 dan kirimkan bukti pembayaran terbaru.','2026-11-20 13:10:00'),
(6,6,2,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Hendra Wijaya','2026-11-12 11:00:00','bukti_transfer_01282.pdf','/uploads/pembayaran/bukti_transfer_01282.pdf',420,1,1,1,'Terverifikasi','Sesuai.','2026-11-13 09:00:00'),
(7,7,1,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Rina Kusuma','2026-11-13 08:40:00','bukti_transfer_01283.pdf','/uploads/pembayaran/bukti_transfer_01283.pdf',420,1,1,1,'Terverifikasi','Nominal pembayaran sesuai tagihan. Identitas pengirim sesuai dan bukti transfer terbaca dengan jelas.','2026-11-14 10:10:00'),
(8,8,1,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Ahmad Fauzan','2026-11-13 09:15:00','bukti_transfer_01284.pdf','/uploads/pembayaran/bukti_transfer_01284.pdf',420,1,1,1,'Terverifikasi','Nominal pembayaran sesuai tagihan. Identitas pengirim sesuai dan bukti transfer terbaca dengan jelas.','2026-11-14 14:35:00'),
(9,9,1,'Bukti transfer tersedia',3000000,3000000,'Transfer bank manual','Siti Aminah','2026-11-20 16:00:00','bukti_transfer_01285.pdf','/uploads/pembayaran/bukti_transfer_01285.pdf',420,1,1,1,'Terverifikasi','Sesuai.','2026-11-21 09:00:00');

INSERT INTO penugasan (id_penugasan,id_pengajuan,id_auditor,id_admin,tanggal_penugasan,catatan_penugasan,status_penugasan) VALUES
(1,1,4,1,'2026-11-05 10:00:00','Mohon lakukan pemeriksaan produk kopi arabika bubuk sesuai prosedur sertifikasi halal.','Selesai'),
(2,2,5,2,'2026-11-06 10:00:00','Mohon lakukan pemeriksaan produk kerupuk udang sesuai prosedur sertifikasi halal.','Ditugaskan'),
(3,4,4,1,'2026-11-07 10:00:00','Mohon lakukan pemeriksaan produk madu murni sesuai prosedur sertifikasi halal.','Ditugaskan'),
(4,6,5,2,'2026-11-13 10:00:00','Mohon lakukan pemeriksaan produk roti dan kue sesuai prosedur sertifikasi halal.','Selesai'),
(5,7,3,1,'2026-11-17 10:00:00','Mohon lakukan pemeriksaan produk bumbu masak sesuai prosedur sertifikasi halal.','Ditugaskan'),
(6,8,3,1,'2026-11-14 15:00:00','Mohon lakukan pemeriksaan produk keripik pisang sesuai prosedur sertifikasi halal.','Ditugaskan');

INSERT INTO pemeriksaan (id_pemeriksaan,id_penugasan,putaran_ke,tanggal_pemeriksaan,catatan_pemeriksaan,hasil_pemeriksaan,status_pemeriksaan) VALUES
(1,1,1,'2026-11-09','Seluruh data dan dokumen sesuai.','Sesuai','Final'),
(2,2,1,'2026-11-09','Merek perisa udang berbeda dari daftar bahan pada dokumen pengajuan.','Tidak Sesuai','Final'),
(3,3,1,'2026-11-18','Pemeriksaan sedang berjalan.',NULL,'Draf'),
(4,4,1,'2026-11-14','Sertifikat halal margarin sudah berakhir dan diagram alir produksi belum lengkap.','Tidak Sesuai','Final'),
(5,4,2,'2026-11-18','Perbaikan telah memenuhi catatan auditor.','Sesuai','Final'),
(6,6,1,'2026-11-17','Merek perisa berbeda dari daftar bahan. Catatan pembersihan alat belum tersedia.','Tidak Sesuai','Final');

INSERT INTO checklist_pemeriksaan (id_checklist,id_pemeriksaan,kategori,butir_pemeriksaan,hasil,keterangan) VALUES
(1,1,'Dokumen','Nomor Induk Berusaha (NIB)','Sesuai',NULL),
(2,1,'Dokumen','Daftar produk dan bahan','Sesuai',NULL),
(3,1,'Dokumen','Diagram alur proses produksi','Sesuai',NULL),
(4,1,'Dokumen','Manual Sistem Jaminan Halal','Sesuai',NULL),
(5,1,'Data Produk dan Bahan','Nama, merek, dan jenis produk sesuai','Sesuai',NULL),
(6,1,'Data Produk dan Bahan','Komposisi dan daftar bahan sesuai','Sesuai',NULL),
(7,1,'Data Produk dan Bahan','Produsen serta pemasok bahan sesuai','Sesuai',NULL),
(8,1,'Data Produk dan Bahan','Proses produksi sesuai dokumen','Sesuai',NULL),
(9,2,'Dokumen','Nomor Induk Berusaha (NIB)','Sesuai',NULL),
(10,2,'Dokumen','Daftar produk dan bahan','Sesuai',NULL),
(11,2,'Dokumen','Diagram alur proses produksi','Sesuai',NULL),
(12,2,'Dokumen','Manual Sistem Jaminan Halal','Sesuai',NULL),
(13,2,'Data Produk dan Bahan','Nama, merek, dan jenis produk sesuai','Sesuai',NULL),
(14,2,'Data Produk dan Bahan','Komposisi dan daftar bahan sesuai','Tidak Sesuai','Merek perisa berbeda dari daftar bahan.'),
(15,2,'Data Produk dan Bahan','Produsen serta pemasok bahan sesuai','Sesuai',NULL),
(16,2,'Data Produk dan Bahan','Proses produksi sesuai dokumen','Sesuai',NULL),
(17,3,'Dokumen','Nomor Induk Berusaha (NIB)','Sesuai',NULL),
(18,3,'Dokumen','Daftar produk dan bahan','Sesuai',NULL),
(19,3,'Dokumen','Diagram alur proses produksi','Sesuai',NULL),
(20,3,'Dokumen','Manual Sistem Jaminan Halal','Sesuai',NULL),
(21,3,'Data Produk dan Bahan','Nama, merek, dan jenis produk sesuai',NULL,NULL),
(22,3,'Data Produk dan Bahan','Komposisi dan daftar bahan sesuai',NULL,NULL),
(23,3,'Data Produk dan Bahan','Produsen serta pemasok bahan sesuai',NULL,NULL),
(24,3,'Data Produk dan Bahan','Proses produksi sesuai dokumen',NULL,NULL),
(25,4,'Dokumen','Nomor Induk Berusaha (NIB)','Sesuai',NULL),
(26,4,'Dokumen','Daftar produk dan bahan','Sesuai',NULL),
(27,4,'Dokumen','Diagram alur proses produksi','Sesuai',NULL),
(28,4,'Dokumen','Manual Sistem Jaminan Halal','Sesuai',NULL),
(29,4,'Data Produk dan Bahan','Nama, merek, dan jenis produk sesuai','Sesuai',NULL),
(30,4,'Data Produk dan Bahan','Komposisi dan daftar bahan sesuai','Tidak Sesuai','Merek perisa berbeda dari daftar bahan.'),
(31,4,'Data Produk dan Bahan','Produsen serta pemasok bahan sesuai','Sesuai',NULL),
(32,4,'Data Produk dan Bahan','Proses produksi sesuai dokumen','Tidak Sesuai','Catatan pembersihan alat belum tersedia.'),
(33,5,'Dokumen','Nomor Induk Berusaha (NIB)','Sesuai',NULL),
(34,5,'Dokumen','Daftar produk dan bahan','Sesuai',NULL),
(35,5,'Dokumen','Diagram alur proses produksi','Sesuai',NULL),
(36,5,'Dokumen','Manual Sistem Jaminan Halal','Sesuai',NULL),
(37,5,'Data Produk dan Bahan','Nama, merek, dan jenis produk sesuai','Sesuai',NULL),
(38,5,'Data Produk dan Bahan','Komposisi dan daftar bahan sesuai','Sesuai',NULL),
(39,5,'Data Produk dan Bahan','Produsen serta pemasok bahan sesuai','Sesuai',NULL),
(40,5,'Data Produk dan Bahan','Proses produksi sesuai dokumen','Sesuai',NULL),
(41,6,'Dokumen','Nomor Induk Berusaha (NIB)','Sesuai',NULL),
(42,6,'Dokumen','Daftar produk dan bahan','Sesuai',NULL),
(43,6,'Dokumen','Diagram alur proses produksi','Sesuai',NULL),
(44,6,'Dokumen','Manual Sistem Jaminan Halal','Sesuai',NULL),
(45,6,'Data Produk dan Bahan','Nama, merek, dan jenis produk sesuai','Sesuai',NULL),
(46,6,'Data Produk dan Bahan','Komposisi dan daftar bahan sesuai','Tidak Sesuai','Merek perisa berbeda dari daftar bahan.'),
(47,6,'Data Produk dan Bahan','Produsen serta pemasok bahan sesuai','Sesuai',NULL),
(48,6,'Data Produk dan Bahan','Proses produksi sesuai dokumen','Tidak Sesuai','Catatan pembersihan alat belum tersedia.');

INSERT INTO catatan_perbaikan (id_catatan,id_pemeriksaan,kategori_temuan,detail_ketidaksesuaian,instruksi_perbaikan,tanggal_pencatatan,batas_waktu_revisi,status_tindak_lanjut) VALUES
(1,2,'Bahan dan komposisi','Merek perisa udang berbeda dari daftar bahan pada dokumen pengajuan.','Perbarui daftar bahan dan lampirkan sertifikat halal perisa udang.','2026-11-09 15:00:00','2026-11-27','Menunggu Perbaikan'),
(2,4,'Bahan dan komposisi','Sertifikat halal margarin sudah berakhir masa berlakunya.','Lampirkan sertifikat halal margarin yang masih berlaku.','2026-11-14 09:15:00','2026-11-17','Sudah Sesuai'),
(3,4,'Proses produksi','Diagram alir produksi roti dan kue belum lengkap.','Lengkapi diagram alir produksi roti dan kue.','2026-11-14 09:15:00','2026-11-17','Sudah Sesuai'),
(4,6,'Bahan dan komposisi','Merek perisa pisang yang digunakan berbeda dari daftar bahan pada dokumen pengajuan.','Perbarui daftar bahan dan lampirkan sertifikat halal perisa pisang yang digunakan.','2026-11-17 14:10:00','2026-11-24','Revisi Diterima'),
(5,6,'Proses produksi','Catatan pembersihan alat penggorengan belum tersedia untuk produksi bulan November.','Lengkapi catatan pembersihan dan unggah bukti penerapan prosedur sanitasi alat.','2026-11-17 14:10:00','2026-11-24','Dalam Perbaikan');

INSERT INTO dokumen_revisi (id_dokumen_revisi,id_pengajuan,id_catatan,id_dokumen_awal,id_user,revisi_ke,nama_dokumen,nama_file,path_file,ukuran_file,keterangan_revisi,tanggal_diterima_lph,tanggal_unggah,status_dokumen_revisi) VALUES
(1,6,2,NULL,5,1,'Sertifikat Halal Margarin','sertifikat_halal_margarin.pdf','/uploads/01282/revisi/sertifikat_halal_margarin.pdf',1200,'Sertifikat halal margarin yang masih berlaku.','2026-11-17 14:30:00','2026-11-17 15:00:00','Sudah Diperiksa'),
(2,6,3,NULL,5,1,'Diagram Alir Produksi','diagram_alir_produksi.pdf','/uploads/01282/revisi/diagram_alir_produksi.pdf',860,'Diagram alir produksi roti dan kue yang sudah dilengkapi.','2026-11-17 14:30:00','2026-11-17 15:00:00','Sudah Diperiksa'),
(3,8,4,48,3,1,'Daftar Bahan Produk','daftar_bahan_revisi_01.pdf','/uploads/01284/revisi/daftar_bahan_revisi_01.pdf',245,'Merek perisa diperbarui sesuai bahan yang digunakan.','2026-11-18 14:30:00','2026-11-19 09:15:00','Menunggu Pemeriksaan');

INSERT INTO riwayat_revisi (id_riwayat,id_pengajuan,id_catatan,id_dokumen_revisi,id_pemeriksaan,id_user,putaran_revisi,jenis_entri,ringkasan,status_tahap,waktu_dicatat) VALUES
(1,2,1,NULL,2,5,1,'Catatan Perbaikan','Perbarui daftar bahan dan lampirkan sertifikat halal perisa udang.','Perbaikan diminta','2026-11-09 15:00:00'),
(2,6,2,NULL,4,5,1,'Catatan Perbaikan','Perbarui bukti halal bahan dan alur produksi.','Perbaikan diminta','2026-11-14 09:15:00'),
(3,6,2,1,NULL,5,1,'Dokumen Revisi','Sertifikat halal margarin diterima LPH dan disimpan.','Diterima dan disimpan','2026-11-17 15:00:00'),
(4,6,3,2,NULL,5,1,'Dokumen Revisi','Diagram alir produksi diterima LPH dan disimpan.','Diterima dan disimpan','2026-11-17 15:00:00'),
(5,6,NULL,NULL,5,5,1,'Pemeriksaan Ulang','Perbaikan telah memenuhi catatan auditor.','Sesuai','2026-11-18 10:00:00'),
(6,8,4,NULL,6,3,1,'Catatan Perbaikan','Merek perisa berbeda dari daftar bahan. Catatan pembersihan alat belum lengkap.','Perbaikan diminta','2026-11-17 14:10:00'),
(7,8,4,3,NULL,3,1,'Dokumen Revisi','Daftar bahan dan sertifikat halal perisa diterima LPH, lalu diunggah oleh auditor untuk pemeriksaan ulang.','Diterima dan disimpan','2026-11-19 09:15:00');

INSERT INTO riwayat_status (id_riwayat_status,id_pengajuan,id_user,status_lama,status_baru,waktu_perubahan,keterangan) VALUES
(1,1,1,'Menunggu Verifikasi','Ditugaskan','2026-11-05 10:00:00','Auditor ditugaskan'),
(2,1,4,'Ditugaskan','Sedang Diperiksa','2026-11-09 08:00:00','Pemeriksaan dimulai'),
(3,1,4,'Sedang Diperiksa','Selesai','2026-11-10 14:10:00','Laporan tersimpan'),
(4,2,2,'Menunggu Verifikasi','Ditugaskan','2026-11-06 10:00:00','Auditor ditugaskan'),
(5,2,5,'Ditugaskan','Sedang Diperiksa','2026-11-09 08:00:00','Pemeriksaan dimulai'),
(6,2,5,'Sedang Diperiksa','Perlu Perbaikan','2026-11-09 15:00:00','Ditemukan ketidaksesuaian'),
(7,4,1,'Menunggu Verifikasi','Ditugaskan','2026-11-07 10:00:00','Auditor ditugaskan'),
(8,4,4,'Ditugaskan','Sedang Diperiksa','2026-11-12 08:00:00','Pemeriksaan dimulai'),
(9,6,2,'Menunggu Verifikasi','Ditugaskan','2026-11-13 10:00:00','Auditor ditugaskan'),
(10,6,5,'Ditugaskan','Sedang Diperiksa','2026-11-14 08:00:00','Pemeriksaan dimulai'),
(11,6,5,'Sedang Diperiksa','Perlu Perbaikan','2026-11-14 09:15:00','Ditemukan ketidaksesuaian'),
(12,6,5,'Perlu Perbaikan','Selesai','2026-11-18 14:30:00','Laporan tersimpan'),
(13,7,1,'Menunggu Verifikasi','Ditugaskan','2026-11-17 10:00:00','Auditor ditugaskan'),
(14,8,1,'Menunggu Verifikasi','Ditugaskan','2026-11-14 15:00:00','Auditor ditugaskan'),
(15,8,3,'Ditugaskan','Sedang Diperiksa','2026-11-17 08:00:00','Pemeriksaan dimulai'),
(16,8,3,'Sedang Diperiksa','Perlu Perbaikan','2026-11-17 14:10:00','Ditemukan ketidaksesuaian');

INSERT INTO laporan (id_laporan,nomor_laporan,id_pengajuan,id_auditor,tanggal_pemeriksaan,tanggal_laporan,ringkasan_hasil,temuan,kesimpulan,hasil_pemeriksaan_ulang,hasil_laporan,status_laporan,waktu_disimpan) VALUES
(1,'LHP-2026-0001',1,4,'2026-11-09','2026-11-10','Pemeriksaan mencakup bahan baku, proses produksi, fasilitas, serta penerapan Sistem Jaminan Produk Halal (SJPH).','Tidak ditemukan ketidaksesuaian.','Hasil pemeriksaan memenuhi kriteria yang ditinjau. Pengajuan dapat dilanjutkan ke tahap berikutnya.','Tidak dilakukan pemeriksaan ulang. Seluruh aspek yang diperiksa telah sesuai pada pemeriksaan awal.','Selesai','Tersimpan','2026-11-10 14:10:00'),
(2,'LHP-2026-0002',6,5,'2026-11-14','2026-11-18','Pemeriksaan mencakup bahan baku, proses produksi, fasilitas, serta penerapan Sistem Jaminan Produk Halal (SJPH).','1. Sertifikat halal margarin sudah berakhir. 2. Diagram alir produksi belum lengkap.','Perbaikan telah diverifikasi dan temuan ditutup. Pengajuan dapat dilanjutkan ke tahap berikutnya.','Sertifikat halal margarin dan diagram alir produksi telah dilengkapi. Seluruh catatan Revisi 01 telah dipenuhi.','Selesai','Tersimpan','2026-11-18 14:30:00');

INSERT INTO notifikasi (id_notifikasi,id_penerima,id_pengajuan,kategori,judul,isi,waktu_dibuat,sudah_dibaca,waktu_dibaca,status_pengiriman,waktu_dikirim,email_tujuan) VALUES
(1,4,1,'Penugasan Auditor','Penugasan baru','Anda ditugaskan memeriksa REG-2026-01277','2026-11-05 10:00:00',1,'2026-11-05 10:30:00','Terkirim','2026-11-05 10:01:00','siti.aminah@example.com'),
(2,5,2,'Penugasan Auditor','Penugasan baru','Anda ditugaskan memeriksa REG-2026-01278','2026-11-06 10:00:00',1,'2026-11-06 10:20:00','Terkirim','2026-11-06 10:01:00','nur.aini@example.com'),
(3,3,8,'Penugasan Auditor','Penugasan baru','Anda ditugaskan memeriksa REG-2026-01284','2026-11-14 15:00:00',1,'2026-11-14 15:10:00','Terkirim','2026-11-14 15:01:00','ahmad.fauzi@example.com'),
(4,3,7,'Penugasan Auditor','Penugasan baru','Anda ditugaskan memeriksa REG-2026-01283. Pengiriman email gagal, silakan kirim ulang.','2026-11-17 10:00:00',0,NULL,'Gagal',NULL,'ahmad.fauzi@example.com'),
(5,1,8,'Catatan Perbaikan','Catatan perbaikan baru','REG-2026-01284 berstatus Perlu Perbaikan','2026-11-17 14:10:00',0,NULL,'Terkirim','2026-11-17 14:10:00',NULL),
(6,3,8,'Dokumen Revisi Diterima','Dokumen revisi diterima','Dokumen revisi REG-2026-01284 menunggu pemeriksaan ulang','2026-11-19 09:15:00',0,NULL,'Terkirim','2026-11-19 09:16:00','ahmad.fauzi@example.com'),
(7,2,9,'Perubahan Status Pemeriksaan','Pengajuan siap ditugaskan','Pembayaran REG-2026-01285 telah terverifikasi. Pengajuan siap ditugaskan kepada auditor.','2026-11-21 09:00:00',0,NULL,'Terkirim','2026-11-21 09:00:00',NULL);

INSERT INTO data_administrasi (id_data_admin,id_user,jenis_data,informasi_data,tanggal_diperbarui) VALUES
(1,1,'Nomor surat pemeriksaan','024/LPH-UINSA/XI/2026','2026-11-20 09:00:00'),
(2,1,'Tanggal surat pemeriksaan','20 November 2026','2026-11-20 09:00:00'),
(3,1,'Lokasi pemeriksaan','Jl. Ahmad Yani No. 117, Surabaya','2026-11-19 09:00:00'),
(4,1,'Penanggung jawab laporan','Ahmad Fauzi','2026-11-19 09:00:00'),
(5,1,'Nomor laporan pemeriksaan','018/LHP/LPH-UINSA/XI/2026','2026-11-18 09:00:00');

-- ---------------------------------------------------------------------
-- TRIGGER
-- ---------------------------------------------------------------------
DELIMITER $$

-- Riwayat revisi tidak boleh diubah/dihapus (NF-08)
CREATE TRIGGER trg_riwayat_revisi_no_update BEFORE UPDATE ON riwayat_revisi
FOR EACH ROW BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'riwayat_revisi bersifat append-only: tidak boleh diubah';
END$$

CREATE TRIGGER trg_riwayat_revisi_no_delete BEFORE DELETE ON riwayat_revisi
FOR EACH ROW BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'riwayat_revisi bersifat append-only: tidak boleh dihapus';
END$$

-- Penugasan: pembayaran harus Terverifikasi, id_auditor harus Auditor, id_admin harus Admin LPH
CREATE TRIGGER trg_penugasan_validasi BEFORE INSERT ON penugasan
FOR EACH ROW BEGIN
  DECLARE v_status VARCHAR(30);
  DECLARE v_role_auditor VARCHAR(20);
  DECLARE v_role_admin VARCHAR(20);

  SELECT status_pembayaran INTO v_status FROM pembayaran WHERE id_pengajuan = NEW.id_pengajuan;
  IF v_status IS NULL OR v_status <> 'Terverifikasi' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pembayaran belum Terverifikasi: pengajuan belum dapat ditugaskan';
  END IF;

  SELECT role INTO v_role_auditor FROM users WHERE id_user = NEW.id_auditor;
  IF v_role_auditor IS NULL OR v_role_auditor <> 'Auditor' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'id_auditor harus pengguna dengan peran Auditor';
  END IF;

  SELECT role INTO v_role_admin FROM users WHERE id_user = NEW.id_admin;
  IF v_role_admin IS NULL OR v_role_admin <> 'Admin LPH' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'id_admin harus pengguna dengan peran Admin LPH';
  END IF;
END$$

DELIMITER ;
