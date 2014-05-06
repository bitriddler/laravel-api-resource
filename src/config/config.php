<?php

return array(
    'routes' => array(
        'prefix' => '/api/1',
        'domain' => '',
    ),

    'filters' => array(
        'creation' => 'auth'
    ),

    'application_management' => true
);