📘 Dokumentasi Instalasi dan Penggunaan RaxNet

Dokumentasi ini menjelaskan langkah-langkah instalasi, konfigurasi, dan penggunaan aplikasi RaxNet di server menggunakan aaPanel.


---

🛠️ 1. Persyaratan Server

Sebelum memulai instalasi, pastikan server Anda memenuhi spesifikasi berikut:

✅ Sistem Operasi: Ubuntu 20.04 / Debian 11 / CentOS 7
✅ Panel Hosting: aaPanel
✅ Software yang diperlukan:

Nginx

MySQL 5.7 atau lebih baru

PHP 7.4 atau lebih baru

phpMyAdmin
✅ Domain: Sudah mengarah ke server



---

🔹 2. Instalasi aaPanel

1. Login ke server via SSH dengan user root:

ssh root@IP_SERVER


2. Jalankan perintah instalasi aaPanel:

apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh


3. Tunggu hingga proses selesai, lalu catat URL login, username, dan password yang muncul.


4. Akses aaPanel melalui browser dengan membuka:

http://IP_SERVER:7800


5. Login ke aaPanel menggunakan username dan password yang telah dicatat.




---

🔹 3. Instalasi LNMP (Nginx, MySQL, PHP)

1. Di aaPanel, buka App Store.


2. Pilih LNMP (Nginx, MySQL, PHP) lalu klik Install.


3. Pilih versi yang sesuai:

Nginx ✅

MySQL 5.7+ ✅

PHP 7.4+ ✅

phpMyAdmin ✅



4. Klik Fast Install dan tunggu hingga proses instalasi selesai.




---

🔹 4. Konfigurasi PHP (Ioncube & fileinfo)

1. Buka aaPanel > App Store > PHP 7.4 > Setting.


2. Install Extensions berikut:
✅ Ioncube
✅ fileinfo


3. Hapus fungsi PHP yang diblokir:

Buka PHP 7.4 > Disabled Functions

Hapus fungsi berikut:

exec, system, putenv, proc_open



4. Klik Save lalu Restart PHP.




---

🔹 5. Menambahkan Website di aaPanel

1. Buka aaPanel > Website > Add Site


2. Isi Domain:

raxnet.my.id


3. Pilih Database: ✅ MySQL


4. Pilih PHP Version: ✅ PHP 7.4


5. Klik Submit untuk membuat website.




---

🔹 6. Upload & Install RaxNet di Server

1. Login ke server melalui SSH:

ssh root@IP_SERVER


2. Pindah ke direktori website:

cd /www/wwwroot/raxnet.my.id


3. Unduh skrip RaxNet dari GitHub:

git clone https://github.com/username/repository-name.git .


4. Install dependensi menggunakan Composer:

composer install


5. Pastikan izin file benar:

chown -R www-data:www-data /www/wwwroot/raxnet.my.id
chmod -R 755 /www/wwwroot/raxnet.my.id




---

🔹 7. Konfigurasi Database di phpMyAdmin

1. Masuk ke phpMyAdmin:

Buka aaPanel > Database

Klik phpMyAdmin

Login dengan:

Username: root
Password: (lihat di aaPanel)



2. Buat Database Baru:

Masuk ke tab Databases

Isi nama database, contoh:

raxnet_db

Pilih utf8mb4_general_ci

Klik Create



3. Import Struktur Database:

Pilih database raxnet_db

Masuk ke tab Import

Pilih file SQL di:

/www/wwwroot/raxnet.my.id/database/database.sql

Klik Go





---

🔹 8. Konfigurasi Aplikasi

1. Edit file konfigurasi:

nano /www/wwwroot/raxnet.my.id/config/config.php


2. Isi informasi database:

$db_host = "localhost";
$db_user = "root";
$db_pass = "password_database";
$db_name = "raxnet_db";


3. Simpan perubahan dan keluar (Ctrl + X → Y → Enter).




---

🔹 9. Konfigurasi Nginx (URL Rewrite)

1. Buka aaPanel > Website > raxnet.my.id > URL Rewrite


2. Tambahkan aturan berikut:

location / {
    try_files $uri $uri/ /index.php?$args;
}


3. Klik Save dan restart Nginx.




---

🔹 10. Mengaktifkan SSL (HTTPS)

1. Buka aaPanel > Website > raxnet.my.id > SSL


2. Klik Apply for Let's Encrypt SSL


3. Centang Force HTTPS lalu klik Save.


4. Restart Nginx untuk menerapkan perubahan.




---

🔹 11. Penggunaan Website RaxNet

📌 Login Admin & User

1. Admin Panel:

https://raxnet.my.id/admin

Username: admin

Password: (sesuai database users)



2. User Panel:

https://raxnet.my.id/login



📌 Fitur Utama RaxNet

✅ Manajemen Akun: Buat akun VMess, VLess, Trojan, SSH
✅ Saldo & Transaksi: Top-up saldo dengan Midtrans / Duitku
✅ Statistik & Grafik: Menampilkan data akun dan pengguna
✅ Manajemen Server: Kelola server dan batasan akun


---

🔹 12. Troubleshooting (Masalah Umum & Solusi)

❌ Error: Database Connection Failed

Solusi:

Cek kembali konfigurasi di config.php

Pastikan MySQL berjalan:

systemctl restart mysql


❌ Error 403 Forbidden di Nginx

Solusi:

Pastikan folder memiliki izin yang benar:

chmod -R 755 /www/wwwroot/raxnet.my.id
chown -R www-data:www-data /www/wwwroot/raxnet.my.id


❌ Tidak Bisa Login Admin

Solusi:

Reset password admin di database users melalui phpMyAdmin.



---

📌 Penutup

Dokumentasi ini mencakup seluruh proses instalasi dan penggunaan RaxNet. Jika Anda mengalami kendala, pastikan untuk memeriksa error log di aaPanel > Log atau menghubungi tim support.

