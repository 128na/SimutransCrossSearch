<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'present' => ':attributeが存在していません。',
    'string' => ':attributeは文字列を指定してください。',
    'max' => [
        'numeric' => ':attributeには、:max以下の数字を指定してください。',
        'file' => ':attributeには、:max kB以下のファイルを指定してください。',
        'string' => ':attributeは、:max文字以下で指定してください。',
        'array' => ':attributeは:max個以下指定してください。',
    ],
    'required' => ':attributeは必ず指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'keyword' => 'キーワード',
        'paks.*' => 'パックセット',
        'sites.*' => 'サイト',
    ],

];
