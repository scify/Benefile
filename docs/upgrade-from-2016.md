# Upgrade an installation from 2016

The first version of BeneFile (2016) ran on Laravel 5.1. This page is for an organisation that still runs it.

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

## What changed

- Laravel 5.1 → 13, PHP 5.5 → 8.3 or later. The structure is the structure of a new Laravel 13 application (`bootstrap/app.php`, `routes/web.php`).
- Login, registration and password reset are new controllers (`app/Http/Controllers/Auth`). The views stay the same.
- The views use `Form::text()` and others from laravelcollective/html, which is abandoned. `app/Support/Form.php` gives the same methods.
- Code for PHP 7: `legacy_count()` (`app/Support/helpers.php`) counts as PHP 7 did, and the MySQL connection is not strict (`config/database.php`), because the forms send empty dates as `''`. Empty fields stay `''` (`bootstrap/app.php`).

## Security fixes

- **Access:** every page needs a logged-in, active account. Each folder can be changed only by its profession (see [Roles and access](development.md#roles-and-access)). Deleting a file, the CSV import and export, users and places are for administrators only. Before, any logged-in user could delete a beneficiary, and the controller of `download-csv` had no access check.
- **SQL injection:** the search and the duplicate check put the values of the form in the SQL text. Now they use bound parameters.
- **Uploaded files:** the files of the medical visits were saved in `public/` with the name from the browser and with no check of their type, so a `.php` file could run on the server, and anybody could download the files by their address. Now they are in `storage/app/private/`, with a random name, only for common document and image types up to 10 MB, and only logged-in users download them (`MedicalUploadController`). The CSV import also stays out of `public/`.
- **Stored XSS:** a work title, which every user can add, ran as JavaScript in the reports page. Now it is escaped.
- **CSV injection:** the CSV exports escape quotes, and a value that starts with `=`, `+`, `-` or `@` gets a leading apostrophe, so that a spreadsheet does not run it as a formula.
- **Registration:** a new user could choose the administrator role. Now only the four professional roles are possible, and only an administrator activates an account.
- **Example accounts:** `migrate --seed` created two administrators with passwords that are public on GitHub. Now the example data is only for `local` and `testing`, and `benefile:create-admin` creates the first administrator.
- **Requests that change data** are POST requests with a CSRF token. Before, deleting, logging out and some changes were links.
- **Changes from other professions:** the basic information form was read-only for other roles, but the server saved it for every user. Now only social workers and administrators can save it. The new session button of the legal folder showed to psychologists, not to lawyers.
- **Ownership:** a medical visit takes its beneficiary from the address and its doctor from the login, not from the form. A change or a delete of a visit, a referral, an incident or a psychosocial session finds it only among the records of that beneficiary.
- **Data in the search:** the search sent every column of each beneficiary to the browser. Now it sends only the columns of its table.
- **Login** is limited to five failed attempts a minute for an email address and IP address. The password reset gives the same answer for every address.

## Known issues

- The views load Font Awesome and Google Fonts from other sites.
