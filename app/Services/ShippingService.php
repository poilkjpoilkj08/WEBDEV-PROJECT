<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Shipping Service - Zone-Based Pricing Calculator
 * 
 * FORMULA: shipping_cost = zone_base + weight_fee + service_fee
 * 
 * Components:
 * 1. zone_base: Price determined by zone (A-E) and service level (hemat/reg/next_day/instant)
 * 2. weight_fee: 1kg included in zone_base, each extra kg charged per WEIGHT_FEE_PER_KG table
 *    - Example: 2.64kg = 1kg included + 1.64kg extra × Rp 2,000 = Rp 3,280 weight fee
 * 3. service_fee: Extra charge based on service level
 *    - hemat: 0, reg: 5k, next_day: 20k, instant: 30k
 * 
 * Weight Pricing Table:
 * | Berat           | Biaya           |
 * | --------------- | --------------- |
 * | 1kg pertama     | Included        |
 * | +1kg berikutnya | +Rp 2,000 (per kg per courier - configurable in WEIGHT_FEE_PER_KG) |
 * 
 * Currently all couriers use Rp 2,000 per extra kg (can be customized per courier in WEIGHT_FEE_PER_KG)
 */
class ShippingService
{
    /**
     * Zone-Based Courier Pricing
     * 
     * Structure: [courier][service_level][zone] = base_price
     * Base prices include 1kg weight; extra kg charged separately via WEIGHT_FEE_PER_KG
     */
    private const ZONE_PRICING = [
        // JNE
        'jne' => [
            'hemat' => ['A' => 6000, 'B' => 9000, 'C' => 11000, 'D' => 18000, 'E' => 35000],
            'reg' => ['A' => 9000, 'B' => 13000, 'C' => 16000, 'D' => 28000, 'E' => 50000],
            'next_day' => ['A' => 15000, 'B' => 20000, 'C' => 27000, 'D' => 45000, 'E' => 85000],
        ],
        // J&T
        'jnt' => [
            'hemat' => ['A' => 5500, 'B' => 8000, 'C' => 10000, 'D' => 16000, 'E' => 30000],
            'reg' => ['A' => 8000, 'B' => 12000, 'C' => 14000, 'D' => 26000, 'E' => 47000],
            'next_day' => ['A' => 13000, 'B' => 18000, 'C' => 24000, 'D' => 42000, 'E' => 78000],
        ],
        // SiCepat
        'sicepat' => [
            'hemat' => ['A' => 6000, 'B' => 9000, 'C' => 11000, 'D' => 18000, 'E' => 35000],
            'reg' => ['A' => 9000, 'B' => 13000, 'C' => 16000, 'D' => 28000, 'E' => 50000],
            'next_day' => ['A' => 15000, 'B' => 20000, 'C' => 27000, 'D' => 45000, 'E' => 85000],
        ],
        // Pos Indonesia
        'pos' => [
            'hemat' => ['A' => 3500, 'B' => 6000, 'C' => 7500, 'D' => 11000, 'E' => 22000],
            'reg' => ['A' => 6000, 'B' => 9000, 'C' => 11000, 'D' => 18000, 'E' => 35000],
            'next_day' => ['A' => 11000, 'B' => 15000, 'C' => 18000, 'D' => 28000, 'E' => 57000],
        ],
        // GoSend
        'gosend' => [
            'instant' => ['A' => 15000, 'B' => 22000, 'C' => 33000, 'D' => 55000, 'E' => 110000],
        ],
        // GrabExpress
        'grab' => [
            'instant' => ['A' => 18000, 'B' => 26000, 'C' => 37000, 'D' => 58000, 'E' => 115000],
        ],
    ];

    /**
     * Service level surcharges
     * Applied ON TOP of the zone_base price
     */
    private const SERVICE_SURCHARGE = [
        'hemat' => 0,       // No extra charge
        'reg' => 5000,      // +5k
        'next_day' => 20000, // +20k
        'instant' => 30000,  // +30k
    ];

    /**
     * Weight fee per extra kg (above 1kg included in zone_base)
     * Each courier can have different rate
     * Format: [courier_key] => price_per_kg_in_IDR
     */
    private const WEIGHT_FEE_PER_KG = [
        'jne' => 2000,      // +2k per extra kg
        'jnt' => 2000,      // +2k per extra kg
        'sicepat' => 2000,  // +2k per extra kg
        'pos' => 2000,      // +2k per extra kg
        'gosend' => 2000,   // +2k per extra kg
        'grab' => 2000,     // +2k per extra kg
    ];

    /**
     * Map old courier codes to new structure
     */
    private const COURIER_MAP = [
        'jne_reg' => ['courier' => 'jne', 'level' => 'reg'],
        'jne_yes' => ['courier' => 'jne', 'level' => 'next_day'],
        'jnt_reg' => ['courier' => 'jnt', 'level' => 'reg'],
        'sicepat' => ['courier' => 'sicepat', 'level' => 'reg'],
        'pos_biasa' => ['courier' => 'pos', 'level' => 'hemat'],
        'gosend' => ['courier' => 'gosend', 'level' => 'instant'],
        'grab_instant' => ['courier' => 'grab', 'level' => 'instant'],
    ];

    /**
     * Province to zone mapping
     * Maps Indonesian provinces directly to their island/region
     * Format: province_name_lowercase => 'region_key'
     */
    private const PROVINCE_ZONES = [
        // Jawa Island
        'dki jakarta' => 'jawa',
        'jakarta' => 'jawa',
        'banten' => 'jawa',
        'jawa barat' => 'jawa',
        'west java' => 'jawa',
        'jawa tengah' => 'jawa',
        'central java' => 'jawa',
        'daerah istimewa yogyakarta' => 'jawa',
        'yogyakarta' => 'jawa',
        'jawa timur' => 'jawa',
        'east java' => 'jawa',
        
        // Sumatera Island
        'aceh' => 'sumatra',
        'sumatera utara' => 'sumatra',
        'north sumatra' => 'sumatra',
        'sumatera barat' => 'sumatra',
        'west sumatra' => 'sumatra',
        'riau' => 'sumatra',
        'jambi' => 'sumatra',
        'sumatera selatan' => 'sumatra',
        'south sumatra' => 'sumatra',
        'bengkulu' => 'sumatra',
        'lampung' => 'sumatra',
        'kepulauan bangka belitung' => 'sumatra',
        'bangka belitung' => 'sumatra',
        
        // Kalimantan Island
        'kalimantan barat' => 'kalimantan',
        'west kalimantan' => 'kalimantan',
        'kalimantan tengah' => 'kalimantan',
        'central kalimantan' => 'kalimantan',
        'kalimantan selatan' => 'kalimantan',
        'south kalimantan' => 'kalimantan',
        'kalimantan timur' => 'kalimantan',
        'east kalimantan' => 'kalimantan',
        'kalimantan utara' => 'kalimantan',
        'north kalimantan' => 'kalimantan',
        
        // Sulawesi Island
        'sulawesi utara' => 'sulawesi',
        'north sulawesi' => 'sulawesi',
        'sulawesi tengah' => 'sulawesi',
        'central sulawesi' => 'sulawesi',
        'sulawesi barat' => 'sulawesi',
        'west sulawesi' => 'sulawesi',
        'sulawesi selatan' => 'sulawesi',
        'south sulawesi' => 'sulawesi',
        'sulawesi tenggara' => 'sulawesi',
        'southeast sulawesi' => 'sulawesi',
        'gorontalo' => 'sulawesi',
        
        // Bali
        'bali' => 'bali',
        
        // Nusa Tenggara
        'nusa tenggara barat' => 'nusatenggara',
        'west nusa tenggara' => 'nusatenggara',
        'nusa tenggara timur' => 'nusatenggara',
        'east nusa tenggara' => 'nusatenggara',
    ];
    
    /**
     * Remote areas that qualify for Zone E
     * Only specific remote regions in:
     * - Papua (all variants)
     * - Maluku (all variants)
     */
    private const REMOTE_AREAS = [
        // Papua
        'papua' => true,
        'papua barat' => true,
        'west papua' => true,
        'papua selatan' => true,
        'south papua' => true,
        'papua tengah' => true,
        'central papua' => true,
        'papua pegunungan' => true,
        'highland papua' => true,
        
        // Maluku
        'maluku' => true,
        'maluku utara' => true,
        'north maluku' => true,
    ];
    
    /**
     * City to province mapping for cases where city name is passed
     * This is a fallback for when destination is passed as city instead of province
     */
    private const CITY_TO_PROVINCE = [
        'jakarta' => 'dki jakarta',
        'tangerang' => 'banten',
        'serang' => 'banten',
        'bandung' => 'jawa barat',
        'bekasi' => 'jawa barat',
        'bogor' => 'jawa barat',
        'depok' => 'jawa barat',
        'semarang' => 'jawa tengah',
        'yogyakarta' => 'daerah istimewa yogyakarta',
        'surabaya' => 'jawa timur',
        'malang' => 'jawa timur',
        'medan' => 'sumatera utara',
        'padang' => 'sumatera barat',
        'pekanbaru' => 'riau',
        'jambi' => 'jambi',
        'palembang' => 'sumatera selatan',
        'bengkulu' => 'bengkulu',
        'bandar lampung' => 'lampung',
        'pontianak' => 'kalimantan barat',
        'palangka raya' => 'kalimantan tengah',
        'banjarmasin' => 'kalimantan selatan',
        'balikpapan' => 'kalimantan timur',
        'tarakan' => 'kalimantan utara',
        'manado' => 'sulawesi utara',
        'palu' => 'sulawesi tengah',
        'mamuju' => 'sulawesi barat',
        'makassar' => 'sulawesi selatan',
        'kendari' => 'sulawesi tenggara',
        'gorontalo' => 'gorontalo',
        'denpasar' => 'bali',
        'mataram' => 'nusa tenggara barat',
        'kupang' => 'nusa tenggara timur',
        'ambon' => 'maluku',
        'ternate' => 'maluku utara',
        'jayapura' => 'papua',
        'manokwari' => 'papua barat',
    ];

    /**
     * Determine shipping zone from origin and destination provinces
     * 
     * Zones:
     * A: Same city/metropolitan area
     * B: Same province but different city
     * C: Same island but different province
     * D: Different main islands (Jawa, Sumatra, Kalimantan, Sulawesi, etc.)
     * E: Remote/Eastern regions (Papua, Maluku, etc.)
     * 
     * @param string $origin_province Origin province
     * @param string $destination_province Destination province
     * @return string Zone letter (A-E)
     */
    public function determineZone($origin_province, $destination_province)
    {
        $origin_province = strtolower(trim($origin_province ?? ''));
        $destination_province = strtolower(trim($destination_province ?? ''));

        // Normalize province names (simple mapping)
        $origin_normalized = $this->normalizeProvince($origin_province);
        $destination_normalized = $this->normalizeProvince($destination_province);

        // Check if either is a remote area (Zone E)
        if ($this->isRemoteArea($origin_normalized) || $this->isRemoteArea($destination_normalized)) {
            return 'E';
        }

        // Get island groupings
        $origin_island = self::PROVINCE_ZONES[$origin_normalized] ?? 'unknown';
        $dest_island = self::PROVINCE_ZONES[$destination_normalized] ?? 'unknown';

        // If either is unknown, default to D (safer than E)
        if ($origin_island === 'unknown' || $dest_island === 'unknown') {
            Log::warning('[ZONE] Unknown province, defaulting to Zone D', [
                'origin' => $origin_province,
                'destination' => $destination_province,
            ]);
            return 'D';
        }

        // Same province = Zone A (same city or within-province)
        if ($origin_normalized === $destination_normalized) {
            return 'A';
        }

        // Same island but different province = Zone C
        if ($origin_island === $dest_island) {
            return 'C';
        }

        // Different islands = Zone D
        return 'D';
    }
    
    /**
     * Check if a province is a remote area (Zone E)
     */
    private function isRemoteArea($province_normalized)
    {
        return isset(self::REMOTE_AREAS[$province_normalized]);
    }

    /**
     * Normalize province name for lookup
     * Handles both province names and city names
     */
    private function normalizeProvince($location)
    {
        $location = strtolower(trim($location ?? ''));
        
        // Try direct province mapping first
        if (isset(self::PROVINCE_ZONES[$location])) {
            return $location;
        }
        
        // Try city-to-province mapping
        if (isset(self::CITY_TO_PROVINCE[$location])) {
            return self::CITY_TO_PROVINCE[$location];
        }
        
        // Return original if no mapping found
        return $location;
    }

    /**
     * Calculate shipping cost using zone-based formula
     * 
     * Formula: shipping_cost = zone_base + weight_fee + service_fee
     * - zone_base: Determined by zone (A-E) and service level (hemat/reg/next_day/instant)
     * - weight_fee: 1kg included, extra kg charged per WEIGHT_FEE_PER_KG[courier]
     * - service_fee: Added based on service level (hemat: 0, reg: 5k, next_day: 20k, instant: 30k)
     * 
     * @param float $weight_grams Weight in grams
     * @param string $zone Zone letter (A-E)
     * @param string $courier Courier code (jne_reg, jnt_reg, etc.)
     * @return array Shipping cost with breakdown
     */
    public function calculateShippingCost($weight_grams, $zone = 'C', $courier = 'jne_reg')
    {
        try {
            // Validate zone
            $zone = strtoupper($zone ?? 'C');
            if (!in_array($zone, ['A', 'B', 'C', 'D', 'E'])) {
                Log::warning('[SHIPPING] Invalid zone, defaulting to C', ['zone' => $zone]);
                $zone = 'C';
            }

            // Map old courier codes to new structure
            if (!isset(self::COURIER_MAP[$courier])) {
                Log::warning('[SHIPPING] Invalid courier, using default', ['courier' => $courier]);
                $courier = 'jne_reg';
            }

            $mapping = self::COURIER_MAP[$courier];
            $courier_key = $mapping['courier'];
            $service_level = $mapping['level'];

            // Validate zone pricing exists
            if (!isset(self::ZONE_PRICING[$courier_key][$service_level][$zone])) {
                Log::warning('[SHIPPING] Zone pricing not found, using zone A', [
                    'courier' => $courier_key,
                    'service_level' => $service_level,
                    'zone' => $zone,
                ]);
                $zone = 'A';
            }

            // Get zone base price
            $zone_base = self::ZONE_PRICING[$courier_key][$service_level][$zone];

            // Calculate weight fee (1kg included, extra kg charged per courier rate)
            $weight_kg = $weight_grams / 1000;
            $extra_kg = max(0, $weight_kg - 1); // 1kg included
            $weight_fee_rate = self::WEIGHT_FEE_PER_KG[$courier_key] ?? 2000; // Default 2k if not found
            $weight_fee = round($extra_kg * $weight_fee_rate);

            // Get service surcharge
            $service_surcharge = self::SERVICE_SURCHARGE[$service_level] ?? 0;

            // Total cost
            $total_cost = $zone_base + $weight_fee + $service_surcharge;

            Log::info('[SHIPPING] Zone-based calculation complete', [
                'courier' => $courier,
                'zone' => $zone,
                'weight_kg' => round($weight_kg, 2),
                'zone_base' => $zone_base,
                'weight_fee' => $weight_fee,
                'service_surcharge' => $service_surcharge,
                'total_cost' => $total_cost,
            ]);

            return [
                'cost' => (int)$total_cost,
                'breakdown' => [
                    'zone' => $zone,
                    'zone_base' => $zone_base,
                    'weight_kg' => round($weight_kg, 2),
                    'weight_fee' => $weight_fee,
                    'service_level' => $service_level,
                    'service_surcharge' => $service_surcharge,
                    'extra_kg' => round($extra_kg, 2),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('[SHIPPING] Error calculating cost', [
                'error' => $e->getMessage(),
                'weight_grams' => $weight_grams,
                'zone' => $zone,
                'courier' => $courier,
            ]);

            // Return minimal default cost
            return [
                'cost' => 15000,
                'breakdown' => [
                    'zone' => $zone ?? 'C',
                    'zone_base' => 15000,
                    'weight_fee' => 0,
                    'service_surcharge' => 0,
                ],
            ];
        }
    }

    /**
     * Get all available couriers with their service levels
     */
    public function getAllCouriers()
    {
        return self::COURIER_MAP;
    }

    /**
     * Get zone pricing table for UI display
     */
    public function getZonePricingTable()
    {
        return self::ZONE_PRICING;
    }
}

