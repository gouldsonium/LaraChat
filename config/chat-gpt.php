<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OPEN AI API KEY
    |--------------------------------------------------------------------------
    |
    | This is the key found at: https://platform.openai.com/api-keys which is
    | used to authenticate your request. Ensure you have set the permission to
    | all
    |
    */

    'key' => env('OPEN_AI_KEY'),

        /*
    |--------------------------------------------------------------------------
    | Model costs
    |--------------------------------------------------------------------------
    |
    | Upon submitting a request, Chat-GPT uses token to measure its cost. The
    | input token and output tokens vary in cost depending on which model you
    | use
    |
    */

    'models' => [
        'gpt-4o' => ['input' => 0.000005, 'output' => 0.000015],
        'gpt-4' => ['input' => 0.000003, 'output' => 0.000004],
        'gpt-4-turbo' => ['input' => 0.0000015, 'output' => 0.000002],
        'gpt-4o-mini' => ['input' => 0.0000012, 'output' => 0.0000018],
    ]
];
