<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Jobs\ProcessContactForm;
use Illuminate\Http\JsonResponse;

class ContactFormController extends Controller
{
    /**
     * @OA\Post(
     *      path="/contact",
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
        ProcessContactForm::dispatch(...$request->safe()->only(['name', 'email', 'message', 'subject']));

        return response()->json([
            'message' => __('responses.contact-form-response'),
        ]);
    }
}
