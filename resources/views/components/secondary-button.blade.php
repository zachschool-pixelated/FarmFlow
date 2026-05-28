<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 tracking-wide shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-farm-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-200']) }}>
    {{ $slot }}
</button>
