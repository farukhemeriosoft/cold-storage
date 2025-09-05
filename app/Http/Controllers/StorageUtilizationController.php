<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use App\Models\Zone;
use App\Models\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StorageUtilizationController extends Controller
{
    /**
     * Get overall storage utilization summary
     */
    public function getOverallUtilization(): JsonResponse
    {
        $rooms = Room::with(['floors.zones', 'batches'])
            ->where('is_active', true)
            ->get();

        $totalCapacity = 0;
        $totalUsed = 0;
        $roomUtilizations = [];

        foreach ($rooms as $room) {
            $roomCapacity = $room->capacity;
            // Calculate actual usage from batches
            $roomUsed = $room->batches()->sum('total_baskets');
            $roomAvailable = $roomCapacity - $roomUsed;
            $roomPercentage = $roomCapacity > 0 ? round(($roomUsed / $roomCapacity) * 100, 2) : 0;

            $totalCapacity += $roomCapacity;
            $totalUsed += $roomUsed;

            $roomUtilizations[] = [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'room_code' => $room->code,
                'capacity' => $roomCapacity,
                'used' => $roomUsed,
                'available' => $roomAvailable,
                'utilization_percentage' => $roomPercentage,
                'status' => $this->getUtilizationStatus($roomPercentage),
                'floors' => $room->floors->map(function ($floor) {
                    $floorCapacity = $floor->capacity;
                    // Calculate actual usage from batches in this floor
                    $floorUsed = Batch::where('floor_id', $floor->id)->sum('total_baskets');
                    $floorPercentage = $floorCapacity > 0 ? round(($floorUsed / $floorCapacity) * 100, 2) : 0;

                    return [
                        'floor_id' => $floor->id,
                        'floor_name' => $floor->name,
                        'floor_number' => $floor->floor_number,
                        'capacity' => $floorCapacity,
                        'used' => $floorUsed,
                        'available' => $floorCapacity - $floorUsed,
                        'utilization_percentage' => $floorPercentage,
                        'status' => $this->getUtilizationStatus($floorPercentage),
                        'zones' => $floor->zones->map(function ($zone) {
                            $zoneCapacity = $zone->capacity;
                            // Calculate actual usage from batches in this zone
                            $zoneUsed = Batch::where('zone_id', $zone->id)->sum('total_baskets');
                            $zonePercentage = $zoneCapacity > 0 ? round(($zoneUsed / $zoneCapacity) * 100, 2) : 0;

                            return [
                                'zone_id' => $zone->id,
                                'zone_name' => $zone->name,
                                'zone_code' => $zone->code,
                                'zone_number' => $zone->zone_number,
                                'capacity' => $zoneCapacity,
                                'used' => $zoneUsed,
                                'available' => $zoneCapacity - $zoneUsed,
                                'utilization_percentage' => $zonePercentage,
                                'status' => $this->getUtilizationStatus($zonePercentage),
                            ];
                        })
                    ];
                })
            ];
        }

        $overallPercentage = $totalCapacity > 0 ? round(($totalUsed / $totalCapacity) * 100, 2) : 0;

        return response()->json([
            'message' => 'Storage utilization retrieved successfully',
            'data' => [
                'overall' => [
                    'total_capacity' => $totalCapacity,
                    'total_used' => $totalUsed,
                    'total_available' => $totalCapacity - $totalUsed,
                    'utilization_percentage' => $overallPercentage,
                    'status' => $this->getUtilizationStatus($overallPercentage),
                ],
                'rooms' => $roomUtilizations,
                'summary' => [
                    'total_rooms' => $rooms->count(),
                    'active_rooms' => $rooms->where('is_active', true)->count(),
                    'high_utilization_rooms' => collect($roomUtilizations)->where('utilization_percentage', '>', 80)->count(),
                    'low_utilization_rooms' => collect($roomUtilizations)->where('utilization_percentage', '<', 30)->count(),
                ]
            ]
        ]);
    }

    /**
     * Get utilization trends over time
     */
    public function getUtilizationTrends(): JsonResponse
    {
        $trends = [];
        $months = 12; // Last 12 months

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // Get batches created in this month
            $batchesInMonth = Batch::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

            $monthlyCapacity = 0;
            $monthlyUsed = 0;

            // Calculate capacity and usage for this month
            $rooms = Room::where('is_active', true)->get();
            foreach ($rooms as $room) {
                $monthlyCapacity += $room->capacity;
                // Count baskets from batches created up to this month
                $batchesUpToMonth = Batch::where('room_id', $room->id)
                    ->where('created_at', '<=', $endOfMonth)
                    ->get();

                $basketsInRoom = $batchesUpToMonth->sum('total_baskets');
                $monthlyUsed += min($basketsInRoom, $room->capacity);
            }

            $utilizationPercentage = $monthlyCapacity > 0 ? round(($monthlyUsed / $monthlyCapacity) * 100, 2) : 0;

            $trends[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'capacity' => $monthlyCapacity,
                'used' => $monthlyUsed,
                'available' => $monthlyCapacity - $monthlyUsed,
                'utilization_percentage' => $utilizationPercentage,
                'batches_created' => $batchesInMonth->count(),
                'baskets_added' => $batchesInMonth->sum('total_baskets'),
            ];
        }

        return response()->json([
            'message' => 'Utilization trends retrieved successfully',
            'data' => $trends
        ]);
    }

    /**
     * Get room-wise detailed utilization
     */
    public function getRoomUtilization($roomId = null): JsonResponse
    {
        $query = Room::with(['floors.zones', 'batches.customer'])
            ->where('is_active', true);

        if ($roomId) {
            $query->where('id', $roomId);
        }

        $rooms = $query->get();

        $roomData = $rooms->map(function ($room) {
            $roomCapacity = $room->capacity;
            // Calculate actual usage from batches
            $roomUsed = $room->batches()->sum('total_baskets');
            $roomAvailable = $roomCapacity - $roomUsed;
            $roomPercentage = $roomCapacity > 0 ? round(($roomUsed / $roomCapacity) * 100, 2) : 0;

            // Get recent batches in this room
            $recentBatches = $room->batches()
                ->with('customer')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($batch) {
                    return [
                        'batch_id' => $batch->id,
                        'customer_name' => $batch->customer->full_name ?? 'Unknown',
                        'baskets_count' => $batch->total_baskets,
                        'created_at' => $batch->created_at->format('Y-m-d H:i'),
                        'expiry_date' => $batch->expiry_date->format('Y-m-d'),
                        'storage_location' => $batch->getStorageLocation(),
                    ];
                });

            return [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'room_code' => $room->code,
                'capacity' => $roomCapacity,
                'used' => $roomUsed,
                'available' => $roomAvailable,
                'utilization_percentage' => $roomPercentage,
                'status' => $this->getUtilizationStatus($roomPercentage),
                'floors' => $room->floors->map(function ($floor) {
                    $floorCapacity = $floor->capacity;
                    // Calculate actual usage from batches in this floor
                    $floorUsed = Batch::where('floor_id', $floor->id)->sum('total_baskets');
                    $floorPercentage = $floorCapacity > 0 ? round(($floorUsed / $floorCapacity) * 100, 2) : 0;

                    return [
                        'floor_id' => $floor->id,
                        'floor_name' => $floor->name,
                        'floor_number' => $floor->floor_number,
                        'capacity' => $floorCapacity,
                        'used' => $floorUsed,
                        'available' => $floorCapacity - $floorUsed,
                        'utilization_percentage' => $floorPercentage,
                        'status' => $this->getUtilizationStatus($floorPercentage),
                        'zones' => $floor->zones->map(function ($zone) {
                            $zoneCapacity = $zone->capacity;
                            // Calculate actual usage from batches in this zone
                            $zoneUsed = Batch::where('zone_id', $zone->id)->sum('total_baskets');
                            $zonePercentage = $zoneCapacity > 0 ? round(($zoneUsed / $zoneCapacity) * 100, 2) : 0;

                            return [
                                'zone_id' => $zone->id,
                                'zone_name' => $zone->name,
                                'zone_code' => $zone->code,
                                'zone_number' => $zone->zone_number,
                                'capacity' => $zoneCapacity,
                                'used' => $zoneUsed,
                                'available' => $zoneCapacity - $zoneUsed,
                                'utilization_percentage' => $zonePercentage,
                                'status' => $this->getUtilizationStatus($zonePercentage),
                            ];
                        })
                    ];
                }),
                'recent_batches' => $recentBatches,
                'total_batches' => $room->batches()->count(),
                'total_baskets' => $room->batches()->sum('total_baskets'),
            ];
        });

        return response()->json([
            'message' => 'Room utilization retrieved successfully',
            'data' => $roomData
        ]);
    }

    /**
     * Get capacity alerts and recommendations
     */
    public function getCapacityAlerts(): JsonResponse
    {
        $rooms = Room::with(['floors.zones'])
            ->where('is_active', true)
            ->get();

        $alerts = [];
        $recommendations = [];

        foreach ($rooms as $room) {
            $roomCapacity = $room->capacity;
            // Calculate actual usage from batches
            $roomUsed = $room->batches()->sum('total_baskets');
            $roomPercentage = $roomCapacity > 0 ? round(($roomUsed / $roomCapacity) * 100, 2) : 0;

            // High utilization alert
            if ($roomPercentage >= 90) {
                $alerts[] = [
                    'type' => 'critical',
                    'level' => 'high',
                    'message' => "Room {$room->name} is at {$roomPercentage}% capacity",
                    'room_id' => $room->id,
                    'room_name' => $room->name,
                    'utilization_percentage' => $roomPercentage,
                    'available_space' => $roomCapacity - $roomUsed,
                ];
                $recommendations[] = "Consider expanding storage capacity for Room {$room->name}";
            } elseif ($roomPercentage >= 80) {
                $alerts[] = [
                    'type' => 'warning',
                    'level' => 'medium',
                    'message' => "Room {$room->name} is at {$roomPercentage}% capacity",
                    'room_id' => $room->id,
                    'room_name' => $room->name,
                    'utilization_percentage' => $roomPercentage,
                    'available_space' => $roomCapacity - $roomUsed,
                ];
            }

            // Check individual zones
            foreach ($room->floors as $floor) {
                foreach ($floor->zones as $zone) {
                    $zoneCapacity = $zone->capacity;
                    // Calculate actual usage from batches in this zone
                    $zoneUsed = Batch::where('zone_id', $zone->id)->sum('total_baskets');
                    $zonePercentage = $zoneCapacity > 0 ? round(($zoneUsed / $zoneCapacity) * 100, 2) : 0;

                    if ($zonePercentage >= 95) {
                        $alerts[] = [
                            'type' => 'critical',
                            'level' => 'high',
                            'message' => "Zone {$zone->code} in Room {$room->name} is at {$zonePercentage}% capacity",
                            'room_id' => $room->id,
                            'room_name' => $room->name,
                            'zone_id' => $zone->id,
                            'zone_name' => $zone->name,
                            'zone_code' => $zone->code,
                            'utilization_percentage' => $zonePercentage,
                            'available_space' => $zoneCapacity - $zoneUsed,
                        ];
                    }
                }
            }
        }

        // Low utilization recommendations
        $lowUtilizationRooms = $rooms->filter(function ($room) {
            $roomUsed = $room->batches()->sum('total_baskets');
            $percentage = $room->capacity > 0 ? round(($roomUsed / $room->capacity) * 100, 2) : 0;
            return $percentage < 30;
        });

        if ($lowUtilizationRooms->count() > 0) {
            $recommendations[] = "Consider consolidating storage in underutilized rooms: " .
                $lowUtilizationRooms->pluck('name')->join(', ');
        }

        return response()->json([
            'message' => 'Capacity alerts retrieved successfully',
            'data' => [
                'alerts' => $alerts,
                'recommendations' => $recommendations,
                'summary' => [
                    'total_alerts' => count($alerts),
                    'critical_alerts' => count(array_filter($alerts, fn($alert) => $alert['type'] === 'critical')),
                    'warning_alerts' => count(array_filter($alerts, fn($alert) => $alert['type'] === 'warning')),
                ]
            ]
        ]);
    }

    /**
     * Get utilization status based on percentage
     */
    private function getUtilizationStatus($percentage): string
    {
        if ($percentage >= 90) {
            return 'critical';
        } elseif ($percentage >= 80) {
            return 'warning';
        } elseif ($percentage >= 50) {
            return 'good';
        } else {
            return 'low';
        }
    }
}
