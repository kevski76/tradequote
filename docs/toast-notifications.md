# Toast Notifications

Global toast alerts are used for all form feedback across the portal (saves, updates, errors, etc.). They auto-dismiss after 4 seconds and support stacking multiple messages.

## How it works

The `<x-toast-manager />` component lives in `resources/views/layouts/app/sidebar.blade.php` and listens globally for `toast` browser events dispatched by any Livewire component on the page.

## Dispatching a toast from PHP (Livewire)

```php
// Success (green) — default type
$this->dispatch('toast', message: 'Quote saved successfully.', type: 'success');

// Info (indigo)
$this->dispatch('toast', message: 'Template loaded. Tweak values and save your quote.', type: 'info');

// Error (red)
$this->dispatch('toast', message: 'Something went wrong. Please try again.', type: 'error');

// Custom duration in milliseconds (default is 4000)
$this->dispatch('toast', message: 'Done!', type: 'success', duration: 6000);
```

## Dispatching from JavaScript / Alpine

```js
window.dispatchEvent(new CustomEvent('toast', {
    detail: { message: 'Saved!', type: 'success' }
}));
```

## Types

| Type      | Colour  | Use for                              |
|-----------|---------|--------------------------------------|
| `success` | Green   | Saved, updated, created, deleted     |
| `error`   | Red     | Validation failures, unexpected errors |
| `info`    | Indigo  | Neutral notices, tips, loaded states |

## Files

| File | Role |
|------|------|
| `resources/views/components/toast-manager.blade.php` | Alpine.js toast stack component |
| `resources/views/layouts/app/sidebar.blade.php` | Mounts `<x-toast-manager />` globally |
