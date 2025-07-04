<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$destination_id = $_GET['id'];
$sql = "SELECT * FROM destinations WHERE destination_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$destination = $result->fetch_assoc();
$image_folder = $destination['is_international'] ? 'international destinations' : 'national destinations';

// Handle booking submission
$booking_success = '';
$booking_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $travel_date = $_POST['travel_date'];
    $number_of_travelers = isset($_POST['number_of_travelers']) ? intval($_POST['number_of_travelers']) : 0;
    $price = isset($destination['price']) ? floatval($destination['price']) : 0;
    // Debug: show raw and converted values
    $debug_values = [
        'Raw price from DB' => $destination['price'],
        'Raw travelers from POST' => $_POST['number_of_travelers'],
        'Converted price' => $price,
        'Converted travelers' => $number_of_travelers
    ];
    var_dump($debug_values);
    if (!is_numeric($price) || !is_numeric($number_of_travelers)) {
        die('Error: One of the values is not numeric.');
    }
    $total_price = $price * $number_of_travelers;

    $booking_sql = "INSERT INTO bookings (user_id, package_id, travel_date, number_of_travelers, total_price) 
                    VALUES (?, ?, ?, ?, ?)";
    $booking_stmt = $conn->prepare($booking_sql);
    $booking_stmt->bind_param("iisid", $user_id, $destination_id, $travel_date, $number_of_travelers, $total_price);

    if ($booking_stmt->execute()) {
        $booking_success = "Booking successful! We'll contact you shortly with more details.";
    } else {
        $booking_error = "Booking failed. Please try again.";
    }
    $booking_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $destination['name']; ?> - TravelEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .destination-header {
            height: 400px;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('<?php echo $image_folder . '/' . $destination['image_path']; ?>');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            align-items: center;
        }
        .booking-card {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">TravelEase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="national.php">National Tours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="international.php">International Tours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="packages.php">Packages</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Destination Header -->
    <div class="destination-header">
        <div class="container">
            <h1 class="display-4"><?php echo $destination['name']; ?></h1>
            <p class="lead"><?php echo $destination['country']; ?></p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Destination Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">About this Destination</h2>
                        <p class="card-text"><?php echo $destination['description']; ?></p>
                        
                        <h3 class="mt-4">Package Details</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-clock me-2"></i> Duration: <?php echo $destination['duration_days']; ?> Days
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-map-marker-alt me-2"></i> Location: <?php echo $destination['country']; ?>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-users me-2"></i> Maximum Travelers: <?php echo $destination['max_travelers']; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Booking Card -->
            <div class="col-lg-4">
                <div class="card booking-card">
                    <div class="card-body">
                        <h3 class="card-title">Book This Package</h3>
                        <?php if($booking_success): ?>
                            <div class="alert alert-success"><?php echo $booking_success; ?></div>
                        <?php endif; ?>
                        <?php if($booking_error): ?>
                            <div class="alert alert-danger"><?php echo $booking_error; ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="travel_date" class="form-label">Travel Date</label>
                                    <input type="date" class="form-control" id="travel_date" name="travel_date" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="number_of_travelers" class="form-label">Number of Travelers</label>
                                    <input type="number" class="form-control" id="number_of_travelers" 
                                           name="number_of_travelers" min="1" max="<?php echo $destination['max_travelers']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Price per Person</label>
                                    <h4><?php echo $destination['is_international'] ? '$' : '₹'; ?><?php echo number_format($destination['price']); ?></h4>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Book Now</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Please <a href="login.php">login</a> or <a href="register.php">register</a> to book this package.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>TravelEase</h5>
                    <p>Your trusted partner for memorable travel experiences.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Contact</a></li>
                        <li><a href="#" class="text-white">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p>Email: info@travelease.com<br>
                    Phone: +1 234 567 890</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 