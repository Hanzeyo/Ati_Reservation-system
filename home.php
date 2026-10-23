<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Authenticated Portal Home: Facility & Dormitory Category Selection
 */

// Fetch active institutional facilities for dynamic dashboard indicators & showcase ordered by customized display sequence
$facStmt = $db->query("SELECT * FROM facilities WHERE status != 'inactive' AND deleted_at IS NULL ORDER BY display_order ASC, id ASC");
$allFacilities = $facStmt ? $facStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$hallsCount = 0;
$dormsCount = 0;
$guesthousesCount = 0;
foreach ($allFacilities as $f) {
  $ftype = strtolower($f['type']);
  if ($ftype === 'dorms') {
    $dormsCount++;
  } elseif ($ftype === 'guesthouses') {
    $guesthousesCount++;
  } else {
    $hallsCount++;
  }
}
$totalFacilities = count($allFacilities);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Select Facility - Agricultural Training Institute</title>
  <meta name="description"
    content="Choose between function halls and dormitory accommodations to start your reservation.">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
  <style>
    /* ==========================================================================
       HOMEPAGE SCENIC BACKGROUND & ATMOSPHERE
       ========================================================================== */
    body.booking-body.homepage-body {
      background:
        linear-gradient(180deg, rgba(14, 38, 24, 0.42) 0%, rgba(9, 28, 17, 0.58) 50%, rgba(6, 20, 12, 0.72) 100%),
        url('assets/images/home_bg.jpg') center center / cover no-repeat fixed,
        url('https://i.pinimg.com/1200x/9d/b7/c1/9db7c11bd7316998f7213e7784123841.jpg') center center / cover no-repeat fixed;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .homepage-body .booking-topbar {
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(220, 232, 224, 0.8);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .portal-category-wrapper {
      max-width: 960px;
      margin: 1.15rem auto 1.25rem;
      padding: 0 1.25rem;
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      justify-content: center;
      flex: 1;
    }

    /* ==========================================================================
       CATEGORY INTRO CARD (RICH SYSTEM AESTHETIC & ELEVATION)
       ========================================================================== */
    .portal-category-intro-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 251, 246, 0.96) 100%);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1.5px solid rgba(162, 215, 180, 0.85);
      border-top: 4px solid #174d2f;
      border-radius: 18px;
      padding: 1.2rem 2rem 1.1rem;
      text-align: center;
      margin-bottom: 1.15rem;
      box-shadow: 0 12px 32px rgba(5, 20, 11, 0.18), 0 2px 6px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .portal-category-intro-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #174d2f 0%, #eab308 50%, #2e7d32 100%);
      z-index: 1;
    }

    .portal-category-intro-card:hover {
      box-shadow: 0 16px 40px rgba(5, 20, 11, 0.22), 0 3px 8px rgba(0, 0, 0, 0.06);
    }

    .portal-category-logo-wrap {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 0.45rem;
    }

    .portal-category-logo {
      width: 48px;
      height: 48px;
      object-fit: contain;
      filter: drop-shadow(0 4px 12px rgba(23, 77, 47, 0.18));
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .portal-category-logo:hover {
      transform: scale(1.08);
    }

    .cat-sec-title {
      font-family: var(--font-serif);
      font-size: 1.6rem;
      font-weight: 900;
      letter-spacing: 0.025em;
      color: #0c331d;
      margin-bottom: 0.3rem;
      text-transform: uppercase;
    }

    .cat-sec-subtitle {
      font-size: 0.88rem;
      color: #3e5948;
      max-width: 620px;
      margin: 0 auto;
      line-height: 1.45;
    }

    .cat-sec-divider {
      width: 52px;
      height: 3px;
      background: linear-gradient(90deg, #174d2f 0%, #eab308 50%, #174d2f 100%);
      border-radius: 3px;
      margin: 0.65rem auto 0;
      box-shadow: 0 2px 6px rgba(234, 179, 8, 0.35);
    }

    /* ==========================================================================
       TWO MAIN CATEGORY CARDS (HALLS & DORMITORIES)
       ========================================================================== */
    .portal-category-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.25rem;
      margin-bottom: 0;
    }

    @media (max-width: 900px) {
      .portal-category-grid {
        grid-template-columns: 1fr;
      }
    }

    .portal-cat-card {
      border-radius: 18px;
      padding: 1.2rem 1.6rem 1.3rem;
      display: flex;
      flex-direction: column;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      cursor: pointer;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }

    .portal-cat-card .cat-card-header {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 0.45rem;
    }

    .cat-icon-container {
      border-radius: 20px;
      padding: 1.25rem 1.4rem;
      min-height: 122px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.1rem;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .cat-icon-container svg {
      width: 92px;
      height: 92px;
      display: block;
      filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.05));
    }

    /* ---------------- 1. HALLS CARD (FOREST EMERALD THEME) ---------------- */
    #cardCatHalls {
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 250, 244, 0.97) 50%, rgba(230, 247, 236, 0.96) 100%);
      border: 2px solid rgba(152, 212, 174, 0.9);
      border-top: 4px solid #174d2f;
      box-shadow: 0 12px 30px rgba(7, 35, 18, 0.16), 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #cardCatHalls:hover {
      border-color: #174d2f;
      background: linear-gradient(165deg, #ffffff 0%, #ecf8f0 100%);
      transform: translateY(-5px);
      box-shadow: 0 18px 45px rgba(23, 77, 47, 0.28), 0 0 0 2px rgba(23, 77, 47, 0.3);
    }

    #cardCatHalls .cat-count-badge {
      display: inline-flex;
      align-items: center;
      background: linear-gradient(135deg, #174d2f 0%, #206740 100%);
      color: #ffffff;
      font-size: 0.74rem;
      font-weight: 700;
      padding: 0.28rem 0.85rem;
      border-radius: 9999px;
      box-shadow: 0 3px 10px rgba(23, 77, 47, 0.24);
      letter-spacing: 0.02em;
    }

    #cardCatHalls .cat-icon-container {
      background: linear-gradient(135deg, #e4f5ea 0%, #ccecd7 100%);
      border: 1.5px solid #a4dcba;
      box-shadow: 0 6px 18px rgba(23, 77, 47, 0.08);
    }

    #cardCatHalls:hover .cat-icon-container {
      background: linear-gradient(135deg, #d8f2e1 0%, #bee7cb 100%);
      transform: scale(1.03);
      border-color: #174d2f;
      box-shadow: 0 8px 22px rgba(23, 77, 47, 0.15);
    }

    #cardCatHalls .cat-card-title {
      font-family: var(--font-serif);
      font-size: 1.4rem;
      font-weight: 800;
      color: #0c331d;
      margin-bottom: 0.3rem;
    }

    #cardCatHalls .cat-card-desc {
      font-size: 0.84rem;
      color: #3b5745;
      line-height: 1.45;
      margin-bottom: 0;
      flex-grow: 1;
    }

    /* ---------------- 2. DORMITORIES CARD (HARVEST GOLD THEME) ---------------- */
    #cardCatDormitories {
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 250, 241, 0.97) 50%, rgba(254, 245, 230, 0.96) 100%);
      border: 2px solid rgba(245, 215, 150, 0.9);
      border-top: 4px solid #d97706;
      box-shadow: 0 12px 30px rgba(50, 25, 5, 0.15), 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #cardCatDormitories:hover {
      border-color: #d97706;
      background: linear-gradient(165deg, #ffffff 0%, #fef7ec 100%);
      transform: translateY(-5px);
      box-shadow: 0 18px 45px rgba(217, 119, 6, 0.25), 0 0 0 2px rgba(217, 119, 6, 0.3);
    }

    #cardCatDormitories .cat-count-badge {
      display: inline-flex;
      align-items: center;
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      color: #ffffff;
      font-size: 0.74rem;
      font-weight: 700;
      padding: 0.28rem 0.85rem;
      border-radius: 9999px;
      box-shadow: 0 3px 10px rgba(217, 119, 6, 0.24);
      letter-spacing: 0.02em;
    }

    #cardCatDormitories .cat-icon-container {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border: 1.5px solid #fcd34d;
      box-shadow: 0 6px 18px rgba(217, 119, 6, 0.1);
    }

    #cardCatDormitories:hover .cat-icon-container {
      background: linear-gradient(135deg, #fde68a 0%, #fcd34d 100%);
      transform: scale(1.03);
      border-color: #d97706;
      box-shadow: 0 8px 22px rgba(217, 119, 6, 0.16);
    }

    #cardCatDormitories .cat-card-title {
      font-family: var(--font-serif);
      font-size: 1.4rem;
      font-weight: 800;
      color: #451a03;
      margin-bottom: 0.3rem;
    }

    #cardCatDormitories .cat-card-desc {
      font-size: 0.84rem;
      color: #5c442a;
      line-height: 1.45;
      margin-bottom: 0;
      flex-grow: 1;
    }

    /* ---------------- 3. GUESTHOUSES CARD (TEAL / EMERALD THEME) ---------------- */
    #cardCatGuesthouses {
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.97) 50%, rgba(230, 250, 246, 0.96) 100%);
      border: 2px solid rgba(153, 246, 228, 0.9);
      border-top: 4px solid #0f766e;
      box-shadow: 0 12px 30px rgba(15, 118, 110, 0.15), 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #cardCatGuesthouses:hover {
      border-color: #0f766e;
      background: linear-gradient(165deg, #ffffff 0%, #ecfdf5 100%);
      transform: translateY(-5px);
      box-shadow: 0 18px 45px rgba(15, 118, 110, 0.25), 0 0 0 2px rgba(15, 118, 110, 0.3);
    }

    #cardCatGuesthouses .cat-count-badge {
      display: inline-flex;
      align-items: center;
      background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
      color: #ffffff;
      font-size: 0.74rem;
      font-weight: 700;
      padding: 0.28rem 0.85rem;
      border-radius: 9999px;
      box-shadow: 0 3px 10px rgba(15, 118, 110, 0.24);
      letter-spacing: 0.02em;
    }

    #cardCatGuesthouses .cat-icon-container {
      background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%);
      border: 1.5px solid #5eead4;
      box-shadow: 0 6px 18px rgba(15, 118, 110, 0.1);
    }

    #cardCatGuesthouses:hover .cat-icon-container {
      background: linear-gradient(135deg, #99f6e4 0%, #5eead4 100%);
      transform: scale(1.03);
      border-color: #0f766e;
      box-shadow: 0 8px 22px rgba(15, 118, 110, 0.16);
    }

    #cardCatGuesthouses .cat-card-title {
      font-family: var(--font-serif);
      font-size: 1.4rem;
      font-weight: 800;
      color: #134e4a;
      margin-bottom: 0.3rem;
    }

    #cardCatGuesthouses .cat-card-desc {
      font-size: 0.84rem;
      color: #234e49;
      line-height: 1.45;
      margin-bottom: 0;
      flex-grow: 1;
    }

    .portal-cat-card .cat-card-header {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 1rem;
    }

    /* ==========================================================================
       QUICK STATUS CARD
       ========================================================================== */
    .portal-quick-status-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 243, 0.96) 100%);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1.5px solid rgba(162, 212, 180, 0.85);
      border-left: 5px solid #174d2f;
      border-radius: 16px;
      padding: 1.35rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      box-shadow: 0 14px 35px rgba(5, 20, 11, 0.18), 0 2px 6px rgba(0, 0, 0, 0.05);
      transition: all 0.25s ease;
    }

    .portal-quick-status-card:hover {
      box-shadow: 0 18px 42px rgba(5, 20, 11, 0.24);
    }

    .status-left {
      display: flex;
      align-items: center;
      gap: 1.15rem;
    }

    .status-icon-bubble {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      background: linear-gradient(135deg, #174d2f 0%, #226b42 100%);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 4px 12px rgba(23, 77, 47, 0.25);
    }

    .status-text h4 {
      font-size: 0.98rem;
      font-weight: 700;
      color: #0c331d;
      margin-bottom: 0.2rem;
    }

    .status-text p {
      font-size: 0.84rem;
      color: #4a6353;
    }

    .btn-view-bookings {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: linear-gradient(135deg, #174d2f 0%, #20633d 100%);
      color: #ffffff;
      font-size: 0.88rem;
      font-weight: 700;
      padding: 0.7rem 1.25rem;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(23, 77, 47, 0.2);
      border: 1px solid #174d2f;
    }

    .btn-view-bookings:hover {
      background: linear-gradient(135deg, #0e3520 0%, #174d2f 100%);
      color: #ffffff;
      box-shadow: 0 6px 16px rgba(23, 77, 47, 0.35);
      transform: translateY(-2px);
    }

    @media (max-width: 860px) {
      .portal-category-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .portal-category-wrapper {
        margin: 1rem auto 1.5rem;
      }

      .portal-category-intro-card {
        padding: 1.1rem 1.15rem 1rem;
        border-radius: 16px;
      }

      .cat-sec-title {
        font-size: 1.4rem;
      }

      .portal-cat-card {
        padding: 1.1rem 1.25rem;
        border-radius: 16px;
      }

      .portal-quick-status-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 1.25rem 1.5rem;
      }

      .btn-view-bookings {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .portal-category-wrapper {
        padding: 0 0.85rem;
      }

      .portal-category-intro-card {
        padding: 1rem 0.85rem;
      }

      .cat-sec-title {
        font-size: 1.25rem;
      }

      .cat-sec-desc {
        font-size: 0.82rem;
      }

      .portal-cat-card {
        padding: 1rem;
      }

      .cat-card-title {
        font-size: 1.25rem !important;
      }

      .status-left {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.65rem;
      }
    }
  </style>
</head>

<body class="booking-body homepage-body">

  <!-- ==========================================================================
       TOPBAR NAVIGATION (AUTHENTICATED)
       ========================================================================== -->
  <header class="booking-topbar">
    <div class="booking-topbar-container">
      <!-- Left: ATI Brand (Scrolls to Top of Portal) -->
      <a href="#top" class="booking-brand" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" title="Scroll back to top of portal">
        <img src="assets/images/ATI_Logo.png" alt="ATI Official Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility and Dormitory Reservation System</p>
        </div>
      </a>

      <!-- Center & Right: Navigation Actions -->
      <nav class="booking-nav-center">
        <!-- Home (Active) -->
        <a href="home.php" class="booking-nav-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Home</span>
        </a>

        <!-- New Reservation -->
        <a href="booking.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          <span>New Reservation</span>
        </a>

        <!-- My Reservations -->
        <a href="my_reservations.php" class="booking-nav-item" title="My Reservations">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count"><?= $currentUserResCount ?></span>
        </a>

        <!-- Master Schedule -->
        <a href="schedule.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span>Master Schedule</span>
        </a>

        <!-- User Profile Badge & Dropdown -->
        <div class="user-profile-menu">
          <div class="user-profile-badge" id="userProfileBadge" role="button" aria-haspopup="true">
            <div class="user-avatar-circle"><?= getUserInitials($_SESSION['full_name']) ?></div>
            <div class="user-details">
              <div class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
              <div class="user-role"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role']))) ?></div>
            </div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
              <div class="dropdown-user-email"><span class="user-verified-dot"></span> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role']))) ?></div>
            </div>
            <a href="profile.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="booking_history.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
              </svg>
              <span>Booking History</span>
            </a>
            <?php if (isAdmin()): ?>
            <a href="admin_dashboard.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
              <span>Admin Dashboard</span>
            </a>
            <?php endif; ?>
            <div style="height: 1px; background: #e5ede7; margin: 0.35rem 0;"></div>
            <a href="logout.php" class="dropdown-item danger">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>Sign Out</span>
            </a>
          </div>
        </div>
      </nav>

      <!-- Mobile Hamburger Menu Button -->
      <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileDrawer(true)"
        aria-label="Open Navigation Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <!-- ==========================================================================
       MOBILE NAVIGATION DRAWER OVERLAY
       ========================================================================== -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"
    onclick="if(event.target===this) toggleMobileDrawer(false);" style="display: none;">
    <div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">

      <!-- Drawer Header -->
      <div class="drawer-header">
        <div class="drawer-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="drawer-logo"
            style="width: 36px; height: 36px; object-fit: contain;">
          <div>
            <h4>ATI Portal</h4>
            <p>Central Office</p>
          </div>
        </div>
        <button type="button" class="drawer-close-btn" id="mobileDrawerClose" onclick="toggleMobileDrawer(false)"
          aria-label="Close Navigation Menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Drawer Body -->
      <div class="drawer-body">
        <!-- User Profile Card -->
        <div class="drawer-profile-card">
          <div class="drawer-avatar">JD</div>
          <div class="drawer-profile-info">
            <h5>Juan Dela Cruz</h5>
            <p>ATI Staff (CDD)</p>
            <span class="drawer-badge">Verified Personnel</span>
          </div>
        </div>

        <!-- Main Navigation Section -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">PORTAL NAVIGATION</div>

          <a href="home.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <span class="drawer-link-text">Home</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="booking.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">New Reservation</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">My Reservations</span>
            <span class="drawer-badge-count"><?= $currentUserResCount ?></span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="schedule.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <span class="drawer-link-text">Master Schedule</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>
        </div>

        <!-- Account Settings Section in Drawer -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">ACCOUNT & SETTINGS</div>

          <a href="profile.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
          </a>

          <?php if (isAdmin()): ?>
          <a href="admin_dashboard.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
            </div>
            <span class="drawer-link-text">Admin Dashboard</span>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Drawer Footer -->
      <div class="drawer-footer">
        <a href="logout.php" class="drawer-logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Sign Out</span>
        </a>
      </div>
    </div>
  </div>

  <main class="portal-category-wrapper">

    <!-- ==========================================================================
         CATEGORY INTRO CARD (Matching Design Reference)
         ========================================================================== -->
    <div class="portal-category-intro-card">
      <div class="portal-category-logo-wrap">
        <img src="assets/images/ATI_Logo.png" alt="Agricultural Training Institute Official Logo"
          class="portal-category-logo">
      </div>
      <h1 class="cat-sec-title">OUR FACILITIES</h1>
      <p class="cat-sec-subtitle">Choose a category below to open a dedicated view of available function halls, training
        rooms, dormitory accommodations, and private guesthouses.</p>
      <div class="cat-sec-divider"></div>
    </div>

    <!-- ==========================================================================
         THREE MAIN CATEGORY CARDS (Halls, Dormitories & Guesthouses)
         ========================================================================== -->
    <div class="portal-category-grid">

      <!-- 1. Halls Category Card -->
      <a href="booking.php?category=halls" class="portal-cat-card" id="cardCatHalls" title="Explore ATI Halls">
        <div class="cat-card-header">
          <span class="cat-count-badge"><?= $hallsCount ?> Venues Available</span>
        </div>
        <div class="cat-icon-container">
          <svg width="92" height="92" viewBox="0 0 64 64" fill="none" stroke="#174d2f" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- Ground & Grass -->
            <line x1="4" y1="54" x2="60" y2="54" stroke-width="2.2" />
            <path d="M6 54l2-3.5m1 3.5l1.5-2.5 M54 54l2-3.5m1 3.5l1.5-2.5" stroke-width="1.8" />
            <!-- Left Tree -->
            <path d="M11 54v-8" stroke-width="2" />
            <path d="M11 36c-2.5 0-4.5 1.8-4.5 4 0 1.2.6 2.3 1.5 3-1 1-1.2 2.5-.2 3.8.7.9 1.8 1.4 3.2 1.4 1.4 0 2.5-.5 3.2-1.4 1-1.3.8-2.8-.2-3.8.9-.7 1.5-1.8 1.5-3 0-2.2-2-4-4.5-4z" fill="#d7f0df" />
            <!-- Right Tree -->
            <path d="M53 54v-8" stroke-width="2" />
            <path d="M53 36c-2.5 0-4.5 1.8-4.5 4 0 1.2.6 2.3 1.5 3-1 1-1.2 2.5-.2 3.8.7.9 1.8 1.4 3.2 1.4 1.4 0 2.5-.5 3.2-1.4 1-1.3.8-2.8-.2-3.8.9-.7 1.5-1.8 1.5-3 0-2.2-2-4-4.5-4z" fill="#d7f0df" />
            <!-- Classical Hall Building -->
            <polygon points="32,20 18,28 46,28" fill="#d7f0df" />
            <circle cx="32" cy="25" r="1.5" fill="#174d2f" stroke="none" />
            <line x1="18" y1="28" x2="46" y2="28" />
            <!-- Columns -->
            <line x1="21.5" y1="28" x2="21.5" y2="48" stroke-width="2.4" />
            <line x1="28" y1="28" x2="28" y2="48" stroke-width="2.4" />
            <line x1="36" y1="28" x2="36" y2="48" stroke-width="2.4" />
            <line x1="42.5" y1="28" x2="42.5" y2="48" stroke-width="2.4" />
            <!-- Doorway -->
            <path d="M30 48v-6a2 2 0 0 1 4 0v6" fill="#c2ebd0" />
            <!-- Base Steps -->
            <rect x="17" y="48" width="30" height="3" fill="#d7f0df" />
            <rect x="15" y="51" width="34" height="3" fill="#d7f0df" />
          </svg>
        </div>
        <h2 class="cat-card-title">Halls</h2>
        <p class="cat-card-desc">Function auditoriums, executive meeting halls &amp; interactive training facilities.</p>
      </a>

      <!-- 2. Dormitories Category Card -->
      <a href="dormitory_booking.php" class="portal-cat-card" id="cardCatDormitories" title="Explore ATI Dormitories">
        <div class="cat-card-header">
          <span class="cat-count-badge"><?= $dormsCount ?> Room Types Available</span>
        </div>
        <div class="cat-icon-container">
          <svg width="92" height="92" viewBox="0 0 64 64" fill="none" stroke="#b45309" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- Ground & Grass -->
            <line x1="4" y1="54" x2="60" y2="54" stroke-width="2.2" />
            <path d="M6 54l2-3.5m1 3.5l1.5-2.5 M54 54l2-3.5m1 3.5l1.5-2.5" stroke-width="1.8" />
            <!-- Left Bush -->
            <path d="M8 54c0-2.5 1.8-4.5 4-4.5s4 2 4 4.5" fill="#fef3c7" />
            <!-- Dormitory Building Block -->
            <rect x="15" y="24" width="10" height="30" rx="1" fill="#fef3c7" />
            <rect x="25" y="16" width="16" height="38" rx="1.5" fill="#fef3c7" />
            <!-- Windows -->
            <rect x="18" y="28" width="4" height="4" rx="0.5" />
            <rect x="18" y="36" width="4" height="4" rx="0.5" />
            <rect x="28" y="21" width="4" height="4" rx="0.5" />
            <rect x="34" y="21" width="4" height="4" rx="0.5" />
            <rect x="28" y="29" width="4" height="4" rx="0.5" />
            <rect x="34" y="29" width="4" height="4" rx="0.5" />
            <rect x="28" y="37" width="4" height="4" rx="0.5" />
            <rect x="34" y="37" width="4" height="4" rx="0.5" />
            <!-- Entrance Door -->
            <rect x="29" y="46" width="6" height="8" rx="1" fill="#fde68a" />
            <!-- Right Tree -->
            <path d="M50 54v-9" stroke-width="2.2" />
            <path d="M50 27c-3 0-5.5 2-5.5 4.5 0 1 .4 2 1 2.8-1.5 1-2 2.7-1 4.2.8 1.2 2.2 1.8 3.5 1.8h4c2.5 0 4.5-1.8 4.5-4 0-1.5-.9-2.8-2.2-3.5.4-.6.7-1.4.7-2.3 0-2-2.2-3.5-5-3.5z" fill="#fef3c7" />
          </svg>
        </div>
        <h2 class="cat-card-title">Dormitories</h2>
        <p class="cat-card-desc">Executive VIP suites, shared trainee quarters, guest lecturer rooms &amp; twin
          accommodations.</p>
      </a>

      <!-- 3. Guesthouses Category Card -->
      <a href="booking.php?category=guesthouses" class="portal-cat-card" id="cardCatGuesthouses" title="Explore ATI Guesthouses">
        <div class="cat-card-header">
          <span class="cat-count-badge"><?= $guesthousesCount ?> Guesthouses Available</span>
        </div>
        <div class="cat-icon-container">
          <svg width="92" height="92" viewBox="0 0 64 64" fill="none" stroke="#0f766e" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- Ground & Grass -->
            <line x1="4" y1="54" x2="60" y2="54" stroke-width="2.2" />
            <path d="M6 54l2-3.5m1 3.5l1.5-2.5 M54 54l2-3.5m1 3.5l1.5-2.5" stroke-width="1.8" />
            <!-- Sun in sky -->
            <circle cx="13" cy="13" r="2.8" fill="#fef08a" />
            <line x1="13" y1="6.5" x2="13" y2="8" stroke-width="1.6" />
            <line x1="13" y1="18" x2="13" y2="19.5" stroke-width="1.6" />
            <line x1="6.5" y1="13" x2="8" y2="13" stroke-width="1.6" />
            <line x1="18" y1="13" x2="19.5" y2="13" stroke-width="1.6" />
            <line x1="8.5" y1="8.5" x2="9.6" y2="9.6" stroke-width="1.6" />
            <line x1="16.4" y1="16.4" x2="17.5" y2="17.5" stroke-width="1.6" />
            <line x1="8.5" y1="17.5" x2="9.6" y2="16.4" stroke-width="1.6" />
            <line x1="16.4" y1="9.6" x2="17.5" y2="8.5" stroke-width="1.6" />
            <!-- Chimney -->
            <path d="M14 28V20H18V24.5" fill="#ccfbf1" stroke-width="2.2" />
            <!-- Cottage Body & Roof -->
            <path d="M12 33v21h22V33" fill="#ccfbf1" />
            <path d="M8 34L23 21L38 34" stroke-width="2.4" />
            <!-- Round Attic Window with Cross -->
            <circle cx="23" cy="30" r="3.2" />
            <line x1="19.8" y1="30" x2="26.2" y2="30" stroke-width="1.5" />
            <line x1="23" y1="26.8" x2="23" y2="33.2" stroke-width="1.5" />
            <!-- Cottage Door -->
            <rect x="19.5" y="41" width="7" height="13" rx="1" fill="#99f6e4" />
            <circle cx="24.8" cy="48" r="0.7" fill="#0f766e" stroke="none" />
            <!-- Lush Tree beside Cottage -->
            <path d="M44 54c0-4 1.2-8.5 1.2-12" stroke-width="2.2" />
            <path d="M48.5 54c0-4-1.2-8.5-1.2-12" stroke-width="2.2" />
            <path d="M47.5 45c2.5-1.5 4.5 0 4.5 0" stroke-width="1.6" />
            <path d="M41 38c-3-1-4.2-3.3-3.8-6 .5-2.8 2.6-4.6 5-4.4.8-3.2 4-5.2 7.2-4.5 3 .6 5 3.2 5 6.2 2.4.5 4.2 2.3 4.2 4.8 0 2.8-2.3 5-5.2 5h-4" fill="#ccfbf1" stroke-width="2.2" />
          </svg>
        </div>
        <h2 class="cat-card-title">Guesthouses</h2>
        <p class="cat-card-desc">Private guest villas, executive staff quarters &amp; resource cottages with home comforts.</p>
      </a>

    </div>

  </main>

  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;
      if (open) {
        overlay.style.display = 'block';
        requestAnimationFrame(function () {
          overlay.classList.add('show');
        });
        document.body.style.overflow = 'hidden';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        setTimeout(function () {
          if (!overlay.classList.contains('show')) overlay.style.display = 'none';
        }, 280);
      }
    }

    // Profile Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function () {
      var profileBadge = document.getElementById('userProfileBadge');
      var profileDropdown = document.getElementById('profileDropdown');

      // Sync Avatar & Profile from localStorage
      var avatar = localStorage.getItem('ati_user_avatar');
      var profile = localStorage.getItem('ati_user_profile');
      if (avatar) {
        document.querySelectorAll('.user-avatar-circle, .drawer-avatar').forEach(function (c) {
          c.innerHTML = '<img src="' + avatar + '" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">';
        });
      }
      if (profile) {
        try {
          var p = JSON.parse(profile);
          if (p.fullName) {
            document.querySelectorAll('.user-name, .dropdown-user-name, .drawer-profile-info h5').forEach(function (el) {
              el.textContent = p.fullName;
            });
          }
        } catch (e) { }
      }

      if (profileBadge && profileDropdown) {
        profileBadge.addEventListener('click', function (e) {
          e.stopPropagation();
          profileDropdown.classList.toggle('show');
          profileDropdown.classList.toggle('active');
        });

        document.addEventListener('click', function (e) {
          if (!profileBadge.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show');
            profileDropdown.classList.remove('active');
          }
        });
      }
    });
  </script>
</body>

</html>