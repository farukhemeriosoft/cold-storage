<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;

class StorageController extends Controller
{
    /**
     * Get all rooms with their floors and zones
     */
    public function getStorageStructure(): JsonResponse
    {
        $rooms = Room::with(['floors.zones'])
            ->where('is_active', true)
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'code' => $room->code,
                    'capacity' => $room->capacity,
                    'current_usage' => $room->current_usage,
                    'available_capacity' => $room->getAvailableCapacity(),
                    'capacity_percentage' => round($room->getCapacityPercentage(), 2),
                    'floors' => $room->floors->map(function ($floor) {
                        return [
                            'id' => $floor->id,
                            'name' => $floor->name,
                            'floor_number' => $floor->floor_number,
                            'capacity' => $floor->capacity,
                            'current_usage' => $floor->current_usage,
                            'available_capacity' => $floor->getAvailableCapacity(),
                            'capacity_percentage' => round($floor->getCapacityPercentage(), 2),
                            'zones' => $floor->zones->map(function ($zone) {
                                return [
                                    'id' => $zone->id,
                                    'name' => $zone->name,
                                    'code' => $zone->code,
                                    'zone_number' => $zone->zone_number,
                                    'capacity' => $zone->capacity,
                                    'current_usage' => $zone->current_usage,
                                    'available_capacity' => $zone->getAvailableCapacity(),
                                    'capacity_percentage' => round($zone->getCapacityPercentage(), 2),
                                    'full_location' => $zone->getFullLocation(),
                                ];
                            })
                        ];
                    })
                ];
            });

        return response()->json([
            'message' => 'Storage structure retrieved successfully',
            'data' => $rooms
        ]);
    }

    /**
     * Get available zones for batch assignment
     */
    public function getAvailableZones(): JsonResponse
    {
        $zones = Zone::with(['floor.room'])
            ->where('is_active', true)
            ->get()
            ->filter(function ($zone) {
                return $zone->getAvailableCapacity() > 0;
            })
            ->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'code' => $zone->code,
                    'room_id' => $zone->room->id,
                    'room_name' => $zone->room->name,
                    'floor_id' => $zone->floor->id,
                    'floor_name' => $zone->floor->name,
                    'available_capacity' => $zone->getAvailableCapacity(),
                    'capacity_percentage' => round($zone->getCapacityPercentage(), 2),
                    'full_location' => $zone->getFullLocation(),
                ];
            });

        return response()->json([
            'message' => 'Available zones retrieved successfully',
            'data' => $zones
        ]);
    }

    /**
     * Get storage capacity summary
     */
    public function getCapacitySummary(): JsonResponse
    {
        $rooms = Room::where('is_active', true)->get();
        $totalCapacity = $rooms->sum('capacity');
        $totalUsage = $rooms->sum('current_usage');
        $availableCapacity = $totalCapacity - $totalUsage;

        $summary = [
            'total_rooms' => $rooms->count(),
            'total_capacity' => $totalCapacity,
            'total_usage' => $totalUsage,
            'available_capacity' => $availableCapacity,
            'utilization_percentage' => round(($totalUsage / $totalCapacity) * 100, 2),
            'rooms' => $rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'code' => $room->code,
                    'capacity' => $room->capacity,
                    'usage' => $room->current_usage,
                    'available' => $room->getAvailableCapacity(),
                    'utilization' => round($room->getCapacityPercentage(), 2)
                ];
            })
        ];

        return response()->json([
            'message' => 'Capacity summary retrieved successfully',
            'data' => $summary
        ]);
    }
}
