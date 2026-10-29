<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'admin@demo.com')->value('id');
        $types = PropertyType::pluck('id', 'name');
        $amenities = Amenity::pluck('id', 'name');

        // [title, type, city, neighborhood, rooms, area m², price, status, furnished, added months ago, amenities]
        $properties = [
            ['Sea-view apartment near the corniche', 'Residential Apartment', 'Latakia', 'Al-Ziraa', 3, 140, 450, 'available', true, 5, ['Furnished', 'Elevator', 'Parking']],
            ['Family villa with a private garden', 'Villa', 'Tartus', 'Al-Hamidiyeh', 6, 420, 1800, 'rented', true, 5, ['Furnished', 'Garden', 'Swimming Pool', 'Parking']],
            ['Modern studio close to the university', 'Residential Apartment', 'Damascus', 'Al-Mazzeh', 1, 55, 220, 'available', true, 4, ['Furnished', 'Elevator']],
            ['Corner shop on a busy main street', 'Commercial Shop', 'Aleppo', 'Al-Aziziyeh', 1, 80, 600, 'booked', false, 4, ['Unfurnished']],
            ['Two-bedroom apartment with balcony', 'Residential Apartment', 'Homs', 'Al-Inshaat', 2, 110, 300, 'available', false, 3, ['Unfurnished', 'Parking']],
            ['Building plot in a quiet area', 'Land', 'Latakia', 'Al-Sleibeh', 0, 600, 950, 'available', false, 3, []],
            ['Penthouse with a rooftop terrace', 'Residential Apartment', 'Damascus', 'Abu Rummaneh', 4, 210, 1200, 'rented', true, 2, ['Furnished', 'Elevator', 'Parking']],
            ['Retail space in a shopping center', 'Commercial Shop', 'Damascus', 'Al-Shaalan', 1, 65, 750, 'available', false, 2, ['Elevator']],
            ['Garden villa outside the city', 'Villa', 'Homs', 'Al-Waer', 5, 360, 1400, 'booked', false, 1, ['Garden', 'Parking']],
            ['Cozy apartment in the old town', 'Residential Apartment', 'Aleppo', 'Al-Jdeideh', 2, 90, 260, 'available', true, 1, ['Furnished']],
            ['Farm land with a water well', 'Land', 'Tartus', 'Safita road', 0, 2500, 2100, 'hidden', false, 1, []],
            ['Bright three-room apartment', 'Residential Apartment', 'Latakia', 'Al-Mashrou', 3, 125, 380, 'available', false, 0, ['Unfurnished', 'Elevator']],
            ['Office-ready shop near the port', 'Commercial Shop', 'Tartus', 'Port district', 1, 95, 680, 'available', false, 0, ['Parking']],
        ];

        foreach ($properties as $i => [$title, $type, $city, $area, $rooms, $size, $price, $status, $furnished, $monthsAgo, $features]) {
            $added = now()->subMonthsNoOverflow($monthsAgo)->subDays($i % 9)->setTime(10 + $i % 7, 0);

            $property = new Property([
                'user_id' => $owner,
                'title' => $title,
                'property_type_id' => $types[$type],
                'city' => $city,
                'neighborhood' => $area,
                'address' => "{$area}, {$city}",
                'rooms' => $rooms,
                'area' => $size,
                'price' => $price,
                'status' => $status,
                'description' => "{$title} in {$area}, {$city}: {$size} m²".($rooms ? ", {$rooms} room(s)" : '').'. Book a visit to see it in person.',
                'is_furnished' => $furnished,
            ]);
            $property->created_at = $property->updated_at = $added;
            $property->save();

            $property->amenities()->attach($amenities->only($features)->values());
        }
    }
}
