<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Step-by-Step Facility Booking Portal
 */
$reqCategory = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : 'halls';
if (!in_array($reqCategory, ['halls', 'dormitories', 'all'])) {
  $reqCategory = 'halls';
}
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

        <!-- New Reservation (Active) -->
        <a href="booking.php" class="booking-nav-item active">
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
              <div class="dropdown-user-email"><span class="user-verified-dot"></span>
                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role']))) ?></div>
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

          <a href="booking.php" class="drawer-nav-link active">
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

          <a href="my_reservations.php?type=facility" class="drawer-nav-link">
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

      <!-- Facility Category Nav Toolbar (Return to Categories & Switch Pills) -->
      <div class="facility-category-nav-header" id="facilitiesSectionAnchor">
        <div class="category-nav-left">
          <a href="home.php" class="btn-return-home" title="Back to Category Selection">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>Change Category (Halls / Dorms)</span>
          </a>
          <span class="active-category-indicator" id="activeCategoryBadge">
            <span class="indicator-dot"></span>
            <span id="activeCategoryName"><?php
            if ($reqCategory === 'dormitories')
              echo 'Dormitories (6 Floors)';
            elseif ($reqCategory === 'halls')
              echo 'Halls (4 Available)';
            else
              echo 'All Facilities (10 Total)';
            ?></span>
          </span>
        </div>

        <div class="filter-pills-list">
          <button type="button" class="filter-pill <?php echo $reqCategory === 'halls' ? 'active' : ''; ?>"
            id="btnFilterHalls" data-category-filter="halls">Halls (4)</button>
          <button type="button" class="filter-pill <?php echo $reqCategory === 'dormitories' ? 'active' : ''; ?>"
            id="btnFilterDormitories" data-category-filter="dormitories">Dormitories (6)</button>
          <button type="button" class="filter-pill <?php echo $reqCategory === 'all' ? 'active' : ''; ?>"
            id="btnFilterAll" data-category-filter="all">Show All (10)</button>
        </div>
      </div>

      <!-- Active Section Title -->
      <div class="facility-header-status-row">
        <div class="filter-label-group">
          <svg viewBox="0 0 24 24" fill="none">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          <span id="categorySectionHeading"><?php
          if ($reqCategory === 'dormitories')
            echo 'AVAILABLE DORMITORY FLOORS (6):';
          elseif ($reqCategory === 'halls')
            echo 'AVAILABLE HALLS & VENUES (4):';
          else
            echo 'ALL AVAILABLE FACILITIES & ROOMS (10):';
          ?></span>
        </div>
        <p class="select-instruction-text">Select one facility card below to proceed with your booking.</p>
      </div>

      <!-- Facility Cards Grid -->
      <div class="facility-selection-grid" id="facilitySelectionGrid">
        <!-- =================== 1. HALLS CATEGORY (4 VENUES) =================== -->
        <!-- Hall 1: Function Hall -->
        <article class="facility-choice-card <?php echo $reqCategory === 'halls' ? 'selected' : ''; ?>"
          data-id="function-hall" style="<?php echo $reqCategory === 'dormitories' ? 'display: none;' : ''; ?>"
          data-name="Function Hall" data-rate="₱5,000/day" data-capacity="150 - 200 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/function_hall.jpg" alt="Function Hall" loading="lazy">
            <span class="facility-cap-badge">150 - 200 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">LARGE EVENT VENUE &bull; HALLS</span>
            <h3 class="facility-title">Function Hall</h3>
            <div class="facility-rate-tag">Standard Rate: ₱5,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Central Aircon</span>
              <span class="amenity-pill">PA Sound System</span>
              <span class="amenity-pill">HD Projector</span>
              <span class="amenity-pill">Stage Setup</span>
              <span class="amenity-pill">VIP Waiting Lounge</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Theater Setup Selected (200 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 2: Training Hall A -->
        <article class="facility-choice-card" data-id="training-hall-a"
          style="<?php echo $reqCategory === 'dormitories' ? 'display: none;' : ''; ?>" data-name="Training Hall A"
          data-rate="₱3,000/day" data-capacity="50 - 80 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/training_hall.jpg" alt="Training Hall A" loading="lazy">
            <span class="facility-cap-badge">50 - 80 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">INTERACTIVE TRAINING &bull; HALLS</span>
            <h3 class="facility-title">Training Hall A</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Modular Desks</span>
              <span class="amenity-pill">Smart Display</span>
              <span class="amenity-pill">High-Speed Wifi</span>
              <span class="amenity-pill">Breakout Corners</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Modular Pods Selected (60 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 3: Mess Hall & Dining Area -->
        <article class="facility-choice-card" data-id="mess-hall" data-name="Mess Hall & Dining Area"
          data-rate="₱3,500/day" data-capacity="100 PAX DINING" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/mess_hall.jpg" alt="Mess Hall & Dining Area" loading="lazy">
            <span class="facility-cap-badge">100 PAX DINING</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">DINING & CATERING &bull; HALLS</span>
            <h3 class="facility-title">Mess Hall & Dining Area</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,500/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Buffet Counters</span>
              <span class="amenity-pill">Kitchen Access</span>
              <span class="amenity-pill">Washing Station</span>
              <span class="amenity-pill">Outdoor Patio Deck</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Full Buffet Selected (100 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- Hall 4: Executive Boardroom -->
        <article class="facility-choice-card" data-id="executive-boardroom"
          style="<?php echo $reqCategory === 'dormitories' ? 'display: none;' : ''; ?>" data-name="Executive Boardroom"
          data-rate="₱2,500/day" data-capacity="20 - 30 PAX" data-facility-type="halls">
          <div class="facility-card-image">
            <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" loading="lazy">
            <span class="facility-cap-badge">20 - 30 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">VIP CONFERENCE ROOM &bull; HALLS</span>
            <h3 class="facility-title">Executive Boardroom</h3>
            <div class="facility-rate-tag">Standard Rate: ₱2,500/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Executive Leather Chairs</span>
              <span class="amenity-pill">Video Conference Cam</span>
              <span class="amenity-pill">Coffee Machine</span>
              <span class="amenity-pill">Acoustic Wall Paneling</span>
            </div>
            <div class="dorm-card-selected-room-badge hall-selected-setup-badge" style="display: none;">
              <span class="d-room-text">✓ Board Table Selected (25 PAX)</span>
            </div>
            <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
              </svg>
              Select Setup &amp; View Floor Plan
            </button>
          </div>
        </article>

        <!-- =================== 2. DORMITORIES CATEGORY (6 ORDERED FLOORS: PHILIPPINE FLOWERS) =================== -->
        <!-- 1st Floor: Sampaguita Dormitory -->
        <article
          class="facility-choice-card dorm-suite-card <?php echo $reqCategory === 'dormitories' ? 'selected' : ''; ?>"
          data-id="dorm-floor-1" data-name="1st Floor: Sampaguita Dormitory" data-rate="₱500/night"
          data-capacity="12 ROOMS (48 BEDS)" data-facility-type="dormitories"
          style="<?php echo $reqCategory === 'halls' ? 'display: none;' : ''; ?>">
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
          data-facility-type="dormitories" style="<?php echo $reqCategory === 'halls' ? 'display: none;' : ''; ?>">
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
          data-facility-type="dormitories" style="<?php echo $reqCategory === 'halls' ? 'display: none;' : ''; ?>">
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
          data-facility-type="dormitories" style="<?php echo $reqCategory === 'halls' ? 'display: none;' : ''; ?>">
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
          data-facility-type="dormitories" style="<?php echo $reqCategory === 'halls' ? 'display: none;' : ''; ?>">
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
          data-facility-type="dormitories" style="<?php echo $reqCategory === 'halls' ? 'display: none;' : ''; ?>">
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
                <button type="button" class="slot-month-nav-btn" id="btnPrevMonth" title="Previous Month"
                  aria-label="Previous Month" disabled>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"></polyline>
                  </svg>
                </button>
                <span class="slot-month-pill" id="slotMonthDisplay"><?= date('F Y') ?></span>
                <button type="button" class="slot-month-nav-btn" id="btnNextMonth" title="Next Month"
                  aria-label="Next Month">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
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
                <button type="button" class="slot-day-btn past-date" data-day="1" title="Unavailable"
                  disabled>1</button>
                <button type="button" class="slot-day-btn past-date" data-day="2" title="Unavailable"
                  disabled>2</button>
                <button type="button" class="slot-day-btn past-date" data-day="3" title="Unavailable"
                  disabled>3</button>

                <button type="button" class="slot-day-btn past-date" data-day="4" title="Unavailable"
                  disabled>4</button>
                <button type="button" class="slot-day-btn selected today" data-day="<?= date('j') ?>"
                  title="Today: <?= date('F j, Y') ?> (Selected)"><?= date('j') ?></button>
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
                <input type="text" id="startDateInput" value="<?= date('m/d/Y') ?>">
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
                <input type="text" id="endDateInput" value="<?= date('m/d/Y') ?>">
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
        <p class="wizard-form-desc" id="step3FormDesc">Provide details regarding the nature of your activity,
          participants, and specific requirements.</p>

        <div class="auth-field-group">
          <label class="auth-field-label" for="eventTitleInput" id="eventTitleLabel">Activity / Event Title <span style="color: #dc2626;">*</span></label>
          <input type="text" id="eventTitleInput" class="auth-input"
            placeholder="e.g. Regional Agricultural Extension Coordinators Training 2026" style="padding-left: 1rem;" required>
          <small class="field-error-msg" id="eventTitleError" style="display: none; color: #dc2626; font-size: 0.78rem; font-weight: 600; margin-top: 0.35rem;">⚠️ Please enter the activity / event title (minimum 3 characters).</small>
        </div>

        <div style="display: grid; grid-template-columns: minmax(160px, 200px) minmax(280px, 420px); gap: 1.25rem; margin-bottom: 1.15rem; align-items: start;">
          <div class="auth-field-group" style="margin-bottom: 0;">
            <label class="auth-field-label" for="eventPaxInput" id="eventPaxLabel">Estimated Attendees <span style="color: #dc2626;">*</span></label>
            <input type="number" id="eventPaxInput" class="auth-input" placeholder="e.g. 120" min="1" max="1000"
              style="padding: 0.55rem 0.85rem; font-size: 0.88rem; border-radius: 8px;" required>
            <small class="field-error-msg" id="eventPaxError" style="display: none; color: #dc2626; font-size: 0.78rem; font-weight: 600; margin-top: 0.35rem;">⚠️ Please specify at least 1 attendee.</small>
          </div>
          <div class="auth-field-group" style="margin-bottom: 0;">
            <label class="auth-field-label" for="deptSelectTrigger">Requesting Division / Unit *</label>
            <div class="dept-select-wrap" id="hallDeptSelectWrap">
              <button type="button" class="dept-select-trigger" id="deptSelectTrigger" aria-haspopup="listbox" aria-expanded="false" style="padding: 0.55rem 0.85rem;">
                <span class="dept-trigger-text" id="deptTriggerText">Career Development Division (CDD)</span>
                <svg class="dept-select-arrow" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <div class="dept-dropdown-panel" id="deptDropdownPanel">
                <div class="dept-search-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                  <input type="text" class="dept-search-input" id="deptSearchInput" placeholder="Quick search division, bureau, agency..." autocomplete="off">
                </div>

                <div class="dept-options-scroll" id="deptOptionsScroll" role="listbox">
                  <div class="dept-group-title">Agricultural Training Institute (ATI)</div>
                  <div class="dept-option-item selected" data-value="Career Development Division (CDD)">Career Development Division (CDD)</div>
                  <div class="dept-option-item" data-value="Partnerships and Accreditation Division (PAD)">Partnerships and Accreditation Division (PAD)</div>
                  <div class="dept-option-item" data-value="Information Services Division (ISD)">Information Services Division (ISD)</div>
                  <div class="dept-option-item" data-value="Policy and Planning Division (PPD)">Policy and Planning Division (PPD)</div>
                  <div class="dept-option-item" data-value="Administrative and Finance Division (AFD / AFU)">Administrative and Finance Division (AFD / AFU)</div>
                  <div class="dept-option-item" data-value="Office of the Director (OD)">Office of the Director (OD)</div>
                  <div class="dept-option-item" data-value="Office of the Assistant Director (OAD)">Office of the Assistant Director (OAD)</div>
                  <div class="dept-option-item" data-value="ATI-RTC CAR (Cordillera Administrative Region)">ATI-RTC CAR (Cordillera Administrative Region)</div>
                  <div class="dept-option-item" data-value="ATI-RTC I (Ilocos Region)">ATI-RTC I (Ilocos Region)</div>
                  <div class="dept-option-item" data-value="ATI-RTC II (Cagayan Valley)">ATI-RTC II (Cagayan Valley)</div>
                  <div class="dept-option-item" data-value="ATI-RTC III (Central Luzon)">ATI-RTC III (Central Luzon)</div>
                  <div class="dept-option-item" data-value="ATI-RTC IV-A (CALABARZON)">ATI-RTC IV-A (CALABARZON)</div>
                  <div class="dept-option-item" data-value="ATI-RTC IV-B (MIMAROPA)">ATI-RTC IV-B (MIMAROPA)</div>
                  <div class="dept-option-item" data-value="ATI-RTC V (Bicol Region)">ATI-RTC V (Bicol Region)</div>
                  <div class="dept-option-item" data-value="ATI-RTC VI (Western Visayas)">ATI-RTC VI (Western Visayas)</div>
                  <div class="dept-option-item" data-value="ATI-RTC VII (Central Visayas)">ATI-RTC VII (Central Visayas)</div>
                  <div class="dept-option-item" data-value="ATI-RTC VIII (Eastern Visayas)">ATI-RTC VIII (Eastern Visayas)</div>
                  <div class="dept-option-item" data-value="ATI-RTC IX (Zamboanga Peninsula)">ATI-RTC IX (Zamboanga Peninsula)</div>
                  <div class="dept-option-item" data-value="ATI-RTC X (Northern Mindanao)">ATI-RTC X (Northern Mindanao)</div>
                  <div class="dept-option-item" data-value="ATI-RTC XI (Davao Region)">ATI-RTC XI (Davao Region)</div>
                  <div class="dept-option-item" data-value="ATI-RTC XII (SOCCSKSARGEN)">ATI-RTC XII (SOCCSKSARGEN)</div>
                  <div class="dept-option-item" data-value="ATI-RTC XIII (Caraga Region)">ATI-RTC XIII (Caraga Region)</div>
                  <div class="dept-option-item" data-value="ATI-ITCPH (International Training Center on Pig Husbandry)">ATI-ITCPH (International Training Center on Pig Husbandry)</div>

                  <div class="dept-group-title">Department of Agriculture (DA) &amp; Sister Agencies</div>
                  <div class="dept-option-item" data-value="DA – Office of the Secretary (OSEC)">DA – Office of the Secretary (OSEC)</div>
                  <div class="dept-option-item" data-value="BAI – Bureau of Animal Industry">BAI – Bureau of Animal Industry</div>
                  <div class="dept-option-item" data-value="BAR – Bureau of Agricultural Research">BAR – Bureau of Agricultural Research</div>
                  <div class="dept-option-item" data-value="BAFS – Bureau of Agriculture and Fisheries Standards">BAFS – Bureau of Agriculture and Fisheries Standards</div>
                  <div class="dept-option-item" data-value="BFAR – Bureau of Fisheries and Aquatic Resources">BFAR – Bureau of Fisheries and Aquatic Resources</div>
                  <div class="dept-option-item" data-value="BPI – Bureau of Plant Industry">BPI – Bureau of Plant Industry</div>
                  <div class="dept-option-item" data-value="BSWM – Bureau of Soils and Water Management">BSWM – Bureau of Soils and Water Management</div>
                  <div class="dept-option-item" data-value="ACPC – Agricultural Credit Policy Council">ACPC – Agricultural Credit Policy Council</div>
                  <div class="dept-option-item" data-value="FPA – Fertilizer and Pesticide Authority">FPA – Fertilizer and Pesticide Authority</div>
                  <div class="dept-option-item" data-value="NMIS – National Meat Inspection Service">NMIS – National Meat Inspection Service</div>
                  <div class="dept-option-item" data-value="PCAF – Philippine Council for Agriculture and Fisheries">PCAF – Philippine Council for Agriculture and Fisheries</div>
                  <div class="dept-option-item" data-value="PCC – Philippine Carabao Center">PCC – Philippine Carabao Center</div>
                  <div class="dept-option-item" data-value="PhilMech – Philippine Center for Postharvest Development and Mechanization">PhilMech – Philippine Center for Postharvest Development and Mechanization</div>
                  <div class="dept-option-item" data-value="NDA – National Dairy Authority">NDA – National Dairy Authority</div>
                  <div class="dept-option-item" data-value="NFA – National Food Authority">NFA – National Food Authority</div>
                  <div class="dept-option-item" data-value="NIA – National Irrigation Administration">NIA – National Irrigation Administration</div>
                  <div class="dept-option-item" data-value="PCA – Philippine Coconut Authority">PCA – Philippine Coconut Authority</div>
                  <div class="dept-option-item" data-value="PCIC – Philippine Crop Insurance Corporation">PCIC – Philippine Crop Insurance Corporation</div>
                  <div class="dept-option-item" data-value="PhilFIDA – Philippine Fiber Industry Development Authority">PhilFIDA – Philippine Fiber Industry Development Authority</div>
                  <div class="dept-option-item" data-value="PhilRice – Philippine Rice Research Institute">PhilRice – Philippine Rice Research Institute</div>
                  <div class="dept-option-item" data-value="SRA – Sugar Regulatory Administration">SRA – Sugar Regulatory Administration</div>

                  <div class="dept-group-title">Other / Partner Organization</div>
                  <div class="dept-option-item" data-value="Other">Other (Please specify below...)</div>
                </div>
              </div>

              <!-- Retain hidden input for form sync and submission -->
              <input type="hidden" id="divisionSelect" value="Career Development Division (CDD)">
              <input type="hidden" id="divisionInput" value="Career Development Division (CDD)">
            </div>

            <div id="divisionOtherWrap" style="display: none; margin-top: 0.6rem;">
              <input type="text" id="divisionOtherInput" class="dept-other-input" placeholder="Type your Department, Unit, or Office name...">
              <small class="dept-other-note">Specify your office / organization for administrative routing</small>
              <small class="field-error-msg" id="divisionOtherError" style="display: none; color: #dc2626; font-size: 0.78rem; font-weight: 600; margin-top: 0.35rem;">⚠️ Please specify your organization / division name.</small>
            </div>
          </div>
        </div>

        <div class="auth-field-group">
          <label class="auth-field-label" for="specialNotes" id="specialNotesLabel">Special Equipment / Setup Notes
            (Optional)</label>
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
        <h3 class="wizard-form-title">Step 4: Supporting Documents Upload <span style="color: #dc2626;">*</span></h3>
        <p class="wizard-form-desc">Attach approved Special Order, Activity Design, or official request endorsement
          memo (Required for reservation processing).</p>

        <!-- Official ATI Form Download Box -->
        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #e6f7ec 100%); border: 1.5px solid #a3d9b5; border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
          <div style="display: flex; align-items: center; gap: 0.9rem;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #175432; color: #facc15; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(23,84,50,0.2);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
              </svg>
            </div>
            <div>
              <div style="font-size: 0.92rem; font-weight: 800; color: #173822; margin-bottom: 0.2rem;">
                Official ATI Facility Reservation Form &amp; Terms
              </div>
              <div style="font-size: 0.8rem; color: #476652; line-height: 1.35;">
                Need the official form? Download or print the 2-page reservation template with Terms &amp; Conditions, fill it out, and upload the signed copy below.
              </div>
            </div>
          </div>

          <a href="download_form.php" target="_blank" class="btn-card-action primary" style="padding: 0.65rem 1.15rem; font-size: 0.84rem; text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.45rem; background: #175432; color: #ffffff; border-radius: 8px; font-weight: 700; box-shadow: 0 3px 8px rgba(23,84,50,0.25);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download Official Form</span>
          </a>
        </div>

        <div id="documentUploadZone"
          style="border: 2px dashed #b8ccbe; border-radius: 12px; padding: 3rem 2rem; text-align: center; background: #fbfdfc; cursor: pointer; transition: all 0.25s ease;">
          <input type="file" id="documentUploadInput" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" style="display: none;" required>
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="1.8"
            style="margin-bottom: 0.75rem;" id="docUploadSvg">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="17 8 12 3 7 8"></polyline>
            <line x1="12" y1="3" x2="12" y2="15"></line>
          </svg>
          <h4 id="documentUploadLabel" style="font-size: 1.1rem; color: #174d2f; margin-bottom: 0.35rem;">Click or Drag
            & Drop File Here</h4>
          <p id="documentUploadDesc" style="font-size: 0.85rem; color: #627b6c;">PDF, DOCX, or PNG formats up to 15MB
          </p>
        </div>
        <small class="field-error-msg" id="documentUploadError" style="display: none; color: #dc2626; font-size: 0.82rem; font-weight: 700; margin-top: 0.65rem; text-align: center;">
          ⚠️ Supporting document is required. Please upload your Activity Design, Special Order, or Official Request Memo.
        </small>
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

        <!-- Step 5 Official Reservation Summary (Visible before final submission) -->
        <div class="review-official-summary-card">
          <div class="summary-card-header">
            <div class="sch-left">
              <span class="sch-badge" id="summaryOfficialBadge">OFFICIAL RESERVATION PARTICULARS</span>
              <h4 class="sch-title" id="summaryVenueName">Function Hall</h4>
            </div>
            <span class="sch-rate-pill" id="summaryVenueRate">₱5,000 / day</span>
          </div>

          <div class="summary-card-body" style="margin-bottom: 1.25rem;">
            <div class="scb-item" id="summaryRoomDetailWrap" style="display: none;">
              <span class="scb-label" id="summaryRoomDetailLabel">Assigned Setup / Unit:</span>
              <strong class="scb-val" id="summaryRoomDetail">Room 102 (1st Floor (Sampaguita))</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Accommodation / Venue Type:</span>
              <strong class="scb-val" id="summaryFacilityType">Conference &amp; Training Venue</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Capacity / Guests:</span>
              <strong class="scb-val" id="summaryVenueCapacity">150 - 200 PAX</strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Reserved Schedule:</span>
              <strong class="scb-val" id="summaryReservationDate"><?= date('m/d/Y') ?></strong>
            </div>
            <div class="scb-item">
              <span class="scb-label">Time Window:</span>
              <strong class="scb-val" id="summaryTimeSlot">Whole Day (8:00 AM - 5:00 PM)</strong>
            </div>
          </div>

          <div
            style="font-size: 0.88rem; color: #435b4d; border-top: 1px solid #e1ece4; padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <span>Requested by: <strong>Juan Dela Cruz</strong> (<span id="summaryRequestedDivision">Career Development Division (CDD)</span>)</span>
            <span class="status-pill-badge"
              style="background: #fef3c7; color: #92400e; font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 999px;">Pending
              Administrative Review</span>
          </div>
        </div>

        <button type="button" class="btn-proceed-step" id="btnSubmitFinalReservation"
          style="width: 100%; justify-content: center; border-radius: 10px; padding: 1rem;">
          <svg viewBox="0 0 24 24" fill="none">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Submit Official Reservation Request</span>
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
       ROOM SELECTION MODAL POPUP
       ========================================================================== -->
    <div class="room-modal-overlay" id="roomSelectionModal" style="display: none;">
      <div class="room-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalDormTitle">

        <!-- Modal Header -->
        <div class="room-modal-header">
          <div class="room-modal-header-info">
            <div class="modal-badge-row">
              <span class="modal-dorm-pill-tag" id="modalCategoryPill">FLOOR PLAN SELECTION</span>
              <span id="modalDormRate" class="modal-dorm-rate-pill">₱500 / night</span>
            </div>
            <h3 id="modalDormTitle" class="modal-dorm-title">1st Floor: Sampaguita Dormitory</h3>
            <p id="modalDormFloor" class="modal-dorm-desc">Standard trainee dormitory floor with 12 air-conditioned
              rooms, individual lockers, and study desks.</p>
          </div>
          <button type="button" class="room-modal-close-btn" id="btnModalClose" aria-label="Close Selection">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
              stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Modal Legend -->
        <div class="room-modal-legend">
          <div class="legend-item">
            <span class="legend-color-dot available"></span>
            <span id="modalLegendAvailText"><strong>Available:</strong> Click to assign for your booking</span>
          </div>
          <div class="legend-item">
            <span class="legend-color-dot reserved"></span>
            <span id="modalLegendResText"><strong>Reserved:</strong> Occupied by scheduled delegates</span>
          </div>
        </div>

        <!-- Modal Body: Interactive Room Grid -->
        <div class="room-modal-body">
          <div class="modal-rooms-grid" id="modalRoomsGrid">
            <!-- Dynamically populated 12 room boxes -->
          </div>

          <!-- Feedback Bar inside modal -->
          <div class="modal-room-feedback-bar" id="modalRoomFeedback" style="display: none;">
            <div class="modal-feedback-left">
              <div class="mf-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div>
                <div class="mf-title" id="modalFeedbackTitle">Room 402 Selected</div>
                <div class="mf-sub" id="modalFeedbackSub">Standard Trainee Dormitory (₱500 / night)</div>
              </div>
            </div>
            <span class="badge-assigned-ok">✓ Ready to Reserve</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         VALIDATION WARNING POPUP MODAL (Clean ATI Custom Alert Dialog)
         ========================================================================== -->
    <div class="room-modal-overlay" id="validationWarningModal" style="display: none; z-index: 10000;">
      <div class="room-modal-dialog" style="max-width: 480px; border-radius: 18px; text-align: center; padding: 2rem 1.75rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; border: 2px solid #fecaca; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
        </div>
        <h3 id="valModalTitle" style="font-size: 1.3rem; font-weight: 800; color: #1f2937; margin-bottom: 0.5rem;">Requirement Missing</h3>
        <p id="valModalMessage" style="font-size: 0.92rem; color: #4b5563; line-height: 1.55; margin-bottom: 1.5rem;">
          Please complete all required fields before proceeding.
        </p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
          <button type="button" id="btnDismissValModal" style="background: #175432; color: #ffffff; font-weight: 700; font-size: 0.9rem; padding: 0.7rem 2rem; border-radius: 9999px; border: none; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(23, 84, 50, 0.25);">
            Understood
          </button>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         RESERVATION SUCCESS POPUP MODAL (Official Receipt & Confirmation)
         ========================================================================== -->
    <div class="room-modal-overlay" id="reservationSuccessModal" style="display: none; z-index: 10000;">
      <div class="room-modal-dialog" style="max-width: 540px; border-radius: 20px; text-align: center; padding: 2.25rem 2rem; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);">
        <!-- Animated Success Checkmark Icon -->
        <div style="width: 72px; height: 72px; border-radius: 50%; background: #ecfdf5; border: 2.5px solid #a7f3d0; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
          <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>

        <span style="display: inline-block; background: #e0f2fe; color: #0284c7; font-size: 0.75rem; font-weight: 800; padding: 3px 12px; border-radius: 9999px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.5rem;">
          REQUEST TRANSMITTED
        </span>

        <h3 style="font-size: 1.45rem; font-weight: 800; color: #142e1f; margin-bottom: 0.35rem;">
          Reservation Submitted Successfully!
        </h3>
        
        <p style="font-size: 0.88rem; color: #526f5e; line-height: 1.5; margin-bottom: 1.25rem;">
          Your official reservation request has been registered and queued for Administrative Review and Approval.
        </p>

        <!-- Summary Receipt Card -->
        <div style="background: #f8faf9; border: 1.5px solid #e1ece4; border-radius: 12px; padding: 1.15rem; text-align: left; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #d5e3da; padding-bottom: 0.5rem;">
            <span style="color: #637f70; font-weight: 600;">Reference No.:</span>
            <strong style="color: #175432; font-size: 0.95rem; font-family: monospace; letter-spacing: 0.05em;" id="succModalRef">ATI-RES-XXXX</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Facility / Venue:</span>
            <strong style="color: #1f2937;" id="succModalFacility">Function Hall</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Schedule Dates:</span>
            <strong style="color: #1f2937;" id="succModalSchedule">Selected Dates</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Time Slot:</span>
            <strong style="color: #1f2937;" id="succModalTime">Whole Day</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Status:</span>
            <span style="background: #fef3c7; color: #92400e; font-weight: 800; font-size: 0.75rem; padding: 2px 8px; border-radius: 6px;">Pending Approval</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
          <a href="my_reservations.php" style="background: #175432; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(23, 84, 50, 0.25);">
            <span>View My Reservations</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
          <a href="home.php" style="background: #edf5ef; color: #175432; text-decoration: none; font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1.5rem; border-radius: 9999px; border: 1.5px solid #cce0d2;">
            Return to Home
          </a>
        </div>
      </div>
    </div>
  </main>

  <script src="js/booking.js?v=<?php echo time(); ?>"></script>
</body>

</html>