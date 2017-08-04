<?php
return [
    'TheRestartProject\RepairDirectory\Domain\Models\Business' => [
        'type'   => 'entity',
        'table'  => 'businesses',
        'id'     => [
            'uid' => [
                'type'     => 'integer',
                'generator' => [
                    'strategy' => 'auto'
                ]
            ],
        ],
        'uniqueConstraints' => [
            [
                'name' => 'business_unique_idx',
                'columns' => ['name', 'address']
            ]
        ],
        'fields' => [
            'name' => [
                'type' => 'string'
            ],
            'address' => [
                'type' => 'string'
            ],
            'postcode' => [
                'type' => 'string'
            ],
            'geolocation' => [
                'type' => 'array'
            ],
            'description' => [
                'type' => 'text'
            ],
            'landline' => [
                'type' => 'string',
                'nullable' => true
            ],
            'mobile' => [
                'type' => 'string',
                'nullable' => true
            ],
            'website' => [
                'type' => 'string',
                'nullable' => true
            ],
            'email' => [
                'type' => 'string',
                'nullable' => true
            ],
            'localArea' => [
                'type' => 'string',
                'nullable' => true
            ],
            'category' => [
                'type' => 'string',
                'nullable' => true
            ],
            'productsRepaired' => [
                'type' => 'array',
                'nullable' => true
            ],
            'authorised' => [
                'type' => 'boolean'
            ],
            'qualifications' => [
                'type' => 'string',
                'nullable' => true
            ],
            'reviews' => [
                'type' => 'array',
                'nullable' => true
            ],
            'positiveReviewPc' => [
                'type' => 'integer',
                'nullable' => true
            ],
            'warranty' => [
                'type' => 'text',
                'nullable' => true
            ],
            'pricingInformation' => [
                'type' => 'text',
                'nullable' => true
            ]
        ]
    ]
];