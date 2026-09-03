<footer class="bg-[#222222] text-[#cccccc] py-8 mt-12 font-sans border-t-[5px] border-[#5a6268]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-[13px] text-center md:text-left">
        
        <div class="mb-4 md:mb-0">
            <p>&copy; {{ date('Y') }} Life Reader With Us. All rights reserved.</p>
        </div>
        
        <ul class="flex flex-wrap justify-center gap-x-4 gap-y-2">
            <li><a href="{{ route('page.privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
            <li><a href="{{ route('page.terms') }}" class="hover:text-white transition-colors">Terms and Conditions</a></li>
            <li><a href="{{ route('page.about') }}" class="hover:text-white transition-colors">About Us</a></li>
            <li><a href="{{ route('page.contact') }}" class="hover:text-white transition-colors">Contact Us</a></li>
        </ul>
        
    </div>
</footer>