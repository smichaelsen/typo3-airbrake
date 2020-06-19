<?php

$EM_CONF['airbrake'] = [
    'title' => 'Airbrake',
    'description' => 'Log exceptions to airbrake',
    'category' => 'services',
    'state' => 'stable',
    'author' => 'Sebastian Michaelsen',
    'author_email' => 'sebastian@michaelsen.io',
    'author_company' => '',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
    ],
];
