<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel + Tailwind + Axios Example</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Laravel + Tailwind CSS + Axios Example</h1>
            
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Test Form</h2>
                <form id="testForm" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your name"
                        >
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your email"
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        Submit (Axios)
                    </button>
                </form>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button 
                        id="successBtn" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        Success Alert
                    </button>
                    <button 
                        id="errorBtn" 
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        Error Alert
                    </button>
                    <button 
                        id="infoBtn" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        Info Alert
                    </button>
                </div>
            </div>

            <div id="responseArea" class="mt-6 p-4 bg-gray-50 rounded-lg hidden">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Response:</h3>
                <pre id="responseContent" class="text-sm text-gray-600"></pre>
            </div>
        </div>
    </div>

    <script>
        // Wait for jQuery and other dependencies to load
        (function() {
            function initApp() {
                if (typeof $ === 'undefined' || typeof axios === 'undefined' || typeof Swal === 'undefined') {
                    setTimeout(initApp, 100);
                    return;
                }

                $(document).ready(function() {
                    // Set CSRF token for Axios
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

                    // Handle form submission with Axios
                    $('#testForm').on('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = {
                            name: $('#name').val(),
                            email: $('#email').val()
                        };

                        axios.post('/api/test', formData)
                            .then(function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Form submitted successfully',
                                    confirmButtonColor: '#10b981'
                                });
                                
                                $('#responseArea').removeClass('hidden');
                                $('#responseContent').text(JSON.stringify(response.data, null, 2));
                            })
                            .catch(function(error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Something went wrong',
                                    confirmButtonColor: '#ef4444'
                                });
                            });
                    });

                    // Success Alert Button
                    $('#successBtn').on('click', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Great!',
                            text: 'This is a success message using SweetAlert2',
                            confirmButtonColor: '#10b981'
                        });
                    });

                    // Error Alert Button
                    $('#errorBtn').on('click', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'This is an error message using SweetAlert2',
                            confirmButtonColor: '#ef4444'
                        });
                    });

                    // Info Alert Button
                    $('#infoBtn').on('click', function() {
                        Swal.fire({
                            icon: 'info',
                            title: 'Information',
                            text: 'This is an info message using SweetAlert2',
                            confirmButtonColor: '#3b82f6'
                        });
                    });
                }); // End of $(document).ready
            } // End of initApp function
            
            // Start initialization
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initApp);
            } else {
                initApp();
            }
        })(); // End of IIFE
    </script>
</body>
</html>

