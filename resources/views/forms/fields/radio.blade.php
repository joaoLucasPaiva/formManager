<div class="form-field">
    <label class="block text-sm font-medium text-gray-700 mb-3">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="space-y-3">
        @foreach($field->options as $option)
            <div class="flex items-center">
                <input
                    type="radio"
                    id="field_{{ $field->id }}_{{ $option->id }}"
                    name="fields[{{ $field->id }}]"
                    value="{{ $option->value }}"
                    @if(old('fields.' . $field->id) == $option->value) checked @endif
                    @if($field->required) required @endif
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                >
                <label for="field_{{ $field->id }}_{{ $option->id }}" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                    {{ $option->label }}
                </label>
            </div>
        @endforeach
    </div>

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
