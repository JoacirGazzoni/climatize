<x-app-layout>
    <x-slot name="header">
        <h5 class="text-left font-semibold text-sm text-white leading-tight">
            <i class="fas fa-users"></i> {{ __('Novo Usuário') }}
        </h5>
    </x-slot>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 rounded p-4 mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('error'))
        <span class="bg-red-100 border border-red-400 text-red-700 rounded p-4 mb-4">{{ session()->get('error') }}</span>
    @endif

    <section class="container mx-auto p-4">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            @if(session()->has('error'))
                <span class="bg-red-100 border border-red-400 text-red-700 rounded">{{ session()->get('error') }}</span>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <x-label class="text-white" for="name" :value="__('Nome:')" />
                    <x-input id="name" class="mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>

                <div>
                    <x-label class="text-white" for="email" :value="__('E-mail:')" />
                    <x-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required />
                </div>

                <div>
                    <x-label class="text-white" for="password" :value="__('Senha:')" />
                    <x-input id="password" class="mt-1 w-full" type="password" name="password" required />
                </div>

                <div>
                    <x-label class="text-white" for="cad_cliente_id" :value="__('Vincular Cliente:')" />
                    <select id="cad_cliente_id" name="cad_cliente_id" class="mt-1 w-full form-select">
                        <option value="" selected>Selecione um cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">
                                {{ $client->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-4 bg-gray-500 rounded-lg flex items-center justify-center mt-4">
                <x-primary-button class="ml-4">
                    <i class="fas fa-save"></i>&nbsp;{{ __('Criar') }}
                </x-primary-button>
                <x-primary-button class="ml-4" href="{{ route('users') }}">
                    <i class="fas fa-undo"></i>&nbsp;{{ __('Voltar') }}
                </x-primary-button>
            </div>
        </form>
    </section>
</x-app-layout>
