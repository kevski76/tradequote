<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Organisations;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password'      => $this->passwordRules(),
            'business_name' => ['required', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'postcode'      => ['nullable', 'string', 'max:20'],
            'phone'         => ['required', 'string', 'regex:/^[\+]?[\d\s\-\(\)\.]{7,20}$/'],
            'google_review_url' => ['nullable', 'url', 'max:255'],
        ])->validate();

        $global_defaults = config('quotes.form_defaults.global', []);
        if (! empty($input['google_review_url'])) {
            $global_defaults['google_review_url'] = $input['google_review_url'];
        }
        
        $global_defaults['feedback_notification_email'] = $input['email'];

        return DB::transaction(function () use ($input, $global_defaults): User {
            $user = User::create([
                'name'     => $input['name'],
                'email'    => $input['email'],
                'password' => $input['password'],
            ]);

            $organisation = Organisations::create([
                'name'     => $input['business_name'],
                'owner_id' => $user->id,
                'address'  => $input['address'] ?? null,
                'city'     => $input['city'] ?? null,
                'postcode' => $input['postcode'] ?? null,
                'phone'    => $input['phone'],
                'quote_defaults' => [
                    'global' => $global_defaults, 
                    'modules' => config('quotes.form_defaults.modules', []),
                ],
            ]);

            $user->update([
                'organisation_id'   => $organisation->id,
                'organisation_role' => 'owner',
            ]);

            return $user->fresh();
        });
    }
}
