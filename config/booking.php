<?php

/*
|--------------------------------------------------------------------------
| Call booking
|--------------------------------------------------------------------------
|
| Weekly availability, blocked dates, the time zone, the meeting link and
| the on/off switch are managed in the admin panel (Availability page).
|
*/

return [

    // Length of each bookable call, in minutes.
    'slot_minutes' => (int) env('BOOKING_SLOT_MINUTES', 30),

    // Gap kept free after each call, in minutes.
    'buffer_minutes' => (int) env('BOOKING_BUFFER_MINUTES', 0),

    // How far ahead a call must be booked, in hours.
    'min_notice_hours' => (int) env('BOOKING_MIN_NOTICE_HOURS', 24),

    // How many days ahead visitors can book.
    'window_days' => (int) env('BOOKING_WINDOW_DAYS', 30),

];
