
<div align="center" style="padding: 20px; background-color: #f3f4f6; border-radius: 10px;">
    <img src="https://github.com/raxnet/vpn/blob/main/raxnet.png?raw=true" alt="Raxnet Logo" width="200">
    <h1>Selamat Datang di <span style="color: #ff5733;">RAXNET</span></h1>
    <p>Solusi Jaringan Terpadu Anda</p>
</div>

---

## 1. Persiapan Instalasi aaPanel (Server Frontend)

Pastikan Anda menggunakan **Ubuntu 18.04** atau versi yang lebih baru. Ikuti langkah-langkah di bawah ini untuk memasang aaPanel pada **server frontend**:

```bash
sudo apt update && sudo apt install -y wget && wget -O install.sh [http://www.aapanel.com/script/install_6.0_en.sh](http://www.aapanel.com/script/install_6.0_en.sh) && bash install.sh

Setelah pemasangan selesai, akses aaPanel dan pilih metode pemasangan LNMP dengan komponen berikut:
 * Peladen Web: Nginx
 * Pangkalan Data: MySQL
 * PHP: PHP 7.4
 * Pengelolaan Pangkalan Data: phpMyAdmin
Gunakan opsi Cepat untuk pemasangan otomatis.
2. Pasang Ekstensi Fileinfo (Server Frontend)
Untuk memastikan kompatibilitas dengan beberapa aplikasi, Anda perlu memasang ekstensi fileinfo pada PHP 7.4 di aaPanel:
 * Buka App Store di aaPanel, pilih PHP 7.4, lalu klik Pengaturan.
 * Pilih Pasang ekstensi, cari dan pasang fileinfo.
3. Konfigurasi Fungsi PHP (Server Frontend)
Agar sistem berjalan dengan lancar, pastikan beberapa fungsi PHP yang tidak diperlukan dinonaktifkan. Ikuti langkah-langkah berikut:
 * Buka App Store > PHP 7.4 > Pengaturan > Fungsi yang dinonaktifkan.
 * Hapus fungsi berikut dari daftar: exec, system, putenv, proc_open.
4. Tambahkan Situs Web ke aaPanel (Server Frontend)
Untuk menambahkan situs web baru ke aaPanel, ikuti langkah-langkah berikut:
 * Buka Situs Web > Tambahkan Situs di aaPanel.
 * Masukkan domain yang mengarah ke server pada kolom Domain.
 * Pilih MySQL untuk Pangkalan Data.
 * Pilih PHP-74 untuk Versi PHP.
5. Pemasangan Raxnet (Server Frontend)
Setelah akses SSH ke server frontend, ikuti langkah-langkah berikut untuk memasang Raxnet:
 * Masuk ke direktori root situs web Anda:
   cd /www/wwwroot/tld.com

 * Hapus berkas yang mungkin ada di direktori tersebut:
   chattr -i .user.ini
rm -rf .htaccess 404.html index.html

 * Unduh dan ekstrak Raxnet:
   git clone [https://github.com/raxnet/website_vpn](https://github.com/raxnet/website_vpn)
unzip website_vpn.zip

 * Perbarui dependensi menggunakan Composer:
   php composer install

 * Hapus berkas zip setelah ekstraksi selesai:
   rm -rf website_vpn.zip

6. Tambahkan SSL untuk Keamanan (Server Frontend)
Untuk menambahkan lapisan keamanan SSL ke situs web, ikuti langkah-langkah berikut:
 * Masuk ke konfigurasi situs yang baru ditambahkan di aaPanel.
 * Pilih tab SSL dan aktifkan sertifikat SSL untuk domain Anda.
 * Pastikan opsi Paksa HTTPS diaktifkan untuk mengamankan komunikasi situs.
 * Setelah SSL diaktifkan, mulai ulang Nginx untuk menerapkan perubahan.
7. Integrasi Payment Gateway (Server Frontend)
Untuk memfasilitasi pembayaran daring, Anda dapat mengintegrasikan berbagai Payment Gateway berikut:
 * Midtrans: Terintegrasi penuh dan siap digunakan.
 * Duitku: Proses pembuatan sedang berlangsung. Nantikan pembaruan lebih lanjut.
 * Xendit: Proses pembuatan sedang berlangsung. Pembaruan akan diberikan segera setelah selesai.
 * Tripay: Proses pembuatan sedang berlangsung dan sedang dalam tahap pengujian.
8. Integrasi Cloudflare Anti-Bot (Server Frontend)
Untuk melindungi situs Anda dari serangan bot dan mengoptimalkan performa, Anda bisa mengonfigurasi Cloudflare Anti-Bot dengan langkah-langkah berikut:
 * Masuk ke akun Cloudflare Anda.
 * Pilih situs web yang ingin dilindungi.
 * Aktifkan mode Sedang Diserang untuk melindungi situs Anda dari serangan bot.
 * Konfigurasi aturan tembok api Cloudflare untuk mencegah akses dari IP yang mencurigakan.
9. Titik Akhir API ke Server X-Ray (Server Frontend)
Untuk menghubungkan sistem Anda dengan server X-Ray, Anda dapat mengatur titik akhir API sebagai berikut:
 * Buka konfigurasi API di aplikasi Anda.
 * Masukkan URL titik akhir X-Ray yang sesuai dengan server Anda.
 * Pastikan koneksi API diatur dengan benar.
10. Pemasangan Server Backend (Server Backend)
Penting: Jangan pasang backend pada server yang sama dengan frontend. Pastikan Anda memiliki server terpisah untuk backend.
Ikuti langkah-langkah berikut untuk memasang backend pada server terpisah:
 * Pastikan sistem Anda sudah diperbarui:
   apt install -y && apt update -y && apt upgrade -y

 * Pasang dependensi tambahan:
   apt install lolcat -y && gem install lolcat

 * Unduh dan jalankan skrip pemasangan:
   wget -q [https://raw.githubusercontent.com/raxnet/vpn/main/install.sh](https://raw.githubusercontent.com/raxnet/vpn/main/install.sh) && chmod +x install.sh && ./install.sh

11. Pembaruan Sistem (Server Backend)
Untuk memperbarui sistem, jalankan perintah berikut pada server backend:
wget [https://raw.githubusercontent.com/raxnet/vpn/main/update.sh](https://raw.githubusercontent.com/raxnet/vpn/main/update.sh) && chmod +x update.sh && ./update.sh

12. Dokumentasi dan Dukungan
 * Dokumentasi: Lihat Wiki
 * Laporkan Masalah: Buka Issue
 * Dukungan Email: support@raxnet.com
13. Terhubung dengan Kami
 * Twitter: Ikuti @raxnet
 * Facebook: Sukai @raxnet
 * GitHub: Bintangi Repo
Dengan mengikuti panduan ini, Anda dapat memasang dan mengonfigurasi Raxnet dengan aaPanel di Ubuntu dengan mudah. Jika Anda memiliki pertanyaan atau masalah, jangan ragu untuk menghubungi tim dukungan kami.

Perubahan utamanya adalah:

* Penekanan pada pemisahan server *frontend* dan *backend*.
* Instruksi yang lebih jelas untuk setiap langkah, termasuk di server mana perintah harus dijalankan.
* Penambahan peringatan penting untuk tidak memasang *backend* di server yang sama dengan *frontend*.

Semoga panduan ini lebih mudah diikuti dan bermanfaat bagi Anda.

