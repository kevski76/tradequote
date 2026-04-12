Trade Quote System Architecture
Overview

This system is a module-driven quoting engine built with Laravel + Livewire.

It is designed to be:

Scalable (support multiple trades: fencing, paving, roofing, etc.)
Maintainable (no duplicated logic)
Dynamic (database-driven in future)
Predictable (strict calculation flow)
Core Principles
Single Source of Truth for Calculations
No Business Logic in Livewire or Blade
Modular but Not Duplicated
Strict Calculation Order
Separation of Concerns
Architecture Overview
Layers

Livewire (UI Layer)
→ Module Calculators (Domain Logic)
→ QuoteService (Core Engine)

1. QuoteService (CORE ENGINE)

There is ONLY ONE QuoteService in the system.

Responsibilities
Execute full quote calculation flow
Apply pricing rules in correct order
Calculate totals:
materials
waste
labour
markup
VAT
Return structured result
Must NOT:
Contain module-specific logic (e.g. fencing formulas)
Know about panels, slabs, tiles, etc.
2. Module Calculators (Strategy Pattern)

Each module has its own calculator class.

Examples
FenceCalculator
PavingCalculator
RoofingCalculator
Interface
interface ModuleCalculator {
    public function calculateItems(array $data): array;
}
Responsibilities
Calculate item quantities
Apply module-specific rules
Return item breakdown
Must NOT:
Apply waste
Apply markup
Apply VAT
Calculate totals
3. Calculator Resolution

QuoteService dynamically resolves the correct calculator:

$calculator = $this->resolveCalculator($data['module']);
$items = $calculator->calculateItems($data);
4. Livewire Components

Each module has its own component.

Examples
FenceQuoteBuilder
PavingQuoteBuilder
RoofingQuoteBuilder
Responsibilities
Hold state (inputs)
Load defaults from settings
Pass data to QuoteService
Display results
Must NOT:
Perform calculations
Contain pricing logic
Know formulas
5. Blade Views

Blade is strictly for display.

Must NOT:
Contain calculations
Contain business logic
Hardcode pricing rules
6. Data Flow

User Input (Livewire)
→ Pass to QuoteService
→ QuoteService resolves Calculator
→ Calculator returns items
→ QuoteService calculates totals
→ Return structured result
→ Render in Blade

7. Output Format

QuoteService must return:

[
  'items' => [],
  'materials_total' => float,
  'materials_with_waste' => float,
  'labour_total' => float,
  'subtotal' => float,
  'subtotal_with_markup' => float,
  'vat' => float,
  'total' => float,
]
8. Non-Negotiable Rules
ONLY ONE QuoteService
NO duplicated calculation logic
ALL totals handled centrally
Calculators ONLY return items
Livewire is thin
Blade is dumb
9. Future Expansion

System must support:

Dynamic module_items (database-driven)
Formula-based calculations
Add-ons and optional extras
Multiple pricing tiers

Architecture must not require rewrites to support new modules.