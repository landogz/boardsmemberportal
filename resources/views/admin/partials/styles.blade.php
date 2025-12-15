<style>
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
        display: none;
    }
    
    .dropdown.show .dropdown-menu {
        display: block;
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
</style>

