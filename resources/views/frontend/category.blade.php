<x-layouts.app title="{{ $currentCategory->name }} - Life Stories With Us">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Breadcrumbs -->
            <div class="text-[13px] text-gray-500 mb-6 font-sans flex items-center flex-wrap gap-2">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a> 
                <span>&gt;</span>
                <span class="text-gray-800 line-clamp-1">{{ $currentCategory->name }}</span>
            </div>

            <!-- Category Title -->
            <div class="mb-8">
                <h1 class="text-2xl md:text-[38px] font-serif font-medium mb-4 text-gray-900 leading-tight">
                    {{ $currentCategory->name }}
                </h1>
            </div>

            <!-- Loop Stories -->
            @forelse ($stories as $index => $story)
                <a href="{{ route('story.detail', $story->slug ?? $story->id) }}" class="block group">
                    <article class="flex flex-col md:flex-row gap-5 md:gap-6 bg-white p-4 md:p-5 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-blue-100">
                        
                        <div class="w-full md:w-1/3 aspect-square md:aspect-auto md:h-48 overflow-hidden rounded-lg shrink-0 bg-gray-50 relative">
                            <img src="{{ $story->thumbnail ? asset('storage/'.$story->thumbnail) : 'https://via.placeholder.com/400x225?text=Life+Reader' }}" 
                                 onerror="this.src='https://via.placeholder.com/400x225?text=No+Image'"
                                 alt="{{ $story->title }}" 
                                 class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        </div>
                        
                        <div class="w-full md:w-2/3 flex flex-col justify-start">
                            <h2 class="text-lg md:text-2xl font-bold mb-2 text-gray-900 group-hover:text-blue-600 transition-colors duration-300 line-clamp-2 leading-snug">
                                {{ $story->title }}
                            </h2>
                            
                            <p class="text-gray-400 text-xs mb-2 md:mb-3 font-medium uppercase tracking-wide">
                                Reading Time: {{ $story->reading_time ?? '5' }} Minutes <span class="mx-2 text-gray-300">|</span> {{ $story->created_at->format('F j, Y') }} in <span class="text-gray-700 font-semibold">{{ $story->category->name ?? 'News & Healthy' }}</span>
                            </p>
                            
                            <p class="text-gray-600 line-clamp-3 leading-relaxed text-sm md:text-base">
                                {{ Str::limit(strip_tags($story->content), 150) }}... <span class="text-blue-600 font-semibold hover:underline">Read more</span>
                            </p>
                        </div>
                        
                    </article>
                </a>

                <!-- Insert Ad after every 3 stories (adjust as needed) -->
                @if (($index + 1) % 3 == 0)
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-5 uppercase tracking-widest border-l-4 border-gray-900 pl-3">Interesting For You</h3>
                        <x-ads.banner />
                    </div>
                @endif
                
            @empty
                <div class="p-8 bg-white text-gray-500 rounded-xl border border-gray-100 text-center shadow-sm">
                    No stories found in this category yet!
                </div>
            @endforelse

            <div class="mt-8">
                {{ $stories->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-6 mt-4 lg:mt-0">
            <!-- Sidebar Widgets (Search, Recent Posts, Tags) -->
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <form class="flex" action="#" method="GET">
                    <input type="text" name="search" placeholder="Search stories..." class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 focus:outline-none focus:border-gray-500 text-sm">
                    <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-r-lg hover:bg-gray-800 transition font-medium text-sm">Search</button>
                </form>
            </div>
            
            <!-- បន្ថែម ផ្ទាំង Ads Banner នៅទីនេះបើសិនជាមាន -->
            <div>
                <x-ads.banner />
            </div>
        </aside>

    </div>
</x-layouts.app>