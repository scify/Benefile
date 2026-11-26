<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Model::unguard();

        // The lists of values: every installation needs them.
        $this->call([
            UsersRolesSeeder::class,
            UsersSubrolesSeeder::class,
            GendersSeeder::class,
            LanguageSeeder::class,
            ReferenceSeeder::class,
            MaritalStatusSeeder::class,
            EducationTableSeeder::class,
            LegalStatusTableSeeder::class,
            SocialFolderSeeder::class,
            WorkTitleListLookupSeeder::class,
            MedicalExaminaitonsSeeder::class,
            MedicalLocationSeeder::class,
            MedicalIncidentTypesSeeders::class,
            ICDSeeder::class,
            LegalFolderTableSeeder::class,
            CountriesLookupSeeder::class,
            CountryAbandonReasonsTableSeeder::class,
            YesNoLookupsSeeder::class,
        ]);

        // Example users and beneficiaries, for development and tests only. Their
        // passwords are public: never add them to a real installation. The first
        // administrator of a real installation: php artisan benefile:create-admin
        if (app()->environment('local', 'testing')) {
            $this->call([
                UsersTableSeeder::class,
                TestUsersTableSeeder::class,
                TestBenefitersSeeder::class,
            ]);
        }

        Model::reguard();
    }
}
