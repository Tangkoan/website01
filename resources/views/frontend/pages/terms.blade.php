<x-layouts.app title="Terms and Conditions - {{ $siteName }}">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-100 mt-6 mb-12">
        <h1 class="text-3xl font-serif text-gray-900 mb-4">Terms and Conditions</h1>
        <p class="text-sm text-gray-500 mb-8 font-sans font-medium">Last Updated: June 2026</p>

        <div class="space-y-6 text-[15px] text-gray-700 leading-relaxed font-sans">
            <p>Welcome to <strong>{{ $siteName }}</strong>. By accessing and using our website, {{ request()->getHost() }}, you agree to comply with and be bound by the following Terms and Conditions.</p>
            
            <h2 class="text-xl font-bold text-gray-900 pt-4">Acceptance of Terms</h2>
            <p>By using this website, you acknowledge that you have read, understood, and agreed to these Terms and Conditions, as well as our Privacy Policy.</p>

            <h2 class="text-xl font-bold text-gray-900 pt-4">Website Content</h2>
            <p>The content published on <strong>{{ $siteName }}</strong> is provided for general informational and entertainment purposes only.</p>
        </div>
    </div>
</x-layouts.app>