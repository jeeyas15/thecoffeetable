<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM stories WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$stories = $stmt->fetchAll();

$navAvatar = null;
if (!empty($user['avatar']) && file_exists('uploads/avatars/' . $user['avatar'])) {
    $navAvatar = 'uploads/avatars/' . htmlspecialchars($user['avatar']);
}

$success = $_SESSION['profile_success'] ?? null;
$error   = $_SESSION['profile_error'] ?? null;
unset($_SESSION['profile_success'], $_SESSION['profile_error']);

function getAvatar($user) {
    if (!empty($user['avatar']) && file_exists('uploads/avatars/' . $user['avatar'])) {
        return 'uploads/avatars/' . htmlspecialchars($user['avatar']);
    }
    return null;
}
$avatarPath = getAvatar($user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - The Coffee Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-card {
            border: 2px solid #D2B48C;
            border-radius: 12px;
            padding: 30px;
            background: white;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 90px; height: 90px;
            background-color: #8B4513;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 2.2rem;
            margin: 0 auto 10px;
            overflow: hidden;
            border: 3px solid #D2B48C;
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-upload-label { cursor: pointer; color: #8B4513; font-size: 0.85rem; text-decoration: underline; }
        .avatar-upload-label:hover { color: #5D4037; }
        .story-card {
            border: 2px solid #D2B48C;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
        }
        .badge-pending  { background-color: #FFC107; color: #333; }
        .badge-approved { background-color: #28a745; color: white; }
        .badge-rejected { background-color: #dc3545; color: white; }
        .section-heading { color: #8B4513; border-bottom: 3px solid #F4A460; padding-bottom: 10px; margin-bottom: 25px; }
        .nav-pills .nav-link {
            color: #333333 !important;
            background-color: #FFF8F0;
            border: 2px solid #D2B48C;
            margin-bottom: 8px;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
        }
        .nav-pills .nav-link:hover { background-color: #D2B48C; border-color: #8B4513; color: #3d1f0a !important; }
        .nav-pills .nav-link.active { background-color: #8B4513 !important; border-color: #8B4513 !important; color: white !important; }
        .photo-preview-wrapper { position: relative; display: inline-block; }
        .remove-photo-btn {
            position: absolute; top: -5px; right: -5px;
            width: 22px; height: 22px; border-radius: 50%;
            background: #dc3545; color: white; border: none;
            font-size: 11px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; padding: 0;
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="centers.php">Centers</a></li>
                    <li class="nav-item"><a class="nav-link" href="stories.php">Stories</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item ms-2">
                        <a class="nav-link text-warning d-flex align-items-center gap-1" href="profile.php">
                            <?php if ($navAvatar): ?>
                                <img src="<?= $navAvatar ?>" alt="avatar" style="width:26px;height:26px;border-radius:50%;object-fit:cover;border:2px solid #F4A460;">
                            <?php else: ?><i class="fas fa-user"></i><?php endif; ?>
                            <?= htmlspecialchars($_SESSION['name']) ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm mt-1 ms-2" href="auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible">✅ <?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible">⚠️ <?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row">
            <!-- Left: Profile Summary -->
            <div class="col-md-3 mb-4">
                <div class="profile-card text-center">
                    <div class="photo-preview-wrapper mb-2">
                        <div class="profile-avatar" id="avatarDisplay">
                            <?php if ($avatarPath): ?>
                                <img src="<?= $avatarPath ?>?v=<?= time() ?>" alt="Profile photo" id="avatarImg">
                            <?php else: ?>
                                <span id="avatarInitial"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($avatarPath): ?>
                            <form method="POST" action="auth/update_profile.php" style="display:inline;">
                                <input type="hidden" name="action" value="remove_photo">
                                <button type="submit" class="remove-photo-btn" title="Remove photo"
                                        onclick="return confirm('Remove your profile photo?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="auth/update_profile.php" enctype="multipart/form-data" id="avatarForm">
                        <input type="hidden" name="action" value="upload_photo">
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;" onchange="previewAndSubmit(this)">
                        <label for="avatarInput" class="avatar-upload-label">
                            <i class="fas fa-camera me-1"></i><?= $avatarPath ? 'Change Photo' : 'Upload Photo' ?>
                        </label>
                        <small class="d-block text-muted" style="font-size:0.75rem;">JPG, PNG · Max 2MB</small>
                    </form>

                    <hr>
                    <h5 class="mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge" style="background-color:#8B4513;"><?= ucfirst($user['role'] ?? 'user') ?></span>
                    <hr>
                    <p class="text-muted small mb-1"><i class="fas fa-calendar-alt me-1"></i>Joined <?= date('M Y', strtotime($user['created_at'])) ?></p>
                    <p class="text-muted small mb-0"><i class="fas fa-book-open me-1"></i><?= count($stories) ?> <?= count($stories) === 1 ? 'story' : 'stories' ?> submitted</p>
                </div>

                <div class="nav flex-column nav-pills">
                    <button class="nav-link active mb-2" data-bs-toggle="pill" data-bs-target="#infoTab"><i class="fas fa-user me-2"></i>My Information</button>
                    <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#passwordTab"><i class="fas fa-lock me-2"></i>Change Password</button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#storiesTab">
                        <i class="fas fa-book-open me-2"></i>My Stories
                        <span class="badge ms-1" style="background-color:#D2B48C;color:#5D4037;"><?= count($stories) ?></span>
                    </button>
                </div>
            </div>

            <!-- Right: Tab Content -->
            <div class="col-md-9">
                <div class="tab-content">

                    <!-- My Information Tab -->
                    <div class="tab-pane fade show active" id="infoTab">
                        <div class="profile-card">
                            <h4 class="section-heading"><i class="fas fa-user me-2"></i>My Information</h4>
                            <form action="auth/update_profile.php" method="POST">
                                <input type="hidden" name="action" value="update_info">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required
                                        pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                                        oninvalid="this.setCustomValidity('Please enter a valid email e.g. name@example.com')"
                                        oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Member Since</label>
                                    <input type="text" class="form-control" value="<?= date('d F Y', strtotime($user['created_at'])) ?>" disabled>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="passwordTab">
                        <div class="profile-card">
                            <h4 class="section-heading"><i class="fas fa-lock me-2"></i>Change Password</h4>
                            <form action="auth/update_profile.php" method="POST">
                                <input type="hidden" name="action" value="update_password">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                    <div class="form-text">Enter your current password to confirm your identity.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">New Password</label>
                                    <input type="password" name="new_password" class="form-control" minlength="6" required>
                                    <div class="form-text">Minimum 6 characters.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-key me-2"></i>Update Password</button>
                            </form>
                        </div>
                    </div>

                    <!-- My Stories Tab -->
                    <div class="tab-pane fade" id="storiesTab">
                        <div class="profile-card">
                            <h4 class="section-heading"><i class="fas fa-book-open me-2"></i>My Stories</h4>
                            <?php if (empty($stories)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-book fa-3x mb-3 d-block"></i>
                                    <p>You haven't submitted any stories yet.</p>
                                    <a href="stories.php" class="btn btn-primary">Share Your Story</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($stories as $story): ?>
                                    <div class="story-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?= date('d M Y', strtotime($story['created_at'])) ?>
                                            </small>
                                            <?php if ($story['status'] === 'approved'): ?>
                                                <span class="badge badge-approved">✅ Approved</span>
                                            <?php elseif ($story['status'] === 'rejected'): ?>
                                                <span class="badge badge-rejected">❌ Rejected</span>
                                            <?php else: ?>
                                                <span class="badge badge-pending">⏳ Pending Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-3"><?= nl2br(htmlspecialchars($story['story'])) ?></p>

                                        <?php if ($story['status'] === 'rejected'): ?>
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                This story was not approved. You're welcome to submit a new one.
                                            </small>
                                        <?php endif; ?>

                                        <!-- Delete Button -->
                                        <form method="POST" action="stories/delete.php"
                                              onsubmit="return confirm('Are you sure you want to delete this story? This cannot be undone.')">
                                            <input type="hidden" name="story_id" value="<?= $story['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i>Delete Story
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                                <div class="text-center mt-3">
                                    <a href="stories.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Submit Another Story</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewAndSubmit(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert('File too large. Maximum size is 2MB.');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const display = document.getElementById('avatarDisplay');
                    display.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="width:100%;height:100%;object-fit:cover;">';
                };
                reader.readAsDataURL(file);
                setTimeout(() => document.getElementById('avatarForm').submit(), 500);
            }
        }
    </script>
</body>
</html>
