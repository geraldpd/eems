<?php

return [

    'monthly_max_event' => [
        0 => 5,     //unapproved organizer
        1 => 50     //approved organizer
    ],

    'roles' => [
        'admin',
        'organizer',
        'attendee',
    ],

    'event_types' => [
        'Seminars',
        'Trainings',
        'Fora',
        'Workshops',
        'Webinars',
    ],

    'default_event_min_time' => [
        'start' => '08:00:00', // 08:00 AM
        'end' => '17:00:00', // 05:00 PM
    ],

    'evaluation_types' => [
        'text' => [
            'minlength',
            'maxlength',
            'required',
        ],

        'number' => [
            'min',
            'max',
            'required'
        ],

        'date' => [
            'min',
            'max',
            'required'
        ],

        'select' => [
            'options',
            'required'
        ],

        'checkbox' => [
            'options',
            'required'
        ],

        'radio' => [
            //'left-text',
            //'right-text',
            'options',
            'required'
        ]

    ]

];
