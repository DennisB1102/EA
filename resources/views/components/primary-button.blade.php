<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-cd-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#1e1e30] focus:bg-[#1e1e30] active:bg-[#1a1a28] focus:outline-none focus:ring-2 focus:ring-cd-red focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
