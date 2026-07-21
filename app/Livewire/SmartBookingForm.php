<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SilidKarununganRequest;

class SmartBookingForm extends Component
{
    public string $preferred_date = '';
    public string $preferred_time = '';
    public array $bookedSlots = [];

    // Static list of available time slots
    public array $timeSlots = [
        '08:00 AM - 09:00 AM',
        '09:00 AM - 10:00 AM',
        '10:00 AM - 11:00 AM',
        '11:00 AM - 12:00 PM',
        '01:00 PM - 02:00 PM',
        '02:00 PM - 03:00 PM',
        '03:00 PM - 04:00 PM',
        '04:00 PM - 05:00 PM',
    ];

    /**
     * Lifecycle hook executed when $preferred_date is updated.
     * Normalizes input date format using Carbon and fetches booked time slots.
     */
    public function updatedPreferredDate($value): void
    {
        if (empty($value)) {
            $this->bookedSlots = [];
            return;
        }

        try {
            // Normalize date format to 'Y-m-d' regardless of UI date picker input format
            $formattedDate = \Carbon\Carbon::parse($value)->format('Y-m-d');

            // Fetch all booked slots for the normalized date
            $this->bookedSlots = SilidKarununganRequest::whereDate('preferred_date', $formattedDate)
                ->pluck('preferred_time')
                ->toArray();
        } catch (\Throwable $e) {
            $this->bookedSlots = [];
        }

        // Reset selected time if it has already been booked for this date
        if (in_array($this->preferred_time, $this->bookedSlots, true)) {
            $this->preferred_time = '';
        }
    }

    public function render()
    {
        return view('livewire.smart-booking-form');
    }
}
