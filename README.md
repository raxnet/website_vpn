# Raxnet VPN Website

<div align="center">
  <h1>Selamat datang di repository Raxnet VPN Website!</h1>
  <p>Proyek ini adalah aplikasi web yang dirancang untuk mengelola akun VPN dan server.</p>
</div>

## 🚀 Instalasi Raxnet VPN

### Instalasi Frontend

<div>
  <h3>Langkah 1: Clone Repository</h3>
  <pre>
    git clone https://github.com/raxnet/website_vpn.git
  </pre>
  
  <h3>Langkah 2: Instal Dependensi</h3>
  <pre>
    composer install
  </pre>

  <h3>Langkah 3: Setel Variabel Lingkungan</h3>
  <p>Edit file .env sesuai kebutuhan.</p>

  <h3>Langkah 4: Jalankan Server Pengembangan</h3>
  <pre>
    php artisan serve
  </pre>
</div>

### Instalasi Backend

<div>
  <h3>Langkah 1: Masuk ke Server</h3>
  <pre>
    cd /www/wwwroot/tld.com
  </pre>
  
  <h3>Langkah 2: Hapus File di Direktori</h3>
  <pre>
    chattr -i .user.ini
    rm -rf .htaccess 404.html index.html
  </pre>
  
  <h3>Langkah 3: Jalankan Perintah untuk Menginstal Raxnet VPN</h3>
  <pre>
    unzip RaxnetVPN.zip
    php composer.phar -n update
    rm -rf RaxnetVPN.zip
    mv .user.ini /www/wwwroot/tld.com/public
    chattr +i .user.ini
  </pre>
</div>

### Konfigurasi Database

<div>
  <h3>Langkah 1: Login ke phpMyAdmin</h3>
  <p>Gunakan nama pengguna root dan kata sandi root untuk mengakses phpMyAdmin. Buat database baru dengan pengaturan utf8mb4_unicode_ci.</p>
  
  <h3>Langkah 2: Impor Database</h3>
  <pre>
    Impor file database.sql dari /www/wwwroot/tld.com/database/database.sql
  </pre>
</div>

### Konfigurasi Situs dan SSL

<div>
  <h3>Langkah 1: Konfigurasi Direktori Situs</h3>
  <pre>
    Edit situs yang ditambahkan > Konfigurasi > Direktori situs > Pilih /public
  </pre>
  
  <h3>Langkah 2: Tambahkan SSL ke Situs Web</h3>
  <pre>
    Edit situs > Konfigurasi > SSL > Aktifkan HTTPS
  </pre>

  <h3>Langkah 3: Tambahkan Pekerjaan Cron</h3>
  <pre>
    crontab -l > cron.tmp
    echo "* * * * * cd /www/wwwroot/tld.com && /usr/bin/php raxnet job:run >> /dev/null 2>&1" >> cron.tmp
    crontab cron.tmp
    rm -rf cron.tmp
  </pre>
</div>

### Buat Akun Administrator

<div>
  <h3>Langkah 1: Masuk ke Server</h3>
  <pre>
    cd /www/wwwroot/tld.com
  </pre>
  
  <h3>Langkah 2: Jalankan Perintah untuk Membuat Akun Admin</h3>
  <pre>
    php raxnet admin
  </pre>

  <h3>Langkah 3: Unduh Aplikasi</h3>
  <pre>
    php raxnet download
  </pre>
</div>

---

## 🔧 Pengaturan Lainnya

<div>
  <h3>Pengaturan Server</h3>
  <p>Atur dan kelola server untuk berbagai protokol VPN seperti VMess, VLess, Trojan, dan SSH.</p>
</div>

<div>
  <h3>Pengaturan Keamanan</h3>
  <p>Pastikan penggunaan SSL dan firewall aktif untuk mengamankan aplikasi.</p>
</div>

<div>
  <h3>Pengaturan Jaringan</h3>
  <p>Sesuaikan konfigurasi jaringan agar sesuai dengan kebutuhan operasional.</p>
</div>

<div>
  <h3>Otorisasi dan API</h3>
  <p>Konfigurasikan API pengguna untuk akses lebih mudah.</p>
</div>

<div>
  <h3>Bot Telegram</h3>
  <p>Integrasi bot Telegram untuk pemberitahuan.</p>
</div>

---

## 📝 Dokumentasi Lainnya

Untuk dokumentasi lebih lanjut, kunjungi [Basis Pengetahuan Raxnet](https://docs.raxnet.com).

---

