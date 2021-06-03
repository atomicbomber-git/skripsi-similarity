# Setup

## Install PHP 8.0^
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

sudo apt-get install php8.0-fpm php8.0-cli \
php8.0-pgsql php8.0-sqlite3 php8.0-gd \
php8.0-curl \
php8.0-imap php8.0-mysql php8.0-mbstring \
php8.0-xml php8.0-zip php8.0-bcmath php8.0-soap \
php8.0-intl php8.0-readline php8.0-xdebug \
php-msgpack php-igbinary \
```

## Install Composer
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash\_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP\_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

sudo mv composer.phar /usr/bin/composer
```

## Install PostgreSQL

```bash

# Installation
sudo apt-get update
sudo apt-get install postgresql postgresql-contrib

# User setup
sudo -u postgres createuser --interactive
sudo -u postgres createdb username # The same as username in linux / unix-based systems

# Create database
psql -c "CREATE DATABASE similaritas_skripsi"
psql -c "CREATE EXTENSION pg_trgm"
```

## Compile smlar extension for PostgreSQL
### Install prerequisites for compiling smlar
```bash
sudo apt-get install postgresql-server-dev-12
```

### Compile and install smlar
```bash
git clone https://github.com/jirutka/smlar
cd smlar
sudo make install USE_PGXS=1
```

### Create the smlar extension at the database
```bash
psql -c "CREATE EXTENSION smlar" similaritas_skripsi
```

# Install composer requirements for project
```bash
cd project_dir
composer install
cp .env.example .env
```

Edit `.env` file inside project, add DB password:
```text
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=similaritas_skripsi
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

```bash
cd project_dir
php artisan key:generate
php artisan migrate:fresh --force
```

# Install Nginx, add server block with format, & restart Nginx:
```text
server {
    listen 80;
    server_name example.com;
    root /path/to/project;

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
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
