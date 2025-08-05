<?php

return [
    /**
     * Service Fee Default Percentage
     *
     * This option is used to calculate service fee if the donated fund is
     * below the threshold.
     */
    'service_fee_percent' => env('DONATION_SERVICE_FEE_PERCENT', 5),

    /**
     * Service Fee Threshold
     *
     * Donated fund amount threshold to switch to fixed service fee
     */
    'service_fee_threshold' => env('DONATION_SERVICE_FEE_THRESHOLD', 100000),

    /**
     * Service Fee Max
     *
     * This option decides the maximum value of service fee if the donated fund
     * exceeds the threshold.
     */
    'service_fee_max' => env('DONATION_SERVICE_FEE_MAX', 5000),
];
