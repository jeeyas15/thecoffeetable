<?php
header('Content-Type: text/css');
header('Cache-Control: no-cache');

require 'config/db.php';

$stmt = $pdo->query("SELECT setting, value FROM site_theme");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$theme = [];
foreach ($rows as $row) {
    $theme[$row['setting']] = $row['value'];
}

$primary   = $theme['primary_color']   ?? '#8B4513';
$secondary = $theme['secondary_color'] ?? '#D2B48C';
$accent    = $theme['accent_color']    ?? '#F4A460';
$light     = $theme['light_color']     ?? '#F5F5DC';
$dark      = $theme['dark_color']      ?? '#5D4037';
$navbar    = $theme['navbar_color']    ?? '#8B4513';
$footer    = $theme['footer_color']    ?? '#5D4037';
$font      = $theme['body_font']       ?? 'Segoe UI';
$hfont     = $theme['heading_font']    ?? 'Segoe UI';
$fsize     = $theme['font_size']       ?? '16px';
?>

:root {
    --primary-color: <?= $primary ?>;
    --secondary-color: <?= $secondary ?>;
    --accent-color: <?= $accent ?>;
    --light-color: <?= $light ?>;
    --dark-color: <?= $dark ?>;
}

body {
    font-family: '<?= $font ?>', Tahoma, Geneva, Verdana, sans-serif;
    background-color: <?= $light ?>;
    color: #333;
    line-height: 1.6;
    font-size: <?= $fsize ?>;
}

h1, h2, h3, h4, h5, h6 {
    font-family: '<?= $hfont ?>', Tahoma, Geneva, Verdana, sans-serif;
}

.navbar {
    background-color: <?= $navbar ?> !important;
}

.navbar-brand, .nav-link {
    color: white !important;
}

.nav-link.active { font-weight: bold; }

.section-title {
    color: <?= $primary ?>;
    border-bottom: 3px solid <?= $accent ?>;
    padding-bottom: 10px;
    margin-bottom: 30px;
}

.btn-primary {
    background-color: <?= $primary ?>;
    border-color: <?= $primary ?>;
}
.btn-primary:hover {
    background-color: <?= $dark ?>;
    border-color: <?= $dark ?>;
}

.service-card { transition: transform 0.3s; border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.service-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
.service-icon { font-size: 3rem; color: <?= $primary ?>; margin-bottom: 20px; }

.testimonial-card { background-color: white; border-left: 4px solid <?= $accent ?>; }

.center-card { transition: all 0.3s; }
.center-card:hover { transform: scale(1.02); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }

.footer {
    background-color: <?= $footer ?>;
    color: white;
    padding-top: 40px;
}
.footer a { color: white; text-decoration: none; }
.footer a:hover { color: <?= $accent ?>; text-decoration: underline; }

.social-icon { font-size: 1.5rem; margin: 0 10px; color: <?= $secondary ?>; transition: color 0.3s; }
.social-icon:hover { color: <?= $accent ?>; }

.hero-section { position: relative; height: 500px; overflow: hidden; }
.hero-img { width: 100%; height: 100%; object-fit: cover; filter: brightness(65%); }
.hero-overlay {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.4); display: flex; align-items: center;
    justify-content: center; text-align: center; color: white; padding: 20px;
}

.about-img {
    width: 100%; height: 280px; object-fit: cover; border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: transform 0.3s ease;
}
.about-img:hover { transform: scale(1.03); }

.map-container { height: 400px; border-radius: 8px; overflow: hidden; }
.map-container iframe { width: 100%; height: 100%; border: 0; display: block; }

#testimonials { background-color: <?= $light ?>; }
#testimonials h2 { color: <?= $primary ?>; font-weight: 700; }
#testimonials .card { border-radius: 20px; background-color: white; transition: transform 0.3s ease; }
#testimonials .card:hover { transform: translateY(-5px); }

.carousel-control-prev-icon, .carousel-control-next-icon {
    background-color: <?= $primary ?>; border-radius: 50%;
    width: 45px; height: 45px; background-size: 50%, 50%; opacity: 0.9;
}
.carousel-control-prev-icon:hover, .carousel-control-next-icon:hover {
    background-color: <?= $accent ?>;
}

.service-section { border: 2px solid <?= $secondary ?>; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
.service-section img { border-radius: 12px; border: 2px solid <?= $secondary ?>; }
.center-section { border: 2px solid <?= $secondary ?>; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
.story-section { border: 2px solid <?= $secondary ?>; border-radius: 12px; padding: 20px; margin-bottom: 30px; }

@media (max-width: 768px) {
    .hero-section { height: 350px; }
    .hero-overlay h1 { font-size: 1.8rem; }
    .hero-overlay p { font-size: 1rem; }
    .section-title { font-size: 1.8rem; }
    .map-container { height: 250px; margin-bottom: 20px; }
    .about-img { height: 220px; }
}
