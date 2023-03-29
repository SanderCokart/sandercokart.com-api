<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/contact",
     *      tags={"Sander's CodeHouse"},
     *      description="Send a message to the owner of the website",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "subject", "message"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="username@domain.com"),
     *              @OA\Property(property="subject", type="string", example="I have a question"),
     *              @OA\Property(property="message", type="string", example="Why are you so awesome?"),
     *          ),
     *      ),
     *      @OA\Response(response="200", description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="We have received your message and would like to thank you for writing to us."),
     *          ),
     *      ),
     *      @OA\Response(response="429", description="Too many requests",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Too many requests. Please try again in 10 minutes."),
     *          )),
     *      ),
     */
    public function __invoke(ContactFormRequest $request): JsonResponse
    {
        Mail::to('codehouse@sandercokart.com', 'Sander Cokart')
            ->queue(new ContactFormMail(
                    senderName: $request->safe()->name,
                    senderEmail: $request->safe()->email,
                    senderSubject: $request->safe()->subject,
                    senderMessage: $request->safe()->message,
                )
            );

        return response()->json([
            'message' => __('responses.contact-form-response'),
        ]);
    }
}
