<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Authenticated Portal Home: Facility & Dormitory Category Selection
 */
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
      max-width: 1060px;
      margin: 2.75rem auto 4.5rem;
      padding: 0 1.5rem;
      position: relative;
      z-index: 2;
    }

    /* ==========================================================================
       CATEGORY INTRO CARD (RICH SYSTEM AESTHETIC & ELEVATION)
       ========================================================================== */
    .portal-category-intro-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 251, 246, 0.96) 100%);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1.5px solid rgba(162, 215, 180, 0.85);
      border-top: 5px solid #174d2f;
      border-radius: 22px;
      padding: 3.25rem 2.25rem 2.75rem;
      text-align: center;
      margin-bottom: 2.25rem;
      box-shadow: 0 18px 45px rgba(5, 20, 11, 0.22), 0 2px 8px rgba(0, 0, 0, 0.06);
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
      box-shadow: 0 22px 55px rgba(5, 20, 11, 0.28), 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .portal-category-logo-wrap {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 1.25rem;
    }

    .portal-category-logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      filter: drop-shadow(0 6px 16px rgba(23, 77, 47, 0.2));
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .portal-category-logo:hover {
      transform: scale(1.08);
    }

    .cat-sec-title {
      font-family: var(--font-serif);
      font-size: 2.35rem;
      font-weight: 900;
      letter-spacing: 0.03em;
      color: #0c331d;
      margin-bottom: 0.75rem;
      text-transform: uppercase;
    }

    .cat-sec-subtitle {
      font-size: 0.98rem;
      color: #3e5948;
      max-width: 680px;
      margin: 0 auto;
      line-height: 1.65;
    }

    .cat-sec-divider {
      width: 72px;
      height: 4px;
      background: linear-gradient(90deg, #174d2f 0%, #eab308 50%, #174d2f 100%);
      border-radius: 4px;
      margin: 1.4rem auto 0;
      box-shadow: 0 2px 6px rgba(234, 179, 8, 0.35);
    }

    /* ==========================================================================
       TWO MAIN CATEGORY CARDS (HALLS & DORMITORIES)
       ========================================================================== */
    .portal-category-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 2rem;
      margin-bottom: 2.5rem;
    }

    .portal-cat-card {
      border-radius: 22px;
      padding: 2.5rem 2.25rem;
      display: flex;
      flex-direction: column;
      text-decoration: none;
      color: inherit;
      transition: all 0.32s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      cursor: pointer;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }

    /* ---------------- 1. HALLS CARD (FOREST EMERALD THEME) ---------------- */
    #cardCatHalls {
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 250, 244, 0.97) 50%, rgba(230, 247, 236, 0.96) 100%);
      border: 2px solid rgba(152, 212, 174, 0.9);
      border-top: 5px solid #174d2f;
      box-shadow: 0 16px 40px rgba(7, 35, 18, 0.2), 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #cardCatHalls:hover {
      border-color: #174d2f;
      background: linear-gradient(165deg, #ffffff 0%, #ecf8f0 100%);
      transform: translateY(-8px);
      box-shadow: 0 24px 55px rgba(23, 77, 47, 0.32), 0 0 0 2px rgba(23, 77, 47, 0.4);
    }

    #cardCatHalls .cat-count-badge {
      display: inline-flex;
      align-items: center;
      background: linear-gradient(135deg, #174d2f 0%, #206740 100%);
      color: #ffffff;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 0.35rem 0.95rem;
      border-radius: 9999px;
      box-shadow: 0 4px 12px rgba(23, 77, 47, 0.28);
      letter-spacing: 0.02em;
    }

    #cardCatHalls .cat-icon-container {
      background: linear-gradient(135deg, #e4f5ea 0%, #ccecd7 100%);
      border-radius: 18px;
      padding: 1.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      border: 1.5px solid #a4dcba;
      box-shadow: 0 8px 22px rgba(23, 77, 47, 0.1);
    }

    #cardCatHalls:hover .cat-icon-container {
      background: linear-gradient(135deg, #d8f2e1 0%, #bee7cb 100%);
      transform: scale(1.03);
      border-color: #174d2f;
      box-shadow: 0 10px 26px rgba(23, 77, 47, 0.18);
    }

    #cardCatHalls .cat-card-title {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      font-weight: 800;
      color: #0c331d;
      margin-bottom: 0.5rem;
    }

    #cardCatHalls .cat-card-desc {
      font-size: 0.95rem;
      color: #3b5745;
      line-height: 1.6;
      margin-bottom: 1.75rem;
      flex-grow: 1;
    }

    #cardCatHalls .btn-explore-category {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: linear-gradient(135deg, #174d2f 0%, #206740 100%);
      color: #ffffff;
      font-size: 0.92rem;
      font-weight: 700;
      padding: 0.9rem 1.4rem;
      border-radius: 12px;
      text-decoration: none;
      transition: all 0.25s ease;
      box-shadow: 0 6px 18px rgba(23, 77, 47, 0.28);
      border: 1px solid #174d2f;
      margin-top: auto;
    }

    #cardCatHalls:hover .btn-explore-category {
      background: linear-gradient(135deg, #0e3520 0%, #174d2f 100%);
      box-shadow: 0 10px 24px rgba(23, 77, 47, 0.4);
    }

    #cardCatHalls .btn-explore-category svg {
      transition: transform 0.22s ease;
    }

    #cardCatHalls:hover .btn-explore-category svg {
      transform: translateX(5px);
    }

    /* ---------------- 2. DORMITORIES CARD (HARVEST GOLD THEME) ---------------- */
    #cardCatDormitories {
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 250, 241, 0.97) 50%, rgba(254, 245, 230, 0.96) 100%);
      border: 2px solid rgba(245, 215, 150, 0.9);
      border-top: 5px solid #d97706;
      box-shadow: 0 16px 40px rgba(50, 25, 5, 0.18), 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #cardCatDormitories:hover {
      border-color: #d97706;
      background: linear-gradient(165deg, #ffffff 0%, #fef7ec 100%);
      transform: translateY(-8px);
      box-shadow: 0 24px 55px rgba(217, 119, 6, 0.3), 0 0 0 2px rgba(217, 119, 6, 0.4);
    }

    #cardCatDormitories .cat-count-badge {
      display: inline-flex;
      align-items: center;
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      color: #ffffff;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 0.35rem 0.95rem;
      border-radius: 9999px;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28);
      letter-spacing: 0.02em;
    }

    #cardCatDormitories .cat-icon-container {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-radius: 18px;
      padding: 1.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      border: 1.5px solid #fcd34d;
      box-shadow: 0 8px 22px rgba(217, 119, 6, 0.12);
    }

    #cardCatDormitories:hover .cat-icon-container {
      background: linear-gradient(135deg, #fde68a 0%, #fcd34d 100%);
      transform: scale(1.03);
      border-color: #d97706;
      box-shadow: 0 10px 26px rgba(217, 119, 6, 0.2);
    }

    #cardCatDormitories .cat-card-title {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      font-weight: 800;
      color: #451a03;
      margin-bottom: 0.5rem;
    }

    #cardCatDormitories .cat-card-desc {
      font-size: 0.95rem;
      color: #5c442a;
      line-height: 1.6;
      margin-bottom: 1.75rem;
      flex-grow: 1;
    }

    #cardCatDormitories .btn-explore-category {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      color: #ffffff;
      font-size: 0.92rem;
      font-weight: 700;
      padding: 0.9rem 1.4rem;
      border-radius: 12px;
      text-decoration: none;
      transition: all 0.25s ease;
      box-shadow: 0 6px 18px rgba(217, 119, 6, 0.28);
      border: 1px solid #d97706;
      margin-top: auto;
    }

    #cardCatDormitories:hover .btn-explore-category {
      background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
      box-shadow: 0 10px 24px rgba(217, 119, 6, 0.4);
    }

    #cardCatDormitories .btn-explore-category svg {
      transition: transform 0.22s ease;
    }

    #cardCatDormitories:hover .btn-explore-category svg {
      transform: translateX(5px);
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

    .status-actions {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
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

    .btn-view-bookings.gold {
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      border-color: #d97706;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.22);
    }

    .btn-view-bookings.gold:hover {
      background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
      box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
    }

    /* ==========================================================================
       SHOWCASE FACILITIES & DORMITORIES SECTION
       ========================================================================== */
    .portal-facilities-showcase {
      margin-top: 3.5rem;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 252, 248, 0.95) 100%);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1.5px solid rgba(162, 215, 180, 0.85);
      border-radius: 22px;
      padding: 2.75rem 2rem 3rem;
      box-shadow: 0 18px 45px rgba(5, 20, 11, 0.16);
    }

    .portal-showcase-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .portal-showcase-title {
      font-family: var(--font-serif);
      font-size: 1.95rem;
      font-weight: 800;
      color: #0c331d;
      margin-bottom: 0.5rem;
    }

    .portal-showcase-subtitle {
      font-size: 0.95rem;
      color: #435f4f;
      max-width: 620px;
      margin: 0 auto;
    }

    .portal-filter-tabs {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.65rem;
      flex-wrap: wrap;
      margin-bottom: 2.25rem;
    }

    .portal-filter-btn {
      padding: 0.65rem 1.4rem;
      border-radius: 9999px;
      border: 1.5px solid #d4e7db;
      background: #ffffff;
      color: #2b533a;
      font-size: 0.88rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.22s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }

    .portal-filter-btn:hover {
      background: #eaf6ee;
      border-color: #174d2f;
      color: #174d2f;
    }

    .portal-filter-btn.active {
      background: linear-gradient(135deg, #174d2f 0%, #206740 100%);
      color: #ffffff;
      border-color: #174d2f;
      box-shadow: 0 4px 14px rgba(23, 77, 47, 0.25);
    }

    .portal-facility-action-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: linear-gradient(135deg, #174d2f 0%, #206740 100%);
      color: #ffffff;
      font-size: 0.82rem;
      font-weight: 700;
      padding: 0.45rem 0.95rem;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(23, 77, 47, 0.2);
    }

    .portal-facility-action-btn:hover {
      background: linear-gradient(135deg, #0d3520 0%, #174d2f 100%);
      color: #ffffff;
      transform: translateX(3px);
      box-shadow: 0 4px 12px rgba(23, 77, 47, 0.35);
    }

    .portal-facility-action-btn.gold {
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      box-shadow: 0 2px 8px rgba(217, 119, 6, 0.2);
    }

    .portal-facility-action-btn.gold:hover {
      background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
    }

    @media (max-width: 860px) {
      .portal-category-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      .portal-category-wrapper {
        margin: 1.5rem auto 3rem;
      }
      .portal-category-intro-card {
        padding: 2.25rem 1.35rem 2rem;
        border-radius: 18px;
      }
      .cat-sec-title {
        font-size: 1.85rem;
      }
      .portal-cat-card {
        padding: 1.75rem 1.5rem;
        border-radius: 18px;
      }
      .portal-quick-status-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 1.25rem 1.5rem;
      }
      .status-actions {
        width: 100%;
        flex-direction: column;
      }
      .btn-view-bookings {
        width: 100%;
        justify-content: center;
      }
      .portal-facilities-showcase {
        padding: 1.75rem 1.25rem 2rem;
        border-radius: 18px;
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
      <!-- Left: ATI Brand -->
      <a href="home.php" class="booking-brand" title="ATI Reservation Portal">
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

        <!-- Book Facility Action -->
        <a href="booking.php" class="booking-nav-item" title="Book Facility (Halls & Venues)">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M12 5v14"></path>
            <path d="M5 12h14"></path>
          </svg>
          <span>+ Book Facility</span>
        </a>

        <!-- Facility Bookings (Halls) -->
        <a href="my_reservations.php" class="booking-nav-item" title="Your Bookings (Facilities & Venues)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Your Bookings</span>
          <span class="nav-badge-count">4</span>
        </a>

        <!-- Dormitory Reservations (Lodging) -->
        <a href="booking_history.php" class="booking-nav-item" title="Your Reservations (Dormitories & Rooms)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
            <path d="M6 8v9"></path>
          </svg>
          <span>Your Reservations</span>
          <span class="nav-badge-count blue">2</span>
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
            <div class="user-avatar-circle">JD</div>
            <div class="user-details">
              <div class="user-name">Juan Dela Cruz</div>
              <div class="user-role">ATI Staff (CDD)</div>
            </div>
          </div>

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
              <span>Your Bookings</span>
            </a>
            <a href="booking_history.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
                <path d="M6 8v9"></path>
              </svg>
              <span>Your Reservations</span>
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
              <span>Settings</span>
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

        <!-- Quick Actions Section in Drawer -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">QUICK ACTIONS</div>
          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;">
            <a href="booking.php" class="drawer-nav-link" style="background: linear-gradient(135deg, #174d2f 0%, #226b42 100%); color: #ffffff; font-weight: 800; border-radius: 10px; padding: 0.75rem 0.95rem; box-shadow: 0 4px 12px rgba(23,77,47,0.25);">
              <div class="drawer-link-icon" style="color: #ffffff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
              </div>
              <span class="drawer-link-text">+ Book Facility</span>
              <span style="font-size: 0.68rem; background: rgba(255,255,255,0.22); padding: 0.15rem 0.45rem; border-radius: 999px;">Halls</span>
            </a>

            <a href="dormitory_booking.php" class="drawer-nav-link" style="background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%); color: #ffffff; font-weight: 800; border-radius: 10px; padding: 0.75rem 0.95rem; box-shadow: 0 4px 12px rgba(2,132,199,0.25);">
              <div class="drawer-link-icon" style="color: #ffffff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
              </div>
              <span class="drawer-link-text">+ Reserve Dormitory</span>
              <span style="font-size: 0.68rem; background: rgba(255,255,255,0.22); padding: 0.15rem 0.45rem; border-radius: 999px;">Rooms</span>
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
            <span class="drawer-link-text">Home</span>
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
            <span class="drawer-link-text">Your Bookings</span>
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
            <span class="drawer-link-text">Your Reservations</span>
            <span class="drawer-badge-count blue">2</span>
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

          <a href="javascript:void(0)" onclick="toggleMobileDrawer(false); openUserSettingsModal();" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </div>
            <span class="drawer-link-text">Settings</span>
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

  <main class="portal-category-wrapper">
    
    <!-- ==========================================================================
         CATEGORY INTRO CARD (Matching Design Reference)
         ========================================================================== -->
    <div class="portal-category-intro-card">
      <div class="portal-category-logo-wrap">
        <img src="assets/images/ATI_Logo.png" alt="Agricultural Training Institute Official Logo" class="portal-category-logo">
      </div>
      <h1 class="cat-sec-title">OUR FACILITIES</h1>
      <p class="cat-sec-subtitle">Choose a category below to open a dedicated view of available function halls, training rooms, and dormitory accommodations.</p>
      <div class="cat-sec-divider"></div>
    </div>

    <!-- ==========================================================================
         TWO MAIN CATEGORY CARDS (Halls vs Dormitories)
         ========================================================================== -->
    <div class="portal-category-grid">
      
      <!-- 1. Halls Category Card -->
      <a href="booking.php" class="portal-cat-card" id="cardCatHalls" title="Explore ATI Halls">
        <div class="cat-card-header">
          <span class="cat-count-badge">6 Venues Available</span>
        </div>
        <div class="cat-icon-container">
          <svg width="72" height="72" viewBox="0 0 64 64" fill="none" stroke="#174d2f" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <!-- Classical Hall Building with Pediment & Columns -->
            <path d="M6 22L32 7L58 22H6Z" fill="#d7f0df"/>
            <path d="M10 22V26H54V22"/>
            <rect x="15" y="26" width="7" height="26" rx="2" fill="#d7f0df"/>
            <rect x="28.5" y="26" width="7" height="26" rx="2" fill="#d7f0df"/>
            <rect x="42" y="26" width="7" height="26" rx="2" fill="#d7f0df"/>
            <path d="M9 52H55V56H9V52Z"/>
            <path d="M5 56H59V60H5V56Z"/>
          </svg>
        </div>
        <h2 class="cat-card-title">Halls</h2>
        <p class="cat-card-desc">Function auditoriums, audio-visual training rooms, executive boardrooms &amp; mess dining facilities.</p>
        <div class="btn-explore-category">
          <span>Explore Halls</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>
      </a>

      <!-- 2. Dormitories Category Card -->
      <a href="dormitory_booking.php" class="portal-cat-card" id="cardCatDormitories" title="Explore ATI Dormitories">
        <div class="cat-card-header">
          <span class="cat-count-badge">6 Floors / 60 Rooms</span>
        </div>
        <div class="cat-icon-container">
          <svg width="72" height="72" viewBox="0 0 64 64" fill="none" stroke="#b45309" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <!-- Multi-story Dormitory Accommodation Building -->
            <rect x="23" y="10" width="18" height="46" rx="2" fill="#fef3c7"/>
            <rect x="9" y="22" width="14" height="34" rx="2" fill="#fef3c7"/>
            <rect x="41" y="22" width="14" height="34" rx="2" fill="#fef3c7"/>
            <rect x="13" y="28" width="6" height="6" rx="1"/>
            <rect x="13" y="40" width="6" height="6" rx="1"/>
            <rect x="45" y="28" width="6" height="6" rx="1"/>
            <rect x="45" y="40" width="6" height="6" rx="1"/>
            <rect x="28" y="16" width="8" height="6" rx="1"/>
            <rect x="28" y="28" width="8" height="6" rx="1"/>
            <rect x="28" y="44" width="8" height="12" rx="1"/>
            <line x1="5" y1="56" x2="59" y2="56"/>
          </svg>
        </div>
        <h2 class="cat-card-title">Dormitories</h2>
        <p class="cat-card-desc">Executive VIP suites, shared trainee quarters, guest lecturer rooms &amp; twin accommodations.</p>
        <div class="btn-explore-category">
          <span>Explore Dormitories</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>
      </a>

    </div>

    <!-- ==========================================================================
         USER RESERVATION QUICK STATUS
         ========================================================================== -->
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
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          </svg>
          <span>Your Bookings (4)</span>
        </a>
        <a href="booking_history.php" class="btn-view-bookings gold" title="View Your Dormitory Reservations">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
          </svg>
          <span>Your Reservations (2)</span>
        </a>
      </div>
    </div>

    <!-- ==========================================================================
         FEATURED FACILITIES & LODGING CARDS SHOWCASE
         ========================================================================== -->
    <section class="portal-facilities-showcase">
      <div class="portal-showcase-header">
        <h2 class="portal-showcase-title">All Available Facilities &amp; Lodging</h2>
        <p class="portal-showcase-subtitle">Browse and select any venue or dormitory accommodation directly to initiate an official reservation.</p>
      </div>

      <!-- Filter Tabs -->
      <div class="portal-filter-tabs" id="portalFacilityFilters">
        <button type="button" class="portal-filter-btn active" data-filter="all">All Facilities (6)</button>
        <button type="button" class="portal-filter-btn" data-filter="halls">Halls &amp; Venues (4)</button>
        <button type="button" class="portal-filter-btn" data-filter="dorms">Dormitories &amp; Lodging (2)</button>
      </div>

      <!-- Facility Cards Grid -->
      <div class="facilities-grid" id="portalFacilitiesGrid">
        
        <!-- 1. Serrano Function Hall -->
        <article class="facility-card" data-category="halls">
          <div class="facility-img-wrapper">
            <img src="assets/images/function_hall.jpg" alt="Serrano Function Hall" loading="lazy">
            <span class="facility-badge">Cap: 200 Pax</span>
          </div>
          <div class="facility-body">
            <h3 class="facility-name">Serrano Function Hall</h3>
            <p class="facility-desc">Flagship multi-purpose auditorium for seminars, large conventions, and institutional assemblies.</p>
            <div class="facility-bubble-tags">
              <span class="facility-bubble-tag">Central Aircon</span>
              <span class="facility-bubble-tag">Dual Projector</span>
              <span class="facility-bubble-tag">Sound System</span>
              <span class="facility-bubble-tag">VIP Lounge</span>
            </div>
            <div class="facility-footer">
              <span class="facility-meta">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                </svg>
                200 Capacity
              </span>
              <a href="booking.php?facility=function-hall" class="portal-facility-action-btn">
                <span>Book Facility</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </article>

        <!-- 2. Training Hall A -->
        <article class="facility-card" data-category="halls">
          <div class="facility-img-wrapper">
            <img src="assets/images/training_hall.jpg" alt="Training Hall A" loading="lazy">
            <span class="facility-badge">Cap: 80 Pax</span>
          </div>
          <div class="facility-body">
            <h3 class="facility-name">Training Hall A</h3>
            <p class="facility-desc">Interactive workshop hall with flexible modular seating, smart monitors, and presenter booth.</p>
            <div class="facility-bubble-tags">
              <span class="facility-bubble-tag">Modular Desks</span>
              <span class="facility-bubble-tag">Smart Display</span>
              <span class="facility-bubble-tag">High-speed WiFi</span>
              <span class="facility-bubble-tag">Whiteboards</span>
            </div>
            <div class="facility-footer">
              <span class="facility-meta">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                Interactive Lab
              </span>
              <a href="booking.php?facility=training-hall-a" class="portal-facility-action-btn">
                <span>Book Facility</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </article>

        <!-- 3. Mess Hall Dining Pavilion -->
        <article class="facility-card" data-category="halls">
          <div class="facility-img-wrapper">
            <img src="assets/images/mess_hall.jpg" alt="Mess Hall Dining Pavilion" loading="lazy">
            <span class="facility-badge">Cap: 150 Pax</span>
          </div>
          <div class="facility-body">
            <h3 class="facility-name">Mess Hall Dining Pavilion</h3>
            <p class="facility-desc">Dedicated institutional dining venue for official banquets, delegate meals, and catered buffets.</p>
            <div class="facility-bubble-tags">
              <span class="facility-bubble-tag">Buffet Counters</span>
              <span class="facility-bubble-tag">Kitchen Access</span>
              <span class="facility-bubble-tag">Sanitation Station</span>
              <span class="facility-bubble-tag">Patio Deck</span>
            </div>
            <div class="facility-footer">
              <span class="facility-meta">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                  <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                  <line x1="6" y1="1" x2="6" y2="4"></line>
                  <line x1="10" y1="1" x2="10" y2="4"></line>
                </svg>
                Dining &amp; Buffet
              </span>
              <a href="booking.php?facility=mess-hall" class="portal-facility-action-btn">
                <span>Book Facility</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </article>

        <!-- 4. Executive Boardroom -->
        <article class="facility-card" data-category="halls">
          <div class="facility-img-wrapper">
            <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" loading="lazy">
            <span class="facility-badge">Cap: 30 Pax</span>
          </div>
          <div class="facility-body">
            <h3 class="facility-name">Executive Boardroom</h3>
            <p class="facility-desc">High-level executive briefing room with premium leather chairs, 4K display, and teleconference system.</p>
            <div class="facility-bubble-tags">
              <span class="facility-bubble-tag">Executive Chairs</span>
              <span class="facility-bubble-tag">Video Conf Cam</span>
              <span class="facility-bubble-tag">Acoustic Walls</span>
              <span class="facility-bubble-tag">Coffee Bar</span>
            </div>
            <div class="facility-footer">
              <span class="facility-meta">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                  <line x1="8" y1="21" x2="16" y2="21"></line>
                  <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                VIP Conference
              </span>
              <a href="booking.php?facility=executive-boardroom" class="portal-facility-action-btn">
                <span>Book Facility</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </article>

        <!-- 5. 1st Floor: Sampaguita Dormitory -->
        <article class="facility-card" data-category="dorms">
          <div class="facility-img-wrapper">
            <img src="assets/images/dormitory_bunk.jpg" alt="1st Floor: Sampaguita Trainee Dormitory" loading="lazy">
            <span class="facility-badge">10 Rooms (40 Beds)</span>
          </div>
          <div class="facility-body">
            <h3 class="facility-name">1st Floor: Sampaguita Dorm</h3>
            <p class="facility-desc">Comfortable delegation quarters on floors 1–4 with sturdy wooden bunk beds, lockers, and air conditioning.</p>
            <div class="facility-bubble-tags">
              <span class="facility-bubble-tag">Wooden Bunks</span>
              <span class="facility-bubble-tag">Full Aircon</span>
              <span class="facility-bubble-tag">Personal Lockers</span>
              <span class="facility-bubble-tag">Study Desks</span>
            </div>
            <div class="facility-footer">
              <span class="facility-meta">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M2 4v16"></path>
                  <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                  <path d="M2 17h20"></path>
                  <path d="M6 8v9"></path>
                </svg>
                Trainee Quarters
              </span>
              <a href="dormitory_booking.php" class="portal-facility-action-btn gold">
                <span>Reserve Dormitory</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </article>

        <!-- 6. 5th Floor: Waling-Waling VIP Suite -->
        <article class="facility-card" data-category="dorms">
          <div class="facility-img-wrapper">
            <img src="assets/images/dormitory.jpg" alt="5th Floor: Waling-Waling VIP Suite" loading="lazy">
            <span class="facility-badge">10 VIP Suites (20 Beds)</span>
          </div>
          <div class="facility-body">
            <h3 class="facility-name">5th Floor: Waling-Waling VIP Suite</h3>
            <p class="facility-desc">Executive lodging for visiting resource speakers, national trainers, and VIP dignitaries with ensuite baths.</p>
            <div class="facility-bubble-tags">
              <span class="facility-bubble-tag">VIP Suite</span>
              <span class="facility-bubble-tag">Ensuite Bath</span>
              <span class="facility-bubble-tag">Workstation</span>
              <span class="facility-bubble-tag">Hot Shower</span>
            </div>
            <div class="facility-footer">
              <span class="facility-meta">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M2 4v16"></path>
                  <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                  <path d="M2 17h20"></path>
                  <path d="M6 8v9"></path>
                </svg>
                VIP Executive
              </span>
              <a href="dormitory_booking.php" class="portal-facility-action-btn gold">
                <span>Reserve Dormitory</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </article>

      </div>
    </section>

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

    // Profile Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
      var profileBadge = document.getElementById('userProfileBadge');
      var profileDropdown = document.getElementById('profileDropdown');

      // Sync Avatar & Profile from localStorage
      var avatar = localStorage.getItem('ati_user_avatar');
      var profile = localStorage.getItem('ati_user_profile');
      if (avatar) {
        document.querySelectorAll('.user-avatar-circle, .drawer-avatar').forEach(function(c) {
          c.innerHTML = '<img src="' + avatar + '" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">';
        });
      }
      if (profile) {
        try {
          var p = JSON.parse(profile);
          if (p.fullName) {
            document.querySelectorAll('.user-name, .dropdown-user-name, .drawer-profile-info h5').forEach(function(el) {
              el.textContent = p.fullName;
            });
          }
        } catch(e) {}
      }

      if (profileBadge && profileDropdown) {
        profileBadge.addEventListener('click', function(e) {
          e.stopPropagation();
          profileDropdown.classList.toggle('show');
          profileDropdown.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
          if (!profileBadge.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show');
            profileDropdown.classList.remove('active');
          }
        });
      }

      // Facility Filter Tabs for Home Portal Showcase
      var filterBtns = document.querySelectorAll('#portalFacilityFilters .portal-filter-btn');
      var facilityCards = document.querySelectorAll('#portalFacilitiesGrid .facility-card');

      filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
          filterBtns.forEach(function(b) { b.classList.remove('active'); });
          btn.classList.add('active');
          var filterVal = btn.getAttribute('data-filter');

          facilityCards.forEach(function(card) {
            if (filterVal === 'all' || card.getAttribute('data-category') === filterVal) {
              card.style.display = 'flex';
            } else {
              card.style.display = 'none';
            }
          });
        });
      });
    });
  </script>
  <script src="js/user_settings.js?v=<?php echo time(); ?>"></script>
</body>

</html>
