<?php

namespace App\Http\Responses;

use Illuminate\Pagination\AbstractPaginator;

class PaginatedResponse extends Base
{
    /**
     * Формирование содержимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        if ($this->data instanceof AbstractPaginator) {
            $this->data = $this->data->toArray();
        }

        return $this->prepareData();
    }
}
