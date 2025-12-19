# Label Checks JSONB Design

## Overview
Store checkbox check information (Checker Name, Chor ID, Remarks, Web PIN) in a JSONB column `label_checks` in the `market_lists` table.

## Database Structure

### Column: `label_checks` (JSONB)
Stores detailed information for each checked checkbox.

**Structure:**
```json
{
  "4x": {
    "checked": true,
    "checker_name": "Harsukh",
    "chor_id": "123",
    "remark": "Checked and verified",
    "checked_by": 1,
    "checked_at": "2025-12-19 17:00:00"
  },
  "b2c": {
    "checked": true,
    "checker_name": "Harsukh",
    "chor_id": "456",
    "remark": "All rates working",
    "checked_by": 1,
    "checked_at": "2025-12-19 17:05:00"
  },
  "b2b": {
    "checked": false
  },
  "usdt": null
}
```

## Benefits of JSONB Approach

✅ **Simpler**: No joins needed, all data in one place  
✅ **Efficient**: PostgreSQL JSONB is fast and indexable  
✅ **Queryable**: Can query JSONB fields directly  
✅ **Flexible**: Easy to add/remove fields  
✅ **Atomic**: Update entire structure in one operation  

## Usage Examples

### Check a checkbox with metadata:
```php
$labelChecks = json_decode($market->label_checks ?? '{}', true);
$labelChecks['4x'] = [
    'checked' => true,
    'checker_name' => auth()->user()->name,
    'chor_id' => $request->chor_id,
    'remark' => $request->remark,
    'checked_by' => auth()->id(),
    'checked_at' => now()->toDateTimeString(),
];

DB::table('market_lists')
    ->where('id', $marketId)
    ->update([
        'label_checks' => json_encode($labelChecks),
        'labels' => json_encode(array_merge($labels, ['4x' => true])),
    ]);
```

### Uncheck a checkbox:
```php
$labelChecks = json_decode($market->label_checks ?? '{}', true);
unset($labelChecks['4x']); // or set to null

DB::table('market_lists')
    ->where('id', $marketId)
    ->update([
        'label_checks' => json_encode($labelChecks),
        'labels' => json_encode(array_merge($labels, ['4x' => false])),
    ]);
```

### Query JSONB (PostgreSQL):
```php
// Find all markets where 4x was checked by user ID 1
DB::table('market_lists')
    ->whereRaw("label_checks->>'4x'->>'checked_by' = ?", [1])
    ->get();

// Find markets checked in last 24 hours
DB::table('market_lists')
    ->whereRaw("label_checks->>'4x'->>'checked_at' > ?", [now()->subDay()])
    ->get();
```

### Display in Blade:
```php
@php
    $labelChecks = json_decode($market->label_checks ?? '{}', true);
    $checkInfo = $labelChecks['4x'] ?? null;
@endphp

@if($checkInfo && ($checkInfo['checked'] ?? false))
    Checker: {{ $checkInfo['checker_name'] ?? '—' }}<br>
    Chor ID: {{ $checkInfo['chor_id'] ?? '—' }}<br>
    Remark: {{ $checkInfo['remark'] ?? '—' }}<br>
    Checked at: {{ $checkInfo['checked_at'] ?? '—' }}
@endif
```

## Migration

Run: `php artisan migrate`

This will add the `label_checks` JSONB column to `market_lists` table.

## When to Use JSONB vs Separate Table

**Use JSONB (this approach) when:**
- Data is mostly read per market (not queried across markets)
- Structure is flexible and may change
- Simplicity is preferred
- PostgreSQL is your database

**Use Separate Table when:**
- Need complex queries across all markets
- Need strict referential integrity
- Need detailed reporting/analytics
- Data structure is fixed

For this use case, **JSONB is perfect** ✅

