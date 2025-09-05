<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Floor;
use App\Models\Zone;

class StorageSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 rooms
        for ($roomNum = 1; $roomNum <= 5; $roomNum++) {
            $room = Room::create([
                'name' => "Room {$roomNum}",
                'code' => "R{$roomNum}",
                'capacity' => 20000,
                'current_usage' => 0,
                'is_active' => true,
                'description' => "Storage Room {$roomNum} with 20,000 basket capacity"
            ]);

            // Create 4 floors for each room
            for ($floorNum = 1; $floorNum <= 4; $floorNum++) {
                $floor = Floor::create([
                    'room_id' => $room->id,
                    'floor_number' => $floorNum,
                    'name' => "Floor {$floorNum}",
                    'capacity' => 5000, // 20000/4 = 5000 per floor
                    'current_usage' => 0,
                    'is_active' => true,
                    'description' => "Floor {$floorNum} of Room {$roomNum}"
                ]);

                // Create 3 zones for each floor
                $zoneNames = ['A', 'B', 'C'];
                for ($zoneNum = 1; $zoneNum <= 3; $zoneNum++) {
                    Zone::create([
                        'floor_id' => $floor->id,
                        'zone_number' => $zoneNum,
                        'name' => "Zone {$zoneNames[$zoneNum - 1]}",
                        'code' => $zoneNames[$zoneNum - 1],
                        'capacity' => 1667, // 5000/3 = ~1667 per zone
                        'current_usage' => 0,
                        'is_active' => true,
                        'description' => "Zone {$zoneNames[$zoneNum - 1]} of Floor {$floorNum}, Room {$roomNum}"
                    ]);
                }
            }
        }
    }
}
