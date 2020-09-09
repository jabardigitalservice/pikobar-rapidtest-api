<?php

return [

    /**
     * Config toggle for notify over SMS/WA
     */
    'notify' => env('NOTIFY', false),

    /**
     * In development/staging environment, we need to carefully send notifications.
     * Redirect/send to developer instead to real numbers
     */
    'notify_to' => env('NOTIFY_TO'),

    'messages' => [
        'registered_thankyou' => env('NOTIFY_MESSAGES_REGISTERED_THANKYOU', false),
        'checkin_thankyou'    => env('NOTIFY_MESSAGES_CHECKIN_THANKYOU', false)
    ],
];
