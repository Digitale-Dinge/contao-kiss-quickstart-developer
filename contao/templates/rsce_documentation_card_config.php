<?php

declare(strict_types=1);

use Contao\System;

$configBuilder = System::getContainer()->get('kiss.rsce_config.builder');

return $configBuilder
    ->create('documentation_card', 'texts', [
        'types' => ['content'],
        'standardFields' => ['cssID'],
    ])
    ->addSelectField('documentationLevel', ['section', 'subsection'])
    ->addField('topline', [
        'label' => true,
        'inputType' => 'text',
        'eval' => [
            'tl_class' => 'w50 clr',
        ],
        'dependsOn' => [
            'field' => 'documentationLevel',
            'value' => 'section',
        ],
    ])
    ->addField('title', [
        'label' => true,
        'inputType' => 'text',
        'eval' => [
            'tl_class' => 'w50 clr',
            'mandatory' => true,
        ],
    ])
    ->addField('text', [
        'label' => true,
        'inputType' => 'textarea',
        'eval' => [
            'tl_class' => 'long clr',
            'allowHtml' => true,
        ],
    ])
    ->build()
;
