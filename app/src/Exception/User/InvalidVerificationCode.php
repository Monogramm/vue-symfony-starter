<?php


namespace App\Exception\User;

use App\Exception\ApiExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class InvalidVerificationCode extends HttpException implements ApiExceptionInterface
{
    protected const ERROR_CODE  = 2001;
    protected const STATUS_CODE = Response::HTTP_BAD_REQUEST;
    protected const MESSAGE     = 'error.user.invalid.verification.code';

    public function __construct(Throwable $previous = null, array $headers = [])
    {
        parent::__construct(
            self::STATUS_CODE,
            self::MESSAGE,
            $previous,
            $headers,
            self::ERROR_CODE
        );
    }
}
