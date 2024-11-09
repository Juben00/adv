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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Details - HubVenue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.3.0/build/global/luxon.min.js"></script>
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
                        <img src="./<?= htmlspecialchars($venue['image_urls'][0]) ?>" alt="Venue Image"
                            class="w-full h-[30.5rem] object-cover rounded-lg">
                    <?php else: ?>
                        <!-- Default Image Fallback if no image is available -->
                        <img src="default-image.jpg" alt="Default Venue Image"
                            class="w-full h-full object-cover rounded-lg">
                    <?php endif; ?>
                </div>

                <!-- Small Images on the Right -->
                <div class="space-y-2">
                    <?php if (!empty($venue['image_urls']) && count($venue['image_urls']) > 1): ?>
                        <img src="./<?= htmlspecialchars($venue['image_urls'][1]) ?>" alt="Venue Image"
                            class="w-full h-60 object-cover rounded-lg">
                    <?php else: ?>
                        <div class="bg-slate-50 w-full h-60 rounded-lg shadow-lg border flex items-center justify-center">
                            <p>No more image to show</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($venue['image_urls']) && count($venue['image_urls']) > 2): ?>
                        <img src="./<?= htmlspecialchars($venue['image_urls'][2]) ?>" alt="Venue Image"
                            class="w-full h-60 object-cover rounded-lg">
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
                            <p class="">Venue Capacity : <span
                                    class="text-gray-600"><?php echo htmlspecialchars($venue['capacity']) ?>
                                    guests</span>
                        </div>
                    </div>


                    <hr class="my-6">

                    <h3 class="text-xl font-semibold mb-4">Place Description</h3>
                    <p class="mb-4"><?php echo htmlspecialchars($venue['description']) ?></p>

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


                    <h3 class="text-xl font-semibold mb-4">The Owner</h3>
                    <div class="flex gap-4 mb-6">
                        <div class="bg-slate-50 shadow-lg rounded-lg p-6 w-80">
                            <!-- Card Header -->
                            <div class="text-center mb-6">
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
                                <!-- Placeholder for User Photo -->
                                <h2 class="text-xl font-semibold text-gray-800">HubVenue ID</h2>
                                <p class="text-sm text-gray-500">Virtual Identification Card</p>
                            </div>

                            <!-- Card Body -->
                            <div class="space-y-4">
                                <!-- First Name -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">First Name:</span>
                                    <span><?= htmlspecialchars($owner[0]['firstname']) ?></span>
                                </div>
                                <!-- Last Name -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">Last Name:</span>
                                    <span><?= htmlspecialchars($owner[0]['lastname']) ?></span>
                                </div>
                                <!-- Middle Name -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">Middle Initial:</span>
                                    <span><?= htmlspecialchars($owner[0]['middlename']) ?>.</span>
                                </div>
                                <!-- Sex -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">Sex:</span>
                                    <span><?= htmlspecialchars($owner[0]['sex']) ?></span>
                                </div>
                                <!-- User Type -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">User Type:</span>
                                    <span><?= htmlspecialchars($owner[0]['user_type']) ?></span>
                                </div>
                                <!-- Birthdate -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">Birthdate:</span>
                                    <span><?= htmlspecialchars($owner[0]['birthdate']) ?></span>
                                </div>
                                <!-- Contact Number -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">Contact Number:</span>
                                    <span><?= htmlspecialchars($owner[0]['contact_number']) ?></span>
                                </div>
                                <!-- Email -->
                                <div class="flex justify-between text-gray-700">
                                    <span class="font-semibold">Email:</span>
                                    <span><?= htmlspecialchars($owner[0]['email']) ?></span>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-6 text-center text-gray-500 text-xs">
                                <p>For inquiries, contact <a href="mailto:<?= htmlspecialchars($owner[0]['email']) ?>"
                                        class="text-blue-500 hover:underline"><?= htmlspecialchars($owner[0]['email']) ?></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:w-1/3">
                    <div class="border rounded-xl p-6 shadow-lg mb-6">
                        <h3 class="text-xl font-semibold mb-4">Location</h3>
                        <div class="bg-gray-100 rounded-lg h-48 w-full mb-4">
                            <?php include_once './openStreetMap/autoMapping.osm.php' ?>
                        </div>
                    </div>

                    <div class="border rounded-xl p-6 shadow-lg sticky top-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <span class="text-2xl font-semibold">₱
                                    <?php echo htmlspecialchars($venue['price']) ?></span>
                                <span class="text-lg">night</span>
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold">{{Rating}}</span>
                                <span class="text-gray-600">· {{Reviews Count}} reviews</span>
                            </div>
                        </div>
                        <div class="border rounded-lg mb-4">
                            <div class="flex">
                                <div class="w-1/2 p-2 border-r">
                                    <label class="block text-xs font-semibold">CHECK-IN</label>
                                    <input type="text" value="11/3/2024" class="w-full">
                                </div>
                                <div class="w-1/2 p-2">
                                    <label class="block text-xs font-semibold">CHECKOUT</label>
                                    <input type="text" value="11/8/2024" class="w-full">
                                </div>
                            </div>
                            <div class="border-t p-2">
                                <label class="block text-xs font-semibold">GUESTS</label>
                                <select class="w-full">
                                    <option>1 guest</option>
                                </select>
                            </div>
                        </div>
                        <button class="w-full bg-red-500 text-white rounded-lg py-3 font-semibold mb-4">Reserve</button>
                        <p class="text-center text-gray-600 mb-4">You won't be charged yet</p>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="underline">₱ <?php echo htmlspecialchars($venue['price']) ?> x {{Nights}}
                                    nights</span>
                                <span>₱{{Total Price for Nights}}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="underline">Entrance fee</span>
                                <span>₱ <?php echo htmlspecialchars($venue['entrance']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="underline">Cleaning fee</span>
                                <span>₱ <?php echo htmlspecialchars($venue['cleaning']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="underline">HubVenue service fee</span>
                                <span>₱{{Service Fee}}</span>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="flex justify-between font-semibold">
                            <span>Total </span>
                            <span>₱{{Total Price}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="./vendor/jQuery-3.7.1/jquery-3.7.1.min.js"></script>
    <script src="./js/user.jquery.js"></script>

</body>

</html>