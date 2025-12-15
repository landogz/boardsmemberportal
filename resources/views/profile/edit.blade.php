<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Edit Profile - Board Member Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="shortcut icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="apple-touch-icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize theme immediately before page renders to prevent flash
        (function() {
            const theme = localStorage.getItem('theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <style>
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #055498 0%, #123a60 50%, #055498 100%);
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
        .profile-picture-container {
            position: relative;
            display: inline-block;
        }
        .profile-picture-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            border-radius: 50%;
            cursor: pointer;
        }
        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
        }
    </style>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Profile</h1>

            <!-- Profile Picture Section -->
            <div class="mb-8 text-center">
                <div class="profile-picture-container inline-block">
                    @php
                        $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=200&background=055498&color=fff';
                        if ($user->profile_picture) {
                            $media = \App\Models\MediaLibrary::find($user->profile_picture);
                            if ($media) {
                                $profilePic = asset('storage/' . $media->file_path);
                            }
                        }
                    @endphp
                    <img id="profilePicturePreview" src="{{ $profilePic }}" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover shadow-lg" style="border: 4px solid #055498;">
                    <div class="profile-picture-overlay">
                        <span class="text-white font-semibold">Change</span>
                    </div>
                </div>
                <input type="file" id="profilePictureInput" accept="image/*" class="hidden">
                <p class="text-sm text-gray-600 mt-4">Click on the image to change your profile picture</p>
            </div>

            <form id="profileForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            value="{{ $user->first_name }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-transparent outline-none transition"
                            style="focus:ring-color: #055498;"
                        >
                        <span class="text-red-500 text-sm hidden" id="first_name-error"></span>
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            value="{{ $user->last_name }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-transparent outline-none transition"
                            style="focus:ring-color: #055498;"
                        >
                        <span class="text-red-500 text-sm hidden" id="last_name-error"></span>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ $user->email }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                    >
                    <span class="text-red-500 text-sm hidden" id="email-error"></span>
                </div>

                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                    <input 
                        type="text" 
                        id="mobile" 
                        name="mobile" 
                        value="{{ $user->mobile }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                    >
                    <span class="text-red-500 text-sm hidden" id="mobile-error"></span>
                </div>

                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <input 
                        type="text" 
                        id="company" 
                        name="company" 
                        value="{{ $user->company }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                    >
                    <span class="text-red-500 text-sm hidden" id="company-error"></span>
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <input 
                        type="text" 
                        id="position" 
                        name="position" 
                        value="{{ $user->position }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        placeholder="Your position/title"
                    >
                    <span class="text-red-500 text-sm hidden" id="position-error"></span>
                </div>

                <div>
                    <label for="representative_name" class="block text-sm font-medium text-gray-700 mb-2">Representative Name</label>
                    <input 
                        type="text" 
                        id="representative_name" 
                        name="representative_name" 
                        value="{{ $user->representative_name }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                    >
                    <span class="text-red-500 text-sm hidden" id="representative_name-error"></span>
                </div>

                <div class="pt-4 border-t">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Change Password</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-transparent outline-none transition"
                            style="focus:ring-color: #055498;"
                            >
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-transparent outline-none transition"
                            style="focus:ring-color: #055498;"
                            >
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-transparent outline-none transition"
                            style="focus:ring-color: #055498;"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('landing') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        id="saveBtn"
                        class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                        style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                    >
                        <span id="saveBtnText">Save Changes</span>
                        <span id="saveBtnLoader" class="hidden">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Profile picture preview
        const profilePictureInput = document.getElementById('profilePictureInput');
        const profilePicturePreview = document.getElementById('profilePicturePreview');
        const profilePictureContainer = document.querySelector('.profile-picture-container');

        profilePictureContainer.addEventListener('click', () => {
            profilePictureInput.click();
        });

        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicturePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('first_name', document.getElementById('first_name').value);
            formData.append('last_name', document.getElementById('last_name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('mobile', document.getElementById('mobile').value);
            formData.append('company', document.getElementById('company').value);
            formData.append('position', document.getElementById('position').value);
            formData.append('representative_name', document.getElementById('representative_name').value);
            
            if (profilePictureInput.files[0]) {
                formData.append('profile_picture', profilePictureInput.files[0]);
            }

            const currentPassword = document.getElementById('current_password').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            const saveBtn = document.getElementById('saveBtn');
            const saveBtnText = document.getElementById('saveBtnText');
            const saveBtnLoader = document.getElementById('saveBtnLoader');

            // Clear previous errors
            document.querySelectorAll('.text-red-500').forEach(el => {
                el.classList.add('hidden');
            });

            // Disable button
            saveBtn.disabled = true;
            saveBtnText.classList.add('hidden');
            saveBtnLoader.classList.remove('hidden');

            try {
                // Update profile
                const response = await axios.post('/profile/update', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Update password if provided
                if (currentPassword && password && passwordConfirmation) {
                    await axios.post('/profile/password', {
                        current_password: currentPassword,
                        password: password,
                        password_confirmation: passwordConfirmation
                    });
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Profile updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    if (response.data.user.profile_picture_url) {
                        profilePicturePreview.src = response.data.user.profile_picture_url;
                    }
                    window.location.reload();
                });
            } catch (error) {
                saveBtn.disabled = false;
                saveBtnText.classList.remove('hidden');
                saveBtnLoader.classList.add('hidden');

                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    
                    Object.keys(errors).forEach(field => {
                        const errorElement = document.getElementById(field + '-error');
                        if (errorElement) {
                            errorElement.textContent = errors[field][0];
                            errorElement.classList.remove('hidden');
                        }
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form for errors',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.',
                    });
                }
            }
        });
    </script>

    @include('components.footer')

    @auth
    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Handle navigation links to landing page sections
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.includes('{{ route("landing") }}')) {
                    e.preventDefault();
                    window.location.href = href;
                }
            });
        });
    </script>
    @endauth
</body>
</html>

