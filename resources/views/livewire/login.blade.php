<div class="w-full max-w-md mx-auto mt-20 bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
    
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">Welcome Back!!!</h2>
        <p class="text-sm text-gray-500 mt-2">Please Enter Your Accout To Login</p>
    </div>

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm text-center font-medium shadow-sm">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-6">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input 
                type="text" 
                wire:model="email" 
                class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                placeholder="you@gmail.com"
                value="d"
            >
            @error('email') 
                <span class="text-red-500 text-sm mt-1 block font-medium">{{ $message }}</span> 
            @enderror
        </div>

        <div x-data="{ showPassword: false }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <div class="relative">
                <input 
                    /* បើ showPassword ពិត វាប្តូរទៅ text បើមិនពិត វាជា password */
                    x-bind:type="showPassword ? 'text' : 'password'" 
                    wire:model="password" 
                    class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                    placeholder="••••••••"
                    value="dd"
                >
                
                <button 
                    type="button"
                    x-on:click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                >
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            
            @error('password') 
                <span class="text-red-500 text-sm mt-1 block font-medium">{{ $message }}</span> 
            @enderror
        </div>

        <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg focus:ring-4 focus:ring-blue-200 flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove>Login</span>
            <span wire:loading>loading...</span>
        </button>

    </form>
</div>