<x-layouts.app title="{{ $story->title }} - Life Reader With Us">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Content -->
        <div class="lg:col-span-8">
            <article class="bg-white p-5 md:p-8 rounded-xl shadow-sm border border-gray-100 mb-8">
                
                <h1 class="text-2xl md:text-[38px] font-serif font-medium mb-4 text-gray-900 leading-tight">
                    {{ $story->title }}
                </h1>
                
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 border-b border-gray-100 pb-4">
                    
                    <div class="flex items-center text-[13px] text-gray-500 font-sans">
                        <img src="https://via.placeholder.com/40" alt="Author" class="w-10 h-10 rounded-full mr-3 object-cover border border-gray-200">
                        <div>
                            <span class="block text-gray-800 font-semibold">Reading Time: {{ $story->reading_time ?? '5' }} Minutes</span>
                            <span class="block mt-0.5 text-gray-400">
                                {{ $story->created_at->format('F j, Y') }} in <span class="text-gray-800 font-semibold">{{ $story->category->name ?? 'News & Healthy' }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-1 mt-4 md:mt-0">
                        <a href="#" class="bg-[#55acee] text-white w-7 h-7 flex items-center justify-center rounded-[3px] hover:opacity-90 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.864 9.864 0 01-3.127 1.195 4.916 4.916 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.902 4.902 0 001.523 6.574 4.903 4.903 0 01-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.935 4.935 0 01-2.224.084 4.928 4.928 0 004.6 3.419A9.9 9.9 0 010 19.54a13.94 13.94 0 007.548 2.212c9.057 0 14.01-7.506 14.01-14.01 0-.213-.005-.425-.014-.636A10.012 10.012 0 0024 4.557z"/></svg>
                        </a>
                        <a href="#" class="bg-[#3b5998] text-white w-7 h-7 flex items-center justify-center rounded-[3px] hover:opacity-90 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                        </a>
                        <a href="#" class="bg-[#0077b5] text-white w-7 h-7 flex items-center justify-center rounded-[3px] hover:opacity-90 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="mb-8">
                    <x-ads.banner />
                </div>

                @if($story->thumbnail)
                    <div class="mb-8 rounded-lg overflow-hidden border border-gray-100">
                        <img src="{{ asset('storage/'.$story->thumbnail) }}" alt="{{ $story->title }}" class="w-full h-auto max-h-[550px] object-cover">
                    </div>
                @endif

                <div class="prose max-w-none text-gray-800 text-base md:text-[17px] leading-relaxed font-serif mb-10">
                    @php
                        $paragraphs = explode('</p>', $story->content);
                        $adFrequency = 4; 
                    @endphp

                    @foreach ($paragraphs as $index => $paragraph)
                        {!! $paragraph !!} 
                        @if(trim($paragraph) != '')
                            {!! '</p>' !!}
                        @endif

                        @if (($index + 1) % $adFrequency == 0 && $index < count($paragraphs) - 1)
                            <div class="my-8 font-sans p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="text-[10px] font-bold text-gray-400 mb-3 uppercase tracking-widest text-center">
                                    Promoted Content
                                </h4>
                                <x-ads.banner />
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <x-ads.banner />
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-6 mt-4 lg:mt-0">
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
                <form class="flex w-full" action="#" method="GET">
                    <input type="text" name="search" placeholder="Search..." class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 text-sm focus:outline-none focus:border-gray-500">
                    <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-r-lg hover:bg-gray-800 text-sm font-medium transition-colors">Search</button>
                </form>
            </div>

            <div>
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
            
            <!-- Tags -->
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

            <div class="sticky top-6 mt-6">
                <x-ads.banner />
            </div>
        </aside>

    </div>
</x-layouts.app>