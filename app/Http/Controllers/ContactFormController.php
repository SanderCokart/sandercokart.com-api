<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Jobs\ProcessContactForm;

class ContactFormController extends Controller
{
    public function __invoke(ContactFormRequest $request): \Illuminate\Http\JsonResponse
    {
        ProcessContactForm::dispatch(...$request->safe()->only(['name', 'email', 'message', 'subject']));

        return response()->json([
            'message' => __('responses.contact-form-response'),
        ]);
    }
}
