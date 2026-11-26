<?php

/*
 * The organisation that uses BeneFile. The screens show its name, its logo
 * and a link to its contact page.
 */
return [

    'organisation' => [
        'name' => env('BENEFILE_ORGANISATION_NAME', 'PRAKSIS'),

        // A path under public/.
        'logo' => env('BENEFILE_ORGANISATION_LOGO', 'images/praksis_logo_black.png'),

        // Empty: the pages for users without access show no contact link.
        'contact_url' => env('BENEFILE_ORGANISATION_CONTACT_URL', 'https://www.praksis.gr/el/επικοινωνια'),
    ],

];
