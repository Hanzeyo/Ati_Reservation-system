<?php
/**
 * Agriculture Training Institute - Facility Reservation System
 * Step-by-Step Facility Venue Reservation Portal (Function Hall, Training Halls, Boardrooms, Mess Hall)
 */
$reqCategory = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : 'halls';
if (in_array($reqCategory, ['dormitories', 'dorm', 'dorms', 'dormitory', 'lodging'])) {
    header("Location: dormitory_booking.php");
    exit;
}
$reqCategory = 'halls';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve ATI Venue & Facilities - Agricultural Training Institute</title>
  <meta name="description"
    content="Submit official reservation requests for ATI function halls, training venues, boardrooms, and dormitory suites.">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
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

        <!-- Account Settings Section -->
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
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </div>
            <span class="drawer-link-text">Settings</span>
          </a>

          <a href="admin_dashboard.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
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

  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;

      if (open) {
        overlay.style.display = 'block';
        void overlay.offsetHeight; // trigger reflow
        overlay.classList.add('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      } else {
        overlay.classList.remove('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        setTimeout(function () {
          if (!overlay.classList.contains('show')) {
            overlay.style.display = 'none';
          }
        }, 280);
      }
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var overlay = document.getElementById('mobileDrawerOverlay');
        if (overlay && overlay.classList.contains('show')) {
          toggleMobileDrawer(false);
        }
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 900) {
        var overlay = document.getElementById('mobileDrawerOverlay');
        if (overlay && overlay.classList.contains('show')) {
          toggleMobileDrawer(false);
        }
      }
    });
  </script>


  <!-- ==========================================================================
       MAIN RESERVATION WORKSPACE
       ========================================================================== -->
  <main class="booking-main-wrapper">
    <!-- Header Section (Without User Category / Rate Plan per instruction) -->
    <section class="booking-header-section">
      <div class="booking-assistant-pill">
        <span class="pulse-dot"></span>
        <span>Step-by-Step Booking Assistant</span>
      </div>
      <h2 class="booking-main-title">Reserve ATI Venue & Training Facilities</h2>
      <p class="booking-main-subtitle">
        Complete the required details below to submit an official reservation request for administrative approval.
      </p>
    </section>

    <!-- Stepper Navigation Bar (Desktop Grid + Compact Mobile Progress Bar) -->
    <div class="stepper-bar-card">
      <!-- Desktop Stepper (5 Tabs) -->
      <div class="stepper-grid">
        <div class="step-tab-btn active" data-step="1">
          <span class="step-number">1</span>
          <span>Facility</span>
        </div>
        <div class="step-tab-btn" data-step="2">
          <span class="step-number">2</span>
          <span>Date & Time</span>
        </div>
        <div class="step-tab-btn" data-step="3" id="stepTabEventDetails">
          <span class="step-number">3</span>
          <span id="stepTabEventName">Event Details</span>
        </div>
        <div class="step-tab-btn" data-step="4" id="stepTabDocuments">
          <span class="step-number">4</span>
          <span>Documents</span>
        </div>
        <div class="step-tab-btn" data-step="5" id="stepTabReview">
          <span class="step-number">5</span>
          <span>Review & Submit</span>
        </div>
      </div>

      <!-- Compact Mobile Stepper Indicator -->
      <div class="mobile-stepper-progress" id="mobileStepperProgress">
        <div class="mobile-stepper-header">
          <div class="mobile-step-pill">
            <span class="mobile-step-badge" id="mobileStepBadge">Step 1 of 5</span>
            <strong class="mobile-step-name" id="mobileStepName">Facility Selection</strong>
          </div>
          <span class="mobile-step-percent" id="mobileStepPercent">20%</span>
        </div>
        <div class="mobile-progress-track">
          <div class="mobile-progress-fill" id="mobileProgressFill" style="width: 20%;"></div>
        </div>
        <div class="mobile-step-dots">
          <div class="mobile-dot active" data-step="1" title="Facility">1</div>
          <div class="mobile-dot" data-step="2" title="Date & Time">2</div>
          <div class="mobile-dot" data-step="3" id="mobileDotEventDetails" title="Event Details">3</div>
          <div class="mobile-dot" data-step="4" id="mobileDotDocuments" title="Documents">4</div>
          <div class="mobile-dot" data-step="5" id="mobileDotReview" title="Review">5</div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 1: FACILITY SELECTION
         ========================================================================== -->
    <div class="wizard-step-view active" data-step="1">
      
      <!-- Facility Category Nav Toolbar (Return to Categories & Switch to Dorms) -->
      <div class="facility-category-nav-header" id="facilitiesSectionAnchor">
        <div class="category-nav-left">
          <a href="home.php" class="btn-return-home" title="Back to Portal Selection">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>Back to Portal Home</span>
          </a>
          <span class="active-category-indicator" id="activeCategoryBadge">
            <span class="indicator-dot"></span>
            <span id="activeCategoryName">Halls &amp; Venues (6 Available)</span>
          </span>
        </div>

        <div class="filter-pills-list">
          <button type="button" class="filter-pill active" id="btnFilterHalls" data-category-filter="halls">Halls &amp; Venues (6)</button>
          <a href="dormitory_booking.php" class="filter-pill" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;" title="Switch to Dormitory Lodging Booking Portal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"></path><path d="M2 8h18a2 2 0 0 1 2 2v10"></path><path d="M2 17h20"></path></svg>
            <span>Book Dormitory Lodging &rarr;</span>
          </a>
        </div>
      </div>

      <!-- Active Section Title -->
      <div class="facility-header-status-row">
        <div class="filter-label-group">
          <svg viewBox="0 0 24 24" fill="none">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          <span id="categorySectionHeading">AVAILABLE FACILITY VENUES &amp; HALLS (6):</span>
        </div>
        <p class="select-instruction-text">Select one facility card below to proceed with your venue reservation.</p>
      </div>

      <!-- Facility Cards Grid -->
      <div class="facility-selection-grid" id="facilitySelectionGrid">
        <!-- =================== 1. HALLS CATEGORY (ALL 6 VENUES IN ORDER) =================== -->
        <!-- Hall 1: Serrano Function Hall -->
        <article class="facility-choice-card selected" data-id="function-hall" data-name="Serrano Function Hall"
          data-rate="₱5,000/day" data-capacity="150 - 200 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/function_hall.jpg" alt="Serrano Function Hall" loading="lazy">
            <span class="facility-cap-badge">150 - 200 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">LARGE EVENT AUDITORIUM &bull; HALL 1</span>
            <h3 class="facility-title">Serrano Function Hall</h3>
            <div class="facility-rate-tag">Standard Rate: ₱5,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Central Aircon</span>
              <span class="amenity-pill">PA Sound System</span>
              <span class="amenity-pill">Dual Laser Projectors</span>
              <span class="amenity-pill">Stage Rostrum</span>
              <span class="amenity-pill">VIP Waiting Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Theater Setup Selected (200 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 2: Training Hall A -->
        <article class="facility-choice-card" data-id="training-hall-a" data-name="Training Hall A"
          data-rate="₱3,000/day" data-capacity="50 - 80 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/training_hall.jpg" alt="Training Hall A" loading="lazy">
            <span class="facility-cap-badge">50 - 80 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">INTERACTIVE TRAINING &bull; HALL 2</span>
            <h3 class="facility-title">Training Hall A</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Modular Desks</span>
              <span class="amenity-pill">75" 4K Smart Display</span>
              <span class="amenity-pill">High-Speed Wifi</span>
              <span class="amenity-pill">Breakout Stations</span>
              <span class="amenity-pill">Whiteboards</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Modular Pods Selected (60 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 3: Training Hall B (Agri-Fisheries) -->
        <article class="facility-choice-card" data-id="training-hall-b" data-name="Training Hall B (Agri-Fisheries)"
          data-rate="₱3,000/day" data-capacity="50 - 80 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/training_hall.jpg" alt="Training Hall B (Agri-Fisheries)" loading="lazy">
            <span class="facility-cap-badge">50 - 80 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">TECHNICAL TRAINING &bull; HALL 3</span>
            <h3 class="facility-title">Training Hall B (Agri-Fisheries)</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Workshop Benches</span>
              <span class="amenity-pill">HD Projection System</span>
              <span class="amenity-pill">Demonstration Corner</span>
              <span class="amenity-pill">Airconditioned</span>
              <span class="amenity-pill">Dedicated LAN Ports</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Workshop Setup Selected (50 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 4: 4-H Learning Center (Multi-Purpose Hall) -->
        <article class="facility-choice-card" data-id="four-h-center" data-name="4-H Learning Center"
          data-rate="₱3,000/day" data-capacity="60 - 80 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/training_hall.jpg" alt="4-H Learning Center" loading="lazy">
            <span class="facility-cap-badge">60 - 80 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">MULTI-PURPOSE CENTER &bull; HALL 4</span>
            <h3 class="facility-title">4-H Learning Center</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Moveable Modular Tables</span>
              <span class="amenity-pill">75" Smart Interactive TV</span>
              <span class="amenity-pill">Digital Agri Display</span>
              <span class="amenity-pill">Public Address System</span>
              <span class="amenity-pill">High-Speed Wi-Fi</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Multi-purpose Setup Selected (70 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 5: Executive Boardroom -->
        <article class="facility-choice-card" data-id="executive-boardroom" data-name="Executive Boardroom"
          data-rate="₱2,500/day" data-capacity="20 - 30 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" loading="lazy">
            <span class="facility-cap-badge">20 - 30 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">VIP CONFERENCE SUITE &bull; HALL 5</span>
            <h3 class="facility-title">Executive Boardroom</h3>
            <div class="facility-rate-tag">Standard Rate: ₱2,500/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Executive Leather Chairs</span>
              <span class="amenity-pill">Hybrid Teleconference Cam</span>
              <span class="amenity-pill">Acoustic Wall Paneling</span>
              <span class="amenity-pill">Coffee Station</span>
              <span class="amenity-pill">Private Restroom</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Board Table Selected (25 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 6: ATI Mess Hall & Dining Pavilion -->
        <article class="facility-choice-card" data-id="mess-hall" data-name="ATI Mess Hall &amp; Dining Pavilion"
          data-rate="₱3,500/day" data-capacity="100 - 150 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/mess_hall.jpg" alt="ATI Mess Hall & Dining Pavilion" loading="lazy">
            <span class="facility-cap-badge">100 - 150 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">DINING &amp; BANQUET COMPLEX &bull; HALL 6</span>
            <h3 class="facility-title">ATI Mess Hall &amp; Dining Pavilion</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,500/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Buffet Serving Counters</span>
              <span class="amenity-pill">Commercial Kitchen Access</span>
              <span class="amenity-pill">Washing Station</span>
              <span class="amenity-pill">Outdoor Patio Deck</span>
              <span class="amenity-pill">Filtered Water Stations</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Full Buffet Selected (100 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

      </div>

      <!-- Step 1 Reservation Summary Preview (Visible before proceeding) -->
      <div class="step1-selection-summary-card" id="step1SummaryCard">
        <div class="summary-card-header">
          <div class="sch-left">
            <span class="sch-badge" id="summaryPreviewBadge">SELECTED VENUE</span>
            <h4 class="sch-title" id="summaryPreviewTitle">Function Hall</h4>
          </div>
          <span class="sch-rate-pill" id="summaryPreviewRate">₱5,000 / day</span>
        </div>
        <div class="summary-card-body">
          <div class="scb-item" id="summaryPreviewRoomWrap" style="display: none;">
            <span class="scb-label" id="summaryPreviewUnitLabel">Assigned Setup / Room:</span>
            <strong class="scb-val" id="summaryPreviewRoom">None</strong>
          </div>
          <div class="scb-item">
            <span class="scb-label">Capacity / Guests:</span>
            <strong class="scb-val" id="summaryPreviewCap">150 - 200 PAX</strong>
          </div>
          <div class="scb-item">
            <span class="scb-label">Next Action:</span>
            <strong class="scb-val" style="color: #175432;">Review summary &amp; click Proceed below</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 2: DATE & TIME
         ========================================================================== -->
    <div class="wizard-step-view" data-step="2">
      <div class="date-time-wizard-card">
        <h3 class="date-time-card-title">Select Dates & Duration</h3>
        <p class="date-time-card-desc">Choose your start/end dates and check for scheduling availability conflicts.</p>

        <div class="date-time-layout-grid">
          <!-- Left Column: Interactive Slot Check -->
          <div class="interactive-slot-box">
            <div class="slot-box-header">
              <div class="slot-box-header-left">
                <span class="slot-box-title">Availability Slot Check</span>
                <div class="slot-mode-selector" id="slotModeSelector">
                  <button type="button" class="slot-mode-btn active" id="btnModeSingle"
                    title="Select single day reservation">Single Day</button>
                  <button type="button" class="slot-mode-btn" id="btnModeRange" title="Select multi-day range">Multi-Day
                    Range</button>
                </div>
              </div>
              <div class="slot-month-nav" id="slotMonthNav">
                <button type="button" class="slot-month-nav-btn" id="btnPrevMonth" title="Previous Month" aria-label="Previous Month" disabled>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <span class="slot-month-pill" id="slotMonthDisplay">October 2026</span>
                <button type="button" class="slot-month-nav-btn" id="btnNextMonth" title="Next Month" aria-label="Next Month">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
              </div>
            </div>

            <div class="slot-calendar-wrapper">
              <div class="slot-weekdays-row">
                <span>Sun</span>
                <span>Mon</span>
                <span>Tue</span>
                <span>Wed</span>
                <span>Thu</span>
                <span>Fri</span>
                <span>Sat</span>
              </div>

              <div class="slot-days-grid" id="slotDaysGrid">
                <!-- October 2026 starts on Thursday -->
                <div class="slot-day-btn empty"></div>
                <div class="slot-day-btn empty"></div>
                <div class="slot-day-btn empty"></div>
                <div class="slot-day-btn empty"></div>
                <button type="button" class="slot-day-btn past-date" data-day="1" title="Unavailable" disabled>1</button>
                <button type="button" class="slot-day-btn past-date" data-day="2" title="Unavailable" disabled>2</button>
                <button type="button" class="slot-day-btn past-date" data-day="3" title="Unavailable" disabled>3</button>

                <button type="button" class="slot-day-btn past-date" data-day="4" title="Unavailable" disabled>4</button>
                <button type="button" class="slot-day-btn selected today" data-day="5" title="Today: October 5, 2026 (Selected)">5</button>
                <button type="button" class="slot-day-btn available" data-day="6">6</button>
                <button type="button" class="slot-day-btn available" data-day="7">7</button>
                <button type="button" class="slot-day-btn available" data-day="8">8</button>
                <button type="button" class="slot-day-btn available" data-day="9">9</button>
                <button type="button" class="slot-day-btn available" data-day="10">10</button>

                <button type="button" class="slot-day-btn available" data-day="11">11</button>
                <button type="button" class="slot-day-btn reserved" data-day="12">12</button>
                <button type="button" class="slot-day-btn available" data-day="13">13</button>
                <button type="button" class="slot-day-btn available" data-day="14">14</button>
                <button type="button" class="slot-day-btn available" data-day="15">15</button>
                <button type="button" class="slot-day-btn available" data-day="16">16</button>
                <button type="button" class="slot-day-btn available" data-day="17">17</button>

                <button type="button" class="slot-day-btn available" data-day="18">18</button>
                <button type="button" class="slot-day-btn reserved" data-day="19">19</button>
                <button type="button" class="slot-day-btn available" data-day="20">20</button>
                <button type="button" class="slot-day-btn available" data-day="21">21</button>
                <button type="button" class="slot-day-btn available" data-day="22">22</button>
                <button type="button" class="slot-day-btn available" data-day="23">23</button>
                <button type="button" class="slot-day-btn available" data-day="24">24</button>

                <button type="button" class="slot-day-btn available" data-day="25">25</button>
                <button type="button" class="slot-day-btn available" data-day="26">26</button>
                <button type="button" class="slot-day-btn reserved" data-day="27">27</button>
                <button type="button" class="slot-day-btn available" data-day="28">28</button>
                <button type="button" class="slot-day-btn suspended" data-day="29"
                  title="Suspended: Facility Maintenance">29</button>
                <button type="button" class="slot-day-btn available" data-day="30">30</button>
                <button type="button" class="slot-day-btn available" data-day="31">31</button>
              </div>
            </div>

            <!-- Legend with Available, Reserved, Suspended, Selected (Past Date text removed per request) -->
            <div class="slot-legend-row">
              <div class="slot-legend-tag">
                <span class="legend-circle available"></span>
                <span>Available</span>
              </div>
              <div class="slot-legend-tag">
                <span class="legend-circle reserved"></span>
                <span>Reserved</span>
              </div>
              <div class="slot-legend-tag">
                <span class="legend-circle suspended"></span>
                <span>Suspended</span>
              </div>
              <div class="slot-legend-tag">
                <span class="legend-circle selected"></span>
                <span>Selected</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Date & Time Form Inputs -->
          <div class="date-time-form-col">
            <!-- Start Date -->
            <div class="dt-field-group">
              <label class="dt-field-label" for="startDateInput">START DATE</label>
              <div class="dt-input-icon-wrap">
                <input type="text" id="startDateInput" value="10/05/2026">
                <svg class="dt-calendar-svg" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
            </div>

            <!-- End Date -->
            <div class="dt-field-group">
              <label class="dt-field-label" for="endDateInput">END DATE</label>
              <div class="dt-input-icon-wrap" id="endDateWrap">
                <input type="text" id="endDateInput" value="10/05/2026">
                <svg class="dt-calendar-svg" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
            </div>

            <!-- Time Window Row -->
            <div class="dt-time-row">
              <div class="dt-field-group">
                <label class="dt-field-label" for="startTimeSelect">START TIME</label>
                <div class="dt-select-wrap">
                  <select id="startTimeSelect">
                    <option value="08:00 AM (Morning)">08:00 AM (Morning)</option>
                    <option value="09:00 AM">09:00 AM</option>
                    <option value="01:00 PM">01:00 PM (Afternoon)</option>
                    <option value="06:00 PM">06:00 PM (Evening)</option>
                  </select>
                  <svg class="dt-select-arrow" viewBox="0 0 24 24" fill="none">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>

              <div class="dt-field-group">
                <label class="dt-field-label" for="endTimeSelect">END TIME</label>
                <div class="dt-select-wrap">
                  <select id="endTimeSelect">
                    <option value="05:00 PM (Full Day)">05:00 PM (Full Day)</option>
                    <option value="12:00 PM">12:00 PM (Half Day)</option>
                    <option value="06:00 PM">06:00 PM</option>
                    <option value="09:00 PM">09:00 PM (Extended)</option>
                  </select>
                  <svg class="dt-select-arrow" viewBox="0 0 24 24" fill="none">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Slot Status Banner -->
            <div class="slot-available-banner" id="slotStatusBanner">
              <div class="banner-check-circle">
                <svg viewBox="0 0 24 24" fill="none">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div class="banner-content-text">
                <h4 id="slotStatusTitle">Selected Slot Available!</h4>
                <p id="slotStatusDesc">No venue conflicts detected for 1 day duration.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 3: EVENT DETAILS
         ========================================================================== -->
    <div class="wizard-step-view" data-step="3">
      <div class="wizard-form-card">
        <h3 class="wizard-form-title" id="step3FormTitle">Step 3: Event &amp; Activity Information</h3>
        <p class="wizard-form-desc" id="step3FormDesc">Provide details regarding the nature of your activity, participants, and specific requirements.</p>

        <div class="auth-field-group">
          <label class="auth-field-label" for="eventTitleInput" id="eventTitleLabel">Activity / Event Title</label>
          <input type="text" id="eventTitleInput" class="auth-input"
            placeholder="e.g. Regional Agricultural Extension Coordinators Training 2026" style="padding-left: 1rem;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
          <div class="auth-field-group">
            <label class="auth-field-label" for="eventPaxInput" id="eventPaxLabel">Estimated Number of Attendees</label>
            <input type="number" id="eventPaxInput" class="auth-input" placeholder="e.g. 120"
              style="padding-left: 1rem;">
          </div>
          <div class="auth-field-group">
            <label class="auth-field-label" for="divisionInput">Requesting Division / Unit</label>
            <input type="text" id="divisionInput" class="auth-input" value="Career Development Division (CDD)"
              style="padding-left: 1rem;">
          </div>
        </div>

        <div class="auth-field-group">
          <label class="auth-field-label" for="specialNotes" id="specialNotesLabel">Special Equipment / Setup Notes (Optional)</label>
          <textarea id="specialNotes" class="auth-input" rows="3"
            placeholder="e.g. Needs 4 wireless microphones, podium banner stand, and registration tables."
            style="padding: 0.8rem 1rem; resize: vertical;"></textarea>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 4: DOCUMENTS
         ========================================================================== -->
    <div class="wizard-step-view" data-step="4">
      <div class="wizard-form-card">
        <h3 class="wizard-form-title">Step 4: Supporting Documents Upload</h3>
        <p class="wizard-form-desc">Attach approved Special Order, Activity Design, or official request endorsement
          memo.</p>

        <div
          style="border: 2px dashed #b8ccbe; border-radius: 12px; padding: 3rem 2rem; text-align: center; background: #fbfdfc; cursor: pointer;">
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="1.8"
            style="margin-bottom: 0.75rem;">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="17 8 12 3 7 8"></polyline>
            <line x1="12" y1="3" x2="12" y2="15"></line>
          </svg>
          <h4 style="font-size: 1.1rem; color: #174d2f; margin-bottom: 0.35rem;">Click or Drag & Drop File Here</h4>
          <p style="font-size: 0.85rem; color: #627b6c;">PDF, DOCX, or PNG formats up to 15MB</p>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 5: REVIEW & SUBMIT
         ========================================================================== -->
    <div class="wizard-step-view" data-step="5">
      <div class="wizard-form-card">
        <h3 class="wizard-form-title">Step 5: Review & Submit Official Request</h3>
        <p class="wizard-form-desc">Please verify your reservation particulars before final submission to the
          administrative approving authority.</p>

        <!-- Step 5 Official Booking Summary (Visible before final submission) -->
        <div class="review-official-summary-card">
          <div class="summary-card-header">
            <div class="sch-left">
              <span class="sch-badge" id="summaryOfficialBadge">OFFICIAL FACILITY BOOKING PARTICULARS</span>
              <h4 class="sch-title" id="summaryVenueName">Serrano Function Hall</h4>
            </div>
            <span class="sch-rate-pill" id="summaryVenueRate">₱5,000 / day</span>
          </div>

          <div class="summary-card-body" style="margin-bottom: 1.25rem;">
            <div class="scb-item" id="summaryRoomDetailWrap">
              <span class="scb-label" id="summaryRoomDetailLabel">Assigned Venue Setup:</span>
              <strong class="scb-val" id="summaryRoomDetail">Theater Setup (200 PAX)</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Venue Location &amp; Type:</span>
              <strong class="scb-val" id="summaryFacilityType">Main Administration Building &bull; Event Auditorium</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Capacity / Attendees:</span>
              <strong class="scb-val" id="summaryVenueCapacity">150 - 200 PAX</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Reserved Schedule:</span>
              <strong class="scb-val" id="summaryReservationDate">10/05/2026</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Time Window:</span>
              <strong class="scb-val" id="summaryTimeSlot">Whole Day (8:00 AM - 5:00 PM)</strong>
            </div>
          </div>

          <div style="font-size: 0.88rem; color: #435b4d; border-top: 1px solid #e1ece4; padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <span>Requested by: <strong>Juan Dela Cruz</strong> (ATI Staff, CDD)</span>
            <span class="status-pill-badge" style="background: #fef3c7; color: #92400e; font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 999px;">Pending Administrative Review</span>
          </div>
        </div>

        <button type="button" class="btn-proceed-step" id="btnSubmitFinalReservation"
          style="width: 100%; justify-content: center; border-radius: 10px; padding: 1rem;">
          <svg viewBox="0 0 24 24" fill="none">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Submit Official Facility Booking Request</span>
        </button>
      </div>
    </div>

    <!-- Bottom Step Navigation Actions (Always present across all steps) -->
    <div class="booking-bottom-actions" id="bookingBottomActions">
      <button type="button" class="btn-step-back" id="btnStepBack" style="display: none;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Back</span>
      </button>

      <button type="button" class="btn-proceed-step" id="btnProceedStep">
        <span>Proceed to Date & Time Selection</span>
        <svg viewBox="0 0 24 24" fill="none">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>
    </div>
  
  <!-- ==========================================================================
       VENUE SETUP & FLOOR PLAN SELECTION MODAL POPUP
       ========================================================================== -->
  <div class="room-modal-overlay" id="roomSelectionModal" style="display: none;">
    <div class="room-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalDormTitle">
      
      <!-- Modal Header -->
      <div class="room-modal-header">
        <div class="room-modal-header-info">
          <div class="modal-badge-row">
            <span class="modal-dorm-pill-tag" id="modalCategoryPill">VENUE SETUP &amp; FLOOR PLAN SELECTION</span>
            <span id="modalDormRate" class="modal-dorm-rate-pill">₱5,000 / day</span>
          </div>
          <h3 id="modalDormTitle" class="modal-dorm-title">Serrano Function Hall</h3>
          <p id="modalDormFloor" class="modal-dorm-desc">Main Administration Building • Ground Floor &bull; Flagship multi-purpose event auditorium.</p>
        </div>
        <button type="button" class="room-modal-close-btn" id="btnModalClose" aria-label="Close Selection">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Modal Legend -->
      <div class="room-modal-legend">
        <div class="legend-item">
          <span class="legend-color-dot available"></span>
          <span id="modalLegendAvailText"><strong>Available:</strong> Click to assign layout setup</span>
        </div>
        <div class="legend-item">
          <span class="legend-color-dot reserved"></span>
          <span id="modalLegendResText"><strong>Reserved:</strong> Layout setup reserved for existing booking</span>
        </div>
      </div>

      <!-- Modal Body: Interactive Layout Grid -->
      <div class="room-modal-body">
        <div class="modal-rooms-grid hall-layout-mode" id="modalRoomsGrid">
          <!-- Dynamically populated 6 hall layout setup cards -->
        </div>

        <!-- Feedback Bar inside modal -->
        <div class="modal-room-feedback-bar" id="modalRoomFeedback" style="display: none;">
          <div class="modal-feedback-left">
            <div class="mf-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div>
              <div class="mf-title" id="modalFeedbackTitle">Theater Setup Selected ✓</div>
              <div class="mf-sub" id="modalFeedbackSub">Serrano Function Hall (₱5,000 / day) &bull; Layout Confirmed</div>
            </div>
          </div>
          <span class="badge-assigned-ok">✓ Setup Confirmed</span>
        </div>
      </div>
    </div>
  </div>
</main>

  <script src="js/booking.js?v=<?php echo time(); ?>"></script>
  <script src="js/user_settings.js?v=<?php echo time(); ?>"></script>
</body>

</html>