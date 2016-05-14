<?php

$EM_CONF['airbrake'] = array(
    'title' => 'Airbrake',
    'description' => 'Log exceptions to airbrake',
    'category' => 'services',
    'state' => 'stable',
    'author' => 'Sebastian Michaelsen',
    'author_email' => '',
    'author_company' => 'app-zap',
    'version' => '1.0.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.2-7.99.99',
        ),
    ),
);
