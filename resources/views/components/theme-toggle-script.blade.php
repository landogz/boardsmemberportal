<script>
    // Dark Mode Toggle - Reusable across all pages
    (function() {
        // Get theme from localStorage or default to light
        function getTheme() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                return savedTheme;
            }
            // Default to light mode (ignore system preference)
            return 'light';
        }

        // Apply theme
        function applyTheme(theme) {
            const html = document.documentElement;
            
            if (theme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            
            // Update icons
            updateThemeIcons(theme);
        }

        // Update theme icons
        function updateThemeIcons(theme) {
            const icon = theme === 'dark' ? '☀️' : '🌙';
            const themeIcon = document.getElementById('themeIcon');
            const themeIconMobile = document.getElementById('themeIconMobile');
            if (themeIcon) themeIcon.textContent = icon;
            if (themeIconMobile) themeIconMobile.textContent = icon;
        }

        // Toggle theme function - Make it globally available
        window.toggleTheme = function() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        };

        // Initialize theme on page load
        const theme = getTheme();
        applyTheme(theme);

        // Wait for DOM to be ready and attach event listeners
        function initThemeToggle() {
            const themeToggle = document.getElementById('themeToggle');
            const themeToggleMobile = document.getElementById('themeToggleMobile');
            
            if (themeToggle) {
                // Remove onclick and add event listener
                themeToggle.removeAttribute('onclick');
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.toggleTheme) {
                        window.toggleTheme();
                    }
                });
            }
            
            if (themeToggleMobile) {
                // Remove onclick and add event listener
                themeToggleMobile.removeAttribute('onclick');
                themeToggleMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.toggleTheme) {
                        window.toggleTheme();
                    }
                });
            }
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initThemeToggle);
        } else {
            // If DOM is already loaded, run immediately
            setTimeout(initThemeToggle, 0);
        }

        // Also listen for theme changes from other tabs/windows
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') {
                const newTheme = e.newValue || 'light';
                applyTheme(newTheme);
            }
        });
    })();
</script>

