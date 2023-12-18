<?php
test('Api is online', function () {
    $response = \Pest\Laravel\getJson('/');
    expect($response->status())->toBe(200);
});
