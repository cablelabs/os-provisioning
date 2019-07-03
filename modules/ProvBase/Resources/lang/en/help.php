<?php

/*
|--------------------------------------------------------------------------
| Language lines for module ProvBase
|--------------------------------------------------------------------------
|
| The following language lines are used by the module ProvBase
 */

return [
    'documentTemplate' => [
        'templateRelation' => 'If company and SEPA account are empty: Base template. If company is set and SEPAaccount is empty: Used for documents for the chosen company. If SEPA account is set: Used to create documents for the chosen SEPA account. Cannot be changed at base templates!',
        'filenamePattern' => 'Pattern for the name of the files to be created. Strings in curly brackets are placeholders and will be replaced. Leave empty to use default pattern.',
        'uploadTemplate' => 'Filenames will be converted to lowercase, spaces and slashes will be replaced by underscores. At the moment only LaTeX files (*.tex) allowed.',
    ],
    'type' => 'For IPv6 currently only public CPE IP pools are supported.',
];
