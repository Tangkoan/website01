<div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
    <h2>បង្កើតគណនីថ្មី (Register)</h2>

    <form wire:submit="register">
        
        <div style="margin-bottom: 15px;">
            <label>ឈ្មោះ៖</label>
            <input type="text" wire:model="name" style="width: 100%; padding: 8px;">
            @error('name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>អ៊ីមែល៖</label>
            <input type="email" wire:model="email" style="width: 100%; padding: 8px;">
            @error('email') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>លេខសម្ងាត់៖</label>
            <input type="password" wire:model="password" style="width: 100%; padding: 8px;">
            @error('password') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>បញ្ជាក់លេខសម្ងាត់ម្តងទៀត៖</label>
            <input type="password" wire:model="password_confirmation" style="width: 100%; padding: 8px;">
        </div>

        <button type="submit" style="padding: 10px 20px; background: green; color: white; border: none; cursor: pointer; width: 100%;">
            ចុះឈ្មោះ
        </button>

    </form>
</div>