<?php

return [
    /** Snap Api Endpoint */
    'snap_api_endpoint' => env('MIDTRANS_SNAP_API_ENDPOINT', 'https://app.sandbox.midtrans.com/snap/v1/transactions/'),

    /** Server Key */
    'server_key' => env('MIDTRANS_SERVER_KEY', null),

    /** Client key */
    'client_key' => env('MIDTRANS_CLIENT_KEY', null),

    /** Is Production */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', true)
];
