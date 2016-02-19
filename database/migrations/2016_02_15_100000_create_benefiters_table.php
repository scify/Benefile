<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBenefitersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benefiters', function (Blueprint $table) {
            $table->increments('id');
            // Insert user that created the benefiter's file.
            $table->string('folder_number');
            $table->string('name');
            $table->string('lastname');
            $table->string('fathers_name');
            $table->string('mothers_name');
            $table->date('birth_date');
            $table->date('arrival_date');
            $table->string('address');
            $table->string('telephone')->nullable();
            $table->integer('number_of_children')->unsigned()->nullable();
            $table->text('relatives_residence')->nullable();
            $table->text('other_language')->nullable();
            $table->boolean('language_interpreter_needed');
            $table->boolean('is_benefiter_working')->nullable();
            $table->string('legal_status_details')->nullable();
            $table->boolean('working_legally')->nullable();
            $table->text('country_abandon_reason')->nullable();
            $table->text('travel_route')->nullable();
            $table->text('travel_duration')->nullable();
            $table->text('detention_duration')->nullable();
            $table->boolean('has_educational_reference');
            $table->text('educational_reference_actions');
            $table->date('educational_reference_date');
            // TODO: Check if below is needed.
            // $table->string('social_history');
            $table->string('origin_country');
            $table->string('nationality_country');
            // Insert user that created the benefiter's file.
            $table->integer('document_manager_id');
            $table->text('social_history');
            $table->timestamps();

            // Foreign keys
            $table->integer('gender_id')->unsigned()->nullable();
            $table->foreign('gender_id')->references('id')->on('genders_lookup');
            // Disable country table for now, insert country as string.
            /*
             * $table->integer('origin_country_id')->unsigned()->nullable();
             * $table->foreign('origin_country_id')->references('id')->on('countries_lookup');
             * $table->integer('nationality_country_id')->unsigned()->nullable();
             * $table->foreign('nationality_country_id')->references('id')->on('countries_lookup');
             */
            $table->integer('marital_status_id')->unsigned()->nullable();
            $table->foreign('marital_status_id')->references('id')->on('marital_status_lookup');
            $table->integer('education_id')->unsigned()->nullable();
            $table->foreign('education_id')->references('id')->on('education_lookup');
            // Lookup table for work field.
            $table->integer('work_title_id')->unsigned()->nullable();
            $table->foreign('work_title_id')->references('id')->on('work_title_list_lookup');
        });

        // Lookup for 'Legal Status' in basic info form.
        Schema::create('legal_status_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        // Middle table for 'Legal Status' in basic info form. Every benefiter may have more than one legal status.
        Schema::create('benefiters_legal_status', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description')->nullable();
            $table->date('exp_date');

            $table->integer('benefiter_id')->unsigned()->nullable();
            $table->foreign('benefiter_id')->references('id')->on('benefiters');
            $table->integer('legal_lookup_id')->unsigned()->nullable();
            $table->foreign('legal_lookup_id')->references('id')->on('legal_status_lookup');
        });

        // Social/legal/etc tables should be independent from main table.
        // Every user class has different permissions and different tables make this process easier.

        /*
         * SOCIAL TABLE(S)
         */
        /*
         * // Like a middle table, links benefiter <-> social table <-> social reference table.
         * Schema::create('benefiters_social_table', function (Blueprint $table) {
         *     $table->increments('id');
         *     $table->integer('has_social_reference')->nullable();
         *     //$table->foreign('social_reference_id')->references('id')->on('yes_or_no_lookup');
         *     $table->string('social_reference_actions');
         *     $table->date('social_reference_date');
         *     $table->string('social_description')->nullable();

         *     $table->integer('benefiters_id')->unsigned()->nullable();
         *     $table->foreign('benefiters_id')->references('id')->on('benefiters');
         * });

         * Schema::create('benefiters_social_conference', function (Blueprint $table) {
         *     $table->increments('id');
         *     $table->date('conference_date')->nullable();
         *     $table->string('conference_topic')->nullable();
         *     $table->string('description')->nullable();

         *     $table->integer('benefiters_social_id')->unsigned()->nullable();
         *     $table->foreign('benefiters_social_id')->references('id')->on('benefiters_social_table');
         * });
         */

        /*
         * MEDICAL TABLE(S)
         */

        // Benefiter's chronic conditions.
        Schema::create('medical_chronic_conditions_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
        });

        Schema::create('medical_location_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        Schema::create('medical_examination_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        Schema::create('medical_visits', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('benefiter_id')->unsigned();
            $table->foreign('benefiter_id')->references('id')->on('benefiters');
            // doctor_id -> user with doctor subrole that has permissions to add/edit medical_visits table.
            $table->integer('doctor_id')->unsigned();
            $table->foreign('doctor_id')->references('id')->on('users');
            $table->integer('medical_location_id')->unsigned();
            $table->foreign('medical_location_id')->references('id')->on('medical_location_lookup');
            $table->integer('medical_examination_id')->unsigned();
            $table->foreign('medical_examination_id')->references('id')->on('medical_examination_lookup');
        });

        // General examination table.
        Schema::create('medical_examinations', function (Blueprint $table) {
            $table->increments('id');
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->float('skull_perimeter')->nullable();
            $table->float('temperature')->nullable();
            $table->float('blood_pressure_diastolic')->nullable();
            $table->float('blood_pressure_systolic')->nullable();
            $table->date('examination_date')->nullable;
            // Notes field.
            $table->text('description')->nullable();

            $table->integer('medical_visit_id')->unsigned();
            $table->foreign('medical_visit_id')->references('id')->on('medical_visits');
        });
        //
        // Lookup table for clinical examination results. Seed with 'respiratory system', 'digestive system', etc.
        Schema::create('medical_examination_results_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            /*
             * $table->integer('medical_results_id')->unsigned();
             * $table->foreign('medical_results_id')->references('id')->on('medical_examination_results');
             */
        });

        Schema::create('medical_chronic_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');

            $table->integer('chronic_condition_id')->unsigned();
            $table->foreign('chronic_condition_id')->references('id')->on('medical_chronic_conditions_lookup');
            $table->integer('benefiters_id')->unsigned();
            $table->foreign('benefiters_id')->references('id')->on('benefiters');
        });

        // Benefiter's clinical examination results.
        Schema::create('medical_examination_results', function (Blueprint $table) {
            $table->increments('id');
            // Notes field.
            $table->text('description')->nullable;

            $table->integer('medical_visit_id')->unsigned();
            $table->foreign('medical_visit_id')->references('id')->on('medical_visits');
            $table->integer('results_lookup_id')->unsigned();
            $table->foreign('results_lookup_id')->references('id')->on('medical_examination_results_lookup');
        });

        // Benefiter's laboratory results.
        Schema::create('medical_laboratory_results', function (Blueprint $table) {
            $table->increments('id');
            $table->text('laboratory_results')->nullable();

            $table->integer('medical_visit_id')->unsigned();
            $table->foreign('medical_visit_id')->references('id')->on('medical_visits');
        });

        Schema::create('medical_referrals', function (Blueprint $table) {
            $table->increments('id');
            $table->text('referrals');

            $table->integer('medical_visit_id')->unsigned();
            $table->foreign('medical_visit_id')->references('id')->on('medical_visits');
        });

        // Medication names lookup table.
        Schema::create('medical_medication_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
        });

        Schema::create('medical_medication', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('medical_visit_id')->unsigned();
            $table->foreign('medical_visit_id')->references('id')->on('medical_visits');
            $table->integer('medication_lookup_id')->unsigned();
            $table->foreign('medication_lookup_id')->references('id')->on('medical_medication_lookup');
        });

        Schema::create('medical_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->string('path');

            $table->integer('medical_visit_id')->unsigned();
            $table->foreign('medical_visit_id')->references('id')->on('medical_visits');
        });

        /*
         * LEGAL TABLE(S)
         */
        /*
         * Schema::create('benefiters_legal_table', function (Blueprint $table) {
         *     $table->increments('id');
         *     $table->date('legal_status_exp_date');
         *     $table->boolean('has_legal_reference');
         *     //$table->foreign('legal_reference_id')->references('id')->on('yes_or_no_lookup');
         *     $table->string('legal_reference_actions');
         *     $table->date('legal_reference_date');
         *     $table->string('legal_description')->nullable();

         *     $table->integer('benefiters_id')->unsigned()->nullable();
         *     $table->foreign('benefiters_id')->references('id')->on('benefiters');
         * });

         * Schema::create('legal_statuses', function (Blueprint $table) {
         *     $table->increments('id');
         *     $table->string('description');
         *     $table->timestamp('expire_date');
         * });

         * Schema::create('benefiters_legal_status', function (Blueprint $table) {
         *     $table->increments('id');
         *     $table->string('description')->nullable();
         *     $table->integer('benefiter_id')->unsigned();
         *     $table->foreign('benefiter_id')->references('id')->on('benefiters');
         *     $table->integer('legal_status_id')->unsigned();
         *     $table->foreign('legal_status_id')->references('id')->on('legal_statuses');
         * });
         */

        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        Schema::create('language_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        Schema::create('benefiters_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('benefiter_id')->unsigned();
            $table->foreign('benefiter_id')->references('id')->on('benefiters');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages');
            $table->integer('language_level_id')->unsigned();
            $table->foreign('language_level_id')->references('id')->on('language_levels');
        });

        Schema::create('psychosocial_support_lookup', function(Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        Schema::create('benefiters_psychosocial_support', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('benefiter_id')->unsigned();
            $table->foreign('benefiter_id')->references('id')->on('benefiters');
            $table->integer('psychosocial_support_id')->unsigned();
            $table->foreign('psychosocial_support_id')->references('id')->on('psychosocial_support_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('benefiters_social_table');
        Schema::dropIfExists('benefiters_psychosocial_support');
        Schema::dropIfExists('psychosocial_support_lookup');
        Schema::dropIfExists('benefiters_languages');
        Schema::dropIfExists('language_levels');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('medical_uploads');
        Schema::dropIfExists('medical_medication');
        Schema::dropIfExists('medical_medication_lookup');
        Schema::dropIfExists('medical_referrals');
        Schema::dropIfExists('medical_laboratory_results');
        Schema::dropIfExists('medical_examination_results');
        Schema::dropIfExists('medical_chronic_conditions');
        Schema::dropIfExists('medical_examination_results_lookup');
        Schema::dropIfExists('medical_examinations');
        Schema::dropIfExists('medical_visits');
        Schema::dropIfExists('medical_examination_lookup');
        Schema::dropIfExists('medical_location_lookup');
        Schema::dropIfExists('medical_chronic_conditions_lookup');
        Schema::dropIfExists('benefiters_legal_status');
        Schema::dropIfExists('legal_status_lookup');
        Schema::dropIfExists('benefiters');
    }
}