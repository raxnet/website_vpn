

<div align="center" style="padding: 20px; background-color: #f3f4f6; border-radius: 10px;">
    <img src="https://github.com/raxnet/vpn/blob/main/raxnet.png?raw=true" alt="raxnet " width="200">
    <h1>Welcome to <span style="color: #ff5733;">RAXNET</span></h1>
    <p>Your All-in-One Networking Solution</p>
</div>
---

<h2 align="center">
    <img src="https://img.shields.io/static/v1?label=Supported%20OS&message=Linux&color=blue&style=flat-square" alt="Supported OS" width="40"> Supported OS
</h2>
<div align="center">
    <img src="https://img.shields.io/badge/Debian-9 (Stretch)-red?style=flat&logo=debian">
    <img src="https://img.shields.io/badge/Debian-10 (Buster)-red?style=flat&logo=debian">
    <img src="https://img.shields.io/badge/Ubuntu-18.04 LTS-orange?style=flat&logo=ubuntu">
    <img src="https://img.shields.io/badge/Ubuntu-20.04 LTS-green?style=flat&logo=ubuntu">
</div>
---

<h2 align="center">
    <img src="https://img.shields.io/badge/Commands%20%26%20Setup%20Guide-green?style=flat-square" alt="Commands" width="40"> Commands & Setup Guide
</h2>
<div align="center">
    <p><b>Installation:</b></p>
    <pre>
apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh
    </pre>
    <p><b>Update:</b></p>
    <pre>
wget https://raw.githubusercontent.com/raxnet/bon/main/update.sh && chmod +x update.sh && ./update.sh
    </pre>
</div>
---

<h2 align="center">
    <img src="https://img.shields.io/badge/Documentation-blue?style=flat-square" alt="Documentation" width="40"> Documentation
</h2>
<div align="center">
    <a href="https://github.com/username/repo/wiki" target="_blank" style="text-decoration: none;">
        <img src="https://img.shields.io/badge/View%20Wiki-blue?style=for-the-badge&logo=readthedocs">
    </a>
</div>
---

<h2 align="center">
    <img src="https://img.shields.io/badge/Feedback%20%26%20Support-red?style=flat-square" alt="Feedback" width="40"> Feedback & Support
</h2>
<div align="center">
    <a href="https://github.com/username/repo/issues" target="_blank" style="text-decoration: none;">
        <img src="https://img.shields.io/badge/Report%20Issue-red?style=for-the-badge&logo=github">
    </a>
    <a href="mailto:support@raxnet.com" style="text-decoration: none;">
        <img src="https://img.shields.io/badge/Email%20Support-green?style=for-the-badge&logo=gmail">
    </a>
</div>
---

<h2 align="center">
    <img src="https://img.shields.io/badge/Connect%20with%20Us-blue?style=flat-square" alt="Social Media" width="40"> Connect with Us
</h2>
<div align="center">
    <a href="https://twitter.com/raxnet" target="_blank" style="text-decoration: none;">
        <img src="https://img.shields.io/badge/Follow%20on%20Twitter-blue?style=for-the-badge&logo=twitter">
    </a>
    <a href="https://facebook.com/raxnet" target="_blank" style="text-decoration: none;">
        <img src="https://img.shields.io/badge/Like%20on%20Facebook-blue?style=for-the-badge&logo=facebook">
    </a>
    <a href="https://github.com/username/repo" target="_blank" style="text-decoration: none;">
        <img src="https://img.shields.io/badge/Star%20on%20GitHub-black?style=for-the-badge&logo=github">
    </a>
</div>
---

<h2 align="center">
    <img src="https://img.shields.io/badge/Installation%20Guide-green?style=flat-square" alt="Installation Guide" width="40"> Installation Guide
</h2>
---

1. Konfigurasi aaPanel

Anda perlu memilih sistem yang digunakan di aaPanel untuk mendapatkan metode instalasi. Di sini, ubuntu 20.04 digunakan sebagai lingkungan sistem untuk instalasi.

Pastikan untuk menggunakan ubuntu 20.04 untuk menginstal aaPanel, karena sistem lain mungkin memiliki masalah yang tidak diketahui.

apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh

Setelah instalasi selesai, masuk ke aaPanel untuk menginstal lingkungan.

Pilih metode instalasi lingkungan menggunakan LNMP dan periksa informasi berikut:

☑️ Nginx

☑️ MySQL

☑️ PHP 7.4

☑️ phpMyAdmin


Pilih "Fast" untuk kompilasi dan instalasi.

2. Instal Ioncube dan fileinfo

aaPanel > App Store > PHP 7.4 > Setting > Install extensions > Ioncube, fileinfo.


3. Hapus fungsi yang dinonaktifkan

aaPanel > App Store > PHP 7.4 > Setting > Disabled functions > hapus dari daftar (exec, system, putenv, proc_open).


4. Tambahkan Website

aaPanel > Website > Add site.


Isi nama domain yang mengarah ke server di kolom Domain.

Pilih MySQL di Database.

Pilih PHP-74 di PHP Version.

5. Instal Raxnet

Setelah login ke server melalui SSH, kunjungi path situs: cd /www/wwwroot/raxnet.my.id.

Perintah-perintah berikut perlu dijalankan di direktori situs.

Hapus file di direktori:

chattr -i .user.ini
rm -rf .htaccess 404.html index.html

Jalankan perintah untuk menginstal Raxnet:

cd /www/wwwroot/raxnet.my.id
wget https://github.com/raxnet/website_vpn/releases/download/raxnet/raxnet.zip
unzip raxnet.zip
composer install
rm -rf raxnet.zip
mv .user.ini /www/wwwroot/raxnet.my.id
cd /www/wwwroot/raxnet.my.id
chattr +i .user.ini

Login ke phpMyAdmin dengan username root dan password root database Anda.

Server connection collation: utf8mb4_unicode_ci.

Buat database baru dengan: utf8mb4_unicode_ci.

Import database.sql dengan phpMyAdmin.

path file: /www/wwwroot/raxnet.my.id/database/database.sql.

NB: Jangan gunakan panel aaPanel untuk mengimpor sql.

Edit konfigurasi di /www/wwwroot/raxnet.my.id/config/config.php.

Anda dapat mengubah path login admin sesuai dengan preferensi Anda. Harus dimulai dengan /.

Isi informasi database Anda (gunakan root sebagai username dan password root di config.php).

6. Konfigurasi direktori situs dan pseudo-static

Setelah penambahan selesai, edit situs yang ditambahkan > URL rewrite untuk mengisi informasi pseudo-static.

location / { try_files $uri $uri/ /$uri.php?$args; }

7. Tambahkan SSL ke website

Edit situs yang ditambahkan > Conf > SSL.

Periksa domain situs dan ajukan sertifikat, aktifkan Force HTTPS.

Restart nginx
