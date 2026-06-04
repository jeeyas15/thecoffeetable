<?php
session_start();
require 'config/db.php';

$stmt = $pdo->query("SELECT field, value FROM site_content WHERE section = 'about_page'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$content = [];
foreach ($rows as $row) { $content[$row['field']] = $row['value']; }

$stmt = $pdo->query("SELECT field, filename, alt_text FROM site_images WHERE section = 'about_page'");
$imgRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$images = [];
foreach ($imgRows as $img) { $images[$img['field']] = $img; }

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

$availableImages = 0;
for ($i = 1; $i <= 4; $i++) {
    if (isset($images["image$i"]) && !empty($images["image$i"]['filename'])) $availableImages++;
}
$textCol  = $availableImages > 0 ? 'col-md-8' : 'col-md-12';
$imageCol = $availableImages > 0 ? 'col-md-4' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | The Coffee Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="theme.php">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-mug-hot me-2"></i>The Coffee Table</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="centers.php">Centers</a></li>
                    <li class="nav-item"><a class="nav-link" href="stories.php">Stories</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-2">
                            <a class="nav-link text-warning d-flex align-items-center gap-1" href="profile.php">
                                <?php if ($navAvatar): ?>
                                    <img src="<?= $navAvatar ?>" alt="avatar" style="width:26px;height:26px;border-radius:50%;object-fit:cover;border:2px solid #F4A460;">
                                <?php else: ?><i class="fas fa-user"></i><?php endif; ?>
                                <?= htmlspecialchars($_SESSION['name']) ?>
                            </a>
                        </li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm mt-1 ms-2" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item ms-2"><button class="btn btn-outline-light btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button></li>
                        <li class="nav-item ms-2"><button class="btn btn-light btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="section-title"><?= t($content, 'title', 'About The Coffee Table') ?></h1>
        <div class="row">
            <div class="<?= $textCol ?>">
                <p class="lead"><?= t($content, 'lead') ?></p>
                <h3 class="mt-4"><?= t($content, 'mission_title', 'Our Mission') ?></h3>
                <p><?= t($content, 'mission_text') ?></p>
                <h3 class="mt-4"><?= t($content, 'vision_title', 'Our Vision') ?></h3>
                <p><?= t($content, 'vision_text') ?></p>
                <h3 class="mt-4"><?= t($content, 'history_title', 'Our History') ?></h3>
                <p><?= t($content, 'history_text') ?></p>
                <h3 class="mt-4"><?= t($content, 'impact_title', 'Our Impact') ?></h3>
                <ul>
                    <li><?= t($content, 'impact_stat1') ?></li>
                    <li><?= t($content, 'impact_stat2') ?></li>
                    <li><?= t($content, 'impact_stat3') ?></li>
                    <li><?= t($content, 'impact_stat4') ?></li>
                    <li><?= t($content, 'impact_stat5') ?></li>
                </ul>
            </div>
            <?php if ($availableImages > 0): ?>
            <div class="<?= $imageCol ?>">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php if (isset($images["image$i"]) && !empty($images["image$i"]['filename'])): $img = $images["image$i"]; ?>
                        <img src="images/<?= htmlspecialchars($img['filename']) ?>"
                             alt="<?= htmlspecialchars($img['alt_text']) ?>"
                             class="img-fluid rounded shadow mb-4 about-img">
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

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
            <div class="text-center small"><p class="mb-0">&copy; 2025 The Coffee Table. All rights reserved.</p></div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header" style="background-color:#8B4513; color:white;"><h5 class="modal-title"><i class="fas fa-sign-in-alt me-2"></i>Login</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><form action="auth/login.php" method="POST">
            <div class="mb-3"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form><p class="text-center mt-3 mb-0">Don't have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Register here</a></p></div>
    </div></div></div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header" style="background-color:#8B4513; color:white;"><h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Create an Account</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><form action="auth/register.php" method="POST">
            <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required
                    pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                    oninvalid="this.setCustomValidity('Please enter a valid email e.g. name@example.com')"
                    oninput="this.setCustomValidity('')"></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" minlength="6" required><div class="form-text">Minimum 6 characters.</div></div>
            <button type="submit" class="btn btn-primary w-100">Create Account</button>
        </form><p class="text-center mt-3 mb-0">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login here</a></p></div>
    </div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
