<div class="p-3 border-b border-gray-700 dark:border-gray-600">
    <input 
        type="text" 
        placeholder="Search emoji..." 
        class="w-full px-3 py-2 text-sm bg-gray-700 dark:bg-gray-800 text-white rounded-lg border border-gray-600 dark:border-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none emoji-search-input"
    >
</div>
<div class="flex-1 overflow-y-auto p-3 emoji-grid" style="min-height: 0; max-height: 220px;">
    <div class="emoji-category" data-category="smileys">
        <div class="text-xs text-gray-400 dark:text-gray-500 mb-2 font-semibold">Smileys &amp; People</div>
        <div class="grid grid-cols-8 gap-1">
            {{-- Core smileys set - can be extended as needed --}}
            @php
                $emojis = [
                    '😀','😃','😄','😁','😆','😅','😂','🤣',
                    '☺️','😊','😇','🙂','🙃','😉','😌','😍',
                    '🥰','😘','😗','😙','😚','😋','😛','😝',
                    '😜','🤪','🤨','🧐','🤓','😎','🤩','🥳',
                    '😏','😒','😞','😔','😟','😕','🙁','☹️',
                    '😣','😖','😫','😩','🥺','😢','😭','😤',
                    '😠','😡','🤬','🤯','😳','🥵','🥶','😱',
                    '😨','😰','😥','😓','🤗','🤔','🤭','🤫',
                    '🤥','😶','😐','😑','😬','🙄','😯','😦',
                    '😧','😮','😲','🥱','😴','🤤','😪','😵',
                    // Extra commonly used icons
                    '👋','🐶','🍕','⚽','🚗','💡','❤️',
                ];
            @endphp
            @foreach($emojis as $emoji)
                <span 
                    class="emoji-item text-2xl cursor-pointer hover:bg-gray-700 dark:hover:bg-gray-800 rounded p-1 text-center" 
                    data-emoji="{{ $emoji }}"
                >
                    {{ $emoji }}
                </span>
            @endforeach
        </div>
    </div>
</div>
<div class="p-2 border-t border-gray-700 dark:border-gray-600 flex items-center justify-around emoji-categories">
    <button class="emoji-category-btn active px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="smileys" title="Smileys &amp; People">😀</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="gestures" title="Gestures">👋</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="animals" title="Animals &amp; Nature">🐶</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="food" title="Food &amp; Drink">🍕</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="activities" title="Activities">⚽</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="travel" title="Travel &amp; Places">🚗</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="objects" title="Objects">💡</button>
    <button class="emoji-category-btn px-2 py-1 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition" data-category="symbols" title="Symbols">❤️</button>
</div>


