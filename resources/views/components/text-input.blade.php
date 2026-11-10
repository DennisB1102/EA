@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-cd-red focus:ring-cd-red rounded-md shadow-sm']) }}>
