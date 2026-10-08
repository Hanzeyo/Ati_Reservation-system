<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Authenticated Portal Home: Facility & Dormitory Category Selection Hub
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Facilities - Agricultural Training Institute</title>
  <meta name="description"
    content="Choose between function halls and dormitory accommodations to start your reservation.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;0,900;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
  <style>
    /* ==========================================================================
       HOMEPAGE ATMOSPHERE & BACKGROUND (CLEAN, WARM, NATURAL AESTHETIC)
       ========================================================================== */
    body.booking-body.homepage-body {
      background:
        radial-gradient(circle at 50% 0%, rgba(220, 242, 228, 0.65) 0%, rgba(244, 247, 244, 0.95) 70%),
        #f4f7f4;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      color: #1a2e22;
    }

    /* ---------------- TOPBAR NAVIGATION (FOREST GREEN THEME) ---------------- */
    .homepage-topbar {
      background: linear-gradient(135deg, #0d381e 0%, #114525 100%) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
      box-shadow: 0 4px 20px rgba(7, 27, 15, 0.28) !important;
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .homepage-topbar .booking-topbar-container {
      max-width: 1440px;
      margin: 0 auto;
      padding: 0.65rem 1.75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .homepage-topbar .booking-brand {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      text-decoration: none;
    }

    .homepage-topbar .booking-brand-logo {
      height: 44px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.25));
    }

    .homepage-topbar .booking-brand-text h1 {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 1.12rem;
      font-weight: 700;
      color: #ffffff !important;
      line-height: 1.2;
      margin: 0;
      letter-spacing: 0.01em;
    }

    .homepage-topbar .booking-brand-text p {
      font-size: 0.74rem;
      color: #a3d9b5 !important;
      font-weight: 500;
      margin: 0.1rem 0 0 0;
      letter-spacing: 0.02em;
    }

    /* Topbar Navigation Actions */
    .homepage-topbar .booking-nav-center {
      display: flex;
      align-items: center;
      gap: 0.45rem;
    }

    .homepage-topbar .booking-nav-item {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      font-size: 0.86rem;
      font-weight: 600;
      color: #e3f2e7 !important;
      text-decoration: none;
      border-radius: 9999px;
      transition: all 0.22s ease;
      white-space: nowrap;
    }

    .homepage-topbar .booking-nav-item svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      flex-shrink: 0;
      transition: transform 0.2s ease;
    }

    .homepage-topbar .booking-nav-item:hover {
      background: rgba(255, 255, 255, 0.12);
      color: #ffffff !important;
      transform: translateY(-1px);
    }

    .homepage-topbar .booking-nav-item:hover svg {
      transform: scale(1.1);
    }

    /* Active Nav Item: Distinct Pill Highlight */
    .homepage-topbar .booking-nav-item.active {
      background: rgba(255, 255, 255, 0.18) !important;
      color: #ffffff !important;
      font-weight: 700;
      border: 1px solid rgba(255, 255, 255, 0.22);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }

    .homepage-topbar .nav-badge-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 0.12rem 0.45rem;
      border-radius: 9999px;
      line-height: 1;
      margin-left: 0.15rem;
    }

    .homepage-topbar .nav-badge-count.blue {
      background: rgba(56, 189, 248, 0.35);
      color: #e0f2fe;
    }

    /* Role / User Profile Pill Button matching Image 1 */
    .role-user-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: rgba(0, 0, 0, 0.28);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 9999px;
      padding: 0.42rem 0.95rem 0.42rem 0.45rem;
      color: #ffffff;
      cursor: pointer;
      transition: all 0.22s ease;
      user-select: none;
    }

    .role-user-badge:hover {
      background: rgba(0, 0, 0, 0.4);
      border-color: rgba(255, 255, 255, 0.45);
      transform: translateY(-1px);
    }

    .role-badge-icon {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
      color: #0c331d;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 800;
      box-shadow: 0 2px 6px rgba(234, 179, 8, 0.35);
      flex-shrink: 0;
    }

    .role-badge-text {
      font-size: 0.82rem;
      font-weight: 500;
      color: #d6ede0;
      white-space: nowrap;
    }

    .role-badge-text strong {
      color: #ffffff;
      font-weight: 700;
    }

    .role-chevron {
      stroke: #d6ede0;
      transition: transform 0.2s ease;
      flex-shrink: 0;
    }

    .role-user-badge[aria-expanded="true"] .role-chevron {
      transform: rotate(180deg);
    }

    /* ==========================================================================
       MAIN PORTAL CONTAINER
       ========================================================================== */
    .portal-category-wrapper {
      max-width: 1040px;
      width: 100%;
      margin: 2.85rem auto 4.5rem;
      padding: 0 1.5rem;
      box-sizing: border-box;
      position: relative;
      z-index: 2;
    }

    /* ==========================================================================
       CATEGORY INTRO CARD (EXACT ELEVATED REPRODUCTION OF IMAGE 1)
       ========================================================================== */
    .portal-category-intro-card {
      background: #ffffff;
      border: 1px solid #dce8df;
      border-radius: 24px;
      padding: 3.25rem 2.5rem 2.85rem;
      text-align: center;
      box-shadow: 0 10px 30px rgba(13, 56, 30, 0.05), 0 2px 6px rgba(0, 0, 0, 0.02);
      margin-bottom: 2.25rem;
      position: relative;
      overflow: hidden;
    }

    .cat-sec-badge-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 1.25rem;
    }

    .cat-sec-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
      font-size: 0.76rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 0.45rem 1.25rem;
      border-radius: 9999px;
      box-shadow: 0 2px 6px rgba(22, 101, 52, 0.08);
    }

    .cat-sec-badge svg {
      stroke: #166534;
      width: 15px;
      height: 15px;
    }

    .cat-sec-title {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 2.75rem;
      font-weight: 900;
      letter-spacing: 0.03em;
      color: #0c331d;
      margin: 0 0 0.85rem 0;
      text-transform: uppercase;
      line-height: 1.15;
    }

    .cat-sec-subtitle {
      font-size: 1.02rem;
      color: #4b6354;
      max-width: 660px;
      margin: 0 auto;
      line-height: 1.65;
    }

    /* Centered Gold Underline Divider */
    .cat-sec-divider {
      width: 72px;
      height: 4px;
      background: #eab308;
      border-radius: 9999px;
      margin: 1.6rem auto 0;
      box-shadow: 0 2px 8px rgba(234, 179, 8, 0.35);
    }

    /* ==========================================================================
       TWO MAIN CATEGORY CARDS (HALLS & DORMITORIES)
       ========================================================================== */
    .portal-category-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 2rem;
      margin-bottom: 2.25rem;
    }

    .portal-cat-card {
      background: #ffffff;
      border: 1.5px solid #dce8df;
      border-radius: 22px;
      padding: 2.25rem 2.25rem 2.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      text-decoration: none;
      color: inherit;
      position: relative;
      cursor: pointer;
      box-shadow: 0 10px 30px rgba(13, 56, 30, 0.05), 0 2px 6px rgba(0, 0, 0, 0.02);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .portal-cat-card:hover {
      transform: translateY(-8px);
      border-color: #1b633a;
      box-shadow: 0 24px 55px rgba(13, 56, 30, 0.12), 0 0 0 2px rgba(27, 99, 58, 0.15);
    }

    /* Top-Right Badge */
    .cat-card-header {
      width: 100%;
      display: flex;
      justify-content: flex-end;
      margin-bottom: 0.5rem;
    }

    .cat-count-badge {
      display: inline-flex;
      align-items: center;
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
      font-size: 0.76rem;
      font-weight: 700;
      padding: 0.35rem 0.95rem;
      border-radius: 9999px;
      letter-spacing: 0.02em;
      box-shadow: 0 2px 6px rgba(22, 101, 52, 0.06);
    }

    /* Large Center Architectural Icon */
    .cat-icon-container {
      width: 130px;
      height: 130px;
      background: #f2f8f4;
      border: 1.5px solid #d5ebd9;
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0.75rem auto 1.5rem;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .portal-cat-card:hover .cat-icon-container {
      background: #e6f6ec;
      border-color: #174d2f;
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(23, 77, 47, 0.12);
    }

    .cat-icon-container svg {
      width: 82px;
      height: 82px;
      display: block;
    }

    /* Card Title */
    .cat-card-title {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 1.95rem;
      font-weight: 800;
      color: #0c331d;
      margin: 0 0 0.65rem 0;
      line-height: 1.2;
    }

    /* Card Description */
    .cat-card-desc {
      font-size: 0.94rem;
      color: #4b6354;
      line-height: 1.65;
      max-width: 380px;
      margin: 0 auto 1.85rem;
      flex-grow: 1;
    }

    /* Dark Pill Action Button matching Image 1 */
    .btn-explore-category {
      background: #111827;
      color: #ffffff;
      font-size: 0.95rem;
      font-weight: 700;
      padding: 0.85rem 2.25rem;
      border-radius: 9999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      width: 100%;
      max-width: 310px;
      margin: 0 auto;
      text-decoration: none;
      transition: all 0.24s ease;
      box-shadow: 0 4px 14px rgba(17, 24, 39, 0.2);
      border: 1px solid #111827;
    }

    .btn-explore-category svg {
      transition: transform 0.22s ease;
      stroke: #ffffff;
      flex-shrink: 0;
    }

    .portal-cat-card:hover .btn-explore-category {
      background: #174d2f;
      border-color: #174d2f;
      box-shadow: 0 8px 24px rgba(23, 77, 47, 0.35);
      transform: translateY(-2px);
    }

    .portal-cat-card:hover .btn-explore-category svg {
      transform: translateX(4px);
    }

    /* ==========================================================================
       USER QUICK RESERVATIONS & BOOKINGS STATUS BAR
       ========================================================================== */
    .portal-quick-status-card {
      background: #ffffff;
      border-radius: 20px;
      border: 1.5px solid #dce8df;
      border-left: 5px solid #174d2f;
      padding: 1.5rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      box-shadow: 0 10px 30px rgba(13, 56, 30, 0.05), 0 2px 6px rgba(0, 0, 0, 0.02);
      transition: all 0.25s ease;
    }

    .portal-quick-status-card:hover {
      box-shadow: 0 14px 38px rgba(13, 56, 30, 0.09);
    }

    .status-left {
      display: flex;
      align-items: center;
      gap: 1.15rem;
      max-width: 620px;
    }

    .status-icon-bubble {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, #174d2f 0%, #206b42 100%);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(23, 77, 47, 0.25);
    }

    .status-text h4 {
      font-size: 1rem;
      font-weight: 800;
      color: #0c331d;
      margin: 0 0 0.25rem 0;
    }

    .status-text p {
      font-size: 0.86rem;
      color: #4b6354;
      line-height: 1.55;
      margin: 0;
    }

    .status-actions {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .btn-view-bookings {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      background: #174d2f;
      color: #ffffff;
      font-size: 0.88rem;
      font-weight: 700;
      padding: 0.72rem 1.35rem;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(23, 77, 47, 0.2);
      border: 1px solid #174d2f;
    }

    .btn-view-bookings:hover {
      background: #0f331f;
      box-shadow: 0 6px 16px rgba(23, 77, 47, 0.35);
      transform: translateY(-2px);
      color: #ffffff;
    }

    .btn-view-bookings.gold {
      background: #d97706;
      border-color: #d97706;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.22);
    }

    .btn-view-bookings.gold:hover {
      background: #b45309;
      box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
    }

    /* ==========================================================================
       RESPONSIVE ADAPTATIONS
       ========================================================================== */
    @media (max-width: 960px) {
      .homepage-topbar .booking-topbar-container {
        padding: 0.6rem 1.25rem;
      }
      .portal-category-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      .portal-category-wrapper {
        margin: 1.75rem auto 3rem;
      }
      .portal-category-intro-card {
        padding: 2.25rem 1.5rem 2rem;
        border-radius: 20px;
      }
      .cat-sec-title {
        font-size: 2.1rem;
      }
      .portal-cat-card {
        padding: 2rem 1.5rem;
        border-radius: 20px;
      }
      .portal-quick-status-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 1.35rem 1.5rem;
      }
      .status-actions {
        width: 100%;
        flex-direction: column;
      }
      .btn-view-bookings {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>

<body class="booking-body homepage-body">

  <!-- ==========================================================================
       TOPBAR NAVIGATION (AUTHENTICATED - FOREST GREEN THEME)
       ========================================================================== -->
  <header class="booking-topbar homepage-topbar">
    <div class="booking-topbar-container">
      <!-- Left: Official ATI Brand -->
      <a href="home.php" class="booking-brand" title="Agricultural Training Institute Reservation Portal">
        <img src="assets/images/ATI_Logo.png" alt="ATI Official Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility &amp; Accommodation Reservation System</p>
        </div>
      </a>

      <!-- Center & Right: Navigation Actions (Matching Image 1) -->
      <nav class="booking-nav-center">
        <!-- 1. Our Facilities (Home Active Tab) -->
        <a href="home.php" class="booking-nav-item active" title="Our Facilities - Category Hub">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Our Facilities</span>
        </a>

        <!-- 2. Reserve Venue (Links to Facility Reservation) -->
        <a href="booking.php" class="booking-nav-item" title="Reserve Venue (Function Halls & Rooms)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
            <line x1="12" y1="14" x2="12" y2="18"></line>
            <line x1="10" y1="16" x2="14" y2="16"></line>
          </svg>
          <span>Reserve Venue</span>
        </a>

        <!-- 3. Master Schedule -->
        <a href="schedule.php" class="booking-nav-item" title="Master Schedule & Public Availability">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span>Master Schedule</span>
        </a>

        <!-- 4. My Bookings (Facilities & Venues) -->
        <a href="my_reservations.php" class="booking-nav-item" title="My Bookings (Facilities & Accommodations)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
          </svg>
          <span>My Bookings</span>
          <span class="nav-badge-count">4</span>
        </a>

        <!-- 5. Admin Portal -->
        <a href="admin_dashboard.php" class="booking-nav-item" title="Admin Portal & Officer Console">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
          <span>Admin Portal</span>
        </a>

        <!-- 6. Role & User Profile Dropdown Pill (Matching Image 1) -->
        <div class="user-profile-menu" style="position: relative;">
          <div class="role-user-badge" id="userProfileBadge" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0" title="User Account & Role Settings">
            <span class="role-badge-icon">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </span>
            <span class="role-badge-text">Role: <strong id="topbarRoleLabel">Government / Public User</strong></span>
            <svg class="role-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>

          <!-- Dropdown Menu -->
          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-avatar-circle">JD</div>
              <div class="dropdown-user-meta">
                <div class="dropdown-user-name">Juan Dela Cruz</div>
                <div class="dropdown-user-email">juan.delacruz@ati.da.gov.ph</div>
              </div>
            </div>

            <a href="profile.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>

            <a href="my_reservations.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
              <span>Facility Bookings (4)</span>
            </a>

            <a href="booking_history.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
                <path d="M6 8v9"></path>
              </svg>
              <span>Dormitory Reservations (2)</span>
            </a>

            <a href="admin_dashboard.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
              <span>Admin Dashboard</span>
            </a>

            <a href="javascript:void(0)" onclick="openUserSettingsModal(); var pdd = document.getElementById('profileDropdown'); if(pdd){pdd.classList.remove('show','active');} return false;" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
              <span>Account Settings</span>
            </a>

            <div class="dropdown-divider"></div>

            <a href="index.php" class="dropdown-item danger">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>Sign Out</span>
            </a>
          </div>
        </div>
      </nav>

      <!-- Upper Left Mobile Hamburger Menu Button -->
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
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
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
            <p>Government / Public User</p>
            <span class="drawer-badge">Verified Account</span>
          </div>
        </div>

        <!-- Quick Reserve Actions in Drawer -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">FACILITY CATEGORIES</div>
          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;">
            <a href="booking.php" class="drawer-nav-link" style="background: linear-gradient(135deg, #174d2f 0%, #226b42 100%); color: #ffffff; font-weight: 800; border-radius: 10px; padding: 0.75rem 0.95rem; box-shadow: 0 4px 12px rgba(23,77,47,0.25);">
              <div class="drawer-link-icon" style="color: #ffffff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <span class="drawer-link-text">Explore Halls &amp; Venues</span>
              <span style="font-size: 0.68rem; background: rgba(255,255,255,0.22); padding: 0.15rem 0.45rem; border-radius: 999px;">4 Venues</span>
            </a>

            <a href="dormitory_booking.php" class="drawer-nav-link" style="background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%); color: #ffffff; font-weight: 800; border-radius: 10px; padding: 0.75rem 0.95rem; box-shadow: 0 4px 12px rgba(2,132,199,0.25);">
              <div class="drawer-link-icon" style="color: #ffffff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 4v16"></path><path d="M2 8h18a2 2 0 0 1 2 2v10"></path><path d="M2 17h20"></path></svg>
              </div>
              <span class="drawer-link-text">Explore Dormitories</span>
              <span style="font-size: 0.68rem; background: rgba(255,255,255,0.22); padding: 0.15rem 0.45rem; border-radius: 999px;">4 Types</span>
            </a>
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
            <span class="drawer-link-text">Our Facilities</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="booking.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <span class="drawer-link-text">Reserve Venue</span>
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

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">My Bookings</span>
            <span class="drawer-badge-count">4</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="booking_history.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
              </svg>
            </div>
            <span class="drawer-link-text">Dormitory Reservations</span>
            <span class="drawer-badge-count blue">2</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>
        </div>

        <!-- Account Settings Section in Drawer -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">ACCOUNT &amp; SETTINGS</div>

          <a href="profile.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
          </a>

          <a href="javascript:void(0)" onclick="toggleMobileDrawer(false); openUserSettingsModal();" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </div>
            <span class="drawer-link-text">Account Settings</span>
          </a>

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
        </div>
      </div>

      <!-- Drawer Footer -->
      <div class="drawer-footer">
        <a href="index.php" class="drawer-logout-btn">
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

  <!-- ==========================================================================
       MAIN VIEWPORT: OUR FACILITIES SHOWCASE (MATCHING IMAGE 1)
       ========================================================================== -->
  <main class="portal-category-wrapper">
    
    <!-- 1. CATEGORY INTRO CARD -->
    <div class="portal-category-intro-card">
      <div class="cat-sec-badge-wrapper">
        <span class="cat-sec-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M12 3l9 7H3l9-7z"></path>
          </svg>
          Agriculture &amp; Training Venues
        </span>
      </div>
      <h1 class="cat-sec-title">OUR FACILITIES</h1>
      <p class="cat-sec-subtitle">Choose a category below to open a dedicated view of available function halls, training rooms, and dormitory accommodations.</p>
      <div class="cat-sec-divider"></div>
    </div>

    <!-- 2. TWO MAIN CATEGORY CARDS (HALLS & DORMITORIES) -->
    <div class="portal-category-grid">
      
      <!-- Card 1: Halls Category -->
      <a href="booking.php" class="portal-cat-card" id="cardCatHalls" title="Explore ATI Function Halls & Venues">
        <div class="cat-card-header">
          <span class="cat-count-badge">4 Venues Available</span>
        </div>
        <div class="cat-icon-container">
          <svg viewBox="0 0 72 72" fill="none" stroke="#174d2f" stroke-linecap="round" stroke-linejoin="round">
            <!-- Pediment roof with circular emblem -->
            <path d="M12 26L36 10L60 26H12Z" stroke-width="2.6" fill="rgba(215, 240, 223, 0.45)"/>
            <circle cx="36" cy="19" r="2.6" stroke-width="2.2"/>
            <!-- Horizontal entablature beam -->
            <line x1="8" y1="26" x2="64" y2="26" stroke-width="2.8"/>
            <!-- Left classical pillar -->
            <rect x="17" y="30" width="7" height="24" rx="1.5" stroke-width="2.4" fill="rgba(215, 240, 223, 0.35)"/>
            <!-- Center classical arched opening -->
            <path d="M30 54V38C30 34.7 32.7 32 36 32C39.3 32 42 34.7 42 38V54" stroke-width="2.6" fill="rgba(215, 240, 223, 0.25)"/>
            <!-- Right classical pillar -->
            <rect x="48" y="30" width="7" height="24" rx="1.5" stroke-width="2.4" fill="rgba(215, 240, 223, 0.35)"/>
            <!-- Stepped podium foundation -->
            <line x1="12" y1="54" x2="60" y2="54" stroke-width="2.8"/>
            <line x1="7" y1="58" x2="65" y2="58" stroke-width="2.8"/>
          </svg>
        </div>
        <h2 class="cat-card-title">Halls</h2>
        <p class="cat-card-desc">Function auditoriums, audio-visual training rooms, executive boardrooms &amp; mess dining facilities.</p>
        <div class="btn-explore-category">
          <span>Explore Halls</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </a>

      <!-- Card 2: Dormitories Category -->
      <a href="dormitory_booking.php" class="portal-cat-card" id="cardCatDormitories" title="Explore ATI Dormitory Accommodations">
        <div class="cat-card-header">
          <span class="cat-count-badge">4 Room Types</span>
        </div>
        <div class="cat-icon-container">
          <svg viewBox="0 0 72 72" fill="none" stroke="#174d2f" stroke-linecap="round" stroke-linejoin="round">
            <!-- Center tall accommodation tower -->
            <rect x="26" y="12" width="20" height="46" rx="2" stroke-width="2.5" fill="rgba(215, 240, 223, 0.4)"/>
            <!-- Left accommodation wing -->
            <rect x="11" y="24" width="15" height="34" rx="2" stroke-width="2.5" fill="rgba(215, 240, 223, 0.3)"/>
            <!-- Right accommodation wing -->
            <rect x="46" y="24" width="15" height="34" rx="2" stroke-width="2.5" fill="rgba(215, 240, 223, 0.3)"/>
            <!-- Left wing windows -->
            <rect x="15.5" y="30" width="6" height="6" rx="1" stroke-width="2"/>
            <rect x="15.5" y="42" width="6" height="6" rx="1" stroke-width="2"/>
            <!-- Right wing windows -->
            <rect x="50.5" y="30" width="6" height="6" rx="1" stroke-width="2"/>
            <rect x="50.5" y="42" width="6" height="6" rx="1" stroke-width="2"/>
            <!-- Center tower windows and entrance -->
            <rect x="32" y="18" width="8" height="6" rx="1" stroke-width="2"/>
            <rect x="32" y="30" width="8" height="6" rx="1" stroke-width="2"/>
            <rect x="32" y="44" width="8" height="14" rx="1.5" stroke-width="2.4" fill="rgba(23, 77, 47, 0.15)"/>
            <!-- Ground foundation line -->
            <line x1="6" y1="58" x2="66" y2="58" stroke-width="2.8"/>
          </svg>
        </div>
        <h2 class="cat-card-title">Dormitories</h2>
        <p class="cat-card-desc">Executive VIP suites, shared trainee quarters, guest lecturer rooms &amp; twin accommodations.</p>
        <div class="btn-explore-category">
          <span>Explore Dormitories</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </a>

    </div>

    <!-- 3. USER RESERVATION QUICK STATUS -->
    <div class="portal-quick-status-card">
      <div class="status-left">
        <div class="status-icon-bubble">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <div class="status-text">
          <h4>Looking for your active bookings &amp; reservations?</h4>
          <p>You have <strong>4</strong> active facility bookings and <strong>2</strong> dormitory reservations on file. Track verification, schedules, and approval status anytime.</p>
        </div>
      </div>
      <div class="status-actions">
        <a href="my_reservations.php" class="btn-view-bookings" title="View Your Facility Bookings">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
          </svg>
          <span>My Bookings (4)</span>
        </a>
        <a href="booking_history.php" class="btn-view-bookings gold" title="View Your Dormitory Reservations">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
          </svg>
          <span>Dorm Reservations (2)</span>
        </a>
      </div>
    </div>

  </main>

  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;
      if (open) {
        overlay.style.display = 'block';
        requestAnimationFrame(function() {
          overlay.classList.add('show');
        });
        document.body.style.overflow = 'hidden';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        setTimeout(function() {
          if (!overlay.classList.contains('show')) overlay.style.display = 'none';
        }, 280);
      }
    }

    // Profile Dropdown Toggle & Sync
    document.addEventListener('DOMContentLoaded', function() {
      var profileBadge = document.getElementById('userProfileBadge');
      var profileDropdown = document.getElementById('profileDropdown');

      // Sync Avatar & Profile from localStorage
      var avatar = localStorage.getItem('ati_user_avatar');
      var profile = localStorage.getItem('ati_user_profile');
      if (avatar) {
        document.querySelectorAll('.dropdown-avatar-circle, .drawer-avatar').forEach(function(c) {
          c.innerHTML = '<img src="' + avatar + '" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">';
        });
      }
      if (profile) {
        try {
          var p = JSON.parse(profile);
          if (p.fullName) {
            document.querySelectorAll('.dropdown-user-name, .drawer-profile-info h5').forEach(function(el) {
              el.textContent = p.fullName;
            });
          }
          if (p.roleTitle) {
            var roleLabel = document.getElementById('topbarRoleLabel');
            if (roleLabel) roleLabel.textContent = p.roleTitle;
          }
        } catch(e) {}
      }

      if (profileBadge && profileDropdown) {
        profileBadge.addEventListener('click', function(e) {
          e.stopPropagation();
          var isOpen = profileDropdown.classList.contains('show');
          if (isOpen) {
            profileDropdown.classList.remove('show', 'active');
            profileBadge.setAttribute('aria-expanded', 'false');
          } else {
            profileDropdown.classList.add('show', 'active');
            profileBadge.setAttribute('aria-expanded', 'true');
          }
        });

        document.addEventListener('click', function(e) {
          if (!profileBadge.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show', 'active');
            profileBadge.setAttribute('aria-expanded', 'false');
          }
        });
      }
    });
  </script>
  <script src="js/user_settings.js?v=<?php echo time(); ?>"></script>
</body>

</html>
