<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-5 py-2.5 bg-farm-700 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-farm-800 focus:bg-farm-800 active:bg-farm-900 focus:outline-none focus:ring-2 focus:ring-farm-500 focus:ring-offset-2 transition ease-in-out duration-200 shadow-sm']) }}>
    {{ $slot }}
</button>
