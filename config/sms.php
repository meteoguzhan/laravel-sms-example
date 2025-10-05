<?php

return [
    // SMS provider base endpoint
    'endpoint' => env('SMS_ENDPOINT', 'http://localhost:9000/'),

    // Queue name for SMS jobs
    'queue' => env('SMS_QUEUE', 'default'),
];
