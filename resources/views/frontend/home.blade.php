<x-layouts.app title="Home - Life Stories With Us">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
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

                @if ($loop->iteration == 2)
                    <div class="py-2">
                        <x-ads.banner />
                    </div>
                @endif

            @empty
                <div class="p-8 bg-white text-gray-500 rounded-xl border border-gray-100 text-center shadow-sm">
                    មិនទាន់មានអត្ថបទនៅឡើយទេ!
                </div>
            @endforelse

            <div class="mt-8">
                {{ $stories->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-6 mt-4 lg:mt-0">
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <form class="flex" action="#" method="GET">
                    <input type="text" name="search" placeholder="Search stories..." class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition text-sm">
                    <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-r-lg hover:bg-gray-800 transition font-medium text-sm">Search</button>
                </form>
            </div>

            <div>
                <x-ads.banner />
            </div>

            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-base font-bold border-l-4 border-gray-900 pl-3 mb-6 uppercase tracking-wider text-gray-800">Recent Posts</h3>
                <ul class="space-y-5">
                    @forelse ($recentPosts ?? [] as $recent)
                        <li>
                            <a href="{{ route('story.detail', $recent->slug ?? $recent->id) }}" class="flex gap-4 group items-center">
                                <div class="w-20 h-20 shrink-0 overflow-hidden rounded-lg bg-gray-50 relative">
                                    <img src="{{ $recent->thumbnail ? asset('storage/'.$recent->thumbnail) : 'https://via.placeholder.com/80x80?text=LR' }}" 
                                         onerror="this.src='https://via.placeholder.com/80x80?text=LR'"
                                         alt="Recent post"
                                         class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <h4 class="font-bold text-sm text-gray-800 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug">{{ $recent->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-1.5 font-medium uppercase tracking-wide">{{ $recent->created_at->format('M j, Y') }}</p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-400 text-sm">No recent posts.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Sidebar Tags -->
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-base font-bold border-l-4 border-gray-900 pl-3 mb-6 uppercase tracking-wider text-gray-800">Tags</h3>
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