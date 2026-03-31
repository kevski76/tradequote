<?php

use App\Livewire\EarlyAccessRegistration;
use App\Notifications\EarlyAccessSubmitted;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('early access form renders', function () {
    Livewire::test(EarlyAccessRegistration::class)
        ->assertSee('Get early access')
        ->assertSet('submitted', false);
});

test('name is required', function () {
    Livewire::test(EarlyAccessRegistration::class)
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertHasErrors(['name' => 'required']);
});

test('business name is required', function () {
    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertHasErrors(['business_name' => 'required']);
});

test('email is required', function () {
    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->call('submit')
        ->assertHasErrors(['email' => 'required']);
});

test('email must be valid', function () {
    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'not-an-email')
        ->call('submit')
        ->assertHasErrors(['email' => 'email']);
});

test('quoting method is optional', function () {
    Notification::fake();

    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertHasNoErrors(['quoting_method']);
});

test('successful submission sends notification to system email', function () {
    Notification::fake();

    config(['mail.from.address' => 'admin@example.com']);

    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'john@example.com')
        ->set('quoting_method', 'Excel spreadsheets')
        ->call('submit')
        ->assertHasNoErrors();

    Notification::assertSentOnDemand(
        EarlyAccessSubmitted::class,
        fn ($notification, $channels, $notifiable) => in_array($notifiable->routes['mail'], ['admin@example.com'])
    );
});

test('submitted state is true after successful submission', function () {
    Notification::fake();

    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertSet('submitted', true);
});

test('early-access-submitted event is dispatched after successful submission', function () {
    Notification::fake();

    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertDispatched('early-access-submitted');
});

test('success state is shown after submission', function () {
    Notification::fake();

    Livewire::test(EarlyAccessRegistration::class)
        ->set('name', 'John Doe')
        ->set('business_name', 'JD Fencing Ltd')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertSee("You're on the list!")
        ->assertDontSee('Get early access');
});
