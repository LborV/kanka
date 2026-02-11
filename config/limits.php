<?php

return [
    'campaigns' => [
        /**
         * Limits in place for standard campaigns. Premium campaigns are unlimited
         */
        'members' => 100,
        'roles' => 15,
        'bookmarks' => 10000,

        /**
         * Entities have a limited number of files (a type of entity_asset) available on each entity
         */
        'files' => [
            'standard' => 20,
            'premium' => 20,
        ],
        /**
         * Number of custom modules allowed per subscription tier.
         */
        'modules' => [
            'premium' =>  100,
            'wyvern' =>  100,
            'elemental' =>  100,
        ],

        'export' => 6, // hours after which exports get deleted
        'maps' => [
            // Maximum number of groups per map
            'groups' => [
                'standard' => 1,
                'premium' => 20,
            ],
            'layers' => [
                'standard' => 1,
                'premium' => 20,
            ],
        ],
        'logs' => [
            'standard' => 7,
            'premium' => 180,
        ],
        'web' => 10,
    ],

    /**
     * Default file upload size for standard user, in MB
     */
    'filesize' => [
        'image' => [
            'standard' =>  100,
            'owlbear' => 100,
            'wyvern' => 100,
            'elemental' =>  100,
        ],
        'map' =>  100,
    ],

    'gallery' => [
        'standard' => 25 * 1024 * 1024,
        'premium' => 25 * 1024 * 1024,
        'wyvern' => 25 * 1024 * 1024,
        'elemental' =>  25 * 1024 * 1024,
        // 'premium' => 20 * 1024,
    ],

    'pagination' => 100,

    'api' => [
        // Throttling values of requests per minute before a 421 "back down" response is thrown
        'throttle' => [
            'subscriber' =>  90,
            'default' => 30,
        ],
    ],
];
