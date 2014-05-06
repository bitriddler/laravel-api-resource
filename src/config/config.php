<?php

return array(
    'routes' => array(
        'resources' => array(
            'prefix' => '/api/1',
        ),
        'application' => array(
            'prefix' => 'application',
        )
    ),

    'filters' => array(
        'creation' => 'auth'
    ),

    'application_management' => true
);