# Quick Reference: Fencing Quote E2E Testing

## Pre-Test Setup

```bash
# 1. Verify database
php artisan verify:fencing-db

# 2. Ensure you're logged in as a user with organisation_id=1
# 3. Open FenceQuoteBuilder: /tradequote/create?type=fencing
# 4. Open DevTools (F12) > Network tab
```

---

## Test Checklist

### ✓ Test 1: Gate Toggle Visibility (< 1 min)
- [ ] "Include gate" toggle visible under Length input
- [ ] Toggle OFF → gate_width input **hides**, gateWidth = '0'
- [ ] Toggle ON → gate_width input **appears**
- [ ] Toggle OFF again → hides again, gateWidth = '0'

### ✓ Test 2: Decimal Typing (< 2 min)
- [ ] Length: Type slowly "1.5" → displays "1.5" ✓
- [ ] Gate width: Type slowly "1.5" → displays "1.5" ✓
- [ ] Labour rate: Type "35.5" → displays "35.5" ✓
- [ ] Values persist on blur/refresh

### ✓ Test 3: Live Updates from Length Change (< 2 min)
- [ ] Set: length=15, gate_width=0, labour_rate=35
- [ ] Observe Item Prices card: posts≈9, panels≈9, labour=15
- [ ] Change length to 20 → ALL totals update (no reload)
  - [ ] posts≈12, panels≈12, labour=20
- [ ] Change to 10 → totals decrease

### ✓ Test 4: Gate Width Effects (< 3 min)
- [ ] Start: length=15, gate_width=0
- [ ] Note material qtys (posts=9, panels=9)
- [ ] Toggle gate ON, enter gate_width=1.5
- [ ] **Verify effective_length = 13.5:**
  - [ ] posts = ceil(13.5/1.8)+1 = 8 ✓ (decreased)
  - [ ] panels = 8 ✓ (decreased)
  - [ ] labour = 15 ✓ (unchanged - full length)
- [ ] Enter gate_price=50 → Gate item shows £50 total

### ✓ Test 5: Calculation Order (< 2 min)
- [ ] Set: length=10m, labour=£35, waste=8%, markup=15%, vat=20%
- [ ] Expected calc: materials → waste → labour → markup → vat
- [ ] Compare UI TOTAL against manual calculation
- [ ] Verify order is correct (not vat first, etc.)

---

## Expected Starting State

| Input | Default |
|-------|---------|
| Length | 10m |
| Labour Rate | £35/m |
| Markup | 15% |
| Waste | 8% |
| VAT | 20% |
| Gate Width | 0 (disabled) |

---

## Example Calculation (for Test 5 validation)

**Inputs:** 10m fence, £35 labour, 15% markup, 8% waste, 20% VAT

| Step | Calculation | Value |
|------|-------------|-------|
| Materials | posts(6)×£8 + panels(6)×£75 = £498 | £498 |
| With Waste | £498 × 1.08 | £537.84 |
| Labour | 10m × £35 | £350 |
| Subtotal | £537.84 + £350 | £887.84 |
| With Markup | £887.84 × 1.15 | £1,021.02 |
| VAT (20%) | £1,021.02 × 0.20 | £204.20 |
| **TOTAL** | | **£1,225.22** |

---

## Browser DevTools Tips

**Network Tab:**
- Filter `wire:` requests to see Livewire updates
- Inspect response JSON for `calculation_data`

**Console:**
- Check for any JavaScript errors
- Verify no 422 validation errors

**Elements Tab:**
- Inspect input value attributes as you type decimals
- Verify gate price input is wired correctly

---

## Pass/Fail Criteria

| Test | Pass | Fail |
|------|------|------|
| 1. Toggle | Visibility toggles, state persists | Toggle broken or state resets |
| 2. Decimals | All inputs accept 1.5-style decimals | Decimals rejected or dropped |
| 3. Length Updates | Instant updates on length change | Stale totals or requires refresh |
| 4. Gate Width | Materials ↓, labour ↑, gate item appears | Gate not reducing materials qty |
| 5. Calc Order | Manual calc matches UI total | Totals don't match expected |

---

## If Test Fails

1. **Screenshot the issue** (esp. Item Prices card, totals)
2. **Copy DevTools Network request/response** (if calc error)
3. **Check browser console** for errors
4. **Verify user org_id=1** (has fencing prices)
5. **Clear cache** and retry (Ctrl+Shift+Delete)

---

## Shortcuts to Re-test

**Reload form:** F5 (clears inputs)  
**Undo gate toggle:** Toggle OFF then ON  
**Reset inputs:** Close browser tab, re-open  
**View calculation data:** DevTools > Network > filter `wire:` > Response tab

---

## Documentation Links

- Full Report: [FENCING_VERIFICATION_REPORT.md](FENCING_VERIFICATION_REPORT.md)
- Implementation Summary: [IMPLEMENTATION_COMPLETE.md](IMPLEMENTATION_COMPLETE.md)
- Database Verification: `php artisan verify:fencing-db`

**Questions?** Check the full report for detailed diagnostic steps.
