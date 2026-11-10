<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-cd-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#b5010f] active:bg-[#900010] focus:outline-none focus:ring-2 focus:ring-cd-red focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
