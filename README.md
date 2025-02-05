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
        <pre><code>php composer.phar -n update</code></pre>
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
        <li>Pastikan koneksi API diatur dengan benar agar dapat melakukan komunikasi dengan server X-Ray.</li>
        <li>Uji koneksi API dengan mengirimkan permintaan ke server X-Ray dan pastikan responsnya sesuai.</li>
    </ol>
</div>

<div>
    <h2>10. Konfigurasi dan Penerapan Lainnya</h2>
    <p>Selain langkah-langkah di atas, Anda dapat menyesuaikan konfigurasi tambahan untuk kebutuhan sistem Anda:</p>
    <ul>
        <li>Penyesuaian pengaturan caching pada server untuk meningkatkan performa situs.</li>
        <li>Pengaturan CDN untuk mempercepat distribusi konten secara global.</li>
        <li>Integrasi API eksternal untuk menambah fungsionalitas dan layanan tambahan.</li>
    </ul>
</div>
