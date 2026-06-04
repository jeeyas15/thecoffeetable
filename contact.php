<?php
session_start();
require 'config/db.php';

// Fetch contact content
$stmt = $pdo->query("SELECT field, value FROM site_content WHERE section = 'contact_page'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$content = [];
foreach ($rows as $row) { $content[$row['field']] = $row['value']; }

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

function t($content, $field, $default = '') { return htmlspecialchars($content[$field] ?? $default); }

// Flash messages
$success = $_SESSION['contact_success'] ?? null;
$error   = $_SESSION['contact_error']   ?? null;
unset($_SESSION['contact_success'], $_SESSION['contact_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - The Coffee Table</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="theme.php">
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="fas fa-mug-hot me-2"></i>The Coffee Table</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="centers.php">Centers</a></li>
          <li class="nav-item"><a class="nav-link" href="stories.php">Stories</a></li>
          <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
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

  <!-- Contact Section -->
  <div class="container py-5">
    <h1 class="section-title text-center mb-4"><?= t($content, 'title', 'Contact Us') ?></h1>
    <p class="text-center mb-5"><?= t($content, 'subtitle', "We'd love to hear from you. Send us a message and we'll get back to you as soon as possible.") ?></p>

    <!-- Flash Messages -->
    <?php if ($success): ?>
      <div class="alert alert-success alert-dismissible text-center">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible text-center">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row">
      <!-- Contact Form -->
      <div class="col-md-7">
        <form action="contact_send.php" method="POST">
          <div class="mb-3">
            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required
                   value="<?= isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['name']) : '' ?>">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Your Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control" required
                   pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                   oninvalid="this.setCustomValidity('Please enter a valid email e.g. name@example.com')"
                   oninput="this.setCustomValidity('')">
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i>Send Message
          </button>
        </form>
      </div>

      <!-- Contact Info + Map -->
      <div class="col-md-5">
        <h4>Get in Touch</h4>
        <p><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'address', 'Wyndham Vale, Victoria') ?></p>
        <p><i class="fas fa-phone me-2"></i><?= t($content, 'phone', '(03) 9876 5432') ?></p>
        <p><i class="fas fa-envelope me-2"></i><?= t($content, 'email', 'info@thecoffeetable.org.au') ?></p>
        <div class="mt-4">
          <h5>Find Us</h5>
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5295.727075023812!2d144.67379977252935!3d-37.886999696674906!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad6858b35b16787%3A0xad8adf289cefea73!2sWyndham%20Park%20Community%20Centre!5e0!3m2!1sen!2sau!4v1762221343467!5m2!1sen!2sau"
            width="100%" height="255" style="border:0;border-radius:8px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>

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
          <p><i class="fas fa-map-marker-alt me-2"></i><?= t($content, 'address', 'Wyndham Vale, Victoria') ?></p>
          <p><i class="fas fa-phone me-2"></i><?= t($content, 'phone', '(03) 9876 5432') ?></p>
          <p><i class="fas fa-envelope me-2"></i><?= t($content, 'email', 'info@thecoffeetable.org.au') ?></p>
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
      <div class="text-center small"><p class="mb-0">&copy; 2025 The Coffee Table. All rights reserved.</p></div>
    </div>
  </footer>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header" style="background-color:#8B4513; color:white;">
      <h5 class="modal-title"><i class="fas fa-sign-in-alt me-2"></i>Login</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <form action="auth/login.php" method="POST">
        <div class="mb-3"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
      <p class="text-center mt-3 mb-0">Don't have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Register here</a></p>
    </div>
  </div></div></div>

  <!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header" style="background-color:#8B4513; color:white;">
      <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Create an Account</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <form action="auth/register.php" method="POST">
        <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" required
            pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
            oninvalid="this.setCustomValidity('Please enter a valid email e.g. name@example.com')"
            oninput="this.setCustomValidity('')">
        </div>
        <div class="mb-3"><label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" minlength="6" required>
          <div class="form-text">Minimum 6 characters.</div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Create Account</button>
      </form>
      <p class="text-center mt-3 mb-0">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login here</a></p>
    </div>
  </div></div></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
