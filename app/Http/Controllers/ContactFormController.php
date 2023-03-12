<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    public function __invoke(ContactFormRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $this->dispatch(function () use ($data) {
            Mail::raw($data['message'], function (Message $message) use ($data) {
                $message->from($data['email'], $data['name']);
                $message->to('business.sandercokart@gmail.com');
                $message->subject($data['subject']);
            });
        });

        return response()->json([
            'message' => __('responses.contact-form-response'),
        ]);
    }
}
