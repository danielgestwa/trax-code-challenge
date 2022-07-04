<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use App\User;
use App\Car;
use App\Trip;
use Carbon\Carbon;

class TripTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Car $car;
    private Collection $trips;

    private Car $anotherUserCar;
    private Trip $anotherUserTrip;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->car = Car::factory()->create(['user_id' => $this->user->id]);
        $this->trips = Trip::factory(10)->create(['car_id' => $this->car->id]);
    }
    
    /**
     * Get the trips for the logged in user
     *
     * @covers \App\Http\Controllers\TripController::index
     * @return void
     */
    public function testGetAllTrips(): void {
        $response = $this->actingAs($this->user, 'api')->getJson('/api/trips');
        $response->assertStatus(200);
        
        foreach($this->trips as $trip) {
            $response->assertSee($trip->id);
            $response->assertSee(Carbon::parse($trip->date)->format('m'));
            $response->assertSee(Carbon::parse($trip->date)->format('d'));
            $response->assertSee(Carbon::parse($trip->date)->format('Y'));
            $response->assertSee($trip->miles);
            $response->assertSee($trip->total);
            $response->assertDontSee($trip->created_at);
            $response->assertDontSee($trip->updated_at);

            $response->assertSee($this->car->make);
            $response->assertSee($this->car->model);
            $response->assertSee($this->car->year);
            $response->assertDontSee($this->car->created_at);
            $response->assertDontSee($this->car->updated_at);
            $response->assertDontSee($this->user->email);
        }
    }

    /**
     * Add a new trip.
     *
     * @covers \App\Http\Controllers\TripController::store
     * @return void
     */
    public function testCreateTrip(): void {
        $trips = [
            [
                'date' => '2022-04-04T03:52:00.000Z',
                'car_id' => $this->car->id,
                'miles' => '123',
            ],
            [
                'date' => '2022-04-02T03:52:00.000Z',
                'car_id' => $this->car->id,
                'miles' => '12.33',
            ],
            [
                'date' => '2022-04-01T03:52:00.000Z',
                'car_id' => $this->car->id,
                'miles' => '2222.33',
            ]
        ];

        foreach($trips as $trip) {
            $response = $this->actingAs($this->user, 'api')->postJson('/api/trips', $trip);
            $response->assertStatus(200);

            $response->assertDontSee(Carbon::parse($trip['date'])->format('m'));
            $response->assertDontSee(Carbon::parse($trip['date'])->format('d'));
            $response->assertDontSee(Carbon::parse($trip['date'])->format('Y'));
            $response->assertDontSee($trip['car_id']);
            $response->assertDontSee($trip['miles']);

            $this->assertDatabaseHas('trips', [
                'date' => Carbon::parse($trip['date'])->format('Y-m-d'),
                'car_id' => $trip['car_id'],
                'miles' => $trip['miles']
            ]);
        }
    }
}
