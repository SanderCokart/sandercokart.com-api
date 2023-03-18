<?php
it('Api is online', function () {
    $response = \Pest\Laravel\getJson(route('hello-world'));
    expect($response->status())->toBe(200);
});
