<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use App\User;
use App\Car;

class CarTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Collection $cars;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cars = Car::factory(10)->create(['user_id' => $this->user->id]);
    }

    /**
     * Get all cars for the logged in user
     *
     * @covers \App\Http\Controllers\CarController::index
     * @return void
     */
    public function testGetAllCars(): void {
        $response = $this->actingAs($this->user, 'api')->getJson('/api/cars');
        $response->assertStatus(200);
        
        foreach($this->cars as $car) {
            $response->assertSee($car->id);
            $response->assertSee($car->make);
            $response->assertSee($car->model);
            $response->assertSee($car->year);
            $response->assertDontSee($car->created_at);
            $response->assertDontSee($car->updated_at);
            $response->assertDontSee($this->user->email);
        }
    }

    /**
     * Add a new car.
     *
     * @covers \App\Http\Controllers\CarController::store
     * @return void
     */
    public function testCreateCar(): void {
        $cars = [
            [
                'year' => 2022,
                'make' => 'Mazda',
                'model' => 'X5'
            ],
            [
                'year' => 2018,
                'make' => 'Audii',
                'model' => 'A6'
            ],
            [
                'year' => 2020,
                'make' => 'Toyota',
                'model' => 'Avensis'
            ]
        ];

        foreach($cars as $car) {
            $response = $this->actingAs($this->user, 'api')->postJson('/api/cars', $car);
            $response->assertStatus(200);

            $response->assertDontSee($car['year']);
            $response->assertDontSee($car['make']);
            $response->assertDontSee($car['model']);

            $this->assertDatabaseHas('cars', $car);
        }
    }

    /**
     * Get a car with the given id.
     *
     * @covers \App\Http\Controllers\CarController::show
     * @return void
     */
    public function testGetSingleCar(): void {
        foreach($this->cars as $car) {
            $response = $this->actingAs($this->user, 'api')->getJson('/api/cars/' . $car->id);
            $response->assertStatus(200);

            $response->assertSee($car->id);
            $response->assertSee($car->make);
            $response->assertSee($car->model);
            $response->assertSee($car->year);
            $response->assertSee($car->trip_count);
            $response->assertSee($car->trip_miles);
            $response->assertDontSee($car->created_at);
            $response->assertDontSee($car->updated_at);
            $response->assertDontSee($this->user->email);
        }
    }

    /**
     * Delete a car with a given id.
     * 
     * @covers \App\Http\Controllers\CarController::destroy
     * @return void
     */
    public function testDeleteCar(): void {
        foreach($this->cars as $car) {
            $response = $this->actingAs($this->user, 'api')->deleteJson('/api/cars/' . $car->id);
            $response->assertStatus(200);
    
            $response->assertDontSee($car->id);
            $response->assertDontSee($car->year);
            $response->assertDontSee($car->make);
            $response->assertDontSee($car->model);
    
            $this->assertDatabaseMissing('cars', [
                'id' => $car->id,
                'year' => $car->year,
                'make' => $car->make,
                'model' => $car->model
            ]);
        }
    }
}
