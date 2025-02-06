# Menggunakan aaPanel untuk Deploy Manual

### 1. Konfigurasi aaPanel

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

### 2. Instal Ioncube dan fileinfo
aaPanel > App Store > PHP 7.4 > Setting > Install extensions > Ioncube, fileinfo.

### 3. Hapus fungsi yang dinonaktifkan
aaPanel > App Store > PHP 7.4 > Setting > Disabled functions > hapus dari daftar (exec, system, putenv, proc_open).

### 4. Tambahkan Website
aaPanel > Website > Add site.  
- Isi nama domain yang mengarah ke server di kolom Domain  
- Pilih MySQL di Database  
- Pilih PHP-74 di PHP Version

### 5. Instal Raxnet
Setelah login ke server melalui SSH, kunjungi path situs: `cd /www/wwwroot/raxnet.my.id`

Perintah-perintah berikut perlu dijalankan di direktori situs.

### Hapus file di direktori

chattr -i .user.ini

rm -rf .htaccess 404.html index.html

### Jalankan perintah untuk menginstal Raxnet

cd /www/wwwroot/raxnet.my.id

wget https://github.com/user-attachments/files/18687948/raxnet.zip

unzip raxnet.zip

composer install

rm -rf raxnet.zip

mv .user.ini /www/wwwroot/raxnet.my.id

cd /www/wwwroot/raxnet.my.id

chattr +i .user.ini

aaPanel > Databases > Root password

Login ke phpMyAdmin dengan username root dan password root database Anda.

Server connection collation: `utf8mb4_unicode_ci`

Buat database baru dengan: `utf8mb4_unicode_ci` 

- Import `database.sql` dengan phpMyAdmin.  
  - path file: `/www/wwwroot/raxnet.my.id/database/database.sql`  
  - NB: Jangan gunakan panel aaPanel untuk mengimpor sql

- Edit konfigurasi di `/www/wwwroot/raxnet.my.id/config/config.php`  
  - Anda dapat mengubah path login admin sesuai dengan preferensi Anda. Harus dimulai dengan `/`.  
  - Isi informasi database Anda (gunakan root sebagai username dan password root di config.php).

### 6. Konfigurasi direktori situs dan pseudo-static


Setelah penambahan selesai, edit situs yang ditambahkan > URL rewrite untuk mengisi informasi pseudo-static.

location / {
        try_files $uri $uri/ /$uri.php?$args;
    }
### 7. Tambahkan SSL ke website

Edit situs yang ditambahkan > Conf > SSL  
Periksa domain situs dan ajukan sertifikat, aktifkan Force HTTPS.

### Restart nginx



