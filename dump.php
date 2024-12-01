<?php
require_once __DIR__ . '/classes/venue.class.php';
require_once __DIR__ . '/classes/account.class.php';

$venueObj = new Venue();
$accountObj = new Account();

// Check if 'id' parameter is present and valid
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Retrieve venue information based on 'id' parameter
$venue = $venueObj->getSingleVenue($_GET['id']);

// If no venue is found, redirect to index.php
if (empty($venue['name'])) {
    header("Location: index.php");
    exit();
}

// Retrieve the owner's information
$owner = $accountObj->getUser($venue['host_id']);
$bookedDate = $venueObj->getBookedDates($_GET['id']);

// Prepare booked dates for JavaScript
$bookedDates = [];
foreach ($bookedDate as $booking) {
    $start = new DateTime($booking['startdate']);
    $end = new DateTime($booking['enddate']);
    $interval = new DateInterval('P1D');
    $dateRange = new DatePeriod($start, $interval, $end->modify('+1 day'));

    foreach ($dateRange as $date) {
        $bookedDates[] = $date->format('Y-m-d');
    }
}

// var_dump($_GET['id']);
// var_dump($bookedDate);
// var_dump($bookedDate[0]['startdate'])
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Details - HubVenue</title>
    <link rel="icon" href="./images/black_ico.png">
    <link rel="stylesheet" href="./output.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.3.0/build/global/luxon.min.js"></script>
    <link rel="stylesheet" href="node_modules/flatpickr/dist/flatpickr.min.css">
    <script src="node_modules/flatpickr/dist/flatpickr.min.js"></script>
    <style>
        .flatpickr-calendar {
            z-index: 100 !important;
        }
        
        .flatpickr-calendar.static {
            position: absolute;
            top: 100% !important;
        }

        /* Add these new styles for image transitions */
        .venue-image {
            transition: opacity 0.5s ease-in-out;
        }

        .venue-image.fade-out {
            opacity: 0;
        }

        .venue-image.fade-in {
            opacity: 1;
        }

        #thumbnailContainer::-webkit-scrollbar {
            height: 8px;
        }

        #thumbnailContainer::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        #thumbnailContainer::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        #thumbnailContainer::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .modal-fade-enter {
            opacity: 0;
            transform: scale(0.9);
        }

        .modal-fade-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 300ms, transform 300ms;
        }

        .modal-fade-exit {
            opacity: 1;
            transform: scale(1);
        }

        .modal-fade-exit-active {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 300ms, transform 300ms;
        }
    </style>
</head>

<body class="bg-slate-50">
    <!-- Header -->
    <?php
    session_start();

    if (isset($_SESSION['user'])) {
        include_once './components/navbar.logged.in.php';
    } else {
        include_once './components/navbar.html';
    }

    include_once './components/SignupForm.html';
    include_once './components/feedback.modal.html';
    include_once './components/confirm.feedback.modal.html';
    include_once './components/Menu.html';

    ?>

    <main class="max-w-7xl pt-32 mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div id="venueDetails">
            <div class="mb-6">
                <h1 class="text-3xl font-semibold mb-2"><?php echo htmlspecialchars($venue['name']) ?></h1>
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-sm font-semibold">{{Rating}} · {{Reviews Count}} reviews</span>
                        <span class="mx-2">·</span>
                        <span class="text-sm font-semibold"><?php echo htmlspecialchars($venue['tag']) ?></span>
                        <span class="mx-2">·</span>
                        <span class="text-sm font-semibold"><?php echo htmlspecialchars($venue['location']) ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 mb-8 relative">
                <!-- Main Image (First in Array) on the Left -->
                <div class="col-span-2">
                    <?php if (!empty($venue['image_urls'])): ?>
                        <img src="./<?= htmlspecialchars($venue['image_urls'][0]) ?>" 
                            alt="Venue Image"
                            class="venue-image w-full h-[30.5rem] object-cover rounded-lg"
                            data-image-index="0">
                    <?php else: ?>
                        <img src="default-image.jpg" 
                            alt="Default Venue Image"
                            class="venue-image w-full h-full object-cover rounded-lg">
                    <?php endif; ?>
                </div>

                <!-- Small Images on the Right -->
                <div class="space-y-2">
                    <?php if (!empty($venue['image_urls']) && count($venue['image_urls']) > 1): ?>
                        <img src="./<?= htmlspecialchars($venue['image_urls'][1]) ?>" 
                            alt="Venue Image"
                            class="venue-image w-full h-60 object-cover rounded-lg"
                            data-image-index="1">
                    <?php else: ?>
                        <div class="bg-slate-50 w-full h-60 rounded-lg shadow-lg border flex items-center justify-center">
                            <p>No more image to show</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($venue['image_urls']) && count($venue['image_urls']) > 2): ?>
                        <img src="./<?= htmlspecialchars($venue['image_urls'][2]) ?>" 
                            alt="Venue Image"
                            class="venue-image w-full h-60 object-cover rounded-lg"
                            data-image-index="2">
                    <?php else: ?>
                        <div class="bg-slate-50 w-full h-60 rounded-lg shadow-lg border flex items-center justify-center">
                            <p>No more image to show</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Show All Photos Button -->
                <button
                    class="absolute border-2 border-gray-500 bottom-4 right-4 bg-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-100">
                    Show all photos
                </button>
            </div>

            <div class="flex gap-12 flex-col md:flex-row">
                <div class="md:w-2/3">
                    <div class="flex justify-between items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($venue['tag']) ?> at
                                <?php echo htmlspecialchars($venue['location']) ?>
                            </h2>
                        </div>
                    </div>


                    <hr class="my-6">

                    <h3 class="text-xl font-semibold mb-4">Place Description</h3>
                    <p class="mb-4"><?php echo htmlspecialchars($venue['description']) ?></p>

                    <hr class="my-6">

                    <h3 class="text-xl font-semibold mb-4">Venue Capacity</h3>
                    <p class="">
                        <?php echo htmlspecialchars($venue['capacity']) ?>
                        guests
                    </p>

                    <hr class="my-6">

                    <h3 class="text-xl font-semibold mb-4">What this place offers</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <?php if (!empty($venue['amenities'])): ?>
                            <?php
                            $amenities = json_decode($venue['amenities'], true);
                            if ($amenities):
                                ?>
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($amenities as $amenity): ?>
                                        <li class="text-sm text-gray-800 leading-tight">
                                            <?= htmlspecialchars($amenity) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">No amenities available</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-500">No amenities available</p>
                        <?php endif; ?>
                    </div>

                    <hr class="my-6">

                    <h3 class="text-xl font-semibold mb-4">Location</h3>
                    <div class="bg-gray-100 rounded-lg h-96 w-full mb-4" id="map">
                        <?php include_once './openStreetMap/autoMapping.osm.php' ?>
                    </div>

                    <hr class="my-6">

                    <h3 class="text-xl font-semibold mb-4">Ratings & Reviews</h3>
                    <div class="mb-8">
                        <div class="flex items-start gap-8">
                            <!-- Overall Rating -->
                            <div class="text-center">
                                <div class="text-5xl font-bold mb-1">4.8</div>
                                <div class="flex items-center justify-center text-yellow-400 mb-1">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="text-sm text-gray-600">128 reviews</div>
                            </div>

                            <!-- Rating Bars -->
                            <div class="flex-grow">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm w-16">5 stars</span>
                                        <div class="flex-grow h-2 bg-gray-200 rounded">
                                            <div class="h-full bg-yellow-400 rounded" style="width: 75%"></div>
                                        </div>
                                        <span class="text-sm w-8">30</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm w-16">4 stars</span>
                                        <div class="flex-grow h-2 bg-gray-200 rounded">
                                            <div class="h-full bg-yellow-400 rounded" style="width: 60%"></div>
                                        </div>
                                        <span class="text-sm w-8">20</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm w-16">3 stars</span>
                                        <div class="flex-grow h-2 bg-gray-200 rounded">
                                            <div class="h-full bg-yellow-400 rounded" style="width: 45%"></div>
                                        </div>
                                        <span class="text-sm w-8">92</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm w-16">2 stars</span>
                                        <div class="flex-grow h-2 bg-gray-200 rounded">
                                            <div class="h-full bg-yellow-400 rounded" style="width: 15%"></div>
                                        </div>
                                        <span class="text-sm w-8">3</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm w-16">1 star</span>
                                        <div class="flex-grow h-2 bg-gray-200 rounded">
                                            <div class="h-full bg-yellow-400 rounded" style="width: 30%"></div>
                                        </div>
                                        <span class="text-sm w-8">72</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <div class="mt-8 space-y-6">
                            <div class="border-b pb-6">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                                    <div>
                                        <h4 class="font-semibold">Sarah Johnson</h4>
                                        <p class="text-sm text-gray-500">2 weeks ago</p>
                                    </div>
                                </div>
                                <div class="flex text-yellow-400 mb-2">
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-gray-700">Amazing venue! Perfect for our wedding reception. The staff was
                                    very accommodating and professional. The place was exactly as described and the
                                    amenities were all in great condition.</p>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center justify-center gap-2 mt-6">
                            <button class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded">Previous</button>
                            <button class="px-4 py-2 text-sm bg-gray-900 text-white rounded">1</button>
                            <button class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded">2</button>
                            <button class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded">3</button>
                            <button class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded">Next</button>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- New Section: Things You Should Know -->
                    <h3 class="text-xl font-semibold mb-4">Things You Should Know</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- House Rules -->
                        <div>
                            <h4 class="font-semibold text-lg mb-3">House Rules</h4>
                            <ul class="space-y-2">
                                <?php
                                if (isset($venue['time_inout'])) {
                                    $timeInOut = json_decode($venue['time_inout'], true); // Decode into array
                                
                                    // Convert to 12-hour format with AM/PM
                                    $checkIn = DateTime::createFromFormat('H:i', $timeInOut['check_in'])->format('h:i A');
                                    $checkOut = DateTime::createFromFormat('H:i', $timeInOut['check_out'])->format('h:i A');
                                    ?>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-clock text-gray-600"></i>
                                        <span>Check-in: After <?php echo htmlspecialchars($checkIn); ?></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-clock text-gray-600"></i>
                                        <span>Checkout: Before <?php echo htmlspecialchars($checkOut); ?></span>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-users text-gray-600"></i>
                                    <span>Maximum <?php echo htmlspecialchars($venue['capacity']) ?> guests</span>
                                </li>
                                <?php
                                if (!empty($venue['rules'])) {
                                    $rules = json_decode($venue['rules'], true); // Decode the JSON string into an array
                                    if ($rules):
                                        ?>
                                        <?php foreach ($rules as $rule): ?>
                                            <li class="list-disc list-inside flex items-center gap-2">
                                                <?= htmlspecialchars($rule) ?>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php
                                    endif;
                                }
                                ?>
                            </ul>
                        </div>

                        <!-- Cancellation Policy -->
                        <div>
                            <h4 class="font-semibold text-lg mb-3">Cancellation Policy</h4>
                            <div class="space-y-3">
                                <p class="text-gray-700">Free cancellation for 48 hours after booking.</p>
                                <p class="text-gray-700">Cancel before check-in and get a full refund, minus the service
                                    fee.</p>
                                <div class="mt-4">
                                    <h5 class="font-medium mb-2">Refund Policy:</h5>
                                    <ul class="space-y-2 text-gray-700">
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-check text-green-600"></i>
                                            <span>100% refund: Cancel 7 days before check-in</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-check text-green-600"></i>
                                            <span>50% refund: Cancel 3-7 days before check-in</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-times text-red-600"></i>
                                            <span>No refund: Cancel less than 3 days before check-in</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">


                </div>

                <div class="md:w-1/3">
                    <div class="border rounded-xl p-6 shadow-lg mb-6">
                        <h3 class="text-xl font-semibold mb-4">The Owner</h3>
                        <div class="flex gap-4">
                            <a href="owner-page.php"
                                class="bg-slate-50 p-6 w-full hover:bg-slate-100 transition duration-300 cursor-pointer">
                                <!-- Card Header -->
                                <div class="text-center mb-4">
                                    <div
                                        class="size-24 text-2xl rounded-full bg-black text-white flex items-center justify-center mx-auto mb-4">
                                        <?php
                                        if (isset($owner)) {
                                            echo $owner[0]['firstname'][0];
                                        } else {
                                            echo "U";
                                        }
                                        ?>
                                    </div>
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($owner[0]['firstname'] . " " . $owner[0]['lastname']); ?>
                                    </h2>
                                    <p class="text-xs text-gray-500">Owner</p>
                                </div>
                            </a>
                        </div>
                    </div>




                    <div class="sticky top-32">
                        <form id="reservationForm" class="border rounded-xl p-6 shadow-lg bg-white" method="GET" action="payment.php">
                            <!-- Price Header -->
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-baseline">
                                    <span class="text-3xl font-bold">₱<?php echo htmlspecialchars($venue['price']); ?></span>
                                    <span class="text-gray-600 ml-2">/ night</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="font-semibold">{{Rating}}</span>
                                    <span class="text-gray-500">· {{Reviews Count}} reviews</span>
                                </div>
                            </div>

                            <!-- Date and Guest Selection -->
                            <div class="border rounded-xl mb-6 shadow-sm bg-gray-50 relative">
                                <div class="flex border-b">
                                    <input type="hidden" name="venueId" value="<?php echo htmlspecialchars($venue['id']); ?>">
                                    <div class="w-1/2 p-3 border-r">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">CHECK-IN</label>
                                        <input type="date" name="checkin" placeholder="Set Date" 
                                            class="w-full bg-transparent focus:outline-none text-gray-800">
                                    </div>
                                    <div class="w-1/2 p-3">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">CHECKOUT</label>
                                        <input type="date" name="checkout" placeholder="Set Date" 
                                            class="w-full bg-transparent focus:outline-none text-gray-800">
                                    </div>
                                </div>
                                <div class="p-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        GUESTS (Maximum <span class="text-red-500 font-bold"><?php echo htmlspecialchars($venue['capacity']); ?></span>)
                                    </label>
                                    <input type="number" name="numberOfGuest" 
                                        class="w-full bg-transparent focus:outline-none text-gray-800" 
                                        placeholder="Enter number of guests">
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 hover:text-gray-900 cursor-help">
                                        ₱<?php echo htmlspecialchars($venue['price']); ?> × <span total-nights>0</span> nights
                                    </span>
                                    <span class="font-medium">₱ <input type="number" class="text-right bg-transparent w-24" 
                                        name="totalPriceForNights" value="0" readonly></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 hover:text-gray-900 cursor-help">
                                        Entrance fee × <span total-entrance-guests>0</span> guest
                                    </span>
                                    <span class="font-medium">₱ <input type="number" class="text-right bg-transparent w-24" 
                                        name="totalEntranceFee" value="<?php echo htmlspecialchars($venue['entrance']); ?>" readonly></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 hover:text-gray-900 cursor-help">Cleaning fee</span>
                                    <span class="font-medium">₱ <input type="number" class="text-right bg-transparent w-24" 
                                        name="cleaningFee" value="<?php echo htmlspecialchars($venue['cleaning']); ?>" readonly></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 hover:text-gray-900 cursor-help">HubVenue service fee</span>
                                    <span class="font-medium">₱ <input type="number" class="text-right bg-transparent w-24" 
                                        name="serviceFee" value="0" readonly></span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="border-t pt-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="font-bold text-lg">₱ <input type="number" 
                                        class="text-right bg-transparent w-24 font-bold" 
                                        name="totalPrice" value="0" readonly></span>
                                </div>
                            </div>

                            <p class="text-center text-gray-600 my-4">You won't be charged yet</p>
                            <button type="submit"
                                class="w-full bg-red-500 text-white rounded-lg py-3 font-semibold mb-4">Reserve</button>
                        </form>
                    </div>





                </div>
            </div>
        </div>

    </main>

    <script src="./vendor/jQuery-3.7.1/jquery-3.7.1.min.js"></script>
    <script src="./js/user.jquery.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Debug logging to verify elements are found
            console.log('Checkin:', document.querySelector('input[name="checkin"]'));
            console.log('Checkout:', document.querySelector('input[name="checkout"]'));
            console.log('Guests:', document.querySelector('input[name="numberOfGuest"]'));
            console.log('Total Price:', document.querySelector('input[name="totalPrice"]'));
            console.log('Price for Nights:', document.querySelector('input[name="totalPriceForNights"]'));
            console.log('Service Fee:', document.querySelector('input[name="serviceFee"]'));
            console.log('Entrance Fee:', document.querySelector('input[name="totalEntranceFee"]'));
            console.log('Cleaning Fee:', document.querySelector('input[name="cleaningFee"]'));

            const checkinInput = document.querySelector('input[name="checkin"]');
            const checkoutInput = document.querySelector('input[name="checkout"]');
            const guestsInput = document.querySelector('input[name="numberOfGuest"]');
            const totalPriceInput = document.querySelector('input[name="totalPrice"]');
            const totalPriceForNightsInput = document.querySelector('input[name="totalPriceForNights"]');
            const serviceFeeInput = document.querySelector('input[name="serviceFee"]');
            const entranceFeeInput = document.querySelector('input[name="totalEntranceFee"]');
            const cleaningFeeInput = document.querySelector('input[name="cleaningFee"]');
            const pricePerNight = <?php echo htmlspecialchars($venue['price']) ?>;
            const entranceFee = <?php echo htmlspecialchars($venue['entrance']) ?>;
            const cleaningFee = <?php echo htmlspecialchars($venue['cleaning']) ?>;
            const serviceFeeRate = 0.15;
            const maxGuests = <?php echo htmlspecialchars($venue['capacity']) ?>;

            const bookedDates = <?php echo json_encode($bookedDates); ?>;

            function disableBookedDates(date) {
                const dateString = date.toISOString().split('T')[0];
                return bookedDates.includes(dateString);
            }

            function disableDates(input, minDate = "today") {
                flatpickr(input, {
                    minDate: minDate,
                    disable: bookedDates,
                    appendTo: input.parentElement,
                    static: true,
                    onChange: function (selectedDates, dateStr, instance) {
                        validateDateRange();
                        calculateTotal();
                    }
                });
            }

            function validateDateRange() {
                const checkinDate = new Date(checkinInput.value);
                const checkoutDate = new Date(checkoutInput.value);
                const dateRange = new Date(checkinDate);

                while (dateRange <= checkoutDate) {
                    if (disableBookedDates(dateRange)) {
                        showModal('Selected date range includes unavailable dates.', undefined, 'black_ico.png');
                        checkinInput.value = '';
                        checkoutInput.value = '';
                        break;
                    }
                    dateRange.setDate(dateRange.getDate() + 1);
                }
            }

            disableDates(checkinInput);
            disableDates(checkoutInput);

            // Get today's date in YYYY-MM-DD format
            const today = new Date();
            const todayFormatted = today.toISOString().split('T')[0];

            // Set 'min' attributes to today for both checkin and checkout inputs
            checkinInput.setAttribute('min', todayFormatted);
            checkoutInput.setAttribute('min', todayFormatted);

            // Validate and correct selected date inputs
            function validateDate(input) {
                const selectedDate = new Date(input.value);
                if (selectedDate < today) {
                    input.value = todayFormatted; // Reset to today's date if past date is selected
                }
            }

            function calculateTotal() {
                console.log('Calculating total...'); // Debug log
                validateDate(checkinInput);
                validateDate(checkoutInput);

                const checkinDate = new Date(checkinInput.value);
                const checkoutDate = new Date(checkoutInput.value);
                const timeDiff = checkoutDate - checkinDate;
                const days = timeDiff / (1000 * 3600 * 24);

                let guests = parseInt(guestsInput.value);
                console.log('Days:', days, 'Guests:', guests); // Debug log

                if (isNaN(guests) || guests < 1) {
                    guests = 1;
                } else if (guests > maxGuests) {
                    guests = maxGuests;
                    guestsInput.value = maxGuests;
                }

                if (days > 0) {
                    const totalPriceForNights = pricePerNight * days;
                    const totalEntranceFee = entranceFee * guests;
                    const serviceFee = pricePerNight * serviceFeeRate;
                    const grandTotal = totalPriceForNights + totalEntranceFee + cleaningFee + serviceFee;

                    console.log('Calculations:', { // Debug log
                        totalPriceForNights,
                        totalEntranceFee,
                        serviceFee,
                        grandTotal
                    });

                    document.querySelector('span[total-nights]').textContent = days;
                    document.querySelector('span[total-entrance-guests]').textContent = guests;
                    totalPriceForNightsInput.value = totalPriceForNights.toFixed(2);
                    entranceFeeInput.value = totalEntranceFee.toFixed(2);
                    serviceFeeInput.value = serviceFee.toFixed(2);
                    totalPriceInput.value = grandTotal.toFixed(2);
                }
            }

            // Add event listeners
            checkinInput.addEventListener('change', calculateTotal);
            checkoutInput.addEventListener('change', calculateTotal);
            guestsInput.addEventListener('input', calculateTotal);

            // Initial calculation
            calculateTotal();
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = <?php echo json_encode($venue['image_urls'] ?? []); ?>;
        if (!images.length) return;

        const mainImage = document.querySelector('.col-span-2 .venue-image');
        const smallImages = document.querySelectorAll('.space-y-2 .venue-image');
        let currentMainIndex = 0;
        let currentSmallIndices = [1, 2];

        // Function to update main image with transition
        function updateMainImage(newIndex) {
            mainImage.classList.add('fade-out');
            
            setTimeout(() => {
                mainImage.src = './' + images[newIndex];
                mainImage.dataset.imageIndex = newIndex;
                mainImage.classList.remove('fade-out');
                mainImage.classList.add('fade-in');
                
                setTimeout(() => {
                    mainImage.classList.remove('fade-in');
                }, 500);
            }, 500);

            currentMainIndex = newIndex;
        }

        // Function to update small images without transition
        function updateSmallImages() {
            smallImages.forEach((img, i) => {
                const nextIndex = (currentMainIndex + i + 1) % images.length;
                img.src = './' + images[nextIndex];
                img.dataset.imageIndex = nextIndex;
                currentSmallIndices[i] = nextIndex;
            });
        }

        // Add click handlers to small images
        smallImages.forEach(img => {
            img.addEventListener('click', function() {
                const clickedIndex = parseInt(this.dataset.imageIndex);
                updateMainImage(clickedIndex);
                
                // After updating main image, update small images
                setTimeout(() => {
                    updateSmallImages();
                }, 500);
            });
        });

        // Automatic rotation for main image only
        setInterval(() => {
            const nextIndex = (currentMainIndex + 1) % images.length;
            updateMainImage(nextIndex);
            updateSmallImages();
        }, 5000);

        // Pause shuffling when user hovers over images
        const imageContainer = document.querySelector('.grid');
        let rotationInterval;

        function startRotation() {
            rotationInterval = setInterval(() => {
                const nextIndex = (currentMainIndex + 1) % images.length;
                updateMainImage(nextIndex);
                updateSmallImages();
            }, 5000);
        }

        imageContainer.addEventListener('mouseenter', () => {
            clearInterval(rotationInterval);
        });

        // Resume shuffling when user moves mouse away
        imageContainer.addEventListener('mouseleave', () => {
            startRotation();
        });

        // Start initial rotation
        startRotation();
    });
    </script>

    <!-- Photo Gallery Modal -->
    <div id="photoGalleryModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-80 transition-opacity duration-300 opacity-0" id="modalBackdrop"></div>
        
        <!-- Modal Content -->
        <div class="relative h-full w-full flex flex-col">
            <!-- Header -->
            <div class="absolute top-0 left-0 right-0 p-4 z-10">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <button id="closeGallery" class="text-white hover:bg-white/10 p-2 rounded-full transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <span class="text-white text-sm">
                        <span id="currentImageIndex">1</span> / <span id="totalImages">5</span>
                    </span>
                </div>
            </div>

            <!-- Main Gallery Area -->
            <div class="flex-1 flex items-center justify-center p-4">
                <div class="relative w-full max-w-7xl mx-auto">
                    <!-- Navigation Buttons -->
                    <button id="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:bg-white/10 p-4 rounded-full transition">
                        <i class="fas fa-chevron-left text-2xl"></i>
                    </button>
                    
                    <button id="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:bg-white/10 p-4 rounded-full transition">
                        <i class="fas fa-chevron-right text-2xl"></i>
                    </button>

                    <!-- Main Image -->
                    <div class="flex justify-center">
                        <img id="mainGalleryImage" src="" alt="Venue Image" class="max-h-[80vh] object-contain">
                    </div>
                </div>
            </div>

            <!-- Thumbnails -->
            <div class="w-full p-4">
                <div class="max-w-7xl mx-auto">
                    <div id="thumbnailContainer" class="flex gap-2 overflow-x-auto pb-2">
                        <!-- Thumbnails will be inserted here by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('photoGalleryModal');
        const modalBackdrop = document.getElementById('modalBackdrop');
        const mainGalleryImage = document.getElementById('mainGalleryImage');
        const thumbnailContainer = document.getElementById('thumbnailContainer');
        const currentImageIndex = document.getElementById('currentImageIndex');
        const totalImages = document.getElementById('totalImages');
        const prevButton = document.getElementById('prevImage');
        const nextButton = document.getElementById('nextImage');
        const closeButton = document.getElementById('closeGallery');
        
        const images = <?php echo json_encode($venue['image_urls'] ?? []); ?>;
        let currentIndex = 0;

        // Show All Photos button click handler
        const showAllPhotosButton = document.querySelector('button[class*="border-2 border-gray-500"]');
        if (showAllPhotosButton) {
            showAllPhotosButton.addEventListener('click', function() {
                openGallery(0);
            });
        }

        function openGallery(index) {
            currentIndex = index;
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalBackdrop.classList.remove('opacity-0');
            }, 10);
            updateGallery();
            createThumbnails();
            document.body.style.overflow = 'hidden';
        }

        function closeGallery() {
            modalBackdrop.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        function updateGallery() {
            mainGalleryImage.src = './' + images[currentIndex];
            currentImageIndex.textContent = currentIndex + 1;
            totalImages.textContent = images.length;
            
            // Update thumbnails active state
            document.querySelectorAll('.thumbnail').forEach((thumb, index) => {
                if (index === currentIndex) {
                    thumb.classList.add('ring-2', 'ring-white');
                    thumb.classList.remove('opacity-70');
                } else {
                    thumb.classList.remove('ring-2', 'ring-white');
                    thumb.classList.add('opacity-70');
                }
            });
        }

        function createThumbnails() {
            thumbnailContainer.innerHTML = '';
            images.forEach((image, index) => {
                const thumb = document.createElement('img');
                thumb.src = './' + image;
                thumb.classList.add('thumbnail', 'h-20', 'w-32', 'object-cover', 'cursor-pointer', 
                    'transition-opacity', 'duration-200', 'opacity-70', 'hover:opacity-100');
                if (index === currentIndex) {
                    thumb.classList.add('ring-2', 'ring-white');
                    thumb.classList.remove('opacity-70');
                }
                thumb.addEventListener('click', () => {
                    currentIndex = index;
                    updateGallery();
                });
                thumbnailContainer.appendChild(thumb);
            });
        }

        // Event Listeners
        closeButton.addEventListener('click', closeGallery);
        modalBackdrop.addEventListener('click', closeGallery);

        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateGallery();
        });

        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % images.length;
            updateGallery();
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('hidden')) {
                if (e.key === 'Escape') closeGallery();
                if (e.key === 'ArrowLeft') prevButton.click();
                if (e.key === 'ArrowRight') nextButton.click();
            }
        });
    });
    </script>

</body>

</html>