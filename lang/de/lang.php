<?php return [
    'plugin' => [
        'name' => 'News PDF',
        'description' => 'Integriert PDF Erstellung in Nachrichten Plugin.'
    ],
    'fields' => [
        'template_code' => [
            'label' => 'Vorlage',
            'comment' => 'Wähle eine PDF-Vorlage',
        ]
    ],
    'tab' => 'PDF Vorlage',

    'error' => [
        'no_template_set' => 'Kein Template gesetzt'
    ]

];