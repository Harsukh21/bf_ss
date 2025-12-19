# Labels Field with Metadata Design

## Overview
Extend the existing `labels` JSONB field to store both checkbox state AND metadata (Checker Name, Chor ID, Remarks, etc.) for each checkbox.

## Current Structure
```json
{
  "4x": true,
  "b2c": true,
  "b2b": false,
  "usdt": false
}
```

## New Structure (Backward Compatible)
```json
{
  "4x": {
    "checked": true,
    "checker_name": "Harsukh",
    "chor_id": "123",
    "remark": "All rates verified",
    "checked_by": 1,
    "checked_at": "2025-12-19 17:00:00"
  },
  "b2c": {
    "checked": true,
    "checker_name": "Harsukh",
    "chor_id": "456",
    "remark": "Working correctly",
    "checked_by": 1,
    "checked_at": "2025-12-19 17:05:00"
  },
  "b2b": false,  // Simple boolean (backward compatible)
  "usdt": false
}
```

## Benefits
✅ **No migration needed** - uses existing field  
✅ **Backward compatible** - still supports simple boolean values  
✅ **All data in one place** - checkbox state + metadata together  
✅ **Simple queries** - one field to read/write  

## Implementation

### Check Checkbox with Metadata:
```php
$labels = json_decode($market->labels ?? '{}', true);
$labels['4x'] = [
    'checked' => true,
    'checker_name' => auth()->user()->name,
    'chor_id' => $request->chor_id,
    'remark' => $request->remark,
    'checked_by' => auth()->id(),
    'checked_at' => now()->toDateTimeString(),
];
```

### Check Checkbox (Simple - backward compatible):
```php
$labels['4x'] = true; // Still works!
```

### Uncheck Checkbox:
```php
$labels['4x'] = false; // or unset($labels['4x'])
```

### Read Checkbox State:
```php
// Handle both formats
$isChecked = false;
if (isset($labels['4x'])) {
    if (is_bool($labels['4x'])) {
        $isChecked = $labels['4x'];
    } elseif (is_array($labels['4x']) && isset($labels['4x']['checked'])) {
        $isChecked = $labels['4x']['checked'];
    }
}
```

### Read Metadata:
```php
$checkInfo = null;
if (isset($labels['4x']) && is_array($labels['4x'])) {
    $checkInfo = $labels['4x'];
    // Access: $checkInfo['checker_name'], $checkInfo['chor_id'], etc.
}
```

## Updated normalizeLabels Function

```php
private function normalizeLabels($labels): array
{
    $default = collect($this->getLabelKeys())
        ->mapWithKeys(fn ($key) => [$key => false])
        ->toArray();

    if (!is_array($labels)) {
        $labels = [];
    }

    foreach ($default as $key => $value) {
        if (isset($labels[$key])) {
            // Handle both boolean and object formats
            if (is_bool($labels[$key])) {
                $default[$key] = $labels[$key];
            } elseif (is_array($labels[$key]) && isset($labels[$key]['checked'])) {
                $default[$key] = $labels[$key]; // Keep full object
            } else {
                $default[$key] = false;
            }
        } else {
            $default[$key] = false;
        }
    }

    return $default;
}
```

## Display in View

```php
@php
    $labelData = json_decode($market->labels ?? '{}', true);
    $checkInfo = $labelData['4x'] ?? false;
    $isChecked = false;
    $metadata = null;
    
    if (is_bool($checkInfo)) {
        $isChecked = $checkInfo;
    } elseif (is_array($checkInfo) && isset($checkInfo['checked'])) {
        $isChecked = $checkInfo['checked'];
        $metadata = $checkInfo;
    }
@endphp

<input type="checkbox" @checked($isChecked)>

@if($metadata)
    <div class="text-xs">
        Checker: {{ $metadata['checker_name'] ?? '—' }}<br>
        Chor ID: {{ $metadata['chor_id'] ?? '—' }}<br>
        Remark: {{ $metadata['remark'] ?? '—' }}
    </div>
@endif
```

