<div style="max-width: 600px; margin: 50px auto; text-align: center; font-family: sans-serif;">
    
    <h1>សូមស្វាគមន៍មកកាន់ Dashboard!</h1>
    <h2 style="color: green;">សួស្តី, {{ auth()->user()->name ?? 'អ្នកប្រើប្រាស់' }} 👋</h2>

    <p style="margin-top: 20px;">នេះគឺជាទំព័រដែលអ្នកអាចមើលឃើញ ក្រោយពេល Login ឬ Register ជោគជ័យ។</p>

    {{-- <button wire:click="logout" style="margin-top: 30px; padding: 10px 20px; background: red; color: white; border: none; cursor: pointer; border-radius: 5px;">
        ចាកចេញពីគណនី (Logout)
    </button> --}}

    <button wire:click="logout" wire:loading.attr="disabled" style="margin-top: 30px; padding: 10px 20px; background: red; color: white; border: none; cursor: pointer; border-radius: 5px;">
        <span wire:loading.remove>ចាកចេញពីគណនី (Logout)</span>
        <span wire:loading>កំពុងចាកចេញ...</span>
    </button>

</div>