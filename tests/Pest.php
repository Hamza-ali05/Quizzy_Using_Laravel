<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{get, post, put, delete, actingAs};
uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
