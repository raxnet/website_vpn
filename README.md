
# Panduan Instalasi Raxnet dengan aaPanel di Ubuntu

<div align="center" style="padding: 20px; background-color: #f3f4f6; border-radius: 10px;">
    <img src="https://github.com/raxnet/vpn/blob/main/raxnet.png?raw=true" alt="Raxnet Logo" width="200">
    <h1>Welcome to <span style="color: #ff5733;">RAXNET</span></h1>
    <p>Your All-in-One Networking Solution</p>
</div>

---

## 1. Persiapan Instalasi aaPanel

Pastikan Anda menggunakan **Ubuntu 18.04** atau versi yang lebih baru. Ikuti langkah-langkah di bawah ini untuk menginstal aaPanel:

```bash
sudo apt update && sudo apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh
```

Setelah instalasi selesai, akses aaPanel dan pilih metode instalasi **LNMP** dengan komponen berikut:

- **Web Server**: Nginx
- **Database**: MySQL
- **PHP**: PHP 7.4
- **Manajemen Database**: phpMyAdmin

Gunakan opsi **Cepat** untuk instalasi otomatis.

---

## 2. Instal Ekstensi Fileinfo

Untuk memastikan kompatibilitas dengan beberapa aplikasi, Anda perlu menginstal ekstensi **fileinfo** pada PHP 7.4 di aaPanel:

1. Buka **App Store** di aaPanel, pilih **PHP 7.4**, kemudian klik **Pengaturan**.
2. Pilih **Instal ekstensi**, cari dan instal **fileinfo**.

---

## 3. Mengonfigurasi Fungsi PHP

Agar sistem berjalan dengan lancar, pastikan beberapa fungsi PHP yang tidak diperlukan dinonaktifkan. Ikuti langkah-langkah berikut:

1. Buka **App Store** > **PHP 7.4** > **Pengaturan** > **Fungsi yang dinonaktifkan**.
2. Hapus fungsi berikut dari daftar: `exec`, `system`, `putenv`, `proc_open`.

---

## 4. Menambahkan Situs Web ke aaPanel

Untuk menambahkan situs web baru ke aaPanel, ikuti langkah-langkah berikut:

1. Buka **Situs Web** > **Tambahkan Situs** di aaPanel.
2. Masukkan domain yang diarahkan ke server pada kolom **Domain**.
3. Pilih **MySQL** untuk **Database**.
4. Pilih **PHP-74** untuk **Versi PHP**.

---

## 5. Instalasi Raxnet

Setelah akses SSH ke server, ikuti langkah-langkah berikut untuk menginstal Raxnet:

1. Masuk ke direktori root situs web Anda:
   ```bash
   cd /www/wwwroot/tld.com
   ```
2. Hapus file yang mungkin ada dalam direktori tersebut:
   ```bash
   chattr -i .user.ini
   rm -rf .htaccess 404.html index.html
   ```
3. Unduh dan ekstrak Raxnet:
   ```bash
   git clone https://github.com/raxnet/website_vpn
   unzip website_vpn.zip
   ```
4. Perbarui dependensi menggunakan Composer:
   ```bash
   php composer install
   ```
5. Hapus file zip setelah ekstraksi selesai:
   ```bash
   rm -rf website_vpn.zip
   ```

---

## 6. Menambahkan SSL untuk Keamanan

Untuk menambahkan lapisan keamanan SSL ke situs web, ikuti langkah-langkah berikut:

1. Masuk ke konfigurasi situs yang baru ditambahkan di aaPanel.
2. Pilih tab **SSL** dan aktifkan sertifikat SSL untuk domain Anda.
3. Pastikan opsi **Paksa HTTPS** diaktifkan untuk mengamankan komunikasi situs.
4. Setelah SSL diaktifkan, restart nginx untuk menerapkan perubahan.

---

## 7. Integrasi Payment Gateway

Untuk memfasilitasi pembayaran online, Anda dapat mengintegrasikan berbagai Payment Gateway berikut:

- **Midtrans**: Terintegrasi penuh dan siap digunakan.
- **Duitku**: Proses pembuatan sedang berlangsung. Nantikan pembaruan lebih lanjut.
- **Xendit**: Proses pembuatan sedang berlangsung. Pembaruan akan diberikan segera setelah selesai.
- **Tripay**: Proses pembuatan sedang berlangsung dan sedang dalam tahap pengujian.

---

## 8. Integrasi Cloudflare Anti-Bot

Untuk melindungi situs Anda dari serangan bot dan mengoptimalkan performa, Anda bisa mengonfigurasi Cloudflare Anti-Bot dengan langkah-langkah berikut:

1. Masuk ke akun Cloudflare Anda.
2. Pilih situs web yang ingin dilindungi.
3. Aktifkan mode **Under Attack** untuk melindungi situs Anda dari serangan bot.
4. Konfigurasi aturan firewall Cloudflare untuk mencegah akses dari IP yang mencurigakan.

---

## 9. API Endpoint ke Server X-Ray
peringatan jangan install backend di server yang sama dengan fronend (aapanel)❗❗❗❗❗❗❗❗❗❗




## 10. Instalasi Server Backend

Untuk menginstal server backend, ikuti langkah-langkah berikut:

1. Pastikan sistem Anda sudah diperbarui:
   ```bash
   apt install -y && apt update -y && apt upgrade -y
   ```
2. Instal dependensi tambahan:
   ```bash
   apt install lolcat -y && gem install lolcat
   ```
3. Unduh dan jalankan skrip instalasi:
   ```bash
   wget -q https://raw.githubusercontent.com/raxnet/vpn/main/install.sh && chmod +x install.sh && ./install.sh
   ```

---

## 11. Update Sistem

Untuk memperbarui sistem, jalankan perintah berikut:

```bash
wget https://raw.githubusercontent.com/raxnet/vpn/main/update.sh && chmod +x update.sh && ./update.sh
```

---

## 12. Dokumentasi dan Dukungan

- **Dokumentasi**: [Lihat Wiki](https://github.com/username/repo/wiki)
- **Laporkan Masalah**: [Buka Issue](https://github.com/username/repo/issues)
- **Dukungan Email**: [support@raxnet.com](mailto:support@raxnet.com)

---

## 13. Terhubung dengan Kami

- **Twitter**: [Follow @raxnet](https://twitter.com/raxnet)
- **Facebook**: [Like @raxnet](https://facebook.com/raxnet)
- **GitHub**: [Star Repo](https://github.com/username/repo)

---

Dengan mengikuti panduan ini, Anda dapat menginstal dan mengonfigurasi Raxnet dengan aaPanel di Ubuntu dengan mudah. Jika Anda memiliki pertanyaan atau masalah, jangan ragu untuk menghubungi tim dukungan kami.
