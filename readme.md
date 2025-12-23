Berikut adalah file **README.md** lengkap yang menggabungkan instruksi instalasi, penjelasan fitur (berdasarkan kode yang Anda bagikan sebelumnya), dan panduan penggunaan.

Silakan copy kode di bawah ini dan simpan sebagai file `README.md` di root folder project Anda.

````markdown
# 🚀 Laravel Livewire Member Management System

Aplikasi manajemen member berbasis **Laravel 10+** dan **Livewire 3**. Aplikasi ini memiliki fitur manajemen silsilah (Genealogy Tree) dengan berbagai mode tampilan, manajemen profil dengan upload foto, dan struktur hierarki member.

## 📋 Persyaratan Sistem (Prerequisites)

Pastikan software berikut sudah terinstall di komputer Anda sebelum memulai:

-   **PHP** 8.1 atau lebih baru
-   **Composer**
-   **Node.js** & **NPM**
-   **MySQL** / MariaDB
-   **Git**

---

## 🛠️ Panduan Instalasi (Step-by-Step)

Ikuti langkah-langkah berikut secara berurutan pada terminal/command prompt Anda.

### 1. Clone Repository

Unduh source code project ke komputer lokal Anda.

```bash
git clone [https://github.com/username-anda/repo-project.git](https://github.com/username-anda/repo-project.git)
cd nama-folder-project
```
````

### 2. Install Dependencies

Install library PHP (Laravel/Livewire) dan JavaScript (Tailwind/Alpine).

```bash
composer install
npm install

```

### 3. Konfigurasi Environment

Duplikasi file konfigurasi contoh `.env.example` menjadi `.env`.

```bash
cp .env.example .env

```

### 4. Setup Database

1. Buka file `.env` dengan text editor.
2. Cari bagian database dan sesuaikan dengan konfigurasi database lokal Anda:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda  # Pastikan database ini sudah dibuat di MySQL
DB_USERNAME=root
DB_PASSWORD=

```

### 5. Generate Application Key

Buat kunci enkripsi aplikasi.

```bash
php artisan key:generate

```

### 6. Migrasi & Seeding Database

Jalankan perintah ini untuk membuat tabel dan mengisi data awal (dummy data).

```bash
php artisan migrate --seed

```

### 7. Setup Storage (Penting!) ⚠️

Karena aplikasi ini menggunakan fitur **Upload Foto Profil**, Anda wajib membuat _symbolic link_ agar file di folder `storage` bisa diakses publik.

```bash
php artisan storage:link

```

### 8. Build Assets

Compile file CSS dan JS (Tailwind/Vite).

```bash
npm run build

```

_(Gunakan `npm run dev` jika Anda ingin mengembangkan tampilan secara real-time)._

### 9. Jalankan Server

Jalankan server lokal Laravel.

```bash
php artisan serve

```

Aplikasi sekarang dapat diakses di: [http://127.0.0.1:8000](https://www.google.com/search?q=http://127.0.0.1:8000)

---

## ✨ Fitur Utama

### 1. Visualisasi Tree Member (Silsilah)

Aplikasi mendukung 3 mode tampilan untuk melihat struktur member:

-   **List View:** Tampilan daftar standar dengan detail lengkap.
-   **Chart View:** Tampilan diagram pohon silsilah horizontal.
-   **Tree View:** Tampilan hierarki folder (seperti File Explorer) yang ringkas.

### 2. Manajemen Member (CRUD)

-   Tambah Member (Child) di bawah parent tertentu.
-   Edit data Member.
-   Hapus Member.
-   Detail Member (Modal Card).

### 3. Manajemen Profil Diri

-   Update informasi pribadi (Nama, NIK, Alamat, dll).
-   Ganti Password (dengan fitur keamanan auto-reset field).
-   Upload & Preview Foto Profil.

---

## ❓ Kendala Umum (Troubleshooting)

**1. Gambar Profil tidak muncul (404 Not Found)**

-   Pastikan Anda sudah menjalankan `php artisan storage:link`.
-   Jika masih error, hapus folder `public/storage` lalu jalankan perintah link ulang.

**2. Tampilan berantakan / Style hilang**

-   Jalankan `npm run build` untuk me-regenerate file CSS/JS.
-   Hapus cache config dengan: `php artisan optimize:clear`.

**3. Password tidak hilang setelah update profil**

-   Pastikan browser tidak melakukan _autofill_. Fitur `wire:key` dinamis sudah diterapkan untuk menangani hal ini.

---

## 🔒 Login Default (Jika menggunakan Seeder)

Jika Anda menjalankan `db:seed`, silakan cek file `database/seeders/DatabaseSeeder.php` untuk melihat akun default, biasanya:

-   **Email:** `admin@example.com` (atau sesuai seeder)
-   **Password:** `password`

---

```

```

Berikut adalah panduan lengkap **DEPLOYMENT_GUIDE.md** untuk melakukan setup server Ubuntu dari nol hingga aplikasi Laravel Livewire Anda bisa diakses menggunakan domain.

Panduan ini menggunakan stack: **Ubuntu 22.04/24.04**, **Nginx**, **MySQL**, **PHP 8.3**, dan **Node.js**.

Silakan simpan teks di bawah ini ke dalam file `DEPLOYMENT.md` di project Anda.

````markdown
# 🚀 Deployment Guide: Laravel Livewire on Ubuntu Server

Panduan langkah demi langkah untuk men-deploy aplikasi ke VPS (Ubuntu 22.04/24.04) menggunakan Nginx, MySQL, PHP 8.3, dan Node.js.

## 📋 Prasyarat

1.  Akses **SSH** ke server Ubuntu (sebagai `root` atau user dengan akses `sudo`).
2.  **Domain** yang sudah diarahkan (A Record) ke IP Address server VPS Anda.

---

## 🛠️ Tahap 1: Persiapan Server

Update repository dan paket sistem agar dalam kondisi terbaru.

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install git curl unzip zip software-properties-common -y
```
````

---

## 🛠️ Tahap 2: Install Nginx (Web Server)

Install Nginx dan atur firewall.

```bash
sudo apt install nginx -y
sudo ufw allow 'Nginx Full'

```

---

## 🛠️ Tahap 3: Install & Setup MySQL

1. **Install MySQL Server:**

```bash
sudo apt install mysql-server -y

```

2. **Amankan Instalasi:**
   Jalankan perintah ini dan ikuti instruksi (pilih level validasi password sesuai kebutuhan, jawab 'Y' untuk opsi keamanan lainnya).

```bash
sudo mysql_secure_installation

```

3. **Buat Database & User:**
   Masuk ke MySQL:

```bash
sudo mysql

```

Jalankan query SQL berikut (ganti `db_name`, `db_user`, dan `password`):

```sql
CREATE DATABASE app_db;
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'password_rahasia_anda';
GRANT ALL PRIVILEGES ON app_db.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

```

---

## 🛠️ Tahap 4: Install PHP 8.3

Ubuntu default mungkin belum memiliki PHP 8.3, jadi kita gunakan PPA dari Ondřej Surý.

1. **Tambahkan Repository PHP:**

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

```

2. **Install PHP 8.3 dan Ekstensi Laravel:**

```bash
sudo apt install php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-intl php8.3-gd -y

```

---

## 🛠️ Tahap 5: Install Composer

```bash
curl -sS [https://getcomposer.org/installer](https://getcomposer.org/installer) | php
sudo mv composer.phar /usr/local/bin/composer

```

---

## 🛠️ Tahap 6: Install Node.js & NPM

Kita gunakan versi LTS terbaru (misal v20.x) untuk compile assets (Tailwind/Vite).

```bash
curl -fsSL [https://deb.nodesource.com/setup_20.x](https://deb.nodesource.com/setup_20.x) | sudo -E bash -
sudo apt install -y nodejs

```

---

## 🚀 Tahap 7: Setup Aplikasi Laravel

1. **Clone Repository:**
   Masuk ke folder web root.

```bash
cd /var/www
sudo git clone [https://github.com/username-anda/repo-anda.git](https://github.com/username-anda/repo-anda.git) nama-domain.com
cd nama-domain.com

```

2. **Install Dependencies:**

```bash
composer install --no-dev --optimize-autoloader
npm install

```

3. **Konfigurasi Environment (.env):**

```bash
cp .env.example .env
nano .env

```

_Ubah setting berikut:_

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=[https://nama-domain.com](https://nama-domain.com)

DB_DATABASE=app_db
DB_USERNAME=app_user
DB_PASSWORD=password_rahasia_anda

```

4. **Generate Key & Migrate:**

```bash
php artisan key:generate
php artisan migrate --seed --force

```

5. **Build Assets (Vite):**

```bash
npm run build

```

6. **Setup Storage & Permissions (PENTING!):**
   Agar fitur upload foto berfungsi dan server bisa membaca file.

```bash
php artisan storage:link

# Atur kepemilikan folder ke user web server (www-data)
sudo chown -R www-data:www-data /var/www/nama-domain.com

# Atur hak akses folder storage dan cache
sudo chmod -R 775 /var/www/[nama-domain.com/storage](https://nama-domain.com/storage)
sudo chmod -R 775 /var/www/[nama-domain.com/bootstrap/cache](https://nama-domain.com/bootstrap/cache)

```

---

## 🌐 Tahap 8: Konfigurasi Nginx

1. **Buat File Konfigurasi:**

```bash
sudo nano /etc/nginx/sites-available/nama-domain.com

```

2. **Isi Konfigurasi:**
   Copy-paste konfigurasi di bawah ini (Ganti `nama-domain.com` dengan domain asli Anda):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name nama-domain.com [www.nama-domain.com](https://www.nama-domain.com);
    root /var/www/[nama-domain.com/public](https://nama-domain.com/public);

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Konfigurasi Upload Size (Opsional, sesuaikan kebutuhan)
    client_max_body_size 10M;
}

```

3. **Aktifkan Konfigurasi:**

```bash
sudo ln -s /etc/nginx/sites-available/nama-domain.com /etc/nginx/sites-enabled/

```

4. **Test & Restart Nginx:**

```bash
sudo nginx -t
# Jika "Syntax OK", restart nginx
sudo systemctl restart nginx

```

---

## 🔒 Tahap 9: Setup SSL (HTTPS) | ATAU BISA MENGGUNAKAN CLOUDFLARE

Gunakan Certbot (Let's Encrypt) agar website aman (gembok hijau).

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d nama-domain.com -d [www.nama-domain.com](https://www.nama-domain.com)

```

_Ikuti instruksi di layar (masukkan email, setuju TOS). Pilih opsi redirect HTTP to HTTPS jika ditanya._

---

## ✅ Selesai!

Sekarang buka browser dan akses **https://www.google.com/url?sa=E&source=gmail&q=https://nama-domain.com**. Aplikasi Livewire Anda sudah online!

### 🔄 Update Aplikasi di Masa Depan

Jika Anda melakukan perubahan kode di repo (push), jalankan perintah ini di server:

```bash
cd /var/www/nama-domain.com
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

```

```

```
