# Scorecard Event Modal Form Controls Implementation Plan

## Overview
This document outlines the plan to add form controls to the Event Details modal on the Scorecard page. The modal will allow users to view and edit event information dynamically based on configuration.

## Current State
- Modal displays static event information (read-only)
- Shows: Event Name, External Event ID, Sport, Tournament, Event Time
- No form controls or edit capabilities
- Content is populated via JavaScript

## Goal
Add configurable form controls to the modal so that required fields can be set or displayed according to configuration settings.

## Implementation Strategy

### Phase 1: Configuration System

#### 1.1 Create Modal Fields Configuration
**File**: `config/scorecard_modal_fields.php`
```php
<?php

return [
    'fields' => [
        'event_name' => [
            'label' => 'Event Name',
            'type' => 'display', // display, text, textarea, select, date, etc.
            'required' => true,
            'editable' => false,
            'show' => true,
        ],
        'external_event_id' => [
            'label' => 'External Event ID',
            'type' => 'display',
            'required' => false,
            'editable' => false,
            'show' => true,
        ],
        'sport' => [
            'label' => 'Sport',
            'type' => 'display',
            'required' => false,
            'editable' => false,
            'show' => true,
        ],
        'tournament' => [
            'label' => 'Tournament',
            'type' => 'text',
            'required' => false,
            'editable' => true,
            'show' => true,
        ],
        'event_time' => [
            'label' => 'Event Time',
            'type' => 'datetime',
            'required' => false,
            'editable' => true,
            'show' => true,
        ],
        'notes' => [
            'label' => 'Notes',
            'type' => 'textarea',
            'required' => false,
            'editable' => true,
            'show' => false, // Hidden by default, can be enabled via config
        ],
        'status' => [
            'label' => 'Status',
            'type' => 'select',
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'required' => false,
            'editable' => true,
            'show' => false,
        ],
    ],
];
```

#### 1.2 Field Types Supported
- `display`: Read-only text display
- `text`: Single-line text input
- `textarea`: Multi-line text input
- `select`: Dropdown selection
- `datetime`: Date and time picker
- `date`: Date picker only
- `time`: Time picker only
- `checkbox`: Boolean checkbox
- `number`: Numeric input

### Phase 2: Backend Controller Updates

#### 2.1 Update ScorecardController
**File**: `app/Http/Controllers/ScorecardController.php`

**Add method to get modal configuration**:
```php
public function getModalFields()
{
    $config = config('scorecard_modal_fields.fields', []);
    $visibleFields = collect($config)->filter(fn($field) => $field['show'] ?? true)->toArray();
    return response()->json(['fields' => $visibleFields]);
}
```

**Add method to update event**:
```php
public function updateEvent(Request $request, $exEventId)
{
    $request->validate([
        // Dynamic validation based on config
    ]);
    
    // Update event in database
    // Return success response
}
```

#### 2.2 Route Updates
**File**: `routes/web.php`
```php
Route::prefix('scorecard')->name('scorecard.')->group(function () {
    Route::get('/', [ScorecardController::class, 'index'])->name('index');
    Route::get('/modal-fields', [ScorecardController::class, 'getModalFields'])->name('modal-fields');
    Route::post('/events/{exEventId}/update', [ScorecardController::class, 'updateEvent'])->name('events.update');
});
```

### Phase 3: Frontend Implementation

#### 3.1 JavaScript Form Builder
**File**: Update `resources/views/scorecard/index.blade.php`

**Function to build form from configuration**:
```javascript
async function buildModalForm(eventData) {
    // Fetch field configuration
    const response = await fetch('/scorecard/modal-fields');
    const config = await response.json();
    
    let formHTML = '<form id="eventModalForm" class="space-y-4">';
    
    config.fields.forEach(field => {
        formHTML += buildFormField(field, eventData);
    });
    
    formHTML += '</form>';
    
    return formHTML;
}

function buildFormField(field, eventData) {
    const fieldKey = field.key || field.label.toLowerCase().replace(/\s+/g, '_');
    const value = eventData[fieldKey] || eventData[mapFieldKey(fieldKey)] || '';
    
    if (field.type === 'display') {
        return `
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                    ${field.label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                </label>
                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">${value || 'N/A'}</p>
            </div>
        `;
    }
    
    if (field.type === 'text') {
        return `
            <div>
                <label for="${fieldKey}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    ${field.label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                </label>
                <input type="text" 
                       id="${fieldKey}" 
                       name="${fieldKey}" 
                       value="${value}" 
                       ${field.required ? 'required' : ''}
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
        `;
    }
    
    if (field.type === 'textarea') {
        return `
            <div>
                <label for="${fieldKey}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    ${field.label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                </label>
                <textarea id="${fieldKey}" 
                          name="${fieldKey}" 
                          rows="3"
                          ${field.required ? 'required' : ''}
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">${value}</textarea>
            </div>
        `;
    }
    
    if (field.type === 'select') {
        let optionsHTML = '<option value="">Select ' + field.label + '</option>';
        if (field.options) {
            Object.entries(field.options).forEach(([key, label]) => {
                optionsHTML += `<option value="${key}" ${value === key ? 'selected' : ''}>${label}</option>`;
            });
        }
        return `
            <div>
                <label for="${fieldKey}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    ${field.label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                </label>
                <select id="${fieldKey}" 
                        name="${fieldKey}" 
                        ${field.required ? 'required' : ''}
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    ${optionsHTML}
                </select>
            </div>
        `;
    }
    
    if (field.type === 'datetime' || field.type === 'date') {
        return `
            <div>
                <label for="${fieldKey}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    ${field.label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                </label>
                <input type="text" 
                       id="${fieldKey}" 
                       name="${fieldKey}" 
                       value="${value}" 
                       ${field.required ? 'required' : ''}
                       class="js-datetime-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Select ${field.label}">
            </div>
        `;
    }
    
    // Add more field types as needed...
    
    return '';
}
```

#### 3.2 Update Modal Opening Function
```javascript
async function openEventModal(eventData) {
    const modal = document.getElementById('eventModal');
    const overlay = document.getElementById('eventModalOverlay');
    const content = document.getElementById('eventModalContent');
    
    // Build form from configuration
    const formHTML = await buildModalForm(eventData);
    
    content.innerHTML = formHTML;
    
    // Initialize date/time pickers if needed
    initializeDatePickers();
    
    modal.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}
```

#### 3.3 Add Save Functionality
```javascript
async function saveEventModal() {
    const form = document.getElementById('eventModalForm');
    const formData = new FormData(form);
    const eventData = JSON.parse(document.querySelector('[data-event-data]').getAttribute('data-event-data'));
    const exEventId = eventData.exEventId;
    
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    try {
        const response = await fetch(`/scorecard/events/${exEventId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            showToast('Event updated successfully', 'success');
            // Close modal
            closeEventModal();
            // Reload page or update table
            window.location.reload();
        } else {
            showToast(result.message || 'Error updating event', 'error');
        }
    } catch (error) {
        showToast('Error updating event', 'error');
        console.error('Error:', error);
    }
}
```

#### 3.4 Update Modal Buttons
```javascript
// Update modal footer to include Save button
function buildModalButtons() {
    return `
        <div class="mt-6 flex justify-end gap-3">
            <button onclick="closeEventModal()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                Cancel
            </button>
            <button onclick="saveEventModal()" class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                Save Changes
            </button>
        </div>
    `;
}
```

### Phase 4: Database Updates

#### 4.1 Add Fields to Events Table (if needed)
**Migration**: `database/migrations/YYYY_MM_DD_add_scorecard_fields_to_events.php`
```php
Schema::table('events', function (Blueprint $table) {
    $table->text('notes')->nullable()->after('marketTime');
    $table->string('status', 50)->default('active')->after('notes');
    // Add other fields as needed based on config
});
```

### Phase 5: Validation

#### 5.1 Dynamic Validation in Controller
```php
private function getValidationRules($exEventId)
{
    $config = config('scorecard_modal_fields.fields', []);
    $rules = [];
    
    foreach ($config as $key => $field) {
        if ($field['show'] && $field['editable']) {
            $rule = [];
            
            if ($field['required']) {
                $rule[] = 'required';
            }
            
            if ($field['type'] === 'email') {
                $rule[] = 'email';
            } elseif ($field['type'] === 'number') {
                $rule[] = 'numeric';
            } elseif ($field['type'] === 'date' || $field['type'] === 'datetime') {
                $rule[] = 'date';
            }
            
            $rules[$key] = $rule;
        }
    }
    
    return $rules;
}
```

## Implementation Steps

### Step 1: Create Configuration File
1. Create `config/scorecard_modal_fields.php`
2. Define default fields with configuration
3. Set which fields are editable vs display-only

### Step 2: Update Backend
1. Add `getModalFields()` method to ScorecardController
2. Add `updateEvent()` method to ScorecardController
3. Add routes for modal fields and update

### Step 3: Update Frontend
1. Create `buildModalForm()` JavaScript function
2. Create `buildFormField()` helper function
3. Update `openEventModal()` to use form builder
4. Add `saveEventModal()` function
5. Add Save button to modal footer

### Step 4: Add Form Controls
1. Implement all field types (text, textarea, select, datetime, etc.)
2. Add validation on frontend
3. Add error handling
4. Add loading states

### Step 5: Testing
1. Test with different field configurations
2. Test save functionality
3. Test validation
4. Test with different field types

## Configuration Examples

### Example 1: Minimal Fields
```php
'fields' => [
    'event_name' => ['type' => 'display', 'show' => true],
    'event_time' => ['type' => 'datetime', 'editable' => true, 'show' => true],
]
```

### Example 2: Full Form
```php
'fields' => [
    'event_name' => ['type' => 'display', 'show' => true],
    'tournament' => ['type' => 'text', 'editable' => true, 'required' => true, 'show' => true],
    'event_time' => ['type' => 'datetime', 'editable' => true, 'show' => true],
    'notes' => ['type' => 'textarea', 'editable' => true, 'show' => true],
    'status' => ['type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'editable' => true, 'show' => true],
]
```

## Benefits

1. **Configurable**: Fields can be shown/hidden via config file
2. **Flexible**: Easy to add new field types
3. **Maintainable**: All field definitions in one place
4. **Scalable**: Can add more fields without code changes
5. **User-friendly**: Dynamic form generation based on requirements

## Future Enhancements

1. Admin panel to manage field configuration
2. Field permissions per user role
3. Conditional field display based on other field values
4. Field grouping/tabs in modal
5. Field validation rules in config
6. Custom field types support

