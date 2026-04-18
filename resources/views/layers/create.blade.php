<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Layer') }} - {{ $layup->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => 'Suppliers', 'url' => route('suppliers.index')],
                ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                ['label' => 'Layup: ' . $layup->name, 'url' => route('suppliers.layups.show', [$supplier, $layup])],
                ['label' => 'Add Layer']
            ]" />
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('suppliers.layups.layers.store', [$supplier, $layup]) }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="layer_order" :value="__('Layer Order')" />
                            <x-text-input id="layer_order" name="layer_order" type="number" min="1" class="mt-1 block w-full" :value="old('layer_order')" required autofocus />
                            <x-input-error :messages="$errors->get('layer_order')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="thickness" :value="__('Thickness')" />
                            <x-text-input id="thickness" name="thickness" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('thickness')" required />
                            <x-input-error :messages="$errors->get('thickness')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="width" :value="__('Width')" />
                            <x-text-input id="width" name="width" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('width')" required />
                            <x-input-error :messages="$errors->get('width')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="angle" :value="__('Angle')" />
                            <x-text-input id="angle" name="angle" type="number" step="0.01" class="mt-1 block w-full" :value="old('angle')" required />
                            <x-input-error :messages="$errors->get('angle')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create Layer') }}</x-primary-button>
                            <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-gray-600 dark:text-gray-400 hover:underline text-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
