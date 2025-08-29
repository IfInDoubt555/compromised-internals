<?php

return [
    // Allowed affiliate brands and their host allowlists
    'brands' => [
        'booking' => ['booking.com'],
        'trip'    => ['trip.com'],
        'agoda'   => ['agoda.com'],
        'expedia' => ['expedia.com'],
        'viator'  => ['viator.com'],
    ],

    // Optional: map unified "subid" to partner-specific param (can be expanded later)
    'subid_param' => [
        'default' => 'subid',
    ],
];