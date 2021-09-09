<?php

namespace App\Http\Responses;

use Illuminate\Validation\ValidationException;

class ValidationExceptionResponse extends Fail
{
    private const MESSAGE = 'Переданные данные не корректны.';
    /**
     * ExceptionResponse constructor.
     *
     * @param ValidationException $exception
     * @param int|null $code
     */
    public function __construct(protected ValidationException $exception, int $code = null)
    {
        $code = $code ?: $exception->getCode() ?: $exception->status;

        parent::__construct([], self::MESSAGE, $code);
    }

    /**
     * Преобразование возвращаемых данных к массиву.
     *
     * @return array
     */
    protected function prepareData(): array
    {
        return $this->exception->validator->errors()->messages();
    }
}
