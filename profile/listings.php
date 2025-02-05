<?php
require_once '../classes/venue.class.php';

session_start();

$venueObj = new Venue();
$venuePost = $venueObj->getAllVenues('', $_SESSION['user']['id']);

?>
<main class="max-w-7xl mx-auto py-6 pt-20 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0" id="mainContent">
        <!-- Main Listings View -->
        <div id="listingsView" class="bg-gray-50 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Your listings</h1>
                <?php if ($_SESSION['user']['user_type_id'] == 1) { ?>
                    <div class="flex items-center space-x-4">
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                            id="addVenueButton">
                            <span class="text-lg font-semibold">+</span>
                            <span class="ml-2 hidden sm:inline">Add Venue</span>
                        </button>
                    </div>
                <?php } ?>
            </div>

            <?php
            if ($_SESSION['user']['user_type_id'] == 2) {
                echo '<p class="text-gray-600 text-left mb-6 bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded">You need to apply for a Host Account before you can list your venue.</p>';
            }

            if (empty($venuePost)) {
                echo '<p class="text-gray-600 text-left mb-6 bg-blue-100 border-l-4 border-blue-500 p-4 rounded">No listings found. Start by adding your first venue!</p>';
            }
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($venuePost as $venue): ?>
                    <a class="venue-card cursor-pointer group"
                        data-id="<?php echo htmlspecialchars($venue['venue_id']); ?>">
                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-1">
                            <div class="relative">
                                <?php
                                $statusColors = [
                                    'Approved' => 'bg-green-500',
                                    'Pending' => 'bg-yellow-500',
                                    'Declined' => 'bg-red-500'
                                ];
                                $statusColor = $statusColors[$venue['status']] ?? 'bg-gray-500';
                                ?>
                                <div
                                    class="absolute top-2 right-2 <?php echo $statusColor; ?> text-white px-3 py-1 rounded-full text-xs font-semibold z-10">
                                    <?php echo htmlspecialchars($venue['status']); ?>
                                </div>
                                <img src="./<?php echo !empty($venue['image_urls'][0]) ? $venue['image_urls'][0] : '../images/icoco_black_ico.png'; ?>"
                                    alt="<?php echo htmlspecialchars($venue['name'] ?? 'Venue'); ?>"
                                    class="w-full h-56 object-cover transition duration-300 ease-in-out group-hover:scale-105">
                            </div>
                            <div class="p-6">
                                <h3 class="font-semibold text-xl mb-2 text-gray-900">
                                    <?php echo htmlspecialchars($venue['name']); ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($venue['location'] ?? 'No location specified'); ?>
                                </p>
                                <!-- Uncomment if you want to display the price -->
                                <p class="text-blue-600 font-bold text-lg">
                                    ₱<?php echo number_format($venue['price'] ?? 0, 2); ?> <span
                                        class="text-gray-500 text-sm font-normal">/ day</span>
                                </p>

                                <div class="mt-4 flex justify-end">
                                    <span
                                        class="text-blue-600 group-hover:text-blue-800 transition duration-300 ease-in-out">View
                                        Details →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<!-- <script>
    function showVenueDetails(venue) {
        // Hide listings view and show details view
        document.getElementById('listingsView').classList.add('hidden');
        document.getElementById('venueDetailsView').classList.remove('hidden');

        // Populate venue details
        document.getElementById('detailVenueName').textContent = venue.name;
        document.getElementById('detailVenueLocation').textContent = venue.location;
        document.getElementById('detailVenueDescription').textContent = venue.description || 'No description provided yet';
        document.getElementById('detailVenueCapacity').textContent = venue.capacity ? `${venue.capacity} guests` : 'Capacity not specified';
        document.getElementById('detailVenuePrice').textContent = venue.price;

        // Populate amenities with descriptions
        const amenitiesList = document.getElementById('detailVenueAmenities');
        amenitiesList.innerHTML = '';
        if (Array.isArray(venue.amenities) && venue.amenities.length === 0) {
            amenitiesList.innerHTML = '<p class="text-gray-500 italic">No amenities listed yet</p>';
        } else if (typeof venue.amenities === 'object') {
            Object.entries(venue.amenities).forEach(([amenity, details]) => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2 mb-3';
                if (details && details.description) {
                    // Detailed amenity format
                    li.innerHTML = `
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="font-medium">${amenity}</span>
                        <p class="text-sm text-gray-500">${details.description}</p>
                    </div>
                `;
                } else {
                    // Simple amenity format
                    li.innerHTML = `
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="font-medium">${amenity}</span>
                    </div>
                `;
                }
                amenitiesList.appendChild(li);
            });
        }

        // Populate rules with sections
        const rulesList = document.getElementById('detailVenueRules');
        rulesList.innerHTML = '';
        if (Array.isArray(venue.rules) && venue.rules.length === 0) {
            rulesList.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-yellow-700 font-medium">Required: Please add venue rules</p>
                </div>
                <p class="text-yellow-600 text-sm mt-2">Specify guidelines and restrictions for venue use</p>
            </div>
        `;
        } else {
            // Existing rules display code...
        }

        // Populate cancellation policy with detailed breakdown
        const policyDiv = document.getElementById('detailCancellationPolicy');
        if (!venue.cancellation_policy) {
            policyDiv.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-yellow-700 font-medium">Required: Please add cancellation policy</p>
            </div>
                <p class="text-yellow-600 text-sm mt-2">Define your booking cancellation terms and conditions</p>
            </div>
        `;
        } else {
            // Existing cancellation policy display code...
        }

        // Populate owner information
        const ownerDiv = document.getElementById('detailVenueOwner');
        ownerDiv.innerHTML = `
        <p><strong>Name:</strong> ${venue.owner.first_name} ${venue.owner.last_name}</p>
        <p><strong>Contact:</strong> ${venue.owner.contact}</p>
        <p><strong>Email:</strong> ${venue.owner.email}</p>
    `;

        // Show status badge
        const statusDiv = document.getElementById('detailVenueStatus');
        let statusColor = '';
        switch (venue.status) {
            case 'Approved':
                statusColor = 'bg-green-500 border-green-600';
                break;
            case 'Pending':
                statusColor = 'bg-yellow-500 border-yellow-600';
                break;
            case 'Declined':
                statusColor = 'bg-red-500 border-red-600';
                break;
        }
        statusDiv.innerHTML = `
        <div class="inline-block border ${statusColor} text-white px-3 py-1 rounded-full">
            ${venue.status}
        </div>
    `;

        // Set edit mode fields initial values
        document.getElementById('editVenueLocation').value = venue.location;
        document.getElementById('editVenueDescription').value = venue.description;
        document.getElementById('editVenueCapacity').value = venue.capacity;

        // Reset to view mode when showing details
        const viewElements = document.querySelectorAll('.view-mode');
        const editElements = document.querySelectorAll('.edit-mode');
        viewElements.forEach(el => el.classList.remove('hidden'));
        editElements.forEach(el => el.classList.add('hidden'));

        // Handle missing images
        const mainImage = document.getElementById('mainImage');
        mainImage.src = venue.image_urls && venue.image_urls.length > 0
            ? venue.image_urls[0]
            : '../images/icoco_black_ico.png';
        mainImage.alt = venue.name || 'Venue image';
    }

    function showListings() {
        // Hide details view and show listings view
        document.getElementById('venueDetailsView').classList.add('hidden');
        document.getElementById('listingsView').classList.remove('hidden');
    }

    function toggleEditMode() {
        const viewElements = document.querySelectorAll('.view-mode');
        const editElements = document.querySelectorAll('.edit-mode');

        viewElements.forEach(el => el.classList.toggle('hidden'));
        editElements.forEach(el => el.classList.toggle('hidden'));

        // Populate edit fields with current values
        if (!editElements[0].classList.contains('hidden')) {
            document.getElementById('editVenueLocation').value = document.getElementById('detailVenueLocation').textContent;
            document.getElementById('editVenueDescription').value = document.getElementById('detailVenueDescription').textContent;
            document.getElementById('editVenueCapacity').value = document.getElementById('detailVenueCapacity').textContent.split(' ')[0];
            populateAmenitiesEdit();

            // Populate rules edit fields
            const currentRules = Array.from(document.getElementById('detailVenueRules').children)
                .map(li => li.textContent);
            const rulesList = document.getElementById('rulesList');
            rulesList.innerHTML = '';
            currentRules.forEach(rule => {
                addRuleField(rule);
            });

            // Handle cancellation policy edit mode
            document.getElementById('editCancellationPolicy').addEventListener('change', function () {
                const customPolicyField = document.getElementById('editCustomPolicy');
                if (this.value === 'custom') {
                    customPolicyField.classList.remove('hidden');
                } else {
                    customPolicyField.classList.add('hidden');
                }
            });
        }
    }

    function populateAmenitiesEdit() {
        const amenitiesList = document.getElementById('amenitiesList');
        amenitiesList.innerHTML = '';

        const currentAmenities = Array.from(document.getElementById('detailVenueAmenities').children)
            .map(li => li.textContent);

        currentAmenities.forEach(amenity => {
            addAmenityField(amenity);
        });
    }

    function addAmenityField(value = '') {
        const amenitiesList = document.getElementById('amenitiesList');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
        <input type="text" class="form-input rounded-md flex-grow" value="${value}">
        <button onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
        amenitiesList.appendChild(div);
    }

    function addRuleField(value = '') {
        const rulesList = document.getElementById('rulesList');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
        <input type="text" class="form-input rounded-md flex-grow" value="${value}" placeholder="Enter venue rule">
        <button onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
        rulesList.appendChild(div);
    }

    // Add this to your existing script section
    function initializeCalendar() {
        // Get current date
        const date = new Date();
        const currentMonth = date.getMonth();
        const currentYear = date.getFullYear();

        // Update calendar header
        updateCalendarHeader(currentMonth, currentYear);

        // Generate calendar days
        generateCalendarDays(currentMonth, currentYear);
    }

    function updateCalendarHeader(month, year) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        document.querySelector('.calendar-header h4').textContent = `${monthNames[month]} ${year}`;
    }

    function generateCalendarDays(month, year) {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let calendarHTML = '';

        // Previous month days
        for (let i = 0; i < firstDay; i++) {
            calendarHTML += `<div class="p-2 border-b border-r text-gray-400"></div>`;
        }

        // Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = day === new Date().getDate() &&
                month === new Date().getMonth() &&
                year === new Date().getFullYear();

            calendarHTML += `
            <div class="relative p-2 border-b border-r hover:bg-gray-50 cursor-pointer" 
                 onclick="editDayPrice(${year}, ${month}, ${day})">
                <div class="text-sm ${isToday ? 'font-bold' : ''}">${day}</div>
                <div class="text-xs text-gray-600">₱2,341</div>
            </div>
        `;
        }

        document.querySelector('.calendar-days').innerHTML = calendarHTML;
    }

    function editDayPrice(year, month, day) {
        // Show a modal or form to edit the price for this specific day
        const date = new Date(year, month, day);
        const formattedDate = date.toLocaleDateString();

        // You can implement your own modal here
        const newPrice = prompt(`Enter new price for ${formattedDate}:`);
        if (newPrice && !isNaN(newPrice)) {
            // Update the price in your database
            // Then refresh the calendar display
            console.log(`Updated price for ${formattedDate} to ₱${newPrice}`);
        }
    }

    // Initialize calendar when page loads
    document.addEventListener('DOMContentLoaded', initializeCalendar);
</script> -->