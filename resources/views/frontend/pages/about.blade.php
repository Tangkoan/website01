<x-layouts.app title="About Us - {{ $siteName }}">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-100 mt-6 mb-12">
        <h1 class="text-3xl font-serif text-gray-900 mb-8">About Us</h1>

        <div class="space-y-6 text-[15px] text-gray-700 leading-relaxed font-sans">
            <p>Welcome to <strong>{{ $siteName }}</strong>!</p>
            <p>At <strong>{{ $siteName }}</strong>, we believe that every story has the power to inspire, educate, and connect people from different backgrounds and experiences. Our mission is to provide readers with engaging, informative, and meaningful content that enriches everyday life.</p>
            <p>We publish a variety of articles covering real-life stories, personal experiences, lifestyle topics, and more. Our goal is to create a platform where visitors can discover interesting perspectives and enjoy quality reading experiences.</p>
            <p>Thank you for visiting <strong>{{ $siteName }}</strong>. We are grateful for your support and look forward to being part of your reading journey.</p>
            
            <p class="pt-4 font-semibold">Sincerely,<br>The {{ $siteName }} Team</p>
        </div>
    </div>
</x-layouts.app>