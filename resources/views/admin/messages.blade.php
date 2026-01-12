@extends('admin.layout')

@section('title', 'Messages')

@php
    $pageTitle = 'Messages';
@endphp

<script>
    // Force light mode for admin messages page - run immediately and continuously
    (function() {
        // Function to enforce light mode
        function enforceLightMode() {
            // Remove dark class if present
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
            }
            // Ensure light mode is set in localStorage
            localStorage.setItem('theme', 'light');
        }
        
        // Run immediately (before DOM is ready)
        enforceLightMode();
        
        // Use MutationObserver to watch for any attempts to add dark class
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (document.documentElement.classList.contains('dark')) {
                            enforceLightMode();
                        }
                    }
                });
            });
            
            // Start observing when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    observer.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class']
                    });
                    enforceLightMode();
                });
            } else {
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
                enforceLightMode();
            }
        }
        
        // Also enforce on DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', enforceLightMode);
        } else {
            enforceLightMode();
        }
        
        // Enforce periodically to catch any late changes (every 500ms is sufficient)
        setInterval(enforceLightMode, 500);
    })();
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
        /* Force light mode for admin messages - override all dark mode styles */
        html, html.dark {
            color-scheme: light !important;
        }
        
        /* Force light backgrounds */
        #conversationsList,
        #chatArea,
        #activeChat,
        #emojiPickerPopup,
        #userSelectionModal,
        #userSelectionModal > div {
            background-color:rgba(255, 255, 255, 0) !important;
        }
        
        #emojiPickerPopup{
            background-color:#fff !important;
        }
        /* Force light mode for conversation list */
        #conversationsList {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
        }
        
        #conversationsList input,
        #conversationsList .conversation-item {
            background-color: #ffffff !important;
            color: #1f2937 !important;
        }
        
        #conversationsList .conversation-item:hover {
            background-color: #f9fafb !important;
        }
        
        /* Force light mode for chat area */
        #chatArea {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
        }
        
        /* #chatMessagesArea {
            background-color: #f9fafb !important;
        } */
        
        #activeChat > div:last-child,
        #messageForm,
        #emptyStateMessageInput {
            background-color: #f9fafb !important;
            border-top-color: #e5e7eb !important;
        }
        
        /* Force light mode for inputs */
        #messageInput,
        #conversationsSearch,
        #userSearchInput {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #1f2937 !important;
        }
        
        #conversationsSearch {
            background-color: #f9fafb !important;
        }
        
        /* Force light mode for text */
        #chatHeaderName,
        #chatHeaderStatusText,
        .conversation-item p,
        .text-gray-800,
        .text-gray-900 {
            color: #1f2937 !important;
        }
        
        .text-gray-500,
        .text-gray-400 {
            color: #6b7280 !important;
        }
        
        /* Force light mode for borders */
        .border-gray-200,
        .border-gray-300,
        .border-gray-700 {
            border-color: #e5e7eb !important;
        }
        
        /* Override: Remove all blue from reactions modal */
        #reactionsModal [class*="bg-blue"],
        #reactionsModal [class*="bg-purple"] {
            background-color: transparent !important;
        }
        
        #reactionsModal .bg-white {
            background-color: #ffffff !important;
        }
        
        #reactionsModal .bg-gray-100 {
            background-color: #f3f4f6 !important;
        }
        
        #reactionsModal .bg-gray-200 {
            background-color: #e5e7eb !important;
        }
        
        /* Remove blue borders from reactions modal */
        #reactionsModal [class*="border-blue"],
        #reactionsModal .border-blue-500 {
            border-color: #374151 !important;
        }
        
        /* Remove blue text colors from reactions modal */
        #reactionsModal [class*="text-blue"],
        #reactionsModal .text-blue-500 {
            color: #1f2937 !important;
        }
        
        /* Force light mode for reply indicator */
        #replyIndicator {
            background-color: #f3f4f6 !important;
            border-left-color: #3b82f6 !important;
        }
        
        /* Force light mode for buttons
        button:not(.new-message-btn):not([class*="bg-blue"]):not([class*="bg-gradient"]):not(#groupSettingsModal button):not(.toggle-admin-btn):not(.remove-member-btn) {
            color: #374151 !important;
        }
        
        button:hover:not(.new-message-btn):not([class*="bg-blue"]):not([class*="bg-gradient"]):not(#groupSettingsModal button):not(.toggle-admin-btn):not(.remove-member-btn) {
            background-color: #f3f4f6 !important;
        } */
        
        /* Group Settings Modal - Override admin button styles to match user side */
        #groupSettingsModal button[style*="background"] {
            color: white !important;
        }
        
        #groupSettingsModal #changeGroupAvatarBtn:hover,
        #groupSettingsModal #saveGroupInfoBtn:hover,
        #groupSettingsModal #addMembersBtn:hover {
            opacity: 0.9 !important;
            background: #055498 !important;
        }
        
        #groupSettingsModal #removeGroupAvatarBtn:hover {
            background-color: #4b5563 !important;
        }
        
        #groupSettingsModal .toggle-admin-btn:hover,
        #groupSettingsModal .remove-member-btn:hover {
            opacity: 0.9 !important;
        }
        
        /* Force light mode for modals */
        #userSelectionModal > div {
            background-color: #ffffff !important;
        }
        
        #userSelectionModal .user-item:hover {
            background-color: #f9fafb !important;
        }
        
        /* Force light mode for reaction picker */
        .reaction-picker-popup {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
        }
        
        .reaction-picker-popup button {
            background-color: transparent !important;
            color: #1f2937 !important;
        }
        
        .reaction-picker-popup button:hover {
            background-color: #f3f4f6 !important;
        }
        
        /* Force light mode for message timestamps */
        .px-3.py-1.bg-gray-200,
        [class*="bg-gray-200"][class*="rounded-full"] {
            background-color: #e5e7eb !important;
        }
        
        [class*="text-gray-600"][class*="font-medium"] {
            color: #4b5563 !important;
        }
        
        /* Message reactions - color will be set by theme dynamically */
        /* .message-reactions {
            color: #4b5563 !important;
        } */
        
        /* Force light mode for back button */
        #backToConversations {
            color: #4b5563 !important;
        }
        
        #backToConversations:hover {
            background-color: #f3f4f6 !important;
            color: #1f2937 !important;
        }
        
        /* Force light mode for reactions modal */
        #reactionsModal > div {
            background-color: #ffffff !important;
        }
        
        #reactionsModal .reaction-user-item:hover {
            background-color: #f3f4f6 !important;
        }
        
        #reactionsModal .reaction-tab {
            color: #4b5563 !important;
        }
        
        #reactionsModal .reaction-tab:hover {
            color: #1f2937 !important;
        }
        
        /* Global override: Force light mode for all elements - must be last */
        html:not([data-force-dark]) *,
        html:not([data-force-dark]) *::before,
        html:not([data-force-dark]) *::after {
            /* Override any dark mode background colors */
        }
        
        
        /* Override admin layout for messages page */
        body {
            overflow: hidden !important;
        }
        main {
            overflow: hidden !important;
            height: calc(100vh - 60px) !important;
            max-height: calc(100vh - 60px) !important;
            display: flex !important;
            flex-direction: column !important;
            padding: 0 !important;
        }
        /* Hide footer on messages page */
        footer, .agency-footer, .standard-footer {
            display: none !important;
        }
        /* Chat popup container is not included on messages page */
        .messages-page-container {
            height: calc(100vh - 60px);
            min-height: calc(100vh - 60px);
            max-height: calc(100vh - 60px);
        }
        /* Tablet (768px - 1023px) - 35% / 65% split for better balance */
        @media (min-width: 768px) and (max-width: 1023px) {
            .messages-page-container {
                height: calc(100vh - 60px);
                min-height: calc(100vh - 60px);
                max-height: calc(100vh - 60px);
                display: grid !important;
                grid-template-columns: 35% 65% !important;
                grid-template-rows: 1fr !important;
                grid-auto-flow: row !important;
                gap: 1rem;
            }
            .messages-container {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
                max-width: 100% !important;
            }
            /* Reduce gap between panels for better space utilization */
            .messages-page-container {
                gap: 0.75rem !important;
            }
            /* Force conversations list to first column */
            #conversationsList {
                grid-column: 1 / 2 !important;
                grid-row: 1 / 2 !important;
                min-width: 0;
                max-width: 100%;
                display: flex !important;
                overflow: hidden;
            }
            /* Force chat area to second column */
            #chatArea {
                grid-column: 2 / 3 !important;
                grid-row: 1 / 2 !important;
                min-width: 0;
                display: flex !important;
                overflow: hidden;
            }
            /* Keep both visible on tablet */
            #conversationsList.mobile-hidden {
                display: flex !important;
            }
            #chatArea.hidden {
                display: flex !important;
            }
            /* Better spacing for conversation items */
            .conversation-item {
                padding: 1rem 0.875rem;
            }
            /* Adjust search and button sizing for tablet */
            #conversationsSearch {
                font-size: 0.875rem;
                padding: 0.75rem 1rem;
            }
            #newMessageBtn {
                padding: 0.75rem 1.25rem;
                font-size: 0.875rem;
            }
            /* Improve chat area appearance */
            #chatArea {
                border-radius: 0.75rem;
            }
            /* Better message bubble sizing */
            .message-bubble {
                max-width: 75%;
            }
            /* Optimize header padding on tablet */
            #conversationsList > div:first-child {
                padding: 1rem 0.875rem;
            }
            /* Better conversation list header */
            .conversations-list {
                padding: 0;
            }
        }
        /* Laptop (1024px) - Fixed width for conversations */
        @media (min-width: 1024px) and (max-width: 1024px) {
            .messages-page-container {
                height: calc(100vh - 60px);
                min-height: calc(100vh - 60px);
                max-height: calc(100vh - 60px);
                display: grid !important;
                grid-template-columns: 380px 1fr !important;
                grid-template-rows: 1fr !important;
                grid-auto-flow: row !important;
                gap: 1.5rem;
            }
            .messages-container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                max-width: 100% !important;
            }
            /* Force conversations list to first column */
            #conversationsList {
                grid-column: 1 / 2 !important;
                grid-row: 1 / 2 !important;
                min-width: 360px;
                max-width: 380px;
                width: 100%;
                display: flex !important;
            }
            /* Force chat area to second column */
            #chatArea {
                grid-column: 2 / 3 !important;
                grid-row: 1 / 2 !important;
                min-width: 0;
                flex: 1;
                display: flex !important;
            }
            /* Keep both visible on laptop */
            #conversationsList.mobile-hidden {
                display: flex !important;
            }
            #chatArea.hidden {
                display: flex !important;
            }
            /* Better spacing for conversation items */
            .conversation-item {
                padding: 0.875rem 1rem;
            }
            /* Adjust search and button sizing */
            #conversationsSearch {
                font-size: 0.875rem;
                padding: 0.625rem 0.875rem;
            }
            #newMessageBtn {
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
            }
            /* Improve chat area appearance */
            #chatArea {
                border-radius: 0.75rem;
            }
            /* Better message bubble sizing */
            .message-bubble {
                max-width: 70%;
            }
        }
        /* Mobile (below 768px) */
        @media (max-width: 767px) {
            .messages-page-container {
                height: calc(100vh - 60px);
                min-height: calc(100vh - 60px);
                max-height: calc(100vh - 60px);
            }
        }
        @media (max-width: 640px) {
            .messages-page-container {
                height: calc(100vh - 60px);
                min-height: calc(100vh - 60px);
                max-height: calc(100vh - 60px);
            }
        }
        .conversations-list {
            height: calc(100% - 60px);
            max-height: calc(100% - 60px);
        }
        @media (max-width: 640px) {
            .conversations-list {
                height: calc(100% - 55px);
                max-height: calc(100% - 55px);
            }
        }
        .chat-messages-area {
            height: calc(100% - 200px);
            max-height: calc(100% - 200px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        @media (max-width: 640px) {
            .chat-messages-area {
                flex: 1 1 auto;
                min-height: 0;
                overflow-y: auto;
            }
            /* Ensure voice messages have proper sizing on mobile */
            .voice-message-container {
                max-width: 280px !important;
                min-width: 200px !important;
            }
            /* Make sure video elements are clearly different */
            video {
                max-width: 100% !important;
                border-radius: 8px !important;
            }
            /* Voice message waveform progress */
            .waveform-bar {
                transition: opacity 0.2s ease;
            }
            .voice-speed-toggle {
                min-width: 32px;
                min-height: 24px;
            }
            /* Ensure voice message container is responsive */
            @media (max-width: 640px) {
                .voice-message-container {
                    max-width: 260px !important;
                    min-width: 180px !important;
                    padding: 0.5rem 0.75rem !important;
                }
                .voice-duration {
                    font-size: 0.65rem !important;
                }
            }
            /* Fix message input at bottom of screen on mobile */
            #activeChat:not(.hidden) {
                position: relative;
                height: 100%;
                display: flex !important;
                flex-direction: column;
            }
            #activeChat:not(.hidden) > div:last-child {
                display: block !important;
                visibility: visible !important;
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                background: white !important;
                z-index: 50 !important;
                border-top: 1px solid #e5e7eb !important;
                padding: 12px !important;
                box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                margin: 0 !important;
            }
            /* Force light mode even on mobile */
            #activeChat:not(.hidden) > div:last-child {
                background: #f9fafb !important;
                border-top-color: #e5e7eb !important;
            }
            /* Account for safe area on mobile devices */
            @supports (padding-bottom: env(safe-area-inset-bottom)) {
                #activeChat:not(.hidden) > div:last-child {
                    padding-bottom: calc(12px + env(safe-area-inset-bottom)) !important;
                }
            }
            #messageForm {
                display: flex !important;
                visibility: visible !important;
                width: 100% !important;
            }
            #messageForm.hidden {
                display: none !important;
            }
            /* Ensure message form container is always visible when chat is active */
            #activeChat:not(.hidden) #messageForm {
                display: flex !important;
                visibility: visible !important;
            }
            /* Ensure message input container is always visible */
            #activeChat:not(.hidden) > div:last-child {
                display: block !important;
                visibility: visible !important;
            }
            /* Ensure message form is visible by default */
            form#messageForm {
                display: flex !important;
                visibility: visible !important;
            }
            /* Adjust messages area to account for fixed input */
            #chatMessagesArea {
                padding-bottom: 20px !important;
                margin-bottom: 0 !important;
            }
            @media (max-width: 640px) {
                #chatMessagesArea {
                    padding-bottom: 10px !important;
                    margin-bottom: 0 !important;
                }
            }
            /* Ensure chat area takes full height on mobile */
            #chatArea.mobile-visible {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                height: 100vh !important;
                width: 100vw !important;
                z-index: 40 !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }
            /* Ensure container doesn't interfere with fixed positioning */
            .messages-page-container {
                position: relative;
            }
            #chatArea.mobile-visible .messages-page-container {
                height: 100vh !important;
            }
        }
        /* Ensure message input area stays fixed at bottom */
        #activeChat {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        #activeChat.hidden {
            display: none !important;
        }
        #activeChat > div:last-child {
            flex-shrink: 0;
            position: relative;
            z-index: 10;
            margin-top: auto;
            display: block !important;
            visibility: visible !important;
        }
        /* Ensure message input container is always at bottom */
        #activeChat:not(.hidden) > div:last-child {
            margin-top: 0px; /* JavaScript will override this when replying */
            display: block !important;
            visibility: visible !important;
        }
        /* Ensure message form is always visible */
        form#messageForm {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        /* Override any hidden class on message form when chat is active */
        #activeChat:not(.hidden) form#messageForm.hidden {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        /* Force activeChat to be visible when not hidden */
        #activeChat:not(.hidden) {
            display: flex !important;
            visibility: visible !important;
        }
        /* Ensure message input container is always visible when activeChat is shown */
        #activeChat:not(.hidden) > div:last-child {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        /* Force message form to be visible */
        #activeChat:not(.hidden) form#messageForm {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
        }
        /* Prevent reply indicator from causing layout shifts */
        #replyIndicator {
            transition: opacity 0.2s ease, max-height 0.2s ease;
            max-height: 0;
            overflow: hidden;
            margin-bottom: 0 !important;
        }
        #replyIndicator:not(.hidden) {
            max-height: 200px;
            margin-bottom: 0.5rem !important;
        }
        /* Ensure input container doesn't shift when reply indicator appears */
        #activeChat > div:last-child {
            min-height: fit-content;
        }
        .chat-messages-area * {
            max-width: 100%;
            box-sizing: border-box;
        }
        .chat-messages-area video {
            max-width: 100% !important;
            height: auto !important;
        }
        /* Mobile-specific styles (below 768px) */
        @media (max-width: 767px) {
            #conversationsList.mobile-hidden {
                display: none !important;
            }
            #chatArea.mobile-visible {
                display: flex !important;
            }
            /* Ensure chat area and message input are visible on mobile when chat is active */
            #chatArea.mobile-visible #activeChat:not(.hidden) {
                display: flex !important;
            }
            #chatArea.mobile-visible #activeChat:not(.hidden) > div:last-child {
                display: block !important;
                visibility: visible !important;
            }
            #chatArea.mobile-visible #activeChat:not(.hidden) #messageForm {
                display: flex !important;
                visibility: visible !important;
            }
            /* Hide header when chat is open on mobile */
            #chatArea.mobile-visible ~ * .top-bar,
            body:has(#chatArea.mobile-visible) .top-bar,
            body:has(#chatArea.mobile-visible) nav {
                display: none !important;
            }
        }
        /* Alternative method for browsers that don't support :has() */
        @media (max-width: 767px) {
            body.header-hidden-mobile .top-bar,
            body.header-hidden-mobile nav {
                display: none !important;
            }
        }
        
        /* Mobile header optimization */
        @media (max-width: 767px) {
            /* Compact header padding */
            header.bg-white {
                padding: 0.5rem 0.75rem !important;
            }
            
            header .flex.items-center.justify-between {
                padding: 0 !important;
            }
            
            /* Reduce spacing between header items */
            header .flex.items-center.space-x-3,
            header .flex.items-center.space-x-4 {
                gap: 0.375rem !important;
            }
            
            /* Smaller buttons on mobile - ensure touch-friendly */
            header button,
            header .dropdown button {
                padding: 0.5rem !important;
                min-width: 44px !important;
                min-height: 44px !important;
            }
            
            /* Smaller icons on mobile */
            header .far,
            header .fas {
                font-size: 1.125rem !important;
            }
            
            /* Smaller profile picture on mobile */
            header img.h-8.w-8 {
                width: 2rem !important;
                height: 2rem !important;
            }
            
            /* Hide profile name and chevron on mobile */
            header .hidden.md\:block {
                display: none !important;
            }
            
            /* Adjust dropdown width on mobile - full width with margins */
            header .dropdown-menu {
                width: calc(100vw - 1rem) !important;
                max-width: 360px !important;
                right: 0.5rem !important;
                left: auto !important;
                margin-top: 0.5rem !important;
            }
            
            /* Smaller dropdown header */
            header .dropdown-menu .p-3 {
                padding: 0.75rem !important;
            }
            
            /* Smaller text in dropdowns */
            header .dropdown-menu h6 {
                font-size: 0.875rem !important;
            }
            
            /* Adjust notification/message badge position and size */
            header .relative button .absolute {
                top: -2px !important;
                right: -2px !important;
                width: 16px !important;
                height: 16px !important;
                font-size: 0.625rem !important;
                line-height: 1 !important;
            }
            
            /* Ensure hamburger menu is visible and properly sized */
            header #sidebarCollapse {
                min-width: 44px !important;
                min-height: 44px !important;
            }
            
            /* Better spacing for header title area */
            header .flex.items-center.space-x-4 {
                flex: 1;
                min-width: 0;
            }
            
            /* Ensure header doesn't overflow */
            header {
                overflow: hidden;
            }
            
            /* Make sure dropdowns don't get cut off */
            header .dropdown {
                position: relative;
            }
        }
        
        /* Extra small mobile devices */
        @media (max-width: 480px) {
            header .flex.items-center.space-x-3,
            header .flex.items-center.space-x-4 {
                gap: 0.25rem !important;
            }
            
            header button,
            header .dropdown button {
                padding: 0.375rem !important;
            }
            
            header .dropdown-menu {
                width: calc(100vw - 0.5rem) !important;
                right: 0.25rem !important;
            }
        }
        /* Responsive search and button optimizations */
        @media (max-width: 640px) {
            #conversationsSearch {
                font-size: 16px; /* Prevents zoom on iOS */
                padding: 0.625rem 0.75rem;
            }
            #newMessageBtn {
                min-width: 44px;
                min-height: 44px;
                padding: 0.625rem 0.75rem;
            }
            .new-message-btn i {
                font-size: 0.75rem;
            }
            /* Fix message form on mobile - ensure send button is visible */
            #messageForm {
                width: 100% !important;
                max-width: 100% !important;
                overflow: visible !important;
                flex-wrap: nowrap !important;
            }
            #messageForm > * {
                flex-shrink: 0 !important;
            }
            #messageInput {
                min-width: 0 !important;
                flex: 1 1 0% !important;
                max-width: 100% !important;
            }
            #sendBtn {
                flex-shrink: 0 !important;
                visibility: visible !important;
                display: flex !important;
            }
            /* Ensure form container doesn't overflow */
            #activeChat:not(.hidden) > div:last-child {
                overflow: visible !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
        }
        @media (min-width: 641px) and (max-width: 767px) {
            #conversationsSearch {
                padding: 0.75rem 1rem;
            }
            #newMessageBtn {
                padding: 0.75rem 1rem;
            }
        }
        /* Ensure button text doesn't break and has proper sizing */
        .new-message-btn {
            flex-shrink: 0;
        }
        /* Better placeholder text on mobile */
        @media (max-width: 640px) {
            #conversationsSearch::placeholder {
                font-size: 14px;
            }
        }
        /* Touch-friendly buttons */
        @media (max-width: 640px) {
            button {
                min-width: 44px;
                min-height: 44px;
            }
            /* Compressed message action buttons - still touch-friendly but more compact */
            .message-react-btn, .message-reply-btn, .message-delete-btn {
                min-width: 36px;
                min-height: 36px;
                padding: 0.5rem !important;
                /* Remove tap highlight and focus ring on mobile */
                -webkit-tap-highlight-color: transparent !important;
                tap-highlight-color: transparent !important;
                outline: none !important;
                -webkit-touch-callout: none;
                touch-action: manipulation;
            }
            /* Remove focus ring on active state */
            .message-react-btn:active, .message-reply-btn:active, .message-delete-btn:active,
            .message-react-btn:focus, .message-reply-btn:focus, .message-delete-btn:focus {
                outline: none !important;
                box-shadow: none !important;
                -webkit-tap-highlight-color: transparent !important;
            }
            input, textarea {
                font-size: 16px; /* Prevents zoom on iOS */
            }
            .conversation-item {
                min-height: 70px;
            }
        }
        /* Tablet/Laptop (768px - 1024px) - Normal hover behavior */
        @media (min-width: 768px) and (max-width: 1024px) {
            .conversation-item, button {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
        }
        /* Always show action buttons on all devices */
        .message-react-btn,
        .message-reply-btn,
        .message-delete-btn {
            opacity: 1 !important;
            visibility: visible !important;
        }
        /* Show action buttons container - always visible */
        .flex.items-center.gap-0.5,
        .flex.items-center.gap-1 {
            opacity: 1 !important;
            visibility: visible !important;
        }
        /* Mobile (below 768px) */
        @media (max-width: 767px) {
            .conversation-item, button {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
        }
    </style>

@section('content')




    <!-- Messages Content -->
    <div class="h-full overflow-hidden" style="height: calc(100vh - 60px);">
        <div class="w-full px-2 sm:px-4 lg:px-8 h-full py-1 sm:py-2 messages-container" style="height: 100%;">
            <!-- Messages Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 messages-page-container h-full" style="height: 100%;">
                <!-- Conversations List -->
                <div id="conversationsList" class="md:col-span-1 lg:col-span-1 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden flex flex-col">
                    <div class="p-3 sm:p-4 border-b border-gray-200">
                        <div class="flex items-center gap-1.5 sm:gap-2 md:gap-2.5">
                            <input type="text" id="conversationsSearch" placeholder="Search conversations..." class="flex-1 px-2.5 sm:px-3 md:px-4 py-2 sm:py-2.5 text-xs sm:text-sm md:text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none min-w-0">
                            <button id="newMessageBtn" class="px-2.5 sm:px-3 md:px-4 py-2 sm:py-2.5 text-xs sm:text-sm md:text-sm font-semibold text-white rounded-lg transition whitespace-nowrap hover:opacity-90 min-w-[44px] sm:min-w-[auto] flex items-center justify-center new-message-btn" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'" onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'">
                                <i class="fas fa-plus text-xs sm:text-sm md:text-sm mr-0.5 sm:mr-1"></i> 
                                <span>New</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto conversations-list" id="conversationsContainer">
                        <div class="p-4 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm">Loading conversations...</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div id="chatArea" class="md:col-span-1 lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden flex flex-col hidden md:flex">
                    <!-- Empty State -->
                    <div id="chatEmptyState" class="flex-1 flex items-center justify-center p-8">
                        <div class="text-center">
                            <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">Select a conversation to start chatting</p>
                        </div>
                    </div>
                    
                    <!-- Message Input for Empty State (always visible) -->
                    <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0" id="emptyStateMessageInput" style="display: none;">
                        <form id="messageFormEmpty" class="flex items-center space-x-1 sm:space-x-2" style="display: flex; visibility: visible; pointer-events: none; opacity: 0.5;">
                            <input type="file" id="fileInputEmpty" class="hidden" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                            <button type="button" class="p-2 sm:p-2.5 text-blue-600 hover:bg-gray-200 rounded-full transition min-w-[44px] min-h-[44px] flex items-center justify-center" disabled>
                                <i class="fas fa-paperclip text-base sm:text-lg"></i>
                            </button>
                            <button type="button" class="p-2 sm:p-2.5 text-red-500 hover:bg-gray-200 rounded-full transition min-w-[44px] min-h-[44px] flex items-center justify-center" disabled>
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"></path>
                                    <path d="M19 11a1 1 0 0 0-2 0 5 5 0 0 1-10 0 1 1 0 0 0-2 0 7.002 7.002 0 0 0 6 6.92V21h-2a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-2v-3.08A7.002 7.002 0 0 0 19 11z"></path>
                                </svg>
                            </button>
                            <input type="text" placeholder="Select a conversation to start chatting..." class="flex-1 px-3 sm:px-4 py-2.5 sm:py-2 text-sm sm:text-base border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" disabled>
                            <button type="button" class="p-2 sm:p-2.5 text-yellow-500 hover:bg-gray-200 rounded-full transition min-w-[44px] min-h-[44px] flex items-center justify-center" disabled>
                                <i class="fas fa-smile text-base sm:text-lg"></i>
                            </button>
                            <button type="button" class="px-4 sm:px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition min-w-[44px] min-h-[44px] flex items-center justify-center" disabled>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Active Chat -->
                    <div id="activeChat" class="flex flex-col h-full hidden" style="position: relative; display: flex; flex-direction: column;">
                        <!-- Chat Header -->
                        <div class="px-3 sm:px-4 py-2 sm:py-3 border-b border-gray-200 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 sm:space-x-3 flex-1 min-w-0">
                                    <!-- Back button for mobile -->
                                    <button id="backToConversations" class="lg:hidden mr-2 p-2 text-gray-600 hover:bg-gray-100 rounded-full transition flex-shrink-0" aria-label="Back to conversations">
                                        <i class="fas fa-arrow-left text-lg"></i>
                                    </button>
                                    <div id="chatHeaderAvatar" class="relative flex-shrink-0">
                                        <!-- Avatar will be inserted here -->
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 id="chatHeaderName" class="text-sm sm:text-base font-semibold text-gray-900 truncate leading-tight"></h3>
                                        <div id="chatHeaderStatus" class="flex items-center gap-1.5 mt-0.5">
                                            <span id="chatHeaderStatusText" class="text-xs text-gray-500"></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Settings buttons -->
                                <div class="flex items-center gap-2">
                                    <!-- Single chat settings button (hidden for groups) -->
                                    <button id="singleChatSettingsBtn" class="hidden p-2 text-gray-600 hover:bg-gray-100 rounded-full transition flex-shrink-0" aria-label="Chat settings">
                                        <i class="fas fa-cog text-lg"></i>
                                    </button>
                                    <!-- Group settings button (only for group admins) -->
                                    <button id="groupSettingsBtn" class="hidden p-2 text-gray-600 hover:bg-gray-100 rounded-full transition flex-shrink-0" aria-label="Group settings">
                                        <i class="fas fa-cog text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div class="flex-1 overflow-y-auto overflow-x-hidden p-3 sm:p-4 space-y-4 bg-gray-50 chat-messages-area" id="chatMessagesArea" style="flex: 1 1 auto; min-height: 0;">
                            <!-- Messages will be loaded here -->
                        </div>

                        <!-- Message Input -->
                        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0" style="position: relative; margin-top: 0px !important; display: block; visibility: visible;">
                            <!-- Reply Indicator -->
                            <div id="replyIndicator" class="hidden mb-2 px-3 py-2 bg-gray-100 rounded-lg border-l-4 border-blue-500" style="position: relative; z-index: 1;">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-700">Replying to <span id="replyToName"></span></p>
                                        <p id="replyToMessage" class="text-xs text-gray-500 truncate"></p>
                                    </div>
                                    <button id="cancelReplyBtn" class="ml-2 text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- File Preview -->
                            <div id="filePreview" class="hidden px-3 flex items-center gap-2 sm:gap-3 overflow-x-auto max-h-32 overflow-y-auto touch-pan-x" style="-webkit-overflow-scrolling: touch;"></div>
                            <!-- Voice Recorder Bar -->
                            <div id="voiceRecorder" class="hidden mb-2 px-3 pb-3">
                                <div class="flex items-center gap-3 rounded-full px-4 py-2" style="background-color:#FF1F70;">
                                    <!-- Cancel recording -->
                                    <button type="button" id="voiceCancelBtn" class="flex items-center justify-center w-8 h-8 rounded-full text-white text-lg font-bold hover:bg-pink-700/60 transition" aria-label="Cancel recording">
                                        ×
                                    </button>
                                    <!-- Stop icon -->
                                    <button type="button" id="voiceStopBtn" class="flex items-center justify-center w-9 h-9 rounded-full bg-white text-pink-500 hover:bg-gray-100 transition" aria-label="Stop recording">
                                        <span class="w-3 h-3 rounded-sm" style="background-color:#FF1F70;"></span>
                                    </button>
                                    <!-- Timer -->
                                    <div class="flex-1 flex justify-end">
                                        <div class="inline-flex items-center justify-center px-4 py-1 rounded-full bg-white text-pink-600 text-xs font-semibold" id="voiceTimer">
                                            0:00
                                        </div>
                                    </div>
                                    <!-- Send voice message -->
                                    <button type="button" id="voiceSendBtn" class="flex items-center justify-center w-9 h-9 rounded-full bg-white text-pink-500 hover:bg-gray-100 transition" aria-label="Send voice message">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3.4 20.4L5 14 14 12 5 10 3.4 3.6 21 12 3.4 20.4Z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Input Form -->
                            <form id="messageForm" class="flex items-center space-x-1 sm:space-x-2 flex-nowrap overflow-hidden" style="display: flex; visibility: visible; width: 100%;">
                                <input type="file" id="fileInput" class="hidden" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                <button type="button" id="attachBtn" class="p-1.5 sm:p-2.5 text-blue-600 hover:bg-gray-200 rounded-full transition min-w-[36px] min-h-[36px] sm:min-w-[44px] sm:min-h-[44px] flex items-center justify-center flex-shrink-0" title="Attach files">
                                    <i class="fas fa-paperclip text-sm sm:text-lg"></i>
                                </button>
                                <button type="button" id="voiceBtn" class="p-1.5 sm:p-2.5 text-red-500 hover:bg-gray-200 rounded-full transition min-w-[36px] min-h-[36px] sm:min-w-[44px] sm:min-h-[44px] flex items-center justify-center flex-shrink-0" title="Record voice message">
                                    <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"></path>
                                        <path d="M19 11a1 1 0 0 0-2 0 5 5 0 0 1-10 0 1 1 0 0 0-2 0 7.002 7.002 0 0 0 6 6.92V21h-2a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-2v-3.08A7.002 7.002 0 0 0 19 11z"></path>
                                    </svg>
                                </button>
                                <input type="text" id="messageInput" placeholder="Type a message..." class="flex-1 min-w-0 px-2 sm:px-4 py-2 sm:py-2 text-sm sm:text-base border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <button type="button" id="emojiBtn" class="p-1.5 sm:p-2.5 text-yellow-500 hover:bg-gray-200 rounded-full transition min-w-[36px] min-h-[36px] sm:min-w-[44px] sm:min-h-[44px] flex items-center justify-center flex-shrink-0" title="Add emoji">
                                    <i class="fas fa-smile text-sm sm:text-lg"></i>
                                </button>
                                <button type="submit" id="sendBtn" class="px-2 sm:px-4 py-2 sm:py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition min-w-[36px] min-h-[36px] sm:min-w-[44px] sm:min-h-[44px] flex items-center justify-center flex-shrink-0" title="Send message">
                                    <i class="fas fa-paper-plane text-sm sm:text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emoji Picker Popup -->
    <div id="emojiPickerPopup" class="hidden fixed z-[9999] bg-white rounded-xl shadow-2xl border border-gray-200 flex flex-col" style="width: 320px; height: 300px;">
        @include('components.emoji-picker')
    </div>

    <!-- User Selection Modal -->
    <div id="userSelectionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-opacity-20 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Select User(s)</h3>
                <button id="closeUserModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 border-b border-gray-200">
                <input type="text" id="userSearchInput" placeholder="Search users..." class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>
            <div class="p-3 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="selectAllUsers" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-600 font-medium">Select All</span>
                        </label>
                    </div>
                    <span class="text-sm text-gray-600">
                        <span id="selectedCount">0</span> selected
                    </span>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button id="openChatBtn" class="hidden px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                        <i class="fas fa-comment mr-2"></i><span id="openChatBtnText">Open Chat</span>
                    </button>
                    <button id="createGroupBtn" class="hidden px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                        <i class="fas fa-users mr-2"></i>Create Group
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto" id="usersListContainer">
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-sm">Loading users...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Group Settings Modal - Professional Branded Style -->
    <div id="groupSettingsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm p-2 sm:p-4" style="background: rgba(0, 0, 0, 0.5);">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl max-h-[95vh] sm:max-h-[92vh] flex flex-col border border-gray-200 overflow-hidden animate-fade-in">
            <!-- Header with brand color -->
            <div class="relative p-3 sm:p-5 flex items-center justify-between" style="background: #055498;">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-white text-sm sm:text-base"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white">Group Settings</h3>
                </div>
                <button id="closeGroupSettingsModal" class="w-8 h-8 min-w-[32px] min-h-[32px] bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition-colors duration-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 bg-white overflow-x-auto">
                <div class="flex min-w-max sm:min-w-0">
                    <button class="group-settings-tab px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap" data-tab="info" style="border-color: #055498; color: #055498;">
                        <i class="fas fa-info-circle mr-1.5 sm:mr-2"></i>Info
                    </button>
                    <button class="group-settings-tab px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors duration-200 text-gray-500 border-transparent hover:text-gray-700 whitespace-nowrap" data-tab="members">
                        <i class="fas fa-users mr-1.5 sm:mr-2"></i>Members
                    </button>
                    <button class="group-settings-tab px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors duration-200 text-gray-500 border-transparent hover:text-gray-700 whitespace-nowrap" data-tab="theme">
                        <i class="fas fa-palette mr-1.5 sm:mr-2"></i>Theme
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 bg-gray-50">
                <!-- Info Tab Content -->
                <div id="groupSettingsInfoTab" class="tab-content">
                    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-1 h-5 rounded-full" style="background: #055498;"></div>
                            <h4 class="text-sm font-semibold text-gray-800">Group Information</h4>
                        </div>
                        
                        <!-- Group Avatar -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Group Profile Image</label>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                                <div class="relative flex-shrink-0">
                                    <img id="groupSettingsAvatarPreview" src="" alt="Group Avatar" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-gray-200 shadow-md">
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center shadow-md border-2 border-white" style="background: #055498;">
                                        <i class="fas fa-users text-white text-[8px] sm:text-[10px]"></i>
                                    </div>
                                    <input type="file" id="groupAvatarInput" accept="image/*" class="hidden">
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button id="changeGroupAvatarBtn" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center justify-center gap-2 min-h-[44px]" style="background: #055498;">
                                        <i class="fas fa-image text-xs"></i>
                                        <span>Change</span>
                                    </button>
                                    <button id="removeGroupAvatarBtn" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 min-h-[44px]">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Remove</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Group Name -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Group Name</label>
                            <input type="text" id="groupSettingsNameInput" placeholder="Enter group name..." class="w-full px-3 py-2.5 sm:py-2 text-sm sm:text-base border border-gray-300 rounded-lg bg-white text-gray-800 outline-none transition-all duration-200 placeholder:text-gray-400 focus:border-055498 focus:ring-1 min-h-[44px]" style="focus:border-color: #055498; focus:ring-color: #055498;">
                        </div>
                        
                        <!-- Group Description -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="groupSettingsDescriptionInput" placeholder="Enter group description..." rows="3" class="w-full px-3 py-2.5 sm:py-2 text-sm sm:text-base border border-gray-300 rounded-lg bg-white text-gray-800 outline-none resize-none transition-all duration-200 placeholder:text-gray-400 focus:border-055498 focus:ring-1" style="focus:border-color: #055498; focus:ring-color: #055498;"></textarea>
                        </div>
                        
                        <button id="saveGroupInfoBtn" class="w-full px-4 py-3 sm:py-2.5 text-white text-sm sm:text-base font-semibold rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center justify-center gap-2 min-h-[44px]" style="background: #055498;">
                            <i class="fas fa-save text-sm"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
                
                <!-- Members Tab Content -->
                <div id="groupSettingsMembersTab" class="tab-content hidden">
                    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0 mb-4 sm:mb-5">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-5 rounded-full" style="background: #055498;"></div>
                                <h4 class="text-xs sm:text-sm font-semibold text-gray-800">Members</h4>
                                <span id="memberCountBadge" class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background: rgba(5, 84, 152, 0.1); color: #055498;">0</span>
                            </div>
                            <button id="addMembersBtn" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center justify-center gap-2 min-h-[44px]" style="background: #055498;">
                                <i class="fas fa-user-plus text-xs"></i>
                                <span>Add Members</span>
                            </button>
                        </div>
                        <div id="groupMembersList" class="space-y-2">
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                <p class="text-sm">Loading members...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Theme Tab Content -->
                <div id="groupSettingsThemeTab" class="tab-content hidden">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Header Section -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                                    <i class="fas fa-palette text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-white">Chat Theme</h4>
                                    <p class="text-xs text-white/80 mt-0.5">Customize the visual appearance of this group chat</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="mb-6">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    Select a theme to personalize this group chat. Changes apply to all members and persist across sessions. Only group admins can modify themes.
                                </p>
                            </div>
                            
                            <!-- Two Column Layout: Theme Selection and Preview -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                <!-- Left Column: Theme Selection -->
                                <div class="lg:border-r lg:border-gray-200 lg:pr-4 xl:pr-6">
                                    <div class="mb-4">
                                        <h5 class="text-sm sm:text-base font-bold text-gray-800 mb-1 flex items-center gap-2">
                                            <i class="fas fa-palette text-sm text-blue-600"></i>
                                            Available Themes
                                        </h5>
                                        <p class="text-xs text-gray-500">Choose a theme to customize your group chat</p>
                                    </div>
                                    <div id="themeSelectionGrid" class="space-y-3 max-h-[300px] sm:max-h-[400px] lg:max-h-[500px] overflow-y-auto pr-2 custom-scrollbar" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
                                        <div class="text-center py-12 text-gray-400">
                                            <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                                            <p class="text-sm">Loading themes...</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column: Theme Preview -->
                                <div class="lg:pl-4 xl:pl-6">
                                    <div id="themePreviewSection" class="p-3 sm:p-4 md:p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-inner min-h-[250px] sm:min-h-[300px] lg:min-h-[400px]">
                                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-eye text-blue-500 text-xs sm:text-sm"></i>
                                                <span class="text-xs sm:text-sm font-semibold text-gray-700">Live Preview</span>
                                            </div>
                                        </div>
                                        <div id="themePreviewContainer" class="space-y-3 bg-white rounded-lg p-3 sm:p-4 shadow-sm min-h-[200px] sm:min-h-[250px] lg:min-h-[350px]">
                                            <div class="text-center py-16 text-gray-400">
                                                <i class="fas fa-mouse-pointer text-3xl mb-3"></i>
                                                <p class="text-sm font-medium">Select a theme to see preview</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-3 sm:p-4 bg-white border-t border-gray-200">
                <!-- Default Footer (Info and Members tabs) -->
                <div id="defaultModalFooter" class="flex justify-end">
                    <button id="closeGroupSettingsBtn" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-medium rounded-lg transition-colors duration-200 min-h-[44px]">
                        Close
                    </button>
                </div>
                
                <!-- Theme Tab Footer -->
                <div id="themeModalFooter" class="hidden flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button id="applyThemeBtn" class="flex-1 px-4 sm:px-5 py-2.5 sm:py-3 text-white text-sm sm:text-base font-semibold rounded-lg transition-all duration-200 hover:opacity-90 hover:shadow-lg flex items-center justify-center gap-2 hidden min-h-[44px]" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-check-circle text-sm sm:text-base"></i>
                        <span>Apply Theme</span>
                    </button>
                    <button id="cancelThemeBtn" class="w-full sm:w-auto px-4 sm:px-5 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-medium rounded-lg transition-colors duration-200 hidden border border-gray-300 min-h-[44px]">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Single Chat Theme Settings Modal -->
    <div id="singleChatThemeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm p-2 sm:p-4" style="background: rgba(0, 0, 0, 0.5);">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl max-h-[95vh] sm:max-h-[92vh] flex flex-col border border-gray-200 overflow-hidden animate-fade-in">
            <!-- Header with brand color -->
            <div class="relative p-3 sm:p-5 flex items-center justify-between" style="background: #055498;">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-palette text-white text-sm sm:text-base"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white">Chat Theme</h3>
                </div>
                <button id="closeSingleChatThemeModal" class="w-8 h-8 min-w-[32px] min-h-[32px] bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition-colors duration-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 bg-gray-50">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Header Section -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                                <i class="fas fa-palette text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-white">Chat Theme</h4>
                                <p class="text-xs text-white/80 mt-0.5">Customize the visual appearance of this conversation</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 leading-relaxed">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Select a theme to personalize this conversation. Changes apply to this chat and persist across sessions.
                            </p>
                        </div>
                        
                        <!-- Two Column Layout: Theme Selection and Preview -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Left Column: Theme Selection -->
                            <div class="lg:border-r lg:border-gray-200 lg:pr-4 xl:pr-6">
                                <div class="mb-4">
                                    <h5 class="text-base font-bold text-gray-800 mb-1 flex items-center gap-2">
                                        <i class="fas fa-palette text-sm text-blue-600"></i>
                                        Available Themes
                                    </h5>
                                    <p class="text-xs text-gray-500">Choose a theme to customize your conversation</p>
                                </div>
                                <div id="singleChatThemeSelectionGrid" class="space-y-3 max-h-[300px] sm:max-h-[400px] lg:max-h-[500px] overflow-y-auto pr-2 custom-scrollbar" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
                                    <div class="text-center py-12 text-gray-400">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                                        <p class="text-sm">Loading themes...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column: Theme Preview -->
                            <div class="lg:pl-4 xl:pl-6">
                                <div id="singleChatThemePreviewSection" class="p-3 sm:p-4 md:p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-inner min-h-[250px] sm:min-h-[300px] lg:min-h-[400px]">
                                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-eye text-blue-500 text-xs sm:text-sm"></i>
                                            <span class="text-xs sm:text-sm font-semibold text-gray-700">Live Preview</span>
                                        </div>
                                    </div>
                                    <div id="singleChatThemePreviewContainer" class="space-y-3 bg-white rounded-lg p-3 sm:p-4 shadow-sm min-h-[200px] sm:min-h-[250px] lg:min-h-[350px]">
                                        <div class="text-center py-16 text-gray-400">
                                            <i class="fas fa-mouse-pointer text-3xl mb-3"></i>
                                            <p class="text-sm font-medium">Select a theme to see preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-3 sm:p-4 bg-white border-t border-gray-200">
                <!-- Default Footer -->
                <div id="singleChatThemeDefaultFooter" class="flex justify-end">
                    <button id="closeSingleChatThemeBtn" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-medium rounded-lg transition-colors duration-200 min-h-[44px]">
                        Close
                    </button>
                </div>
                
                <!-- Theme Tab Footer -->
                <div id="singleChatThemeModalFooter" class="hidden flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button id="applySingleChatThemeBtn" class="flex-1 px-4 sm:px-5 py-2.5 sm:py-3 text-white text-sm sm:text-base font-semibold rounded-lg transition-all duration-200 hover:opacity-90 hover:shadow-lg flex items-center justify-center gap-2 hidden min-h-[44px]" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-check-circle text-sm sm:text-base"></i>
                        <span>Apply Theme</span>
                    </button>
                    <button id="cancelSingleChatThemeBtn" class="w-full sm:w-auto px-4 sm:px-5 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-medium rounded-lg transition-colors duration-200 hidden border border-gray-300 min-h-[44px]">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.98) translateY(-5px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .animate-fade-in {
            animation: fade-in 0.2s ease-out;
        }
        #groupSettingsNameInput:focus,
        #groupSettingsDescriptionInput:focus {
            border-color: #055498 !important;
            box-shadow: 0 0 0 1px rgba(5, 84, 152, 0.2) !important;
        }
        
        /* Group Settings Modal - Ensure buttons match user side exactly */
        #groupSettingsModal #changeGroupAvatarBtn,
        #groupSettingsModal #saveGroupInfoBtn,
        #groupSettingsModal #addMembersBtn {
            background: #055498 !important;
            color: white !important;
        }
        
        #groupSettingsModal #changeGroupAvatarBtn:hover,
        #groupSettingsModal #saveGroupInfoBtn:hover,
        #groupSettingsModal #addMembersBtn:hover {
            background: #055498 !important;
            opacity: 0.9 !important;
            color: white !important;
        }
        
        #groupSettingsModal #removeGroupAvatarBtn {
            background-color: #6b7280 !important;
            color: white !important;
        }
        
        #groupSettingsModal #removeGroupAvatarBtn:hover {
            background-color: #4b5563 !important;
            color: white !important;
        }
        
        #groupSettingsModal .toggle-admin-btn,
        #groupSettingsModal .remove-member-btn {
            color: white !important;
            cursor: pointer !important;
            pointer-events: auto !important;
        }
        
        #groupSettingsModal .toggle-admin-btn:hover,
        #groupSettingsModal .remove-member-btn:hover {
            opacity: 0.9 !important;
            color: white !important;
        }
        
        #groupSettingsModal .toggle-admin-btn i,
        #groupSettingsModal .remove-member-btn i {
            pointer-events: none !important;
        }
        
        /* SweetAlert2 button text color - white */
        .swal2-confirm,
        .swal2-cancel,
        .swal2-deny {
            color: white !important;
        }

     
        .swal2-confirm {
            background-color: #055498 !important;
        }
        
        .swal2-confirm:hover {
            background-color: #123a60 !important;
        }
        
        .swal2-cancel {
            background-color: #6b7280 !important;
        }
        
        .swal2-cancel:hover {
            background-color: #4b5563 !important;
        }
        
        /* Responsive styles for Group Settings Modal */
        @media (max-width: 640px) {
            #groupSettingsModal {
                padding: 0.5rem !important;
            }
            
            #groupSettingsModal > div {
                max-height: 98vh !important;
                border-radius: 0.5rem !important;
            }
            
            #groupSettingsModal .bg-white {
                padding: 0.75rem !important;
            }
            
            #groupSettingsModal .space-y-2 > div {
                padding: 0.75rem !important;
            }
            
            /* Make member cards stack better on mobile */
            #groupMembersList .flex {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            /* Adjust theme preview on mobile */
            #themePreviewSection {
                min-height: 200px !important;
            }
            
            #themePreviewContainer {
                min-height: 150px !important;
            }
        }
        
        @media (min-width: 641px) and (max-width: 1024px) {
            #groupSettingsModal {
                padding: 1rem !important;
            }
            
            #groupSettingsModal > div {
                max-width: 90% !important;
            }
        }
        
        .swal2-deny {
            background-color: #CE2028 !important;
        }
        
        .swal2-deny:hover {
            background-color: #a01a20 !important;
        }
    </style>
    
    <!-- Create Group Modal -->
    <div id="createGroupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-opacity-20 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Create Group Chat</h3>
                <button id="closeGroupModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-y-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Group Name</label>
                    <input type="text" id="groupNameInput" placeholder="Enter group name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="groupDescriptionInput" placeholder="Enter group description..." rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selected Members</label>
                    <div id="selectedMembersList" class="space-y-2 max-h-48 overflow-y-auto">
                        <!-- Selected members will be listed here -->
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button id="cancelGroupBtn" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>
                <button id="confirmGroupBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                    Create Group
                </button>
            </div>
        </div>
    </div>
    
    <!-- Full Screen Image Viewer Modal -->
    <div id="imageViewerModal" class="fixed inset-0 bg-black bg-opacity-90 z-[200] hidden flex items-center justify-center">
        <div class="relative w-full h-full flex items-center justify-center p-4 overflow-hidden">
            <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
                <div class="flex items-center gap-1 bg-black bg-opacity-60 hover:bg-opacity-70 rounded-lg p-1 backdrop-blur-sm">
                    <button id="zoomOutBtn" class="w-8 h-8 bg-black bg-opacity-40 hover:bg-opacity-60 rounded flex items-center justify-center text-white transition" title="Zoom Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                        </svg>
                    </button>
                    <button id="resetZoomBtn" class="w-8 h-8 bg-black bg-opacity-40 hover:bg-opacity-60 rounded flex items-center justify-center text-white transition text-xs font-semibold" title="Reset Zoom">100%</button>
                    <button id="zoomInBtn" class="w-8 h-8 bg-black bg-opacity-40 hover:bg-opacity-60 rounded flex items-center justify-center text-white transition" title="Zoom In">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </button>
                </div>
                <button id="downloadImageViewer" class="w-10 h-10 bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full flex items-center justify-center text-white transition backdrop-blur-sm" title="Download Image">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </button>
                <button id="closeImageViewer" class="w-10 h-10 bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full flex items-center justify-center text-white transition backdrop-blur-sm" title="Close">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="imageViewerContainer" class="w-full h-full flex items-center justify-center overflow-hidden" style="cursor: grab;">
                <img id="viewerImage" src="" alt="Full view" class="object-contain rounded-lg transition-transform duration-200" style="transform-origin: center center; max-width: 100vw; max-height: 100vh;">
            </div>
        </div>
    </div>

    <script>
        // Chat popup container is not included on messages page
        
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        const currentUserId = @json(Auth::id());
        let currentChatUserId = null;
        let pollingInterval = null;
        let attachedFiles = [];
        let replyToMessageId = null;
        let lastMessageTimestamp = null;
        let isScrollingToParent = false; // Flag to prevent auto-scroll when scrolling to parent message
        let userHasScrolledUp = false; // Track if user has manually scrolled up
        let lastSentMessageId = null; // Track the last sent message ID for "Seen" indicator
        let conversationsData = []; // Store conversations data globally
        let conversationListRefreshInterval = null; // Interval for periodic conversation list refresh
        
        // Voice recording variables
        let mediaRecorder = null;
        let recordedChunks = [];
        let voiceRecordingBlob = null;
        let voiceRecordingUrl = null;
        let voiceTimerInterval = null;
        let voiceSeconds = 0;
        let voiceAutoSend = false;
        
        // Get current user profile picture
        let currentUserProfilePicture = null;
        @php
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->profile_picture) {
                $currentUserMedia = \App\Models\MediaLibrary::find($currentUser->profile_picture);
                if ($currentUserMedia) {
                    $currentUserProfilePicUrl = asset('storage/' . $currentUserMedia->file_path);
                } else {
                    $currentUserProfilePicUrl = null;
                }
            } else {
                $currentUserProfilePicUrl = null;
            }
        @endphp
        @if($currentUserProfilePicUrl ?? null)
            currentUserProfilePicture = @json($currentUserProfilePicUrl);
        @endif
        
        const currentUserInitials = '{{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}';
        
        // User data mapping for chat users
        const userData = {};
        
        // Function to make action buttons visible on mobile
        function makeActionButtonsVisible() {
            if (window.innerWidth <= 767) {
                const actionButtonContainers = document.querySelectorAll('.opacity-0.group-hover\\:opacity-100, .opacity-0');
                actionButtonContainers.forEach(container => {
                    container.classList.remove('opacity-0');
                    container.style.opacity = '1';
                    container.style.visibility = 'visible';
                });
                // Also ensure individual buttons are visible
                const actionButtons = document.querySelectorAll('.message-react-btn, .message-reply-btn, .message-delete-btn');
                actionButtons.forEach(btn => {
                    btn.style.opacity = '1';
                    btn.style.visibility = 'visible';
                });
            }
        }

        // Function to remove dark classes from elements
        function removeDarkClasses(element) {
            if (!element) return;
            
            // Remove dark class from the element itself
            element.classList.remove('dark');
            
            // Remove prefixed classes by finding and replacing them
            const allElements = element.querySelectorAll ? element.querySelectorAll('*') : [];
            [element, ...Array.from(allElements)].forEach(el => {
                // Get all classes
                if (el.className && typeof el.className === 'string') {
                    // Remove classes that contain 'dark:' (Tailwind dark mode classes)
                    const classes = el.className.split(' ').filter(cls => !cls.includes('dark:')).join(' ');
                    if (classes !== el.className) {
                        el.className = classes;
                    }
                } else if (el.classList) {
                    // Use classList API if available
                    const classesToRemove = [];
                    el.classList.forEach(cls => {
                        if (cls.includes('dark:')) {
                            classesToRemove.push(cls);
                        }
                    });
                    classesToRemove.forEach(cls => el.classList.remove(cls));
                }
            });
        }

        // Load conversations on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure light mode is maintained (redundant check)
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
            }
            localStorage.setItem('theme', 'light');
            
            // Remove dark classes from main containers
            removeDarkClasses(document.getElementById('conversationsList'));
            removeDarkClasses(document.getElementById('chatArea'));
            removeDarkClasses(document.getElementById('activeChat'));
            removeDarkClasses(document.getElementById('chatMessagesArea'));
            
            loadConversations();
            setupEventListeners();
            
            // Check if URL has #new-message hash to auto-open new message modal
            if (window.location.hash === '#new-message') {
                // Remove hash from URL
                window.history.replaceState(null, null, window.location.pathname);
                // Wait a bit for page to fully load, then trigger new message button
                setTimeout(function() {
                    const newMessageBtn = document.getElementById('newMessageBtn');
                    if (newMessageBtn) {
                        newMessageBtn.click();
                    } else {
                        // Fallback: try to open user selection modal directly
                        const userModal = document.getElementById('userSelectionModal');
                        if (userModal && typeof loadUsersForSelection === 'function') {
                            if (typeof resetUserSelectionModal === 'function') {
                                resetUserSelectionModal();
                            }
                            userModal.classList.remove('hidden');
                            loadUsersForSelection();
                        }
                    }
                }, 500);
            }
            
            // Start the periodic refresh for conversation list (function is defined globally below)
            startConversationListRefresh();
            
            // Also refresh when page becomes visible (user switches back to tab)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    // Page is visible, refresh conversation list
                    updateUnreadCounts();
                }
            });
            
            // Ensure message form is visible on page load
            const messageForm = document.getElementById('messageForm');
            const messageInputContainer = messageForm?.closest('div');
            if (messageForm) {
                messageForm.classList.remove('hidden');
                messageForm.style.display = 'flex';
                messageForm.style.visibility = 'visible';
            }
            if (messageInputContainer) {
                messageInputContainer.style.display = 'block';
                messageInputContainer.style.visibility = 'visible';
            }
            
            // Make action buttons visible on mobile on page load
            setTimeout(makeActionButtonsVisible, 500);
            
            // Handle window resize for responsive behavior
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    // On desktop/tablet, ensure both are visible and header is shown
                    if (window.innerWidth > 767) {
                        const conversationsList = document.getElementById('conversationsList');
                        const chatArea = document.getElementById('chatArea');
                        if (conversationsList) {
                            conversationsList.classList.remove('mobile-hidden');
                        }
                        if (chatArea) {
                            chatArea.classList.remove('mobile-visible');
                            if (!chatArea.classList.contains('lg:flex')) {
                                chatArea.classList.add('lg:flex');
                            }
                        }
                        // Always show header on desktop
                        document.body.classList.remove('header-hidden-mobile');
                    } else {
                        // On mobile, ensure action buttons are visible
                        makeActionButtonsVisible();
                    }
                }, 250);
            });
        });

        // Function to reset user selection modal state (global scope)
        function resetUserSelectionModal() {
            // Reset flags
            window.addingMembersToGroup = false;
            window.targetGroupId = null;
            
            // Reset selected users
            selectedUsers = [];
            
            // Reset checkboxes
            const container = document.getElementById('usersListContainer');
            if (container) {
                container.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            }
            const selectAllCheckbox = document.getElementById('selectAllUsers');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            
            // Reset button states - hide both buttons initially
            const createGroupBtn = document.getElementById('createGroupBtn');
            const openChatBtn = document.getElementById('openChatBtn');
            if (createGroupBtn) {
                createGroupBtn.innerHTML = '<i class="fas fa-users mr-2"></i>Create Group';
                createGroupBtn.classList.add('hidden');
            }
            if (openChatBtn) {
                openChatBtn.classList.add('hidden');
            }
            
            // Reset selected count
            const selectedCount = document.getElementById('selectedCount');
            if (selectedCount) {
                selectedCount.textContent = '0';
            }
            
            // Clear search input
            const userSearchInput = document.getElementById('userSearchInput');
            if (userSearchInput) {
                userSearchInput.value = '';
            }
        }

        function setupEventListeners() {
            // New message button
            const newMessageBtn = document.getElementById('newMessageBtn');
            if (newMessageBtn) {
                newMessageBtn.addEventListener('click', function() {
                    // Reset modal state before opening
                    resetUserSelectionModal();
                    
                    const userModal = document.getElementById('userSelectionModal');
                    if (userModal) {
                        userModal.classList.remove('hidden');
                    }
                    loadUsersForSelection();
                });
            }

            // Close modals
            const closeUserModal = document.getElementById('closeUserModal');
            if (closeUserModal) {
                closeUserModal.addEventListener('click', function() {
                    const userModal = document.getElementById('userSelectionModal');
                    if (userModal) {
                        userModal.classList.add('hidden');
                        
                        // If opened from Group Settings, reopen Group Settings modal
                        if (window.addingMembersToGroup) {
                            const groupSettingsModal = document.getElementById('groupSettingsModal');
                            if (groupSettingsModal) {
                                groupSettingsModal.classList.remove('hidden');
                            }
                        }
                        
                        resetUserSelectionModal();
                    }
                });
            }

            // Click outside to close modals
            const userSelectionModal = document.getElementById('userSelectionModal');
            if (userSelectionModal) {
                userSelectionModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                        
                        // If opened from Group Settings, reopen Group Settings modal
                        if (window.addingMembersToGroup) {
                            const groupSettingsModal = document.getElementById('groupSettingsModal');
                            if (groupSettingsModal) {
                                groupSettingsModal.classList.remove('hidden');
                            }
                        }
                        
                        resetUserSelectionModal();
                    }
                });
            }

            // Search conversations
            const conversationsSearch = document.getElementById('conversationsSearch');
            if (conversationsSearch) {
                conversationsSearch.addEventListener('input', function() {
                    filterConversations(this.value);
                });
            }

            // Message form
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    sendMessage();
                });
            }

            // Message input - mark messages as seen when focused/clicked
            const messageInput = document.getElementById('messageInput');
            if (messageInput) {
                messageInput.addEventListener('focus', function() {
                    markMessagesAsSeenOnInputFocus();
                });
                messageInput.addEventListener('click', function() {
                    markMessagesAsSeenOnInputFocus();
                });
            }

            // Attach button
            const attachBtn = document.getElementById('attachBtn');
            if (attachBtn) {
                attachBtn.addEventListener('click', function() {
                    const fileInput = document.getElementById('fileInput');
                    if (fileInput) {
                        fileInput.click();
                    }
                });
            }

            // File input
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    handleFileSelect(e.target.files);
                });
            }

            // Emoji button - show popup beside button
            const emojiBtn = document.getElementById('emojiBtn');
            if (emojiBtn) {
                emojiBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const emojiPicker = document.getElementById('emojiPickerPopup');
                
                // Remove any existing picker
                const existingPicker = document.querySelector('.emoji-picker-popup');
                if (existingPicker && existingPicker !== emojiPicker) {
                    existingPicker.remove();
                }
                
                // Toggle picker
                if (emojiPicker.classList.contains('hidden')) {
                    // Show picker
                    emojiPicker.classList.remove('hidden');
                    
                    // Position picker beside button
                    const buttonRect = emojiBtn.getBoundingClientRect();
                    const pickerWidth = 320;
                    const pickerHeight = 300;
                    
                    // Position above the button, aligned to the right
                    let top = buttonRect.top - pickerHeight - 8; // 8px gap above button
                    let left = buttonRect.right - pickerWidth; // Align right edge with button right edge
                    
                    // Adjust if picker goes off screen
                    if (top < 8) {
                        // If not enough space above, show below
                        top = buttonRect.bottom + 8;
                    }
                    if (left < 8) {
                        left = 8;
                    }
                    if (left + pickerWidth > window.innerWidth - 8) {
                        left = window.innerWidth - pickerWidth - 8;
                    }
                    
                    emojiPicker.style.top = `${top}px`;
                    emojiPicker.style.left = `${left}px`;
                    
                    // Setup emoji picker functionality
                    setupEmojiPicker(emojiPicker);
                } else {
                    // Hide picker
                    emojiPicker.classList.add('hidden');
                }
            });
            
            // Close emoji picker when clicking outside
            document.addEventListener('click', function(e) {
                const emojiPicker = document.getElementById('emojiPickerPopup');
                const emojiBtn = document.getElementById('emojiBtn');
                if (emojiPicker && !emojiPicker.contains(e.target) && e.target !== emojiBtn && !emojiBtn.contains(e.target)) {
                    emojiPicker.classList.add('hidden');
                }
            });
            
            // Event delegation for reply indicators (handles dynamically added elements)
            const messagesArea = document.getElementById('chatMessagesArea');
            if (messagesArea) {
                messagesArea.addEventListener('click', function(e) {
                    const replyIndicator = e.target.closest('.reply-to-message');
                    if (replyIndicator) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        const parentId = replyIndicator.getAttribute('data-parent-id');
                        if (parentId) {
                            scrollToParentMessage(parseInt(parentId));
                        }
                        return false;
                    }
                });
            }
            
            // Back to conversations button (mobile)
            const backToConversations = document.getElementById('backToConversations');
            if (backToConversations) {
                backToConversations.addEventListener('click', function() {
                    const conversationsList = document.getElementById('conversationsList');
                    const chatArea = document.getElementById('chatArea');
                    const activeChat = document.getElementById('activeChat');
                    const chatEmptyState = document.getElementById('chatEmptyState');
                    
                    if (conversationsList) {
                        conversationsList.classList.remove('mobile-hidden');
                    }
                    if (chatArea) {
                        chatArea.classList.remove('mobile-visible');
                        if (window.innerWidth <= 767) {
                            chatArea.classList.add('hidden');
                        }
                    }
                    if (activeChat) {
                        activeChat.classList.add('hidden');
                    }
                    if (chatEmptyState) {
                        chatEmptyState.classList.remove('hidden');
                    }
                    
                    // Ensure message input is hidden when no chat is selected
                    const messageForm = document.getElementById('messageForm');
                    const messageInputContainer = messageForm?.closest('div');
                    if (messageInputContainer) {
                        messageInputContainer.style.display = 'none';
                    }
                    
                    // Show header again when going back to conversations on mobile
                    if (window.innerWidth <= 767) {
                        document.body.classList.remove('header-hidden-mobile');
                    }
                    
                    currentChatUserId = null;
                });
            }
            
            // Cancel reply button
            const cancelReplyBtn = document.getElementById('cancelReplyBtn');
            if (cancelReplyBtn) {
                cancelReplyBtn.addEventListener('click', function() {
                    replyToMessageId = null;
                    const replyIndicator = document.getElementById('replyIndicator');
                    if (replyIndicator) {
                        replyIndicator.classList.add('hidden');
                    }
                    
                    // Restore margin-top when reply is closed
                    const messageInputContainer = replyIndicator?.closest('.flex-shrink-0');
                    if (messageInputContainer) {
                        messageInputContainer.style.removeProperty('margin-top');
                    }
                    
                    // Restore messages area padding when reply indicator is hidden
                    const messagesArea = document.getElementById('chatMessagesArea');
                    if (messagesArea) {
                        // Reset to original padding (check mobile padding)
                        const isMobile = window.innerWidth <= 640;
                        messagesArea.style.paddingBottom = isMobile ? '10px' : '';
                    }
                });
            }
            
            // Voice recording functionality
            setupVoiceRecording();
        }
        
        // Voice recording functions
        function resetVoiceState() {
            if (voiceTimerInterval) {
                clearInterval(voiceTimerInterval);
                voiceTimerInterval = null;
            }
            voiceSeconds = 0;
            const voiceTimer = document.getElementById('voiceTimer');
            if (voiceTimer) {
                voiceTimer.textContent = '0:00';
            }
            voiceRecordingBlob = null;
            // Ensure message form is visible after resetting voice state
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.classList.remove('hidden');
                messageForm.style.display = 'flex';
                messageForm.style.visibility = 'visible';
            }
            if (voiceRecordingUrl) {
                URL.revokeObjectURL(voiceRecordingUrl);
                voiceRecordingUrl = null;
            }
        }
        
        function formatVoiceTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s.toString().padStart(2, '0')}`;
        }
        
        function setupVoiceRecording() {
            const voiceBtn = document.getElementById('voiceBtn');
            const voiceRecorder = document.getElementById('voiceRecorder');
            const voiceTimer = document.getElementById('voiceTimer');
            const voiceCancelBtn = document.getElementById('voiceCancelBtn');
            const voiceStopBtn = document.getElementById('voiceStopBtn');
            const voiceSendBtn = document.getElementById('voiceSendBtn');
            const messageForm = document.getElementById('messageForm');
            
            if (!voiceBtn || !voiceRecorder || !voiceTimer) return;
            
            // Start recording
            voiceBtn.addEventListener('click', async function() {
                try {
                    // If already recording, ignore
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        return;
                    }
                    
                    // Helper function to setup MediaRecorder
                    const setupRecorder = function(stream) {
                        recordedChunks = [];
                        resetVoiceState();
                        
                        // Detect supported MIME type for mobile devices
                        let mimeType = 'audio/webm';
                        if (MediaRecorder.isTypeSupported('audio/mp4')) {
                            mimeType = 'audio/mp4';
                        } else if (MediaRecorder.isTypeSupported('audio/mpeg')) {
                            mimeType = 'audio/mpeg';
                        } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
                            mimeType = 'audio/ogg';
                        } else if (MediaRecorder.isTypeSupported('audio/webm')) {
                            mimeType = 'audio/webm';
                        }
                        
                        // Create MediaRecorder with options
                        const options = { mimeType: mimeType };
                        if (MediaRecorder.isTypeSupported(mimeType)) {
                            mediaRecorder = new MediaRecorder(stream, options);
                        } else {
                            // Fallback: let browser choose
                            mediaRecorder = new MediaRecorder(stream);
                        }
                        
                        mediaRecorder.ondataavailable = function(e) {
                            if (e.data && e.data.size > 0) {
                                recordedChunks.push(e.data);
                            }
                        };
                        
                        mediaRecorder.onstop = function() {
                            // Stop all tracks
                            stream.getTracks().forEach(t => t.stop());
                            
                            if (recordedChunks.length === 0) {
                                resetVoiceState();
                                return;
                            }
                            
                            // Determine blob type based on what was actually recorded
                            const blobType = mediaRecorder.mimeType || 'audio/webm';
                            voiceRecordingBlob = new Blob(recordedChunks, { type: blobType });
                            voiceRecordingUrl = URL.createObjectURL(voiceRecordingBlob);
                            
                            // If user pressed Send while recording, auto-send after stop
                            if (voiceAutoSend && voiceRecordingBlob && voiceRecordingUrl) {
                                sendVoiceMessage();
                                voiceAutoSend = false;
                            }
                        };
                        
                        mediaRecorder.start();
                        
                        // Show recorder UI, hide normal form
                        voiceRecorder.classList.remove('hidden');
                        if (messageForm) {
                            messageForm.classList.add('hidden');
                        }
                        
                        // Start timer
                        voiceTimerInterval = setInterval(function() {
                            voiceSeconds++;
                            if (voiceTimer) {
                                voiceTimer.textContent = formatVoiceTime(voiceSeconds);
                            }
                        }, 1000);
                    };
                    
                    // Try with enhanced audio settings first
                    let stream;
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ 
                            audio: {
                                echoCancellation: true,
                                noiseSuppression: true,
                                autoGainControl: true
                            } 
                        });
                        setupRecorder(stream);
                    } catch (constraintErr) {
                        // If enhanced settings fail, try with basic audio
                        if (constraintErr.name === 'OverconstrainedError' || constraintErr.name === 'ConstraintNotSatisfiedError') {
                            stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                            setupRecorder(stream);
                        } else {
                            throw constraintErr;
                        }
                    }
                    
                } catch (err) {
                    console.error('Voice recording error:', err);
                    let errorMessage = 'Unable to access microphone. Please check your browser permissions.';
                    
                    // More specific error messages
                    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                        errorMessage = 'Microphone permission denied. Please allow microphone access in your browser settings.';
                    } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                        errorMessage = 'No microphone found. Please connect a microphone and try again.';
                    } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                        errorMessage = 'Microphone is being used by another application. Please close it and try again.';
                    }
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Microphone Error',
                            text: errorMessage,
                        });
                    } else {
                        alert(errorMessage);
                    }
                }
            });
            
            // Stop recording button
            if (voiceStopBtn) {
                voiceStopBtn.addEventListener('click', function() {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        if (voiceTimerInterval) {
                            clearInterval(voiceTimerInterval);
                            voiceTimerInterval = null;
                        }
                    }
                });
            }
            
            // Cancel recording
            if (voiceCancelBtn) {
                voiceCancelBtn.addEventListener('click', function() {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                    resetVoiceState();
                    if (voiceRecorder) {
                        voiceRecorder.classList.add('hidden');
                    }
                    if (messageForm) {
                        messageForm.classList.remove('hidden');
                    }
                });
            }
            
            // Send voice message
            if (voiceSendBtn) {
                voiceSendBtn.addEventListener('click', function() {
                    // If still recording, stop first and mark for auto-send
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        voiceAutoSend = true;
                        mediaRecorder.stop();
                        if (voiceTimerInterval) {
                            clearInterval(voiceTimerInterval);
                            voiceTimerInterval = null;
                        }
                        return;
                    }
                    
                    // If already recorded, send immediately
                    if (voiceRecordingBlob && voiceRecordingUrl) {
                        sendVoiceMessage();
                    }
                });
            }
        }
        
        // Send voice message to server
        function sendVoiceMessage() {
            if (!voiceRecordingBlob || !voiceRecordingUrl || !currentChatUserId || currentChatUserId.trim() === '') {
                console.error('Invalid voice message data or chat user');
                return;
            }
            
            const voiceSendBtn = document.getElementById('voiceSendBtn');
            const voiceRecorder = document.getElementById('voiceRecorder');
            const messageForm = document.getElementById('messageForm');
            
            // Disable send button
            if (voiceSendBtn) {
                voiceSendBtn.disabled = true;
                voiceSendBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
            }
            
            // Get duration
            const duration = formatVoiceTime(voiceSeconds || Math.max(1, Math.round(voiceRecordingBlob.size / 16000)));
            
            // Always save as MP3 format
            // Note: Browser MediaRecorder may record as webm/mp4, but we'll save with MP3 extension
            // Server should handle conversion if needed
            const fileName = `voice-message-${Date.now()}-${duration.replace(':', '-')}.mp3`;
            const voiceFile = new File([voiceRecordingBlob], fileName, { type: 'audio/mpeg' });
            
            // Create FormData for voice message
            const formData = new FormData();
            
            // Check if this is a group chat
            const isGroup = currentChatUserId?.startsWith('group_');
            if (isGroup) {
                const groupId = currentChatUserId.replace('group_', '').trim();
                if (!groupId) {
                    console.error('Invalid group ID for voice message');
                    resetVoiceState();
                    return;
                }
                formData.append('group_id', groupId);
            } else {
                const receiverId = currentChatUserId?.trim();
                if (!receiverId) {
                    console.error('Invalid receiver ID for voice message');
                    resetVoiceState();
                    return;
                }
                formData.append('receiver_id', receiverId);
            }
            
            formData.append('voice_duration', duration);
            formData.append('attachments[]', voiceFile);
            formData.append('convert_to_mp3', 'true'); // Flag for server to convert if needed
            
            // Send voice message to server
            axios.post('{{ route("messages.send") }}', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then(response => {
                if (response.data.success) {
                    // Reset voice state
                    resetVoiceState();
                    if (voiceRecorder) {
                        voiceRecorder.classList.add('hidden');
                    }
                    if (messageForm) {
                        messageForm.classList.remove('hidden');
                    }
                    
                    // Reload conversation to show new message
                    if (currentChatUserId) {
                        loadChatConversation(currentChatUserId);
                    }
                }
            })
            .catch(error => {
                console.error('Error sending voice message:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to send voice message. Please try again.'
                    });
                }
            })
            .finally(() => {
                // Re-enable send button
                if (voiceSendBtn) {
                    voiceSendBtn.disabled = false;
                    voiceSendBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3.4 20.4L5 14 14 12 5 10 3.4 3.6 21 12 3.4 20.4Z"></path>
                        </svg>
                    `;
                }
            });
        }
        
        // Setup emoji picker functionality
        function setupEmojiPicker(emojiPicker) {
                if (!emojiPicker) return;
                
                // Emoji search functionality
                const emojiSearchInput = emojiPicker.querySelector('.emoji-search-input');
                if (emojiSearchInput) {
                    const emojiKeywords = {
                        'smile': '😀😃😄😁😆😅😂🤣☺️😊😇🙂🙃😉😌😍',
                        'happy': '😀😃😄😁😆😅😂🤣☺️😊😇🙂🙃😉😌😍🥰',
                        'sad': '😞😔😟😕🙁☹️😣😖😫😩🥺😢😭',
                        'love': '❤️🧡💛💚💙💜🖤🤍🤎💔❣️💕💞💓💗💖💘💝💟😍🥰😘',
                        'angry': '😠😡🤬🤯😤',
                        'wow': '😮😲😯😦😧🤯',
                        'hand': '👋🤚🖐️✋🖖👌🤌🤏✌️🤞🤟🤘🤙👈👉👆🖕👇☝️👍👎✊👊🤛🤜👏🙌👐🤲🤝🙏',
                        'wave': '👋',
                        'dog': '🐶',
                        'cat': '🐱',
                        'pizza': '🍕',
                        'food': '🍕🍔🍟🌭🍿🧂🥓🥚🍳🥘🥗🍱🍘🍙🍚🍛🍜🍝🍠🍢🍣🍤🍥🥮🍡🥟🥠🥡',
                        'soccer': '⚽',
                        'ball': '⚽🏀🏈⚾🥎🎾🏐🏉🥏🎱🏓🏸🏒🏑🥍🏏',
                        'car': '🚗',
                        'vehicle': '🚗🚕🚙🚌🚎🏎️🚓🚑🚒🚐🛻🚚🚛🚜',
                        'light': '💡',
                        'bulb': '💡',
                        'heart': '❤️🧡💛💚💙💜🖤🤍🤎💔❣️💕💞💓💗💖💘💝💟',
                        'thumbs': '👍👎',
                        'ok': '👌',
                        'fire': '🔥',
                        'star': '⭐🌟',
                        'party': '🎉🎊🥳',
                        'birthday': '🎂🎉🎊🥳',
                        'cake': '🎂',
                        'coffee': '☕',
                        'drink': '☕🫖🍵🍶🍾🍷🍸🍹🍺🍻🥂🥃🥤🧋🧃🧉🧊',
                        'money': '💰💵💴💶💷💳',
                        'clock': '🕛🕧🕐🕜🕑🕝🕒🕞🕓🕟🕔🕠🕕🕡🕖🕢🕗🕣🕘🕤🕙🕥🕚🕦',
                        'time': '🕛🕧🕐🕜🕑🕝🕒🕞🕓🕟🕔🕠🕕🕡🕖🕢🕗🕣🕘🕤🕙🕥🕚🕦⏰⏲️⏱️⌛⏳⌚',
                    };
                    
                    emojiSearchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        const allEmojiItems = emojiPicker.querySelectorAll('.emoji-item');
                        const allCategories = emojiPicker.querySelectorAll('.emoji-category');
                        
                        if (searchTerm === '') {
                            allCategories.forEach(cat => {
                                cat.querySelectorAll('.emoji-item').forEach(item => {
                                    item.style.display = '';
                                });
                            });
                            const activeCategory = emojiPicker.querySelector('.emoji-category.active');
                            if (activeCategory) {
                                allCategories.forEach(cat => cat.classList.add('hidden'));
                                activeCategory.classList.remove('hidden');
                            }
                        } else {
                            allCategories.forEach(cat => {
                                cat.classList.remove('hidden');
                                const items = cat.querySelectorAll('.emoji-item');
                                items.forEach(item => {
                                    item.style.display = 'none';
                                });
                            });
                            
                            let foundCount = 0;
                            allEmojiItems.forEach(item => {
                                const emoji = item.getAttribute('data-emoji');
                                let shouldShow = false;
                                
                                for (const [keyword, emojiList] of Object.entries(emojiKeywords)) {
                                    if (keyword.includes(searchTerm) || searchTerm.includes(keyword)) {
                                        if (emojiList.includes(emoji)) {
                                            shouldShow = true;
                                            break;
                                        }
                                    }
                                }
                                
                                if (emoji.toLowerCase().includes(searchTerm) || searchTerm.includes(emoji)) {
                                    shouldShow = true;
                                }
                                
                                if (shouldShow) {
                                    item.style.display = '';
                                    foundCount++;
                                }
                            });
                        }
                    });
                }

                // Emoji selection
                const emojiItems = emojiPicker.querySelectorAll('.emoji-item');
                emojiItems.forEach(item => {
                    item.addEventListener('click', function() {
                        const emoji = this.getAttribute('data-emoji');
                        const input = document.getElementById('messageInput');
                        if (input) {
                            input.value += emoji;
                            input.focus();
                        }
                        if (emojiSearchInput) {
                            emojiSearchInput.value = '';
                            emojiSearchInput.dispatchEvent(new Event('input'));
                        }
                    });
                });

                // Emoji category switching
                const categoryBtns = emojiPicker.querySelectorAll('.emoji-category-btn');
                const categoryDivs = emojiPicker.querySelectorAll('.emoji-category');
                
                categoryBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const category = this.getAttribute('data-category');
                        
                        categoryBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        categoryDivs.forEach(div => {
                            if (div.getAttribute('data-category') === category) {
                                div.classList.remove('hidden');
                                div.classList.add('active');
                            } else {
                                div.classList.add('hidden');
                                div.classList.remove('active');
                            }
                        });
                    });
                });
            }

            // Cancel reply
            document.getElementById('cancelReplyBtn').addEventListener('click', function() {
                replyToMessageId = null;
                const replyIndicator = document.getElementById('replyIndicator');
                if (replyIndicator) {
                    replyIndicator.classList.add('hidden');
                }
                
                // Restore margin-top when reply is closed
                const messageInputContainer = replyIndicator?.closest('.flex-shrink-0');
                if (messageInputContainer) {
                    messageInputContainer.style.marginTop = '0px';
                }
            });
        }

        function loadConversations() {
            axios.get('{{ route("messages.conversations") }}')
                .then(response => {
                    if (response.data.success) {
                        conversationsData = response.data.conversations; // Store globally
                        renderConversations(response.data.conversations);
                        
                        // Calculate and update header badge count on initial load using API
                        if (typeof window.loadAdminUnreadCount === 'function') {
                            window.loadAdminUnreadCount();
                        } else if (typeof loadAdminUnreadCount === 'function') {
                            loadAdminUnreadCount();
                        } else {
                            // Fallback: calculate from conversations
                            let totalUnread = 0;
                            response.data.conversations.forEach(conv => {
                                totalUnread += (conv.unread_count || 0);
                            });
                            
                            if (typeof updateAdminMessagesBadge === 'function') {
                                updateAdminMessagesBadge(totalUnread);
                            } else {
                                // Fallback: direct update if function not available
                                const badgeCount = document.getElementById('adminMessagesBadgeCount');
                                if (badgeCount) {
                                    if (totalUnread > 0) {
                                        badgeCount.textContent = totalUnread > 99 ? '99+' : totalUnread;
                                        badgeCount.classList.remove('hidden');
                                    } else {
                                        badgeCount.classList.add('hidden');
                                    }
                                }
                            }
                        }
                        
                        // Update timestamps periodically
                        updateConversationTimestamps();
                        // Set up interval to update timestamps every minute
                        if (window.conversationTimestampInterval) {
                            clearInterval(window.conversationTimestampInterval);
                        }
                        window.conversationTimestampInterval = setInterval(updateConversationTimestamps, 60000); // Update every minute
                        
                        // No interval - badges will update in real-time when events occur
                    }
                })
                .catch(error => {
                    console.error('Error loading conversations:', error);
                });
        }

        // Function to update conversation timestamps periodically
        function updateConversationTimestamps() {
            const conversationItems = document.querySelectorAll('.conversation-item');
            conversationItems.forEach(item => {
                const userId = item.getAttribute('data-user-id');
                const conv = conversationsData.find(c => c.user_id === userId);
                if (conv && conv.last_message_time) {
                    const timeAgo = getTimeAgo(conv.last_message_time);
                    // Use the specific class to target only the timestamp element
                    const timeElement = item.querySelector('p.conversation-timestamp');
                    if (timeElement) {
                        timeElement.textContent = timeAgo;
                    }
                }
            });
        }

        // Function to update unread counts in real-time
        function updateUnreadCounts() {
            axios.get('{{ route("messages.conversations") }}')
                .then(response => {
                    if (response.data.success && response.data.conversations) {
                        // Update conversationsData and conversation list
                        const container = document.getElementById('conversationsContainer');
                        let hasNewConversations = false;
                        
                        response.data.conversations.forEach(newConv => {
                            const existingConv = conversationsData.find(c => c.user_id === newConv.user_id);
                            const conversationItem = document.querySelector(`.conversation-item[data-user-id="${newConv.user_id}"]`);
                            
                            if (existingConv) {
                                // Update unread count
                                const oldUnreadCount = existingConv.unread_count || 0;
                                const oldLastMessage = existingConv.last_message;
                                const oldLastMessageTime = existingConv.last_message_time;
                                
                                existingConv.unread_count = newConv.unread_count || 0;
                                existingConv.last_message = newConv.last_message || 'No messages yet';
                                existingConv.last_message_time = newConv.last_message_time;
                                // Calculate time ago from last_message_time
                                existingConv.time_ago = existingConv.last_message_time ? getTimeAgo(existingConv.last_message_time) : (newConv.time_ago || '');
                                
                                // Update conversation item in DOM
                                if (conversationItem) {
                                    // Update last message text - be more specific to avoid matching group icon
                                    // Look for the last message paragraph element specifically (not icon, not timestamp)
                                    const messageContainer = conversationItem.querySelector('.flex-1.min-w-0');
                                    if (messageContainer) {
                                        // Find the last message paragraph - it's the <p> tag with text-xs sm:text-sm text-gray-500 classes
                                        // Exclude the timestamp paragraph which has conversation-timestamp class
                                        const allParagraphs = messageContainer.querySelectorAll('p');
                                        let lastMessageEl = null;
                                        for (let p of allParagraphs) {
                                            // Check if it's the last message paragraph (has text-gray-500 but not conversation-timestamp)
                                            if (p.classList.contains('text-gray-500') && 
                                                !p.classList.contains('conversation-timestamp') &&
                                                (p.classList.contains('text-xs') || p.classList.contains('sm:text-sm'))) {
                                                lastMessageEl = p;
                                                break;
                                            }
                                        }
                                        
                                        if (lastMessageEl && existingConv.last_message !== oldLastMessage) {
                                            lastMessageEl.textContent = existingConv.last_message;
                                        }
                                    }
                                    
                                    // Update timestamp - always recalculate to ensure accuracy
                                    const timeAgoEl = conversationItem.querySelector('.conversation-timestamp');
                                    const calculatedTimeAgo = existingConv.last_message_time ? getTimeAgo(existingConv.last_message_time) : '';
                                    if (timeAgoEl) {
                                        // Always update timestamp to reflect current time
                                        if (calculatedTimeAgo) {
                                            timeAgoEl.textContent = calculatedTimeAgo;
                                        } else {
                                            // Remove timestamp if no valid time
                                            timeAgoEl.remove();
                                        }
                                    } else if (calculatedTimeAgo) {
                                        // Create timestamp element if it doesn't exist
                                        const lastMessageContainer = conversationItem.querySelector('.flex-1.min-w-0');
                                        if (lastMessageContainer) {
                                            const newTimeAgoEl = document.createElement('p');
                                            newTimeAgoEl.className = 'text-xs text-gray-400 mt-1 conversation-timestamp';
                                            newTimeAgoEl.textContent = calculatedTimeAgo;
                                            lastMessageContainer.appendChild(newTimeAgoEl);
                                        }
                                    }
                                    
                                    // Update badge
                                    const headerDiv = conversationItem.querySelector('.flex.items-center.justify-between.mb-1');
                                    if (headerDiv) {
                                        // Remove any existing badge first
                                        const existingBadge = headerDiv.querySelector('span.bg-red-500') || 
                                                              headerDiv.querySelector('.bg-red-500') ||
                                                              Array.from(headerDiv.querySelectorAll('span')).find(span => 
                                                                  span.classList.contains('bg-red-500') || 
                                                                  (span.classList.contains('rounded-full') && span.textContent.match(/^\d+$/))
                                                              );
                                        if (existingBadge) {
                                            existingBadge.remove();
                                        }
                                        
                                        // Add new badge if unread count > 0
                                        if (existingConv.unread_count > 0) {
                                            const badgeSpan = document.createElement('span');
                                            badgeSpan.className = 'flex-shrink-0 px-2 py-0.5 bg-red-500 text-white text-xs font-semibold rounded-full min-w-[20px] text-center shadow-sm';
                                            badgeSpan.textContent = existingConv.unread_count;
                                            headerDiv.appendChild(badgeSpan);
                                        }
                                    }
                                }
                            } else {
                                // Add new conversation
                                conversationsData.push(newConv);
                                hasNewConversations = true;
                            }
                        });
                        
                        // Sort conversations by last_message_time (most recent first)
                        conversationsData.sort((a, b) => {
                            const timeA = a.last_message_time ? new Date(a.last_message_time).getTime() : 0;
                            const timeB = b.last_message_time ? new Date(b.last_message_time).getTime() : 0;
                            // If times are equal, maintain order
                            if (timeB === timeA) {
                                return 0;
                            }
                            return timeB - timeA; // Descending order (newest first)
                        });
                        
                        // Re-render conversations in sorted order to ensure consistency
                        renderConversations(conversationsData);
                        
                        // If there are new conversations, reload the full list to render them
                        if (hasNewConversations) {
                            loadConversations();
                        }

                        // Calculate total unread count for conversation list badges
                        let totalUnread = 0;
                        conversationsData.forEach(conv => {
                            totalUnread += (conv.unread_count || 0);
                        });

                        // Update header badge using API for accuracy
                        if (typeof window.loadAdminUnreadCount === 'function') {
                            window.loadAdminUnreadCount();
                        } else if (typeof loadAdminUnreadCount === 'function') {
                            loadAdminUnreadCount();
                        } else if (typeof updateAdminMessagesBadge === 'function') {
                            updateAdminMessagesBadge(totalUnread);
                        } else {
                            // Fallback: direct update if function not available
                            const badgeCount = document.getElementById('adminMessagesBadgeCount');
                            if (badgeCount) {
                                if (totalUnread > 0) {
                                    badgeCount.textContent = totalUnread > 99 ? '99+' : totalUnread;
                                    badgeCount.classList.remove('hidden');
                                } else {
                                    badgeCount.classList.add('hidden');
                                }
                            }
                        }

                        // Update UI badges without re-rendering entire list
                        const conversationItems = document.querySelectorAll('.conversation-item');
                        conversationItems.forEach(item => {
                            const userId = item.getAttribute('data-user-id');
                            const conv = conversationsData.find(c => c.user_id === userId);
                            if (conv) {
                                const unreadCount = conv.unread_count || 0;
                                const headerDiv = item.querySelector('.flex.items-center.justify-between.mb-1');
                                if (headerDiv) {
                                    // Find badge - check multiple possible selectors
                                    let badge = headerDiv.querySelector('span.bg-red-500') || 
                                                headerDiv.querySelector('.bg-red-500') ||
                                                headerDiv.querySelector('span[class*="bg-red"]') ||
                                                Array.from(headerDiv.querySelectorAll('span')).find(span => 
                                                    span.classList.contains('bg-red-500') || 
                                                    span.classList.contains('rounded-full')
                                                );
                                    
                                    if (unreadCount > 0) {
                                        if (badge) {
                                            // Update existing badge
                                            badge.textContent = unreadCount;
                                            badge.classList.remove('hidden');
                                            // Ensure badge is visible
                                            if (badge.style.display === 'none') {
                                                badge.style.display = '';
                                            }
                                        } else {
                                            // Create new badge
                                            const badgeSpan = document.createElement('span');
                                            badgeSpan.className = 'flex-shrink-0 px-2 py-0.5 bg-red-500 text-white text-xs font-semibold rounded-full min-w-[20px] text-center shadow-sm';
                                            badgeSpan.textContent = unreadCount;
                                            headerDiv.appendChild(badgeSpan);
                                        }
                                    } else {
                                        // Remove badge if count is 0
                                        if (badge) {
                                            badge.remove();
                                        }
                                    }
                                }

                                // Update last message text if changed
                                const lastMessageElement = item.querySelector('.text-xs.sm\\:text-sm.text-gray-500');
                                if (lastMessageElement && conv.last_message) {
                                    const currentText = lastMessageElement.textContent.trim();
                                    if (currentText !== conv.last_message) {
                                        lastMessageElement.textContent = conv.last_message;
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating unread counts:', error);
            });
        }

        function renderConversations(conversations) {
            const container = document.getElementById('conversationsContainer');
            if (conversations.length === 0) {
                container.innerHTML = '<div class="p-4 text-center text-gray-500"><p class="text-sm">No conversations yet</p></div>';
                return;
            }

            // Remove duplicates by user_id
            const uniqueConversations = [];
            const seenUserIds = new Set();
            conversations.forEach(conv => {
                if (conv.user_id && !seenUserIds.has(conv.user_id)) {
                    seenUserIds.add(conv.user_id);
                    uniqueConversations.push(conv);
                }
            });
            
            // Sort conversations by last_message_time (most recent first) - ensure consistent ordering
            uniqueConversations.sort((a, b) => {
                const timeA = a.last_message_time ? new Date(a.last_message_time).getTime() : 0;
                const timeB = b.last_message_time ? new Date(b.last_message_time).getTime() : 0;
                // If times are equal, prioritize groups or use user_id as tiebreaker
                if (timeB === timeA) {
                    // If both are groups or both are not groups, maintain order
                    // Otherwise, prioritize non-groups (or vice versa - you can adjust this)
                    return 0;
                }
                return timeB - timeA; // Descending order (newest first)
            });

            container.innerHTML = uniqueConversations.map(conv => {
                // Use the correct data structure from API
                const userId = conv.user_id;
                const isGroup = conv.is_group || userId?.startsWith('group_');
                const fullName = conv.user_name || 'Unknown User';
                const initials = conv.user_initials || 'UU';
                const profilePictureUrl = conv.profile_picture_url || null;
                const lastMessage = conv.last_message || 'No messages yet';
                const unreadCount = conv.unread_count || 0;
                const isOnline = conv.is_online || false;
                // Calculate time_ago from last_message_time if available, otherwise use provided time_ago
                let timeAgo = '';
                if (conv.last_message_time) {
                    timeAgo = getTimeAgo(conv.last_message_time);
                } else if (conv.time_ago) {
                    timeAgo = conv.time_ago;
                }
                
                // Online indicator color (only for non-groups)
                const indicatorColor = isGroup ? '' : (isOnline ? 'bg-green-500' : 'bg-gray-400');
                
                let avatarHtml = '';
                if (profilePictureUrl) {
                    avatarHtml = `<img src="${profilePictureUrl}" alt="${fullName}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">`;
                } else {
                    const gradientColors = ['from-purple-400 to-purple-600', 'from-blue-400 to-blue-600', 'from-green-400 to-green-600', 'from-indigo-400 to-indigo-600', 'from-yellow-400 to-orange-500', 'from-pink-400 to-pink-600', 'from-red-400 to-red-600'];
                    const colorIndex = (userId?.charCodeAt(0) || 0) % gradientColors.length;
                    avatarHtml = `<div class="w-12 h-12 rounded-full bg-gradient-to-br ${gradientColors[colorIndex]} flex items-center justify-center text-white font-semibold text-sm">${initials}</div>`;
                }

                // Group indicator icon
                const groupIcon = isGroup ? '<i class="fas fa-users text-xs text-gray-500 absolute -top-1 -right-1 bg-white rounded-full p-1"></i>' : '';

                return `
                    <div class="conversation-item p-3 sm:p-4 hover:bg-gray-50 active:bg-gray-100 cursor-pointer transition border-b border-gray-100 touch-manipulation" data-user-id="${userId}" data-user-name="${fullName}" data-is-group="${isGroup}" style="-webkit-tap-highlight-color: transparent;">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="relative flex-shrink-0">
                                ${avatarHtml}
                                ${groupIcon}
                                ${!isGroup ? `<div class="absolute bottom-0 right-0 w-3 h-3 ${indicatorColor} rounded-full border-2 border-white"></div>` : ''}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1 gap-2">
                                    <div class="flex items-center gap-1.5 flex-1 min-w-0">
                                        <p class="text-sm sm:text-base font-semibold text-gray-800 truncate">${fullName}</p>
                                        ${isGroup ? '<i class="fas fa-users text-xs text-gray-400 flex-shrink-0"></i>' : ''}
                                </div>
                                    ${unreadCount > 0 ? `<span class="flex-shrink-0 px-2 py-0.5 bg-red-500 text-white text-xs font-semibold rounded-full min-w-[20px] text-center shadow-sm">${unreadCount}</span>` : ''}
                                </div>
                                <p class="text-xs sm:text-sm text-gray-500 truncate">${escapeHtml(lastMessage)}</p>
                                ${timeAgo ? `<p class="text-xs text-gray-400 mt-1 conversation-timestamp">${timeAgo}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Add click handlers
            container.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    // Get user data from the stored conversations
                    const conv = conversationsData.find(c => c.user_id === userId);
                    openChat(userId, userName, conv);
                });
            });
        }

        // Update a specific conversation in the list
        function updateConversationInList(userId, message, messageText) {
            if (!userId) return;
            
            const container = document.getElementById('conversationsContainer');
            if (!container) return;
            
            // Find existing conversation item
            const existingItem = container.querySelector(`[data-user-id="${userId}"]`);
            
            // Get conversation data
            let conv = conversationsData.find(c => c.user_id === userId);
            
            // Format last message text
            let lastMessageText = '';
            if (messageText && messageText.trim()) {
                lastMessageText = messageText.length > 50 ? messageText.substring(0, 50) + '...' : messageText;
            } else if (message && message.attachments && message.attachments.length > 0) {
                // Check attachment type - check for voice/audio messages first
                const attachment = message.attachments[0];
                
                // Check if it's explicitly a voice/audio message (by name or explicit audio type)
                const isVoiceMessage = (attachment.name && (
                    attachment.name.includes('voice-message') || 
                    attachment.name.includes('voice_') ||
                    attachment.name.includes('recording') ||
                    /\.mp3$/i.test(attachment.name)
                )) || (attachment.type && (
                    attachment.type.startsWith('audio/') ||
                    attachment.type === 'audio/mpeg'
                )) || (attachment.url && /\.mp3(\?|$|#)/i.test(attachment.url));
                
                // Check if it's an audio file (not voice message, but still audio)
                const isAudio = !isVoiceMessage && (
                    (attachment.type && attachment.type.startsWith('audio/')) ||
                    (attachment.name && /\.(mp3|wav|ogg|m4a|aac)$/i.test(attachment.name)) ||
                    (attachment.url && /\.(mp3|wav|ogg|m4a|aac)(\?|$|#)/i.test(attachment.url))
                );
                
                // Check if it's a video file (but exclude voice messages which might be video/mp4)
                const isVideo = !isVoiceMessage && (
                    (attachment.type && attachment.type.startsWith('video/')) || 
                    (attachment.name && /\.(mp4|webm|mov|avi|wmv|flv|mkv|3gp|m4v)$/i.test(attachment.name)) ||
                    (attachment.url && /\.(mp4|webm|mov|avi|wmv|flv|mkv|3gp|m4v)(\?|$|#)/i.test(attachment.url))
                );
                
                if (attachment.type) {
                    if (attachment.type.startsWith('image/')) {
                        lastMessageText = '📷 Image';
                    } else if (isVoiceMessage) {
                        lastMessageText = '🎤 Voice message';
                    } else if (isAudio) {
                        lastMessageText = '🎵 Audio';
                    } else if (isVideo) {
                        lastMessageText = '🎥 Video';
                    } else {
                        lastMessageText = '📎 Attachment';
                    }
                } else {
                    // Fallback to name/url checking if type is not available
                    if (isVoiceMessage) {
                        lastMessageText = '🎤 Voice message';
                    } else if (isAudio) {
                        lastMessageText = '🎵 Audio';
                    } else if (isVideo) {
                        lastMessageText = '🎥 Video';
                    } else {
                        lastMessageText = '📎 Attachment';
                    }
                }
            } else {
                lastMessageText = 'No messages yet';
            }
            
            // Update time ago
            const timeAgo = message && message.created_at ? getTimeAgo(message.created_at) : 'Just now';
            const messageTime = message ? message.created_at : new Date().toISOString();
            
            if (existingItem && conv) {
                // Update existing conversation data
                conv.last_message = lastMessageText;
                conv.last_message_time = messageTime;
                conv.time_ago = timeAgo;
                
                // Update UI elements
                const lastMessageEl = existingItem.querySelector('.text-xs.sm\\:text-sm.text-gray-500');
                const timeAgoEl = existingItem.querySelector('.conversation-timestamp');
                
                if (lastMessageEl) {
                    lastMessageEl.textContent = lastMessageText;
                }
                if (timeAgoEl) {
                    timeAgoEl.textContent = timeAgo;
                }
                
                // Re-sort all conversations by last_message_time (most recent first)
                conversationsData.sort((a, b) => {
                    const timeA = a.last_message_time ? new Date(a.last_message_time).getTime() : 0;
                    const timeB = b.last_message_time ? new Date(b.last_message_time).getTime() : 0;
                    return timeB - timeA; // Descending order (newest first)
                });
                
                // Re-render conversations in sorted order
                renderConversations(conversationsData);
            } else {
                // New conversation - reload full list
                loadConversations();
            }
        }

        function openChat(userId, userName, convData = null) {
            // Clear theme if switching to a different chat or non-group chat
            const previousChatUserId = currentChatUserId;
            currentChatUserId = userId;
            
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            if (chatMessagesArea) {
                const previousThemeGroupId = chatMessagesArea.getAttribute('data-theme-group-id');
                const isGroupChat = userId && userId.startsWith('group_');
                const currentGroupId = isGroupChat ? userId.replace('group_', '') : null;
                
                // Get previous theme user ID to check if we're switching between single chats
                const previousThemeUserId = chatMessagesArea.getAttribute('data-theme-user-id');
                
                // Clear theme if:
                // 1. Switching to a non-group chat from a group chat
                // 2. Switching to a different group chat
                // 3. Switching to a different single chat (different user)
                const isSwitchingSingleChats = !isGroupChat && previousThemeUserId && previousThemeUserId !== userId;
                if (!isGroupChat || (previousThemeGroupId && previousThemeGroupId !== currentGroupId) || isSwitchingSingleChats) {
                    // Reset to default appearance
                    chatMessagesArea.style.background = '';
                    chatMessagesArea.style.backgroundImage = '';
                    chatMessagesArea.style.backgroundSize = '';
                    chatMessagesArea.style.backgroundPosition = '';
                    chatMessagesArea.style.backgroundRepeat = '';
                    chatMessagesArea.removeAttribute('data-theme-id');
                    chatMessagesArea.removeAttribute('data-theme-group-id');
                    chatMessagesArea.removeAttribute('data-theme-user-id');
                    
                    // Reset header
                    const chatHeader = document.querySelector('#activeChat .border-b.bg-white');
                    if (chatHeader) {
                        chatHeader.style.backgroundColor = '';
                        chatHeader.style.borderColor = '';
                        const headerText = chatHeader.querySelectorAll('#chatHeaderName, #chatHeaderStatusText');
                        headerText.forEach(el => el.style.color = '');
                        const headerIcons = chatHeader.querySelectorAll('#groupSettingsBtn i, #backToConversations i');
                        headerIcons.forEach(icon => icon.style.color = '');
                    }
                    
                    // Reset message bubbles to default
                    chatMessagesArea.querySelectorAll('[style*="background"]').forEach(el => {
                        if (el.classList.contains('voice-message-container') || 
                            el.classList.contains('bg-gradient-to-r') || 
                            el.classList.contains('bg-white')) {
                            el.style.background = '';
                            el.style.color = '';
                        }
                    });
                }
                
                // Load single chat theme if it's a single chat
                if (!isGroupChat && userId) {
                    window.currentSingleChatUserId = userId;
                    // Load themes first if not loaded, then load and apply current theme
                    if (singleChatAvailableThemes.length === 0) {
                        loadSingleChatThemes().then(() => {
                            loadSingleChatCurrentTheme().then(() => {
                                if (singleChatCurrentAppliedTheme && window.currentSingleChatUserId === userId) {
                                    applySingleChatThemeToChat(singleChatCurrentAppliedTheme);
                                }
                            });
                        });
                    } else {
                        loadSingleChatCurrentTheme().then(() => {
                            if (singleChatCurrentAppliedTheme && window.currentSingleChatUserId === userId) {
                                applySingleChatThemeToChat(singleChatCurrentAppliedTheme);
                            }
                        });
                    }
                } else {
                    window.currentSingleChatUserId = null;
                }
            }
            
            // Highlight the selected conversation in the list
            const conversationItems = document.querySelectorAll('.conversation-item');
            conversationItems.forEach(item => {
                const itemUserId = item.getAttribute('data-user-id');
                if (itemUserId === userId) {
                    item.classList.add('active:bg-gray-100', 'bg-gray-100');
                    item.style.backgroundColor = '#f3f4f6';
                } else {
                    item.classList.remove('active:bg-gray-100', 'bg-gray-100');
                    item.style.backgroundColor = '';
                }
            });
            
            // Mobile: Hide conversations list, show chat area, hide header
            const conversationsList = document.getElementById('conversationsList');
            const chatArea = document.getElementById('chatArea');
            if (window.innerWidth <= 767) {
                if (conversationsList) {
                    conversationsList.classList.add('mobile-hidden');
                }
                if (chatArea) {
                    chatArea.classList.add('mobile-visible');
                    chatArea.classList.remove('hidden');
                }
                // Hide header on mobile when chat is open
                document.body.classList.add('header-hidden-mobile');
            }
            
            // Hide empty state, show active chat
            const chatEmptyState = document.getElementById('chatEmptyState');
            const activeChat = document.getElementById('activeChat');
            const emptyStateInput = document.getElementById('emptyStateMessageInput');
            
            if (chatEmptyState) {
                chatEmptyState.classList.add('hidden');
            }
            if (emptyStateInput) {
                emptyStateInput.style.display = 'none';
            }
            if (activeChat) {
                activeChat.classList.remove('hidden');
                activeChat.style.display = 'flex';
            }
            
            // Ensure message input is visible when chat is active
            const messageForm = document.getElementById('messageForm');
            const messageInputContainer = messageForm?.closest('div');
            
            // Make sure the input container is visible
            if (messageInputContainer) {
                messageInputContainer.style.display = 'block';
                messageInputContainer.style.visibility = 'visible';
                messageInputContainer.style.opacity = '1';
                messageInputContainer.classList.remove('hidden');
            }
            
            // Make sure the form is visible
            if (messageForm) {
                messageForm.classList.remove('hidden');
                messageForm.style.display = 'flex';
                messageForm.style.visibility = 'visible';
                messageForm.style.opacity = '1';
                messageForm.style.width = '100%';
            }
            
            // Also ensure voice recorder is hidden if not recording
            const voiceRecorder = document.getElementById('voiceRecorder');
            if (voiceRecorder && (!mediaRecorder || mediaRecorder.state !== 'recording')) {
                voiceRecorder.classList.add('hidden');
            }
            
            // Force visibility with a small delay to ensure DOM is ready
            setTimeout(() => {
                // Ensure activeChat is visible
                if (activeChat) {
                    activeChat.classList.remove('hidden');
                    activeChat.style.display = 'flex';
                    activeChat.style.visibility = 'visible';
                }
                
                if (messageInputContainer) {
                    messageInputContainer.style.display = 'block';
                    messageInputContainer.style.visibility = 'visible';
                    messageInputContainer.style.opacity = '1';
                    messageInputContainer.classList.remove('hidden');
                }
                if (messageForm) {
                    messageForm.classList.remove('hidden');
                    messageForm.style.display = 'flex';
                    messageForm.style.visibility = 'visible';
                    messageForm.style.opacity = '1';
                    messageForm.style.width = '100%';
                }
            }, 100);
            
            // Double-check after a longer delay to ensure visibility
            setTimeout(() => {
                const checkForm = document.getElementById('messageForm');
                const checkContainer = checkForm?.closest('div');
                if (checkForm && window.getComputedStyle(checkForm).display === 'none') {
                    checkForm.style.display = 'flex';
                    checkForm.style.visibility = 'visible';
                }
                if (checkContainer && window.getComputedStyle(checkContainer).display === 'none') {
                    checkContainer.style.display = 'block';
                    checkContainer.style.visibility = 'visible';
                }
            }, 300);
            
            // Update header with user data
            const chatHeaderName = document.getElementById('chatHeaderName');
            const chatHeaderAvatar = document.getElementById('chatHeaderAvatar');
            const chatHeaderStatusText = document.getElementById('chatHeaderStatusText');
            
            // Check if this is a group chat
            const isGroup = userId?.startsWith('group_') || convData?.is_group;
            
            // Store current chat user ID for avatar updates
            currentChatUserId = userId;
            
            if (chatHeaderName) {
                chatHeaderName.textContent = userName;
            }
            
            // Get user data from conversation or use defaults
            const profilePictureUrl = convData?.profile_picture_url || null;
            const initials = convData?.user_initials || (userName ? userName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 'UU');
            const isOnline = convData?.is_online || false;
            
            // Generate avatar HTML
            let avatarHtml = '';
            if (profilePictureUrl) {
                avatarHtml = `<img src="${profilePictureUrl}" alt="${userName}" class="w-10 h-10 rounded-full object-cover">`;
            } else {
                const gradientColors = ['from-purple-400 to-purple-600', 'from-blue-400 to-blue-600', 'from-green-400 to-green-600', 'from-indigo-400 to-indigo-600', 'from-yellow-400 to-orange-500', 'from-pink-400 to-pink-600', 'from-red-400 to-red-600'];
                const colorIndex = (userId?.charCodeAt(0) || 0) % gradientColors.length;
                avatarHtml = `<div class="w-10 h-10 rounded-full bg-gradient-to-br ${gradientColors[colorIndex]} flex items-center justify-center text-white font-semibold text-sm">${initials}</div>`;
            }
            
            // Add online status indicator (only for non-groups)
                const indicatorColor = isGroup ? '' : (isOnline ? '#3fbb46' : '#9ca3af');
                const indicatorHtml = !isGroup ? `<div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white" style="background-color: ${indicatorColor};"></div>` : '';
            
            // Add group icon for groups
            const groupIcon = isGroup ? '<i class="fas fa-users text-xs text-gray-500 absolute -top-1 -right-1 bg-white rounded-full p-1"></i>' : '';
            
            if (chatHeaderAvatar) {
                chatHeaderAvatar.innerHTML = `<div class="relative">${avatarHtml}${indicatorHtml}${groupIcon}</div>`;
            }
            
            // Update status text
            if (chatHeaderStatusText) {
                if (isGroup) {
                    chatHeaderStatusText.textContent = 'Group chat';
                } else {
                chatHeaderStatusText.textContent = isOnline ? 'Active now' : 'Offline';
                }
            }
            
            // Show/hide settings buttons
            const groupSettingsBtn = document.getElementById('groupSettingsBtn');
            const singleChatSettingsBtn = document.getElementById('singleChatSettingsBtn');
            
            if (isGroup) {
                // Hide single chat settings, show group settings if admin
                if (singleChatSettingsBtn) {
                    singleChatSettingsBtn.classList.add('hidden');
                }
                if (groupSettingsBtn) {
                    // Extract group ID and load group details to check admin status
                    const groupId = userId.replace('group_', '');
                    window.currentGroupId = groupId; // Set currentGroupId for group operations
                    loadGroupDetails(groupId);
                }
            } else {
                // Show single chat settings, hide group settings
                if (singleChatSettingsBtn) {
                    singleChatSettingsBtn.classList.remove('hidden');
                }
                if (groupSettingsBtn) {
                    groupSettingsBtn.classList.add('hidden');
                }
                window.currentGroupId = null; // Clear when not a group
                window.currentSingleChatUserId = userId; // Store current single chat user ID
            }
            
            // Setup scroll listener to track manual scrolling
            const messagesArea = document.getElementById('chatMessagesArea');
            if (messagesArea) {
                // Remove existing listener if any
                messagesArea.removeEventListener('scroll', handleManualScroll);
                // Add new listener
                messagesArea.addEventListener('scroll', handleManualScroll, { passive: true });
            }
            
            // Reset scroll flags
            userHasScrolledUp = false;
            isScrollingToParent = false;
            
            // Load conversation
            loadChatConversation(userId);
            
            // Apply theme after messages are loaded (with delay to ensure messages are rendered)
            const isSingleChat = !isGroup && userId;
            if (isSingleChat) {
                setTimeout(() => {
                    if (singleChatCurrentAppliedTheme && singleChatAvailableThemes.length > 0) {
                        applySingleChatThemeToChat(singleChatCurrentAppliedTheme);
                    }
                }, 500);
            }
            
            // Ensure scroll to bottom after opening chat
            setTimeout(() => {
                const messagesArea = document.getElementById('chatMessagesArea');
                if (messagesArea && !isScrollingToParent) {
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }
            }, 300);
            
            // Start polling
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
            // Function to run polling updates
            const runPollingUpdates = () => {
                const activeUserId = currentChatUserId;
                if (activeUserId) {
                    loadNewMessages(activeUserId);
                    updateReactionsForVisibleMessages(activeUserId);
                    updateReadStatusForVisibleMessages(activeUserId);
                }
                // Also update unread counts to detect new messages from other users
                // This ensures conversation list badges update when messages arrive from other users
                updateUnreadCounts();
            };
            
            // Run immediately on chat open
            runPollingUpdates();
            
            // Then set up interval
            pollingInterval = setInterval(runPollingUpdates, 3000);
        }
        
        // Set up periodic refresh for conversation list (even when no chat is open)
        // This ensures the conversation list updates in real-time, similar to the header dropdown
        function startConversationListRefresh() {
            // Clear any existing interval
            if (conversationListRefreshInterval) {
                clearInterval(conversationListRefreshInterval);
            }
            
            // Refresh conversation list every 5 seconds (even when no chat is open)
            conversationListRefreshInterval = setInterval(() => {
                // Check if updateUnreadCounts is defined before calling
                if (typeof updateUnreadCounts === 'function') {
                    // Update unread counts which will refresh the conversation list
                    // This works even when no chat is open, ensuring real-time updates
                    updateUnreadCounts();
                }
            }, 5000); // 5 seconds - frequent enough for real-time updates
        }
        
        // Start the refresh interval immediately (will be called again in DOMContentLoaded to ensure it starts)
        // This ensures it works even if DOMContentLoaded has already fired
        if (document.readyState === 'loading') {
            // DOM is still loading, will start in DOMContentLoaded
        } else {
            // DOM is already loaded, start immediately
            startConversationListRefresh();
        }
        
        // Make openChat globally accessible
        window.openChat = openChat;
        
        // Track manual scrolling by user
        function handleManualScroll() {
            if (isScrollingToParent) return; // Don't track if we're programmatically scrolling
            
            const messagesArea = document.getElementById('chatMessagesArea');
            if (!messagesArea) return;
            
            const isNearBottom = messagesArea.scrollHeight - messagesArea.scrollTop - messagesArea.clientHeight < 100;
            userHasScrolledUp = !isNearBottom;
        }

        function loadChatConversation(userId) {
            const messagesArea = document.getElementById('chatMessagesArea');
            messagesArea.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>';

            axios.get(`{{ route('messages.conversation', ':userId') }}`.replace(':userId', userId))
                .then(response => {
                    if (response.data.success) {
                        // Check if data is encrypted and decrypt it
                        let processMessages = (messages) => {
                        messagesArea.innerHTML = '';
                        lastMessageTimestamp = null;
                        lastSentMessageId = null; // Reset last sent message ID
                            if (messages && messages.length > 0) {
                            let previousMsg = null;
                                messages.forEach(msg => {
                                appendMessageToPage(msg, userId, previousMsg);
                                previousMsg = msg;
                                if (!lastMessageTimestamp || msg.created_at > lastMessageTimestamp) {
                                    lastMessageTimestamp = msg.created_at;
                                }
                            });
                            
                            // Find the last sent message after all messages are loaded
                                const sentMessages = messages.filter(m => m.is_sender);
                            if (sentMessages.length > 0) {
                                // Sort by created_at descending and get the first one
                                sentMessages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                                lastSentMessageId = sentMessages[0].id;
                            }
                            }
                            
                            // Always scroll to bottom when loading a new conversation
                            setTimeout(() => {
                                if (messagesArea && !isScrollingToParent) {
                                    messagesArea.scrollTop = messagesArea.scrollHeight;
                                }
                                // Make action buttons visible on mobile
                                makeActionButtonsVisible();
                                
                                // Mark messages as read AFTER messages are fully rendered
                                // If we loaded messages for this user, mark them as read
                                if (userId && messages && messages.length > 0) {
                                    // Mark as read with a small delay to ensure DOM is ready
                                    setTimeout(() => {
                                        // Only mark as read if this is still the active chat
                                        if (currentChatUserId === userId) {
                            axios.post(`{{ route('messages.mark-as-read', ':userId') }}`.replace(':userId', userId))
                                                .then((response) => {
                                                    
                                                    // Update unread count immediately for this conversation
                                                    const conv = conversationsData.find(c => c.user_id === userId);
                                                    if (conv) {
                                                        conv.unread_count = 0;
                                                    }
                                                    
                                                    // Update the badge in UI immediately
                                                    const conversationItem = document.querySelector(`[data-user-id="${userId}"].conversation-item`);
                                                    if (conversationItem) {
                                                        const headerDiv = conversationItem.querySelector('.flex.items-center.justify-between.mb-1');
                                                        if (headerDiv) {
                                                            const badge = headerDiv.querySelector('.bg-red-500');
                                                            if (badge) {
                                                                badge.remove();
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Update unread counts immediately (don't wait for server)
                                                    updateUnreadCounts();
                                                    
                                                    // Also call the unread-count API directly for header badge
                                                    if (typeof window.loadAdminUnreadCount === 'function') {
                                                        window.loadAdminUnreadCount();
                                                    } else if (typeof loadAdminUnreadCount === 'function') {
                                                        loadAdminUnreadCount();
                                                    }
                                                    
                                                    // Sync with server after a delay to ensure server has processed
                                                    setTimeout(() => {
                                                        updateUnreadCounts();
                                                        if (typeof window.loadAdminUnreadCount === 'function') {
                                                            window.loadAdminUnreadCount();
                                                        } else if (typeof loadAdminUnreadCount === 'function') {
                                                            loadAdminUnreadCount();
                                                        }
                                                    }, 500);
                                                })
                                                .catch(err => {
                                                    // Silently handle errors
                                                });
                                        }
                                    }, 500); // Delay to ensure user is viewing
                                }
                            }, 200); // Increased delay to ensure messages are fully rendered
                        };
                        
                        // Handle encrypted response
                        if (response.data.encrypted && response.data.data) {
                            // Decrypt the data by calling a decryption endpoint
                            axios.post('{{ route("messages.decrypt") }}', {
                                encrypted_data: response.data.data
                            })
                            .then(decryptResponse => {
                                if (decryptResponse.data.success) {
                                    processMessages(decryptResponse.data.messages || []);
                        } else {
                                    console.error('Decryption failed:', decryptResponse.data.message);
                                    messagesArea.innerHTML = '<div class="p-4 text-center text-red-500">Failed to decrypt messages. Please refresh the page.</div>';
                                    return;
                                }
                            })
                            .catch(error => {
                                console.error('Decryption error:', error);
                                messagesArea.innerHTML = '<div class="p-4 text-center text-red-500">Failed to decrypt messages. Please refresh the page.</div>';
                                return;
                            });
                        } else {
                            // Not encrypted, use directly
                            processMessages(response.data.messages || []);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading conversation:', error);
                    messagesArea.innerHTML = '<div class="p-4 text-center text-red-500">Error loading messages</div>';
                });
        }

        // Format time for timestamp separators (e.g., "7:09 PM")
        function formatTimestampSeparator(timestamp) {
            if (!timestamp) return '';
            const time = new Date(timestamp);
            const hours = time.getHours();
            const minutes = time.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const displayMinutes = minutes.toString().padStart(2, '0');
            return `${displayHours}:${displayMinutes} ${ampm}`;
        }
        
        // Check if we should show a timestamp separator
        function shouldShowTimestampSeparator(currentMsg, previousMsg) {
            if (!previousMsg) return true; // Show for first message
            
            const currentTime = new Date(currentMsg.created_at);
            const previousTime = new Date(previousMsg.created_at);
            const diffMinutes = (currentTime - previousTime) / (1000 * 60);
            
            // Show separator if messages are more than 5 minutes apart
            return diffMinutes > 5;
        }
        
        // Create timestamp separator element
        function createTimestampSeparator(timestamp) {
            const separatorDiv = document.createElement('div');
            separatorDiv.className = 'flex items-center justify-center my-4';
            separatorDiv.innerHTML = `
                <div class="px-3 py-1 bg-gray-200 rounded-full" style="background-color: #e5e7eb !important;">
                    <span class="text-xs text-gray-600 font-medium" style="color: #4b5563 !important;">${formatTimestampSeparator(timestamp)}</span>
                </div>
            `;
            return separatorDiv;
        }

        // Full message rendering function from popup - adapted for page
        function appendMessageToPage(msg, userId, previousMsg = null) {
            const messagesArea = document.getElementById('chatMessagesArea');
            if (!messagesArea) return;

            // Check if message already exists to prevent duplicates
            const existingMessage = messagesArea.querySelector(`[data-message-id="${msg.id}"]`);
            if (existingMessage) {
                return; // Message already exists, skip
            }
            
            // Check if there's a temp separator that was added for a temp message
            const tempSeparator = messagesArea.querySelector('[data-temp-separator="true"]');
            if (tempSeparator) {
                // Check if the timestamps match (within 1 minute) - if so, reuse the separator
                const separatorTimestamp = tempSeparator.getAttribute('data-separator-timestamp');
                const msgTimestamp = formatTimestampSeparator(msg.created_at);
                const msgTime = new Date(msg.created_at);
                const separatorTimeStr = tempSeparator.getAttribute('data-created-at');
                const separatorTime = separatorTimeStr ? new Date(separatorTimeStr) : msgTime;
                const timeDiff = Math.abs(msgTime - separatorTime) / (1000 * 60); // Difference in minutes
                
                if (timeDiff < 1 && separatorTimestamp === msgTimestamp) {
                    // Timestamps match, reuse the separator and remove temp flag
                    tempSeparator.removeAttribute('data-temp-separator');
                    tempSeparator.removeAttribute('data-separator-timestamp');
                    tempSeparator.removeAttribute('data-created-at');
                } else {
                    // Timestamps don't match, remove temp separator and let normal logic decide
                    tempSeparator.remove();
            // Check if we need to add a timestamp separator
            if (shouldShowTimestampSeparator(msg, previousMsg)) {
                const separator = createTimestampSeparator(msg.created_at);
                messagesArea.appendChild(separator);
                    }
                }
            } else {
                // Check if we need to add a timestamp separator
                if (shouldShowTimestampSeparator(msg, previousMsg)) {
                    const separator = createTimestampSeparator(msg.created_at);
                    messagesArea.appendChild(separator);
                }
            }

            const isSender = msg.is_sender;
            const senderName = msg.sender ? `${msg.sender.first_name} ${msg.sender.last_name}` : 'User';
            const senderInitials = msg.sender ? (msg.sender.first_name[0] + msg.sender.last_name[0]).toUpperCase() : 'U';
            
            // Check if current chat is a group chat
            const isGroupChat = currentChatUserId && currentChatUserId.startsWith('group_');
            
            const messageDiv = document.createElement('div');
            messageDiv.className = isSender 
                ? 'flex items-start space-x-2 justify-end' 
                : 'flex items-start space-x-2';
            messageDiv.setAttribute('data-message-id', msg.id);
            messageDiv.setAttribute('data-created-at', msg.created_at);

            let messageContent = '';
            
            // Handle attachments - full implementation from popup
            if (msg.attachments && msg.attachments.length > 0) {
                msg.attachments.forEach((attachment, index) => {
                    if (!attachment || (!attachment.url && !attachment.type)) {
                        console.warn('Invalid attachment:', attachment);
                        return;
                    }
                    
                    // Check if it's explicitly a voice/audio message (by name or explicit audio type)
                    // Voice messages might be video/mp4 but have specific naming patterns
                    // Also check for MP3 files by extension or MIME type
                    const isVoiceMessage = (attachment.name && (
                        attachment.name.includes('voice-message') || 
                        attachment.name.includes('voice_') ||
                        attachment.name.includes('recording') ||
                        /\.mp3$/i.test(attachment.name)
                    )) || (attachment.type && (
                        attachment.type.startsWith('audio/') ||
                        attachment.type === 'audio/mpeg'
                    )) || (attachment.url && /\.mp3(\?|$|#)/i.test(attachment.url));
                    
                    // Check if it's a video file (but exclude voice messages)
                    // Prioritize video detection for actual video files
                    const isVideo = !isVoiceMessage && (
                                   (attachment.type && attachment.type.startsWith('video/')) || 
                        (attachment.name && /\.(mp4|webm|mov|avi|wmv|flv|mkv|3gp|m4v)$/i.test(attachment.name)) ||
                        (attachment.url && /\.(mp4|webm|mov|avi|wmv|flv|mkv|3gp|m4v)(\?|$|#)/i.test(attachment.url))
                    );
                    
                    // Check if it's an audio file (only if not video and not voice message)
                    const isAudio = !isVideo && !isVoiceMessage && (
                        (attachment.type && attachment.type.startsWith('audio/')) ||
                        (attachment.name && /\.(mp3|wav|ogg|m4a|aac)$/i.test(attachment.name)) ||
                        (attachment.url && /\.(mp3|wav|ogg|m4a|aac)(\?|$|#)/i.test(attachment.url))
                                   );
                    
                    if (isVideo) {
                        if (!attachment.url) return;
                        const videoName = attachment.name || 'Video';
                        let videoType = attachment.type || 'video/mp4';
                        if (!attachment.type && attachment.url) {
                            if (/\.webm(\?|$|#)/i.test(attachment.url)) videoType = 'video/webm';
                            else if (/\.ogg(\?|$|#)/i.test(attachment.url)) videoType = 'video/ogg';
                            else if (/\.mov(\?|$|#)/i.test(attachment.url)) videoType = 'video/quicktime';
                            else if (/\.avi(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-msvideo';
                            else if (/\.wmv(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-ms-wmv';
                            else if (/\.flv(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-flv';
                            else if (/\.mkv(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-matroska';
                            else videoType = 'video/mp4';
                        }
                        messageContent += `
                            <div class="mb-2 relative group">
                                <video src="${attachment.url}" 
                                       controls 
                                       class="rounded-lg shadow-sm" 
                                       style="max-width: 100%; max-width: 400px; max-height: 500px; width: auto; height: auto; display: block; background: #000;" 
                                       preload="metadata"
                                       playsinline>
                                    <source src="${attachment.url}" type="${videoType}">
                                    <p class="text-gray-500 text-sm p-2">Your browser does not support the video tag. 
                                        <a href="${attachment.url}" download="${videoName}" class="text-blue-500 hover:underline">Download video</a>
                                    </p>
                                </video>
                            </div>
                        `;
                    } else if (isVoiceMessage) {
                        // Voice message - display with voice player
                        if (!attachment.url) {
                            console.warn('Voice message missing URL:', attachment);
                            return;
                        }
                        
                        // Extract duration from filename or use default
                        // Always show in "0:00 / duration" format initially
                        let durationLabel = '0:00 / 0:00';
                        let totalDuration = '0:00';
                        const nameToCheck = attachment.name || attachment.url || '';
                        if (nameToCheck) {
                            // Try to extract from filename format: voice-message-timestamp-m-s.webm or voice-message-timestamp-m-s.mp3
                            const durationMatch = nameToCheck.match(/(\d+)-(\d+)\.(webm|mp3|wav|ogg|m4a|aac|mp4)$/i);
                            if (durationMatch) {
                                const mins = parseInt(durationMatch[1]);
                                const secs = parseInt(durationMatch[2]);
                                totalDuration = `${mins}:${secs.toString().padStart(2, '0')}`;
                                durationLabel = `0:00 / ${totalDuration}`;
                            } else {
                                // Try standard format: m:s
                                const standardMatch = nameToCheck.match(/(\d+):(\d+)/);
                                if (standardMatch) {
                                    totalDuration = `${standardMatch[1]}:${standardMatch[2]}`;
                                    durationLabel = `0:00 / ${totalDuration}`;
                                }
                            }
                        }
                        const voiceBubbleId = `voice-${msg.id}-${index}`;
                        
                        // Get current theme if available (check both group and single chat themes)
                        const chatMessagesArea = document.getElementById('chatMessagesArea');
                        const currentThemeId = chatMessagesArea?.getAttribute('data-theme-id');
                        const currentThemeGroupId = chatMessagesArea?.getAttribute('data-theme-group-id');
                        const currentThemeUserId = chatMessagesArea?.getAttribute('data-theme-user-id');
                        const groupId = isGroupChat ? currentChatUserId.replace('group_', '') : null;
                        
                        let theme = null;
                        if (isGroupChat && currentThemeId && currentThemeGroupId === groupId && availableThemes.length > 0) {
                            theme = availableThemes.find(t => t.id === currentThemeId);
                        } else if (!isGroupChat && currentThemeId && currentThemeUserId === currentChatUserId && singleChatAvailableThemes.length > 0) {
                            theme = singleChatAvailableThemes.find(t => t.id === currentThemeId);
                        }
                        
                        // Use theme colors if available, otherwise use defaults
                        const voiceBubbleBg = isSender 
                            ? (theme ? '' : 'bg-[#FF1F70]') 
                            : 'bg-white border border-gray-200';
                        const voiceBubbleStyle = isSender && theme 
                            ? `background: ${theme.sender_bubble};` 
                            : '';
                        const voiceButtonBg = isSender ? 'bg-white' : 'bg-gray-100';
                        const voiceButtonColor = isSender 
                            ? (theme ? `color: ${theme.sender_bubble};` : 'text-[#FF1F70]') 
                            : 'text-gray-700';
                        const waveformColor = isSender ? 'bg-white' : 'bg-gray-400';
                        const waveformPlayedColor = isSender ? 'bg-white opacity-60' : 'bg-gray-300';
                        const durationTextColor = isSender 
                            ? (theme ? `color: ${theme.sender_text};` : 'text-white') 
                            : 'text-gray-700';
                        
                        messageContent += `
                            <div class="mb-2 rounded-2xl px-4 py-3 shadow-sm flex items-center gap-3 max-w-[280px] sm:max-w-[320px] ${voiceBubbleBg} voice-message-container" style="min-width: 200px; ${voiceBubbleStyle}" data-voice-id="${voiceBubbleId}">
                                <button type="button" class="voice-play-toggle flex items-center justify-center w-9 h-9 rounded-full ${voiceButtonBg} ${voiceButtonColor} hover:opacity-80 transition shadow-sm flex-shrink-0" aria-label="Play voice message" data-voice-id="${voiceBubbleId}">
                                    <svg class="w-4 h-4 play-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                    <svg class="w-4 h-4 pause-icon hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"></path></svg>
                                </button>
                                <div class="flex-1 flex items-center justify-center min-w-0 voice-waveform-container" style="position: relative;">
                                    <div class="flex items-end gap-[2px] voice-waveform" style="position: relative; width: 100%;">
                                        <div class="w-[3px] h-3 rounded-full ${waveformColor} opacity-80 waveform-bar" data-index="0"></div>
                                        <div class="w-[3px] h-5 rounded-full ${waveformColor} opacity-90 waveform-bar" data-index="1"></div>
                                        <div class="w-[3px] h-7 rounded-full ${waveformColor} opacity-95 waveform-bar" data-index="2"></div>
                                        <div class="w-[3px] h-4 rounded-full ${waveformColor} opacity-85 waveform-bar" data-index="3"></div>
                                        <div class="w-[3px] h-6 rounded-full ${waveformColor} opacity-90 waveform-bar" data-index="4"></div>
                                        <div class="w-[3px] h-3 rounded-full ${waveformColor} opacity-80 waveform-bar" data-index="5"></div>
                                    </div>
                                </div>
                                <div class="ml-2 flex flex-col items-end gap-1 flex-shrink-0">
                                    <div class="text-xs font-semibold voice-duration whitespace-nowrap" style="min-width: 50px; text-align: right; ${durationTextColor}">${durationLabel}</div>
                                    <button type="button" class="voice-speed-toggle text-xs font-semibold bg-white hover:opacity-80 transition px-1.5 py-0.5 rounded" style="${isSender ? (theme ? `color: ${theme.sender_bubble};` : 'color: #FF1F70;') : (durationTextColor.includes('color:') ? durationTextColor : '')}" data-voice-id="${voiceBubbleId}" data-speed="1">1x</button>
                                </div>
                                <audio class="hidden voice-audio" id="${voiceBubbleId}" preload="metadata">
                                    ${(() => {
                                        // Detect correct MIME type from URL extension if type is missing or incorrect
                                        let mimeType = attachment.type;
                                        if (!mimeType || mimeType === 'audio/mpeg') {
                                            if (attachment.url.match(/\.mp4(\?|$|#)/i)) {
                                                mimeType = 'audio/mp4';
                                            } else if (attachment.url.match(/\.webm(\?|$|#)/i)) {
                                                mimeType = 'audio/webm';
                                            } else if (attachment.url.match(/\.mp3(\?|$|#)/i)) {
                                                mimeType = 'audio/mpeg';
                                            } else {
                                                mimeType = 'audio/mpeg';
                                            }
                                        }
                                        return `<source src="${attachment.url}" type="${mimeType}">`;
                                    })()}
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        `;
                        
                        // Try to get actual duration from audio element after it loads
                        setTimeout(() => {
                            const audioEl = document.getElementById(voiceBubbleId);
                            if (audioEl) {
                                const updateTotalDuration = function() {
                                    if (audioEl.duration && !isNaN(audioEl.duration)) {
                                        const minutes = Math.floor(audioEl.duration / 60);
                                        const seconds = Math.floor(audioEl.duration % 60);
                                        const totalDurationStr = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                                        const container = audioEl.closest('.voice-message-container');
                                        const durationEl = container?.querySelector('.voice-duration');
                                        if (durationEl) {
                                            // Store total duration on audio element for later use
                                            audioEl.dataset.totalDuration = totalDurationStr;
                                            if (audioEl.currentTime === 0 && audioEl.paused) {
                                                durationEl.textContent = `0:00 / ${totalDurationStr}`;
                                            }
                                        }
                                    }
                                };
                                
                                audioEl.addEventListener('loadedmetadata', updateTotalDuration);
                                
                                // Try to load if not already
                                if (audioEl.readyState === 0) {
                                    audioEl.load();
                                } else if (audioEl.readyState >= 1) {
                                    updateTotalDuration();
                                }
                            }
                        }, 100);
                    } else {
                        // Check if it's an image
                        const isImage = (attachment.type && attachment.type.startsWith('image/')) || 
                                       (attachment.name && /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)$/i.test(attachment.name)) ||
                                       (attachment.url && /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?|$|#)/i.test(attachment.url));
                        
                        if (isImage && attachment.url) {
                            const imageName = attachment.name || 'Image';
                            messageContent += `
                                <div class="mb-2 relative group">
                                    <img src="${attachment.url}" alt="${imageName}" 
                                         class="max-w-[200px] rounded-lg cursor-pointer hover:opacity-90 transition shadow-sm"
                                         style="max-height: 300px; max-width: 200px; object-fit: contain; display: block;"
                                         onclick="if(typeof window.openImageViewer === 'function') window.openImageViewer('${attachment.url}')"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'200\\' height=\\'200\\'%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23999\\'%3EImage not found%3C/text%3E%3C/svg%3E';">
                                    <button onclick="if(typeof window.openImageViewer === 'function') window.openImageViewer('${attachment.url}'); event.stopPropagation();" 
                                            class="absolute top-2 right-2 bg-black bg-opacity-70 hover:bg-opacity-90 text-white rounded-full p-2 transition-all z-10"
                                            title="Open in image viewer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </button>
                                </div>
                            `;
                        } else if (attachment.url) {
                            // Document/file attachment
                            const fileName = attachment.name || 'File';
                            const fileExt = fileName.includes('.') ? fileName.split('.').pop().toLowerCase() : '';
                            const fileIcon = getFileIconForAttachment(fileExt);
                            const isPdf = fileExt === 'pdf';
                            const fileBgClass = isSender ? 'bg-white bg-opacity-20' : 'bg-gray-100';
                            const fileTextClass = isPdf ? 'text-black' : (isSender ? 'text-white' : 'text-gray-700');
                            messageContent += `
                                <div class="mb-2 p-2 ${fileBgClass} rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        ${fileIcon}
                                        <a href="${attachment.url}" download="${fileName}" 
                                           class="text-xs ${fileTextClass} hover:underline truncate flex-1 font-medium" 
                                           title="${fileName}">
                                            ${fileName}
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    }
                });
            }

            // Add text message - use theme colors if available
            if (msg.message && msg.message.trim()) {
                const chatMessagesArea = document.getElementById('chatMessagesArea');
                const currentThemeId = chatMessagesArea?.getAttribute('data-theme-id');
                const currentThemeGroupId = chatMessagesArea?.getAttribute('data-theme-group-id');
                const groupId = isGroupChat ? currentChatUserId.replace('group_', '') : null;
                
                let theme = null;
                if (currentThemeId && currentThemeGroupId === groupId && availableThemes.length > 0) {
                    theme = availableThemes.find(t => t.id === currentThemeId);
                }
                
                // Apply theme colors for both sender and receiver messages (check both group and single chat themes)
                const chatMessagesAreaForText = document.getElementById('chatMessagesArea');
                const currentThemeIdForText = chatMessagesAreaForText?.getAttribute('data-theme-id');
                const currentThemeGroupIdForText = chatMessagesAreaForText?.getAttribute('data-theme-group-id');
                const currentThemeUserIdForText = chatMessagesAreaForText?.getAttribute('data-theme-user-id');
                const groupIdForText = isGroupChat ? currentChatUserId.replace('group_', '') : null;
                
                let themeForText = null;
                if (isGroupChat && currentThemeIdForText && currentThemeGroupIdForText === groupIdForText && availableThemes.length > 0) {
                    themeForText = availableThemes.find(t => t.id === currentThemeIdForText);
                } else if (!isGroupChat && currentThemeIdForText && currentThemeUserIdForText === currentChatUserId && singleChatAvailableThemes.length > 0) {
                    themeForText = singleChatAvailableThemes.find(t => t.id === currentThemeIdForText);
                }
                
                let bubbleStyle = '';
                let bubbleClass = '';
                
                if (themeForText) {
                    // Theme is applied - use theme colors
                    if (isSender) {
                        bubbleStyle = `background: ${themeForText.sender_bubble} !important; color: ${themeForText.sender_text} !important;`;
                        bubbleClass = ''; // No default classes when theme is applied
                    } else {
                        bubbleStyle = `background: ${themeForText.receiver_bubble} !important; color: ${themeForText.receiver_text} !important;`;
                        bubbleClass = ''; // No default classes when theme is applied
                    }
                } else {
                    // No theme - use default colors
                    if (isSender) {
                        bubbleStyle = '';
                        bubbleClass = 'bg-gradient-to-r from-blue-500 to-purple-600 text-white';
                    } else {
                        bubbleStyle = '';
                        bubbleClass = 'bg-white text-gray-800';
                    }
                }
                
                if (messageContent) {
                    messageContent += `<div class="rounded-lg p-2 shadow-sm mt-2 ${bubbleClass}" style="${bubbleStyle}"><p class="text-xs">${escapeHtml(msg.message)}</p></div>`;
                } else {
                    messageContent = `<div class="rounded-lg p-2 shadow-sm ${bubbleClass}" style="${bubbleStyle}"><p class="text-xs">${escapeHtml(msg.message)}</p></div>`;
                }
            }

            const timeAgo = getTimeAgo(msg.created_at);
            
            // Get theme for reaction colors (check both group and single chat themes)
            const chatMessagesAreaForReactions = document.getElementById('chatMessagesArea');
            const currentThemeIdForReactions = chatMessagesAreaForReactions?.getAttribute('data-theme-id');
            const currentThemeGroupIdForReactions = chatMessagesAreaForReactions?.getAttribute('data-theme-group-id');
            const currentThemeUserIdForReactions = chatMessagesAreaForReactions?.getAttribute('data-theme-user-id');
            const groupIdForReactions = isGroupChat ? currentChatUserId.replace('group_', '') : null;
            
            let themeForReactions = null;
            if (isGroupChat && currentThemeIdForReactions && currentThemeGroupIdForReactions === groupIdForReactions && availableThemes.length > 0) {
                themeForReactions = availableThemes.find(t => t.id === currentThemeIdForReactions);
            } else if (!isGroupChat && currentThemeIdForReactions && currentThemeUserIdForReactions === currentChatUserId && singleChatAvailableThemes.length > 0) {
                themeForReactions = singleChatAvailableThemes.find(t => t.id === currentThemeIdForReactions);
            }
            
            // Get reactions display with theme colors
            const reactions = msg.reactions || [];
            let reactionsDisplay = '';
            if (reactions.length > 0) {
                const reactionEmojis = {
                    'like': '👍', 'love': '❤️', 'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😠'
                };
                const reactionCounts = reactions.map(r => `${reactionEmojis[r.type] || '👍'} ${r.count}`).join(' ');
                // Use theme colors: sender_text for sent messages, receiver_text for received messages
                const reactionColor = themeForReactions 
                    ? (isSender ? themeForReactions.sender_text : themeForReactions.receiver_text)
                    : '#4b5563';
                reactionsDisplay = `<div class="flex justify-end mt-1"><div class="message-reactions flex items-center gap-1 text-xs cursor-pointer hover:opacity-80 transition" data-message-id="${msg.id}" title="View reactions" style="color: ${reactionColor} !important;">${reactionCounts}</div></div>`;
            }
            
            // Check if this is a reply and show reply indicator
            let replyIndicator = '';
            // Check if there's actual content (message or attachments) to determine if we need margin
            const hasContent = (msg.message && msg.message.trim()) || (msg.attachments && msg.attachments.length > 0);
            
            if (msg.parent_id) {
                let parentMessageText = 'Message';
                let parentSenderName = 'User';
                let isReplyingToSelf = false;
                
                if (msg.parent_message) {
                    parentMessageText = msg.parent_message.message || 'Message';
                    parentSenderName = msg.parent_message.sender_name || 'User';
                    // Check if replying to own message
                    const parentSenderId = msg.parent_message.sender_id;
                    // Compare as strings to handle UUID comparison
                    isReplyingToSelf = parentSenderId && String(parentSenderId) === String(currentUserId) && isSender;
                }
                
                // Only add mb-1 if there's content below the reply indicator
                const replyLabel = isReplyingToSelf ? 'You replied to yourself' : `${escapeHtml(parentSenderName)}: ${escapeHtml(parentMessageText.substring(0, 40))}${parentMessageText.length > 40 ? '...' : ''}`;
                
                replyIndicator = `
                    <div class="${hasContent ? 'mb-1' : ''} flex items-center gap-2 text-xs text-gray-400 cursor-pointer hover:text-gray-600 transition-colors reply-to-message" data-parent-id="${msg.parent_id}" title="Click to view original message">
                        <svg class="w-3 h-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <span class="truncate">${replyLabel}</span>
                    </div>
                `;
            }

            if (isSender) {
                const senderAvatar = currentUserProfilePicture 
                    ? `<img src="${currentUserProfilePicture}" alt="You" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200 flex-shrink-0">`
                    : `<div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">${currentUserInitials}</div>`;
                
                const hasVideo = messageContent && messageContent.includes('<video');
                // Only create contentWrapper if there's actual content to avoid empty divs
                const contentWrapper = messageContent && messageContent.trim() ? `<div class="space-y-2">${messageContent}</div>` : '';
                
                // Add data attribute to identify sent messages for read status updates
                messageDiv.setAttribute('data-is-sender', 'true');
                
                const actionButtons = `
                    <button class="message-react-btn p-0.5 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" title="React">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <button class="message-reply-btn p-0.5 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" title="Reply">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </button>
                    <button class="message-delete-btn p-0.5 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" title="Delete">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                
                if (hasVideo) {
                    messageDiv.innerHTML = `
                        <div class="flex-1 flex justify-end items-start gap-2 group">
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${contentWrapper || messageContent || ''}
                                <div class="flex items-center justify-end gap-0.5 mt-2">${actionButtons}</div>
                                ${reactionsDisplay}
                                ${(() => {
                                    // Add "Seen" indicator only for the last sent message
                                    if (isSender && msg.is_read && msg.id == lastSentMessageId) {
                                        return '<div class="flex justify-end mt-1 message-seen-indicator"><span class="text-xs text-blue-500 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Seen</span></div>';
                                    }
                                    return '';
                                })()}
                            </div>
                        </div>
                        ${senderAvatar}
                    `;
                } else {
                    messageDiv.innerHTML = `
                        <div class="flex-1 flex justify-end items-start gap-2 group">
                            <div class="flex items-center gap-0.5">${actionButtons}</div>
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${contentWrapper || messageContent || ''}
                                ${reactionsDisplay}
                                ${(() => {
                                    // Add "Seen" indicator only for the last sent message
                                    if (isSender && msg.is_read && msg.id == lastSentMessageId) {
                                        return '<div class="flex justify-end mt-1 message-seen-indicator"><span class="text-xs text-blue-500 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Seen</span></div>';
                                    }
                                    return '';
                                })()}
                            </div>
                        </div>
                        ${senderAvatar}
                    `;
                }
            } else {
                // Store user data for received messages (use sender_id for group messages)
                const userKey = msg.group_id ? msg.sender_id : userId;
                if (!userData[userKey]) {
                    userData[userKey] = {
                        initials: senderInitials,
                        color: 'from-purple-400 to-purple-600',
                        profilePicture: msg.sender?.profile_picture_url || null
                    };
                }
                const user = userData[userKey];
                const senderProfilePicture = msg.sender?.profile_picture_url || user.profilePicture || null;
                const senderIsOnline = msg.sender?.is_online || false;
                
                // Create avatar with online indicator
                const onlineIndicatorColor = senderIsOnline ? '#3fbb46' : '#9ca3af';
                const onlineIndicator = `<div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white" style="background-color: ${onlineIndicatorColor};"></div>`;
                
                const receiverAvatar = senderProfilePicture
                    ? `<div class="relative flex-shrink-0">${onlineIndicator}<img src="${senderProfilePicture}" alt="${senderName}" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200"></div>`
                    : `<div class="relative flex-shrink-0">${onlineIndicator}<div class="w-6 h-6 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs">${user.initials || senderInitials}</div></div>`;
                
                // Apply theme colors to received messages (check both group and single chat themes)
                const chatMessagesArea = document.getElementById('chatMessagesArea');
                const currentThemeId = chatMessagesArea?.getAttribute('data-theme-id');
                const currentThemeGroupId = chatMessagesArea?.getAttribute('data-theme-group-id');
                const currentThemeUserId = chatMessagesArea?.getAttribute('data-theme-user-id');
                const groupId = isGroupChat ? currentChatUserId.replace('group_', '') : null;
                
                let theme = null;
                if (isGroupChat && currentThemeId && currentThemeGroupId === groupId && availableThemes.length > 0) {
                    theme = availableThemes.find(t => t.id === currentThemeId);
                } else if (!isGroupChat && currentThemeId && currentThemeUserId === currentChatUserId && singleChatAvailableThemes.length > 0) {
                    theme = singleChatAvailableThemes.find(t => t.id === currentThemeId);
                }
                
                // For group messages, show sender first name only with theme color
                let senderNameDisplay = '';
                if (isGroupChat && !isSender && msg.sender) {
                    const firstName = msg.sender.first_name || senderName.split(' ')[0];
                    const senderNameColor = theme ? theme.receiver_text : '#000000';
                    senderNameDisplay = `<p class="text-xs font-semibold mb-1" style="color: ${senderNameColor} !important;">${escapeHtml(firstName)}</p>`;
                }
                
                let receivedMessageContent = messageContent;
                if (receivedMessageContent) {
                    // Replace sender bubble styles with receiver bubble styles
                    receivedMessageContent = receivedMessageContent.replace(/bg-gradient-to-r from-blue-500 to-purple-600 text-white/g, theme ? `style="background: ${theme.receiver_bubble}; color: ${theme.receiver_text};"` : 'bg-white text-gray-800');
                    receivedMessageContent = receivedMessageContent.replace(/bg-gradient-to-r from-pink-500 to-pink-600/g, theme ? `style="background: ${theme.receiver_bubble};"` : 'bg-white');
                    // Also replace inline styles for sender bubbles
                    receivedMessageContent = receivedMessageContent.replace(/style="background: [^"]*sender_bubble[^"]*"/g, theme ? `style="background: ${theme.receiver_bubble}; color: ${theme.receiver_text};"` : '');
                    receivedMessageContent = `<div class="space-y-2">${receivedMessageContent}</div>`;
                } else if (msg.message && msg.message.trim()) {
                    const receiverStyle = theme 
                        ? `style="background: ${theme.receiver_bubble}; color: ${theme.receiver_text};"` 
                        : '';
                    receivedMessageContent = `<div class="bg-white rounded-lg p-2 shadow-sm" ${receiverStyle}><p class="text-xs text-gray-800">${escapeHtml(msg.message)}</p></div>`;
                }
                
                const hasVideo = messageContent && messageContent.includes('<video');
                const actionButtons = `
                    <button class="message-react-btn p-1 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" title="React">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <button class="message-reply-btn p-1 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" title="Reply">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </button>
                `;
                
                if (hasVideo) {
                    messageDiv.innerHTML = `
                        ${receiverAvatar}
                        <div class="flex-1 flex items-start gap-2 group">
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${senderNameDisplay}
                                ${receivedMessageContent || ''}
                                <div class="flex items-center gap-0.5 mt-2">${actionButtons}</div>
                                ${reactionsDisplay}
                            </div>
                        </div>
                    `;
                } else {
                    messageDiv.innerHTML = `
                        ${receiverAvatar}
                        <div class="flex-1 flex items-start gap-2 group">
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${senderNameDisplay}
                                ${receivedMessageContent || ''}
                                ${reactionsDisplay}
                            </div>
                            <div class="flex items-center gap-0.5">${actionButtons}</div>
                        </div>
                    `;
                }
            }

            messagesArea.appendChild(messageDiv);
            
            // Apply theme to newly added message if theme is active
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            const currentThemeId = chatMessagesArea?.getAttribute('data-theme-id');
            const currentThemeGroupId = chatMessagesArea?.getAttribute('data-theme-group-id');
            const groupId = isGroupChat ? currentChatUserId.replace('group_', '') : null;
            
            if (currentThemeId && currentThemeGroupId === groupId && availableThemes.length > 0) {
                const theme = availableThemes.find(t => t.id === currentThemeId);
                if (theme) {
                    // Apply theme to this specific message
                    applyThemeToMessage(messageDiv, theme, isSender);
                }
            }
            
            // Ensure action buttons are always visible on all devices
            // Select containers that have flex, items-center classes and contain action buttons
            const actionButtonContainers = messageDiv.querySelectorAll('[class*="flex"][class*="items-center"]');
            actionButtonContainers.forEach(container => {
                // Check if this container has action buttons
                const hasActionButtons = container.querySelector('.message-react-btn, .message-reply-btn, .message-delete-btn');
                if (hasActionButtons) {
                    container.classList.remove('opacity-0');
                    container.style.opacity = '1';
                    container.style.visibility = 'visible';
                }
            });
            
            // Wire up voice message play/pause buttons with progress tracking
            messageDiv.querySelectorAll('.voice-play-toggle').forEach(playBtn => {
                const voiceId = playBtn.getAttribute('data-voice-id');
                if (!voiceId) {
                    console.warn('Voice play button missing data-voice-id');
                    return;
                }
                
                const audioEl = document.getElementById(voiceId);
                if (!audioEl) {
                    console.warn('Audio element not found for voice ID:', voiceId);
                    return;
                }
                
                // Verify audio source and ensure it's set correctly
                const sourceEl = audioEl.querySelector('source');
                if (sourceEl) {
                    const audioUrl = sourceEl.getAttribute('src');
                    
                    // Ensure the audio element has the correct src
                    if (audioUrl && !audioEl.src) {
                        audioEl.src = audioUrl;
                    }
                    
                    // Test if URL is accessible
                    fetch(audioUrl, { method: 'HEAD' })
                        .catch(error => {
                            // Silently handle fetch errors
                        });
                }
                
                // Ensure audio element is properly set up and loaded
                if (audioEl.readyState === 0) {
                    audioEl.load();
                }
                
                // Force reload if src is set but not loading (for newly sent messages)
                setTimeout(() => {
                    if (audioEl.readyState === 0 && sourceEl && sourceEl.getAttribute('src')) {
                        audioEl.load();
                    }
                }, 500);
                
                // Add error handling
                audioEl.addEventListener('error', function(e) {
                    console.error('Audio playback error:', e);
                    const sourceEl = audioEl.querySelector('source');
                    const audioUrl = sourceEl ? sourceEl.getAttribute('src') : 'No source element';
                    console.error('Audio source URL:', audioUrl);
                    console.error('Audio error code:', audioEl.error?.code);
                    console.error('Audio error message:', audioEl.error?.message);
                    
                    // Try to get more details about the error
                    let errorMessage = 'Failed to load audio.';
                    if (audioEl.error) {
                        switch(audioEl.error.code) {
                            case 1: // MEDIA_ERR_ABORTED
                                errorMessage = 'Audio loading was aborted.';
                                break;
                            case 2: // MEDIA_ERR_NETWORK
                                errorMessage = 'Network error while loading audio.';
                                break;
                            case 3: // MEDIA_ERR_DECODE
                                errorMessage = 'Audio decoding error.';
                                break;
                            case 4: // MEDIA_ERR_SRC_NOT_SUPPORTED
                                errorMessage = 'Audio format not supported.';
                                break;
                        }
                    }
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Audio Error',
                            html: `${errorMessage}<br><small>URL: ${audioUrl}</small>`,
                            timer: 5000,
                            showConfirmButton: true
                        });
                    }
                });
                
                // Add canplay event to verify audio can play
                audioEl.addEventListener('canplay', function() {
                    console.log('Audio can play:', voiceId);
                });
                
                if (audioEl) {
                    const playIcon = playBtn.querySelector('.play-icon');
                    const pauseIcon = playBtn.querySelector('.pause-icon');
                    const container = playBtn.closest('.voice-message-container');
                    const waveformBars = container?.querySelectorAll('.waveform-bar');
                    const durationEl = container?.querySelector('.voice-duration');
                    const speedBtn = container?.querySelector('.voice-speed-toggle');
                    
                    // Initialize playback speed
                    let playbackSpeed = 1;
                    if (speedBtn) {
                        speedBtn.setAttribute('data-speed', '1');
                    }
                    
                    // Update waveform progress
                    const updateWaveformProgress = () => {
                        if (!audioEl.duration || !waveformBars) return;
                        const progress = audioEl.currentTime / audioEl.duration;
                        const barsToHighlight = Math.floor(progress * waveformBars.length);
                        
                        waveformBars.forEach((bar, index) => {
                            if (index < barsToHighlight) {
                                bar.style.opacity = '1';
                            } else {
                                const originalOpacity = bar.getAttribute('data-original-opacity') || '0.8';
                                bar.style.opacity = originalOpacity;
                            }
                        });
                    };
                    
                    // Update duration display
                    const updateDuration = () => {
                        if (!durationEl || !audioEl.duration) return;
                        const total = Math.floor(audioEl.duration);
                        const totalMins = Math.floor(total / 60);
                        const totalSecs = total % 60;
                        const totalDuration = `${totalMins}:${totalSecs.toString().padStart(2, '0')}`;
                        
                        if (!audioEl.paused && audioEl.currentTime > 0) {
                            // Show current time / total time when playing
                            const current = Math.floor(audioEl.currentTime);
                            const currentMins = Math.floor(current / 60);
                            const currentSecs = current % 60;
                            durationEl.textContent = `${currentMins}:${currentSecs.toString().padStart(2, '0')} / ${totalDuration}`;
                        } else if (audioEl.currentTime === 0) {
                            // Show 0:00 / total duration format when not playing
                            durationEl.textContent = `0:00 / ${totalDuration}`;
                        } else {
                            // Show current / total when paused
                            const current = Math.floor(audioEl.currentTime);
                            const currentMins = Math.floor(current / 60);
                            const currentSecs = current % 60;
                            durationEl.textContent = `${currentMins}:${currentSecs.toString().padStart(2, '0')} / ${totalDuration}`;
                        }
                    };
                    
                    // Store original opacity values
                    if (waveformBars) {
                        waveformBars.forEach(bar => {
                            const computed = window.getComputedStyle(bar);
                            bar.setAttribute('data-original-opacity', computed.opacity);
                        });
                    }
                    
                    // Load metadata and update total duration
                    audioEl.addEventListener('loadedmetadata', function() {
                        if (audioEl.duration && !isNaN(audioEl.duration)) {
                            const minutes = Math.floor(audioEl.duration / 60);
                            const seconds = Math.floor(audioEl.duration % 60);
                            const totalDuration = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                            if (durationEl && !audioEl.currentTime) {
                                durationEl.textContent = totalDuration;
                            }
                        }
                    });
                    
                    // Play/pause button handler
                    playBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Stop all other audio
                        document.querySelectorAll('.voice-audio').forEach(audio => {
                            if (audio !== audioEl && !audio.paused) {
                                audio.pause();
                                audio.currentTime = 0;
                                const otherBtn = document.querySelector(`[data-voice-id="${audio.id}"]`);
                                if (otherBtn) {
                                    const otherPlayIcon = otherBtn.querySelector('.play-icon');
                                    const otherPauseIcon = otherBtn.querySelector('.pause-icon');
                                    if (otherPlayIcon) otherPlayIcon.classList.remove('hidden');
                                    if (otherPauseIcon) otherPauseIcon.classList.add('hidden');
                                }
                                // Reset other waveforms
                                const otherContainer = audio.closest('.voice-message-container');
                                if (otherContainer) {
                                    const otherBars = otherContainer.querySelectorAll('.waveform-bar');
                                    otherBars.forEach(bar => {
                                        const originalOpacity = bar.getAttribute('data-original-opacity') || '0.8';
                                        bar.style.opacity = originalOpacity;
                                    });
                                }
                            }
                        });
                        
                        if (audioEl.paused) {
                            audioEl.play().catch(err => console.error('Error playing audio:', err));
                            if (playIcon) playIcon.classList.add('hidden');
                            if (pauseIcon) pauseIcon.classList.remove('hidden');
                        } else {
                            audioEl.pause();
                            if (playIcon) playIcon.classList.remove('hidden');
                            if (pauseIcon) pauseIcon.classList.add('hidden');
                        }
                    });
                    
                    // Progress tracking
                    audioEl.addEventListener('timeupdate', function() {
                        updateWaveformProgress();
                        updateDuration();
                    });
                    
                    // When audio ends
                    audioEl.addEventListener('ended', function() {
                        if (playIcon) playIcon.classList.remove('hidden');
                        if (pauseIcon) pauseIcon.classList.add('hidden');
                        audioEl.currentTime = 0;
                        updateWaveformProgress();
                        if (durationEl && audioEl.duration) {
                            const totalMins = Math.floor(audioEl.duration / 60);
                            const totalSecs = Math.floor(audioEl.duration % 60);
                            durationEl.textContent = `${totalMins}:${totalSecs.toString().padStart(2, '0')}`;
                        }
                    });
                    
                    // Playback speed control
                    if (speedBtn) {
                        speedBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const speeds = [1, 1.5, 2];
                            const currentSpeed = parseFloat(this.getAttribute('data-speed')) || 1;
                            const currentIndex = speeds.indexOf(currentSpeed);
                            const nextIndex = (currentIndex + 1) % speeds.length;
                            const nextSpeed = speeds[nextIndex];
                            
                            audioEl.playbackRate = nextSpeed;
                            this.setAttribute('data-speed', nextSpeed.toString());
                            this.textContent = `${nextSpeed}x`;
                        });
                    }
                }
            });
            
            // Wire up reply indicator click
            const replyIndicatorEl = messageDiv.querySelector('.reply-to-message');
            if (replyIndicatorEl) {
                // Use parent_id from msg or fallback to data attribute
                const parentId = msg.parent_id || replyIndicatorEl.getAttribute('data-parent-id');
                if (parentId) {
                    replyIndicatorEl.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        const targetParentId = this.getAttribute('data-parent-id') || parentId;
                        if (targetParentId) {
                            scrollToParentMessage(parseInt(targetParentId));
                        }
                        return false;
                    });
                }
            }
            
            // Wire up react, reply, and delete buttons
            const reactBtn = messageDiv.querySelector('.message-react-btn');
            const replyBtn = messageDiv.querySelector('.message-reply-btn');
            const deleteBtn = messageDiv.querySelector('.message-delete-btn');
            
            if (reactBtn) {
                reactBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    handleMessageReact(msg.id);
                });
            }
            if (replyBtn) {
                replyBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    handleMessageReply(msg.id, userId);
                });
            }
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    handleMessageDelete(msg.id, messageDiv);
                });
            }
            
            // Wire up reactions click
            const reactionsDiv = messageDiv.querySelector('.message-reactions');
            if (reactionsDiv) {
                reactionsDiv.addEventListener('click', function(e) {
                    e.stopPropagation();
                    showReactionsModal(msg.id, msg.reactions || []);
                });
            }
            
            // Only auto-scroll to bottom if not scrolling to a parent message
            if (!isScrollingToParent) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        }

        // Function to append a temporary "sending" message
        function appendSendingMessage(messageText, attachments, parentId) {
            const messagesArea = document.getElementById('chatMessagesArea');
            if (!messagesArea) return null;
            
            const tempId = 'temp-' + Date.now();
            const now = new Date().toISOString();
            
            // Get last message for timestamp separator check
            const lastMessage = messagesArea.querySelector('[data-message-id]:last-of-type');
            const previousMsg = lastMessage ? {
                created_at: lastMessage.getAttribute('data-created-at')
            } : null;
            
            // Check if we need to add a timestamp separator
            if (shouldShowTimestampSeparator({ created_at: now }, previousMsg)) {
                const separator = createTimestampSeparator(now);
                // Mark this separator as potentially temporary (will be reused if timestamps match)
                separator.setAttribute('data-temp-separator', 'true');
                separator.setAttribute('data-separator-timestamp', formatTimestampSeparator(now));
                separator.setAttribute('data-created-at', now);
                messagesArea.appendChild(separator);
            }
            
            const senderAvatar = currentUserProfilePicture 
                ? `<img src="${currentUserProfilePicture}" alt="You" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200 flex-shrink-0">`
                : `<div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">${currentUserInitials}</div>`;
            
            let messageContent = '';
            
            // Handle text message
            if (messageText) {
                messageContent = `<div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-2 shadow-sm"><p class="text-xs">${messageText}</p></div>`;
            }
            
            // Handle attachments
            if (attachments && attachments.length > 0) {
                attachments.forEach((file, index) => {
                    if (file.type && file.type.startsWith('image/')) {
                        const imageUrl = URL.createObjectURL(file);
                        messageContent += `<div class="mt-2"><img src="${imageUrl}" alt="Attachment" class="max-w-[200px] max-h-[200px] rounded-lg object-cover"></div>`;
                    } else if (file.type && file.type.startsWith('video/')) {
                        const videoUrl = URL.createObjectURL(file);
                        messageContent += `<div class="mt-2"><video src="${videoUrl}" controls class="max-w-[200px] max-h-[200px] rounded-lg"></video></div>`;
                    } else {
                        messageContent += `<div class="mt-2 bg-gray-100 rounded-lg p-2"><p class="text-xs text-gray-700">📎 ${file.name}</p></div>`;
                    }
                });
            }
            
            // Handle reply indicator
            let replyIndicator = '';
            if (parentId) {
                const parentMessage = messagesArea.querySelector(`[data-message-id="${parentId}"]`);
                if (parentMessage) {
                    const parentText = parentMessage.querySelector('.message-content')?.textContent || 'Message';
                    replyIndicator = `<div class="mb-1 flex items-center gap-2 text-xs text-gray-400 cursor-pointer hover:text-gray-600 transition-colors reply-to-message" data-parent-id="${parentId}">
                        <svg class="w-3 h-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <span class="truncate">You: ${parentText.substring(0, 30)}${parentText.length > 30 ? '...' : ''}</span>
                    </div>`;
                }
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex items-start space-x-2 justify-end';
            messageDiv.setAttribute('data-message-id', tempId);
            messageDiv.setAttribute('data-temp-message', 'true');
            messageDiv.setAttribute('data-created-at', now);
            
            messageDiv.innerHTML = `
                <div class="flex-1 flex justify-end items-start gap-2 group">
                    <div class="flex items-center gap-0" style="opacity: 1; visibility: visible;">
                        <button class="message-react-btn p-0.5 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-full transition" data-message-id="${tempId}" title="React" disabled>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                        <button class="message-reply-btn p-0.5 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-full transition" data-message-id="${tempId}" title="Reply" disabled>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                        </button>
                        <button class="message-delete-btn p-0.5 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-full transition" data-message-id="${tempId}" title="Delete" disabled>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="max-w-[75%] relative">
                        ${replyIndicator}
                        <div class="space-y-2">${messageContent}</div>
                        <div class="flex justify-end mt-1">
                            <div class="text-xs text-gray-400 italic">Sending...</div>
                        </div>
                    </div>
                </div>
                ${senderAvatar}
            `;
            
            messagesArea.appendChild(messageDiv);
            
            // Apply theme to newly added "sending" message if theme is active
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            const currentThemeId = chatMessagesArea?.getAttribute('data-theme-id');
            const currentThemeGroupId = chatMessagesArea?.getAttribute('data-theme-group-id');
            const isGroupChat = currentChatUserId && currentChatUserId.startsWith('group_');
            const groupId = isGroupChat ? currentChatUserId.replace('group_', '') : null;
            
            if (currentThemeId && currentThemeGroupId === groupId && availableThemes.length > 0) {
                const theme = availableThemes.find(t => t.id === currentThemeId);
                if (theme) {
                    // Apply theme to this specific message
                    applyThemeToMessage(messageDiv, theme, true);
                }
            }
            
            // Scroll to bottom
            setTimeout(() => {
                if (messagesArea && !isScrollingToParent) {
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }
            }, 100);
            
            return tempId;
        }
        
        // Function to update temp message to show error
        function updateTempMessageError(tempId, errorMessage) {
            const tempMessage = document.querySelector(`[data-message-id="${tempId}"][data-temp-message="true"]`);
            if (!tempMessage) return;
            
            const sendingDiv = tempMessage.querySelector('.text-xs.text-gray-400');
            if (sendingDiv) {
                sendingDiv.className = 'text-xs text-red-500';
                sendingDiv.textContent = `Failed: ${errorMessage}`;
            }
            
            // Make message semi-transparent to indicate error
            tempMessage.style.opacity = '0.6';
        }
        
        // Function to remove temp message
        // Function to remove temp message and mark its associated separator if needed
        function removeTempMessage(tempId) {
            const tempMessage = document.querySelector(`[data-message-id="${tempId}"][data-temp-message="true"]`);
            if (tempMessage) {
                // Check if there's a timestamp separator right before this temp message
                const previousSibling = tempMessage.previousElementSibling;
                if (previousSibling && previousSibling.classList.contains('flex') && 
                    previousSibling.classList.contains('items-center') && 
                    previousSibling.classList.contains('justify-center') &&
                    previousSibling.classList.contains('my-4')) {
                    // This is likely a timestamp separator - mark it for reuse check
                    const separatorTimestamp = previousSibling.querySelector('span')?.textContent || '';
                    const tempCreatedAt = tempMessage.getAttribute('data-created-at');
                    previousSibling.setAttribute('data-temp-separator', 'true');
                    previousSibling.setAttribute('data-separator-timestamp', separatorTimestamp);
                    if (tempCreatedAt) {
                        previousSibling.setAttribute('data-created-at', tempCreatedAt);
                    }
                }
                tempMessage.remove();
            }
        }

        function sendMessage() {
            if (!currentChatUserId || currentChatUserId.trim() === '') {
                console.error('No chat user selected');
                return;
            }
            
            const messageInput = document.getElementById('messageInput');
            const messageText = messageInput.value.trim();
            
            if (!messageText && attachedFiles.length === 0) return;
            
            // Store values before clearing
            const textToSend = messageText;
            const filesToSend = [...attachedFiles];
            const parentIdToSend = replyToMessageId;
            
            // Clear input immediately
            messageInput.value = '';
            const filePreview = document.getElementById('filePreview');
            if (filePreview) {
                filePreview.classList.add('hidden');
                filePreview.innerHTML = '';
            }
            
            // Append temporary "sending" message
            const tempId = appendSendingMessage(textToSend, filesToSend, parentIdToSend);
            
            // Clear attached files and reply state
            attachedFiles = [];
            replyToMessageId = null;
            const replyIndicator = document.getElementById('replyIndicator');
            if (replyIndicator) {
                replyIndicator.classList.add('hidden');
            }
            
            // Restore margin-top when reply is closed and files are cleared
            const messageInputContainer = document.getElementById('messageForm')?.closest('.flex-shrink-0');
            if (messageInputContainer) {
                messageInputContainer.style.removeProperty('margin-top');
            }
            
            const formData = new FormData();
            // Check if this is a group chat
            const isGroup = currentChatUserId?.startsWith('group_');
            if (isGroup) {
                const groupId = currentChatUserId.replace('group_', '').trim();
                if (!groupId) {
                    console.error('Invalid group ID');
                    if (tempId) removeTempMessage(tempId);
                    return;
                }
                formData.append('group_id', groupId);
            } else {
                const receiverId = currentChatUserId.trim();
                if (!receiverId) {
                    console.error('Invalid receiver ID');
                    if (tempId) removeTempMessage(tempId);
                    return;
                }
                formData.append('receiver_id', receiverId);
            }
            if (textToSend) {
                formData.append('message', textToSend);
            }
            if (parentIdToSend) {
                formData.append('parent_id', parentIdToSend);
            }
            
            filesToSend.forEach(file => {
                formData.append('attachments[]', file);
            });
            
            axios.post('{{ route("messages.send") }}', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then(response => {
                if (response.data.success) {
                    // Remove temp message
                    if (tempId) {
                        removeTempMessage(tempId);
                    }
                    
                    // Reset file input to allow re-attaching the same file
                    const fileInput = document.getElementById('fileInput');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                    
                    // Get message data if available
                    let msg = null;
                    if (response.data.message) {
                        msg = response.data.message;
                        
                        // Get the last message for timestamp separator check
                        const messagesArea = document.getElementById('chatMessagesArea');
                        let previousMsg = null;
                        if (messagesArea) {
                            const allMessages = messagesArea.querySelectorAll('[data-message-id]');
                            if (allMessages.length > 0) {
                                const lastMessage = allMessages[allMessages.length - 1];
                                const lastMessageCreatedAt = lastMessage.getAttribute('data-created-at');
                                if (lastMessageCreatedAt) {
                                    previousMsg = {
                                        created_at: lastMessageCreatedAt
                                    };
                                }
                            }
                        }
                        
                        // Append the real message directly
                        appendMessageToPage(msg, currentChatUserId, previousMsg);
                        
                        // Update last sent message ID
                        if (msg.id) {
                            lastSentMessageId = msg.id;
                        }
                        
                        // Update last message timestamp
                        if (msg.created_at) {
                            if (!lastMessageTimestamp || msg.created_at > lastMessageTimestamp) {
                                lastMessageTimestamp = msg.created_at;
                            }
                        }
                        
                        // Scroll to bottom
                    setTimeout(() => {
                        if (messagesArea && !isScrollingToParent) {
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                        }
                        }, 100);
                    }
                    
                    // Update the conversation in the list immediately
                    if (currentChatUserId) {
                        updateConversationInList(currentChatUserId, msg, textToSend);
                    }
                    
                    // Reload conversation list to ensure it's up to date (with a slight delay to allow server to process)
                    setTimeout(() => {
                        loadConversations();
                    }, 500);
                    
                    // Update badges in real-time after sending message
                    setTimeout(() => {
                        updateUnreadCounts();
                        // Also call the unread-count API directly for header badge
                        if (typeof window.loadAdminUnreadCount === 'function') {
                            window.loadAdminUnreadCount();
                        } else if (typeof loadAdminUnreadCount === 'function') {
                            loadAdminUnreadCount();
                        }
                    }, 800);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                let errorMessage = 'Failed to send message. Please try again.';
                
                // Extract error message from response if available
                if (error.response && error.response.data) {
                    if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    } else if (error.response.data.error) {
                        errorMessage = error.response.data.error;
                    } else if (error.response.data.errors) {
                        // Handle validation errors
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0];
                        if (Array.isArray(firstError) && firstError.length > 0) {
                            errorMessage = firstError[0];
                        } else if (typeof firstError === 'string') {
                            errorMessage = firstError;
                        }
                    }
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                // Check for file size errors and make them more user-friendly
                if (errorMessage.includes('25600') || errorMessage.includes('kilobytes') || 
                    errorMessage.includes('too large') || errorMessage.includes('exceeds') ||
                    errorMessage.includes('must not be greater')) {
                    errorMessage = 'File is too large. Maximum allowed size is 25MB. Please choose a smaller file.';
                }
                
                // Update temp message to show error
                if (tempId) {
                    updateTempMessageError(tempId, errorMessage);
                }
                
                if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                        text: errorMessage,
                        timer: 5000,
                        showConfirmButton: true
                });
                }
            });
        }

        function loadNewMessages(userId) {
            if (!userId || !lastMessageTimestamp) return;
            
            axios.get(`{{ route('messages.new', ':userId') }}`.replace(':userId', userId) + `?since=${lastMessageTimestamp}`)
                .then(response => {
                    if (response.data.success && response.data.messages && response.data.messages.length > 0) {
                        const messagesArea = document.getElementById('chatMessagesArea');
                        if (!messagesArea) return;
                        
                        // Check if user is near bottom (within 100px) before loading new messages
                        const isNearBottom = messagesArea.scrollHeight - messagesArea.scrollTop - messagesArea.clientHeight < 100;
                        
                        // Get the last message element to check for timestamp separator
                        const allMessages = messagesArea.querySelectorAll('[data-message-id]');
                        let previousMsg = null;
                        if (allMessages.length > 0) {
                            const lastMessageElement = allMessages[allMessages.length - 1];
                            const lastMessageId = lastMessageElement.getAttribute('data-message-id');
                            const lastMessageCreatedAt = lastMessageElement.getAttribute('data-created-at');
                            if (lastMessageId && lastMessageCreatedAt) {
                                previousMsg = {
                                    id: lastMessageId,
                                    created_at: lastMessageCreatedAt
                                };
                            }
                        }
                        
                        // Track if we're adding new messages
                        let hasNewMessages = false;
                        
                        response.data.messages.forEach(msg => {
                            // Check if message already exists to avoid duplicates
                            const existingMessage = messagesArea.querySelector(`[data-message-id="${msg.id}"]`);
                            if (!existingMessage) {
                            appendMessageToPage(msg, userId, previousMsg);
                            previousMsg = msg;
                                hasNewMessages = true;
                                
                            if (msg.created_at > lastMessageTimestamp) {
                                lastMessageTimestamp = msg.created_at;
                            }
                            // Update last sent message ID if this is a sent message
                            if (msg.is_sender) {
                                if (!lastSentMessageId) {
                                    lastSentMessageId = msg.id;
                                } else {
                                    // Compare timestamps to find the most recent
                                    const currentLastMsg = response.data.messages.find(m => m.id == lastSentMessageId);
                                    if (currentLastMsg && new Date(msg.created_at) > new Date(currentLastMsg.created_at)) {
                                        lastSentMessageId = msg.id;
                                    } else if (!currentLastMsg) {
                                        // If current last message not found, use this one
                                        lastSentMessageId = msg.id;
                                    }
                                }
                                }
                            } else {
                                // Message already exists, update previousMsg for next iteration
                                previousMsg = msg;
                            }
                        });
                        
                        // Only auto-scroll to bottom if:
                        // 1. Not scrolling to a parent message
                        // 2. User was near bottom before new messages loaded
                        // 3. We actually added new messages
                        if (hasNewMessages && !isScrollingToParent && isNearBottom) {
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                            userHasScrolledUp = false;
                        }
                        
                        // Get the latest message to update conversation list
                        const latestMessage = response.data.messages[response.data.messages.length - 1];
                        
                        // Update the conversation in the list and move to top
                        if (userId && latestMessage) {
                            updateConversationInList(userId, latestMessage, latestMessage.message || '');
                        }
                        
                        // Update unread counts when new messages arrive
                        updateUnreadCounts();
                        
                        // Also call the unread-count API directly for header badge
                        if (typeof loadAdminUnreadCount === 'function') {
                            loadAdminUnreadCount();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading new messages:', error);
                });
        }

        function loadUsersForSelection() {
            axios.get('{{ route("messages.users") }}')
                .then(response => {
                    if (response.data.success) {
                        renderUsersForSelection(response.data.users);
                    }
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                });
        }

        // Global selected users array for user selection modal
        let selectedUsers = [];

        function renderUsersForSelection(users) {
            const container = document.getElementById('usersListContainer');
            
            // Reset selected users when rendering (but preserve if modal is still open)
            // Only reset if modal is hidden
            const userModal = document.getElementById('userSelectionModal');
            if (userModal && userModal.classList.contains('hidden')) {
                selectedUsers = [];
            }
            
            // Filter out users who are already members of the group when adding members
            let filteredUsers = users;
            if (window.addingMembersToGroup && currentGroupData && currentGroupData.members) {
                const existingMemberIds = currentGroupData.members.map(m => m.id);
                filteredUsers = users.filter(user => !existingMemberIds.includes(user.id));
            }
            
            // Group users by privilege first, then by agency
            const groupedUsers = {};
            filteredUsers.forEach(user => {
                const privilege = user.privilege || 'No Privilege';
                const agency = user.agency || 'No Agency';
                
                if (!groupedUsers[privilege]) {
                    groupedUsers[privilege] = {};
                }
                if (!groupedUsers[privilege][agency]) {
                    groupedUsers[privilege][agency] = [];
                }
                groupedUsers[privilege][agency].push(user);
            });
            
            // Sort privileges
            const privilegeOrder = ['admin', 'consec', 'user'];
            const sortedPrivileges = Object.keys(groupedUsers).sort((a, b) => {
                const aIndex = privilegeOrder.indexOf(a.toLowerCase());
                const bIndex = privilegeOrder.indexOf(b.toLowerCase());
                if (aIndex !== -1 && bIndex !== -1) return aIndex - bIndex;
                if (aIndex !== -1) return -1;
                if (bIndex !== -1) return 1;
                return a.localeCompare(b);
            });
            
            let html = '';
            sortedPrivileges.forEach(privilege => {
                const agencies = groupedUsers[privilege];
                const sortedAgencies = Object.keys(agencies).sort((a, b) => {
                    if (a === 'No Agency') return 1;
                    if (b === 'No Agency') return -1;
                    return a.localeCompare(b);
                });
                
                sortedAgencies.forEach(agency => {
                    const users = agencies[agency];
                    const privilegeText = privilege.charAt(0).toUpperCase() + privilege.slice(1);
                    const agencyText = agency;
                    
                    html += `
                        <div class="sticky top-0 bg-gray-100 px-4 py-2 border-b border-gray-200 z-10">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                ${privilegeText}${agencyText !== 'No Agency' ? ` • ${agencyText}` : ''}
                            </p>
                        </div>
                    `;
                    
                    users.forEach(user => {
                        const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
                        const initials = (user.first_name?.[0] || '') + (user.last_name?.[0] || '');
                        const isOnline = user.is_online || false;
                        const indicatorColor = isOnline ? '#3fbb46' : '#9ca3af';
                        
                        let avatarHtml = '';
                        if (user.profile_picture_url) {
                            avatarHtml = `<img src="${user.profile_picture_url}" alt="${fullName}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">`;
                        } else {
                            const gradientColors = ['from-purple-400 to-purple-600', 'from-blue-400 to-blue-600', 'from-green-400 to-green-600', 'from-indigo-400 to-indigo-600', 'from-yellow-400 to-orange-500', 'from-pink-400 to-pink-600', 'from-red-400 to-red-600'];
                            const colorIndex = (user.id?.toString().charCodeAt(0) || 0) % gradientColors.length;
                            avatarHtml = `<div class="w-12 h-12 rounded-full bg-gradient-to-br ${gradientColors[colorIndex]} flex items-center justify-center text-white font-semibold text-sm">${initials}</div>`;
                        }
                        
                        // Store full user object as JSON data attribute
                        const userDataJson = JSON.stringify({
                            id: user.id,
                            first_name: user.first_name,
                            last_name: user.last_name,
                            profile_picture_url: user.profile_picture_url,
                            is_online: user.is_online,
                            position: user.position,
                            privilege: user.privilege,
                            agency: user.agency
                        });
                        
                        html += `
                            <div class="p-3 hover:bg-gray-50 cursor-pointer user-item border-b border-gray-100" data-user-id="${user.id}" data-user-name="${fullName}" data-user-data='${userDataJson.replace(/'/g, "&#39;")}'>
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" data-user-id="${user.id}" data-user-name="${fullName}" data-user-data='${userDataJson.replace(/'/g, "&#39;")}'>
                                    <div class="relative flex-shrink-0">
                                        ${avatarHtml}
                                        <div class="absolute bottom-0 right-0 w-3 h-3 ${indicatorColor} rounded-full border-2 border-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">${fullName}</p>
                                        ${user.position ? `<p class="text-xs text-gray-500 truncate">${user.position}</p>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                });
            });
            
            container.innerHTML = html;
            
            // Handle checkbox selection
            const updateSelectedCount = () => {
                const count = selectedUsers.length;
                document.getElementById('selectedCount').textContent = count;
                
                const createGroupBtn = document.getElementById('createGroupBtn');
                const openChatBtn = document.getElementById('openChatBtn');
                const openChatBtnText = document.getElementById('openChatBtnText');
                
                // Check if we're adding members to an existing group
                if (window.addingMembersToGroup) {
                    if (count >= 1) {
                        // Show "Add Selected Members" button (allow adding even 1 user)
                        if (createGroupBtn) {
                            createGroupBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Add Selected Members';
                            createGroupBtn.classList.remove('hidden');
                        }
                        if (openChatBtn) {
                            openChatBtn.classList.add('hidden');
                        }
                    } else {
                        // Hide both buttons
                        if (createGroupBtn) {
                            createGroupBtn.classList.add('hidden');
                        }
                        if (openChatBtn) {
                            openChatBtn.classList.add('hidden');
                        }
                    }
                } else {
                    // Normal flow (creating new group or opening chat)
                    if (count > 1) {
                        // Show create group button
                        if (createGroupBtn) {
                            createGroupBtn.innerHTML = '<i class="fas fa-users mr-2"></i>Create Group';
                            createGroupBtn.classList.remove('hidden');
                        }
                        if (openChatBtn) {
                            openChatBtn.classList.add('hidden');
                        }
                    } else if (count === 1) {
                        // Show open/create chat button
                        if (createGroupBtn) {
                            createGroupBtn.classList.add('hidden');
                        }
                        if (openChatBtn && openChatBtnText) {
                            // Check if conversation exists with this user
                            const selectedUser = selectedUsers[0];
                            const userId = selectedUser.id;
                            const conversationExists = conversationsData.some(conv => conv.user_id === userId);
                            
                            if (conversationExists) {
                                openChatBtnText.textContent = 'Open Chat';
                            } else {
                                openChatBtnText.textContent = 'Create Chat';
                            }
                            openChatBtn.classList.remove('hidden');
                        }
                    } else {
                        // Hide both buttons
                        if (createGroupBtn) {
                            createGroupBtn.classList.add('hidden');
                        }
                        if (openChatBtn) {
                            openChatBtn.classList.add('hidden');
                        }
                    }
                }
            };
            
            container.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function(e) {
                    e.stopPropagation();
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    const userDataJson = this.getAttribute('data-user-data');
                    
                    if (this.checked) {
                        // Check if already selected to avoid duplicates
                        if (!selectedUsers.find(u => u.id === userId)) {
                            try {
                                const userData = JSON.parse(userDataJson);
                                selectedUsers.push({
                                    id: userId,
                                    name: userName,
                                    data: userData
                                });
                            } catch (e) {
                                console.error('Error parsing user data:', e);
                            }
                        }
                    } else {
                        selectedUsers = selectedUsers.filter(u => u.id !== userId);
                    }
                    
                    // Update select all checkbox state
                    const selectAllCheckbox = document.getElementById('selectAllUsers');
                    if (selectAllCheckbox) {
                        const allCheckboxes = container.querySelectorAll('.user-checkbox');
                        const checkedCount = container.querySelectorAll('.user-checkbox:checked').length;
                        selectAllCheckbox.checked = checkedCount === allCheckboxes.length && allCheckboxes.length > 0;
                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
                    }
                    
                    updateSelectedCount();
                });
            });
            
            // Handle select all checkbox
            const selectAllCheckbox = document.getElementById('selectAllUsers');
            if (selectAllCheckbox) {
                // Remove old listener by cloning
                const newSelectAllCheckbox = selectAllCheckbox.cloneNode(true);
                selectAllCheckbox.parentNode.replaceChild(newSelectAllCheckbox, selectAllCheckbox);
                
                newSelectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = container.querySelectorAll('.user-checkbox');
                    const isChecked = this.checked;
                    
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                        // Trigger change event to update selectedUsers array
                        checkbox.dispatchEvent(new Event('change'));
                    });
                });
            }
            
            container.querySelectorAll('.user-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking on checkbox
                    if (e.target.type === 'checkbox') return;
                    
                    const checkbox = this.querySelector('.user-checkbox');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    } else {
                        // Fallback: single user selection (old behavior)
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                        const userDataJson = this.getAttribute('data-user-data');
                        
                        let userData = null;
                        try {
                            userData = JSON.parse(userDataJson);
                        } catch (e) {
                            console.error('Error parsing user data:', e);
                        }
                        
                        const convData = userData ? {
                            user_id: userData.id,
                            profile_picture_url: userData.profile_picture_url,
                            is_online: userData.is_online || false,
                            user_initials: (userData.first_name?.[0] || '') + (userData.last_name?.[0] || ''),
                            position: userData.position,
                            privilege: userData.privilege,
                            agency: userData.agency
                        } : null;
                        
                    document.getElementById('userSelectionModal').classList.add('hidden');
                        openChat(userId, userName, convData);
                    }
                });
            });
            
            // Handle open/create chat button (single user)
            // Remove old listener by cloning the button
            const openChatBtn = document.getElementById('openChatBtn');
            if (openChatBtn) {
                const newOpenChatBtn = openChatBtn.cloneNode(true);
                openChatBtn.parentNode.replaceChild(newOpenChatBtn, openChatBtn);
                
                newOpenChatBtn.addEventListener('click', function() {
                    // Get current selectedUsers from the closure
                    if (selectedUsers.length !== 1) {
                        console.log('Selected users count:', selectedUsers.length);
                        return;
                    }
                    
                    const selectedUser = selectedUsers[0];
                    if (!selectedUser) {
                        console.error('No selected user found');
                        return;
                    }
                    
                    const userId = selectedUser.id;
                    const userName = selectedUser.name;
                    const userData = selectedUser.data;
                    
                    if (!userData) {
                        console.error('No user data found');
                        return;
                    }
                    
                    // Prepare conversation data
                    const convData = {
                        user_id: userData.id,
                        profile_picture_url: userData.profile_picture_url,
                        is_online: userData.is_online || false,
                        user_initials: (userData.first_name?.[0] || '') + (userData.last_name?.[0] || ''),
                        position: userData.position,
                        privilege: userData.privilege,
                        agency: userData.agency
                    };
                    
                    // Close modal and reset
                    document.getElementById('userSelectionModal').classList.add('hidden');
                    selectedUsers = [];
                    updateSelectedCount();
                    container.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
                    
                    // Open the chat
                    openChat(userId, userName, convData);
                });
            }
            
            // Handle create group button
            // Remove old listener by cloning the button
            const createGroupBtn = document.getElementById('createGroupBtn');
            if (createGroupBtn) {
                const newCreateGroupBtn = createGroupBtn.cloneNode(true);
                createGroupBtn.parentNode.replaceChild(newCreateGroupBtn, createGroupBtn);
                
                newCreateGroupBtn.addEventListener('click', function() {
                    // Get current selectedUsers from the global scope
                    console.log('Selected users count:', selectedUsers.length);
                    console.log('Selected users:', selectedUsers);
                    
                    // Check if we're adding members to an existing group
                    if (window.addingMembersToGroup && window.targetGroupId) {
                        if (selectedUsers.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Users Selected',
                                text: 'Please select at least one user to add to the group.'
                            });
                            return;
                        }
                        
                        // Add members to existing group
                        axios.post(`{{ route('messages.groups.members.add', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.targetGroupId), {
                            user_ids: selectedUsers.map(u => u.id)
                        })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Members Added',
                                    text: 'Members added successfully!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                
                                // Close modal and reset
                                document.getElementById('userSelectionModal').classList.add('hidden');
                                resetUserSelectionModal();
                                
                                // Reload group details and reopen settings
                                loadGroupDetails(window.currentGroupId);
                                setTimeout(() => {
                                    openGroupSettings();
                                }, 500);
                            }
                        })
                        .catch(error => {
                            console.error('Error adding members:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.response?.data?.message || 'Failed to add members. Please try again.'
                            });
                        });
                        return;
                    }
                    
                    // Normal flow: creating a new group
                    if (selectedUsers.length < 2) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select Multiple Users',
                            text: 'Please select at least 2 users to create a group chat.'
                        });
                        return;
                    }
                    
                    // Show selected members in create group modal
                    const selectedMembersList = document.getElementById('selectedMembersList');
                    if (selectedMembersList) {
                        selectedMembersList.innerHTML = selectedUsers.map(user => {
                            const initials = (user.data.first_name?.[0] || '') + (user.data.last_name?.[0] || '');
                            const avatar = user.data.profile_picture_url 
                                ? `<img src="${user.data.profile_picture_url}" alt="${user.name}" class="w-8 h-8 rounded-full object-cover">`
                                : `<div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">${initials}</div>`;
                            
                            return `
                                <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                    <div class="relative flex-shrink-0">${avatar}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">${escapeHtml(user.name)}</p>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }
                    
                    // Show create group modal
                    document.getElementById('userSelectionModal').classList.add('hidden');
                    document.getElementById('createGroupModal').classList.remove('hidden');
                });
            }
            
            // Handle create group confirmation
            const confirmGroupBtn = document.getElementById('confirmGroupBtn');
            if (confirmGroupBtn) {
                confirmGroupBtn.addEventListener('click', function() {
                    const groupName = document.getElementById('groupNameInput').value.trim();
                    const groupDescription = document.getElementById('groupDescriptionInput').value.trim();
                    
                    if (!groupName) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Group Name Required',
                            text: 'Please enter a group name.'
                        });
                        return;
                    }
                    
                    if (selectedUsers.length < 2) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Insufficient Members',
                            text: 'Please select at least 2 users to create a group chat.'
                        });
                        return;
                    }
                    
                    // Disable button during creation
                    this.disabled = true;
                    this.textContent = 'Creating...';
                    
                    // Create group
                    axios.post('{{ route("messages.groups.create") }}', {
                        name: groupName,
                        description: groupDescription,
                        member_ids: selectedUsers.map(u => u.id)
                    })
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Group Created',
                                text: 'Group chat created successfully!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Close modal and reset
                            document.getElementById('createGroupModal').classList.add('hidden');
                            document.getElementById('groupNameInput').value = '';
                            document.getElementById('groupDescriptionInput').value = '';
                            selectedUsers = [];
                            updateSelectedCount();
                            
                            // Reload conversations to show new group
                            loadConversations();
                            
                            // Open the new group chat
                            if (response.data.group) {
                                const group = response.data.group;
                                setTimeout(() => {
                                    openChat('group_' + group.id, group.name, {
                                        user_id: 'group_' + group.id,
                                        user_name: group.name,
                                        user_initials: group.name.substring(0, 2).toUpperCase(),
                                        profile_picture_url: group.avatar || null,
                                        is_group: true
                                    });
                                }, 500);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error creating group:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.response?.data?.message || 'Failed to create group chat. Please try again.'
                        });
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.textContent = 'Create Group';
                    });
                });
            }
            
            // Handle cancel group creation
            const cancelGroupBtn = document.getElementById('cancelGroupBtn');
            const closeGroupModal = document.getElementById('closeGroupModal');
            [cancelGroupBtn, closeGroupModal].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', function() {
                        document.getElementById('createGroupModal').classList.add('hidden');
                        document.getElementById('userSelectionModal').classList.remove('hidden');
                    });
                }
            });
            
            // Reset selected users when modal is closed
            const closeUserModal = document.getElementById('closeUserModal');
            if (closeUserModal) {
                // Remove old listener by cloning
                const newCloseBtn = closeUserModal.cloneNode(true);
                closeUserModal.parentNode.replaceChild(newCloseBtn, closeUserModal);
                
                newCloseBtn.addEventListener('click', function() {
                    document.getElementById('userSelectionModal').classList.add('hidden');
                    
                    // If opened from Group Settings, reopen Group Settings modal
                    if (window.addingMembersToGroup) {
                        const groupSettingsModal = document.getElementById('groupSettingsModal');
                        if (groupSettingsModal) {
                            groupSettingsModal.classList.remove('hidden');
                        }
                    }
                    
                    resetUserSelectionModal();
                });
            }
            
            // Add search functionality
            const userSearchInput = document.getElementById('userSearchInput');
            if (userSearchInput) {
                userSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const userItems = container.querySelectorAll('.user-item');
                    const sectionHeaders = container.querySelectorAll('.sticky');
                    
                    if (searchTerm === '') {
                        // Show all users and headers
                        userItems.forEach(item => {
                            item.style.display = '';
                        });
                        sectionHeaders.forEach(header => {
                            header.style.display = '';
                        });
                    } else {
                        // Filter users
                        userItems.forEach(item => {
                            const userName = item.getAttribute('data-user-name').toLowerCase();
                            const userDataJson = item.getAttribute('data-user-data');
                            let matches = userName.includes(searchTerm);
                            
                            // Also search in position and agency if available
                            if (!matches && userDataJson) {
                                try {
                                    const userData = JSON.parse(userDataJson);
                                    const position = (userData.position || '').toLowerCase();
                                    const agency = (userData.agency || '').toLowerCase();
                                    matches = position.includes(searchTerm) || agency.includes(searchTerm);
                                } catch (e) {
                                    // Ignore parse errors
                                }
                            }
                            
                            if (matches) {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        
                        // Hide/show section headers based on visible users in their section
                        sectionHeaders.forEach(header => {
                            let hasVisibleInSection = false;
                            let currentElement = header.nextElementSibling;
                            
                            // Check all siblings until we hit another header or end
                            while (currentElement && !currentElement.classList.contains('sticky')) {
                                if (currentElement.classList.contains('user-item') && currentElement.style.display !== 'none') {
                                    hasVisibleInSection = true;
                                    break;
                                }
                                currentElement = currentElement.nextElementSibling;
                            }
                            
                            if (hasVisibleInSection) {
                                header.style.display = '';
                            } else {
                                header.style.display = 'none';
                            }
                        });
                    }
                });
            }
        }

        function filterConversations(searchTerm) {
            const items = document.querySelectorAll('.conversation-item');
            const term = searchTerm.toLowerCase();
            items.forEach(item => {
                const name = item.getAttribute('data-user-name').toLowerCase();
                if (name.includes(term)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function handleFileSelect(files) {
            attachedFiles = Array.from(files);
            const preview = document.getElementById('filePreview');
            const messageInputContainer = document.getElementById('messageForm')?.closest('.flex-shrink-0');
            
            if (attachedFiles.length > 0) {
                preview.classList.remove('hidden');
                preview.innerHTML = attachedFiles.map((file, index) => {
                    const isImage = file.type.startsWith('image/');
                    const isVideo = file.type.startsWith('video/');
                    let previewContent = '';
                    
                    if (isImage) {
                        previewContent = `<img src="${URL.createObjectURL(file)}" class="w-12 h-12 object-cover rounded" alt="${file.name}">`;
                    } else if (isVideo) {
                        previewContent = `<video src="${URL.createObjectURL(file)}" class="w-12 h-12 object-cover rounded" muted></video>`;
                    } else {
                        previewContent = `<i class="fas fa-file text-2xl text-gray-500"></i>`;
                    }
                    
                    return `
                        <div class="flex items-center gap-2 p-2 bg-gray-100 rounded-lg flex-shrink-0" data-file-index="${index}">
                            ${previewContent}
                            <span class="text-xs text-gray-700 truncate max-w-[120px] sm:max-w-[150px]">${file.name}</span>
                            <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 flex-shrink-0 min-w-[32px] min-h-[32px] flex items-center justify-center" aria-label="Remove file">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }).join('');
                
                // Ensure input container stays visible by scrolling it into view on mobile
                if (messageInputContainer && window.innerWidth <= 767) {
                    setTimeout(() => {
                        messageInputContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                }
                
                // Adjust margin-top when files are attached (similar to reply indicator)
                if (messageInputContainer) {
                    // Wait for preview to render and get its height
                    setTimeout(() => {
                        const previewHeight = preview.offsetHeight || preview.scrollHeight;
                        if (previewHeight > 0) {
                            // Set margin-top to move container up (similar to reply indicator)
                            messageInputContainer.style.position = 'relative';
                            messageInputContainer.style.transform = 'translateZ(0)'; // Force GPU acceleration
                            messageInputContainer.style.setProperty('margin-top', '-72px', 'important');
                        }
                    }, 50);
                }
            } else {
                preview.classList.add('hidden');
                preview.innerHTML = ''; // Clear preview content
                
                // Restore margin-top when file preview is hidden
                if (messageInputContainer) {
                    messageInputContainer.style.removeProperty('margin-top');
                }
            }
        }

        function removeFile(index) {
            attachedFiles.splice(index, 1);
            handleFileSelect([]);
            const fileInput = document.getElementById('fileInput');
            fileInput.value = '';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getTimeAgo(timestamp) {
            if (!timestamp) return '';
            const now = new Date();
            const time = new Date(timestamp);
            const diffMs = now - time;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return time.toLocaleDateString();
        }
        
        // Helper function to get file icon for attachments
        function getFileIconForAttachment(ext) {
            if (['pdf'].includes(ext)) {
                return `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
            } else if (['doc', 'docx'].includes(ext)) {
                return `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
            } else if (['xls', 'xlsx'].includes(ext)) {
                return `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
            } else if (['zip', 'rar', '7z'].includes(ext)) {
                return `<svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,17H7V15H14M17,13H7V11H17M17,9H7V7H17M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3Z"/></svg>`;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
                return `<svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
            } else {
                return `<svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
            }
        }
        
        // Scroll to parent message when reply indicator is clicked
        function scrollToParentMessage(parentId) {
            const messagesArea = document.getElementById('chatMessagesArea');
            if (!messagesArea) return;
            
            // Set flag to prevent auto-scroll to bottom - keep it true for longer
            isScrollingToParent = true;
            
            const parentMessage = messagesArea.querySelector(`[data-message-id="${parentId}"]`);
            if (!parentMessage) {
                isScrollingToParent = false; // Reset flag
                if (window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Message not found',
                        text: 'The original message may not be loaded yet.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                return;
            }
            
            // Use getBoundingClientRect for accurate position calculation
            const messageRect = parentMessage.getBoundingClientRect();
            const containerRect = messagesArea.getBoundingClientRect();
            const currentScrollTop = messagesArea.scrollTop;
            
            // Calculate the position of the message relative to the container's scroll position
            const messageTopRelativeToContainer = messageRect.top - containerRect.top + currentScrollTop;
            
            // Adjust to position message near top with padding (100px from top)
            const topPadding = 100;
            const targetScrollTop = Math.max(0, messageTopRelativeToContainer - topPadding);
            
            // Store target position for scroll blocker
            const savedTargetScroll = targetScrollTop;
            
            // Add a scroll blocker that prevents scrolling to bottom
            const blockAutoScroll = function() {
                if (isScrollingToParent) {
                    const currentScroll = messagesArea.scrollTop;
                    const maxScroll = messagesArea.scrollHeight - messagesArea.clientHeight;
                    // If scroll is trying to go to bottom (within 100px), restore our position
                    if (Math.abs(currentScroll - maxScroll) < 100 && Math.abs(currentScroll - savedTargetScroll) > 150) {
                        messagesArea.scrollTop = savedTargetScroll;
                    }
                }
            };
            
            // Add scroll blocker
            messagesArea.addEventListener('scroll', blockAutoScroll, { passive: true });
            
            // Scroll to the calculated position
            // Use requestAnimationFrame to ensure DOM is ready
            requestAnimationFrame(() => {
                // Recalculate to ensure accuracy
                const messageRect = parentMessage.getBoundingClientRect();
                const containerRect = messagesArea.getBoundingClientRect();
                const currentScrollTop = messagesArea.scrollTop;
                const messageTopRelativeToContainer = messageRect.top - containerRect.top + currentScrollTop;
                const finalScrollTop = Math.max(0, messageTopRelativeToContainer - topPadding);
                
                // Scroll smoothly to the target position
                messagesArea.scrollTo({
                    top: finalScrollTop,
                    behavior: 'smooth'
                });
            });
            
            // Highlight the parent message after scroll completes
            setTimeout(() => {
                parentMessage.style.transition = 'background-color 0.3s ease';
                parentMessage.style.backgroundColor = 'rgba(59, 130, 246, 0.2)';
                
                setTimeout(() => {
                    parentMessage.style.backgroundColor = '';
                    setTimeout(() => {
                        parentMessage.style.transition = '';
                        // Remove scroll blocker
                        messagesArea.removeEventListener('scroll', blockAutoScroll);
                        // Keep flag true for a bit longer to prevent any delayed scrolls
                        setTimeout(() => {
                            isScrollingToParent = false;
                        }, 1000);
                    }, 300);
                }, 2000);
            }, 600);
        }
        
        // Handle message react
        function handleMessageReact(messageId) {
            const reactBtn = document.querySelector(`.message-react-btn[data-message-id="${messageId}"]`);
            if (!reactBtn) return;
            
            const existingPicker = document.querySelector('.reaction-picker-popup');
            if (existingPicker) {
                existingPicker.remove();
            }
            
            const reactionTypes = [
                { type: 'like', emoji: '👍', label: 'Like' },
                { type: 'love', emoji: '❤️', label: 'Love' },
                { type: 'haha', emoji: '😂', label: 'Haha' },
                { type: 'wow', emoji: '😮', label: 'Wow' },
                { type: 'sad', emoji: '😢', label: 'Sad' },
                { type: 'angry', emoji: '😠', label: 'Angry' }
            ];
            
            const picker = document.createElement('div');
            picker.className = 'reaction-picker-popup fixed z-[9999] bg-white rounded-xl shadow-2xl p-3 flex gap-2 border border-gray-200';
            picker.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)';
            picker.style.backgroundColor = '#ffffff';
            picker.style.borderColor = '#e5e7eb';
            
            reactionTypes.forEach(reaction => {
                const btn = document.createElement('button');
                btn.className = 'w-12 h-12 flex items-center justify-center rounded-lg hover:scale-125 hover:bg-gray-100 transition-all duration-200 cursor-pointer';
                btn.style.fontSize = '1.75rem';
                btn.textContent = reaction.emoji;
                btn.title = reaction.label;
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sendReaction(messageId, reaction.type);
                    picker.remove();
                });
                picker.appendChild(btn);
            });
            
            document.body.appendChild(picker);
            const buttonRect = reactBtn.getBoundingClientRect();
            const pickerWidth = picker.offsetWidth;
            const pickerHeight = picker.offsetHeight;
            let top = buttonRect.top - pickerHeight - 8;
            let left = buttonRect.left + (buttonRect.width / 2) - (pickerWidth / 2);
            
            if (left < 8) left = 8;
            if (left + pickerWidth > window.innerWidth - 8) left = window.innerWidth - pickerWidth - 8;
            if (top < 8) top = buttonRect.bottom + 8;
            
            picker.style.top = `${top}px`;
            picker.style.left = `${left}px`;
            
            // Remove any dark classes that might have been added
            removeDarkClasses(picker);
            
            setTimeout(() => {
                const closePicker = function(e) {
                    if (!picker.contains(e.target) && !reactBtn.contains(e.target)) {
                        picker.remove();
                        document.removeEventListener('click', closePicker);
                    }
                };
                document.addEventListener('click', closePicker);
            }, 100);
        }
        
        // Send reaction
        function sendReaction(messageId, reactionType) {
            axios.post(`/messages/${messageId}/react`, { reaction_type: reactionType })
            .then(response => {
                if (response.data.success) {
                    updateMessageReactions(messageId, response.data.reactions);
                }
            })
            .catch(error => {
                console.error('Error reacting to message:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to react to message',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Update message reactions display
        function updateMessageReactions(messageId, reactions) {
            const messageDiv = document.querySelector(`[data-message-id="${messageId}"]`);
            if (!messageDiv) return;
            
            // Determine if this is a sent or received message
            const isSender = messageDiv.classList.contains('justify-end');
            
            // Get theme for reaction colors
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            const currentThemeId = chatMessagesArea?.getAttribute('data-theme-id');
            const currentThemeGroupId = chatMessagesArea?.getAttribute('data-theme-group-id');
            const groupId = currentChatUserId && currentChatUserId.startsWith('group_') ? currentChatUserId.replace('group_', '') : null;
            
            let theme = null;
            if (currentThemeId && currentThemeGroupId === groupId && availableThemes.length > 0) {
                theme = availableThemes.find(t => t.id === currentThemeId);
            }
            
            const reactionEmojis = {
                'like': '👍', 'love': '❤️', 'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😠'
            };
            
            let reactionsDisplay = '';
            if (reactions && reactions.length > 0) {
                const reactionCounts = reactions.map(r => `${reactionEmojis[r.type] || '👍'} ${r.count}`).join(' ');
                // Use theme colors: sender_text for sent messages, receiver_text for received messages
                const reactionColor = theme 
                    ? (isSender ? theme.sender_text : theme.receiver_text)
                    : '#4b5563';
                reactionsDisplay = `<div class="flex justify-end mt-1"><div class="message-reactions flex items-center gap-1 text-xs cursor-pointer hover:opacity-80 transition" data-message-id="${messageId}" title="View reactions" style="color: ${reactionColor} !important;">${reactionCounts}</div></div>`;
            }
            
            const messageContentContainer = messageDiv.querySelector('.max-w-\\[75\\%\\]');
            if (!messageContentContainer) return;
            
            const existingReactionsWrapper = messageContentContainer.querySelector('.flex.justify-end.mt-1');
            const timestamp = messageContentContainer.querySelector('.text-xs.text-gray-400, .text-xs.text-gray-500');
            
            if (existingReactionsWrapper) {
                if (reactionsDisplay) {
                    existingReactionsWrapper.outerHTML = reactionsDisplay;
                } else {
                    existingReactionsWrapper.remove();
                }
            } else if (reactionsDisplay) {
                if (timestamp) {
                    timestamp.insertAdjacentHTML('afterend', reactionsDisplay);
                } else {
                    messageContentContainer.insertAdjacentHTML('beforeend', reactionsDisplay);
                }
            }
            
            const newReactionsDiv = messageContentContainer.querySelector('.message-reactions');
            if (newReactionsDiv) {
                const newReactionsDivClone = newReactionsDiv.cloneNode(true);
                newReactionsDiv.parentNode.replaceChild(newReactionsDivClone, newReactionsDiv);
                newReactionsDivClone.addEventListener('click', function(e) {
                    e.stopPropagation();
                    showReactionsModal(messageId, reactions || []);
                });
            }
        }
        
        // Update reactions for all visible messages in the current conversation
        function updateReactionsForVisibleMessages(userId) {
            if (!userId || !currentChatUserId || userId !== currentChatUserId) return;
            
            const messagesArea = document.getElementById('chatMessagesArea');
            if (!messagesArea) return;
            
            // Get all visible message IDs
            const messageElements = messagesArea.querySelectorAll('[data-message-id]');
            if (messageElements.length === 0) return;
            
            const messageIds = Array.from(messageElements).map(el => el.getAttribute('data-message-id'));
            
            // Use batch endpoint to fetch all reactions in a single request
            if (messageIds.length === 0) return;
            
            axios.post(`{{ route('messages.reactions.batch') }}`, {
                message_ids: messageIds
            })
            .then(response => {
                if (response.data.success && response.data.reactions) {
                    // Update reactions for each message
                    Object.keys(response.data.reactions).forEach(messageId => {
                        const reactions = response.data.reactions[messageId];
                        if (reactions && reactions.length > 0) {
                            updateMessageReactions(messageId, reactions);
                        } else {
                            // If no reactions, clear the display
                            updateMessageReactions(messageId, []);
                        }
                    });
                }
            })
            .catch(error => {
                // Silently fail - reactions might not exist for all messages
                if (error.response && error.response.status !== 404) {
                    console.debug('Error fetching batch reactions:', error);
                }
            });
        }
        
        // Update read status for the last sent message only
        function updateReadStatusForVisibleMessages(userId) {
            if (!userId || !currentChatUserId || userId !== currentChatUserId) return;
            if (!lastSentMessageId) return; // No sent messages yet
            
            const messagesArea = document.getElementById('chatMessagesArea');
            if (!messagesArea) return;
            
            // Only check the last sent message
            axios.get(`{{ route('messages.conversation', ':userId') }}`.replace(':userId', userId))
                .then(response => {
                    if (response.data.success && response.data.messages) {
                        // Find the last sent message
                        const lastSentMessage = response.data.messages
                            .filter(m => m.is_sender)
                            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
                        
                        if (lastSentMessage && lastSentMessage.id == lastSentMessageId) {
                            updateMessageReadStatus(lastSentMessageId, lastSentMessage.is_read);
                        }
                    }
                })
                .catch(error => {
                    // Silently fail - conversation might not be accessible
                    if (error.response && error.response.status !== 404) {
                        console.debug('Error fetching read status:', error);
                    }
                });
        }
        
        // Mark messages as seen when message input is focused/clicked
        function markMessagesAsSeenOnInputFocus() {
            // Only mark as read if there's an active chat
            if (!currentChatUserId) return;
            
            // Mark messages as read
            axios.post(`{{ route('messages.mark-as-read', ':userId') }}`.replace(':userId', currentChatUserId))
                .then((response) => {
                    if (response.data.success) {
                        // Update unread count immediately for this conversation
                        const conv = conversationsData.find(c => c.user_id === currentChatUserId);
                        if (conv) {
                            conv.unread_count = 0;
                        }
                        
                        // Update the badge in UI immediately
                        const conversationItem = document.querySelector(`[data-user-id="${currentChatUserId}"].conversation-item`);
                        if (conversationItem) {
                            const headerDiv = conversationItem.querySelector('.flex.items-center.justify-between.mb-1');
                            if (headerDiv) {
                                const badge = headerDiv.querySelector('.bg-red-500');
                                if (badge) {
                                    badge.remove();
                                }
                            }
                        }
                        
                        // Update unread counts
                        updateUnreadCounts();
                        
                        // Also call the unread-count API directly for header badge
                        if (typeof window.loadAdminUnreadCount === 'function') {
                            window.loadAdminUnreadCount();
                        } else if (typeof loadAdminUnreadCount === 'function') {
                            loadAdminUnreadCount();
                        }
                    }
                })
                .catch(error => {
                    // Silently handle errors - don't show error to user
                    console.debug('Error marking messages as read:', error);
                });
        }
        
        // Update read status indicator for a specific message (only for last sent message)
        function updateMessageReadStatus(messageId, isRead) {
            // Only update if this is the last sent message
            if (messageId != lastSentMessageId) return;
            
            const messageDiv = document.querySelector(`[data-message-id="${messageId}"]`);
            if (!messageDiv) return;
            
            // Check if already has seen indicator
            let seenIndicator = messageDiv.querySelector('.message-seen-indicator');
            
            if (isRead) {
                if (!seenIndicator) {
                    // Create and add seen indicator
                    seenIndicator = document.createElement('div');
                    seenIndicator.className = 'flex justify-end mt-1 message-seen-indicator';
                    seenIndicator.innerHTML = '<span class="text-xs text-blue-500 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Seen</span>';
                    
                    // Find where to insert (after reactions, before end of message content)
                    const reactionsDiv = messageDiv.querySelector('.message-reactions');
                    const messageContent = messageDiv.querySelector('.max-w-\\[75\\%\\]') || messageDiv.querySelector('.relative');
                    if (messageContent) {
                        if (reactionsDiv && reactionsDiv.nextSibling) {
                            messageContent.insertBefore(seenIndicator, reactionsDiv.nextSibling);
                        } else {
                            messageContent.appendChild(seenIndicator);
                        }
                    }
                }
            } else {
                // Remove seen indicator if message is unread
                if (seenIndicator) {
                    seenIndicator.remove();
                }
            }
        }
        
        // Handle message reply
        function handleMessageReply(messageId, userId) {
            const messageInput = document.getElementById('messageInput');
            if (!messageInput) return;
            
            const parentMessage = document.querySelector(`[data-message-id="${messageId}"]`);
            if (!parentMessage) return;
            
            replyToMessageId = messageId;
            messageInput.setAttribute('data-reply-to', messageId);
            
            // Use existing reply indicator from DOM, don't create dynamically
            const replyIndicator = document.getElementById('replyIndicator');
            if (!replyIndicator) {
                console.error('Reply indicator element not found');
                return;
            }
            
            let senderName = 'User';
            let messageText = 'Message';
            
            const parentBubble = parentMessage.querySelector('.bg-gradient-to-r, .bg-white');
            if (parentBubble) {
                messageText = parentBubble.textContent.trim() || 'Message';
                messageText = messageText.replace(/\s+/g, ' ').trim();
            }
            
            const parentAvatar = parentMessage.querySelector('img[alt], .bg-gradient-to-br');
            if (parentAvatar && parentAvatar.alt && parentAvatar.alt !== 'You') {
                senderName = parentAvatar.alt;
            } else {
                const isParentSender = parentMessage.classList.contains('justify-end');
                senderName = isParentSender ? 'You' : (userData[userId]?.name || 'User');
            }
            
            replyIndicator.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-700 truncate">
                                <span class="text-gray-500">You replied to </span>
                                <span class="font-medium text-gray-800">${senderName}</span>
                            </p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">${escapeHtml(messageText.substring(0, 60))}${messageText.length > 60 ? '...' : ''}</p>
                        </div>
                    </div>
                    <button id="cancelReplyBtn" class="ml-2 text-gray-500 hover:text-gray-700 flex-shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            // Store current scroll position
            const messagesArea = document.getElementById('chatMessagesArea');
            const currentScrollTop = messagesArea ? messagesArea.scrollTop : 0;
            const currentScrollHeight = messagesArea ? messagesArea.scrollHeight : 0;
            
            // Show reply indicator without causing layout shift
            replyIndicator.classList.remove('hidden');
            
            // Adjust messages area height to account for reply indicator
            if (messagesArea && replyIndicator) {
                // Wait for reply indicator to render and get its height
                setTimeout(() => {
                    const replyIndicatorHeight = replyIndicator.offsetHeight || replyIndicator.scrollHeight;
                    if (replyIndicatorHeight > 0) {
                        // Add padding-bottom to messages area to account for reply indicator
                        const currentPadding = parseInt(window.getComputedStyle(messagesArea).paddingBottom) || 0;
                        messagesArea.style.paddingBottom = `${currentPadding + replyIndicatorHeight + 8}px`;
                    }
                }, 50);
            }
            
            // Prevent scrolling when showing reply indicator
            const messageInputContainer = replyIndicator.closest('.flex-shrink-0');
            if (messageInputContainer) {
                // Ensure container stays in place and doesn't cause scroll
                messageInputContainer.style.position = 'relative';
                messageInputContainer.style.transform = 'translateZ(0)'; // Force GPU acceleration
                // Set margin-top using setProperty to override !important
                messageInputContainer.style.setProperty('margin-top', '-72px', 'important');
            }
            
            // Restore scroll position immediately to prevent jump
            if (messagesArea) {
                requestAnimationFrame(() => {
                    messagesArea.scrollTop = currentScrollTop;
                    // Also check if scroll height changed and adjust
                    if (messagesArea.scrollHeight !== currentScrollHeight) {
                        const scrollDiff = messagesArea.scrollHeight - currentScrollHeight;
                        messagesArea.scrollTop = currentScrollTop + scrollDiff;
                    }
                });
            }
            
            // Focus input without scrolling - use preventScroll option
            setTimeout(() => {
                messageInput.focus({ preventScroll: true });
                // Double-check scroll position after focus
                if (messagesArea) {
                    requestAnimationFrame(() => {
                        messagesArea.scrollTop = currentScrollTop;
                    });
                }
            }, 50);
            
            const cancelBtn = replyIndicator.querySelector('#cancelReplyBtn');
            if (cancelBtn) {
                // Remove old listeners by cloning
                const newCancelBtn = cancelBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                
                newCancelBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    replyToMessageId = null;
                    messageInput.removeAttribute('data-reply-to');
                    replyIndicator.classList.add('hidden');
                    
                    // Restore margin-top when reply is closed
                    const messageInputContainer = replyIndicator.closest('.flex-shrink-0');
                    if (messageInputContainer) {
                        messageInputContainer.style.marginTop = '0px';
                    }
                    
                    // Restore messages area padding when reply indicator is hidden
                    const messagesArea = document.getElementById('chatMessagesArea');
                    if (messagesArea) {
                        // Reset to original padding (check mobile padding)
                        const isMobile = window.innerWidth <= 640;
                        messagesArea.style.paddingBottom = isMobile ? '10px' : '';
                    }
                });
            }
        }
        
        // Handle message delete
        function handleMessageDelete(messageId, messageDiv) {
            if (!window.Swal) {
                if (confirm('Are you sure you want to delete this message?')) {
                    deleteMessage(messageId, messageDiv);
                }
                return;
            }
            
            Swal.fire({
                title: 'Delete Message?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteMessage(messageId, messageDiv);
                }
            });
        }
        
        // Delete message
        function deleteMessage(messageId, messageDiv) {
            axios.delete(`/messages/${messageId}`)
            .then(response => {
                if (response.data.success) {
                    messageDiv.style.transition = 'opacity 0.3s';
                    messageDiv.style.opacity = '0';
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 300);
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Message deleted successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error deleting message:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to delete message',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Show reactions modal
        function showReactionsModal(messageId, initialReactions) {
            const existingModal = document.getElementById('reactionsModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            const modal = document.createElement('div');
            modal.id = 'reactionsModal';
            modal.className = 'fixed inset-0 z-[10000] flex items-center justify-center bg-opacity-20 backdrop-blur-sm';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Message reactions</h3>
                        <button class="close-reactions-modal w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-1 px-4 pt-3 border-b border-gray-200 reactions-tabs"></div>
                    <div class="flex-1 overflow-y-auto p-4 reactions-content">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-500"></div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            const closeBtn = modal.querySelector('.close-reactions-modal');
            closeBtn.addEventListener('click', () => modal.remove());
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
            
            axios.get(`/messages/${messageId}/reactions`)
                .then(response => {
                    if (response.data.success) {
                        renderReactionsModal(modal, response.data.reactions, messageId);
                    }
                })
                .catch(error => {
                    console.error('Error fetching reactions:', error);
                    const content = modal.querySelector('.reactions-content');
                    content.innerHTML = '<div class="text-center text-gray-500 py-8">Failed to load reactions</div>';
                });
        }
        
        // Render reactions in modal
        function renderReactionsModal(modal, reactions, messageId) {
            const reactionEmojis = {
                'like': '👍', 'love': '❤️', 'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😠'
            };
            
            const tabsContainer = modal.querySelector('.reactions-tabs');
            const contentContainer = modal.querySelector('.reactions-content');
            
            const totalCount = reactions.reduce((sum, r) => sum + r.count, 0);
            
            let tabsHTML = `
                <button class="reaction-tab px-4 py-2 text-sm font-medium text-gray-900 border-b-2 border-gray-700 transition" data-tab="all">
                    All ${totalCount}
                </button>
            `;
            
            reactions.forEach(reaction => {
                tabsHTML += `
                    <button class="reaction-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 border-b-2 border-transparent transition" data-tab="${reaction.type}">
                        ${reactionEmojis[reaction.type] || '👍'} ${reaction.count}
                    </button>
                `;
            });
            
            tabsContainer.innerHTML = tabsHTML;
            renderReactionsContent(contentContainer, reactions, 'all', messageId, modal);
            
            tabsContainer.querySelectorAll('.reaction-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    tabsContainer.querySelectorAll('.reaction-tab').forEach(t => {
                        t.classList.remove('border-gray-700', 'text-gray-900');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-gray-700', 'text-gray-900');
                    const tabType = this.getAttribute('data-tab');
                    renderReactionsContent(contentContainer, reactions, tabType, messageId, modal);
                });
            });
        }
        
        // Render reactions content
        function renderReactionsContent(container, reactions, filterType, messageId, modal) {
            const reactionEmojis = {
                'like': '👍', 'love': '❤️', 'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😠'
            };
            
            let usersToShow = [];
            
            if (filterType === 'all') {
                reactions.forEach(reaction => {
                    reaction.users.forEach(user => {
                        if (!usersToShow.find(u => u.id === user.id)) {
                            usersToShow.push({
                                ...user,
                                reaction_type: reaction.type,
                                reaction_emoji: reactionEmojis[reaction.type] || '👍'
                            });
                        }
                    });
                });
            } else {
                const reaction = reactions.find(r => r.type === filterType);
                if (reaction) {
                    usersToShow = reaction.users.map(user => ({
                        ...user,
                        reaction_type: reaction.type,
                        reaction_emoji: reactionEmojis[reaction.type] || '👍'
                    }));
                }
            }
            
            if (usersToShow.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-8">No reactions</div>';
                return;
            }
            
            let contentHTML = '<div class="space-y-2">';
            usersToShow.forEach(user => {
                contentHTML += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition cursor-pointer reaction-user-item" 
                         data-user-id="${user.id}" 
                         data-reaction-id="${user.reaction_id}"
                         data-is-current-user="${user.is_current_user}">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <img src="${user.profile_picture_url}" alt="${user.name}" 
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 font-medium truncate">${escapeHtml(user.name)}</p>
                                ${user.is_current_user ? '<p class="text-xs text-gray-500">Click to remove</p>' : ''}
                            </div>
                        </div>
                        <div class="text-2xl flex-shrink-0 ml-3">${user.reaction_emoji}</div>
                    </div>
                `;
            });
            contentHTML += '</div>';
            
            container.innerHTML = contentHTML;
            
            container.querySelectorAll('.reaction-user-item[data-is-current-user="true"]').forEach(item => {
                item.addEventListener('click', function() {
                    const reactionId = this.getAttribute('data-reaction-id');
                    removeReaction(messageId, reactionId, modal);
                });
            });
        }
        
        // Remove reaction
        function removeReaction(messageId, reactionId, modal) {
            const clickedItem = modal.querySelector(`[data-reaction-id="${reactionId}"]`);
            if (!clickedItem) return;
            
            const reactionEmoji = clickedItem.querySelector('.text-2xl').textContent.trim();
            const reactionTypes = {
                '👍': 'like', '❤️': 'love', '😂': 'haha', '😮': 'wow', '😢': 'sad', '😠': 'angry'
            };
            const reactionType = reactionTypes[reactionEmoji] || 'like';
            
            axios.post(`/messages/${messageId}/react`, { reaction_type: reactionType })
            .then(response => {
                if (response.data.success) {
                    updateMessageReactions(messageId, response.data.reactions);
                    axios.get(`/messages/${messageId}/reactions`)
                        .then(res => {
                            if (res.data.success) {
                                renderReactionsModal(modal, res.data.reactions, messageId);
                            }
                        });
                }
            })
            .catch(error => {
                console.error('Error removing reaction:', error);
            });
        }
        
        // Image viewer functions
        let currentZoom = 1;
        let isDragging = false;
        let startX = 0;
        let startY = 0;
        let scrollLeft = 0;
        let scrollTop = 0;
        
        window.openImageViewer = function(imageUrl) {
            const modal = document.getElementById('imageViewerModal');
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            
            if (modal && img && container) {
                img.src = imageUrl;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                currentZoom = 1;
                img.style.transform = 'scale(1)';
                img.style.width = 'auto';
                img.style.height = 'auto';
                img.style.maxWidth = '100vw';
                img.style.maxHeight = '100vh';
                img.style.position = 'relative';
                img.style.left = '0';
                img.style.top = '0';
                
                container.scrollLeft = 0;
                container.scrollTop = 0;
                container.style.overflow = 'hidden';
                
                img.onload = function() {
                    const naturalWidth = img.naturalWidth;
                    const naturalHeight = img.naturalHeight;
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    
                    const scaleX = (viewportWidth * 0.9) / naturalWidth;
                    const scaleY = (viewportHeight * 0.9) / naturalHeight;
                    const initialScale = Math.min(scaleX, scaleY, 1);
                    
                    currentZoom = initialScale;
                    img.style.width = (naturalWidth * initialScale) + 'px';
                    img.style.height = (naturalHeight * initialScale) + 'px';
                    img.style.transform = 'scale(1)';
                    img.style.maxWidth = 'none';
                    img.style.maxHeight = 'none';
                };
            }
        };
        
        window.closeImageViewer = function() {
            const modal = document.getElementById('imageViewerModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentZoom = 1;
            }
        };
        
        function zoomIn() {
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            if (img && container) {
                currentZoom = Math.min(currentZoom + 0.25, 5);
                img.style.transform = `scale(${currentZoom})`;
                if (currentZoom > 1) {
                    container.style.cursor = 'grab';
                }
                updateZoomDisplay();
            }
        }
        
        function zoomOut() {
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            if (img && container) {
                currentZoom = Math.max(currentZoom - 0.25, 0.25);
                img.style.transform = `scale(${currentZoom})`;
                if (currentZoom <= 1) {
                    container.style.cursor = 'default';
                    container.scrollLeft = 0;
                    container.scrollTop = 0;
                }
                updateZoomDisplay();
            }
        }
        
        // Group Settings Functions
        let currentGroupData = null;
        
        function loadGroupDetails(groupId) {
            // Set currentGroupId to ensure it's available for all group operations
            window.currentGroupId = groupId;
            
            return axios.get(`{{ route('messages.groups.show', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', groupId))
                .then(response => {
                    if (response.data.success) {
                        currentGroupData = response.data.group;
                        window.currentGroupIsAdmin = response.data.is_admin;
                        
                        // Show/hide settings button based on admin status
                        const groupSettingsBtn = document.getElementById('groupSettingsBtn');
                        if (groupSettingsBtn) {
                            if (response.data.is_admin) {
                                groupSettingsBtn.classList.remove('hidden');
                            } else {
                                groupSettingsBtn.classList.add('hidden');
                            }
                        }
                        
                        // Apply theme if available
                        if (response.data.group?.theme && availableThemes.length > 0) {
                            applyThemeToChat(response.data.group.theme);
                        } else if (availableThemes.length === 0) {
                            // Load themes first, then apply
                            loadThemes().then(() => {
                                if (response.data.group?.theme) {
                                    applyThemeToChat(response.data.group.theme);
                                }
                            });
                        }
                        
                        return response.data;
                    }
                })
                .catch(error => {
                    console.error('Error loading group details:', error);
                    const groupSettingsBtn = document.getElementById('groupSettingsBtn');
                    if (groupSettingsBtn) {
                        groupSettingsBtn.classList.add('hidden');
                    }
                    throw error;
                });
        }
        
        function openGroupSettings() {
            if (!currentGroupData && window.currentGroupId) {
                // Load group details first
                loadGroupDetails(window.currentGroupId);
                setTimeout(() => {
                    if (currentGroupData) {
                        populateGroupSettingsModal();
                        document.getElementById('groupSettingsModal').classList.remove('hidden');
                    }
                }, 500);
            } else if (currentGroupData) {
                populateGroupSettingsModal();
                document.getElementById('groupSettingsModal').classList.remove('hidden');
            }
        }
        
        function populateGroupSettingsModal() {
            if (!currentGroupData) {
                console.error('No group data available');
                return;
            }
            
            // Set group name and description
            const nameInput = document.getElementById('groupSettingsNameInput');
            const descInput = document.getElementById('groupSettingsDescriptionInput');
            if (nameInput) {
                nameInput.value = currentGroupData.name || '';
            }
            if (descInput) {
                descInput.value = currentGroupData.description || '';
            }
            
            // Set avatar preview
            const avatarPreview = document.getElementById('groupSettingsAvatarPreview');
            if (avatarPreview) {
                if (currentGroupData.avatar) {
                    avatarPreview.src = currentGroupData.avatar;
                } else {
                    // Use UI Avatars as fallback
                    const name = currentGroupData.name || 'Group';
                    avatarPreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=80&background=055498&color=fff`;
                }
            }
            
            // Populate members list
            renderGroupMembers();
            
            // Load themes if on theme tab
            const themeTab = document.getElementById('groupSettingsThemeTab');
            if (themeTab && !themeTab.classList.contains('hidden')) {
                if (availableThemes.length === 0) {
                    loadThemes();
                } else {
                    // Update theme selection to reflect current theme
                    const currentTheme = currentGroupData?.theme || 'default';
                    selectedThemeId = currentTheme;
                    currentAppliedTheme = currentTheme;
                    updateThemeSelection();
                }
            }
            
            // Apply theme to chat if available
            if (currentGroupData?.theme && availableThemes.length > 0) {
                applyThemeToChat(currentGroupData.theme);
            } else if (availableThemes.length === 0) {
                // Load themes first, then apply
                loadThemes().then(() => {
                    if (currentGroupData?.theme) {
                        applyThemeToChat(currentGroupData.theme);
                    }
                });
            }
        }
        
        function renderGroupMembers() {
            if (!currentGroupData || !currentGroupData.members) {
                const membersList = document.getElementById('groupMembersList');
                if (membersList) {
                    membersList.innerHTML = '<div class="text-center py-8 text-gray-400"><p class="text-sm">No members found</p></div>';
                }
                return;
            }
            
            const membersList = document.getElementById('groupMembersList');
            const memberCountBadge = document.getElementById('memberCountBadge');
            const currentUserId = '{{ Auth::id() }}';
            
            // Update member count badge
            if (memberCountBadge) {
                memberCountBadge.textContent = currentGroupData.members.length;
            }
            
            if (membersList) {
                membersList.innerHTML = currentGroupData.members.map(member => {
                    const fullName = `${member.first_name || ''} ${member.last_name || ''}`.trim();
                    const isCurrentUser = member.id === currentUserId;
                    const canRemove = window.currentGroupIsAdmin && !isCurrentUser;
                    const canToggleAdmin = window.currentGroupIsAdmin && !isCurrentUser;
                    
                    return `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="relative">
                                    <img src="${member.profile_picture_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&size=40&background=055498&color=fff'}" 
                                         alt="${fullName}" 
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    ${member.is_admin ? `
                                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full flex items-center justify-center border border-white" style="background: #CE2028;">
                                            <i class="fas fa-crown text-white text-[7px]"></i>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="text-sm font-medium text-gray-800 truncate">${escapeHtml(fullName)}</p>
                                        ${isCurrentUser ? '<span class="px-1.5 py-0.5 text-xs font-medium rounded" style="background: rgba(5, 84, 152, 0.1); color: #055498;">You</span>' : ''}
                                    </div>
                                    ${member.is_admin ? '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-xs font-semibold rounded" style="background: rgba(206, 32, 40, 0.1); color: #CE2028;"><i class="fas fa-crown text-[7px]"></i> Admin</span>' : '<span class="text-xs text-gray-500">Member</span>'}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${canToggleAdmin ? `
                                    <button type="button" class="toggle-admin-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-colors duration-200 text-white hover:opacity-90" 
                                            data-user-id="${member.id}" 
                                            data-is-admin="${member.is_admin ? 'true' : 'false'}"
                                            style="${member.is_admin ? 'background: #CE2028;' : 'background: #055498;'} cursor: pointer;">
                                        <i class="fas ${member.is_admin ? 'fa-user-minus' : 'fa-crown'} mr-1"></i>
                                        ${member.is_admin ? 'Remove Admin' : 'Make Admin'}
                                    </button>
                                ` : ''}
                                ${canRemove ? `
                                    <button class="remove-member-btn px-3 py-1.5 text-xs font-medium rounded-lg text-white transition-colors duration-200 hover:opacity-90" 
                                            data-user-id="${member.id}"
                                            style="background: #CE2028;">
                                        <i class="fas fa-user-minus mr-1"></i>
                                        Remove
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Attach event listeners using event delegation for dynamically added buttons
            // Remove any existing listeners first
            const newMembersList = membersList.cloneNode(true);
            membersList.parentNode.replaceChild(newMembersList, membersList);
            
            // Use event delegation on the container
            newMembersList.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                
                // Handle remove member button (check button or icon inside)
                const removeBtn = e.target.closest('.remove-member-btn');
                if (removeBtn) {
                    e.preventDefault();
                    const userId = removeBtn.getAttribute('data-user-id');
                    if (userId) {
                        removeGroupMember(userId);
                    }
                    return;
                }
                
                // Handle toggle admin button (check button or icon inside)
                const toggleBtn = e.target.closest('.toggle-admin-btn');
                if (toggleBtn) {
                    e.preventDefault();
                    const userId = toggleBtn.getAttribute('data-user-id');
                    const isAdminAttr = toggleBtn.getAttribute('data-is-admin');
                    
                    if (!userId) {
                        console.error('No user ID found on toggle admin button');
                        return;
                    }
                    
                    // Handle both boolean and string values (true, "true", 1, "1")
                    const isAdmin = isAdminAttr === 'true' || isAdminAttr === true || isAdminAttr === '1' || isAdminAttr === 1;
                    
                    toggleGroupAdmin(userId, isAdmin);
                    return;
                }
            });
        }
        
        function saveGroupInfo() {
            if (!window.currentGroupId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Group ID not found.'
                });
                return;
            }
            
            const nameInput = document.getElementById('groupSettingsNameInput');
            const descInput = document.getElementById('groupSettingsDescriptionInput');
            
            if (!nameInput) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Group name input not found.'
                });
                return;
            }
            
            const name = nameInput.value.trim();
            const description = descInput ? descInput.value.trim() : '';
            
            if (!name) {
                Swal.fire({
                    icon: 'error',
                    title: 'Group Name Required',
                    text: 'Please enter a group name.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we update the group information.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            axios.put(`{{ route('messages.groups.update', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.currentGroupId), {
                name: name,
                description: description
            })
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Group information updated successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Update currentGroupData
                    if (response.data.group) {
                        currentGroupData = response.data.group;
                    }
                    
                    // Update chat header name if it matches
                    const chatHeaderName = document.querySelector('#chatHeaderName');
                    if (chatHeaderName) {
                        chatHeaderName.textContent = name;
                    }
                    
                    // Reload group details to get updated data
                    loadGroupDetails(window.currentGroupId);
                    
                    // Reload conversations to update the list
                    loadConversations();
                } else {
                    throw new Error(response.data.message || 'Failed to update group information');
                }
            })
            .catch(error => {
                console.error('Error updating group:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to update group information. Please try again.',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        }
        
        function removeGroupMember(userId) {
            if (!window.currentGroupId) return;
            
            Swal.fire({
                title: 'Remove Member?',
                text: 'Are you sure you want to remove this member from the group?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`{{ route('messages.groups.members.remove', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.currentGroupId), {
                        data: { user_ids: [userId] }
                    })
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: 'Member removed successfully!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Reload group details and refresh the modal
                            loadGroupDetails(window.currentGroupId).then(() => {
                                populateGroupSettingsModal();
                            }).catch(() => {
                                // Still refresh modal even if loadGroupDetails fails
                                populateGroupSettingsModal();
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error removing member:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.response?.data?.message || 'Failed to remove member.'
                        });
                    });
                }
            });
        }
        
        function toggleGroupAdmin(userId, isCurrentlyAdmin) {
            if (!window.currentGroupId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Group ID not found.'
                });
                return;
            }
            
            const endpoint = isCurrentlyAdmin 
                ? `{{ route('messages.groups.admins.revoke', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.currentGroupId)
                : `{{ route('messages.groups.admins.assign', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.currentGroupId);
            
            console.log('API endpoint:', endpoint);
            console.log('Request data:', { user_ids: [userId] });
            console.log('HTTP method:', isCurrentlyAdmin ? 'DELETE' : 'POST');
            
            // Show loading
            Swal.fire({
                title: isCurrentlyAdmin ? 'Removing Admin...' : 'Assigning Admin...',
                text: 'Please wait.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Use DELETE for revoke, POST for assign
            const requestPromise = isCurrentlyAdmin
                ? axios.delete(endpoint, { data: { user_ids: [userId] } })
                : axios.post(endpoint, { user_ids: [userId] });
            
            requestPromise
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: `Admin privileges ${isCurrentlyAdmin ? 'revoked' : 'assigned'} successfully!`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Update currentGroupData with the response data if available
                        if (response.data.group) {
                            currentGroupData = response.data.group;
                            // Update is_admin status if it changed for current user
                            if (response.data.is_admin !== undefined) {
                                window.currentGroupIsAdmin = response.data.is_admin;
                            }
                        }
                        
                        // Reload group details to get fresh data from database
                        loadGroupDetails(window.currentGroupId).then(() => {
                            // Refresh the modal with updated member data
                            populateGroupSettingsModal();
                        }).catch((error) => {
                            console.error('Error reloading group details:', error);
                            // Still refresh modal with current data even if loadGroupDetails fails
                            if (currentGroupData) {
                                populateGroupSettingsModal();
                            }
                        });
                    } else {
                        throw new Error(response.data.message || 'Failed to update admin privileges');
                    }
                })
                .catch(error => {
                    console.error('Error toggling admin:', error);
                    console.error('Error response:', error.response);
                    console.error('Error details:', {
                        message: error.message,
                        response: error.response?.data,
                        status: error.response?.status,
                        statusText: error.response?.statusText
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || error.message || 'Failed to update admin privileges. Please try again.',
                        timer: 3000,
                        showConfirmButton: true
                    });
                });
        }
        
        // Theme Management
        let availableThemes = [];
        let selectedThemeId = null;
        let previewThemeId = null;
        let currentAppliedTheme = null;
        
        // Tab switching functionality
        function switchGroupSettingsTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.group-settings-tab').forEach(tab => {
                tab.classList.remove('text-gray-800');
                tab.classList.add('text-gray-500', 'border-transparent');
                tab.style.borderColor = 'transparent';
                tab.style.color = '';
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById(`groupSettings${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Tab`);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }
            
            // Activate selected tab button
            const selectedTabBtn = document.querySelector(`[data-tab="${tabName}"]`);
            if (selectedTabBtn) {
                selectedTabBtn.classList.remove('text-gray-500', 'border-transparent');
                selectedTabBtn.classList.add('text-gray-800');
                selectedTabBtn.style.borderColor = '#055498';
                selectedTabBtn.style.color = '#055498';
            }
            
            // Toggle footer visibility based on active tab
            const defaultFooter = document.getElementById('defaultModalFooter');
            const themeFooter = document.getElementById('themeModalFooter');
            if (tabName === 'theme') {
                if (defaultFooter) defaultFooter.classList.add('hidden');
                if (themeFooter) themeFooter.classList.remove('hidden');
            } else {
                if (defaultFooter) defaultFooter.classList.remove('hidden');
                if (themeFooter) themeFooter.classList.add('hidden');
            }
            
            // Load themes if switching to theme tab
            if (tabName === 'theme' && availableThemes.length === 0) {
                loadThemes();
            }
        }
        
        // Load available themes
        async function loadThemes() {
            try {
                const response = await axios.get('{{ route("messages.groups.themes") }}');
                if (response.data.success) {
                    availableThemes = response.data.themes;
                    renderThemeSelection();
                    
                    // Set current theme
                    const currentTheme = currentGroupData?.theme || 'default';
                    selectedThemeId = currentTheme;
                    currentAppliedTheme = currentTheme;
                    updateThemeSelection();
                    
                    // Show preview of current theme
                    if (currentTheme) {
                        showThemePreview(currentTheme);
                    }
                }
            } catch (error) {
                console.error('Error loading themes:', error);
                const themeGrid = document.getElementById('themeSelectionGrid');
                if (themeGrid) {
                    themeGrid.innerHTML = '<div class="text-center py-4 text-red-500 text-sm">Failed to load themes</div>';
                }
            }
        }
        
        // Render theme selection grid
        function renderThemeSelection() {
            const themeGrid = document.getElementById('themeSelectionGrid');
            if (!themeGrid) return;
            
            themeGrid.innerHTML = availableThemes.map(theme => {
                const isSelected = selectedThemeId === theme.id;
                const isApplied = currentAppliedTheme === theme.id;
                
                // Create thumbnail preview
                let thumbnailStyle = '';
                if (theme.background_image) {
                    thumbnailStyle = `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};`;
                } else if (theme.background.startsWith('linear-gradient')) {
                    thumbnailStyle = `background: ${theme.background};`;
                } else {
                    thumbnailStyle = `background: ${theme.background};`;
                }
                
                return `
                    <div class="theme-option group relative cursor-pointer bg-white rounded-lg border-2 transition-all duration-200 ${isSelected ? 'border-blue-500 ring-2 ring-blue-200 shadow-lg' : 'border-gray-200 hover:border-gray-300 hover:shadow-md'} overflow-hidden" 
                         data-theme-id="${theme.id}">
                        <div class="flex items-stretch">
                            <!-- Theme Preview Thumbnail -->
                            <div class="w-24 flex-shrink-0 relative overflow-hidden" style="${thumbnailStyle}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                                <!-- Preview Bubbles -->
                                <div class="absolute bottom-2 left-0 right-0 flex items-end justify-center gap-1.5 px-2">
                                    <div class="w-8 h-5 rounded-md shadow-sm border border-white/30" style="background: ${theme.receiver_bubble};"></div>
                                    <div class="w-10 h-6 rounded-md shadow-sm border border-white/30" style="background: ${theme.sender_bubble};"></div>
                                </div>
                            </div>
                            
                            <!-- Theme Info -->
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <h6 class="text-sm font-semibold text-gray-800">${escapeHtml(theme.name)}</h6>
                                        ${isSelected ? `
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-white text-[10px]"></i>
                                            </div>
                                        ` : ''}
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed mb-2">${escapeHtml(theme.description)}</p>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <!-- Color Swatches -->
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.sender_bubble};"></div>
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.receiver_bubble};"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Applied Badge -->
                                    ${isApplied ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-full">
                                            <i class="fas fa-check-circle text-[9px]"></i>
                                            <span>Active</span>
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hover Effect -->
                        <div class="absolute inset-0 bg-blue-50/0 group-hover:bg-blue-50/30 transition-all duration-200 pointer-events-none"></div>
                    </div>
                `;
            }).join('');
            
            // Add click handlers
            themeGrid.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    const themeId = this.getAttribute('data-theme-id');
                    selectTheme(themeId);
                });
            });
        }
        
        // Select a theme
        function selectTheme(themeId) {
            selectedThemeId = themeId;
            updateThemeSelection();
            showThemePreview(themeId);
            
            // Apply theme preview in real-time (without saving)
            const theme = availableThemes.find(t => t.id === themeId);
            if (theme && currentChatUserId && currentChatUserId.startsWith('group_')) {
                // Temporarily apply theme for preview (real-time)
                applyThemeToChat(themeId);
                if (theme) {
                    updateMessageBubblesTheme(theme);
                }
            }
            
            // Show apply and cancel buttons
            const applyBtn = document.getElementById('applyThemeBtn');
            const cancelBtn = document.getElementById('cancelThemeBtn');
            if (applyBtn) applyBtn.classList.remove('hidden');
            if (cancelBtn) cancelBtn.classList.remove('hidden');
        }
        
        // Update theme selection UI
        function updateThemeSelection() {
            const themeGrid = document.getElementById('themeSelectionGrid');
            if (!themeGrid) return;
            
            themeGrid.querySelectorAll('.theme-option').forEach(option => {
                const themeId = option.getAttribute('data-theme-id');
                const isSelected = selectedThemeId === themeId;
                const isApplied = currentAppliedTheme === themeId;
                
                if (isSelected) {
                    option.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                    option.classList.remove('border-gray-200');
                    
                    // Add checkmark if not present
                    if (!option.querySelector('.fa-check')) {
                        const checkmark = document.createElement('div');
                        checkmark.className = 'absolute top-2 right-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center';
                        checkmark.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                        option.appendChild(checkmark);
                    }
                } else {
                    option.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                    option.classList.add('border-gray-200');
                    
                    // Remove checkmark
                    const checkmark = option.querySelector('.fa-check');
                    if (checkmark && checkmark.closest('.absolute.top-2')) {
                        checkmark.closest('.absolute.top-2').remove();
                    }
                }
                
                // Update "Applied" badge
                const appliedBadge = option.querySelector('.bg-green-500');
                if (isApplied && !appliedBadge) {
                    const badge = document.createElement('div');
                    badge.className = 'absolute bottom-2 left-2 px-2 py-0.5 bg-green-500 rounded text-white text-[10px] font-semibold';
                    badge.textContent = 'Applied';
                    option.appendChild(badge);
                } else if (!isApplied && appliedBadge) {
                    appliedBadge.remove();
                }
            });
        }
        
        // Show theme preview
        function showThemePreview(themeId) {
            const theme = availableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const previewSection = document.getElementById('themePreviewSection');
            const previewContainer = document.getElementById('themePreviewContainer');
            
            if (!previewSection || !previewContainer) return;
            
            previewThemeId = themeId;
            
            // Helper function to check if color is light (matches backend logic)
            const isLightColor = (color) => {
                if (!color) return false;
                
                // Check for common dark/black color names
                const darkColors = ['black', '#000', '#000000', 'rgb(0,0,0)', 'rgba(0,0,0'];
                const colorLower = color.toLowerCase().trim();
                for (const darkColor of darkColors) {
                    if (colorLower.includes(darkColor)) {
                        return false; // Definitely dark
                    }
                }
                
                let r, g, b;
                if (color.startsWith('#')) {
                    let hex = color.replace('#', '');
                    // Handle 3-digit hex
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }
                    if (hex.length === 6) {
                        r = parseInt(hex.substring(0, 2), 16);
                        g = parseInt(hex.substring(2, 4), 16);
                        b = parseInt(hex.substring(4, 6), 16);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else if (color.startsWith('rgb')) {
                    const matches = color.match(/\d+/g);
                    if (matches && matches.length >= 3) {
                        r = parseInt(matches[0]);
                        g = parseInt(matches[1]);
                        b = parseInt(matches[2]);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                
                // YIQ formula: brightness threshold lowered to 150 (matches backend)
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq > 150; // Lower threshold to catch more dark colors
            };
            
            const headerBgColor = theme.header_color || theme.accent_color;
            const headerTextColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
            const iconColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
            
            // Create full preview with header, messages, and input area
            previewContainer.innerHTML = `
                <!-- Chat Header Preview -->
                <div class="border-b rounded-t-lg" style="background: ${headerBgColor}; border-color: ${headerBgColor};">
                    <div class="px-3 py-2 flex items-center justify-between">
                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold truncate" style="color: ${headerTextColor};">Group Chat Preview</h3>
                                <p class="text-xs truncate" style="color: ${headerTextColor}; opacity: 0.8;">Group chat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-1.5 rounded-full hover:bg-white/10 transition" style="color: ${iconColor};">
                                <i class="fas fa-cog text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area Preview -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3 min-h-[200px] max-h-[250px]" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`}">
                    <!-- Received Message -->
                    <div class="flex items-start space-x-2">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs mb-1" style="color: ${theme.receiver_text}; opacity: 0.7;">John Doe</div>
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.receiver_bubble}; color: ${theme.receiver_text};">
                                <p class="text-xs">Hey! How are you doing?</p>
                            </div>
                            <div class="text-[10px] mt-1" style="color: ${theme.receiver_text}; opacity: 0.5;">10:30 AM</div>
                        </div>
                    </div>
                    
                    <!-- Sent Message -->
                    <div class="flex items-start space-x-2 justify-end">
                        <div class="flex-1 flex justify-end">
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.sender_bubble}; color: ${theme.sender_text};">
                                <p class="text-xs">I'm doing great, thanks!</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Input Area Preview -->
                <div class="border-t rounded-b-lg p-2" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`} border-color: ${theme.receiver_bubble};">
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition" style="color: ${theme.accent_color || '#6b7280'};">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <div class="flex-1 rounded-lg border px-3 py-2" style="background: white; border-color: ${theme.receiver_bubble};">
                            <input type="text" placeholder="Type a message..." class="w-full text-xs outline-none" style="color: #1f2937;" disabled>
                        </div>
                        <button class="p-2 rounded-lg transition" style="background: ${theme.accent_color || '#3b82f6'}; color: #ffffff;">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // Ensure preview section is visible
            previewSection.classList.remove('hidden');
        }
        
        // Apply theme
        async function applyTheme() {
            if (!selectedThemeId || !window.currentGroupId) return;
            
            try {
                const response = await axios.post(`{{ route('messages.groups.theme.apply', ['groupId' => ':groupId']) }}`.replace(':groupId', window.currentGroupId), {
                    theme: selectedThemeId
                });
                
                if (response.data.success) {
                    // Update current group data
                    if (currentGroupData) {
                        currentGroupData.theme = selectedThemeId;
                    }
                    
                    currentAppliedTheme = selectedThemeId;
                    selectedThemeId = selectedThemeId;
                    
                    // Apply theme to chat immediately for real-time update
                    applyThemeToChat(selectedThemeId);
                    
                    // Force update all existing messages immediately
                    const theme = availableThemes.find(t => t.id === selectedThemeId);
                    if (theme) {
                        updateMessageBubblesTheme(theme);
                    }
                    
                    // Show preview of applied theme
                    showThemePreview(selectedThemeId);
                    
                    // Hide buttons
                    const applyBtn = document.getElementById('applyThemeBtn');
                    const cancelBtn = document.getElementById('cancelThemeBtn');
                    if (applyBtn) applyBtn.classList.add('hidden');
                    if (cancelBtn) cancelBtn.classList.add('hidden');
                    
                    // Update theme selection UI
                    updateThemeSelection();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Theme Applied',
                        text: 'The chat theme has been updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error applying theme:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to apply theme. Please try again.',
                    confirmButtonColor: '#055498'
                });
            }
        }
        
        // Cancel theme selection
        function cancelThemeSelection() {
            // Reset to current theme
            const currentTheme = currentGroupData?.theme || 'default';
            selectedThemeId = currentTheme;
            updateThemeSelection();
            
            // Show preview of current theme instead of hiding
            if (currentTheme) {
                showThemePreview(currentTheme);
            }
            
            // Restore original theme in real-time
            if (currentChatUserId && currentChatUserId.startsWith('group_')) {
                applyThemeToChat(currentTheme);
                const theme = availableThemes.find(t => t.id === currentTheme);
                if (theme) {
                    updateMessageBubblesTheme(theme);
                }
            }
            
            // Hide buttons
            const applyBtn = document.getElementById('applyThemeBtn');
            const cancelBtn = document.getElementById('cancelThemeBtn');
            if (applyBtn) applyBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
        }
        
        // Get current theme
        function getCurrentTheme() {
            if (!currentGroupData || !currentGroupData.theme) {
                return availableThemes.find(t => t.id === 'default') || null;
            }
            return availableThemes.find(t => t.id === currentGroupData.theme) || availableThemes.find(t => t.id === 'default') || null;
        }
        
        // Apply theme to chat area - ONLY for the current group chat
        function applyThemeToChat(themeId) {
            // Only apply theme if we're currently viewing the group chat that has this theme
            if (!currentChatUserId || !currentChatUserId.startsWith('group_')) {
                return; // Not a group chat, don't apply theme
            }
            
            const currentGroupId = currentChatUserId.replace('group_', '');
            if (window.currentGroupId && currentGroupId !== window.currentGroupId.toString()) {
                return; // Different group chat, don't apply theme
            }
            
            const theme = availableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            if (!chatMessagesArea) return;
            
            // Store theme in data attribute with group ID to ensure isolation
            chatMessagesArea.setAttribute('data-theme-id', themeId);
            chatMessagesArea.setAttribute('data-theme-group-id', currentGroupId);
            
            // Apply background - support both color and image
            if (theme.background_image) {
                chatMessagesArea.style.backgroundImage = `url(${theme.background_image})`;
                chatMessagesArea.style.backgroundSize = 'cover';
                chatMessagesArea.style.backgroundPosition = 'center';
                chatMessagesArea.style.backgroundRepeat = 'no-repeat';
                chatMessagesArea.style.backgroundColor = theme.background; // Fallback color
            } else {
                chatMessagesArea.style.backgroundImage = '';
                chatMessagesArea.style.backgroundSize = '';
                chatMessagesArea.style.backgroundPosition = '';
                chatMessagesArea.style.backgroundRepeat = '';
                chatMessagesArea.style.background = theme.background;
            }
            
            // Apply theme to chat header
            const chatHeader = document.querySelector('#activeChat .border-b.bg-white');
            if (chatHeader && currentChatUserId && currentChatUserId.startsWith('group_')) {
                const headerBgColor = theme.header_color || theme.accent_color;
                chatHeader.style.setProperty('background-color', headerBgColor, 'important');
                chatHeader.style.setProperty('border-color', headerBgColor, 'important');
                
                // Helper function to check if color is light
                const isLightColor = (color) => {
                    if (!color) return false;
                    // Convert hex to RGB
                    let r, g, b;
                    if (color.startsWith('#')) {
                        r = parseInt(color.slice(1, 3), 16);
                        g = parseInt(color.slice(3, 5), 16);
                        b = parseInt(color.slice(5, 7), 16);
                    } else if (color.startsWith('rgb')) {
                        const matches = color.match(/\d+/g);
                        if (matches && matches.length >= 3) {
                            r = parseInt(matches[0]);
                            g = parseInt(matches[1]);
                            b = parseInt(matches[2]);
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                    // Calculate luminance (YIQ formula)
                    const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                    return yiq >= 128;
                };
                
                // Update header text color for readability
                const headerText = chatHeader.querySelectorAll('#chatHeaderName, #chatHeaderStatusText');
                const textColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
                headerText.forEach(el => {
                    el.style.setProperty('color', textColor, 'important');
                });
                
                // Update icons in header (settings button, back button)
                // Use appropriate color based on background brightness
                const iconColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
                const headerIcons = chatHeader.querySelectorAll('#groupSettingsBtn i, #backToConversations i');
                headerIcons.forEach(icon => {
                    icon.style.setProperty('color', iconColor, 'important');
                });
                
                // Update buttons
                const headerButtons = chatHeader.querySelectorAll('#groupSettingsBtn, #backToConversations');
                headerButtons.forEach(btn => {
                    btn.style.setProperty('color', iconColor, 'important');
                });
            }
            
            // Update send button icon color - always use white for contrast on colored background
            const sendBtn = document.getElementById('sendBtn');
            if (sendBtn) {
                const sendIcon = sendBtn.querySelector('i');
                if (sendIcon) {
                    sendIcon.style.color = '#ffffff';
                }
                // Also update button background if needed
                if (theme.accent_color) {
                    sendBtn.style.background = `linear-gradient(135deg, ${theme.accent_color} 0%, ${theme.accent_color}dd 100%)`;
                }
            }
            
            // Update voice recorder buttons
            const voiceRecorder = document.getElementById('voiceRecorder');
            if (voiceRecorder) {
                // Update voice recorder background
                const voiceRecorderBar = voiceRecorder.querySelector('div[style*="background-color"]');
                if (voiceRecorderBar && theme.accent_color) {
                    voiceRecorderBar.style.backgroundColor = theme.accent_color;
                }
                
                // Update voice recorder buttons
                const voiceCancelBtn = document.getElementById('voiceCancelBtn');
                const voiceStopBtn = document.getElementById('voiceStopBtn');
                const voiceSendBtn = document.getElementById('voiceSendBtn');
                const voiceTimer = document.getElementById('voiceTimer');
                
                if (voiceCancelBtn) {
                    voiceCancelBtn.style.color = '#ffffff';
                }
                if (voiceStopBtn) {
                    const stopIcon = voiceStopBtn.querySelector('span');
                    if (stopIcon && theme.accent_color) {
                        stopIcon.style.backgroundColor = theme.accent_color;
                    }
                }
                if (voiceSendBtn) {
                    voiceSendBtn.style.color = theme.accent_color || '#FF1F70';
                    const sendIcon = voiceSendBtn.querySelector('svg');
                    if (sendIcon) {
                        sendIcon.style.color = theme.accent_color || '#FF1F70';
                    }
                }
                if (voiceTimer && theme.accent_color) {
                    voiceTimer.style.color = theme.accent_color;
                }
            }
            
            // Update existing message bubbles and voice messages
            updateMessageBubblesTheme(theme);
        }
        
        // Apply theme to a single message element
        function applyThemeToMessage(messageDiv, theme, isSender) {
            if (!messageDiv || !theme) return;
            
            if (isSender) {
                // Text message bubbles
                const textBubbles = messageDiv.querySelectorAll('.bg-gradient-to-r, .bg-\\[\\#FF1F70\\], .bg-\\[\\#055498\\]');
                textBubbles.forEach(bubble => {
                    bubble.style.background = theme.sender_bubble;
                    bubble.style.color = theme.sender_text;
                });
                
                // Voice message bubbles
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.sender_bubble;
                    
                    const durationText = bubble.querySelector('.voice-duration');
                    if (durationText) {
                        durationText.style.color = theme.sender_text;
                    }
                    
                    const playButton = bubble.querySelector('.voice-play-toggle');
                    if (playButton) {
                        playButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        playButton.style.color = theme.sender_bubble;
                    }
                    
                    const speedButton = bubble.querySelector('.voice-speed-toggle');
                    if (speedButton) {
                        speedButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        speedButton.style.color = theme.sender_bubble;
                    }
                    
                    const waveformBars = bubble.querySelectorAll('.waveform-bar');
                    waveformBars.forEach(bar => {
                        bar.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.8)' : 'rgba(255, 255, 255, 0.4)';
                    });
                });
            } else {
                // Receiver bubbles
                const textBubbles = messageDiv.querySelectorAll('.bg-white.text-gray-800, .bg-white');
                textBubbles.forEach(bubble => {
                    bubble.style.background = theme.receiver_bubble;
                    bubble.style.color = theme.receiver_text;
                });
                
                // Receiver voice messages
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.receiver_bubble;
                    bubble.style.borderColor = theme.receiver_bubble !== '#ffffff' ? theme.receiver_bubble : '#e5e7eb';
                    
                    const durationText = bubble.querySelector('.voice-duration');
                    if (durationText) {
                        durationText.style.color = theme.receiver_text;
                    }
                });
            }
        }
        
        // Update message bubbles with theme
        function updateMessageBubblesTheme(theme) {
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            if (!chatMessagesArea) return;
            
            // Update sender bubbles (messages on the right)
            chatMessagesArea.querySelectorAll('.flex.items-start.space-x-2.justify-end').forEach(messageDiv => {
                // Text message bubbles - find by classes OR inline styles
                const textBubbles = messageDiv.querySelectorAll('.space-y-2 > div, .bg-gradient-to-r, .bg-\\[\\#FF1F70\\], .bg-\\[\\#055498\\]');
                textBubbles.forEach(bubble => {
                    // Check if this is a message bubble (has rounded-lg, p-2, or has inline style with background)
                    const hasStyle = bubble.hasAttribute('style') && bubble.getAttribute('style').includes('background');
                    const hasBubbleClasses = bubble.classList.contains('rounded-lg') || bubble.classList.contains('p-2') || 
                                            bubble.classList.contains('bg-gradient-to-r') || 
                                            bubble.classList.contains('bg-[#FF1F70]') || 
                                            bubble.classList.contains('bg-[#055498]');
                    
                    if (hasStyle || hasBubbleClasses) {
                        // Check if it's inside space-y-2 (message content area)
                        const isInMessageContent = bubble.closest('.space-y-2') || bubble.parentElement?.classList.contains('space-y-2');
                        if (isInMessageContent || hasBubbleClasses) {
                            bubble.style.setProperty('background', theme.sender_bubble, 'important');
                            bubble.style.setProperty('color', theme.sender_text, 'important');
                        }
                    }
                });
                
                // Update reaction colors for sent messages
                const reactions = messageDiv.querySelectorAll('.message-reactions');
                reactions.forEach(reaction => {
                    reaction.style.setProperty('color', theme.sender_text, 'important');
                });
                
                // Also find any div with inline background style in the message
                const allBubbles = messageDiv.querySelectorAll('div[style*="background"]');
                allBubbles.forEach(bubble => {
                    // Check if it's a message bubble (not a button, icon, etc.)
                    if (bubble.classList.contains('rounded-lg') || 
                        bubble.classList.contains('p-2') || 
                        bubble.closest('.space-y-2')) {
                        const style = bubble.getAttribute('style') || '';
                        // Only update if it looks like a message bubble (has background color)
                        if (style.includes('background') && !bubble.classList.contains('voice-message-container')) {
                            bubble.style.setProperty('background', theme.sender_bubble, 'important');
                            if (style.includes('color')) {
                                bubble.style.setProperty('color', theme.sender_text, 'important');
                            }
                        }
                    }
                });
                
                // Voice message bubbles
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.sender_bubble;
                    
                    const durationText = bubble.querySelector('.voice-duration, .text-white');
                    if (durationText) {
                        durationText.style.color = theme.sender_text;
                    }
                    
                    const playButton = bubble.querySelector('.voice-play-toggle');
                    if (playButton) {
                        playButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        playButton.style.color = theme.sender_bubble;
                    }
                    
                    const speedButton = bubble.querySelector('.voice-speed-toggle');
                    if (speedButton) {
                        speedButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        speedButton.style.color = theme.sender_bubble;
                    }
                    
                    const waveformBars = bubble.querySelectorAll('.waveform-bar');
                    waveformBars.forEach(bar => {
                        bar.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.8)' : 'rgba(255, 255, 255, 0.4)';
                    });
                });
            });
            
            // Update receiver bubbles (messages on the left)
            chatMessagesArea.querySelectorAll('.flex.items-start.space-x-2:not(.justify-end)').forEach(messageDiv => {
                const textBubbles = messageDiv.querySelectorAll('.space-y-2 > div, .bg-white.text-gray-800, .bg-white');
                textBubbles.forEach(bubble => {
                    // Check if this is a message bubble
                    const hasStyle = bubble.hasAttribute('style') && bubble.getAttribute('style').includes('background');
                    const hasBubbleClasses = bubble.classList.contains('rounded-lg') || bubble.classList.contains('p-2') || 
                                            bubble.classList.contains('bg-white');
                    
                    if (hasStyle || hasBubbleClasses) {
                        const isInMessageContent = bubble.closest('.space-y-2') || bubble.parentElement?.classList.contains('space-y-2');
                        if (isInMessageContent || hasBubbleClasses) {
                            bubble.style.setProperty('background', theme.receiver_bubble, 'important');
                            bubble.style.setProperty('color', theme.receiver_text, 'important');
                        }
                    }
                });
                
                // Also find any div with inline background style in the message
                const allBubbles = messageDiv.querySelectorAll('div[style*="background"]');
                allBubbles.forEach(bubble => {
                    // Check if it's a message bubble (not a button, icon, etc.)
                    if (bubble.classList.contains('rounded-lg') || 
                        bubble.classList.contains('p-2') || 
                        bubble.closest('.space-y-2')) {
                        const style = bubble.getAttribute('style') || '';
                        // Only update if it looks like a message bubble (has background color)
                        if (style.includes('background') && !bubble.classList.contains('voice-message-container')) {
                            bubble.style.setProperty('background', theme.receiver_bubble, 'important');
                            if (style.includes('color')) {
                                bubble.style.setProperty('color', theme.receiver_text, 'important');
                            }
                        }
                    }
                });
                
                // Receiver voice messages
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.receiver_bubble;
                    bubble.style.borderColor = theme.receiver_bubble !== '#ffffff' ? theme.receiver_bubble : '#e5e7eb';
                    
                    const durationText = bubble.querySelector('.voice-duration');
                    if (durationText) {
                        durationText.style.color = theme.receiver_text;
                    }
                });
                
                // Update sender name colors for received messages (group chats)
                const senderNames = messageDiv.querySelectorAll('p.text-xs.font-semibold.mb-1');
                senderNames.forEach(name => {
                    name.style.setProperty('color', theme.receiver_text, 'important');
                });
                
                // Update reaction colors for received messages
                const reactions = messageDiv.querySelectorAll('.message-reactions');
                reactions.forEach(reaction => {
                    reaction.style.setProperty('color', theme.receiver_text, 'important');
                });
            });
        }
        
        function resetZoom() {
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            if (img && container) {
                currentZoom = 1;
                img.style.transform = 'scale(1)';
                container.style.cursor = 'default';
                container.scrollLeft = 0;
                container.scrollTop = 0;
                updateZoomDisplay();
            }
        }
        
        function updateZoomDisplay() {
            const resetBtn = document.getElementById('resetZoomBtn');
            if (resetBtn) {
                resetBtn.textContent = Math.round(currentZoom * 100) + '%';
            }
        }
        
        // Download image
        let isDownloading = false; // Flag to prevent multiple simultaneous downloads
        function downloadImage() {
            // Prevent multiple simultaneous downloads
            if (isDownloading) {
                return;
            }
            
            const img = document.getElementById('viewerImage');
            if (img && img.src) {
                isDownloading = true;
                const link = document.createElement('a');
                link.href = img.src;
                link.download = 'image-' + Date.now() + '.png';
                link.setAttribute('data-pdf-modal', 'false');
                document.body.appendChild(link);
                link.click();
                
                // Remove link and reset flag after a short delay
                setTimeout(() => {
                document.body.removeChild(link);
                    isDownloading = false;
                }, 100);
            }
        }
        
        // Flag to track if event listeners have been initialized
        let imageViewerListenersInitialized = false;
        // Store handler references for proper cleanup
        let downloadHandlerRef = null;
        
        // Initialize image viewer controls
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize listeners once
            if (imageViewerListenersInitialized) {
                return;
            }
            imageViewerListenersInitialized = true;
            
            const closeBtn = document.getElementById('closeImageViewer');
            const downloadBtn = document.getElementById('downloadImageViewer');
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const resetZoomBtn = document.getElementById('resetZoomBtn');
            const modal = document.getElementById('imageViewerModal');
            const imageContainer = document.getElementById('imageViewerContainer');
            
            if (closeBtn) {
                closeBtn.addEventListener('click', window.closeImageViewer);
            }
            if (downloadBtn) {
                // Create handler function and store reference
                downloadHandlerRef = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    downloadImage();
                };
                // Remove any existing listener first to prevent duplicates
                if (downloadHandlerRef) {
                    downloadBtn.removeEventListener('click', downloadHandlerRef);
                }
                downloadBtn.addEventListener('click', downloadHandlerRef);
            }
            if (zoomInBtn) {
                zoomInBtn.addEventListener('click', zoomIn);
            }
            if (zoomOutBtn) {
                zoomOutBtn.addEventListener('click', zoomOut);
            }
            if (resetZoomBtn) {
                resetZoomBtn.addEventListener('click', resetZoom);
            }
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal || e.target === imageContainer) {
                        window.closeImageViewer();
                    }
                });
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        window.closeImageViewer();
                    }
                });
            }
            
            // Group Settings Event Listeners
            const groupSettingsBtn = document.getElementById('groupSettingsBtn');
            if (groupSettingsBtn) {
                groupSettingsBtn.addEventListener('click', openGroupSettings);
            }
            
            const closeGroupSettingsModal = document.getElementById('closeGroupSettingsModal');
            const closeGroupSettingsBtn = document.getElementById('closeGroupSettingsBtn');
            if (closeGroupSettingsModal) {
                closeGroupSettingsModal.addEventListener('click', function() {
                    document.getElementById('groupSettingsModal').classList.add('hidden');
                });
            }
            if (closeGroupSettingsBtn) {
                closeGroupSettingsBtn.addEventListener('click', function() {
                    document.getElementById('groupSettingsModal').classList.add('hidden');
                });
            }
            
            const saveGroupInfoBtn = document.getElementById('saveGroupInfoBtn');
            if (saveGroupInfoBtn) {
                saveGroupInfoBtn.addEventListener('click', saveGroupInfo);
            }
            
            // Tab switching
            document.querySelectorAll('.group-settings-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    switchGroupSettingsTab(tabName);
                });
            });
            
            // Theme buttons
            const applyThemeBtn = document.getElementById('applyThemeBtn');
            if (applyThemeBtn) {
                applyThemeBtn.addEventListener('click', applyTheme);
            }
            
            const cancelThemeBtn = document.getElementById('cancelThemeBtn');
            if (cancelThemeBtn) {
                cancelThemeBtn.addEventListener('click', cancelThemeSelection);
            }
            
            const closeThemePreviewBtn = document.getElementById('closeThemePreviewBtn');
            if (closeThemePreviewBtn) {
                closeThemePreviewBtn.addEventListener('click', function() {
                    const previewSection = document.getElementById('themePreviewSection');
                    if (previewSection) previewSection.classList.add('hidden');
                });
            }
            
            const addMembersBtn = document.getElementById('addMembersBtn');
            if (addMembersBtn) {
                addMembersBtn.addEventListener('click', function() {
                    // Ensure we have current group data before opening modal
                    if (!currentGroupData && window.currentGroupId) {
                        loadGroupDetails(window.currentGroupId).then(() => {
                            openAddMembersModal();
                        });
                    } else {
                        openAddMembersModal();
                    }
                });
            }
            
            function openAddMembersModal() {
                // Close settings modal and open user selection modal
                document.getElementById('groupSettingsModal').classList.add('hidden');
                document.getElementById('userSelectionModal').classList.remove('hidden');
                loadUsersForSelection();
                
                // Store that we're adding members to a group
                window.addingMembersToGroup = true;
                window.targetGroupId = window.currentGroupId;
                
                // Change the create group button to "Add Members" when adding to existing group
                const createGroupBtn = document.getElementById('createGroupBtn');
                const openChatBtn = document.getElementById('openChatBtn');
                if (createGroupBtn) {
                    createGroupBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Add Selected Members';
                    createGroupBtn.classList.add('hidden'); // Hide initially, will show when user is selected
                }
                if (openChatBtn) {
                    openChatBtn.classList.add('hidden');
                }
            }
            
            const changeGroupAvatarBtn = document.getElementById('changeGroupAvatarBtn');
            const groupAvatarInput = document.getElementById('groupAvatarInput');
            if (changeGroupAvatarBtn && groupAvatarInput) {
                changeGroupAvatarBtn.addEventListener('click', function() {
                    groupAvatarInput.click();
                });
                
                groupAvatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (2MB max)
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Too Large',
                                text: 'Avatar image must be less than 2MB.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            this.value = '';
                            return;
                        }
                        
                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                        if (!validTypes.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: 'Please select a valid image file (JPEG, PNG, GIF, or WebP).',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            this.value = '';
                            return;
                        }
                        
                        // Preview the image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('groupSettingsAvatarPreview');
                            if (preview) {
                                preview.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                        
                        // Upload the image
                        if (!window.currentGroupId) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Group ID not found.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            return;
                        }
                        
                        const formData = new FormData();
                        formData.append('avatar_file', file);
                        
                        // Show loading
                        Swal.fire({
                            title: 'Uploading...',
                            text: 'Please wait while we upload your avatar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Use axios.post with method spoofing for file uploads (PUT doesn't work well with FormData)
                        formData.append('_method', 'PUT');
                        axios.post(`{{ route('messages.groups.update', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.currentGroupId), formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Avatar updated successfully!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                
                                // Update currentGroupData
                                if (currentGroupData && response.data.group) {
                                    currentGroupData.avatar = response.data.group.avatar;
                                }
                                
                                // Update avatar preview in settings modal
                                const avatarPreview = document.getElementById('groupSettingsAvatarPreview');
                                if (avatarPreview && response.data.group) {
                                    if (response.data.group.avatar) {
                                        avatarPreview.src = response.data.group.avatar;
                                    } else {
                                        const name = currentGroupData?.name || 'Group';
                                        avatarPreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=80&background=055498&color=fff`;
                                    }
                                }
                                
                                // Update chat header avatar if this is the current group
                                const chatHeaderAvatar = document.getElementById('chatHeaderAvatar');
                                if (chatHeaderAvatar && currentChatUserId) {
                                    const isGroup = currentChatUserId?.startsWith('group_');
                                    if (isGroup) {
                                        const groupId = currentChatUserId?.replace('group_', '');
                                        if (groupId === window.currentGroupId) {
                                            // Update the avatar in chat header
                                            const name = currentGroupData?.name || response.data.group.name || 'Group';
                                            let avatarHtml = '';
                                            if (response.data.group.avatar) {
                                                avatarHtml = `<img src="${response.data.group.avatar}" alt="${name}" class="w-10 h-10 rounded-full object-cover">`;
                                            } else {
                                                const initials = name.substring(0, 2).toUpperCase();
                                                avatarHtml = `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">${initials}</div>`;
                                            }
                                            const groupIcon = '<i class="fas fa-users text-xs text-gray-500 absolute -top-1 -right-1 bg-white rounded-full p-1"></i>';
                                            chatHeaderAvatar.innerHTML = `<div class="relative">${avatarHtml}${groupIcon}</div>`;
                                        }
                                    }
                                }
                                
                                // Reload group details to get updated data
                                loadGroupDetails(window.currentGroupId).then(() => {
                                    // Reload conversations to update avatar in conversation list
                                    loadConversations();
                                });
                            } else {
                                throw new Error(response.data.message || 'Failed to upload avatar');
                            }
                        })
                        .catch(error => {
                            console.error('Error uploading avatar:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: error.response?.data?.message || 'Failed to upload avatar. Please try again.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        });
                    }
                });
            }
            
            const removeGroupAvatarBtn = document.getElementById('removeGroupAvatarBtn');
            if (removeGroupAvatarBtn) {
                removeGroupAvatarBtn.addEventListener('click', function() {
                    if (!window.currentGroupId) return;
                    
                    Swal.fire({
                        title: 'Remove Avatar?',
                        text: 'Are you sure you want to remove the group avatar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.put(`{{ route('messages.groups.update', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.currentGroupId), {
                                avatar: null
                            })
                            .then(response => {
                                if (response.data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Removed',
                                        text: 'Avatar removed successfully!',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    
                                    // Update preview
                                    const name = currentGroupData?.name || response.data.group?.name || 'Group';
                                    const avatarPreview = document.getElementById('groupSettingsAvatarPreview');
                                    if (avatarPreview) {
                                        avatarPreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=80&background=055498&color=fff`;
                                    }
                                    
                                    // Update currentGroupData
                                    if (currentGroupData && response.data.group) {
                                        currentGroupData.avatar = null;
                                    }
                                    
                                    // Update chat header avatar if this is the current group
                                    const chatHeaderAvatar = document.getElementById('chatHeaderAvatar');
                                    if (chatHeaderAvatar && currentChatUserId) {
                                        const isGroup = currentChatUserId?.startsWith('group_');
                                        if (isGroup) {
                                            const groupId = currentChatUserId?.replace('group_', '');
                                            if (groupId === window.currentGroupId) {
                                                const initials = name.substring(0, 2).toUpperCase();
                                                const avatarHtml = `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">${initials}</div>`;
                                                const groupIcon = '<i class="fas fa-users text-xs text-gray-500 absolute -top-1 -right-1 bg-white rounded-full p-1"></i>';
                                                chatHeaderAvatar.innerHTML = `<div class="relative">${avatarHtml}${groupIcon}</div>`;
                                            }
                                        }
                                    }
                                    
                                    // Reload group details and conversations
                                    loadGroupDetails(window.currentGroupId).then(() => {
                                        loadConversations();
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error removing avatar:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.response?.data?.message || 'Failed to remove avatar.'
                                });
                            });
                        }
                    });
                });
            }
        });
        
        // Single Chat Theme Settings
        let singleChatAvailableThemes = [];
        let singleChatSelectedThemeId = null;
        let singleChatCurrentAppliedTheme = null;
        
        // Open single chat theme settings modal
        function openSingleChatThemeSettings() {
            const modal = document.getElementById('singleChatThemeModal');
            if (!modal || !window.currentSingleChatUserId) {
                console.error('Cannot open theme settings: modal or user ID missing', {
                    modal: !!modal,
                    userId: window.currentSingleChatUserId
                });
                return;
            }
            
            // Reset button states
            const previewSection = document.getElementById('singleChatThemePreviewSection');
            const applyBtn = document.getElementById('applySingleChatThemeBtn');
            const cancelBtn = document.getElementById('cancelSingleChatThemeBtn');
            const defaultFooter = document.getElementById('singleChatThemeDefaultFooter');
            const modalFooter = document.getElementById('singleChatThemeModalFooter');
            
            if (previewSection) previewSection.classList.add('hidden');
            if (applyBtn) applyBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
            if (defaultFooter) defaultFooter.classList.remove('hidden');
            if (modalFooter) modalFooter.classList.add('hidden');
            
            // Reset selected theme
            singleChatSelectedThemeId = null;
            
            modal.classList.remove('hidden');
            loadSingleChatThemes();
            loadSingleChatCurrentTheme();
        }
        
        // Load available themes for single chat
        async function loadSingleChatThemes() {
            try {
                const response = await axios.get('{{ route("messages.themes") }}');
                if (response.data.success) {
                    singleChatAvailableThemes = response.data.themes;
                    renderSingleChatThemeSelection();
                }
            } catch (error) {
                console.error('Error loading themes:', error);
            }
        }
        
        // Load current theme for single chat
        async function loadSingleChatCurrentTheme() {
            if (!window.currentSingleChatUserId) return null;
            
            try {
                const response = await axios.get(`{{ route("messages.conversation.theme", ["otherUserId" => ":userId"]) }}`.replace(':userId', window.currentSingleChatUserId));
                if (response.data.success && response.data.theme) {
                    singleChatCurrentAppliedTheme = response.data.theme;
                    singleChatSelectedThemeId = response.data.theme;
                    updateSingleChatThemeSelection();
                    return response.data.theme;
                }
                return null;
            } catch (error) {
                console.error('Error loading current theme:', error);
                return null;
            }
        }
        
        // Render theme selection grid (same format as group chat)
        function renderSingleChatThemeSelection() {
            const themeGrid = document.getElementById('singleChatThemeSelectionGrid');
            if (!themeGrid) return;
            
            if (singleChatAvailableThemes.length === 0) {
                themeGrid.innerHTML = '<div class="text-center py-12 text-gray-400"><p class="text-sm">No themes available</p></div>';
                return;
            }
            
            // Helper function to escape HTML
            const escapeHtml = (text) => {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            };
            
            themeGrid.innerHTML = singleChatAvailableThemes.map(theme => {
                const isSelected = singleChatSelectedThemeId === theme.id;
                const isApplied = singleChatCurrentAppliedTheme === theme.id;
                
                // Create thumbnail preview
                let thumbnailStyle = '';
                if (theme.background_image) {
                    thumbnailStyle = `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};`;
                } else if (theme.background.startsWith('linear-gradient')) {
                    thumbnailStyle = `background: ${theme.background};`;
                } else {
                    thumbnailStyle = `background: ${theme.background};`;
                }
                
                return `
                    <div class="theme-option group relative cursor-pointer bg-white rounded-lg border-2 transition-all duration-200 ${isSelected ? 'border-blue-500 ring-2 ring-blue-200 shadow-lg' : 'border-gray-200 hover:border-gray-300 hover:shadow-md'} overflow-hidden" 
                         data-theme-id="${theme.id}">
                        <div class="flex items-stretch">
                            <!-- Theme Preview Thumbnail -->
                            <div class="w-24 flex-shrink-0 relative overflow-hidden" style="${thumbnailStyle}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                                <!-- Preview Bubbles -->
                                <div class="absolute bottom-2 left-0 right-0 flex items-end justify-center gap-1.5 px-2">
                                    <div class="w-8 h-5 rounded-md shadow-sm border border-white/30" style="background: ${theme.receiver_bubble};"></div>
                                    <div class="w-10 h-6 rounded-md shadow-sm border border-white/30" style="background: ${theme.sender_bubble};"></div>
                                </div>
                            </div>
                            
                            <!-- Theme Info -->
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <h6 class="text-sm font-semibold text-gray-800">${escapeHtml(theme.name)}</h6>
                                        ${isSelected ? `
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-white text-[10px]"></i>
                                            </div>
                                        ` : ''}
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed mb-2">${escapeHtml(theme.description)}</p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <!-- Color Swatches -->
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.sender_bubble};"></div>
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.receiver_bubble};"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Applied Badge -->
                                    ${isApplied ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-full">
                                            <i class="fas fa-check-circle text-[9px]"></i>
                                            <span>Active</span>
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Hover Effect -->
                        <div class="absolute inset-0 bg-blue-50/0 group-hover:bg-blue-50/30 transition-all duration-200 pointer-events-none"></div>
                    </div>
                `;
            }).join('');
            
            // Add click handlers
            themeGrid.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    const themeId = this.getAttribute('data-theme-id');
                    selectSingleChatTheme(themeId);
                });
            });
        }
        
        // Select a theme
        function selectSingleChatTheme(themeId) {
            singleChatSelectedThemeId = themeId;
            updateSingleChatThemeSelection();
            showSingleChatThemePreview(themeId);
            
            // Show apply and cancel buttons
            const applyBtn = document.getElementById('applySingleChatThemeBtn');
            const cancelBtn = document.getElementById('cancelSingleChatThemeBtn');
            const defaultFooter = document.getElementById('singleChatThemeDefaultFooter');
            const modalFooter = document.getElementById('singleChatThemeModalFooter');
            
            if (applyBtn) applyBtn.classList.remove('hidden');
            if (cancelBtn) cancelBtn.classList.remove('hidden');
            if (defaultFooter) defaultFooter.classList.add('hidden');
            if (modalFooter) modalFooter.classList.remove('hidden');
        }
        
        // Update theme selection UI (same format as group chat)
        function updateSingleChatThemeSelection() {
            const themeGrid = document.getElementById('singleChatThemeSelectionGrid');
            if (!themeGrid) return;
            
            themeGrid.querySelectorAll('.theme-option').forEach(option => {
                const themeId = option.getAttribute('data-theme-id');
                const isSelected = singleChatSelectedThemeId === themeId;
                const isApplied = singleChatCurrentAppliedTheme === themeId;
                
                if (isSelected) {
                    option.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                    option.classList.remove('border-gray-200');
                    
                    // Add checkmark if not present
                    if (!option.querySelector('.fa-check')) {
                        const checkmarkContainer = option.querySelector('.flex.items-center.justify-between.mb-1');
                        if (checkmarkContainer && !checkmarkContainer.querySelector('.w-5.h-5.bg-blue-500')) {
                            const checkmark = document.createElement('div');
                            checkmark.className = 'w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm';
                            checkmark.innerHTML = '<i class="fas fa-check text-white text-[10px]"></i>';
                            checkmarkContainer.appendChild(checkmark);
                        }
                    }
                } else {
                    option.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                    option.classList.add('border-gray-200');
                    
                    // Remove checkmark
                    const checkmark = option.querySelector('.w-5.h-5.bg-blue-500');
                    if (checkmark) {
                        checkmark.remove();
                    }
                }
                
                // Update "Applied" badge
                const appliedBadge = option.querySelector('.bg-green-100');
                if (isApplied) {
                    if (!appliedBadge) {
                        const badgeContainer = option.querySelector('.flex.items-center.justify-between:last-child');
                        if (badgeContainer) {
                            const badge = document.createElement('span');
                            badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-full';
                            badge.innerHTML = '<i class="fas fa-check-circle text-[9px]"></i><span>Active</span>';
                            badgeContainer.appendChild(badge);
                        }
                    }
                } else {
                    if (appliedBadge) {
                        appliedBadge.remove();
                    }
                }
            });
        }
        
        // Show theme preview (same format as group chat)
        function showSingleChatThemePreview(themeId) {
            const theme = singleChatAvailableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const previewContainer = document.getElementById('singleChatThemePreviewContainer');
            const previewSection = document.getElementById('singleChatThemePreviewSection');
            
            if (!previewContainer || !previewSection) return;
            
            previewSection.classList.remove('hidden');
            
            // Helper function to check if color is light (matches group chat preview logic)
            const isLightColor = (color) => {
                if (!color) return false;
                
                // Check for common dark/black color names
                const darkColors = ['black', '#000', '#000000', 'rgb(0,0,0)', 'rgba(0,0,0'];
                const colorLower = color.toLowerCase().trim();
                for (const darkColor of darkColors) {
                    if (colorLower.includes(darkColor)) {
                        return false; // Definitely dark
                    }
                }
                
                let r, g, b;
                if (color.startsWith('#')) {
                    let hex = color.replace('#', '');
                    // Handle 3-digit hex
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }
                    if (hex.length === 6) {
                        r = parseInt(hex.substring(0, 2), 16);
                        g = parseInt(hex.substring(2, 4), 16);
                        b = parseInt(hex.substring(4, 6), 16);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else if (color.startsWith('rgb')) {
                    const matches = color.match(/\d+/g);
                    if (matches && matches.length >= 3) {
                        r = parseInt(matches[0]);
                        g = parseInt(matches[1]);
                        b = parseInt(matches[2]);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                
                // YIQ formula: brightness threshold lowered to 150 (matches backend)
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq > 150; // Lower threshold to catch more dark colors
            };
            
            const senderTextColor = theme.sender_text || (isLightColor(theme.sender_bubble) ? '#1f2937' : '#ffffff');
            const receiverTextColor = theme.receiver_text || (isLightColor(theme.receiver_bubble) ? '#1f2937' : '#ffffff');
            
            // Reset container styles
            previewContainer.style.cssText = '';
            previewContainer.className = 'space-y-3 bg-white rounded-lg p-0 shadow-sm min-h-[200px] sm:min-h-[250px] lg:min-h-[350px] overflow-hidden';
            
            // Create full preview matching group chat format
            previewContainer.innerHTML = `
                <!-- Chat Header Preview -->
                <div class="border-b rounded-t-lg bg-white">
                    <div class="px-3 py-2 flex items-center justify-between">
                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold truncate text-gray-800">Chat Preview</h3>
                                <p class="text-xs truncate text-gray-500">Single chat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-1.5 rounded-full hover:bg-gray-100 transition text-gray-600">
                                <i class="fas fa-cog text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area Preview -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3 min-h-[200px] max-h-[250px]" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`}">
                    <!-- Received Message -->
                    <div class="flex items-start space-x-2">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs mb-1" style="color: ${receiverTextColor}; opacity: 0.7;">John Doe</div>
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.receiver_bubble}; color: ${receiverTextColor};">
                                <p class="text-xs">Hey! How are you doing?</p>
                            </div>
                            <div class="text-[10px] mt-1" style="color: ${receiverTextColor}; opacity: 0.5;">10:30 AM</div>
                        </div>
                    </div>
                    
                    <!-- Sent Message -->
                    <div class="flex items-start space-x-2 justify-end">
                        <div class="flex-1 flex justify-end">
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.sender_bubble}; color: ${senderTextColor};">
                                <p class="text-xs">I'm doing great, thanks!</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Input Area Preview -->
                <div class="border-t rounded-b-lg p-2" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`} border-color: ${theme.receiver_bubble};">
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition" style="color: ${theme.accent_color || '#6b7280'};">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <div class="flex-1 rounded-lg border px-3 py-2" style="background: white; border-color: ${theme.receiver_bubble};">
                            <input type="text" placeholder="Type a message..." class="w-full text-xs outline-none" style="color: #1f2937;" disabled>
                        </div>
                        <button class="p-2 rounded-lg transition" style="background: ${theme.accent_color || '#3b82f6'}; color: #ffffff;">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Apply single chat theme
        async function applySingleChatTheme() {
            if (!singleChatSelectedThemeId || !window.currentSingleChatUserId) {
                console.error('Cannot apply theme: missing theme ID or user ID', {
                    themeId: singleChatSelectedThemeId,
                    userId: window.currentSingleChatUserId
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a theme first.'
                });
                return;
            }
            
            try {
                const url = `{{ route("messages.conversation.theme.apply", ["otherUserId" => ":userId"]) }}`.replace(':userId', window.currentSingleChatUserId);
                console.log('Applying theme:', { themeId: singleChatSelectedThemeId, userId: window.currentSingleChatUserId, url });
                
                const response = await axios.post(url, {
                    theme: singleChatSelectedThemeId
                });
                
                if (response.data.success) {
                    singleChatCurrentAppliedTheme = singleChatSelectedThemeId;
                    updateSingleChatThemeSelection();
                    
                    // Apply theme to chat immediately
                    applySingleChatThemeToChat(singleChatSelectedThemeId);
                    
                    // Hide preview and buttons
                    const previewSection = document.getElementById('singleChatThemePreviewSection');
                    const applyBtn = document.getElementById('applySingleChatThemeBtn');
                    const cancelBtn = document.getElementById('cancelSingleChatThemeBtn');
                    const defaultFooter = document.getElementById('singleChatThemeDefaultFooter');
                    const modalFooter = document.getElementById('singleChatThemeModalFooter');
                    const modal = document.getElementById('singleChatThemeModal');
                    
                    if (previewSection) previewSection.classList.add('hidden');
                    if (applyBtn) applyBtn.classList.add('hidden');
                    if (cancelBtn) cancelBtn.classList.add('hidden');
                    if (defaultFooter) defaultFooter.classList.remove('hidden');
                    if (modalFooter) modalFooter.classList.add('hidden');
                    
                    // Close modal
                    if (modal) modal.classList.add('hidden');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Theme Applied',
                        text: 'The chat theme has been updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error applying theme:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to apply theme.'
                });
            }
        }
        
        // Apply theme to chat messages area
        function applySingleChatThemeToChat(themeId) {
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            if (!chatMessagesArea || !window.currentSingleChatUserId) return;
            
            // Verify we're still on the same chat before applying theme
            if (currentChatUserId !== window.currentSingleChatUserId) {
                return; // User switched to a different chat, don't apply theme
            }
            
            const theme = singleChatAvailableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            // Store theme info with current user ID to ensure it's scoped correctly
            chatMessagesArea.setAttribute('data-theme-id', themeId);
            chatMessagesArea.setAttribute('data-theme-user-id', window.currentSingleChatUserId);
            
            // Apply background
            if (theme.background_image) {
                chatMessagesArea.style.backgroundImage = `url(${theme.background_image})`;
                chatMessagesArea.style.backgroundSize = 'cover';
                chatMessagesArea.style.backgroundPosition = 'center';
                chatMessagesArea.style.backgroundRepeat = 'no-repeat';
            } else {
                chatMessagesArea.style.backgroundImage = 'none';
                chatMessagesArea.style.backgroundColor = theme.background;
            }
            
            // Update existing messages with theme colors
            updateSingleChatMessageBubblesTheme(theme);
        }
        
        // Update message bubbles with theme colors
        function updateSingleChatMessageBubblesTheme(theme) {
            const chatMessagesArea = document.getElementById('chatMessagesArea');
            if (!chatMessagesArea) return;
            
            const messages = chatMessagesArea.querySelectorAll('.message-bubble, [class*="bg-gradient"], [class*="bg-white"][style*="background"]');
            messages.forEach(message => {
                const isSender = message.closest('.justify-end') !== null || message.classList.contains('sent');
                if (isSender) {
                    message.style.background = theme.sender_bubble;
                    message.style.color = theme.sender_text;
                } else {
                    message.style.background = theme.receiver_bubble;
                    message.style.color = theme.receiver_text;
                }
            });
        }
        
        // Cancel theme selection
        function cancelSingleChatThemeSelection() {
            singleChatSelectedThemeId = singleChatCurrentAppliedTheme;
            updateSingleChatThemeSelection();
            
            // Hide preview and buttons
            const previewSection = document.getElementById('singleChatThemePreviewSection');
            const applyBtn = document.getElementById('applySingleChatThemeBtn');
            const cancelBtn = document.getElementById('cancelSingleChatThemeBtn');
            const defaultFooter = document.getElementById('singleChatThemeDefaultFooter');
            const modalFooter = document.getElementById('singleChatThemeModalFooter');
            
            if (previewSection) previewSection.classList.add('hidden');
            if (applyBtn) applyBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
            if (defaultFooter) defaultFooter.classList.remove('hidden');
            if (modalFooter) modalFooter.classList.add('hidden');
        }
        
        // Event listeners for single chat theme settings
        const singleChatSettingsBtn = document.getElementById('singleChatSettingsBtn');
        if (singleChatSettingsBtn) {
            singleChatSettingsBtn.addEventListener('click', openSingleChatThemeSettings);
        }
        
        const closeSingleChatThemeModal = document.getElementById('closeSingleChatThemeModal');
        const closeSingleChatThemeBtn = document.getElementById('closeSingleChatThemeBtn');
        if (closeSingleChatThemeModal) {
            closeSingleChatThemeModal.addEventListener('click', function() {
                document.getElementById('singleChatThemeModal').classList.add('hidden');
            });
        }
        if (closeSingleChatThemeBtn) {
            closeSingleChatThemeBtn.addEventListener('click', function() {
                document.getElementById('singleChatThemeModal').classList.add('hidden');
            });
        }
        
        // Use event delegation for apply button (in case it's dynamically shown/hidden)
        document.addEventListener('click', function(e) {
            if (e.target.closest('#applySingleChatThemeBtn')) {
                e.preventDefault();
                e.stopPropagation();
                applySingleChatTheme();
            }
            if (e.target.closest('#cancelSingleChatThemeBtn')) {
                e.preventDefault();
                e.stopPropagation();
                cancelSingleChatThemeSelection();
            }
        });
        
        // Also attach directly for compatibility
        const applySingleChatThemeBtn = document.getElementById('applySingleChatThemeBtn');
        if (applySingleChatThemeBtn) {
            applySingleChatThemeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                applySingleChatTheme();
            });
        }
        
        const cancelSingleChatThemeBtn = document.getElementById('cancelSingleChatThemeBtn');
        if (cancelSingleChatThemeBtn) {
            cancelSingleChatThemeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                cancelSingleChatThemeSelection();
            });
        }
    </script>
@endsection

