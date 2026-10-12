<?php
namespace Database\Factories;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
class StudentFactory extends Factory {
    protected $model = Student::class;
    public function definition(): array {
        return ['name' => fake()->unique()->name(), 'father_name' => fake()->firstName(), 'phone' => '0912' . fake()->numerify('######'),
            'status' => 'active', 'registered_at' => now()->toDateString()];
    }
}
