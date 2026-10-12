<?php
namespace Database\Factories;
use App\Models\{Supervisor, User};
use Illuminate\Database\Eloquent\Factories\Factory;
class SupervisorFactory extends Factory {
    protected $model = Supervisor::class;
    public function definition(): array { return ['user_id' => User::factory(), 'phone' => '0912' . fake()->numerify('######'), 'is_active' => true]; }
}
