<?php

namespace App\Livewire;

use App\Notifications\EarlyAccessSubmitted;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EarlyAccessRegistration extends Component
{
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|string|max:150')]
    public string $business_name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:500')]
    public string $quoting_method = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        $recipient = config('mail.from.address');

        Notification::route('mail', $recipient)
            ->notify(new EarlyAccessSubmitted([
                'name' => $this->name,
                'business_name' => $this->business_name,
                'email' => $this->email,
                'quoting_method' => $this->quoting_method,
            ]));

        $this->submitted = true;

        $this->dispatch('early-access-submitted');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.early-access-registration');
    }
}
