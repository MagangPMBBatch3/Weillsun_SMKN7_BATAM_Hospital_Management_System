@props(['headers', 'requireRole' => null])

@php
    $hasPermission = true;
    if ($requireRole) {
        $hasPermission = auth()->check() && auth()->user()->role === $requireRole;
    }
@endphp

<div class="overflow-x-auto overflow-y-hidden bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700']) }}>
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
                
                @if($hasPermission)
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                @endif
            </tr>
        </thead>
        <tbody class="">
            {{ $slot }}
        </tbody>
    </table>
</div>