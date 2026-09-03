<x-layouts.app title="Contact Us - {{ $siteName }}">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-100 mt-6 mb-12">
        <h1 class="text-3xl font-serif text-gray-900 mb-8">Contact Us</h1>

        <div class="space-y-6 text-[15px] text-gray-700 leading-relaxed font-sans">
            <p>Thank you for visiting <strong>{{ $siteName }}</strong>.</p>
            <p>We value our readers and welcome your questions, comments, suggestions, and feedback. If you need assistance or would like to get in touch with us, please use the information below.</p>
            
            <h2 class="text-xl font-bold text-gray-900 pt-4">Get in Touch</h2>
            <ul class="space-y-2">
                <li><strong>Website:</strong> {{ request()->getHost() }}</li>
                <li><strong>Email:</strong> contact@{{ str_replace('www.', '', request()->getHost()) }}</li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 pt-4">Important Notice</h2>
            <p>Please do not send sensitive personal information through email. For your security, only provide information necessary for us to assist with your inquiry.</p>
            <p class="pt-4 font-semibold">We look forward to hearing from you.<br>{{ $siteName }} Team</p>
        </div>
    </div>
</x-layouts.app>