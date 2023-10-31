<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Animal;

class AnimalControllerTest extends TestCase
{

    public function test_get_all_animals(): void
    {
        $response = $this->get("/api/animal");
        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertTrue(is_array($responseData));
    }

    public function test_fail_to_add_animal_with_blank_fields(): void
    {
        $response = $this->postJson('/api/animal', ['name' => '', 'type' => '', 'conservationStatus' => 'Least Concern']);

        $response->assertStatus(400);
    }

    public function test_fail_to_add_animal_with_invalid_type(): void
    {
        $response = $this->postJson('/api/animal', ['name' => 'Ocelot', 'type' => 'InvalidType', 'conservationStatus' => 'Least Concern']);

        $response->assertStatus(400);

        // Verify that Ocelot wasn't added to the database
        $animal = Animal::where('name', 'Ocelot')->first();
        $this->assertNull($animal);
    }

    public function test_fail_to_add_animal_with_invalid_status(): void
    {
        $response = $this->postJson('/api/animal', ['name' => 'Ocelot', 'type' => 'Mammal', 'conservationStatus' => 'Invalid Status']);

        $response->assertStatus(400);

        // Verify that Ocelot wasn't added to the database
        $animal = Animal::where('name', 'Ocelot')->first();
        $this->assertNull($animal);
    }

    public function test_add_animal(): void
    {
        $response = $this->postJson('/api/animal', ['name' => 'Goldfish', 'type' => 'Fish', 'conservationStatus' => 'Least Concern']);

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['newAnimal']);

        $animal = Animal::where('name', 'Goldfish')->first();
        $this->assertEquals($animal['type'], 'Fish');
        $this->assertEquals($animal['conservationStatus'], 'Least Concern');
    }

    public function test_fail_to_update_animal_missing_name(): void
    {
        // Update the Goldfish
        $animal = Animal::where('name', 'Goldfish')->first();

        $response = $this->put('/api/animal/' . $animal->id, ['name' => '', 'type' => 'Reptile', 'conservationStatus' => 'Vulnerable' ]);

        $response->assertStatus(400);

        // Verify that Goldfish has been updated in the database
        $animal = Animal::where('id', $animal->id)->first();

        $this->assertEquals($animal['name'], 'Goldfish');
        $this->assertEquals($animal['type'], 'Fish');
        $this->assertEquals($animal['conservationStatus'], 'Least Concern');
    }


    public function test_update_animal(): void
    {
        // Update the Goldfish
        $animal = Animal::where('name', 'Goldfish')->first();

        $response = $this->put('/api/animal/' . $animal->id, ['name' => 'Silverfish', 'type' => 'Reptile', 'conservationStatus' => 'Vulnerable' ]);

        $response->assertStatus(200);

        // Verify that Goldfish has been updated in the database
        $animal = Animal::where('name', 'Silverfish')->first();

        $this->assertEquals($animal['name'], 'Silverfish');
        $this->assertEquals($animal['type'], 'Reptile');
        $this->assertEquals($animal['conservationStatus'], 'Vulnerable');
    }


    public function test_delete_animal(): void
    {
        // Delete the Goldfish turned Silverfish
        $animal = Animal::where('name', 'Silverfish')->first();

        $response = $this->delete('/api/animal/' . $animal->id);

        $response->assertStatus(200);

        // Verify that it's gone from the database
        $animal = Animal::where('name', 'Silverfish')->first();

        $this->assertNull($animal);
    }
}
