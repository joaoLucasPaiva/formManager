<div>
    <div style="margin-bottom: 1rem;">
        <input
            type="text"
            value="{{ $url }}"
            readonly
            id="form-link-input"
            onclick="this.select()"
            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #f9fafb; color: #1f2937; font-size: 0.875rem; font-family: monospace; margin-bottom: 0.75rem;"
        >
        <button
            type="button"
            onclick="
                const input = document.getElementById('form-link-input');
                input.select();
                navigator.clipboard.writeText(input.value);
                this.textContent = '✓ Copiado!';
                this.style.backgroundColor = '#16a34a';
                setTimeout(() => {
                    this.textContent = '📋 Copiar Link';
                    this.style.backgroundColor = '#2563eb';
                }, 2000);
            "
            style="width: 100%; padding: 0.625rem 1rem; background-color: #2563eb; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer; transition: background-color 0.2s;"
            onmouseover="this.style.backgroundColor='#1d4ed8'"
            onmouseout="if(this.textContent !== '✓ Copiado!') this.style.backgroundColor='#2563eb'"
        >
            📋 Copiar Link
        </button>
    </div>

    <div style="display: flex; gap: 0.5rem; padding: 0.75rem; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.375rem;">
        <div style="font-size: 1rem; color: #2563eb; flex-shrink: 0;">ℹ️</div>
        <p style="font-size: 0.875rem; color: #1e40af; margin: 0;">
            Compartilhe este link com as pessoas que precisam preencher o formulário.
        </p>
    </div>
</div>
