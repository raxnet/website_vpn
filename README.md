<div class="twilight-container" style="background-color: #1a1a2e; color: #f0f0f0; font-family: 'Roboto', sans-serif; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);">
  <h1 style="color: #f3a847;">Menggunakan aaPanel untuk Deploy Manual</h1>

  <h2 style="color: #f3a847;">1. Konfigurasi aaPanel</h2>
  <p style="line-height: 1.6;">Anda perlu memilih sistem yang digunakan di aaPanel untuk mendapatkan metode instalasi. Di sini, ubuntu 20.04 digunakan sebagai lingkungan sistem untuk instalasi.</p>
  <p style="line-height: 1.6;">Pastikan untuk menggunakan ubuntu 20.04 untuk menginstal aaPanel, karena sistem lain mungkin memiliki masalah yang tidak diketahui.</p>
  <pre style="background-color: #282845; padding: 15px; border-radius: 5px; overflow-x: auto; color: #f0f0f0; font-family: 'Courier New', monospace;">
    <code style="background-color: #282845; color: #f0f0f0; padding: 2px 4px; border-radius: 3px;">apt install -y wget && wget -O install.sh http://www.aapanel.com/script/install_6.0_en.sh && bash install.sh</code>
  </pre>

  <h2 style="color: #f3a847;">2. Instal Ioncube dan fileinfo</h2>
  <p style="line-height: 1.6;">aaPanel > App Store > PHP 7.4 > Setting > Install extensions > Ioncube, fileinfo.</p>

  <h2 style="color: #f3a847;">3. Hapus fungsi yang dinonaktifkan</h2>
  <p style="line-height: 1.6;">aaPanel > App Store > PHP 7.4 > Setting > Disabled functions > hapus dari daftar (exec, system, putenv, proc_open).</p>

  <h2 style="color: #f3a847;">4. Tambahkan Website</h2>
  <p style="line-height: 1.6;">aaPanel > Website > Add site.</p>
  <p style="line-height: 1.6;">Isi nama domain yang mengarah ke server di kolom Domain.</p>
  <p style="line-height: 1.6;">Pilih MySQL di Database.</p>
  <p style="line-height: 1.6;">Pilih PHP-74 di PHP Version.</p>

  <h2 style="color: #f3a847;">5. Instal Raxnet</h2>
  <p style="line-height: 1.6;">Setelah login ke server melalui SSH, kunjungi path situs: <code>cd /www/wwwroot/raxnet.my.id</code></p>
  <p style="line-height: 1.6;">Perintah-perintah berikut perlu dijalankan di direktori situs.</p>
  <pre style="background-color: #282845; padding: 15px; border-radius: 5px; overflow-x: auto; color: #f0f0f0; font-family: 'Courier New', monospace;">
    <code style="background-color: #282845; color: #f0f0f0; padding: 2px 4px; border-radius: 3px;">chattr -i .user.ini</code>
  </pre>
  <pre style="background-color: #282845; padding: 15px; border-radius: 5px; overflow-x: auto; color: #f0f0f0; font-family: 'Courier New', monospace;">
    <code style="background-color: #282845; color: #f0f0f0; padding: 2px 4px; border-radius: 3px;">rm -rf .htaccess 404.html index.html</code>
  </pre>

  <h2 style="color: #f3a847;">6. Konfigurasi direktori situs dan pseudo-static</h2>
  <p style="line-height: 1.6;">Setelah penambahan selesai, edit situs yang ditambahkan > URL rewrite untuk mengisi informasi pseudo-static.</p>
  <pre style="background-color: #282845; padding: 15px; border-radius: 5px; overflow-x: auto; color: #f0f0f0; font-family: 'Courier New', monospace;">
    <code style="background-color: #282845; color: #f0f0f0; padding: 2px 4px; border-radius: 3px;">location / { try_files $uri $uri/ /$uri.php?$args; }</code>
  </pre>

  <h2 style="color: #f3a847;">7. Tambahkan SSL ke website</h2>
  <p style="line-height: 1.6;">Edit situs yang ditambahkan > Conf > SSL. Periksa domain situs dan ajukan sertifikat, aktifkan Force HTTPS.</p>

  <h2 style="color: #f3a847;">Restart nginx</h2>
  <button class="btn" style="background-color: #f3a847; color: #1a1a2e; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;">Restart Nginx</button>
</div>
