@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'mt-2 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 shadow-sm']) }}>
