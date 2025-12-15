<script>
    $(document).ready(function() {
        // Sidebar Toggle
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('-translate-x-full');
            $('#sidebarOverlay').toggleClass('hidden');
        });
        
        $('#sidebarOverlay').on('click', function() {
            $('#sidebar').addClass('-translate-x-full');
            $('#sidebarOverlay').addClass('hidden');
        });

        // Menu Toggle
        $('.menu-toggle').on('click', function(e) {
            e.preventDefault();
            const $menuItem = $(this).parent('li');
            const $submenu = $menuItem.find('> ul');
            $submenu.slideToggle();
            $(this).find('.fa-chevron-down').toggleClass('rotate-180');
        });
        
        // Dropdown Toggle
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().toggleClass('show');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
            }
        });

        // Logout functionality
        $('#logoutBtn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("logout") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.redirect || '/';
                            }
                        },
                        error: function() {
                            window.location.href = '/';
                        }
                    });
                }
            });
        });
    });
</script>

