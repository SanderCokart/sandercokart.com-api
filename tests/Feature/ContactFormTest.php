<?php

use function Pest\Laravel\postJson;

it('Contact form works',
    /**
     * @param $data array{
     *      submittedData: array{
     *          name: string,
     *          email: string,
     *          subject: string,
     *          message: string
     *     },
     *     expected: array {
     *          status: int
     *     },
     * }
     */
    function (array $data) {
        $response = postJson(route('api.contact'), $data['submittedData']);
        expect($response->status())->toBe($data['expected']['status']);
    }
)->with('ContactFormPestDataset');
