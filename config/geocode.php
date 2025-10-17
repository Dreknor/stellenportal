<?php
return [
    'geocode_url' => env('GEOCODE_URL', 'https://geocode.maps.co/search?q='),
    'geocode_key' => env('GEOCODE_KEY', null),

    // Map generation settings
    'mapquest_key' => env('MAPQUEST_API_KEY', null),
    'map_zoom' => env('MAP_ZOOM', 15),
    'map_width' => env('MAP_WIDTH', 600),
    'map_height' => env('MAP_HEIGHT', 400),
];
