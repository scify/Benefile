<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Seeder;
use App\Models\User as User;

class TestUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // TEST USERS (WAIT TO BE ACTIVE)
        User::create(array(
            'name' => 'Doc_N_1',
            'lastname' => 'Doct_L_1',
            'email' => 'user3@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 2,
            'user_subrole_id' => 3,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        User::create(array(
            'name' => 'Legal_Adv_Name_2',
            'lastname' => 'Legal_Adv_Lastname_2',
            'email' => 'user4@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 3,
            'user_subrole_id' => null,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        User::create(array(
            'name' => 'Social_Adv_Name_3',
            'lastname' => 'Social_Adv_Lastname_3',
            'email' => 'user5@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 4,
            'user_subrole_id' => null,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        User::create(array(
            'name' => 'Psycho_Name_4',
            'lastname' => 'Psycho_Lastname_4',
            'email' => 'user6@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 5,
            'user_subrole_id' => null,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));


        //---------------------------------------------------------------


        // TEST USERS (ACTIVE)
        User::create(array(
            'name' => 'Doc_N_2',
            'lastname' => 'Doc_L_2',
            'email' => 'user7@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 2,
            'user_subrole_id' => 8,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        User::create(array(
            'name' => 'Legal_Adv_Name_2',
            'lastname' => 'Legal_Adv_Lastname_2',
            'email' => 'user8@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 3,
            'user_subrole_id' => null,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        User::create(array(
            'name' => 'Social_Adv_Name_3',
            'lastname' => 'Social_Adv_Lastname_3',
            'email' => 'user9@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 4,
            'user_subrole_id' => null,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        User::create(array(
            'name' => 'Psycho_Name_4',
            'lastname' => 'Psycho_Lastname_4',
            'email' => 'user10@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 5,
            'user_subrole_id' => null,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ));

        // ----------------------------------------------


        // TEST USERS (DEACTIVATED)
        User::create(array(
            'name' => 'Doc_N_3',
            'lastname' => 'Doc_L_3',
            'email' => 'user11@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 2,
            'user_subrole_id' => 6,
            'activation_status' => 0,
            'is_deactivated' => 1,
        ));

        User::create(array(
            'name' => 'Legal_Adv_Name_2',
            'lastname' => 'Legal_Adv_Lastname_2',
            'email' => 'user12@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 3,
            'user_subrole_id' => null,
            'activation_status' => 0,
            'is_deactivated' => 1,
        ));

        User::create(array(
            'name' => 'Social_Adv_Name_3',
            'lastname' => 'Social_Adv_Lastname_3',
            'email' => 'user13@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 4,
            'user_subrole_id' => null,
            'activation_status' => 0,
            'is_deactivated' => 1,
        ));

        User::create(array(
            'name' => 'Psycho_Name_4',
            'lastname' => 'Psycho_Lastname_4',
            'email' => 'user14@example.test',
            'password' => Hash::make('1234qwer'),
            'user_role_id' => 5,
            'user_subrole_id' => null,
            'activation_status' => 0,
            'is_deactivated' => 1,
        ));
    }
}
