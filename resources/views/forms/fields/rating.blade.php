<div class="form-field">
    <label class="block text-sm font-medium text-gray-700 mb-3">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="flex items-center space-x-2">
        @php
            $maxRating = $field->validation['max'] ?? 5;
        @endphp
        @for($i = 1; $i <= $maxRating; $i++)
            <label class="cursor-pointer">
                <input
                    type="radio"
                    name="fields[{{ $field->id }}]"
                    value="{{ $i }}"
                    @if(old('fields.' . $field->id) == $i) checked @endif
                    @if($field->required) required @endif
                    class="sr-only peer"
                >
                <svg class="w-10 h-10 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </label>
        @endfor
    </div>

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
