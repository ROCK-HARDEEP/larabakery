<div>
    <form wire:submit.prevent="save" class="space-y-4">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
            <input type="text" wire:model="subtitle" id="subtitle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('subtitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea wire:model="content" id="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
            <input type="file" wire:model="image" id="image" class="mt-1 block w-full">
            @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="button_text" class="block text-sm font-medium text-gray-700">Button Text</label>
                <input type="text" wire:model="button_text" id="button_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('button_text') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="button_link" class="block text-sm font-medium text-gray-700">Button Link</label>
                <input type="url" wire:model="button_link" id="button_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('button_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="background_color" class="block text-sm font-medium text-gray-700">Background Color</label>
                <input type="color" wire:model="background_color" id="background_color" class="mt-1 block w-full h-10">
                @error('background_color') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="text_color" class="block text-sm font-medium text-gray-700">Text Color</label>
                <input type="color" wire:model="text_color" id="text_color" class="mt-1 block w-full h-10">
                @error('text_color') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="layout" class="block text-sm font-medium text-gray-700">Layout</label>
                <select wire:model="layout" id="layout" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="default">Default</option>
                    <option value="left-image">Left Image</option>
                    <option value="right-image">Right Image</option>
                    <option value="hero">Hero</option>
                </select>
                @error('layout') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="page" class="block text-sm font-medium text-gray-700">Page</label>
                <select wire:model="page" id="page" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="home">Home</option>
                    <option value="about">About</option>
                    <option value="contact">Contact</option>
                </select>
                @error('page') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                <input type="number" wire:model="position" id="position" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('position') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
        </div>

        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Section
            </button>
        </div>
    </form>
</div>