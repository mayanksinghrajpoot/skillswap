<?php
$error_code = $_SERVER['REDIRECT_STATUS'];
$error_messages = [
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Page Not Found',
    500 => 'Internal Server Error'
];
$error_message = $error_messages[$error_code] ?? 'Unknown Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $error_code; ?> - SkillSwap</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto text-center p-8">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-indigo-600"><?php echo $error_code; ?></h1>
            <h2 class="text-2xl font-semibold text-gray-900 mt-4"><?php echo $error_message; ?></h2>
            <p class="text-gray-600 mt-2">
                <?php
                switch($error_code) {
                    case 404:
                        echo "The page you're looking for doesn't exist or has been moved.";
                        break;
                    case 500:
                        echo "Something went wrong on our end. Please try again later.";
                        break;
                    default:
                        echo "An error occurred while processing your request.";
                }
                ?>
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="/" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-300">
                <i class="fas fa-home mr-2"></i>Back to Home
            </a>
            
            <div class="text-gray-600">
                <p>Need help? <a href="/contact" class="text-indigo-600 hover:text-indigo-500">Contact Support</a></p>
            </div>
        </div>
    </div>

    <?php
    if ($error_code == 500) {
        error_log("500 error occurred: " . $_SERVER['REQUEST_URI']);
    }
    ?>
</body>
</html>