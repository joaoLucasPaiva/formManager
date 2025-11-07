<div class="form-field">
    <label for="field_{{ $field->id }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $field->label }}
        @if($field->required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="mt-2">
        <label for="field_{{ $field->id }}" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-indigo-400 transition-colors @error('fields.' . $field->id) border-red-500 @enderror">
            <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                </svg>
                <div class="flex text-sm text-gray-600">
                    <span class="relative font-medium text-indigo-600 hover:text-indigo-500">
                        Clique para enviar arquivo
                    </span>
                    <span class="pl-1">ou arraste e solte</span>
                </div>
                @if(isset($field->validation['mimes']))
                    <p class="text-xs text-gray-500">
                        Formatos aceitos: {{ str_replace(',', ', ', $field->validation['mimes']) }}
                    </p>
                @endif
                @if(isset($field->validation['max']))
                    <p class="text-xs text-gray-500">
                        Tamanho máximo: {{ $field->validation['max'] }}KB
                    </p>
                @endif
            </div>
            <input
                type="file"
                id="field_{{ $field->id }}"
                name="fields[{{ $field->id }}]"
                @if($field->required) required @endif
                @if(isset($field->validation['mimes'])) accept=".{{ str_replace(',', ',.', $field->validation['mimes']) }}" @endif
                class="sr-only"
                onchange="updateFileName(this)"
            >
        </label>
        <p id="file_name_{{ $field->id }}" class="mt-2 text-sm text-gray-600 hidden"></p>
    </div>

    @error('fields.' . $field->id)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
@push('scripts')
<script>
function updateFileName(input) {
    const fileNameElement = document.getElementById('file_name_' + input.id.replace('field_', ''));
    if (input.files && input.files[0]) {
        fileNameElement.textContent = 'Arquivo selecionado: ' + input.files[0].name;
        fileNameElement.classList.remove('hidden');
    } else {
        fileNameElement.classList.add('hidden');
    }
}
</script>
@endpush
@endonce
