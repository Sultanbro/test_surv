<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class EditPersonRequest extends FormRequest
{

    protected $errorBag = 'default';


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'position' => 'required',     // position_id Position::class
            'zarplata' => 'required',     // zarplata Zarplata::class
            'card_number' => 'required:digits:16',  // card_number Zarplata::class
            'user_type' => 'in:'.User::USER_TYPE_OFFICE.','.User::USER_TYPE_REMOTE,
            'program_type' => 'required', // program_id
            'working_days' => 'required',  // working_day_id
            'working_times' => 'required', // working_time_id
            // 'file1' => 'mimes:pdf,zip,rar|max:2048',
            // 'file2' => 'mimes:pdf,zip,rar|max:2048',
            // 'file3' => 'mimes:pdf,zip,rar|max:2048',
            // 'file4' => 'mimes:pdf,zip,rar|max:2048',
            // 'file5' => 'mimes:pdf,zip,rar|max:2048',
            // 'file7' => 'mimes:zip,rar|max:10000',
        ];
    }
}
