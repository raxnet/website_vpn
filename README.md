# Panduan Instalasi XMPlus dengan aaPanel (Ubuntu)

<div>
    <h2>1. Konfigurasi aaPanel</h2>
    <p>Anda perlu memilih sistem Anda di aaPanel untuk mendapatkan metode instalasi. Di sini, Ubuntu digunakan sebagai lingkungan sistem untuk instalasi.</p>
    <p>Pastikan untuk menggunakan Ubuntu 18.04+ untuk menginstal aaPanel, sistem lain mungkin memiliki masalah yang tidak diketahui.</p>
    <pre><code>sudo apt update && sudo apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh</code></pre>
    <p>Setelah instalasi selesai, masuk ke aaPanel untuk menginstal lingkungan. Pilih metode instalasi lingkungan menggunakan LNMP dengan komponen berikut:</p>
    <ul>
        <li><strong>Bahasa Indonesia</strong>: Nginx ☑️, MySQL ☑️, PHP 7.4 ☑️, phpMyAdmin</li>
    </ul>
    <p>Pilih <strong>Cepat</strong> untuk mengkompilasi dan menginstal.</p>
</div>

<div>
    <h2>2. Instal fileinfo</h2>
    <p>Pada aaPanel:</p>
    <ol>
        <li>Buka <strong>App Store</strong> > <strong>PHP 7.4</strong> > <strong>Pengaturan</strong> > <strong>Instal ekstensi</strong>.</li>
        <li>Instal <strong>fileinfo</strong>.</li>
    </ol>
</div>

<div>
    <h2>3. Hapus Fungsi yang Dinonaktifkan</h2>
    <p>Pada aaPanel:</p>
    <ol>
        <li>Buka <strong>App Store</strong> > <strong>PHP 7.4</strong> > <strong>Pengaturan</strong> > <strong>Fungsi yang dinonaktifkan</strong>.</li>
        <li>Hapus fungsi berikut dari daftar: <code>exec</code>, <code>system</code>, <code>putenv</code>, <code>proc_open</code>.</li>
    </ol>
</div>

<div>
    <h2>4. Tambahkan Situs Web</h2>
    <p>Untuk menambahkan situs web di aaPanel:</p>
    <ol>
        <li>Buka aaPanel > <strong>Situs Web</strong> > <strong>Tambahkan situs</strong>.</li>
        <li>Isi nama domain yang Anda arahkan ke server di <strong>Domain</strong>.</li>
        <li>Pilih <strong>MySQL</strong> di <strong>Database</strong>.</li>
        <li>Pilih <strong>PHP-74</strong> di <strong>Versi PHP</strong>.</li>
    </ol>
</div>

<div>
    <h2>5. Instal XMPlus</h2>
    <p>Setelah masuk ke server melalui SSH, ikuti langkah-langkah berikut:</p>
    <ol>
        <li>Masuk ke direktori situs Anda:</li>
        <pre><code>cd /www/wwwroot/tld.com</code></pre>
        <li>Hapus file dalam direktori jika ada:</li>
        <pre><code>chattr -i .user.ini</code></pre>
        <pre><code>rm -rf .htaccess 404.html index.html</code></pre>
        <li>Unduh dan ekstrak XMPlus:</li>
        <pre><code>wget https://github.com/xcode75/XManagerPlus/releases/download/v20250104/XMPlus.zip</code></pre>
        <pre><code>unzip XMPlus.zip</code></pre>
        <li>Perbarui dependensi menggunakan Composer:</li>
        <pre><code>php composer.phar -n update</code></pre>
        <li>Hapus file zip setelah ekstraksi:</li>
        <pre><code>rm -rf XMPlus.zip</code></pre>
        <li>Pindahkan dan atur izin untuk file <code>.user.ini</code>:</li>
        <pre><code>mv .user.ini /www/wwwroot/tld.com/public</code></pre>
        <pre><code>cd /www/wwwroot/tld.com/public</code></pre>
        <pre><code>chattr +i .user.ini</code></pre>
    </ol>
</div>

<div>
    <h2>6. Konfigurasi Direktori Situs dan Pseudo-Statis</h2>
    <p>Untuk konfigurasi direktori situs dan pseudo-statis, lakukan langkah berikut:</p>
    <ol>
        <li>Edit situs yang ditambahkan > Konfigurasi > Direktori situs > Pilih <strong>/public</strong> dan simpan.</li>
        <li>Setelah penambahan selesai, edit situs yang ditambahkan > Penulisan ulang URL dan tambahkan konfigurasi berikut:</li>
        <pre><code>location / {
    try_files $uri /index.php$is_args$args;
}</code></pre>
    </ol>
</div>

<div>
    <h2>7. Tambahkan SSL ke Situs Web</h2>
    <p>Untuk menambahkan SSL pada situs:</p>
    <ol>
        <li>Edit situs yang ditambahkan > Konfigurasi > SSL.</li>
        <li>Periksa domain situs dan ajukan sertifikat SSL, aktifkan opsi <strong>Paksa HTTPS</strong>.</li>
        <li>Mulai ulang nginx setelah SSL diaktifkan.</li>
    </ol>
</div>

<div>
    <h2>8. Tambahkan Pekerjaan Cron</h2>
    <p>Untuk menambahkan pekerjaan cron, lakukan langkah berikut:</p>
    <pre><code>crontab -l > cron.tmp</code></pre>
    <pre><code>echo "* * * * * cd /www/wwwroot/tld.com && /usr/bin/php xmplus job:run >> /dev/null 2>&1" >> cron.tmp</code></pre>
    <pre><code>crontab cron.tmp</code></pre>
    <pre><code>rm -rf cron.tmp</code></pre>
</div>
