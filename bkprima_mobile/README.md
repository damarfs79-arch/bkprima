# BK Prima Mobile (Android Flutter)

Aplikasi mobile resmi Bimbingan Konseling (BK) dan Presensi Siswa SMK PGRI 05 Jember berbasis Flutter (Dart), terintegrasi dengan backend Laravel BKPRIMA.

Aplikasi ini dirancang khusus untuk **Guru BK / Konselor Sekolah** dengan antarmuka yang bersih, fungsional, bebas AI slop, dan mematuhi design system BKPRIMA (Royal Blue `#2563EB`, Slate 50 `#F8FAFC`, font Plus Jakarta Sans).

---

## 1. Kompatibilitas Perangkat Android
- **Minimum SDK**: `minSdk = 21` (Mendukung Android 5.0 Lollipop ke atas, mencakup >99.5% perangkat Android aktif).
- **Target SDK**: Sesuai standar Android terbaru (Android 14 / 15).
- **Cleartext Traffic**: Diaktifkan (`android:usesCleartextTraffic="true"` di `AndroidManifest.xml`) untuk mendukung komunikasi HTTP lokal ke server Laravel selama tahap pengembangan/jaringan LAN.
- **Izin yang Dikonfigurasi**:
  - `INTERNET` & `ACCESS_NETWORK_STATE`
  - `CAMERA` (Ambil foto bukti pelanggaran langsung)
  - `READ_EXTERNAL_STORAGE` & `READ_MEDIA_IMAGES` (Pilih bukti foto dari galeri)

---

## 2. Batasan Akses Peran (Role Guard)
- **Khusus Guru BK**: Aplikasi mobile hanya mengizinkan akun dengan peran `guru` (Guru BK/Konselor).
- Akun Administrator yang mencoba masuk di mobile akan ditolak dengan pesan:
  > *"Aplikasi mobile BK Prima khusus untuk Guru BK. Akun Administrator silakan masuk melalui Web Panel."*

### Kredensial Pengujian (Guru BK):
- **Email Guru BK**: `nurul.hidayati@bkprima.sch.id` (atau `guru@bkprima.sch.id`)
- **Kata Sandi**: `secret123` (atau `BkSMKPGRI052026!`)

---

## 3. Fitur Utama
1. **Splash Screen / Loading Screen Elegan**:
   - Menampilkan logo resmi BK Prima dengan animasi subtle.
   - Pengecekan otomatis sesi token login yang tersimpan di perangkat.
2. **Halaman Login Elegan**:
   - Form otentikasi Guru BK yang bersih dan minimalis.
   - Tombol pengaturan alamat server API di pojok kanan atas untuk fleksibilitas testing.
3. **Rekap Presensi Harian Siswa (Tab Presensi)**:
   - Header tanggal dan statistik harian 2x2 (Total, Hadir, Terlambat, Alpa/Izin/Sakit).
   - Filter rombel & pencarian siswa secara instan.
   - Tombol cepat kontak WhatsApp wali murid siswa.
   - Tombol sinkronisasi presensi HadirMu.
4. **Form Input Pelanggaran (Tab Verifikasi)**:
   - Pemilihan siswa terdaftar atau input manual.
   - Chips kategori pelanggaran cepat (Terlambat, Atribut, Merokok, dll).
   - Poin pelanggaran bersifat opsional (otomatis berstatus Kasus Pembinaan jika dikosongkan).
   - Pengambilan bukti foto pelanggaran dari Kamera atau Galeri.
5. **Catatan Pelanggaran & Surat Panggilan SP**:
   - Ringkasan kasus per kategori sanksi.
   - Riwayat kasus siswa beserta preview foto dan badge poin.
6. **Profil Guru BK (Tab Profil)**:
   - Identitas pendidik, status kepegawaian, dan informasi rombel kelas binaan.
   - Tombol keluar sesi aman.
7. **Statistik Kedisiplinan (Tab Statistik)**:
   - Tren presensi dan kedisiplinan 30 hari terakhir.

---

## 4. Konfigurasi Alamat Server API

Secara bawaan, aplikasi menggunakan URL backend:
- **Emulator Android**: `http://10.0.2.2:8000/api`
- **HP Fisik Android via WiFi**: `http://[IP_KOMPUTER_ANDA]:8000/api` (misal: `http://192.168.1.10:8000/api`)

Pengaturan IP server dapat diubah langsung dari aplikasi tanpa perlu compile ulang:
1. Di layar login, klik ikon pengaturan di pojok kanan atas (ikon kabel LAN/jaringan).
2. Masukkan alamat IP komputer backend Laravel Anda.
3. Tekan **Simpan**.

---

## 5. Cara Menjalankan & Membangun Aplikasi

### Prasyarat
- Flutter SDK (>= 3.13.0)
- Android SDK (Android Studio / Command Line Tools)
- Server backend Laravel berjalan di port 8000

### Menjalankan Mode Pengembangan
```bash
cd bkprima_mobile
flutter pub get
flutter run
```

### Membangun APK Rilis (Siap Install di Android)
Untuk membangun berkas APK universal:
```bash
flutter build apk --release
```
Berkas APK akan tersedia di:
`build/app/outputs/flutter-apk/app-release.apk`

Untuk membuat APK yang lebih ringan per arsitektur CPU (disarankan untuk kompatibilitas HP lama/baru):
```bash
flutter build apk --split-per-abi
```
Berkas APK akan terbagi menjadi `app-armeabi-v7a-release.apk`, `app-arm64-v8a-release.apk`, dan `app-x86_64-release.apk`.
