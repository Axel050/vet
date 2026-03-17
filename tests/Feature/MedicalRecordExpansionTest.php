<?php

use App\Models\Pet;
use App\Models\MedicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a pet can be created with new fields', function () {
    $pet = Pet::factory()->create([
        'microchip_id' => '1234567890123456',
        'is_sterilized' => true,
        'allergies' => 'Penicilina',
        'blood_type' => 'DEA 1.1',
    ]);

    expect($pet->microchip_id)->toBe('1234567890123456')
        ->and($pet->is_sterilized)->toBeTrue()
        ->and($pet->allergies)->toBe('Penicilina')
        ->and($pet->blood_type)->toBe('DEA 1.1');
});

test('a medical record can be created with new fields', function () {
    $record = MedicalRecord::factory()->create([
        'temperature' => 38.5,
        'heart_rate' => 80,
        'diagnosis' => 'Gripe canina',
        'is_visible_to_owner' => true,
        'next_appointment_at' => now()->addDays(7)->format('Y-m-d'),
    ]);

    expect($record->temperature)->toBe("38.50") // Decimal cast in model
        ->and($record->heart_rate)->toBe(80)
        ->and($record->diagnosis)->toBe('Gripe canina')
        ->and($record->is_visible_to_owner)->toBeTrue()
        ->and($record->next_appointment_at->format('Y-m-d'))->toBe(now()->addDays(7)->format('Y-m-d'));
});
