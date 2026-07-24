<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingService
{
    /**
     * Check if a specific Date and Time slot is available.
     */
    public function checkAvailability(string $date, string $time, ?int $excludeBookingId = null): bool
    {
        $query = Booking::whereDate('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['pending', 'approved', 'review', 'confirmed']);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    /**
     * Reserve a time slot for a given model.
     */
    public function reserveSlot($bookableModel, string $date, string $time, ?int $userId = null): Booking
    {
        if (!$this->checkAvailability($date, $time)) {
            throw new \Exception("The selected time slot {$time} on {$date} is already booked.");
        }

        return Booking::create([
            'user_id' => $userId ?? auth()->id(),
            'bookable_type' => get_class($bookableModel),
            'bookable_id' => $bookableModel->id,
            'booking_date' => $date,
            'booking_time' => $time,
            'status' => $bookableModel->status ?? 'pending',
        ]);
    }

    /**
     * Get list of booked slots for a specific date.
     */
    public function getBookedSlotsForDate(string $date): array
    {
        return Booking::whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'approved', 'review', 'confirmed'])
            ->pluck('booking_time')
            ->toArray();
    }
}
