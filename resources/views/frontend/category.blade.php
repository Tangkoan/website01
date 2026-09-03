<x-layouts.app title="{{ $currentCategory->name }} - Life Reader With Us">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Category Title -->
            <div class="mb-8 border-b-2 border-gray-900 pb-2">
                <h1 class="text-2xl md:text-3xl font-serif font-bold text-gray-900">
                    {{ $currentCategory->name }}
                </h1>
            </div>

            <!-- Loop Stories -->
            @forelse ($stories as $story)
                <a href="{{ route('story.detail', $story->slug ?? $story->id) }}" class="block group">
                    <article class="flex flex-col md:flex-row gap-5 md:gap-6 bg-white p-4 md:p-5 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-blue-100">
                        
                        <div class="w-full md:w-1/3 aspect-video md:aspect-auto md:h-48 overflow-hidden rounded-lg shrink-0 bg-gray-50 relative">
                            <img src="{{ $story->thumbnail ? asset('storage/'.$story->thumbnail) : 'https://via.placeholder.com/400x225?text=Life+Reader' }}" 
                                 onerror="this.src='https://via.placeholder.com/400x225?text=No+Image'"
                                 alt="{{ $story->title }}" 
                                 class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        </div>
                        
                        <div class="w-full md:w-2/3 flex flex-col justify-center">
                            <h2 class="text-lg md:text-2xl font-bold mb-2 text-gray-900 group-hover:text-blue-600 transition-colors duration-300 line-clamp-2 leading-snug">
                                {{ $story->title }}
                            </h2>
                            
                            <p class="text-gray-400 text-xs mb-2 md:mb-3 font-medium uppercase tracking-wide">
                                Reading Time: {{ $story->reading_time ?? '5' }} Min <span class="mx-2 text-gray-300">|</span> {{ $story->created_at->format('M j, Y') }} in <span class="text-gray-700 font-semibold">{{ $story->category->name ?? 'Uncategorized' }}</span>
                            </p>
                            
                            <p class="text-gray-600 line-clamp-3 leading-relaxed text-sm md:text-base">
                                {{ Str::limit(strip_tags($story->content), 150) }}
                            </p>
                        </div>
                        
                    </article>
                </a>
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