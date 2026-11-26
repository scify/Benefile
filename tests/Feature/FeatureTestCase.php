<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests run on a MySQL database ("testing"): the queries of BeneFile use MySQL functions.
 * The database gets the lists of values and the example data of DatabaseSeeder.
 */
abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;
}
