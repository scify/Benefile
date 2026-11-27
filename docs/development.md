# Develop BeneFile

## A local copy

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

## Tests

The tests use a MySQL database named `testing`, because the queries of BeneFile use MySQL functions. Create it once, then run the tests:

```shell
ddev mysql -uroot -proot -e "CREATE DATABASE testing; GRANT ALL ON testing.* TO 'db'@'%';"
ddev artisan test
```

`tests/Feature` checks the access rules, login and registration, the password reset, SQL injection, the medical files, the save of each folder, the CSV exports and the reports. `tests/Unit` checks the dates.

## Front-end libraries

The pages use no jQuery. The CSS is Bootstrap 3 (`public/bootstrap-3.3.6/dist/css`). `public/js/ui.js` gives the Bootstrap 3 data attributes of the dialogs, tabs, sections and menus (`data-toggle`, `data-target`, `data-dismiss`).

The other libraries are copies of npm packages in `public/vendor`, with exact versions in `package.json`: simple-datatables (tables), Tom Select (lists with search), vanillajs-datepicker (calendars) and Dropzone (CSV import). `public/vendor/README.md` tells how to update them. There is no build step.

simple-datatables has the LGPL-3.0 licence. BeneFile uses an unchanged copy, which the LGPL allows.

The scripts of the pages are in `public/js`, in plain JavaScript (ES2020). Their texts come from the language files (`lang/gr`, `lang/en`), through the page. The browser tests (`tests/e2e`) open every screen with each role, in both languages, and check each behaviour of the scripts. A screenshot of each screen goes to `storage/playwright-screens/`:

```shell
npm install
BASE_URL=https://benefile.ddev.site npx playwright test
```

## Code structure

- `app/Http/Controllers/Folders`: one controller for each folder (basic information, medical, legal, psychosocial) and for the beneficiary.
- `app/Services`: the work of the controllers, for example `BeneficiaryRegistrar` (a new beneficiary and its folders, in one transaction), `BenefiterMedicalFolderService` (the medical visits), `FolderHistoryService` and `BeneficiaryCsvExporter`.
- `app/Models`: the tables. The models of the legal and psychosocial folders are in `app/Models/Folders`.
- `resources/views/components`: `<x-beneficiary-header>` and `<x-form-messages>`, which the folders share.
- `app/Support/Form.php`: `Form::text()` and the other form helpers that the views use.
- `legacy_count()` (`app/Support/helpers.php`) counts as PHP 7 did. The MySQL connection is not strict (`config/database.php`), because the forms send empty dates as `''`. Empty fields stay `''` (`bootstrap/app.php`).

## Static analysis

[Larastan](https://github.com/larastan/larastan) checks the code at level 5 (`phpstan.neon`):

```shell
ddev composer analyse
```

Raise the level step by step: level 6 asks for the types of all parameters and return values.

## Roles and access

| Role | Can change |
|------|------------|
| Administrator | Everything. Activates and deactivates users, manages places of examination, imports and exports CSV files, deletes files. |
| Doctor | Medical visits. Each doctor has a specialty. |
| Lawyer | The legal folder and the legal sessions. |
| Social worker | The basic information, new beneficiaries, and the social history in the psychosocial folder. |
| Psychologist | The psychosocial sessions. Their notes are visible only to psychologists and administrators. |

Every active user can read every folder, and add referrals and incidents in the basic information. The abilities are in `AppServiceProvider::ABILITIES`. The routes (`routes/web.php`) and the views use the same abilities, for example `->can('edit-legal-folder')` and `Auth::user()->can('edit-legal-folder')`.
