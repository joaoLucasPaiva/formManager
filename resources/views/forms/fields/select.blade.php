<div class="form-field">
    <label for="field_{{ $field->id }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <select
        id="field_{{ $field->id }}"
        name="fields[{{ $field->id }}]"
        @if($field->required) required @endif
        class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white @error('fields.' . $field->id) border-red-500 @enderror"
    >
        <option value="">{{ $field->ui['placeholder'] ?? 'Selecione uma opção' }}</option>
        @foreach($field->options as $option)
            <option value="{{ $option->value }}" @if(old('fields.' . $field->id) == $option->value) selected @endif>
                {{ $option->label }}
            </option>
        @endforeach
    </select>

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
