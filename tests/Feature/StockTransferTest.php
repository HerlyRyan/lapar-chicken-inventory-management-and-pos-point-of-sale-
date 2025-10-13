<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Unit;
use App\Models\FinishedProduct;
use App\Models\SemiFinishedProduct;
use App\Models\FinishedBranchStock;
use App\Models\SemiFinishedBranchStock;
use App\Models\StockTransfer;
use App\Models\Role;
use App\Models\User;
use App\Services\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StockTransferTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsSuperAdmin(?Branch $branch = null): User
    {
        $user = User::factory()->create([
            'branch_id' => $branch?->id,
        ]);
        // Ensure Super Admin role exists (custom Role model) and attach
        $role = Role::firstOrCreate(
            ['code' => 'super_admin'],
            ['name' => 'Super Admin', 'is_active' => true]
        );
        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
        }
        $this->actingAs($user);
        return $user;
    }

    protected function seedProductsAndStocks(Branch $from, Branch $to): array
    {
        $category = Category::firstOrCreate(
            ['name' => 'Test Category'],
            ['code' => 'TC-' . Str::random(4), 'is_active' => true]
        );
        $unit = Unit::firstOrCreate(
            ['unit_name' => 'Unit'],
            ['abbreviation' => 'U', 'is_active' => true]
        );

        $finished = FinishedProduct::create([
            'name' => 'Finished ' . Str::random(5),
            'code' => 'FP-' . Str::random(5),
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'price' => 0,
            'minimum_stock' => 0,
            'production_cost' => 0,
            'is_active' => true,
        ]);

        $semi = SemiFinishedProduct::create([
            'name' => 'Semi ' . Str::random(5),
            'code' => 'SP-' . Str::random(5),
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit_price' => 0,
            'production_cost' => 0,
            'selling_price' => 0,
            'minimum_stock' => 0,
            'current_stock' => 0,
            'is_active' => true,
        ]);

        FinishedBranchStock::create([
            'branch_id' => $from->id,
            'finished_product_id' => $finished->id,
            'quantity' => 100,
        ]);
        SemiFinishedBranchStock::create([
            'branch_id' => $from->id,
            'semi_finished_product_id' => $semi->id,
            'quantity' => 200,
        ]);

        // Ensure destination rows exist (optional)
        FinishedBranchStock::firstOrCreate([
            'branch_id' => $to->id,
            'finished_product_id' => $finished->id,
        ], ['quantity' => 0]);
        SemiFinishedBranchStock::firstOrCreate([
            'branch_id' => $to->id,
            'semi_finished_product_id' => $semi->id,
        ], ['quantity' => 0]);

        return [$finished, $semi];
    }

    public function test_batch_request_creates_multiple_pending_transfers_and_deducts_source()
    {
        $from = Branch::create([
            'name' => 'From Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        $to = Branch::create([
            'name' => 'To Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        [$finished, $semi] = $this->seedProductsAndStocks($from, $to);

        // Act as Super Admin and set branch context to from-branch
        $this->actingAsSuperAdmin($from);
        $this->withSession(['current_branch_id' => $from->id]);

        $payload = [
            'items' => [
                [
                    'item_type' => 'finished',
                    'item_id' => $finished->id,
                    'to_branch_id' => $to->id,
                    'quantity' => 10,
                    'notes' => 'Batch transfer finished',
                ],
                [
                    'item_type' => 'semi-finished',
                    'item_id' => $semi->id,
                    'to_branch_id' => $to->id,
                    'quantity' => 20,
                    'notes' => 'Batch transfer semi',
                ],
            ],
        ];

        $response = $this->post(route('stock-transfer.request'), $payload);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $data = $response->json();
        $this->assertCount(2, $data['transfer_ids']);

        // Assert transfers persisted with status sent
        $this->assertEquals(2, StockTransfer::where('status', 'sent')->count());

        // Assert source stocks deducted
        $fromFinished = FinishedBranchStock::where('branch_id', $from->id)
            ->where('finished_product_id', $finished->id)->first();
        $fromSemi = SemiFinishedBranchStock::where('branch_id', $from->id)
            ->where('semi_finished_product_id', $semi->id)->first();
        $this->assertEquals(90, (int) $fromFinished->quantity);
        $this->assertEquals(180, (int) $fromSemi->quantity);
    }

    public function test_accept_transfer_adds_to_destination_and_marks_accepted()
    {
        $from = Branch::create([
            'name' => 'From Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        $to = Branch::create([
            'name' => 'To Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        [$finished] = $this->seedProductsAndStocks($from, $to);

        // Create a pending transfer
        $this->actingAsSuperAdmin($from);
        $service = app(StockTransferService::class);
        $transfer = $service->createPendingTransfer('finished', $finished->id, $from->id, $to->id, 15, 'Accept test');

        // Accept as Super Admin (bypasses branch/role checks)
        $this->actingAsSuperAdmin($to);
        $this->withSession(['current_branch_id' => $to->id]);
        $this->post(route('stock-transfer.accept', $transfer), [
            'response_notes' => 'OK diterima',
        ])->assertRedirect();

        $transfer->refresh();
        $this->assertEquals('accepted', $transfer->status);

        $toFinished = FinishedBranchStock::where('branch_id', $to->id)
            ->where('finished_product_id', $finished->id)->first();
        $this->assertEquals(15, (int) $toFinished->quantity);
    }

    public function test_reject_transfer_returns_stock_to_source_and_marks_rejected()
    {
        $from = Branch::create([
            'name' => 'From Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        $to = Branch::create([
            'name' => 'To Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        [$finished] = $this->seedProductsAndStocks($from, $to);

        // Create a pending transfer
        $this->actingAsSuperAdmin($from);
        $service = app(StockTransferService::class);
        $transfer = $service->createPendingTransfer('finished', $finished->id, $from->id, $to->id, 12, 'Reject test');

        // Reject as Super Admin
        $this->actingAsSuperAdmin($to);
        $this->withSession(['current_branch_id' => $to->id]);
        $this->post(route('stock-transfer.reject', $transfer), [
            'response_notes' => 'Tidak sesuai',
        ])->assertRedirect();

        $transfer->refresh();
        $this->assertEquals('rejected', $transfer->status);

        $fromFinished = FinishedBranchStock::where('branch_id', $from->id)
            ->where('finished_product_id', $finished->id)->first();
        // Originally 100, deducted 12 on createPending, returned 12 on reject -> back to 100
        $this->assertEquals(100, (int) $fromFinished->quantity);
    }

    public function test_request_requires_branch_context_returns_422()
    {
        $from = Branch::create([
            'name' => 'From Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        $to = Branch::create([
            'name' => 'To Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        [$finished, $semi] = $this->seedProductsAndStocks($from, $to);

        // Act as Super Admin WITHOUT setting branch context and with no user branch
        $this->actingAsSuperAdmin(null);

        $payload = [
            'items' => [
                [
                    'item_type' => 'finished',
                    'item_id' => $finished->id,
                    'to_branch_id' => $to->id,
                    'quantity' => 5,
                ],
            ],
        ];

        $response = $this->postJson(route('stock-transfer.request'), $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
        $this->assertStringContainsString('Pilih cabang sumber', $response->json('message'));
        $this->assertEquals(0, StockTransfer::count());
    }

    public function test_batch_request_rejects_non_integer_quantities()
    {
        $from = Branch::create([
            'name' => 'From Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        $to = Branch::create([
            'name' => 'To Branch',
            'code' => 'BR-' . Str::random(4),
            'type' => 'branch',
            'is_active' => true,
        ]);
        [$finished, $semi] = $this->seedProductsAndStocks($from, $to);

        // Act as Super Admin and set source branch context
        $this->actingAsSuperAdmin($from);
        $this->withSession(['current_branch_id' => $from->id]);

        $payload = [
            'items' => [
                [
                    'item_type' => 'finished',
                    'item_id' => $finished->id,
                    'to_branch_id' => $to->id,
                    'quantity' => 1.5, // invalid: must be integer
                ],
                [
                    'item_type' => 'semi-finished',
                    'item_id' => $semi->id,
                    'to_branch_id' => $to->id,
                    'quantity' => 'abc', // invalid: not integer
                ],
            ],
        ];

        $response = $this->postJson(route('stock-transfer.request'), $payload);
        $response->assertStatus(422);
        // Ensure nothing created
        $this->assertEquals(0, StockTransfer::count());
    }
}
