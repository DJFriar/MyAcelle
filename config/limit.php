<?php

return [
    'plan' => [
        // Do not show "New Plan" if there are already 3 plans
        'limit' => 3,

        // If this option is true, then do not show the related menu item. Also, do not allow direct access via web URL
        'disable_public_page' => true,
    ],

    'sending_server' => [
        // Do not show "New Sending server" if there is already one sending server
        'limit' => 1
    ],

    'bounce_handler' => [
        // hide the menu item, do not allow direct access via web URL
        'disable' => true
    ],
    'feedback_loop_handler' => [
        'disable' => true
    ],
    'email_verfication_server' => [
        'disable' => true
    ],
    'campaign' => [ 'limit' => 1 ],

    'automation' => [ 'disable' => true ],

    'list' => [
        'limit' => 1,
        'disable_segment' => true,
    ],

    'form' => [ 'disable' => true ],

    'website' => [ 'disable' => true ],
];
