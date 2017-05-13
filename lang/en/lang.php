<?php return [
    'plugin' => [
        'name' => 'News PDF',
        'description' => 'Adding the feature to generate PDF\'s in News and Newsletters plugin.'
    ],
    'fields' => [
        'template_code' => [
            'label' => 'Template',
            'comment' => 'Choose a PDF template',
        ]
    ],
    'tab' => 'PDF template',

    'error' => [
        'no_template_set' => 'No pdf template set'
    ]

];