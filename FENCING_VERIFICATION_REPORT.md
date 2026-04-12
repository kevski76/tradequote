# Fencing Quote System - Verification Report

## Phase 2: Quote Defaults Behavior ✓

### How Quote Defaults Populate the Form

Quote defaults follow a **precedence cascade** in [FenceQuoteBuilder.php#L121-L148](app/Livewire/FenceQuoteBuilder.php):

```
Priority (highest to lowest):
1. Organisation module defaults    (organisations.quote_defaults[modules][fencing])
2. Organisation global defaults    (organisations.quote_defaults[global])
3. Config module defaults          (config/quotes.php > form_defaults.modules.fencing)
4. Config global defaults          (config/quotes.php > form_defaults.global)
```

### Current Default Values

From [config/quotes.php](config/quotes.php):

| Field | Default | Source |
|-------|---------|--------|
| `length` | 10 metres | config.modules.fencing |
| `labour_rate` | £35 per metre | config.modules.fencing |
| `markup` | 15% | config.modules.fencing |
| `waste` | 8% | config.modules.fencing |
| `vat_rate` | 20% | config.global |
| `payment_terms` | (blank, set from global) | config.global |
| `type` | 'panels' | config.modules.fencing |

### Current Implementation Status

- ✅ **Config-driven defaults:** All defaults pulled from `config/quotes.php` on mount
- ✅ **Infrastructure for org-specific overrides:** `organisations.quote_defaults` JSON column exists and is checked
- ⚠️ **Not yet used:** No organisation has custom quote_defaults set (database column is NULL/empty for all orgs)

### Method Call Chain

1. `FenceQuoteBuilder::mount()` (line 76)
   ↓
2. `applyDefaultFormValues()` (line 121-147)
   - Loads config via `config('quotes.form_defaults.*')`
   - Loads org defaults via `loadOrganisationFormDefaults($organisationId)`
   - Merges with precedence: org > config, module > global
   ↓
3. Properties set: `$this->length = '10'`, `$this->labourRate = '35'`, etc.
   ↓
4. Form rendered with pre-populated values

### Example: Setting a Custom Organisation Default

To override defaults for a specific organisation, populate the `quote_defaults` JSON column:

```json
{
  "global": {
    "vat_rate": 20,
    "payment_terms": "50% upfront, balance on completion"
  },
  "modules": {
    "fencing": {
      "length": 15,
      "labour_rate": 40,
      "markup": 20,
      "waste": 10
    }
  }
}
```

**Note:** This would require a UI in Settings to configure per-organisation. Currently not implemented.

---

## Phase 3: Gate Design & Pricing Configuration ✓

### Why Gate is NOT a Database Module Item

**Design Decision:** Gate is injected dynamically at calculation time (not stored as a module_item row) because:

1. **Flexibility:** Gate pricing can vary per-quote without DB modification
2. **Optional:** Not all fences have gates; dynamic injection avoids empty rows
3. **UI simplicity:** Gate is toggled on/off; module_items are simpler to list as pre-loaded items
4. **Future refactor:** If all quotes use same gate price, can be moved to a module_item later

### Current Gate Implementation

Located in [FenceCalculator.php#L114-L124](app/Calculators/FenceCalculator.php):

```php
// Gate item injected when gate_width > 0
if ($gateWidth > 0 && ! $this->hasKey($result, 'gate')) {
    $gatePrice = isset($itemInputs['gate']['price']) ? (float) $itemInputs['gate']['price'] : 0.0;
    $result[] = [
        'module_item_id' => 0,
        'key' => 'gate',
        'name' => 'Gate (' . number_format($gateWidth, 1) . 'm)',
        'type' => 'material',
        'quantity' => 1.0,
        'unit_price' => round($gatePrice, 2),
        'total' => round($gatePrice, 2),
    ];
}
```

### Gate Pricing Configuration (Current)

- **Initial value:** £0 (user must enter)
- **No config default:** Unlike labour_rate (£35), there's no default gate price in config/quotes.php
- **User input:** Wire binding in [fence-quote-builder.blade.php](resources/views/livewire/fence-quote-builder.blade.php) collects user-entered gate price

### Gate Pricing Proposal (Future Enhancement)

**Recommendation:** Add default gate price to config so users don't start at £0:

```php
// In config/quotes.php > form_defaults.modules.fencing
'gate_price' => 50, // Default £50 gate
```

Then in FenceCalculator, use this as fallback when gate_price not explicitly set.

### Gate Calculation Logic

**Effective Length Calculation** [FenceCalculator.php#L37-L40](app/Calculators/FenceCalculator.php):

```
effective_length = length - gate_width  (when 0 < gate_width < length)
```

**Impact:**
- **Materials** (posts, panels, gravel_boards, rails): Use `effective_length`
  - Example: 15m fence with 1.5m gate = 13.5m effective
  - posts = ceil(13.5 / 1.8) + 1 = 9 posts (not 10)
- **Labour:** Uses **full length** (15m), not effective_length
  - Rationale: Worker installs labour across full distance including gate
- **Gate:** Separate line item with quantity=1, user-specified price

### Effective Length Formula

```
If gate_width is 0 or >= length:
  effective_length = full length (no reduction)

If 0 < gate_width < length:
  effective_length = length - gate_width

Example calculations:
- 15m fence, no gate (0m): materials use 15m ✓
- 15m fence, 1.5m gate: materials use 13.5m, labour 15m ✓
- 15m fence, 20m gate (invalid): materials use 15m (gate_width >= length) ✓
```

---

## Summary

| Phase | Status | Key Finding |
|-------|--------|-------------|
| 1. Database | ✅ Complete | Fencing module + 6 items + prices all seeded |
| 2. Defaults | ✅ Complete | Config-driven; org overrides infrastructure ready but unused |
| 3. Gate Design | ✅ Complete | Dynamic injection (not DB row); no default price currently |
| 4. E2E Testing | 📋 Ready | Manual checklist prepared (see Phase 4 below) |

---

## Phase 4: End-to-End Manual Verification Checklist

### Prerequisites
- Ensure logged-in user has `organisation_id = 1` (which has fencing prices)
- Clear browser cache (or use incognito window)
- Have browser DevTools open to check network and console

### Test 1: Gate Toggle Visibility ✓

**Steps:**
1. Navigate to FenceQuoteBuilder form
2. Verify "Include gate" toggle appears under Length input
3. Toggle **OFF** → gate_width input should **hide**, gateWidth = '0'
4. Toggle **ON** → gate_width input should **appear**
5. Toggle **OFF** again → input hides, gateWidth resets to '0'

**Expected:** Toggle controls visibility; state persists across Network requests

**Browser Check:** DevTools > Network tab, confirm `wire:` updates are sent on each toggle

---

### Test 2: Decimal Typing (1.5) ✓

**Steps:**
1. In **Length input:** Type `15.5` (slowly) → should display "15.5"
2. In **Gate width input:** Type `1.5` (slowly) → should display "1.5"
3. In **Labour rate input:** Type `35.5` → should display "35.5"
4. Leave the form and refresh → values persist

**Expected:** Decimals accepted; intermediate values not dropped (e.g., while typing "1.5", should see "1", "1.", "1.5" progression)

**Why:** Uses `wire:model.live.debounce.200ms` which preserves string type during editing

**Browser Check:** DevTools > Elements, inspect input value attribute as you type

---

### Test 3: Live Total Updates from Length Changes ✓

**Steps:**
1. Set form to: length=15, gate_width=0, labour_rate=35, markup=15%, waste=8%, vat=20%
2. Observe Item Prices card (lists posts, panels, labour, etc. with quantities)
   - Expected: posts qty = ceil(15/1.8)+1 = 9, panels = 9, labour = 15
3. Change length to `20` → **totals should update immediately** (debounce 200ms)
   - Expected: posts ≈ 12, panels ≈ 12, labour = 20
4. Change length to `10` → totals update again
   - Expected: posts ≈ 6, panels ≈ 6, labour = 10

**Expected:** Live updates without page reload; calculations recalculate on length change

**Browser Check:** DevTools > Network, confirm Livewire requests (`wire:update`) sent for each change

---

### Test 4: Live Totals from Gate Width Changes ✓

**Steps:**
1. Set form: length=15, gate_width=0
2. **Before adding gate:**
   - Note material quantities (posts=9, panels=9, etc.)
   - Note labour = 15m
3. Toggle "Include gate" **ON**
4. Enter gate_width=`1.5` → **effective_length = 15 - 1.5 = 13.5**
   - Expected posts = ceil(13.5/1.8)+1 = 8 (decreased from 9)
   - Expected panels = 8 (decreased from 9)
   - Expected labour = **15** (unchanged - uses full length)
   - Gate item appears with quantity=1, price=(user enters, initially £0)
5. Enter gate_price=`50` (in gate price field if visible, or via itemInputs)
   - Expected: Gate total = £50

**Expected:** Material quantities adjust for effective_length; labour unchanged; gate item appears with user-entered price

**Browser Check:** Inspect calculation_data in network response to verify effective_length math

---

### Test 5: Calculation Order Verification ✓

**Spot Check Example:**
- **Input:** length=10m, labour_rate=£35, waste=8%, markup=15%, vat=20%, no gate
- **Expected Calculation:**

  | Step | Calculation | Result |
  |------|-------------|--------|
  | 1a. Materials | posts(6)×£8 + panels(6)×£75 + labour(0) | +   |
  | 1b. Gravel/Rails | gravel(6)×£15 + rails(14)×£12 (if enabled) | ... |
  | 2. With Waste | subtotal × 1.08 | (8% added) |
  | 3. Labour | labour(10m)×£35 | +£350 |
  | 4. Subtotal | materials_w_waste + labour | = X |
  | 5. With Markup | X × 1.15 | (15% added) |
  | 6. VAT | result × 0.20 | (20% added) |
  | 7. **Final** | subtotal_with_markup + vat | **TOTAL** |

- **Actual in UI:** Take screenshot of "TOTAL" field, manually calculate above to verify

**Expected:** Final total matches manual calculation; order is (waste before labour, then markup, then VAT)

**Browser Check:** DevTools > Network, inspect last Livewire response for `calculation_data` JSON payload

---

## Notes for Phase 4 Testing

- **Timing:** Debounce is 200ms; allow brief pause after typing before checking updates
- **Organisation:** Tests assume Org ID 1 (default from seeder)
- **Rounding:** All prices rounded to 2 decimals; quantities rounded to 2 decimals (but display as integers where appropriate)
- **Optional Items:** gravel_boards and rails are optional (is_optional=true); ensure they're enabled in UI toggle or auto-included by default
- **Browser Caching:** Clear `wire:` request cache if seeing stale data

---

## Next Steps

If all Phase 4 tests pass without issues, the fencing quote system is **ready for production verification**. If issues found, capture screenshots and error messages in DevTools for debugging.
