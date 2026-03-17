<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">គ្រប់គ្រងគណនីប្រើប្រាស់</h1>
            <p class="mt-2 text-sm text-gray-600">គ្រប់គ្រងព័ត៌មានបុគ្គលិក កំណត់តួនាទី និងកម្រិតសិទ្ធិប្រើប្រាស់ក្នុងប្រព័ន្ធ។</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button wire:click="openModal" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                បន្ថែម User ថ្មី
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        <div class="relative rounded-lg shadow-sm w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live="searchTerm" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg p-2.5 border" placeholder="ស្វែងរកឈ្មោះ ឬអ៊ីមែល...">
        </div>
    </div>

    <div class="mb-4 h-10">
        @if(count($selectedUsers) > 0)
            <div class="flex items-center justify-between bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-2 animate-in fade-in duration-300">
                <div class="flex items-center text-indigo-700 text-sm font-medium">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                    បានជ្រើសរើស {{ count($selectedUsers) }} នាក់
                </div>
                <div class="flex gap-3">
                    <button onclick="confirm('តើអ្នកពិតជាចង់លុប User ទាំងនេះមែនទេ?') || event.stopImmediatePropagation()" wire:click="deleteSelected" class="text-xs font-bold text-red-600 hover:text-red-800 uppercase tracking-wider">លុបទាំងអស់</button>
                    <button wire:click="$set('selectedUsers', [])" class="text-xs font-bold text-gray-500 hover:text-gray-700 uppercase tracking-wider">បោះបង់</button>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">បុគ្គលិក</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">តួនាទី & កម្រិត</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ស្ថានភាព</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        @php
                            $targetMaxLevel = $user->roles->max('level') ?? 0;
                            $isProtected = ($targetMaxLevel >= $myMaxLevel && !auth()->user()->hasRole('Super Admin')) || $user->id === auth()->id();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}" {{ $isProtected ? 'disabled' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded {{ $isProtected ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->getRoleNames() as $role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                {{ $role }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-mono">Hierarchy Level: {{ $targetMaxLevel }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">សកម្ម</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!$isProtected)
                                    <button wire:click="editUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md transition-all mr-2">កែប្រែ</button>
                                    <button onclick="confirm('តើអ្នកពិតជាចង់លុបមែនទេ?') || event.stopImmediatePropagation()" wire:click="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-md transition-all">លុប</button>
                                @else
                                    <span class="inline-flex items-center text-gray-400 text-xs italic">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                        Protected
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">មិនមានទិន្នន័យ</h3>
                                <p class="mt-1 text-sm text-gray-500">សាកល្បងស្វែងរកឈ្មោះផ្សេង ឬបន្ថែមថ្មី។</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/75" wire:click="$set('isModalOpen', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ $userId ? 'កែប្រែទិន្នន័យ' : 'បង្កើតគណនីថ្មី' }}</h3>
                        <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
                    </div>

                    <form wire:submit.prevent="saveUser" class="p-6 space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">ឈ្មោះពេញ</label>
                                <input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">អ៊ីមែល</label>
                                <input type="email" wire:model="email" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">ពាក្យសម្ងាត់ {{ $userId ? '(ទុកទទេបើមិនប្តូរ)' : '' }}</label>
                                <input type="password" wire:model="password" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border">
                                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">តួនាទី (Roles)</label>
                                <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                                    @foreach($availableRoles as $role)
                                        <label class="flex items-center space-x-3 cursor-pointer p-1.5 hover:bg-white rounded-lg transition-all border border-transparent hover:border-gray-100">
                                            <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700 font-medium">{{ $role->name }} <span class="text-[10px] text-gray-400">L:{{ $role->level }}</span></span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedRoles') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">បោះបង់</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md transition-all">
                                <span wire:loading.remove>រក្សាទុកទិន្នន័យ</span>
                                <span wire:loading>កំពុងដំណើរការ...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>