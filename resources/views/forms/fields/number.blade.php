<div class="form-field">
    <label for="field_{{ $field->id }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input
        type="number"
        id="field_{{ $field->id }}"
        name="fields[{{ $field->id }}]"
        value="{{ old('fields.' . $field->id) }}"
        placeholder="{{ $field->ui['placeholder'] ?? '' }}"
        @if($field->required) required @endif
        @if(isset($field->validation['min'])) min="{{ $field->validation['min'] }}" @endif
        @if(isset($field->validation['max'])) max="{{ $field->validation['max'] }}" @endif
        @if(isset($field->ui['step'])) step="{{ $field->ui['step'] }}" @endif
        class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('fields.' . $field->id) border-red-500 @enderror"
    >

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
