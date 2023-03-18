<?php

dataset('ContactFormPestDataset', function () {
    $default = [
        'submittedData' => [
            'name'    => 'John Doe',
            'email'   => 'username@domain.com',
            'subject' => 'Test subject',
            'message' => 'Test message',
        ],
        'expected'      => [
            'status' => 200,
        ],
    ];

    //success
    yield function () use ($default) {
        return $default;
    };

    //validation error
    yield function () use ($default) {
        return array_merge($default, [
            'submittedData' => [
                'name'    => '',
                'email'   => '',
                'subject' => '',
                'message' => '',
            ],
            'expected'      => [
                'status' => 422,
            ],
        ]);
    };
});
