# Install BeneFile

## Requirements

- PHP 8.3 or later, with the extensions `pdo_mysql`, `mbstring`, `fileinfo` and `openssl`
- MySQL 8 (or MariaDB 10.6 or later)
- Composer 2
- A web server whose document root is `public/`, with HTTPS

The application is Laravel. Its pages use the files in `public/` (Bootstrap 3 and plain JavaScript): there is no front-end build.

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

## The organisation

The screens show the name, the logo and the contact page of the organisation that uses BeneFile. The defaults are those of PRAKSIS. To change them, set in `.env`:

| Key | Value |
|-----|-------|
| `BENEFILE_ORGANISATION_NAME` | The name, for example in "Supplied by …" for a medicine. |
| `BENEFILE_ORGANISATION_LOGO` | The logo, a path under `public/`. |
| `BENEFILE_ORGANISATION_CONTACT_URL` | The contact page for users without access. Empty: no link. |

`APP_LOCALE` is the language before a user chooses one (`gr` or `en`).

The web server must be able to write to `storage/` and `bootstrap/cache/`. Make backups of the database and of `storage/app/private/` (the files of the medical visits).

The first administrator activates the other users. [Roles and access](development.md#roles-and-access) lists what each role can do.
