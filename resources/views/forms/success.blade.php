@extends('layouts.form')

@section('title', 'Formulário Enviado com Sucesso!')

@section('content')
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="px-8 py-12 text-center">
            {{-- Ícone de sucesso --}}
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Mensagem de sucesso --}}
            <h1 class="text-3xl font-bold text-gray-900 mb-3">
                Formulário Enviado com Sucesso!
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Obrigado por preencher o formulário <strong>{{ $form->title }}</strong>.
            </p>

            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8 max-w-md mx-auto">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-left">
                        <h3 class="text-green-800 font-semibold mb-1">Suas respostas foram recebidas</h3>
                        <p class="text-green-700 text-sm">
                            Recebemos suas informações e entraremos em contato em breve se necessário.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botão para voltar --}}
            <div class="space-x-4">
                <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Voltar para Início
                </a>
            </div>
        </div>
    </div>
@endsection
