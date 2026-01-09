<!-- Agency Footer - 1190 W, H varies - Mandatory, Customizable -->
<div class="agency-footer">
    <div class="container mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-bold mb-4" style="color: #055498;">Board Portal</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Modern board management platform for efficient collaboration and communication.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4" style="color: #055498;">Quick Links</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    @auth
                    <li><a href="{{ route('landing') }}#announcements" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Announcements</a></li>
                    <li><a href="{{ route('landing') }}#calendar-activities" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Calendar Activities</a></li>
                    <li><a href="{{ route('board-issuances') }}" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Board Issuances</a></li>
                    <li><a href="{{ route('referendums.index') }}" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Referendums</a></li>
                    <li><a href="{{ route('notices.index') }}" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Notices</a></li>
                    @else
                    <li><a href="#about" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">About</a></li>
                    <li><a href="#contact" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Contact</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4" style="color: #055498;">Account</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    @auth
                        <li><a href="{{ route('profile.edit') }}" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">My Profile</a></li>
                    @else
                        <li><a href="/login" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Login</a></li>
                        <li><a href="/register" class="transition" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Register</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4" style="color: #055498;">Contact</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>Email: info@boardportal.gov.ph</li>
                    <li>Phone: +63 (2) 1234-5678</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Standard Footer - 1190 W, H varies - Mandatory, Locked -->
<div class="standard-footer">
    <div class="container mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="republic-seal-container">
                <img src="{{ asset('images/republica.png') }}" 
                     alt="Republic of the Philippines" 
                     class="republic-seal">
            </div>
            <div>
                <h4 class="mb-2">REPUBLIC OF THE PHILIPPINES</h4>
                <p>All content is in the public domain unless otherwise stated.</p>
            </div>
            <div>
                <h4 class="mb-2">ABOUT PORTAL</h4>
                <p class="mb-2">Learn more about the Board Member Portal, its features, and how it facilitates seamless board management.</p>
                <ul class="space-y-1" style="list-style: none; padding: 0;">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#announcements">Announcements</a></li>
                    <li><a href="#meetings">Public Meetings</a></li>
                </ul>
            </div>
            <div>
                <h4 class="mb-2">GOVERNMENT LINKS</h4>
                <ul class="space-y-1" style="list-style: none; padding: 0;">
                    <li><a href="https://www.gov.ph" target="_blank" rel="noopener noreferrer">GOV.PH</a></li>
                    <li><a href="https://data.gov.ph" target="_blank" rel="noopener noreferrer">Open Data Portal</a></li>
                    <li><a href="https://www.officialgazette.gov.ph" target="_blank" rel="noopener noreferrer">Official Gazette</a></li>
                    <li><a href="https://www.president.gov.ph" target="_blank" rel="noopener noreferrer">Office of the President</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-600 pt-4 text-center">
            <p>&copy; {{ date('Y') }} Board Member Portal. All rights reserved. | Republic of the Philippines</p>
        </div>
    </div>
</div>

