@props([
    'striped' => false,
    'hover' => true,
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200']) }}>
        @isset($head)
            <thead class="bg-gray-50">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endisset

        <tbody class="bg-white divide-y divide-gray-200 {{ $striped ? '[&>tr:nth-child(even)]:bg-gray-50' : '' }} {{ $hover ? '[&>tr]:hover:bg-gray-50' : '' }}">
            {{ $slot }}
        </tbody>

        @isset($foot)
            <tfoot class="bg-gray-50">
                {{ $foot }}
            </tfoot>
        @endisset
    </table>
</div>
