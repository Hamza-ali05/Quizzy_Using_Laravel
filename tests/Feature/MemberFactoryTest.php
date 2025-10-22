<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates 10 fake members using the factory', function () {
    Member::factory()->count(10)->create();
    expect(Member::count())->toBe(10);
    $member = Member::first();
    expect($member->name)->not->toBeEmpty()->and($member->email)->toContain('@')->and(in_array($member->role, ['admin', 'student']))->toBeTrue();
});
