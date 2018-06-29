<?php

namespace Dingo\Api\Http\Validation;

use Illuminate\Http\Request;
use Dingo\Api\Contract\Http\Validator;
use Dingo\Api\Http\Parser\Accept as AcceptParser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Accept implements Validator
{
    /**
     * Accept parser instance.
     *
     * @var \Dingo\Api\Http\Parser\Accept
     */
    protected $accept;

    /**
     * Indicates if the accept matching is strict.
     *
     * @var bool
     */
    protected $strict;

    /**
     * Create a new accept validator instance.
     *
     * @param \Dingo\Api\Http\Parser\Accept $accept
     * @param bool                          $strict
     *
     * @return void
     */
    public function __construct(AcceptParser $accept, $strict = false)
    {
        $this->accept = $accept;
        $this->strict = $strict;
    }

    /**
     * Validate the accept header on the request. If this fails it will throw
     * an HTTP exception that will be caught by the middleware. This
     * validator should always be run last and must not return
     * a success boolean.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception|\Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return bool
     */
    public function validate(Request $request)
    {
        try {
            $call_callbackurl = config('toters.robo_calls.call_callback_url');
            $accept_header = config('toters.apps.backend.accept_header');
            if ($request->is($call_callbackurl)){
                $request->headers->set('accept', $accept_header, true);
            }
            $this->accept->parse($request, $this->strict);
        } catch (BadRequestHttpException $exception) {
            if ($request->getMethod() === 'OPTIONS') {
                return true;
            }

            throw $exception;
        }
    }
}
