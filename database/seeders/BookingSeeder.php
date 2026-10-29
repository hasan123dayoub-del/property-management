<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingMessage;
use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Visit bookings spread over the last six months and the next ten days, in every status,
     * with a few chat messages and reviews, so the dashboards, charts and reports have data.
     * Uses a fixed random seed, so the demo looks the same after every restart.
     */
    public function run(): void
    {
        mt_srand(2026);

        $employees = User::role('employee')->orderBy('id')->get();
        $customers = User::role('customer')->orderBy('id')->get();
        $properties = Property::where('status', '!=', 'hidden')->orderBy('id')->get();

        $notes = [
            'Customer prefers a morning visit.',
            'Asked about parking and the building entrance.',
            'Wants to see the kitchen and the balcony.',
            'Coming with family; please bring the keys.',
            null,
        ];
        $comments = [
            5 => 'Exactly as described, and the visit was well organized.',
            4 => 'Nice place and a helpful employee. A bit noisy outside.',
            3 => 'Good location, but smaller than it looked in the listing.',
        ];

        $reviewed = [];

        for ($i = 0; $i < 46; $i++) {
            // Older bookings are mostly finished; recent and future ones are mostly open.
            $daysAgo = $i < 36 ? mt_rand(0, 175) : -mt_rand(1, 10);
            $created = now()->subDays(max($daysAgo, 0) + mt_rand(0, 2))->setTime(mt_rand(8, 18), [0, 15, 30, 45][mt_rand(0, 3)]);
            $scheduled = now()->subDays($daysAgo)->setTime(mt_rand(9, 17), [0, 30][mt_rand(0, 1)]);

            $status = $daysAgo < 0
                ? ['pending', 'pending', 'approved', 'rescheduled'][mt_rand(0, 3)]
                : ['completed', 'completed', 'completed', 'approved', 'canceled', 'rejected', 'pending'][mt_rand(0, 6)];

            $customer = $customers[mt_rand(0, $customers->count() - 1)];
            $property = $properties[mt_rand(0, $properties->count() - 1)];

            $booking = new Booking([
                'user_id' => $customer->id,
                'property_id' => $property->id,
                'scheduled_at' => $scheduled,
                'status' => $status,
                'notes' => $notes[mt_rand(0, count($notes) - 1)],
                'rejection_reason' => $status === 'rejected' ? 'The property is not available at the requested time.' : null,
            ]);
            $booking->employee_id = $employees[$i % $employees->count()]->id;
            $booking->rejected_at = $status === 'rejected' ? $created->copy()->addDay() : null;
            $booking->created_at = $booking->updated_at = $created;
            $booking->save();

            if (in_array($status, ['approved', 'rescheduled'], true) && $i % 2 === 0) {
                foreach ([
                    [$customer->id, 'Hello, can we move the visit 30 minutes later?'],
                    [$booking->employee_id, 'Sure, I have updated the time. See you there.'],
                ] as [$sender, $text]) {
                    BookingMessage::create(['booking_id' => $booking->id, 'sender_id' => $sender, 'message' => $text]);
                }
            }

            // One review per customer and property (unique in the database), only after a completed visit.
            $key = $customer->id.'-'.$property->id;
            if ($status === 'completed' && ! isset($reviewed[$key])) {
                $reviewed[$key] = true;
                $rating = [5, 5, 4, 4, 3][mt_rand(0, 4)];
                Review::create([
                    'booking_id' => $booking->id,
                    'user_id' => $customer->id,
                    'property_id' => $property->id,
                    'rating' => $rating,
                    'comment' => $comments[$rating],
                ]);
                $property->increment('rating_count');
                $property->increment('rating_sum', $rating);
                $property->forceFill(['rating_avg' => round($property->rating_sum / $property->rating_count, 2)])->save();
            }
        }
    }
}
