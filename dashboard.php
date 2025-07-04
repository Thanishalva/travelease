<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Fetch user's bookings
$bookings_sql = "SELECT b.*, d.name as destination_name, d.country, d.image_path, d.is_international 
                 FROM bookings b 
                 JOIN destinations d ON b.package_id = d.destination_id 
                 WHERE b.user_id = ? 
                 ORDER BY b.booking_date DESC";
$bookings_stmt = $conn->prepare($bookings_sql);
$bookings_stmt->bind_param("i", $user_id);
$bookings_stmt->execute();
$bookings = $bookings_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - TravelEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .booking-card {
            transition: transform 0.3s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .profile-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
                    <p class="lead mb-0">Manage your bookings and profile</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="#profile" class="btn btn-light">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- My Bookings Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">My Bookings</h2>
                <?php if ($bookings->num_rows > 0): ?>
                    <div class="row g-4">
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card booking-card h-100">
                                    <div class="position-relative">
                                        <img src="<?php echo $booking['is_international'] ? 'international destinations/' : 'national destinations/'; ?><?php echo $booking['image_path']; ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo $booking['destination_name']; ?>"
                                             style="height: 200px; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $booking['destination_name']; ?></h5>
                                        <p class="card-text">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo $booking['country']; ?><br>
                                            <i class="fas fa-calendar"></i> Travel Date: <?php echo date('M d, Y', strtotime($booking['travel_date'])); ?><br>
                                            <i class="fas fa-users"></i> Travelers: <?php echo $booking['number_of_travelers']; ?><br>
                                            <i class="fas fa-rupee-sign"></i> Total: <?php echo number_format($booking['total_price']); ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <small class="text-muted">Booked on: <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't made any bookings yet. <a href="national.php">Start exploring our destinations!</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile Information Section -->
        <div class="row" id="profile">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Profile Information</h3>
                        <form method="POST" action="update_profile.php">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" rows="3" readonly><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
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