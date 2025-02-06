<div align="center" style="padding: 20px; background-color: #f3f4f6; border-radius: 10px;">
    <img src="https://github.com/raxnet/vpn/blob/main/raxnet.png?raw=true" alt="Raxnet Logo" width="200">
    <h1>Selamat Datang di <span style="color: #ff5733;">RAXNET</span></h1>
    <p>Solusi Jaringan Terpadu Anda</p>
</div>

<div style="margin-top: 20px;">

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>1. Persiapan Instalasi aaPanel (Server Frontend)</h2>
        <p>Pastikan Anda menggunakan **Ubuntu 18.04** atau versi yang lebih baru. Ikuti langkah-langkah di bawah ini untuk memasang aaPanel pada **server frontend**:</p>
        <pre style="background-color: #e9ecef; padding: 10px; border-radius: 5px;"><code>sudo apt update && sudo apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh</code></pre>
        <p>Setelah pemasangan selesai, akses aaPanel dan pilih metode pemasangan **LNMP** dengan komponen berikut:</p>
        <ul>
            <li><strong>Peladen Web</strong>: Nginx</li>
            <li><strong>Pangkalan Data</strong>: MySQL</li>
            <li><strong>PHP</strong>: PHP 7.4</li>
            <li><strong>Pengelolaan Pangkalan Data</strong>: phpMyAdmin</li>
        </ul>
        <p>Gunakan opsi **Cepat** untuk pemasangan otomatis.</p>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>2. Pasang Ekstensi Fileinfo (Server Frontend)</h2>
        <p>Untuk memastikan kompatibilitas dengan beberapa aplikasi, Anda perlu memasang ekstensi **fileinfo** pada PHP 7.4 di aaPanel:</p>
        <ol>
            <li>Buka <strong>App Store</strong> di aaPanel, pilih <strong>PHP 7.4</strong>, lalu klik <strong>Pengaturan</strong>.</li>
            <li>Pilih <strong>Pasang ekstensi</strong>, cari dan pasang <strong>fileinfo</strong>.</li>
        </ol>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>3. Konfigurasi Fungsi PHP (Server Frontend)</h2>
        <p>Agar sistem berjalan dengan lancar, pastikan beberapa fungsi PHP yang tidak diperlukan dinonaktifkan. Ikuti langkah-langkah berikut:</p>
        <ol>
            <li>Buka <strong>App Store</strong> > <strong>PHP 7.4</strong> > <strong>Pengaturan</strong> > <strong>Fungsi yang dinonaktifkan</strong>.</li>
            <li>Hapus fungsi berikut dari daftar: <code>exec</code>, <code>system</code>, <code>putenv</code>, <code>proc_open</code>.</li>
        </ol>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>4. Tambahkan Situs Web ke aaPanel (Server Frontend)</h2>
        <p>Untuk menambahkan situs web baru ke aaPanel, ikuti langkah-langkah berikut:</p>
        <ol>
            <li>Buka <strong>Situs Web</strong> > <strong>Tambahkan Situs</strong> di aaPanel.</li>
            <li>Masukkan domain yang mengarah ke server pada kolom <strong>Domain</strong>.</li>
            <li>Pilih <strong>MySQL</strong> untuk <strong>Pangkalan Data</strong>.</li>
            <li>Pilih <strong>PHP-74</strong> untuk <strong>Versi PHP</strong>.</li>
        </ol>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>5. Pemasangan Raxnet (Server Frontend)</h2>
        <p>Setelah akses SSH ke server **frontend**, ikuti langkah-langkah berikut untuk memasang Raxnet:</p>
        <ol>
            <li>Masuk ke direktori root situs web Anda: <pre style="background-color: #e9ecef; padding: 10px; border-radius: 5px;"><code>cd /www/wwwroot/tld.com</code></pre></li>
            <li>Hapus berkas yang mungkin ada di direktori tersebut: <pre style="background-color: #e9ecef; padding: 10px; border-radius: 5px;"><code>chattr -i .user.ini
rm -rf .htaccess 404.html index.html</code></pre></li>
            <li>Unduh dan ekstrak Raxnet: <pre style="background-color: #e9ecef; padding: 10px; border-radius: 5px;"><code>git clone https://github.com/raxnet/website_vpn
unzip website_vpn.zip</code></pre></li>
            <li>Perbarui dependensi menggunakan Composer: <pre style="background-color: #e9ecef; padding: 10px; border-radius: 5px;"><code>php composer install</code></pre></li>
            <li>Hapus berkas zip setelah ekstraksi selesai: <pre style="background-color: #e9ecef; padding: 10px; border-radius: 5px;"><code>rm -rf website_vpn.zip</code></pre></li>
        </ol>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>6. Tambahkan SSL untuk Keamanan (Server Frontend)</h2>
        <p>Untuk menambahkan lapisan keamanan SSL ke situs web, ikuti langkah-langkah berikut:</p>
        <ol>
            <li>Masuk ke konfigurasi situs yang baru ditambahkan di aaPanel.</li>
            <li>Pilih tab <strong>SSL</strong> dan aktifkan sertifikat SSL untuk domain Anda.</li>
            <li>Pastikan opsi **Paksa HTTPS** diaktifkan untuk mengamankan komunikasi situs.</li>
            <li>Setelah SSL diaktifkan, mulai ulang Nginx untuk menerapkan perubahan.</li>
        </ol>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>7. Integrasi Payment Gateway (Server Frontend)</h2>
        <p>Untuk memfasilitasi pembayaran daring, Anda dapat mengintegrasikan berbagai Payment Gateway berikut:</p>
        <ul>
            <li><strong>Midtrans</strong>: Terintegrasi penuh dan siap digunakan.</li>
            <li><strong>Duitku</strong>: Proses pembuatan sedang berlangsung. Nantikan pembaruan lebih lanjut.</li>
            <li><strong>Xendit</strong>: Proses pembuatan sedang berlangsung. Pembaruan akan diberikan segera setelah selesai.</li>
            <li><strong>Tripay</strong>: Proses pembuatan sedang berlangsung dan sedang dalam tahap pengujian.</li>
        </ul>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>8. Integrasi Cloudflare Anti-Bot (Server Frontend)</h2>
        <p>Untuk melindungi situs Anda dari serangan bot dan mengoptimalkan performa, Anda bisa mengonfigurasi Cloudflare Anti-Bot dengan langkah-langkah berikut:</p>
        <ol>
            <li>Masuk ke akun Cloudflare Anda.</li>
            <li>Pilih situs web yang ingin dilindungi.</li>
            <li>Aktifkan mode **Sedang Diserang** untuk melindungi situs Anda dari serangan bot.</li>
            <li>Konfigurasi aturan tembok api Cloudflare untuk mencegah akses dari IP yang mencurigakan.</li>
        </ol>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>9. Titik Akhir API ke Server X-Ray (Server Frontend)</h2>
        <p>Untuk menghubungkan sistem Anda dengan server X-Ray, Anda dapat mengatur titik akhir API sebagai berikut:</p>
        <ol>
            <li>Buka konfigurasi API di aplikasi Anda.</li>
            <li>Masukkan URL titik akhir X-Ray yang sesuai dengan server Anda.</li>
            <li>Pastikan koneksi API di
            
