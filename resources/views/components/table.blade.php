@props(['headers', 'requireRole' => null])

@php
    $hasPermission = true;
    if ($requireRole) {
        $hasPermission = auth()->check() && auth()->user()->role === $requireRole;
    }
@endphp


    <table {{ $attributes->merge(['class' => 'min-w-full text-sm rounded-t-2xl border-b-4 border-dashed text-gray-700 dark:text-gray-200']) }}>
        <thead class="bg-gradient-to-r from-indigo-500 to-violet-500 text-white">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold tracking-wider uppercase">
                        {{ $header }}
                    </th>
                @endforeach

                @if ($hasPermission)
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold tracking-wider uppercase">
                        Actions
                    </th>
                @endif
            </tr>
        </thead>

        {{ $slot }}

    </table>

