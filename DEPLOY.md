# Deploying exams to the VPS (Apache2, no Docker)

Target: **https://exams.futurecampus.in**, served from `/var/www/html/exams` by
the server's existing **PHP 8.3**, the same PHP the two other sites use. Tested in
Docker on PHP 8.3 + MySQL 8.0 (case-sensitive tables).

Commands assume Ubuntu/Debian and a sudo user. Replace `CHANGE_ME` values.

## 0. Before you start

- **DNS:** add an `A` record `exams` → the VPS IP, and wait until
  `ping exams.futurecampus.in` resolves to it.
- **Note what's running now**, so you can confirm nothing else changed:
  ```bash
  apache2ctl -S                 # existing vhosts
  ls /etc/php/                  # installed PHP versions
  apache2ctl -M | grep -i php   # mod_php in use?
  ```

## 1. PHP 8.3 extensions

PHP 8.3 is already installed. Add any extensions exams needs that are missing
(apt skips ones already installed; adding extensions doesn't change the other sites):

```bash
sudo apt update
sudo apt install -y php8.3-mysql php8.3-gd php8.3-zip php8.3-intl \
  php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath
sudo a2enmod rewrite headers
```

Then check how Apache runs PHP:

```bash
apache2ctl -M | grep -iE "php|proxy_fcgi"
```

- `php_module` listed: **mod_php**. The vhost already sets this site's PHP limits. Nothing more to do here.
- Only `proxy_fcgi_module`: **PHP-FPM**. Uncomment the `FilesMatch` block in
  `deploy/exams.futurecampus.in.conf`, and optionally copy
  `deploy/php-exams.ini` to `/etc/php/8.3/fpm/conf.d/99-exams.ini` (read its note first).

## 2. Database (on the server's existing MySQL)

Use a separate database and user. Don't share futurecampus's database.

MySQL's root user has a password here, so plain `sudo mysql` is refused. Use
`mysql -u root -p` if you know it. Otherwise use Ubuntu's maintenance login
`mysql --defaults-file=/etc/mysql/debian.cnf` in place of `sudo mysql` below.

```bash
sudo mysql -e "
CREATE DATABASE exams CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'exams'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON exams.* TO 'exams'@'localhost';"
```

Upload `docker/db/exams-deploy.sql` (git-ignored, so copy it with `scp`) and import it:

```bash
mysql -uexams -p exams < exams-deploy.sql
rm exams-deploy.sql      # contains real data
```

This one file is the exams build's database. Non-exam permissions and modules
are already removed, and `view_student_details` is created without a DEFINER or
a database name, so a normal (non-root) user can import it into any database name.
It was tested this way. Expected after import: 154 tables/views, `permission` = 55 rows.

**Check the SQL mode** (the app was tested with `STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION`):

```bash
mysql -e "SELECT @@global.sql_mode"
```

If it contains `ONLY_FULL_GROUP_BY` (the MySQL 8 default), some report queries
may fail. Don't change it globally without checking the other two sites. Tell
Claude/the developer and set it for this app's connection only.

## 3. Code

```bash
sudo git clone https://github.com/techiemithlesh/futurecampus /var/www/html/exams
cd /var/www/html/exams
sudo git checkout <branch-with-exams-changes>
```

Upload the files that aren't in git:

| What | From your PC |
|---|---|
| `application/config/database.php` | create it, see below |
| `uploads/` (only part of it is tracked) | `rsync -av uploads/ user@VPS:/var/www/html/exams/uploads/` |

`application/config/database.php` on the server:

```php
'hostname' => 'localhost',
'username' => 'exams',
'password' => 'CHANGE_ME_STRONG_PASSWORD',
'database' => 'exams',
```

(Keep the rest of the file as it is in the repo.)

Permissions:

```bash
sudo chown -R www-data:www-data /var/www/html/exams/uploads /var/www/html/exams/application/logs /var/www/html/exams/application/cache
sudo find /var/www/html/exams -type d -exec chmod 755 {} \;
sudo find /var/www/html/exams -type f -exec chmod 644 {} \;
```

## 4. Apache site + HTTPS

```bash
sudo cp deploy/exams.futurecampus.in.conf /etc/apache2/sites-available/
sudo a2ensite exams.futurecampus.in
sudo apache2ctl configtest            # must say "Syntax OK" before reloading
sudo systemctl reload apache2         # reload, not restart: no downtime for other sites
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d exams.futurecampus.in   # choose "redirect" to force HTTPS
```

## 5. Cron jobs

Cron URLs and the secret key are shown in the admin panel: **Settings → Cron Job**.
Add them with `crontab -e`, for example:

```
*/5 * * * * curl -s https://exams.futurecampus.in/cron_api/send_smsemail_command/SECRET_KEY >/dev/null
```

## 6. Check

- https://exams.futurecampus.in loads the login page with styling, and http redirects to https.
- A student login shows only Online Exam, Live Exam, Reports, Coins.
- The **Coins** page opens without an error (this proves the view import worked).
- `sudo tail -f /var/log/apache2/exams_error.log` stays quiet.
- The other two sites still load.

## Updating later

```bash
cd /var/www/html/exams && sudo git pull
sudo systemctl reload apache2      # mod_php (use: sudo systemctl reload php8.3-fpm if on FPM)
```

Test changes in Docker first (`DOCKER.md`). Its PHP/MySQL versions, extensions and
case-sensitive table names match this server.
