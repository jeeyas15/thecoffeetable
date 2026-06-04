<?php
session_start();
require 'config/db.php';
// Fetch avatar for navbar
$navAvatar = null;
if (isset($_SESSION['user_id'])) {
    $stmtNav = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmtNav->execute([$_SESSION['user_id']]);
    $navUser = $stmtNav->fetch();
    if (!empty($navUser['avatar']) && file_exists('uploads/avatars/' . $navUser['avatar'])) {
        $navAvatar = 'uploads/avatars/' . htmlspecialchars($navUser['avatar']);
    }
}


// Fetch centers content
$stmt = $pdo->query("SELECT field, value FROM site_content WHERE section = 'centers_page'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$content = [];
foreach ($rows as $row) {
    $content[$row['field']] = $row['value'];
}

function t($content, $field, $default = '') {
    return htmlspecialchars($content[$field] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Community Centers | The Coffee Table</title>
  <meta name="description" content="Explore The Coffee Table's community centers across Wyndham Vale, Tarneit, Point Cook, Werribee, and Hoppers Crossing.">
  <link rel="icon" type="image/png" href="favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="theme.php">
  <style>
    .center-section {
      border: 2px solid #d2b48c;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 30px;
      background-color: transparent;
    }
    .center-section .row {
      display: flex;
      align-items: stretch;
    }
    .map-container {
      border: 2px solid #d2b48c;
      border-radius: 8px;
      overflow: hidden;
      flex-grow: 1;
      min-height: 250px;
    }
    .map-container iframe {
      width: 100%;
      height: 100%;
      min-height: 250px;
      border: 0;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <i class="fas fa-mug-hot me-2"></i>The Coffee Table
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
          <li class="nav-item"><a class="nav-link active" href="centers.php">Centers</a></li>
          <li class="nav-item"><a class="nav-link" href="stories.php">Stories</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-2">
                            <a class="nav-link text-warning d-flex align-items-center gap-1" href="profile.php">
                                <?php if ($navAvatar): ?>
                                    <img src="<?= $navAvatar ?>" alt="avatar" style="width:26px;height:26px;border-radius:50%;object-fit:cover;border:2px solid #F4A460;">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($_SESSION['name']) ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm mt-1 ms-2" href="auth/logout.php">Logout</a>
                        </li>
          <?php else: ?>
            <li class="nav-item ms-2">
              <button class="btn btn-outline-light btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
            </li>
            <li class="nav-item ms-2">
              <button class="btn btn-light btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Centers Content -->
  <main class="container py-5">
    <h1 class="section-title">Our Centers</h1>

    <!-- Wyndham Vale -->
    <section class="row mb-5 center-section" id="wyndham-vale">
      <div class="col-md-8">
        <h2><?= t($content, 'wyndham_title', 'Wyndham Park Community Center') ?></h2>
        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'wyndham_address') ?></p>
        <p><?= t($content, 'wyndham_desc') ?></p>
        <h5>Services Available:</h5>
        <ul>
          <li>Full Food Bank Operations</li>
          <li>Community Kitchen Classes</li>
          <li>Coffee Table Program</li>
          <li>Sewing Classes</li>
          <li>Case Management Services</li>
        </ul>
        <h5>Hours:</h5>
        <p><?= t($content, 'wyndham_hours') ?></p>
        <h5>Contact:</h5>
        <p>Phone: <?= t($content, 'wyndham_phone') ?><br>Email: <?= t($content, 'wyndham_email') ?></p>
      </div>
      <div class="col-md-4">
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5295.727075023812!2d144.67379977252935!3d-37.886999696674906!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad6858b35b16787%3A0xad8adf289cefea73!2sWyndham%20Park%20Community%20Centre!5e0!3m2!1sen!2sau!4v1762221343467!5m2!1sen!2sau" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>

    <!-- Tarneit -->
    <section class="row mb-5 center-section" id="tarneit">
      <div class="col-md-8">
        <h2><?= t($content, 'tarneit_title', 'Tarneit Community Learning Centre') ?></h2>
        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'tarneit_address') ?></p>
        <p><?= t($content, 'tarneit_desc') ?></p>
        <h5>Services Available:</h5>
        <ul>
          <li>Family Food Assistance</li>
          <li>Parenting Support Groups</li>
          <li>Youth Programs</li>
          <li>Childcare During Classes</li>
        </ul>
        <h5>Hours:</h5>
        <p><?= t($content, 'tarneit_hours') ?></p>
        <h5>Contact:</h5>
        <p>Phone: <?= t($content, 'tarneit_phone') ?><br>Email: <?= t($content, 'tarneit_email') ?></p>
      </div>
      <div class="col-md-4">
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d50416.86219921009!2d144.66088627910156!3d-37.835624299999985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad689c336856d53%3A0xdc7f4e453db9f1fe!2sTarneit%20Community%20Learning%20Centre!5e0!3m2!1sen!2sau!4v1762221440616!5m2!1sen!2sau" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>

    <!-- Point Cook -->
    <section class="row mb-5 center-section" id="point-cook">
      <div class="col-md-8">
        <h2><?= t($content, 'pointcook_title', 'Point Cook Community Learning Centre') ?></h2>
        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'pointcook_address') ?></p>
        <p><?= t($content, 'pointcook_desc') ?></p>
        <h5>Services Available:</h5>
        <ul>
          <li>Senior Nutrition Program</li>
          <li>Intergenerational Activities</li>
          <li>Transportation Assistance</li>
          <li>Health & Wellness Workshops</li>
        </ul>
        <h5>Hours:</h5>
        <p><?= t($content, 'pointcook_hours') ?></p>
        <h5>Contact:</h5>
        <p>Phone: <?= t($content, 'pointcook_phone') ?><br>Email: <?= t($content, 'pointcook_email') ?></p>
      </div>
      <div class="col-md-4">
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d25192.587274534202!2d144.71830683955073!3d-37.8819659!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad68957a8445417%3A0x9007565584f8890e!2sPoint%20Cook%20Community%20Learning%20Centre!5e0!3m2!1sen!2sau!4v1762221508305!5m2!1sen!2sau" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>

    <!-- Werribee -->
    <section class="row mb-5 center-section" id="werribee">
      <div class="col-md-8">
        <h2><?= t($content, 'werribee_title', 'Gateways Support Services') ?></h2>
        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'werribee_address') ?></p>
        <p><?= t($content, 'werribee_desc') ?></p>
        <h5>Services Available:</h5>
        <ul>
          <li>24/7 Crisis Support Hotline</li>
          <li>Emergency Accommodation Assistance</li>
          <li>Crisis Food Packages</li>
          <li>Referral Services</li>
        </ul>
        <h5>Hours:</h5>
        <p><?= t($content, 'werribee_hours') ?></p>
        <h5>Contact:</h5>
        <p>Phone: <?= t($content, 'werribee_phone') ?><br>Email: <?= t($content, 'werribee_email') ?></p>
      </div>
      <div class="col-md-4">
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d25186.476682064236!2d144.62833287431638!3d-37.89982589999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad6850d7748a217%3A0xad4599cb75fdab9a!2sGateways%20Support%20Services!5e0!3m2!1sen!2sau!4v1762221699341!5m2!1sen!2sau" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>

    <!-- Hoppers Crossing -->
    <section class="row mb-5 center-section" id="hoppers">
      <div class="col-md-8">
        <h2><?= t($content, 'hoppers_title', 'The Grange Community Centre') ?></h2>
        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'hoppers_address') ?></p>
        <p><?= t($content, 'hoppers_desc') ?></p>
        <h5>Services Available:</h5>
        <ul>
          <li>Mobile Food Bank</li>
          <li>Outreach Programs</li>
          <li>Community Partnership Events</li>
          <li>Mobile Sewing Classes</li>
        </ul>
        <h5>Hours:</h5>
        <p><?= t($content, 'hoppers_hours') ?></p>
        <h5>Contact:</h5>
        <p>Phone: <?= t($content, 'hoppers_phone') ?><br>Email: <?= t($content, 'hoppers_email') ?></p>
      </div>
      <div class="col-md-4">
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d25199.876477730555!2d144.63824567431638!3d-37.86065169999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad68f3e3e98849b%3A0xcf149b764850bb0c!2sThe%20Grange%20Community%20Centre!5e0!3m2!1sen!2sau!4v1762221741881!5m2!1sen!2sau" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer mt-5 py-5">
    <div class="container">
      <div class="row text-center text-md-start">
        <div class="col-md-4 mb-4 mb-md-0">
          <h5 class="fw-bold text-uppercase">The Coffee Table</h5>
          <p>Building community through connection, support, and shared experiences since 2015.</p>
          <div class="d-flex justify-content-center justify-content-md-start">
            <a href="https://www.facebook.com/profile.php?id=61583187663282" class="social-icon" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://www.instagram.com/thecoffeetable12/" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://x.com/Thecoffeet49330" class="social-icon" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
          </div>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
          <h5 class="fw-bold text-uppercase">Contact Info</h5>
          <p><i class="fas fa-map-marker-alt me-2"></i>Wyndham Vale, Victoria</p>
          <p><i class="fas fa-phone me-2"></i>(03) 9876 5432</p>
          <p><i class="fas fa-envelope me-2"></i>info@thecoffeetable.org.au</p>
        </div>
        <div class="col-md-4">
          <h5 class="fw-bold text-uppercase">Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
            <li><a href="about.php" class="text-white text-decoration-none">About Us</a></li>
            <li><a href="services.php" class="text-white text-decoration-none">Our Services</a></li>
            <li><a href="centers.php" class="text-white text-decoration-none">Our Centers</a></li>
            <li><a href="stories.php" class="text-white text-decoration-none">Community Stories</a></li>
            <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
          </ul>
        </div>
      </div>
      <hr class="my-4 border-light">
      <div class="text-center small">
        <p class="mb-0">&copy; 2025 The Coffee Table. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#8B4513; color:white;">
          <h5 class="modal-title"><i class="fas fa-sign-in-alt me-2"></i>Login</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="auth/login.php" method="POST">
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
          <p class="text-center mt-3 mb-0">Don't have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Register here</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#8B4513; color:white;">
          <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Create an Account</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="auth/register.php" method="POST">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required
                pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                oninvalid="this.setCustomValidity('Please enter a valid email e.g. name@example.com')"
                oninput="this.setCustomValidity('')">
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" minlength="6" required>
              <div class="form-text">Minimum 6 characters.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Account</button>
          </form>
          <p class="text-center mt-3 mb-0">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login here</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
