<x-layouts.app title="{{ $story->title }} - Life Stories With Us">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Content -->
        <div class="lg:col-span-8">
            
            <!-- Breadcrumbs -->
            <div class="text-[13px] text-gray-500 mb-6 font-sans flex items-center flex-wrap gap-2">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a> 
                <span>&gt;</span>
                <a href="{{ url('/category/' . ($story->category->slug ?? '')) }}" class="hover:text-blue-600 transition-colors">{{ $story->category->name ?? 'Uncategorized' }}</a> 
                <span>&gt;</span>
                <span class="text-gray-800 line-clamp-1">{{ $story->title }}</span>
            </div>

            <article class="bg-white p-5 md:p-8 rounded-xl shadow-sm border border-gray-100 mb-8">
                
                <h1 class="text-2xl md:text-[38px] font-serif font-medium mb-4 text-gray-900 leading-tight">
                    {{ $story->title }}
                </h1>
                
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 border-b border-gray-100 pb-4">
                    
                    <div class="flex items-center text-[13px] text-gray-500 font-sans">
                        <!-- ទាញយក Logo ពី Table shop_infos -->
                        @if(optional($shopInfo)->logo)
                            <img src="{{ asset('storage/' . $shopInfo->logo) }}?v={{ time() }}" alt="{{ optional($shopInfo)->site_name ?? 'Logo' }}" class="w-10 h-10 rounded-full mr-3 object-cover border border-gray-200 shadow-sm">
                        @else
                            <!-- បើមិនទាន់ Upload Logo ទេ វានឹងយកឈ្មោះ Site Name មកធ្វើជារូបភាពបណ្តោះអាសន្ន -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($shopInfo)->site_name ?? 'Life Stories') }}&background=2563eb&color=fff" alt="Default Avatar" class="w-10 h-10 rounded-full mr-3 object-cover border border-gray-200">
                        @endif
                        
                        <div>
                            <span class="block text-gray-800 font-semibold">Reading Time: {{ $story->reading_time ?? '5' }} Minutes</span>
                            <span class="block mt-0.5 text-gray-400">
                                {{ $story->created_at->format('F j, Y') }} in <span class="text-gray-800 font-semibold">{{ $story->category->name ?? 'News & Healthy' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Social Share Buttons -->
                    <div class="flex gap-1.5 mt-4 md:mt-0">
                        <a href="#" class="bg-[#3b5998] text-white w-7 h-7 flex items-center justify-center rounded-[3px] hover:opacity-90 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                        </a>
                        <a href="#" class="bg-[#55acee] text-white w-7 h-7 flex items-center justify-center rounded-[3px] hover:opacity-90 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.864 9.864 0 01-3.127 1.195 4.916 4.916 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.902 4.902 0 001.523 6.574 4.903 4.903 0 01-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.935 4.935 0 01-2.224.084 4.928 4.928 0 004.6 3.419A9.9 9.9 0 010 19.54a13.94 13.94 0 007.548 2.212c9.057 0 14.01-7.506 14.01-14.01 0-.213-.005-.425-.014-.636A10.012 10.012 0 0024 4.557z"/></svg>
                        </a>
                        <a href="#" class="bg-[#cb2027] text-white w-7 h-7 flex items-center justify-center rounded-[3px] hover:opacity-90 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 4.135 2.152 7.766 5.412 9.972.107-.962.203-2.433.453-3.487.218-.916 1.405-5.952 1.405-5.952s-.36-.72-.36-1.782c0-1.668.967-2.915 2.17-2.915 1.023 0 1.518.768 1.518 1.688 0 1.03-.655 2.568-.992 3.992-.28 1.197.6 2.172 1.777 2.172 2.13 0 3.768-2.247 3.768-5.485 0-2.86-2.057-4.86-4.992-4.86-3.398 0-5.39 2.548-5.39 5.184 0 1.03.397 2.137.892 2.736.098.118.112.224.083.345l-.333 1.36c-.052.218-.172.263-.394.16-1.472-.68-2.39-2.825-2.39-4.545 0-3.697 2.686-7.094 7.747-7.094 4.062 0 7.218 2.894 7.218 6.777 0 4.037-2.543 7.284-6.074 7.284-1.185 0-2.3-.615-2.68-1.345l-.733 2.79c-.266 1.018-.98 2.29-1.464 3.067C10.224 23.86 11.096 24 12 24c6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
                        </a>
                    </div>
                </div>

                @if($story->thumbnail)
                    <div class="mb-8 rounded-lg overflow-hidden border border-gray-100">
                        <img src="{{ asset('storage/'.$story->thumbnail) }}" alt="{{ $story->title }}" class="w-full h-auto max-h-[550px] object-cover">
                    </div>
                @endif

                <!-- Alpine.js សម្រាប់មុខងារ Continue Reading -->
                <div x-data="{ expanded: false }" class="relative">
                    
                    <!-- Content Container (លាក់បន្តិចប្រសិនបើមិនទាន់ចុច) -->
                    <div class="prose max-w-none text-gray-800 text-base md:text-[17px] leading-relaxed font-serif relative"
                         :class="expanded ? 'pb-10' : 'max-h-[800px] overflow-hidden pb-0'">
                        
                        @php
                            $paragraphs = explode('</p>', $story->content);
                            $adFrequency = 4; 
                        @endphp

                        @foreach ($paragraphs as $index => $paragraph)
                            {!! $paragraph !!} 
                            @if(trim($paragraph) != '')
                                {!! '</p>' !!}
                            @endif

                            <!-- In-Article Ads -->
                            @if (($index + 1) % $adFrequency == 0 && $index < count($paragraphs) - 1)
                                <div class="my-8 font-sans">
                                    <h4 class="text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">
                                        Promoted Content
                                    </h4>
                                    <x-ads.banner />
                                </div>
                            @endif
                        @endforeach

                        <!-- ស្រមោលសៗខាងក្រោមពេលមិនទាន់បើកអាន (Gradient Fade) -->
                        <div x-show="!expanded" class="absolute bottom-0 left-0 w-full h-40 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                    </div>

                    <!-- ប៊ូតុង Continue Reading -->
                    <div x-show="!expanded" class="flex justify-center mt-4 mb-10 relative z-10">
                        <button @click="expanded = true" class="bg-[#5a6268] text-white font-sans text-[13px] font-bold uppercase tracking-wider py-3 px-8 rounded hover:bg-gray-800 transition-colors shadow-md flex items-center gap-2">
                            Continue Reading
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                </div>

                <!-- Story Tags នៅខាងក្រោមអត្ថបទ -->
                <div class="mt-4 pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($story->tags as $tag)
                            <a href="#" class="border border-gray-200 text-gray-600 text-[11px] uppercase tracking-wide px-3 py-1.5 rounded hover:bg-gray-50 transition-colors">
                                {{ $tag->name }}
                            </a>
                        @empty
                        @endforelse
                    </div>
                </div>

                <!-- Bottom Ad / Interesting For You -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-5 uppercase tracking-widest border-l-4 border-gray-900 pl-3">Interesting For You</h3>
                    <x-ads.banner />
                </div>
            </article>
        </div>

        <!-- Sidebar (នៅរក្សាទុកដដែល) -->
        <aside class="lg:col-span-4 space-y-6 mt-4 lg:mt-0">
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <form class="flex w-full" action="#" method="GET">
                    <input type="text" name="search" placeholder="Search..." class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 text-sm focus:outline-none focus:border-gray-500">
                    <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-r-lg hover:bg-gray-800 text-sm font-medium transition-colors">Search</button>
                </form>
            </div>

            <div class="sticky top-6 mt-6">
                <x-ads.banner />
            </div>

            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-5 uppercase tracking-widest border-l-4 border-gray-900 pl-3">Recent Posts</h3>
                <ul class="space-y-5">
                    @forelse ($recentPosts as $recent)
                        <li class="flex gap-4 items-center group">
                            <a href="{{ route('story.detail', $recent->slug ?? $recent->id) }}" class="w-20 h-20 shrink-0 block bg-gray-50 relative rounded-lg overflow-hidden">
                                <img src="{{ $recent->thumbnail ? asset('storage/'.$recent->thumbnail) : 'https://via.placeholder.com/80x80?text=LR' }}" 
                                     onerror="this.src='https://via.placeholder.com/80x80?text=LR'"
                                     class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            </a>
                            <div class="flex-1">
                                <a href="{{ route('story.detail', $recent->slug ?? $recent->id) }}" class="font-bold text-sm text-gray-800 group-hover:text-blue-600 leading-snug line-clamp-2 mb-1.5 transition-colors">
                                    {{ $recent->title }}
                                </a>
                                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide">{{ $recent->created_at->format('M j, Y') }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm">No recent posts.</li>
                    @endforelse
                </ul>
            </div>
            
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-5 uppercase tracking-widest border-l-4 border-gray-900 pl-3">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse ($sidebarTags ?? [] as $tag)
                        <a href="#" class="border border-gray-200 text-gray-600 text-[11px] uppercase tracking-wide px-3 py-1.5 rounded hover:bg-gray-50 transition-colors">
                            {{ $tag->name }}
                        </a>
                    @empty
                        <span class="text-xs text-gray-400">No tags.</span>
                    @endforelse
                </div>
            </div>
        </aside>

    </div>
</x-layouts.app>