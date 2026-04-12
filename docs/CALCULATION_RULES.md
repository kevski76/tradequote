Calculation Rules (Source of Truth)

This document defines ALL pricing and quantity rules.

QuoteService MUST follow this order EXACTLY.

🔢 Calculation Order (STRICT)
Calculate quantities (via Module Calculator)
materials_total:
Sum of all material item totals
Apply waste:
materials_with_waste = materials_total * (1 + waste%)
labour_total:
labour_total = length * labour_rate
subtotal:
subtotal = materials_with_waste + labour_total
Apply markup:
subtotal_with_markup = subtotal * (1 + markup%)
Apply VAT:
vat = subtotal_with_markup * (vat_rate / 100)
total_price:
total_price = subtotal_with_markup + vat
⚠️ Critical Rules
Labour MUST be included BEFORE markup
Markup MUST affect BOTH materials and labour
VAT MUST be applied LAST
NO module is allowed to override this order
🪵 FENCING MODULE RULES
1. Effective Length

If a gate is present:

effective_length = length - gate_width

All fencing calculations MUST use effective_length.

2. Panel Fencing

panels = ceil(effective_length / 1.8)

posts = panels + 1

gravel_boards = panels

3. Featheredge Fencing

boards = ceil(effective_length / 0.1)

posts = ceil(effective_length / 1.8) + 1

arris_rails = posts * 2

4. Fence Height Rules

Height affects pricing ONLY.

Height does NOT affect spacing or quantity formulas.

Example Pricing Logic
1.5m → lower cost
1.8m → standard cost
2.0m → higher cost

This must be handled inside the FenceCalculator.

5. Gates

Gates must follow these rules:

Gates are separate items
Gates reduce fence length
Gates add material cost
Gates may add labour cost
Gate Types
Single gate
Double gate

Each has its own pricing.

6. Labour

Default:

labour_total = length * labour_rate

Optional Extensions
Height multiplier (e.g. taller fence = more labour)
Fixed gate installation cost
7. Waste

Waste applies ONLY to materials:

materials_with_waste = materials_total * (1 + waste%)

8. Markup

Markup applies to:

Materials (after waste)
Labour
9. VAT

VAT applies to final subtotal AFTER markup.

🚫 Disallowed Logic
No calculations in Blade
No totals in calculators
No duplication of calculation order
No skipping steps
✅ Expected Behaviour
Changing labour rate updates total
Changing markup updates total
Gates reduce quantities correctly
Heights affect pricing correctly
Totals always follow strict order
🔮 Future Considerations
Database-driven formulas
Dynamic pricing rules
Add-ons (removal, disposal, upgrades)
Multi-module quotes

All future features must respect this calculation structure.