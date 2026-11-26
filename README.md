# BeneFile

BeneFile keeps the medical, legal and psychosocial records of the beneficiaries of an organisation in one digital file. Doctors, lawyers, social workers and psychologists work on the same file, each in their own folder. SciFY developed it in 2016, with feedback from the NGO PRAKSIS. The interface is in Greek.

Website: https://scify.github.io/Benefile/ (the `gh-pages` branch).

## Requirements

- PHP 8.3 or later, with the extensions `pdo_mysql`, `mbstring`, `fileinfo` and `openssl`
- MySQL 8 (or MariaDB 10.6 or later)
- Composer 2
- A web server whose document root is `public/`, with HTTPS

The application is Laravel 13. Its pages use the files in `public/` (Bootstrap 3, jQuery): there is no front-end build.

## Install

```shell
git clone https://github.com/scify/Benefile.git
cd Benefile
composer install --no-dev --optimize-autoloader
cp .env.example .env            # Then set APP_URL, the database and the mail server.
php artisan key:generate
php artisan migrate --seed --force
php artisan benefile:create-admin
```

- `migrate --seed` creates the tables and the lists of values (roles, countries, ICD-10 codes …). On `APP_ENV=production` it adds no users and no example data.
- `benefile:create-admin` asks for the name, the email and the password of the first administrator.
- Then your colleagues register at `/auth/register`, and the administrator activates each one in *Χρήστες* (Users). A new account has no access until then.

For production, set in `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, `SESSION_SECURE_COOKIE=true`, and the mail settings (`MAIL_*`), which the password reset uses.

The web server must be able to write to `storage/` and `bootstrap/cache/`. Make backups of the database and of `storage/app/private/` (the files of the medical visits).

## Roles and access

| Role | Can change |
|------|------------|
| Administrator | Everything. Activates and deactivates users, manages places of examination, imports and exports CSV files, deletes files. |
| Doctor | Medical visits. Each doctor has a specialty. |
| Lawyer | The legal folder and the legal sessions. |
| Social worker | The social history in the psychosocial folder. |
| Psychologist | The psychosocial sessions. Their notes are visible only to psychologists and administrators. |

Every active user can read every folder and change the basic information. `routes/web.php` has the rules.

## Upgrade an installation from 2016 (Laravel 5.1)

The tables do not change: the new version works on the existing database. Before you start, make a backup of the database and of `public/uploads/`.

1. Install PHP 8.3 or later and deploy the new code. Run `composer install --no-dev --optimize-autoloader`.
2. Make a new `.env` from `.env.example`. Copy the database settings and `APP_KEY` from the old `.env`. Keep `AUTH_PASSWORD_RESET_TOKEN_TABLE=password_resets`.
3. Move the files of the medical visits out of the public folder:
   ```shell
   mkdir -p storage/app/private/medical-uploads
   mv public/uploads/medical-visit-uploads/* storage/app/private/medical-uploads/
   rm -rf public/uploads
   ```
   The old files are still found by their title. New files get a random name.
4. Run `php artisan optimize`. Everyone logs in again.
5. Check the accounts in *Χρήστες*. Old installations may have administrators with the passwords of the example data (`UsersTableSeeder` of 2016): change those passwords, or deactivate those accounts.

## Development

The project includes nothing for a specific local environment. With [DDEV](https://ddev.com):

```shell
ddev config --project-type=laravel --docroot=public --php-version=8.4 --database=mysql:8.0
ddev start
ddev composer install
cp .env.example .env   # DB_HOST=db, DB_DATABASE=db, DB_USERNAME=db, DB_PASSWORD=db
ddev artisan key:generate
ddev artisan migrate --seed
```

With `APP_ENV=local`, `migrate --seed` also adds example users and beneficiaries (`database/seeders/Test*`). Their passwords are in the seeders: never use them on a real installation.

### Tests

The tests use a MySQL database named `testing`, because the queries of BeneFile use MySQL functions. Create it once, then run the tests:

```shell
ddev mysql -uroot -proot -e "CREATE DATABASE testing; GRANT ALL ON testing.* TO 'db'@'%';"
ddev artisan test
```

`tests/Feature` checks the access rules, login and registration, the password reset, SQL injection, the medical files, the CSV exports and the reports.

### Static analysis

[Larastan](https://github.com/larastan/larastan) checks the code at level 5 (`phpstan.neon`):

```shell
ddev composer analyse
```

Raise the level step by step: level 6 asks for the types of all parameters and return values.

## What changed in the upgrade to Laravel 13

- Laravel 5.1 → 13, PHP 5.5 → 8.3 or later. The structure is the structure of a new Laravel 13 application (`bootstrap/app.php`, `routes/web.php`).
- Login, registration and password reset are new controllers (`app/Http/Controllers/Auth`). The views stay the same.
- The views use `Form::text()` and others from laravelcollective/html, which is abandoned. `app/Support/Form.php` gives the same methods.
- Code for PHP 7: `legacy_count()` (`app/Support/helpers.php`) counts as PHP 7 did, and the MySQL connection is not strict (`config/database.php`), because the forms send empty dates as `''`. Empty fields stay `''` (`bootstrap/app.php`).

### Security fixes

- **Access:** every page needs a logged-in, active account. Each folder can be changed only by its profession (see "Roles and access"). Deleting a file, the CSV import and export, users and places are for administrators only. Before, any logged-in user could delete a beneficiary, and the controller of `download-csv` had no access check.
- **SQL injection:** the search and the duplicate check put the values of the form in the SQL text. Now they use bound parameters.
- **Uploaded files:** the files of the medical visits were saved in `public/` with the name from the browser and with no check of their type, so a `.php` file could run on the server, and anybody could download the files by their address. Now they are in `storage/app/private/`, with a random name, only for common document and image types up to 10 MB, and only logged-in users download them (`MedicalUploadController`). The CSV import also stays out of `public/`.
- **Stored XSS:** a work title, which every user can add, ran as JavaScript in the reports page. Now it is escaped.
- **CSV injection:** the CSV exports escape quotes, and a value that starts with `=`, `+`, `-` or `@` gets a leading apostrophe, so that a spreadsheet does not run it as a formula.
- **Registration:** a new user could choose the administrator role. Now only the four professional roles are possible, and only an administrator activates an account.
- **Example accounts:** `migrate --seed` created two administrators with passwords that are public on GitHub. Now the example data is only for `local` and `testing`, and `benefile:create-admin` creates the first administrator.
- **Requests that change data** are POST requests with a CSRF token. Before, deleting, logging out and some changes were links.
- **Login** is limited to five failed attempts a minute for an email address and IP address. The password reset gives the same answer for every address.

### Known issues

- Three pages show a JavaScript error in the browser console, as before the upgrade: the new beneficiary page (`init` is not defined), the CSV import (`progressbar`) and the reports (a chart canvas). The pages work.
- The views load Font Awesome and Google Fonts from other sites.

## Licence

Apache License 2.0. See [licence](licence).
