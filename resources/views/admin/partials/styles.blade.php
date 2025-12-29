<style>
    /* Typography Standards */
    
    /* Body Text - Gotham or Montserrat, 14-16px, 1-1.5 line height */
    body, p, span, div, li, td, th, label, input, textarea, select, button {
        font-family: 'Gotham Rounded', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        font-size: 14px; /* 14px digital (default) */
        line-height: 1.5; /* 1.5 line height for readability */
    }
    
    /* Titles/Headlines - Montserrat Bold (or Gotham Bold fallback), bumped sizes for clarity */
    h1, .title, .headline {
        font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-weight: 700; /* Bold */
        font-size: 34px; /* Larger headline */
        line-height: 1.3; /* Slightly taller for readability */
    }
    
    /* Headers/Subheaders - Montserrat Semi-Bold, bumped sizes for clarity */
    h2, h3, h4, h5, h6, .header, .subheader {
        font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-weight: 600; /* Semi-Bold */
        font-size: 24px; /* Larger default for headers/subheaders */
        line-height: 1.3;
    }
    
    /* Specific heading sizes */
    h2 {
        font-size: 28px;
    }
    
    h3 {
        font-size: 24px;
    }
    
    h4 {
        font-size: 22px;
    }
    
    h5, h6 {
        font-size: 20px;
    }
    
    /* Small text adjustments */
    small, .text-sm, .text-xs {
        font-size: 12px;
        line-height: 1.5;
    }
    
    /* Custom styles for sidebar toggle and animations */
    #sidebar {
        transition: transform 0.3s ease-in-out;
    }
    
    #sidebar.hidden {
        transform: translateX(-100%);
    }
    
    @media (min-width: 1024px) {
        #sidebar {
            transform: translateX(0);
        }
    }
    
    /* Dropdown styles */
    .dropdown-menu {
        display: none !important;
    }
    
    /* Default dropdown - use block for vertical list */
    .dropdown.show .dropdown-menu {
        display: block !important;
    }
    
    /* Notifications and messages dropdowns - use flex for complex layout */
    .dropdown.show #notificationsMenu,
    .dropdown.show .dropdown-menu.flex {
        display: flex !important;
    }
    
    /* Ensure dropdown menu items use flex */
    .dropdown-menu a {
        display: flex !important;
        white-space: nowrap;
    }
    
    /* Notification and Message items styling */
    .notification-item,
    .message-item {
        transition: background-color 0.2s ease;
        position: relative;
        overflow: visible;
    }
    
    .notification-item:hover,
    .message-item:hover {
        background-color: #f9fafb !important;
    }
    
    /* Notification menu dropdown styles */
    .notification-menu-dropdown {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 9999 !important;
    }
    
    .notification-menu-dropdown button {
        cursor: pointer;
    }
    
    .notification-menu-dropdown button:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .notification-menu-container {
        z-index: 100;
        position: relative;
    }
    
    .notification-menu-dropdown[style*="display: block"] {
        display: block !important;
    }
    
    #notificationsList .notification-item {
        position: relative;
        overflow: visible;
    }
    
    /* Notification icon container */
    .notification-item .flex-shrink-0 {
        flex-shrink: 0;
    }
    
    /* Message avatar styling */
    .message-item .flex-shrink-0 {
        flex-shrink: 0;
    }
    
    /* Better text spacing */
    .notification-item p,
    .message-item p {
        margin: 0;
        line-height: 1.4;
    }
    
    /* Truncate text properly */
    .notification-item .truncate,
    .message-item .truncate {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Mobile responsive adjustments for dropdowns */
    @media (max-width: 640px) {
        /* Prevent overflow and limit height on mobile */
        #notificationsMenu,
        .dropdown-menu.flex {
            max-width: calc(100vw - 1rem) !important;
            max-height: 60vh !important;
            right: 0.5rem !important;
        }
    }
    
    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Online status indicator */
    .online_animation {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #055498;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    /* Global form input borders: darker lines for text-like fields */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="date"],
    input[type="number"],
    textarea,
    select {
        border-color: #4b5563 !important;
        border-width: 1.5px !important;
    }
    
    
    /* Global Tooltip styles for action buttons */
    .action-btn {
        position: relative;
        cursor: pointer;
    }
    
    .action-btn[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-bottom: 8px;
        padding: 6px 10px;
        background-color: #1f2937;
        color: #fff;
        font-size: 12px;
        white-space: nowrap;
        border-radius: 4px;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        animation: tooltipFadeIn 0.2s ease-in forwards;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .action-btn[data-tooltip]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-bottom: 2px;
        border: 5px solid transparent;
        border-top-color: #1f2937;
        z-index: 1001;
        pointer-events: none;
        opacity: 0;
        animation: tooltipFadeIn 0.2s ease-in forwards;
    }
    
    @keyframes tooltipFadeIn {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-3px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    /* Global Responsive Improvements */
    
    /* Ensure all admin pages have proper mobile padding */
    @media (max-width: 640px) {
        .p-4.lg\:p-6,
        .p-6 {
            padding: 1rem !important;
        }
        
        /* Make buttons touch-friendly on mobile */
        button, .btn, a[role="button"] {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Ensure tables are scrollable */
        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Responsive text sizes */
        h1, .text-2xl {
            font-size: 1.5rem !important;
        }
        
        h2, .text-xl {
            font-size: 1.25rem !important;
        }
        
        h3, .text-lg {
            font-size: 1.125rem !important;
        }
        
        /* Ensure cards stack properly */
        .grid {
            grid-template-columns: 1fr !important;
        }
        
        /* Fix flex layouts on mobile */
        .flex.flex-wrap {
            flex-wrap: wrap;
        }
        
        /* Ensure modals are mobile-friendly */
        .modal, .swal2-popup {
            max-width: calc(100vw - 2rem) !important;
            margin: 1rem !important;
        }
    }
    
    /* Tablet responsive (641px - 1024px) */
    @media (min-width: 641px) and (max-width: 1024px) {
        .grid-cols-1.md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        
        .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    
    /* Ensure DataTables are responsive */
    @media (max-width: 767px) {
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            margin-top: 0.5rem;
        }
        
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            width: 100% !important;
            margin: 0.25rem 0;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.5rem !important;
            margin: 0.125rem !important;
        }
    }
    
    /* Cross-browser compatibility */
    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    
    /* Ensure smooth scrolling on all devices */
    html {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    
    /* Fix for iOS Safari viewport */
    @supports (-webkit-touch-callout: none) {
        .min-h-screen {
            min-height: -webkit-fill-available;
        }
    }
    
    /* Ensure proper text rendering */
    body {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }
    
    /* Responsive step indicators */
    @media (max-width: 640px) {
        .step-indicator {
            margin-bottom: 1rem !important;
        }
        
        .step-indicator .step-number {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.875rem !important;
        }
        
        .step-indicator .step-label {
            font-size: 0.625rem !important;
            margin-top: 4px !important;
        }
        
        .step-indicator::before {
            top: 16px !important;
        }
    }
    
    /* Responsive form inputs */
    @media (max-width: 640px) {
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="number"],
        textarea,
        select {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
    }
    
    /* Ensure DataTables are responsive on all devices */
    @media (max-width: 767px) {
        .dataTables_wrapper {
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper table {
            font-size: 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .dataTables_wrapper .dataTables_info {
            font-size: 0.75rem;
            padding: 0.5rem 0;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.5rem !important;
            margin: 0.125rem !important;
            min-width: 32px;
            min-height: 32px;
        }
    }
    
    /* Responsive table cells */
    @media (max-width: 767px) {
        table td,
        table th {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem;
        }
        
        table th {
            font-size: 0.625rem;
        }
    }
    
    /* Responsive cards and containers */
    @media (max-width: 640px) {
        .bg-white.rounded-lg {
            border-radius: 0.5rem;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
    }
    
    /* Ensure modals are mobile-friendly */
    @media (max-width: 640px) {
        .swal2-popup {
            width: calc(100vw - 2rem) !important;
            margin: 1rem !important;
            padding: 1rem !important;
        }
        
        .swal2-title {
            font-size: 1.25rem !important;
        }
        
        .swal2-content {
            font-size: 0.875rem !important;
        }
        
        .swal2-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .swal2-confirm,
        .swal2-cancel {
            width: 100% !important;
            margin: 0.25rem 0 !important;
        }
    }
    
    /* Responsive grid improvements */
    @media (max-width: 640px) {
        .grid {
            gap: 0.75rem !important;
        }
        
        .grid-cols-1.md\:grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        
        .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3 {
            grid-template-columns: 1fr !important;
        }
        
        .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
            grid-template-columns: 1fr !important;
        }
    }
    
    /* Responsive flex improvements */
    @media (max-width: 640px) {
        .flex.items-center.justify-between {
            flex-direction: row;
        }
        
        .flex.items-center.space-x-4 {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }
    
    /* Touch-friendly interactive elements */
    @media (hover: none) and (pointer: coarse) {
        a, button, .btn, [role="button"] {
            min-height: 44px;
            min-width: 44px;
        }
        
        input[type="checkbox"],
        input[type="radio"] {
            width: 20px;
            height: 20px;
        }
    }
    
    /* General DataTable row hover effect for all admin tables */
    table.dataTable tbody tr {
        transition: background-color 0.2s ease;
        cursor: default;
    }
    
    table.dataTable tbody tr:hover {
        background-color: #f3f4f6;
    }
    
    
    /* Ensure action buttons and dropdowns are not affected by row hover */
    table.dataTable tbody tr:hover .action-dropdown-btn,
    table.dataTable tbody tr:hover button[class*="action"],
    table.dataTable tbody tr:hover .btn {
        background-color: white;
        border-color: #d1d5db;
    }
    
    
    /* Keep dropdown menus styling independent */
    .action-dropdown-menu,
    .dropdown-menu {
        background-color: white !important;
    }
    
    
    /* Ensure action cells don't change on row hover */
    table.dataTable tbody tr td:last-child,
    table.dataTable tbody tr td[class*="action"] {
        background-color: transparent;
    }
    
    table.dataTable tbody tr:hover td:last-child,
    table.dataTable tbody tr:hover td[class*="action"] {
        background-color: transparent;
    }
    
    /* Hide messages popup container and chat heads on mobile devices globally */
    @media (max-width: 767px) {
        /* Hide the entire messages popup container on mobile */
        #messagesPopupContainer {
            display: none !important;
        }
        
        /* Hide chat popup headers on mobile */
        #messagesPopupContainer .messages-popup .flex.items-center.justify-between.p-4,
        #messagesPopupContainer .messages-popup [class*="popup-header"],
        #messagesPopupContainer .messages-popup [class*="chat-header"] {
            display: none !important;
        }
    }
    
    /* Force light mode for reactions modal globally */
    #reactionsModal,
    #reactionsModal * {
        background-color:rgba(255, 255, 255, 0) !important;
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
    
    #reactionsModal .text-gray-900,
    #reactionsModal .text-gray-800 {
        color: #1f2937 !important;
    }
    
    #reactionsModal .text-gray-500 {
        color: #6b7280 !important;
    }
    
    #reactionsModal .text-gray-700 {
        color: #374151 !important;
    }
    
    #reactionsModal .border-gray-200 {
        border-color: #e5e7eb !important;
    }
    
    #reactionsModal .border-gray-700 {
        border-color: #374151 !important;
    }
    
    
    #reactionsModal [class*="bg-gray-800"],
    #reactionsModal [class*="bg-gray-900"],
    #reactionsModal [class*="bg-gray-700"] {
        background-color: #ffffff !important;
    }
    
    #reactionsModal [class*="text-white"],
    #reactionsModal [class*="text-gray-400"] {
        color: #1f2937 !important;
    }
    
    #reactionsModal .hover\:bg-gray-100:hover {
        background-color: #f3f4f6 !important;
    }
    
    #reactionsModal .hover\:bg-gray-200:hover {
        background-color: #e5e7eb !important;
    }
</style>

