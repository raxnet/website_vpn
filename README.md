# Panduan Instalasi Raxnet dengan aaPanel di Ubuntu

<div>
    <h2>1. Persiapan Instalasi aaPanel</h2>
    <p>Untuk memulai instalasi aaPanel pada sistem Ubuntu, pastikan Anda menggunakan Ubuntu 18.04 atau versi yang lebih baru. Ikuti langkah-langkah di bawah ini untuk menginstal aaPanel:</p>
    <pre><code>sudo apt update && sudo apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh</code></pre>
    <p>Setelah instalasi selesai, akses aaPanel dan pilih metode instalasi <strong>LNMP</strong> dengan komponen berikut:</p>
    <ul>
        <li><strong>Web Server</strong>: Nginx</li>
        <li><strong>Database</strong>: MySQL</li>
        <li><strong>PHP</strong>: PHP 7.4</li>
        <li><strong>Manajemen Database</strong>: phpMyAdmin</li>
    </ul>
    <p>Gunakan opsi <strong>Cepat</strong> untuk instalasi otomatis.</p>
</div>

<div>
    <h2>2. Instal Ekstensi Fileinfo</h2>
    <p>Untuk memastikan kompatibilitas dengan beberapa aplikasi, Anda perlu menginstal ekstensi <strong>fileinfo</strong> pada PHP 7.4 di aaPanel:</p>
    <ol>
        <li>Buka <strong>App Store</strong> di aaPanel, pilih <strong>PHP 7.4</strong>, kemudian klik <strong>Pengaturan</strong>.</li>
        <li>Pilih <strong>Instal ekstensi</strong>, cari dan instal <strong>fileinfo</strong>.</li>
    </ol>
</div>

<div>
    <h2>3. Mengonfigurasi Fungsi PHP</h2>
    <p>Agar sistem berjalan dengan lancar, pastikan beberapa fungsi PHP yang tidak diperlukan dinonaktifkan. Ikuti langkah-langkah berikut untuk menghapus fungsi yang dinonaktifkan:</p>
    <ol>
        <li>Buka <strong>App Store</strong> > <strong>PHP 7.4</strong> > <strong>Pengaturan</strong> > <strong>Fungsi yang dinonaktifkan</strong>.</li>
        <li>Hapus fungsi berikut dari daftar: <code>exec</code>, <code>system</code>, <code>putenv</code>, <code>proc_open</code>.</li>
    </ol>
</div>

<div>
    <h2>4. Menambahkan Situs Web ke aaPanel</h2>
    <p>Untuk menambahkan situs web baru ke aaPanel, ikuti langkah-langkah berikut:</p>
    <ol>
        <li>Buka <strong>Situs Web</strong> > <strong>Tambahkan Situs</strong> di aaPanel.</li>
        <li>Masukkan domain yang diarahkan ke server pada kolom <strong>Domain</strong>.</li>
        <li>Pilih <strong>MySQL</strong> untuk <strong>Database</strong>.</li>
        <li>Pilih <strong>PHP-74</strong> untuk <strong>Versi PHP</strong>.</li>
    </ol>
</div>

<div>
    <h2>5. Instalasi Raxnet</h2>
    <p>Setelah akses SSH ke server, ikuti langkah-langkah berikut untuk menginstal Raxnet:</p>
    <ol>
        <li>Masuk ke direktori root situs web Anda:</li>
        <pre><code>cd /www/wwwroot/tld.com</code></pre>
        <li>Hapus file yang mungkin ada dalam direktori tersebut:</li>
        <pre><code>chattr -i .user.ini</code></pre>
        <pre><code>rm -rf .htaccess 404.html index.html</code></pre>
        <li>Unduh dan ekstrak Raxnet:</li>
        <pre><code>git clone https://github.com/raxnet/website_vpn</code></pre>
        <pre><code>unzip website_vpn.zip</code></pre>
        <li>Perbarui dependensi menggunakan Composer:</li>
        <pre><code>php composer install </code></pre>
        <li>Hapus file zip setelah ekstraksi selesai:</li>
        <pre><code>rm -rf website_vpn.zip</code></pre>
    </ol>
</div>

<div>
    <h2>6. Menambahkan SSL untuk Keamanan</h2>
    <p>Untuk menambahkan lapisan keamanan SSL ke situs web, ikuti langkah-langkah berikut:</p>
    <ol>
        <li>Masuk ke konfigurasi situs yang baru ditambahkan di aaPanel.</li>
        <li>Pilih tab <strong>SSL</strong> dan aktifkan sertifikat SSL untuk domain Anda.</li>
        <li>Pastikan opsi <strong>Paksa HTTPS</strong> diaktifkan untuk mengamankan komunikasi situs.</li>
        <li>Setelah SSL diaktifkan, restart nginx untuk menerapkan perubahan.</li>
    </ol>
</div>

<div>
    <h2>7. Integrasi Payment Gateway</h2>
    <p>Untuk memfasilitasi pembayaran online, Anda dapat mengintegrasikan berbagai Payment Gateway berikut:</p>
    <ul>
        <li><strong>Midtrans</strong>: Terintegrasi penuh dan siap digunakan.</li>
        <li><strong>Duitku</strong>: Proses pembuatan sedang berlangsung. Nantikan pembaruan lebih lanjut.</li>
        <li><strong>Xendit</strong>: Proses pembuatan sedang berlangsung. Pembaruan akan diberikan segera setelah selesai.</li>
        <li><strong>Tripay</strong>: Proses pembuatan sedang berlangsung dan sedang dalam tahap pengujian.</li>
    </ul>
</div>

<div>
    <h2>8. Integrasi Cloudflare Anti-Bot</h2>
    <p>Untuk melindungi situs Anda dari serangan bot dan mengoptimalkan performa, Anda bisa mengonfigurasi Cloudflare Anti-Bot dengan langkah-langkah berikut:</p>
    <ol>
        <li>Masuk ke akun Cloudflare Anda.</li>
        <li>Pilih situs web yang ingin dilindungi.</li>
        <li>Aktifkan mode "Under Attack" untuk melindungi situs Anda dari serangan bot.</li>
        <li>Konfigurasi aturan firewall Cloudflare untuk mencegah akses dari IP yang mencurigakan.</li>
    </ol>
</div>

<div>
    <h2>9. API Endpoint ke Server X-Ray</h2>
    <p>Untuk menghubungkan sistem Anda dengan server X-Ray, Anda dapat mengatur endpoint API sebagai berikut:</p>
    <ol>
        <li>Buka konfigurasi API di aplikasi Anda.</li>
        <li>Masukkan URL endpoint X-Ray yang sesuai dengan server Anda.</li>
        <li>Pastikan koneksi API diatur denga# Panduan Instalasi Raxnet dengan aaPanel di Ubuntu

<div>
    <h2>1. Persiapan Instalasi aaPanel</h2>
    <p>Untuk memulai instalasi aaPanel pada sistem Ubuntu, pastikan Anda menggunakan Ubuntu 18.04 atau versi yang lebih baru. Ikuti langkah-langkah di bawah ini untuk menginstal aaPanel:</p>
    <pre><code>sudo apt update && sudo apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh</code></pre>
    <p>Setelah instalasi selesai, akses aaPanel dan pilih metode instalasi <strong>LNMP</strong> dengan komponen berikut:</p>
    <ul>
        <li><strong>Web Server</strong>: Nginx</li>
        <li><strong>Database</strong>: MySQL</li>
        <li><strong>PHP</strong>: PHP 7.4</li>
        <li><strong>Manajemen Database</strong>: phpMyAdmin</li>
    </ul>
    <p>Gunakan opsi <strong>Cepat</strong> untuk instalasi otomatis.</p>
</div>

<div>
    <h2>2. Instal Ekstensi Fileinfo</h2>
    <p>Untuk memastikan kompatibilitas dengan beberapa aplikasi, Anda perlu menginstal ekstensi <strong>fileinfo</strong> pada PHP 7.4 di aaPanel:</p>
    <ol>
        <li>Buka <strong>App Store</strong> di aaPanel, pilih <strong>PHP 7.4</strong>, kemudian klik <strong>Pengaturan</strong>.</li>
        <li>Pilih <strong>Instal ekstensi</strong>, cari dan instal <strong>fileinfo</strong>.</li>
    </ol>
</div>

<div>
    <h2>3. Mengonfigurasi Fungsi PHP</h2>
    <p>Agar sistem berjalan dengan lancar, pastikan beberapa fungsi PHP yang tidak diperlukan dinonaktifkan. Ikuti langkah-langkah berikut untuk menghapus fungsi yang dinonaktifkan:</p>
    <ol>
        <li>Buka <strong>App Store</strong> > <strong>PHP 7.4</strong> > <strong>Pengaturan</strong> > <strong>Fungsi yang dinonaktifkan</strong>.</li>
        <li>Hapus fungsi berikut dari daftar: <code>exec</code>, <code>system</code>, <code>putenv</code>, <code>proc_open</code>.</li>
    </ol>
</div>

<div>
    <h2>4. Menambahkan Situs Web ke aaPanel</h2>
    <p>Untuk menambahkan situs web baru ke aaPanel, ikuti langkah-langkah berikut:</p>
    <ol>
        <li>Buka <strong>Situs Web</strong> > <strong>Tambahkan Situs</strong> di aaPanel.</li>
        <li>Masukkan domain yang diarahkan ke server pada kolom <strong>Domain</strong>.</li>
        <li>Pilih <strong>MySQL</strong> untuk <strong>Database</strong>.</li>
        <li>Pilih <strong>PHP-74</strong> untuk <strong>Versi PHP</strong>.</li>
    </ol>
</div>

<div>
    <h2>5. Instalasi Raxnet</h2>
    <p>Setelah akses SSH ke server, ikuti langkah-langkah berikut untuk menginstal Raxnet:</p>
    <ol>
        <li>Masuk ke direktori root situs web Anda:</li>
        <pre><code>cd /www/wwwroot/tld.com</code></pre>
        <li>Hapus file yang mungkin ada dalam direktori tersebut:</li>
        <pre><code>chattr -i .user.ini</code></pre>
        <pre><code>rm -rf .htaccess 404.html index.html</code></pre>
        <li>Unduh dan ekstrak Raxnet:</li>
        <pre><code>git clone https://github.com/raxnet/website_vpn</code></pre>
        <pre><code>unzip website_vpn.zip</code></pre>
        <li>Perbarui dependensi menggunakan Composer:</li>
        <pre><code>php composer install </code></pre>
        <li>Hapus file zip setelah ekstraksi selesai:</li>
        <pre><code>rm -rf website_vpn.zip</code></pre>
    </ol>
</div>

<div>
    <h2>6. Menambahkan SSL untuk Keamanan</h2>
    <p>Untuk menambahkan lapisan keamanan SSL ke situs web, ikuti langkah-langkah berikut:</p>
    <ol>
        <li>Masuk ke konfigurasi situs yang baru ditambahkan di aaPanel.</li>
        <li>Pilih tab <strong>SSL</strong> dan aktifkan sertifikat SSL untuk domain Anda.</li>
        <li>Pastikan opsi <strong>Paksa HTTPS</strong> diaktifkan untuk mengamankan komunikasi situs.</li>
        <li>Setelah SSL diaktifkan, restart nginx untuk menerapkan perubahan.</li>
    </ol>
</div>

<div>
    <h2>7. Integrasi Payment Gateway</h2>
    <p>Untuk memfasilitasi pembayaran online, Anda dapat mengintegrasikan berbagai Payment Gateway berikut:</p>
    <ul>
        <li><strong>Midtrans</strong>: Terintegrasi penuh dan siap digunakan.</li>
        <li><strong>Duitku</strong>: Proses pembuatan sedang berlangsung. Nantikan pembaruan lebih lanjut.</li>
        <li><strong>Xendit</strong>: Proses pembuatan sedang berlangsung. Pembaruan akan diberikan segera setelah selesai.</li>
        <li><strong>Tripay</strong>: Proses pembuatan sedang berlangsung dan sedang dalam tahap pengujian.</li>
    </ul>
</div>

<div>
    <h2>8. Integrasi Cloudflare Anti-Bot</h2>
    <p>Untuk melindungi situs Anda dari serangan bot dan mengoptimalkan performa, Anda bisa mengonfigurasi Cloudflare Anti-Bot dengan langkah-langkah berikut:</p>
    <ol>
        <li>Masuk ke akun Cloudflare Anda.</li>
        <li>Pilih situs web yang ingin dilindungi.</li>
        <li>Aktifkan mode "Under Attack" untuk melindungi situs Anda dari serangan bot.</li>
        <li>Konfigurasi aturan firewall Cloudflare untuk mencegah akses dari IP yang mencurigakan.</li>
    </ol>
</div>

<div>
    <h2>9. API Endpoint ke Server X-Ray</h2>
    <p>Untuk menghubungkan sistem Anda dengan server X-Ray, Anda dapat mengatur endpoint API sebagai berikut:</p>
    <ol>


#intalasi server backend






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
    <img src="https://img.shields.io/badge/Supported%20Services-blue?style=flat-square" alt="Supported Services" width="40"> Supported Services
</h2>
<div align="center">
    <img src="https://img.shields.io/badge/OpenSSH-Active-success.svg">
    <img src="https://img.shields.io/badge/Dropbear-Active-success.svg">
    <img src="https://img.shields.io/badge/BadVPN-Active-success.svg">
    <img src="https://img.shields.io/badge/Stunnel-Active-success.svg">
    <img src="https://img.shields.io/badge/OpenVPN-Active-success.svg">
    <img src="https://img.shields.io/badge/Webmin-Active-success.svg">
    <img src="https://img.shields.io/badge/Privoxy-Active-green.svg">
    <img src="https://img.shields.io/badge/WireGuard-Active-success.svg">
</div>

---

<h2 align="center">
    <img src="https://img.shields.io/static/v1?label=Commands&message=Setup%20Guide&color=green&style=flat-square" alt="Commands" width="40"> Commands
</h2>
<div align="center">
    <p><b>Installation:</b></p>
    <pre>
apt install -y && apt update -y && apt upgrade -y && apt install lolcat -y && gem install lolcat && wget -q https://raw.githubusercontent.com/raxnet/vpn/main/install.sh && chmod +x install.sh && ./install.sh
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
<div <div align="center" style="padding: 20px; background-color: #f3f4f6; border-radius: 10px;">
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
    <img src="https://img.shields.io/badge/Supported%20Services-blue?style=flat-square" alt="Supported Services" width="40"> Supported Services
</h2>
<div align="center">
    <img src="https://img.shields.io/badge/OpenSSH-Active-success.svg">
    <img src="https://img.shields.io/badge/Dropbear-Active-success.svg">
    <img src="https://img.shields.io/badge/BadVPN-Active-success.svg">
    <img src="https://img.shields.io/badge/Stunnel-Active-success.svg">
    <img src="https://img.shields.io/badge/OpenVPN-Active-success.svg">
    <img src="https://img.shields.io/badge/Webmin-Active-success.svg">
    <img src="https://img.shields.io/badge/Privoxy-Active-green.svg">
    <img src="https://img.shields.io/badge/WireGuard-Active-success.svg">
</div>

---

<h2 align="center">
    <img src="https://img.shields.io/static/v1?label=Commands&message=Setup%20Guide&color=green&style=flat-square" alt="Commands" width="40"> Commands
</h2>
<div align="center">
    <p><b>Installation:</b></p>
    <pre>
apt install -y && apt update -y && apt upgrade -y && apt install lolcat -y && gem install lolcat && wget -q https://raw.githubusercontent.com/raxnet/vpn/main/install.sh && chmod +x install.sh && ./install.sh
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
align="center">
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





        
