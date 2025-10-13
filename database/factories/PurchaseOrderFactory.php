<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'PO-' . date('Y-m-') . str_pad(fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'order_code' => 'PO' . str_pad(fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'supplier_id' => Supplier::factory(),
            'status' => fake()->randomElement(['draft', 'ordered']),
            'notes' => fake()->optional()->sentence(),
            'total_amount' => fake()->numberBetween(50000, 1000000),
            'requested_delivery_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the purchase order is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the purchase order is ordered.
     */
    public function ordered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ordered',
        ]);
    }
}
