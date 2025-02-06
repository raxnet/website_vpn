
1. Konfigurasi aaPanel

Pastikan server Anda menggunakan Ubuntu 20.04 dan lakukan instalasi aaPanel dengan perintah berikut:

apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh

Setelah instalasi selesai, akses aaPanel melalui browser dan pilih LNMP untuk instalasi lingkungan:

☑️ Nginx

☑️ MySQL

☑️ PHP 7.4

☑️ phpMyAdmin


Pilih opsi Fast untuk kompilasi dan instalasi.


---

2. Instalasi Ioncube dan fileinfo

aaPanel > App Store > PHP 7.4 > Setting > Install Extensions

Install Ioncube dan fileinfo.



---

3. Menghapus Fungsi yang Dinonaktifkan

aaPanel > App Store > PHP 7.4 > Setting > Disabled Functions

Hapus fungsi seperti exec, system, putenv, dan proc_open.



---

4. Menambahkan Website

aaPanel > Website > Add Site

Isi nama domain yang mengarah ke server di kolom Domain.

Pilih MySQL di Database.

Pilih PHP-74 di PHP Version.



---

5. Menginstal Aplikasi dari GitHub

Setelah masuk ke server melalui SSH, jalankan perintah berikut untuk mengkloning aplikasi dari GitHub:

cd /www/wwwroot/your-site-folder
git clone https://github.com/username/repository-name.git

Catatan: Gantilah username/repository-name dengan username dan nama repository GitHub yang sesuai.

Setelah mengkloning repositori, jalankan perintah berikut untuk menginstal dependensi:

cd /www/wwwroot/your-site-folder
composer install


---

6. Konfigurasi Database

Masuk ke phpMyAdmin melalui aaPanel dan buat database baru.

Import file SQL ke dalam database dengan phpMyAdmin.

Path file SQL: /www/wwwroot/your-site-folder/database/database.sql.



Catatan: Jangan gunakan aaPanel untuk mengimpor SQL, lebih baik menggunakan phpMyAdmin.


---

7. Edit Konfigurasi

Edit file konfigurasi aplikasi di /www/wwwroot/your-site-folder/config/config.php:

Ubah pengaturan seperti path login admin sesuai preferensi Anda.

Isi informasi database (gunakan username root dan password root di config.php).



---

8. Konfigurasi Direktori dan Pseudo-Static

Edit pengaturan URL Rewrite untuk situs Anda dengan menambahkan kode berikut pada bagian Conf:

location / {
    try_files $uri $uri/ /$uri.php?$args;
}


---

9. Menambahkan SSL ke Website

Edit Situs > Conf > SSL

Periksa domain situs dan ajukan sertifikat SSL.

Aktifkan Force HTTPS.

Restart Nginx untuk menerapkan perubahan.



---

Dengan mengikuti langkah-langkah ini, Anda akan dapat mengonfigurasi dan menginstal aplikasi di server menggunakan aaPanel dan GitHub.

