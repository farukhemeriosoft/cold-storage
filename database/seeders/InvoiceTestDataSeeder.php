<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Batch;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Floor;
use App\Models\Zone;

class InvoiceTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test customer
        $customer = Customer::firstOrCreate(
            ['cnic_number' => '12345-1234567-1'],
            [
                'full_name' => 'Test Customer',
                'phone_number' => '1234567890',
                'address' => 'Test Address',
                'is_active' => true
            ]
        );

        // Create test room, floor, zone if they don't exist
        $room = Room::firstOrCreate(
            ['name' => 'Room 1'],
            ['capacity' => 20000, 'current_usage' => 0]
        );

        $floor = Floor::firstOrCreate(
            ['name' => 'Floor 1', 'room_id' => $room->id],
            ['capacity' => 5000, 'current_usage' => 0]
        );

        $zone = Zone::firstOrCreate(
            ['name' => 'Zone A', 'floor_id' => $floor->id],
            ['capacity' => 1667, 'current_usage' => 0]
        );

        // Create test batch
        $batch = Batch::create([
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'floor_id' => $floor->id,
            'zone_id' => $zone->id,
            'unit_price' => 10.00,
            'total_baskets' => 5,
            'total_weight' => 5,
            'total_value' => 50.00,
            'expiry_date' => now()->addYear(),
            'can_dispatch' => false,
            'status' => 'active'
        ]);

        // Create test invoice
        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'batch_id' => $batch->id,
            'invoice_number' => 'INV-202501-0001',
            'subtotal' => 50.00,
            'tax_amount' => 0.00,
            'total_amount' => 50.00,
            'paid_amount' => 0.00,
            'balance_due' => 50.00,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'unpaid'
        ]);

        $this->command->info('Test invoice created with ID: ' . $invoice->id);
    }
}
