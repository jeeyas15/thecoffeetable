<?php
session_start();
require 'config/db.php';

$navAvatar = null;
if (isset($_SESSION['user_id'])) {
    $stmtNav = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmtNav->execute([$_SESSION['user_id']]);
    $navUser = $stmtNav->fetch();
    if (!empty($navUser['avatar']) && file_exists('uploads/avatars/' . $navUser['avatar'])) {
        $navAvatar = 'uploads/avatars/' . htmlspecialchars($navUser['avatar']);
    }
}

$success = $_SESSION['story_success'] ?? null;
unset($_SESSION['story_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Stories - The Coffee Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="theme.php">
    <style>
        .story-section { border: 2px solid #d2b48c; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
        .story-section img { border: 2px solid #d2b48c; border-radius: 8px; }
        .community-story-card { border: 2px solid #d2b48c; border-radius: 12px; padding: 25px; margin-bottom: 20px; background: white; }
        .community-story-card .story-author { font-weight: bold; color: #8B4513; }
        .submit-box { background: white; border: 2px solid #d2b48c; border-radius: 12px; padding: 30px; margin-bottom: 40px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-mug-hot me-2"></i>The Coffee Table</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="centers.php">Centers</a></li>
                    <li class="nav-item"><a class="nav-link active" href="stories.php">Stories</a></li>
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
        <h1 class="section-title">Community Stories</h1>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible">✅ <?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
            <div class="alert alert-warning alert-dismissible">⚠️ Please log in to submit a story.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Submit Box -->
        <div class="submit-box">
            <?php if (isset($_SESSION['user_id'])): ?>
                <h4><i class="fas fa-pen me-2" style="color:#8B4513;"></i>Share Your Story</h4>
                <p class="text-muted">Your story will be reviewed before it appears on the site.</p>
                <form action="stories/submit.php" method="POST">
                    <div class="mb-3"><textarea name="story" class="form-control" rows="5" placeholder="Share your experience with The Coffee Table..." required></textarea></div>
                    <button type="submit" class="btn btn-primary">Submit Story</button>
                </form>
            <?php else: ?>
                <div class="text-center py-3">
                    <i class="fas fa-mug-hot fa-2x mb-3" style="color:#8B4513;"></i>
                    <h5>Want to share your story?</h5>
                    <p class="text-muted">Join our community to share your experience.</p>
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#registerModal">Register – it's free!</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Community Stories from Database -->
        <h2 class="section-title">Stories from Our Community</h2>
        <div id="community-stories">
            <div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading stories...</div>
        </div>

        <!-- Static Featured Stories -->
        <h2 class="section-title mt-5">Featured Stories</h2>
        <div class="row mb-5 story-section">
            <div class="col-md-8">
                <h2>Sarah's Journey to Stability</h2>
                <p class="text-muted">Wyndham Vale Resident</p>
                <p>"The Coffee Table gave me hope when I had nowhere else to turn. After losing my job and facing eviction, I was struggling to feed my two young children. The food bank helped feed my family during our toughest month, while the community kitchen taught me skills to prepare nutritious meals on a budget. Today, I have a new job, stable housing, and I volunteer at the center one day a week."</p>
                <p><strong>How We Helped:</strong> Food Bank assistance, Community Kitchen classes, Peer support groups</p>
            </div>
            <div class="col-md-4"><img src="https://placehold.co/400x300/D2B48C/5D4037?text=Sarah" alt="Sarah" class="img-fluid rounded shadow"></div>
        </div>
        <div class="row mb-5 story-section">
            <div class="col-md-8">
                <h2>James's Culinary Transformation</h2>
                <p class="text-muted">Tarneit Community Member</p>
                <p>"Learning to cook in the community kitchen changed my life. The confidence I gained led me to enroll in an advanced culinary course, and I even started a small catering business for local events."</p>
                <p><strong>How We Helped:</strong> Community Kitchen program, Advanced cooking workshops, Business mentorship</p>
            </div>
            <div class="col-md-4"><img src="https://placehold.co/400x300/8B4513/FFFFFF?text=James" alt="James" class="img-fluid rounded shadow"></div>
        </div>
        <div class="row mb-5 story-section">
            <div class="col-md-8">
                <h2>Robert's Volunteer Journey</h2>
                <p class="text-muted">Long-term Volunteer</p>
                <p>"As a volunteer, I've seen firsthand how our services transform lives. What began as a way to stay busy became one of the most rewarding experiences of my life."</p>
                <p><strong>How He Contributes:</strong> Food bank coordination, Peer mentoring, Event organization</p>
            </div>
            <div class="col-md-4"><img src="https://placehold.co/400x300/5D4037/D2B48C?text=Robert" alt="Robert" class="img-fluid rounded shadow"></div>
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
    <script>
        fetch('stories/fetch.php')
            .then(res => res.json())
            .then(stories => {
                const container = document.getElementById('community-stories');
                if (stories.length === 0) {
                    container.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-comment-slash fa-2x mb-3 d-block"></i><p>No community stories yet. Be the first to share yours!</p></div>';
                    return;
                }
                container.innerHTML = stories.map(s => `
                    <div class="community-story-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="story-author"><i class="fas fa-user-circle me-2"></i>${escapeHtml(s.name)}</span>
                            <span class="text-muted small"><i class="fas fa-calendar-alt me-1"></i>${new Date(s.created_at).toLocaleDateString('en-AU',{day:'numeric',month:'long',year:'numeric'})}</span>
                        </div>
                        <p class="mb-0">${escapeHtml(s.story).replace(/\n/g,'<br>')}</p>
                    </div>`).join('');
            })
            .catch(() => { document.getElementById('community-stories').innerHTML = '<p class="text-muted">Unable to load stories at this time.</p>'; });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
    </script>
</body>
</html>
