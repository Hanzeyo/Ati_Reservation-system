<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Step-by-Step Dormitory Lodging & Room Booking Portal
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book ATI Dormitory Accommodations - Agricultural Training Institute</title>
  <meta name="description"
    content="Submit official lodging and room booking requests for ATI dormitory suites, trainee floors, and guest quarters.">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
  <style>
    .dorm-hero-pill {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      color: #1e40af;
    }

    .dorm-hero-pill .pulse-dot {
      background: #2563eb;
    }

    .booking-switch-banner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
      border: 1.5px solid #a7f3d0;
      border-radius: 12px;
      padding: 0.85rem 1.25rem;
      margin-bottom: 1.75rem;
      text-decoration: none;
      color: #065f46;
      transition: all 0.22s ease;
    }

    .booking-switch-banner:hover {
      background: #d1fae5;
      border-color: #059669;
      transform: translateY(-2px);
    }

    .booking-switch-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      background: #ffffff;
      color: #065f46;
      font-weight: 700;
      font-size: 0.8rem;
      padding: 0.45rem 0.85rem;
      border-radius: 8px;
      border: 1px solid #a7f3d0;
      flex-shrink: 0;
    }
  </style>
</head>

<body class="booking-body">

  <!-- ==========================================================================
       TOPBAR NAVIGATION (AUTHENTICATED)
       ========================================================================== -->
  <header class="booking-topbar">
    <div class="booking-topbar-container">
      <!-- Left: ATI Brand -->
      <a href="home.php" class="booking-brand" title="Return to Home">
        <img src="assets/images/ATI_Logo.png" alt="ATI Official Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility and Dormitory Reservation System</p>
        </div>
      </a>

      <!-- Center & Right: Navigation Actions -->
      <nav class="booking-nav-center">
        <!-- Home -->
        <a href="home.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Home</span>
        </a>

        <!-- New Reservation / New Booking -->
        <a href="dormitory_booking.php" class="booking-nav-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
            <path d="M6 8v9"></path>
          </svg>
          <span>Book Lodging</span>
        </a>

        <!-- My Reservations -->
        <a href="my_reservations.php" class="booking-nav-item" title="My Reservations">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count">4</span>
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
            <a href="admin_dashboard.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
              <span>Admin Dashboard</span>
            </a>
            <div style="height: 1px; background: #e5ede7; margin: 0.35rem 0;"></div>
            <a href="index.php" class="dropdown-item danger">
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

      <div class="drawer-body">
        <div class="drawer-profile-card">
          <div class="drawer-avatar">JD</div>
          <div class="drawer-profile-info">
            <h5>Juan Dela Cruz</h5>
            <p>ATI Staff (CDD)</p>
            <span class="drawer-badge">Verified Personnel</span>
          </div>
        </div>

        <div class="drawer-nav-section">
          <div class="drawer-section-label">PORTAL NAVIGATION</div>

          <a href="home.php" class="drawer-nav-link">
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
            <span class="drawer-link-text">New Facility Reservation</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="dormitory_booking.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
              </svg>
            </div>
            <span class="drawer-link-text">Book Dormitory Lodging</span>
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
            <span class="drawer-badge-count">4</span>
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
       MAIN DORMITORY BOOKING WORKSPACE
       ========================================================================== -->
  <main class="booking-main-wrapper">

    <!-- Switch Banner: Quick Jump to Facility Reservations -->
    <a href="booking.php?category=halls" class="booking-switch-banner" title="Switch to Facility Reservation Portal">
      <div>
        <strong style="display: block; font-size: 0.92rem; color: #064e3b;">Looking to reserve a Function Hall or
          Training Venue instead?</strong>
        <span style="font-size: 0.8rem; color: #047857;">Submit requests for Function Hall, Training Hall A, Boardroom,
          and Mess Hall</span>
      </div>
      <span class="booking-switch-btn">
        <span>Go to Facility Reservations</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </span>
    </a>

    <!-- Header Section -->
    <section class="booking-header-section">
      <div class="booking-assistant-pill dorm-hero-pill">
        <span class="pulse-dot"></span>
        <span>Dormitory Lodging &amp; Room Booking Assistant</span>
      </div>
      <h2 class="booking-main-title">Book ATI Dormitory Accommodations</h2>
      <p class="booking-main-subtitle">
        Submit an official lodging request for trainee delegations, resource speakers, and participants for dormitory
        custodian bed allotment.
      </p>
    </section>

    <!-- Stepper Navigation Bar -->
    <div class="stepper-bar-card">
      <div class="stepper-grid">
        <div class="step-tab-btn active" data-step="1">
          <span class="step-number">1</span>
          <span>Dormitory Floor</span>
        </div>
        <div class="step-tab-btn" data-step="2">
          <span class="step-number">2</span>
          <span>Stay Dates &amp; Nights</span>
        </div>
        <div class="step-tab-btn" data-step="3" id="stepTabEventDetails">
          <span class="step-number">3</span>
          <span id="stepTabEventName">Lodging Details</span>
        </div>
        <div class="step-tab-btn" data-step="4" id="stepTabDocuments">
          <span class="step-number">4</span>
          <span>Roster &amp; Authority</span>
        </div>
        <div class="step-tab-btn" data-step="5" id="stepTabReview">
          <span class="step-number">5</span>
          <span>Review &amp; Submit</span>
        </div>
      </div>

      <!-- Compact Mobile Stepper Indicator -->
      <div class="mobile-stepper-progress" id="mobileStepperProgress">
        <div class="mobile-stepper-header">
          <div class="mobile-step-pill">
            <span class="mobile-step-badge" id="mobileStepBadge">Step 1 of 5</span>
            <strong class="mobile-step-name" id="mobileStepName">Dormitory Selection</strong>
          </div>
          <span class="mobile-step-percent" id="mobileStepPercent">20%</span>
        </div>
        <div class="mobile-progress-track">
          <div class="mobile-progress-fill" id="mobileProgressFill" style="width: 20%;"></div>
        </div>
        <div class="mobile-step-dots">
          <div class="mobile-dot active" data-step="1" title="Dormitory">1</div>
          <div class="mobile-dot" data-step="2" title="Stay Dates">2</div>
          <div class="mobile-dot" data-step="3" title="Lodging Details">3</div>
          <div class="mobile-dot" data-step="4" title="Documents">4</div>
          <div class="mobile-dot" data-step="5" title="Review">5</div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 1: DORMITORY FLOOR SELECTION
         ========================================================================== -->
    <div class="wizard-step-view active" data-step="1">

      <div class="facility-category-nav-header" id="facilitiesSectionAnchor">
        <div class="category-nav-left">
          <a href="home.php" class="btn-return-home" title="Back to Category Selection">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>Back to Portal Home</span>
          </a>
          <span class="active-category-indicator" id="activeCategoryBadge">
            <span class="indicator-dot"></span>
            <span id="activeCategoryName">Dormitories (6 Floors Available)</span>
          </span>
        </div>
      </div>

      <div class="facility-header-status-row">
        <div class="filter-label-group">
          <svg viewBox="0 0 24 24" fill="none">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          <span id="categorySectionHeading">AVAILABLE DORMITORY FLOORS (6):</span>
        </div>
        <p class="select-instruction-text">Select a dormitory floor below, then click to view rooms and choose your room
          allotment.</p>
      </div>

      <!-- Facility Cards Grid (6 Floors) -->
      <div class="facility-selection-grid" id="facilitySelectionGrid">
        <!-- 1st Floor: Sampaguita Dormitory -->
        <article class="facility-choice-card dorm-suite-card selected" data-id="dorm-floor-1"
          data-name="1st Floor: Sampaguita Dormitory" data-rate="₱500/night" data-capacity="12 ROOMS (48 BEDS)"
          data-facility-type="dormitories">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="1st Floor: Sampaguita Dormitory" loading="lazy">
            <span class="facility-cap-badge">12 ROOMS (48 BEDS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">1ST FLOOR &bull; SAMPAGUITA DORMITORY</span>
            <h3 class="facility-title">1st Floor: Sampaguita Dormitory</h3>
            <div class="facility-rate-tag">Standard Rate: ₱500/night</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">12 Aircon Rooms</span>
              <span class="amenity-pill">Single Bunk Beds</span>
              <span class="amenity-pill">Individual Lockers</span>
              <span class="amenity-pill">Study Desks &amp; Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge" style="display: none;">
              <span class="d-room-text">✓ Room Selected</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Room &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- 2nd Floor: Ilang-Ilang Dormitory -->
        <article class="facility-choice-card dorm-suite-card" data-id="dorm-floor-2"
          data-name="2nd Floor: Ilang-Ilang Dormitory" data-rate="₱500/night" data-capacity="12 ROOMS (48 BEDS)"
          data-facility-type="dormitories">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="2nd Floor: Ilang-Ilang Dormitory" loading="lazy">
            <span class="facility-cap-badge">12 ROOMS (48 BEDS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">2ND FLOOR &bull; ILANG-ILANG DORMITORY</span>
            <h3 class="facility-title">2nd Floor: Ilang-Ilang Dormitory</h3>
            <div class="facility-rate-tag">Standard Rate: ₱500/night</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">12 Aircon Rooms</span>
              <span class="amenity-pill">Single Bunk Beds</span>
              <span class="amenity-pill">Individual Lockers</span>
              <span class="amenity-pill">Study Desks &amp; Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge" style="display: none;">
              <span class="d-room-text">✓ Room Selected</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Room &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- 3rd Floor: Gumamela Dormitory -->
        <article class="facility-choice-card dorm-suite-card" data-id="dorm-floor-3"
          data-name="3rd Floor: Gumamela Dormitory" data-rate="₱500/night" data-capacity="12 ROOMS (48 BEDS)"
          data-facility-type="dormitories">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="3rd Floor: Gumamela Dormitory" loading="lazy">
            <span class="facility-cap-badge">12 ROOMS (48 BEDS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">3RD FLOOR &bull; GUMAMELA DORMITORY</span>
            <h3 class="facility-title">3rd Floor: Gumamela Dormitory</h3>
            <div class="facility-rate-tag">Standard Rate: ₱500/night</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">12 Aircon Rooms</span>
              <span class="amenity-pill">Single Bunk Beds</span>
              <span class="amenity-pill">Individual Lockers</span>
              <span class="amenity-pill">Study Desks &amp; Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge" style="display: none;">
              <span class="d-room-text">✓ Room Selected</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Room &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- 4th Floor: Rosal Dormitory -->
        <article class="facility-choice-card dorm-suite-card" data-id="dorm-floor-4"
          data-name="4th Floor: Rosal Dormitory" data-rate="₱500/night" data-capacity="12 ROOMS (48 BEDS)"
          data-facility-type="dormitories">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="4th Floor: Rosal Dormitory" loading="lazy">
            <span class="facility-cap-badge">12 ROOMS (48 BEDS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">4TH FLOOR &bull; ROSAL DORMITORY</span>
            <h3 class="facility-title">4th Floor: Rosal Dormitory</h3>
            <div class="facility-rate-tag">Standard Rate: ₱500/night</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">12 Aircon Rooms</span>
              <span class="amenity-pill">Single Bunk Beds</span>
              <span class="amenity-pill">Individual Lockers</span>
              <span class="amenity-pill">Study Desks &amp; Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge" style="display: none;">
              <span class="d-room-text">✓ Room Selected</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Room &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- 5th Floor: Waling-Waling Dormitory -->
        <article class="facility-choice-card dorm-suite-card" data-id="dorm-floor-5"
          data-name="5th Floor: Waling-Waling Dormitory" data-rate="₱500/night" data-capacity="12 ROOMS (48 BEDS)"
          data-facility-type="dormitories">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="5th Floor: Waling-Waling Dormitory" loading="lazy">
            <span class="facility-cap-badge">12 ROOMS (48 BEDS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">5TH FLOOR &bull; WALING-WALING DORMITORY</span>
            <h3 class="facility-title">5th Floor: Waling-Waling Dormitory</h3>
            <div class="facility-rate-tag">Standard Rate: ₱500/night</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">12 Aircon Rooms</span>
              <span class="amenity-pill">Single Bunk Beds</span>
              <span class="amenity-pill">Individual Lockers</span>
              <span class="amenity-pill">Study Desks &amp; Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge" style="display: none;">
              <span class="d-room-text">✓ Room Selected</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Room &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- 6th Floor: Tayabak Dormitory -->
        <article class="facility-choice-card dorm-suite-card" data-id="dorm-floor-6"
          data-name="6th Floor: Tayabak Dormitory" data-rate="₱500/night" data-capacity="12 ROOMS (48 BEDS)"
          data-facility-type="dormitories">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="6th Floor: Tayabak Dormitory" loading="lazy">
            <span class="facility-cap-badge">12 ROOMS (48 BEDS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">6TH FLOOR &bull; TAYABAK DORMITORY</span>
            <h3 class="facility-title">6th Floor: Tayabak Dormitory</h3>
            <div class="facility-rate-tag">Standard Rate: ₱500/night</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">12 Aircon Rooms</span>
              <span class="amenity-pill">Single Bunk Beds</span>
              <span class="amenity-pill">Individual Lockers</span>
              <span class="amenity-pill">Study Desks &amp; Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge" style="display: none;">
              <span class="d-room-text">✓ Room Selected</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Room &amp; View Floor Plan
            </button>
          </div>
        </article>
      </div>

      <!-- Selection Summary Preview -->
      <div class="step1-selection-summary-card" id="step1SummaryCard">
        <div class="summary-card-header">
          <div class="sch-left">
            <span class="sch-badge">SELECTED DORMITORY FLOOR</span>
            <h4 class="sch-title" id="summaryPreviewTitle">1st Floor: Sampaguita Dormitory</h4>
          </div>
          <span class="sch-rate-pill" id="summaryPreviewRate">₱500 / night</span>
        </div>
        <div class="summary-card-body">
          <div class="scb-item" id="summaryPreviewRoomWrap">
            <span class="scb-label">Assigned Room:</span>
            <strong class="scb-val" id="summaryPreviewRoom">Room 101 Selected</strong>
          </div>
          <div class="scb-item">
            <span class="scb-label">Standard Capacity:</span>
            <strong class="scb-val" id="summaryPreviewCap">4 Beds per Room</strong>
          </div>
          <div class="scb-item">
            <span class="scb-label">Next Action:</span>
            <strong class="scb-val" style="color: #1e40af;">Click Proceed to Stay Dates below</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 2: STAY DATES & NIGHTS
         ========================================================================== -->
    <div class="wizard-step-view" data-step="2">
      <div class="dates-main-layout">
        <div class="date-picker-pane">
          <div class="pane-sec-header">
            <h4>Check-in &amp; Check-out Schedule</h4>
            <p>Select your arrival and departure dates. Standard check-in is 02:00 PM and check-out is 12:00 PM.</p>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
              <label for="dormCheckInDate"
                style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem; color: #1e3a29;">Check-in
                Date *</label>
              <input type="date" id="dormCheckInDate" class="step-text-input" value="2026-10-15"
                style="width: 100%; padding: 0.75rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
            </div>
            <div>
              <label for="dormCheckOutDate"
                style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem; color: #1e3a29;">Check-out
                Date *</label>
              <input type="date" id="dormCheckOutDate" class="step-text-input" value="2026-10-18"
                style="width: 100%; padding: 0.75rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
            </div>
          </div>

          <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="color: #14532d; font-size: 1.05rem;" id="dormNightsCountDisplay">3 Nights
                  Accommodation</strong>
                <p style="color: #166534; font-size: 0.82rem; margin: 0.2rem 0 0;">Check-in: 02:00 PM &bull; Check-out:
                  12:00 PM</p>
              </div>
              <span style="font-weight: 800; font-size: 1.2rem; color: #15803d;"
                id="dormEstimatedRateDisplay">₱1,500</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 3: LODGING SPECIFICATIONS & TRAINEE DETAILS
         ========================================================================== -->
    <div class="wizard-step-view" data-step="3">
      <div style="background: #ffffff; border: 1px solid #e1ece4; border-radius: 16px; padding: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #153924; margin-bottom: 0.5rem;">Delegation &amp; Guest
          Lodging Information</h3>
        <p style="font-size: 0.88rem; color: #516d5d; margin-bottom: 1.5rem;">Provide details regarding the group or
          resource persons staying in the dormitory.</p>

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
          <div>
            <label for="dormLodgingPurpose"
              style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; color: #1b3826;">Lodging
              Purpose / Official Training Activity *</label>
            <input type="text" id="dormLodgingPurpose" class="step-text-input"
              placeholder="e.g. National Young Farmers Camp Trainees Accommodation"
              value="Regional Agricultural Extension Trainees Accommodation"
              style="width: 100%; padding: 0.85rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
              <label for="dormGuestCount"
                style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; color: #1b3826;">Total
                Number of Delegates / Guests *</label>
              <input type="number" id="dormGuestCount" class="step-text-input" value="16" min="1" max="48"
                style="width: 100%; padding: 0.85rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
            </div>
            <div>
              <label for="dormDivisionUnit"
                style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; color: #1b3826;">Endorsing
                Division / Unit *</label>
              <select id="dormDivisionUnit"
                style="width: 100%; padding: 0.85rem; border: 1.5px solid #c9ded0; border-radius: 8px; font-weight: 600;">
                <option value="CDD" selected>Career Development Division (CDD)</option>
                <option value="PAD">Partnership &amp; Accreditation Division (PAD)</option>
                <option value="ISD">Information Services Division (ISD)</option>
                <option value="PPD">Policy &amp; Planning Division (PPD)</option>
                <option value="AFU">Administrative and Finance Unit</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
              <label for="dormMaleCount"
                style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; color: #1b3826;">Male
                Delegates Count</label>
              <input type="number" id="dormMaleCount" class="step-text-input" value="8" min="0" max="48"
                style="width: 100%; padding: 0.85rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
            </div>
            <div>
              <label for="dormFemaleCount"
                style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; color: #1b3826;">Female
                Delegates Count</label>
              <input type="number" id="dormFemaleCount" class="step-text-input" value="8" min="0" max="48"
                style="width: 100%; padding: 0.85rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
            </div>
          </div>

          <div>
            <label for="dormSpecialRequests"
              style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 0.35rem; color: #1b3826;">Special
              Custodian Requests / Linen &amp; Towel Requirements</label>
            <textarea id="dormSpecialRequests" rows="3"
              style="width: 100%; padding: 0.85rem; border: 1.5px solid #c9ded0; border-radius: 8px;"
              placeholder="e.g. Linen turnover requested on arrival. Late check-in after 8:00 PM due to delayed flight from Mindanao.">Linens & towels requested. Gender-segregated rooms on 1st Floor.</textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 4: TRAVEL ORDER & GUEST ROSTER
         ========================================================================== -->
    <div class="wizard-step-view" data-step="4">
      <div style="background: #ffffff; border: 1px solid #e1ece4; border-radius: 16px; padding: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #153924; margin-bottom: 0.5rem;">Official Authorization
          &amp; Guest Roster</h3>
        <p style="font-size: 0.88rem; color: #516d5d; margin-bottom: 1.5rem;">Upload required government authority
          papers or official participant lists for custodian gate verification.</p>

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
          <div
            style="border: 2px dashed #9bcbb0; background: #fbfdfc; border-radius: 12px; padding: 2rem; text-align: center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#174d2f" stroke-width="1.8"
              style="margin-bottom: 0.5rem;">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="12" y1="18" x2="12" y2="12"></line>
              <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            <h4 style="font-size: 1rem; font-weight: 700; color: #164329; margin-bottom: 0.25rem;">Upload Official
              Travel Order / Training List</h4>
            <p style="font-size: 0.82rem; color: #5b7566; margin-bottom: 1rem;">PDF, DOCX, or scanned JPEG (Max 10MB)
            </p>
            <input type="file" id="dormDocUpload" style="display: none;">
            <button type="button" onclick="document.getElementById('dormDocUpload').click()"
              style="background: #174d2f; color: #fff; border: none; padding: 0.65rem 1.4rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Select
              Document</button>
            <p style="font-size: 0.78rem; color: #16a34a; margin-top: 0.75rem; font-weight: 600;">✓
              Official_CDD_Trainee_Roster_Batch4.pdf (Attached)</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 5: REVIEW & SUBMIT TO CUSTODIAN
         ========================================================================== -->
    <div class="wizard-step-view" data-step="5">
      <div
        style="background: #ffffff; border: 1.5px solid #86efac; border-radius: 18px; padding: 2.25rem; box-shadow: 0 10px 30px rgba(23, 77, 47, 0.08);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
          <span
            style="background: #dcfce7; color: #166534; padding: 0.4rem 0.85rem; border-radius: 999px; font-weight: 700; font-size: 0.82rem;">VERIFICATION
            READY</span>
          <span style="font-size: 0.85rem; color: #5b7566;">Reference: <strong
              id="dormRefPreview">ATI-BK-2026-1088</strong></span>
        </div>

        <h3 style="font-size: 1.45rem; font-weight: 800; color: #10331f; margin-bottom: 1.25rem;" id="dormReviewTitle">
          Regional Agricultural Extension Trainees Accommodation</h3>

        <div
          style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; background: #f8faf9; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.75rem;">
          <div>
            <span style="font-size: 0.76rem; text-transform: uppercase; color: #526f5e; font-weight: 700;">Dormitory
              Floor &amp; Rooms</span>
            <strong style="display: block; font-size: 0.95rem; color: #143522; margin-top: 0.2rem;"
              id="dormReviewFloor">1st Floor: Sampaguita (Rooms 101-104)</strong>
          </div>
          <div>
            <span style="font-size: 0.76rem; text-transform: uppercase; color: #526f5e; font-weight: 700;">Stay
              Window</span>
            <strong style="display: block; font-size: 0.95rem; color: #143522; margin-top: 0.2rem;"
              id="dormReviewStay">Oct 15 - 18, 2026 (3 Nights)</strong>
          </div>
          <div>
            <span style="font-size: 0.76rem; text-transform: uppercase; color: #526f5e; font-weight: 700;">Delegates
              Count</span>
            <strong style="display: block; font-size: 0.95rem; color: #143522; margin-top: 0.2rem;"
              id="dormReviewPax">16 Delegates (8M / 8F)</strong>
          </div>
          <div>
            <span style="font-size: 0.76rem; text-transform: uppercase; color: #526f5e; font-weight: 700;">Endorsing
              Unit</span>
            <strong style="display: block; font-size: 0.95rem; color: #143522; margin-top: 0.2rem;">Career Development
              Division (CDD)</strong>
          </div>
        </div>

        <div
          style="border-top: 1px solid #e1ece4; padding-top: 1rem; margin-bottom: 1.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
          <span style="font-size: 0.88rem; color: #435b4d;">Routing Clearance: <strong>Dormitory Custodian Bed Allotment
              &amp; Security Gate Pass</strong></span>
          <span
            style="background: #fef3c7; color: #92400e; font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 999px;">Pending
            Custodian Allocation</span>
        </div>

        <button type="button" id="btnSubmitDormitoryBooking"
          style="width: 100%; background: linear-gradient(135deg, #174d2f 0%, #226b42 100%); color: #ffffff; border: none; padding: 1.1rem; border-radius: 12px; font-weight: 800; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 6px 18px rgba(23, 77, 47, 0.3);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Submit Official Dormitory Booking Request</span>
        </button>
      </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="booking-bottom-actions" id="bookingBottomActions">
      <button type="button" class="btn-step-back" id="btnStepBack" style="display: none;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Back</span>
      </button>

      <button type="button" class="btn-proceed-step" id="btnProceedStep">
        <span>Proceed to Stay Dates</span>
        <svg viewBox="0 0 24 24" fill="none">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>
    </div>

    <!-- Room Selection Modal -->
    <div class="room-modal-overlay" id="roomSelectionModal" style="display: none;">
      <div class="room-modal-dialog" role="dialog" aria-modal="true">
        <div class="room-modal-header">
          <div class="room-modal-header-info">
            <div class="modal-badge-row">
              <span class="modal-dorm-pill-tag" id="modalCategoryPill">DORMITORY ROOM PLAN</span>
              <span class="modal-dorm-rate-pill" id="modalDormRate">₱500 / night</span>
            </div>
            <h3 class="modal-dorm-title" id="modalDormTitle">1st Floor: Sampaguita Dormitory</h3>
            <p class="modal-dorm-desc" id="modalDormFloor">Select rooms to allocate for delegates and trainees.</p>
          </div>
          <button type="button" class="room-modal-close-btn" id="btnModalClose" onclick="closeRoomModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        <!-- Modal Legend -->
        <div class="room-modal-legend">
          <div class="legend-item">
            <span class="legend-color-dot available"></span>
            <span id="modalLegendAvailText"><strong>Available:</strong> Click to assign for your stay</span>
          </div>
          <div class="legend-item">
            <span class="legend-color-dot reserved"></span>
            <span id="modalLegendResText"><strong>Reserved:</strong> Occupied by scheduled delegates</span>
          </div>
        </div>
        <div class="room-modal-body">
          <div class="modal-rooms-grid" id="modalRoomsGrid"></div>

          <!-- Feedback Bar inside modal -->
          <div class="modal-room-feedback-bar" id="modalRoomFeedback" style="display: none;">
            <div class="modal-feedback-left">
              <div class="mf-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div>
                <div class="mf-title" id="modalFeedbackTitle">Room Selected</div>
                <div class="mf-sub" id="modalFeedbackSub">Standard Trainee Dormitory (₱500 / night)</div>
              </div>
            </div>
            <span class="badge-assigned-ok">✓ Ready to Allocate</span>
          </div>
        </div>
      </div>
    </div>

  </main>

  <script src="js/booking.js?v=<?php echo time(); ?>"></script>
  <script>
    // Dormitory Stepper Logic
    let currentDormStep = 1;
    const totalDormSteps = 5;

    function setDormStep(step) {
      if (step < 1 || step > totalDormSteps) return;
      currentDormStep = step;

      document.querySelectorAll('.wizard-step-view').forEach(view => {
        view.classList.toggle('active', parseInt(view.dataset.step) === step);
      });

      document.querySelectorAll('.step-tab-btn').forEach(tab => {
        tab.classList.toggle('active', parseInt(tab.dataset.step) === step);
      });

      document.querySelectorAll('.mobile-dot').forEach(dot => {
        dot.classList.toggle('active', parseInt(dot.dataset.step) === step);
      });

      const badge = document.getElementById('mobileStepBadge');
      const stepName = document.getElementById('mobileStepName');
      const percent = document.getElementById('mobileStepPercent');
      const fill = document.getElementById('mobileProgressFill');
      const btnBack = document.getElementById('btnStepBack');
      const btnProceed = document.getElementById('btnProceedStep');

      const names = ['Dormitory Floor', 'Stay Dates', 'Lodging Details', 'Roster Documents', 'Review & Submit'];
      if (badge) badge.textContent = `Step ${step} of 5`;
      if (stepName) stepName.textContent = names[step - 1];
      const pct = (step / 5) * 100;
      if (percent) percent.textContent = `${pct}%`;
      if (fill) fill.style.width = `${pct}%`;

      if (btnBack) btnBack.style.display = step > 1 ? 'inline-flex' : 'none';
      if (btnProceed) {
        if (step === 5) {
          btnProceed.style.display = 'none';
        } else {
          btnProceed.style.display = 'inline-flex';
          const nextNames = ['Stay Dates', 'Lodging Details', 'Roster Documents', 'Review Summary'];
          btnProceed.querySelector('span').textContent = `Proceed to ${nextNames[step - 1]}`;
        }
      }

      window.scrollTo({ top: 120, behavior: 'smooth' });
    }

    document.getElementById('btnProceedStep')?.addEventListener('click', () => {
      setDormStep(currentDormStep + 1);
    });

    document.getElementById('btnStepBack')?.addEventListener('click', () => {
      setDormStep(currentDormStep - 1);
    });

    document.querySelectorAll('.step-tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        setDormStep(parseInt(btn.dataset.step));
      });
    });

    document.getElementById('btnSubmitDormitoryBooking')?.addEventListener('click', () => {
      alert('Official Dormitory Booking Request Submitted Successfully!\nYour request has been routed to the Dormitory Custodian for Bed Assignment.');
      window.location.href = 'booking_history.php';
    });

    function closeRoomModal() {
      document.getElementById('roomSelectionModal').style.display = 'none';
    }
  </script>
</body>

</html>