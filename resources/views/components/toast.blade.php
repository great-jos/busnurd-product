<div 
    x-data 
    x-show="$store.toast.visible" 
    x-transition
    x-cloak
    class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg p-4 border"
    :class="$store.toast.type === 'error' ? 'border-red-500 text-red-600' : 'border-green-500 text-green-600'"
>
    <div class="flex items-start justify-between">
        <div>
            <strong x-text="$store.toast.title"></strong>
            <p class="text-sm mt-1" x-text="$store.toast.message"></p>
        </div>
        <button @click="$store.toast.close()" class="ml-4 text-gray-400 hover:text-gray-600">&times;</button>
    </div>
</div>
