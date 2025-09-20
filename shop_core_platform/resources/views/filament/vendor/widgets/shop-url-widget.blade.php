<x-filament::widget>
    <x-filament::card>
        <div class="">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Shop Website:</h2>
                <span>
                    {{ $copyButton }}
                </span>
            </div>

            <div class="text-sm text-gray-600">
                <a href="{{ $url }}" target="_blank">{{ $url }}</a>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
