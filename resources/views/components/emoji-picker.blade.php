<div class="p-2 md:p-3 border-b border-gray-300 flex items-center gap-2 w-full" style="flex-shrink: 0; flex-grow: 0;">
    <input 
        type="text" 
        placeholder="Search emoji..." 
        class="flex-1 px-3 py-2 text-sm md:text-base bg-gray-50 text-gray-800 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none emoji-search-input"
    >
    <button type="button" id="closeEmojiPicker" class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors" aria-label="Close emoji picker">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<div class="flex-1 overflow-y-auto overflow-x-hidden p-2 md:p-4 emoji-grid w-full" style="min-height: 0; flex: 1 1 0; overflow-y: auto;">
    <!-- Smileys & People -->
    <div class="emoji-category active" data-category="smileys">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Smileys &amp; People</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $smileys = [
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
                ];
            @endphp
            @foreach($smileys as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Gestures -->
    <div class="emoji-category hidden" data-category="gestures">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Gestures &amp; Body</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $gestures = [
                    '👋','🤚','🖐️','✋','🖖','👌','🤌','🤏',
                    '✌️','🤞','🤟','🤘','🤙','👈','👉','👆',
                    '🖕','👇','☝️','👍','👎','✊','👊','🤛',
                    '🤜','👏','🙌','👐','🤲','🤝','🙏','✍️',
                    '💪','🦾','🦿','🦵','🦶','👂','🦻','👃',
                    '🧠','🫀','🫁','🦷','🦴','👀','👁️','👅',
                    '👄','💋','🩸',
                ];
            @endphp
            @foreach($gestures as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Animals & Nature -->
    <div class="emoji-category hidden" data-category="animals">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Animals &amp; Nature</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $animals = [
                    '🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼',
                    '🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵',
                    '🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤',
                    '🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗',
                    '🐴','🦄','🐝','🐛','🦋','🐌','🐞','🐜',
                    '🦟','🦗','🕷️','🦂','🐢','🐍','🦎','🦖',
                    '🦕','🐙','🦑','🦐','🦞','🦀','🐡','🐠',
                    '🐟','🐬','🐳','🐋','🦈','🐊','🐅','🐆',
                    '🦓','🦍','🦧','🐘','🦛','🦏','🐪','🐫',
                    '🦒','🦘','🦬','🐃','🐂','🐄','🐎','🐖',
                    '🐏','🐑','🦙','🐐','🦌','🐕','🐩','🦮',
                    '🐕‍🦺','🐈','🐈‍⬛','🐓','🦃','🦤','🦚','🦜',
                    '🦢','🦩','🕊️','🐇','🦝','🦨','🦡','🦫',
                    '🦦','🦥','🐁','🐀','🐿️','🌲','🌳','🌴',
                    '🌵','🌶️','🌷','🌹','🥀','🌺','🌸','🌼',
                    '🌻','🌞','🌝','🌛','🌜','🌚','🌕','🌖',
                    '🌗','🌘','🌑','🌒','🌓','🌔','🌙','🌎',
                    '🌍','🌏','🪐','⭐','🌟','💫','✨','☄️',
                    '💥','🔥','🌈','☀️','⛅','☁️','⛈️','🌤️',
                ];
            @endphp
            @foreach($animals as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Food & Drink -->
    <div class="emoji-category hidden" data-category="food">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Food &amp; Drink</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $food = [
                    '🍕','🍔','🍟','🌭','🍿','🧂','🥓','🥚',
                    '🍳','🥘','🥗','🍿','🧈','🧇','🥞','🥩',
                    '🍗','🍖','🦴','🌮','🌯','🫔','🥙','🧆',
                    '🥚','🍳','🥘','🍲','🫕','🥣','🥗','🍿',
                    '🧈','🧇','🥞','🥩','🍗','🍖','🦴','🌮',
                    '🌯','🫔','🥙','🧆','🥚','🍳','🥘','🍲',
                    '🫕','🥣','🥗','🍿','🧈','🧇','🥞','🥩',
                    '🍗','🍖','🦴','🌮','🌯','🫔','🥙','🧆',
                    '🍱','🍘','🍙','🍚','🍛','🍜','🍝','🍠',
                    '🍢','🍣','🍤','🍥','🥮','🍡','🥟','🥠',
                    '🥡','🦀','🦞','🦐','🦑','🦪','🍦','🍧',
                    '🍨','🍩','🍪','🎂','🍰','🧁','🥧','🍫',
                    '🍬','🍭','🍮','🍯','🍼','🥛','☕','🫖',
                    '🍵','🍶','🍾','🍷','🍸','🍹','🍺','🍻',
                    '🥂','🥃','🥤','🧋','🧃','🧉','🧊','🥢',
                    '🍽️','🍴','🥄','🔪','🏺',
                ];
            @endphp
            @foreach($food as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Activities -->
    <div class="emoji-category hidden" data-category="activities">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Activities</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $activities = [
                    '⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉',
                    '🥏','🎱','🏓','🏸','🏒','🏑','🥍','🏏',
                    '🥅','⛳','🏹','🎣','🤿','🥊','🥋','🎽',
                    '🛹','🛷','⛸️','🥌','🎿','⛷️','🏂','🪂',
                    '🏋️','🤼','🤸','⛹️','🤺','🤾','🏌️','🏇',
                    '🧘','🏄','🏊','🤽','🚣','🧗','🚵','🚴',
                    '🏆','🥇','🥈','🥉','🏅','🎖️','🏵️','🎗️',
                    '🎫','🎟️','🎪','🤹','🎭','🩰','🎨','🎬',
                    '🎤','🎧','🎼','🎹','🥁','🎷','🎺','🎸',
                    '🪗','🎻','🎲','♟️','🎯','🎳','🎮','🎰',
                    '🧩',
                ];
            @endphp
            @foreach($activities as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Travel & Places -->
    <div class="emoji-category hidden" data-category="travel">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Travel &amp; Places</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $travel = [
                    '🚗','🚕','🚙','🚌','🚎','🏎️','🚓','🚑',
                    '🚒','🚐','🛻','🚚','🚛','🚜','🦯','🦽',
                    '🦼','🛴','🚲','🛵','🏍️','🛺','🚨','🚔',
                    '🚍','🚘','🚖','🚡','🚠','🚟','🚃','🚋',
                    '🚞','🚝','🚄','🚅','🚈','🚂','🚆','🚇',
                    '🚊','🚉','✈️','🛫','🛬','🛩️','💺','🚁',
                    '🚟','🚠','🚡','🛰️','🚀','🛸','🛎️','🧳',
                    '⌛','⏳','⌚','⏰','⏲️','⏱️','🕛','🕧',
                    '🕐','🕜','🕑','🕝','🕒','🕞','🕓','🕟',
                    '🕔','🕠','🕕','🕡','🕖','🕢','🕗','🕣',
                    '🕘','🕤','🕙','🕥','🕚','🕦','🌍','🌎',
                    '🌏','🌐','🗺️','🧭','🏔️','⛰️','🌋','🗻',
                    '🏕️','🏖️','🏜️','🏝️','🏞️','🏟️','🏛️','🏗️',
                    '🧱','🏘️','🏚️','🏠','🏡','🏢','🏣','🏤',
                    '🏥','🏦','🏨','🏩','🏪','🏫','🏬','🏭',
                    '🏯','🏰','🗼','🗽','⛪','🕌','🛕','🕍',
                    '⛩️','🕋','⛲','⛺','🌁','🌃','🏙️','🌄',
                    '🌅','🌆','🌇','🌉','♨️','🎠','🎡','🎢',
                    '💈','🎪','🚂','🚃','🚄','🚅','🚆','🚇',
                    '🚈','🚉','🚊','🚝','🚞','🚟','🚠','🚡',
                    '🛤️','🛣️','🗾','🗺️',
                ];
            @endphp
            @foreach($travel as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Objects -->
    <div class="emoji-category hidden" data-category="objects">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Objects</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $objects = [
                    '💡','🔦','🕯️','🪔','🧯','🛢️','💸','💵',
                    '💴','💶','💷','💰','💳','💎','⚖️','🪜',
                    '🧰','🪛','🔧','🔨','⚒️','🛠️','⛏️','🪚',
                    '🔩','⚙️','🪤','🧱','⛓️','🧲','🔫','💣',
                    '🧨','🪓','🔪','🗡️','⚔️','🛡️','🚬','⚰️',
                    '🪦','⚱️','🏺','🔮','📿','🧿','💈','⚗️',
                    '🔭','🔬','🕳️','🩹','🩺','💊','💉','🩸',
                    '🧬','🦠','🧫','🧪','🌡️','🧹','🪠','🧺',
                    '🧻','🚽','🚿','🛁','🛀','🧼','🪒','🧽',
                    '🪣','🧴','🛎️','🔑','🗝️','🚪','🪑','🛋️',
                    '🛏️','🛌','🧸','🪆','🖼️','🪞','🪟','🛍️',
                    '🛒','🎁','🎈','🎏','🎀','🪄','🪅','🎊',
                    '🎉','🎎','🏮','🎐','🧧','✉️','📩','📨',
                    '📧','💌','📥','📤','📦','🏷️','🪧','📪',
                    '📫','📬','📭','📮','📯','📜','📃','📄',
                    '📑','🧾','📊','📈','📉','🗒️','🗓️','📆',
                    '📅','📇','🗃️','🗳️','🗄️','📋','📁','📂',
                    '🗂️','🗞️','📰','📓','📔','📒','📕','📗',
                    '📘','📙','📚','📖','🔖','🧷','🔗','📎',
                    '🖇️','📏','📐','✂️','🗑️','🔒','🔓','🔏',
                    '🔐','🔑','🗝️','🔨','🪓','⛏️','⚒️','🛠️',
                    '🪛','🔧','🔩','⚙️','🪤','🧰','🧲','🪜',
                ];
            @endphp
            @foreach($objects as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
    
    <!-- Symbols -->
    <div class="emoji-category hidden" data-category="symbols">
        <div class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3 font-semibold">Symbols</div>
        <div class="flex flex-wrap gap-1 md:gap-2" style="width: 100%; box-sizing: border-box;">
            @php
                $symbols = [
                    '❤️','🧡','💛','💚','💙','💜','🖤','🤍',
                    '🤎','💔','❣️','💕','💞','💓','💗','💖',
                    '💘','💝','💟','☮️','✝️','☪️','🕉️','☸️',
                    '✡️','🔯','🕎','☯️','☦️','🛐','⛎','♈',
                    '♉','♊','♋','♌','♍','♎','♏','♐',
                    '♑','♒','♓','🆔','⚛️','🉑','☢️','☣️',
                    '📴','📳','🈶','🈚','🈸','🈺','🈷️','✴️',
                    '🆚','💮','🉐','㊙️','㊗️','🈴','🈵','🈹',
                    '🈲','🅰️','🅱️','🆎','🆑','🅾️','🆘','❌',
                    '⭕','🛑','⛔','📛','🚫','💯','💢','♨️',
                    '🚷','🚯','🚳','🚱','🔞','📵','🚭','❗',
                    '❓','❕','❔','‼️','⁉️','🔅','🔆','〽️',
                    '⚠️','🚸','🔱','⚜️','🔰','♻️','✅','🈯',
                    '💹','❇️','✳️','❎','🌐','💠','Ⓜ️','🌀',
                    '💤','🏧','🚾','♿','🅿️','🈳','🈂️','🛂',
                    '🛃','🛄','🛅','🚹','🚺','🚼','🚻','🚮',
                    '🎦','📶','🈁','🔣','ℹ️','🔤','🔡','🔠',
                    '🆖','🆗','🆙','🆒','🆕','🆓','0️⃣','1️⃣',
                    '2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣',
                    '🔟','🔢','#️⃣','*️⃣','⏏️','▶️','⏸️','⏯️',
                    '⏹️','⏺️','⏭️','⏮️','⏩','⏪','⏫','⏬',
                    '◀️','🔼','🔽','➡️','⬅️','⬆️','⬇️','↗️',
                    '↘️','↙️','↖️','↕️','↔️','↪️','↩️','⤴️',
                    '⤵️','🔀','🔁','🔂','🔄','🔃','🎵','🎶',
                    '➕','➖','➗','✖️','♾️','💲','💱','™️',
                    '©️','®️','〰️','➰','➿','🔚','🔙','🔛',
                    '🔝','🔜','✔️','☑️','🔘','⚪','⚫','🔴',
                    '🟠','🟡','🟢','🔵','🟣','🟤','⚫','⚪',
                    '🟥','🟧','🟨','🟩','🟦','🟪','🟫','⬛',
                    '⬜','◼️','◻️','◾','◽','▪️','▫️','🔶',
                    '🔷','🔸','🔹','🔺','🔻','💠','🔘','🔳',
                    '🔲','🏁','🚩','🎌','🏴','🏳️','🏳️‍🌈','🏳️‍⚧️',
                    '🏴‍☠️',
                ];
            @endphp
            @foreach($symbols as $emoji)
                <span class="emoji-item text-xl md:text-2xl cursor-pointer hover:bg-gray-200 rounded p-1 md:p-1.5 text-center transition-colors" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
            @endforeach
        </div>
    </div>
</div>
<div class="py-2 px-2 border-t border-gray-300 flex items-center justify-between gap-1 overflow-x-auto overflow-y-hidden emoji-categories w-full" style="flex-shrink: 0; flex-grow: 0; min-height: 50px; height: 50px; max-height: 50px; display: flex; align-items: center;">
    <button class="emoji-category-btn active px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="smileys" title="Smileys &amp; People">😀</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="gestures" title="Gestures">👋</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="animals" title="Animals &amp; Nature">🐶</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="food" title="Food &amp; Drink">🍕</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="activities" title="Activities">⚽</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="travel" title="Travel &amp; Places">🚗</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="objects" title="Objects">💡</button>
    <button class="emoji-category-btn px-2 py-1.5 rounded hover:bg-gray-200 transition text-sm md:text-base flex items-center justify-center min-w-0 flex-1" data-category="symbols" title="Symbols">❤️</button>
</div>


