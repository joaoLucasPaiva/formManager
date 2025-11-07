<div class="form-field">
    <label for="field_{{ $field->id }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <input
            type="datetime-local"
            id="field_{{ $field->id }}"
            name="fields[{{ $field->id }}]"
            value="{{ old('fields.' . $field->id) }}"
            @if($field->required) required @endif
            @if(isset($field->validation['min'])) min="{{ $field->validation['min'] }}" @endif
            @if(isset($field->validation['max'])) max="{{ $field->validation['max'] }}" @endif
            class="block w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('fields.' . $field->id) border-red-500 @enderror"
        >
    </div>

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
