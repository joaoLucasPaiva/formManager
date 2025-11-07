<div class="form-field">
    <label class="block text-sm font-medium text-gray-700 mb-3">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="space-y-3">
        @foreach($field->options as $option)
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input
                        type="checkbox"
                        id="field_{{ $field->id }}_{{ $option->id }}"
                        name="fields[{{ $field->id }}][]"
                        value="{{ $option->value }}"
                        @if(is_array(old('fields.' . $field->id)) && in_array($option->value, old('fields.' . $field->id))) checked @endif
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="field_{{ $field->id }}_{{ $option->id }}" class="font-medium text-gray-700 cursor-pointer">
                        {{ $option->label }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
