<x-layouts.app title="Privacy Policy - {{ $siteName }}">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-100 mt-6 mb-12">
        <h1 class="text-3xl font-serif text-gray-900 mb-4">Privacy Policy</h1>
        <p class="text-sm text-gray-500 mb-8 font-sans font-medium">Last Updated: June 2026</p>

        <div class="space-y-6 text-[15px] text-gray-700 leading-relaxed font-sans">
            <p>Welcome to <strong>{{ $siteName }}</strong> ("we", "our", or "us"). Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect information when you visit our website, {{ request()->getHost() }}.</p>
            
            <h2 class="text-xl font-bold text-gray-900 pt-4">Information We Collect</h2>
            <h3 class="text-lg font-semibold text-gray-800">Personal Information</h3>
            <p>We may collect personal information that you voluntarily provide, such as:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Name</li>
                <li>Email address</li>
                <li>Information submitted through contact forms</li>
            </ul>

            <h3 class="text-lg font-semibold text-gray-800 pt-2">Non-Personal Information</h3>
            <p>When you visit our website, certain information may be collected automatically, including your IP address, browser type, and device information.</p>

            <h2 class="text-xl font-bold text-gray-900 pt-4">Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us at:</p>
            <p><strong>Email:</strong> contact@{{ str_replace('www.', '', request()->getHost()) }}</p>
        </div>
    </div>
</x-layouts.app>