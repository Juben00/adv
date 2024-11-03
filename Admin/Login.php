<php
require 'db_connect.php';
?


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERM Motors Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="h-screen">
    <div class="flex h-full">
        <div class="w-full md:w-1/2 flex flex-col bg-white">
            <div class="flex-grow flex items-center justify-center px-8 pb-8">
                <div class="w-full max-w-md">
                    <div class="text-center mb-8">
                        <img src="logo only.png" alt="ERM Motors Logo" class="w-60 h-auto mx-auto">
                    </div>
                    <form id="loginForm" action="Login.php" method="post" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="Enter your email"
                                required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400
                                       focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500"
                            >
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
                                required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400
                                       focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500"
                            >
                        </div>
                        <div>
                            <button
                                type="submit"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 rounded-md"
                            >
                                LOGIN
                            </button>
                        </div>
                    </form>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-pink-500 hover:text-pink-600">
                                Forgot your password?
                            </a>
                        </div>
                    </div>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="mt-4 text-red-500 text-sm text-center">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="hidden md:flex md:w-1/2 bg-cover bg-center" style="background-image: url('https://i.pinimg.com/564x/42/2a/fc/422afc576106b5bd024a2e1d419baa95.jpg')">
            <div class="w-full h-full flex items-center justify-center bg-black bg-opacity-50">
                <div class="text-center text-white p-8">
                    <h2 class="text-4xl font-bold mb-4">Welcome to HubVenue Admin Page.</h2>
                    <p class="text-xl">Sign in to continue to your account.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Remove the existing JavaScript for form submission
    </script>
</body>
</html>