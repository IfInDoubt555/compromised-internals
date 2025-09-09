<?php

return [
    'brands' => [
        'booking' => ['booking.com'],
        'trip'    => ['trip.com'],
        'agoda'   => ['agoda.com'],
        'expedia' => ['expedia.com'],
        'viator'  => ['viator.com'],
        'osr'     => ['opensimracing.com', 'www.opensimracing.com'],

        // NordVPN program domains (cover both direct and affiliate hosts)
        'nordvpn' => [
            'nordvpn.com', 'www.nordvpn.com',
            'go.nordvpn.net',                // CJ/Impact style links
            'nordvpn.tpx.lt', 'www.tpx.lt',  // the link used in your components
        ],
    ],
    'subid_param' => [
        'default' => 'subid',
    ],
];