Berikut adalah file **DEPLOYMENT.md** lengkap yang sudah digabungkan. Panduan ini mencakup instalasi stack server (Nginx, PHP 8.3, MySQL, Node.js), deployment aplikasi Laravel, SSL, dan **setup PHPMyAdmin** yang aman.

Simpan file ini di root project Anda.

```markdown
# 🚀 VPS Deployment Guide (Ubuntu 22.04/24.04)

**Stack:** Nginx, MySQL, PHP 8.3, Node.js 20.x, PHPMyAdmin

## 1. Install System Stack
Jalankan perintah berikut blok demi blok (sebagai `root` atau user `sudo`).

```bash
# 1. Update & Install Basic Tools, Nginx, MySQL, PHPMyAdmin
sudo apt update && sudo apt upgrade -y
sudo apt install -y git curl unzip zip nginx mysql-server

# 2. Install PHP 8.3 & Extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
php8.3-bcmath php8.3-curl php8.3-zip php8.3-intl php8.3-gd

# 3. Install Composer
curl -sS [https://getcomposer.org/installer](https://getcomposer.org/installer) | php
sudo mv composer.phar /usr/local/bin/composer

# 4. Install Node.js 20.x & NPM
curl -fsSL [https://deb.nodesource.com/setup_20.x](https://deb.nodesource.com/setup_20.x) | sudo -E bash -
sudo apt install -y nodejs

```

## 2. Setup Database (MySQL)

Masuk ke MySQL dan buat database aplikasi.

```bash
sudo mysql

```

Jalankan query SQL (Ganti `db_name`, `user`, dan `password`):

```sql
CREATE DATABASE nama_db;
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'password_rahasia';
GRANT ALL PRIVILEGES ON app_db.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

```

## 3. Setup PHPMyAdmin (Opsional)

Install PHPMyAdmin agar bisa mengelola database via browser.

1. **Install Paket:**
```bash
sudo apt install phpmyadmin -y

```


> **PENTING:** Saat muncul layar konfigurasi:


> 1. **Web server:** JANGAN PILIH APA-APA (Kosongkan Apache/Lighttpd). Tekan **TAB** -> **OK**.
> 2. **Configure database:** Pilih **Yes**.
> 3. Masukkan password untuk user phpmyadmin.
> 
> 


2. **Buat Akses Rahasia (Symlink):**
Kita akan membuat link ke folder public Laravel agar bisa diakses, tapi dengan nama samaran agar tidak mudah ditebak hacker.
*(Ganti `domain.com` dan `secret-db-admin` sesuai keinginan)*.
```bash
# Nanti dijalankan setelah clone project (Langkah 4), tapi catat perintahnya:
# sudo ln -s /usr/share/phpmyadmin /var/www/[domain.com/public/secret-db-admin](https://domain.com/public/secret-db-admin)

```



## 4. Setup Aplikasi Laravel


```bash
# 1. Clone Repo
sudo git clone (Link Repository)
cd domain.com

# 2. Install Dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Environment Setup
cp .env.example .env
nano .env
# -> Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL
# -> Set APP_ENV=production, APP_DEBUG=false

# 4. Migrate & Storage
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link

# 5. Aktifkan PHPMyAdmin (Langkah dari tahap 3 tadi)
# Ganti 'secret-db-admin' dengan nama unik pilihan Anda
sudo ln -s /usr/share/phpmyadmin /var/www/[domain.com/public/secret-db-admin](https://domain.com/public/secret-db-admin)

# 6. Permission (Wajib agar upload foto jalan)
sudo chown -R www-data:www-data /var/www/domain.com
sudo chmod -R 775 storage bootstrap/cache

```

## 5. Setup Nginx

Buat file config baru:

```bash
sudo nano /etc/nginx/sites-available/nama-domain.com

```

Paste konfigurasi berikut:

```nginx
server {
    listen 80;
    server_name domain.com www.domain.com;
    root /[nama-folder-hasil-clone]/public;

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

    # Handle PHP Files
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
    
    # Upload Size Limit (Sesuaikan kebutuhan)
    client_max_body_size 10M;
}

```

Aktifkan dan restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/domain.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

```

## 6. Setup SSL (HTTPS)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d domain.com -d [www.domain.com](https://www.domain.com)

```

---

## ✅ Selesai!

1. **Aplikasi:** Akses `https://domain.com`
2. **PHPMyAdmin:** Akses `https://domain.com/secret-db-admin` (Gunakan user database yang dibuat di Tahap 2).

---

## 🔄 Cara Update Aplikasi (Redeploy)

Jika ada update code dari git, jalankan perintah ini di server:

```bash
cd /var/www/domain.com
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Reset permission jika perlu
sudo chown -R www-data:www-data /var/www/domain.com
sudo systemctl reload nginx

```

```

```