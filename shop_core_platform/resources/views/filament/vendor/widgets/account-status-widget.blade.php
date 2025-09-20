<x-filament::widget>
    <x-filament::card>
        <div class="">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Account Status:</h2>
                <span @class([
                    'text-green-600 font-semibold' => $status === 'active',
                    'text-yellow-600 font-semibold' => $status === 'pending',
                    'text-red-600 font-semibold' => $status === 'suspended',
                    'text-gray-600 font-semibold' => !in_array($status, ['active', 'pending', 'suspended']),
                ])>
                    {{ ucfirst($status) }}
                </span>
            </div>

            <div class="text-sm text-gray-600">
                {{ $description }}
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
