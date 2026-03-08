@extends('layouts.app')
@section('title', 'Edit Employee — ' . $employee->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Employee</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->name }} · {{ $employee->employee_id }}</p>
        </div>
        <a href="{{ route('employees.show', $employee) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <div>
                <p class="text-sm font-medium text-red-700 dark:text-red-300">Please fix the following errors:</p>
                <ul class="mt-1 text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete form — standalone (cannot be nested inside the update form) -->
    <form id="emp-delete-form" action="{{ route('employees.destroy', $employee) }}" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" novalidate>
            @csrf @method('PUT')
            @include('employees.partials.form', [
                'submitLabel'   => 'Save Changes',
                'cancelUrl'     => route('employees.show', $employee),
                'deleteLabel'   => 'Delete Employee',
                'deleteConfirm' => 'Delete ' . addslashes($employee->name) . '? This cannot be undone.',
            ])
        </form>
    </div>
</div>
@endsection
