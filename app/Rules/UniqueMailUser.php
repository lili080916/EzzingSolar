<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Models\User;

class UniqueMailUser implements Rule
{
    protected $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = User::where('email', $value)
                    ->where('id', '!=', $this->id)
                    ->first();

        return $user ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The email already exists.';
    }
}
