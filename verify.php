<?php
session_start();
require 'config/db.php';


// Redirect if not coming from registration
if (!isset($_SESSION['verify_email'])) {
    header('Location: index.php');
    exit;
}

$email = $_SESSION['verify_email'];
$name  = $_SESSION['verify_name'] ?? 'there';
$error   = $_SESSION['verify_error'] ?? null;
$success = $_SESSION['verify_success'] ?? null;
unset($_SESSION['verify_error'], $_SESSION['verify_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email – The Coffee Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="theme.php">
    <style>
        body {
            background-color: #F5F5DC;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verify-card {
            background: white;
            border: 2px solid #D2B48C;
            border-radius: 16px;
            padding: 40px;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .code-input {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 12px;
            text-align: center;
            border: 2px solid #D2B48C;
            border-radius: 8px;
            padding: 15px;
            color: #8B4513;
            width: 100%;
        }
        .code-input:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139,69,19,0.15);
            outline: none;
        }
        .icon-circle {
            width: 70px;
            height: 70px;
            background-color: #FFF8F0;
            border: 2px solid #D2B48C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: #8B4513;
        }
        .resend-link {
            color: #8B4513;
            cursor: pointer;
            text-decoration: underline;
            background: none;
            border: none;
            padding: 0;
            font-size: inherit;
        }
        .resend-link:hover { color: #5D4037; }
        #timer { font-weight: bold; color: #8B4513; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="icon-circle">
            <i class="fas fa-envelope-open-text"></i>
        </div>

        <h3 class="text-center mb-2" style="color:#8B4513;">Check Your Email</h3>
        <p class="text-center text-muted mb-4">
            Hi <strong><?= htmlspecialchars($name) ?></strong>! We sent a 6-digit verification code to<br>
            <strong><?= htmlspecialchars($email) ?></strong>
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="auth/verify_code.php" method="POST">
            <div class="mb-4">
                <input type="text" name="code" class="code-input"
                       maxlength="6" placeholder="000000"
                       pattern="[0-9]{6}" inputmode="numeric"
                       autocomplete="one-time-code" required autofocus>
                <div class="form-text text-center mt-2">Enter the 6-digit code from your email</div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="fas fa-check-circle me-2"></i>Verify Email
            </button>
        </form>

        <div class="text-center text-muted small">
            <p class="mb-1">Code expires in <span id="timer">15:00</span></p>
            <p class="mb-0">Didn't receive it?
                <form action="auth/resend_code.php" method="POST" style="display:inline;">
                    <button type="submit" class="resend-link" id="resendBtn">Resend code</button>
                </form>
            </p>
        </div>

        <hr class="my-4" style="border-color:#D2B48C;">
        <div class="text-center">
            <a href="index.php" class="text-muted small">← Back to Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown timer
        let seconds = 15 * 60;
        const timerEl = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');

        function updateTimer() {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            timerEl.textContent = m + ':' + String(s).padStart(2, '0');
            if (seconds <= 0) {
                timerEl.textContent = 'Expired';
                timerEl.style.color = '#dc3545';
                resendBtn.style.fontWeight = 'bold';
            } else {
                seconds--;
                setTimeout(updateTimer, 1000);
            }
        }
        updateTimer();

        // Auto-format code input
        document.querySelector('input[name="code"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>
</html>
