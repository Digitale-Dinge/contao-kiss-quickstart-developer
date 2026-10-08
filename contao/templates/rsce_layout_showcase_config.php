<?php

declare(strict_types=1);

use Contao\System;

$configBuilder = System::getContainer()->get('kiss.rsce_config.builder');

return $configBuilder
    ->create('layout_showcase', 'texts', [
        'types' => ['content'],
        'standardFields' => ['cssID'],
    ])
    ->build()
;
