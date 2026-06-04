<?php
session_start();
require 'config/db.php';

$stmt = $pdo->query("SELECT section, field, value FROM site_content");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$content = [];
foreach ($rows as $row) { $content[$row['section']][$row['field']] = $row['value']; }
function c($content, $section, $field, $default = '') {
    return htmlspecialchars($content[$section][$field] ?? $default);
}
    $navAvatar = null;
    if (isset($_SESSION['user_id'])) {
        $stmtNav = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmtNav->execute([$_SESSION['user_id']]);
        $navUser = $stmtNav->fetch();
        if (!empty($navUser['avatar']) && file_exists('uploads/avatars/' . $navUser['avatar'])) {
            $navAvatar = 'uploads/avatars/' . htmlspecialchars($navUser['avatar']);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Coffee Table – Building Community in Wyndham Vale</title>
    <meta name="description" content="The Coffee Table is a community organisation in Wyndham Vale.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="theme.php">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-mug-hot me-2"></i>The Coffee Table</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="centers.php">Centers</a></li>
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

    <?php if (isset($_SESSION['register_success'])): ?>
        <div class="alert alert-success alert-dismissible text-center mb-0"><?= $_SESSION['register_success'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['register_success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="alert alert-danger alert-dismissible text-center mb-0"><?= $_SESSION['login_error'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['register_error'])): ?>
        <div class="alert alert-danger alert-dismissible text-center mb-0"><?= $_SESSION['register_error'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['register_error']); ?>
    <?php endif; ?>

    <!-- Hero -->
    <header class="hero-section text-center text-white" role="banner">
        <img src="images/hero.jpg" alt="Volunteers distributing food and supplies" class="hero-img">
        <div class="hero-overlay">
            <div class="container">
                <h1 class="display-4 fw-bold"><?= c($content,'hero','title','Building Community, One Cup at a Time') ?></h1>
                <p class="lead"><?= c($content,'hero','subtitle','Supporting Wyndham Vale through food security, education, and connection') ?></p>
                <a href="<?= c($content,'hero','button1_link','services.php') ?>" class="btn btn-primary btn-lg me-2"><?= c($content,'hero','button1_text','Our Services') ?></a>
                <a href="<?= c($content,'hero','button2_link','contact.php') ?>" class="btn btn-outline-light btn-lg"><?= c($content,'hero','button2_text','Get Involved') ?></a>
            </div>
        </div>
    </header>

    <!-- About Preview -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center"><?= c($content,'about','title','About The Coffee Table') ?></h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="lead"><?= c($content,'about','paragraph1') ?></p>
                    <p><?= c($content,'about','paragraph2') ?></p>
                    <a href="about.php" class="btn btn-primary"><?= c($content,'about','button_text','Learn More About Us') ?></a>
                </div>
                <div class="col-md-6">
                    <img src="images/about.jpg" alt="Community members at The Coffee Table" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Preview -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Our Services</h2>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4"><div class="card service-card h-100"><div class="card-body text-center"><div class="service-icon"><i class="fas fa-utensils"></i></div><h5 class="card-title">Food Bank</h5><p class="card-text">Emergency food assistance for families in need.</p><a href="services.php#food-bank" class="btn btn-sm btn-primary">Learn More</a></div></div></div>
                <div class="col-md-6 col-lg-3 mb-4"><div class="card service-card h-100"><div class="card-body text-center"><div class="service-icon"><i class="fas fa-plate-wheat"></i></div><h5 class="card-title">Community Kitchen</h5><p class="card-text">Learn cooking skills and share meals with others.</p><a href="services.php#community-kitchen" class="btn btn-sm btn-primary">Learn More</a></div></div></div>
                <div class="col-md-6 col-lg-3 mb-4"><div class="card service-card h-100"><div class="card-body text-center"><div class="service-icon"><i class="fas fa-mug-hot"></i></div><h5 class="card-title">Coffee Table</h5><p class="card-text">A welcoming hub offering companionship and support.</p><a href="services.php#coffee-table" class="btn btn-sm btn-primary">Learn More</a></div></div></div>
                <div class="col-md-6 col-lg-3 mb-4"><div class="card service-card h-100"><div class="card-body text-center"><div class="service-icon"><i class="fas fa-scissors"></i></div><h5 class="card-title">Sewing Classes</h5><p class="card-text">Learn practical skills in a friendly environment.</p><a href="services.php#sewing-classes" class="btn btn-sm btn-primary">Learn More</a></div></div></div>
            </div>
            <div class="text-center mt-4"><a href="services.php" class="btn btn-primary">View All Services</a></div>
        </div>
    </section>

    <!-- Centers Preview -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Our Centers</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4"><div class="card center-card"><div class="card-body"><h5 class="card-title">Wyndham Vale Hub</h5><p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>115 Kookaburra Ave</p><p class="card-text">Our flagship center offering all core services.</p><a href="centers.php#wyndham-vale" class="btn btn-sm btn-primary">Details</a></div></div></div>
                <div class="col-md-6 col-lg-4 mb-4"><div class="card center-card"><div class="card-body"><h5 class="card-title">Tarneit Outreach</h5><p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>150 Sunset Views Blvd</p><p class="card-text">Specialized services for youth and families.</p><a href="centers.php#tarneit" class="btn btn-sm btn-primary">Details</a></div></div></div>
                <div class="col-md-6 col-lg-4 mb-4"><div class="card center-card"><div class="card-body"><h5 class="card-title">Point Cook Community</h5><p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>1/21 Cheetham St</p><p class="card-text">Focus on elderly support programs.</p><a href="centers.php#point-cook" class="btn btn-sm btn-primary">Details</a></div></div></div>
            </div>
            <div class="text-center mt-4"><a href="centers.php" class="btn btn-primary">View All Centers</a></div>
        </div>
    </section>

    <!-- Stories Preview -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Community Stories</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4"><div class="card testimonial-card h-100"><div class="card-body"><blockquote class="blockquote"><p>"The Coffee Table gave me hope when I had nowhere else to turn."</p><footer class="blockquote-footer">Sarah M., Wyndham Vale</footer></blockquote></div></div></div>
                <div class="col-md-6 col-lg-4 mb-4"><div class="card testimonial-card h-100"><div class="card-body"><blockquote class="blockquote"><p>"Learning to cook in the community kitchen changed my life."</p><footer class="blockquote-footer">James T., Tarneit</footer></blockquote></div></div></div>
                <div class="col-md-6 col-lg-4 mb-4"><div class="card testimonial-card h-100"><div class="card-body"><blockquote class="blockquote"><p>"The sense of community here is truly special."</p><footer class="blockquote-footer">Robert K., Volunteer</footer></blockquote></div></div></div>
            </div>
            <div class="text-center mt-4"><a href="stories.php" class="btn btn-primary">Read More Stories</a></div>
        </div>
    </section>

    <!-- Testimonials Carousel -->
    <section id="testimonials" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="mb-4">What Our Community Says</h2>
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                    <div class="carousel-item active"><div class="card shadow-sm border-0 p-4 mx-auto" style="max-width:800px;"><p class="fst-italic">"The Coffee Table gave me a safe place to connect with others."</p><h6 class="mt-3 mb-0 fw-bold">— Sarah Williams</h6></div></div>
                    <div class="carousel-item"><div class="card shadow-sm border-0 p-4 mx-auto" style="max-width:800px;"><p class="fst-italic">"Joining the community kitchen showed me how powerful sharing meals can be."</p><h6 class="mt-3 mb-0 fw-bold">— Daniel Kim</h6></div></div>
                    <div class="carousel-item"><div class="card shadow-sm border-0 p-4 mx-auto" style="max-width:800px;"><p class="fst-italic">"The sewing class gave me confidence and purpose."</p><h6 class="mt-3 mb-0 fw-bold">— Priya Thapa</h6></div></div>
                    <div class="carousel-item"><div class="card shadow-sm border-0 p-4 mx-auto" style="max-width:800px;"><p class="fst-italic">"When I was struggling, the food bank helped me feed my family."</p><h6 class="mt-3 mb-0 fw-bold">— Jason Brown</h6></div></div>
                    <div class="carousel-item"><div class="card shadow-sm border-0 p-4 mx-auto" style="max-width:800px;"><p class="fst-italic">"I was new to Wyndham Vale but The Coffee Table welcomed me."</p><h6 class="mt-3 mb-0 fw-bold">— Ahmed Khan</h6></div></div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
            </div>
        </div>
    </section>

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
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header" style="background-color:#8B4513; color:white;">
                <h5 class="modal-title"><i class="fas fa-sign-in-alt me-2"></i>Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="auth/login.php" method="POST">
                    <div class="mb-3"><label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3 mb-0">Don't have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Register here</a></p>
            </div>
        </div></div>
    </div>
    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header" style="background-color:#8B4513; color:white;">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Create an Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="auth/register.php" method="POST">
                    <div class="mb-3"><label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required
                            pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                            oninvalid="this.setCustomValidity('Please enter a valid email e.g. name@example.com')"
                            oninput="this.setCustomValidity('')"></div>
                    <div class="mb-3"><label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                        <div class="form-text">Minimum 6 characters.</div></div>
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
                <p class="text-center mt-3 mb-0">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login here</a></p>
            </div>
        </div></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
