<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The lists of yes/no values that the forms use.
 */
class YesNoLookupsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('binary_lookup')->insert(
            array(
                array(
                    'id' => 0,
                    'description' => 'όχι',
                ),
                array(
                    'id' => 1,
                    'description' => 'ναι',
                ),
            )
        );

        DB::table('working_legally_lookup')->insert(
            array(
                array('description' => 'Νόμιμη'),
                array('description' => 'Παράνομη'),
            )
        );

        // TESTING SEEDS FOR PRESENTATION
    }
}
