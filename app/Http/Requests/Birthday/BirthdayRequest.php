<?php

namespace App\Http\Requests\Birthday;

use App\Traits\Paginateable;
use Illuminate\Foundation\Http\FormRequest;

class BirthdayRequest extends FormRequest
{
    use Paginateable;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->defaultPage = config('app.pagination.birthdays.page');
        $this->defaultPerPage = config('app.pagination.birthdays.per_page');
    }

    public function rules(): array
    {
        return $this->withPaginationRules([
        ]);
    }
}
