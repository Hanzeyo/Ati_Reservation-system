<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';

$selectedFacilityId = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;

// Fetch active dormitory facilities from MySQL
$dormStmt = $db->query("SELECT * FROM facilities WHERE type = 'dorms' AND status != 'inactive' ORDER BY id ASC");
$dbDorms = $dormStmt ? $dormStmt->fetchAll(PDO::FETCH_ASSOC) : [];
$dormCount = count($dbDorms);

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
            <span>Change Category (Halls / Dorms)</span>
          </a>
          <span class="active-category-indicator" id="activeCategoryBadge" title="Current category view">
            <span class="indicator-dot"></span>
            <span class="indicator-prefix">Viewing:</span>
            <span id="activeCategoryName">Dormitories (<?= $dormCount ?> Suites Available)</span>
          </span>
        </div>

        <div class="category-nav-right">
          <a href="booking.php?category=halls" class="btn-switch-portal to-halls" title="Switch to Function Halls & Venues">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
              <line x1="9" y1="22" x2="9" y2="12"></line>
              <line x1="15" y1="22" x2="15" y2="12"></line>
              <line x1="9" y1="6" x2="9.01" y2="6"></line>
              <line x1="15" y1="6" x2="15.01" y2="6"></line>
            </svg>
            <span>Switch to Function Halls</span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>
        </div>
      </div>

      <div class="facility-header-status-row">
        <div class="filter-label-group">
          <svg viewBox="0 0 24 24" fill="none">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          <span id="categorySectionHeading">AVAILABLE DORMITORY SUITES (<?= $dormCount ?>):</span>
        </div>
        <p class="select-instruction-text">Select a dormitory floor below, then click to view rooms and choose your room
          allotment.</p>
      </div>

      <!-- Facility Cards Grid (Dynamically rendered from MySQL facilities database) -->
      <div class="facility-selection-grid" id="facilitySelectionGrid">
        <?php if ($dormCount === 0): ?>
          <div class="booking-empty-state" id="dormEmptyState" style="display: block;">
            <div class="booking-empty-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
                <path d="M6 8v9"></path>
              </svg>
            </div>
            <h3 class="booking-empty-title">No Dormitories Available</h3>
            <p class="booking-empty-desc">There are currently no active dormitory floors or lodging facilities open for booking. Please check back later or browse available facility venues.</p>
            <a href="booking.php?category=halls" class="btn-empty-reset" style="text-decoration: none;">
              <span>Browse Facility Venues</span>
            </a>
          </div>
        <?php else: ?>
          <?php 
          $hasSelected = false;
          foreach ($dbDorms as $idx => $f): 
            $isMaint = ($f['status'] === 'maintenance');
            $isSelected = false;
            if ($selectedFacilityId > 0 && $f['id'] == $selectedFacilityId && !$isMaint) {
              $isSelected = true;
              $hasSelected = true;
            } elseif (!$hasSelected && !$isMaint) {
              $isSelected = true;
              $hasSelected = true;
            }
            
            $cardSlug = 'dorm-facility-' . $f['id'];
            $lower = strtolower($f['name']);
            if (strpos($lower, 'sampaguita') !== false || strpos($lower, '1st') !== false) {
              $cardSlug = 'dorm-floor-1';
            } elseif (strpos($lower, 'ilang-ilang') !== false || strpos($lower, '2nd') !== false) {
              $cardSlug = 'dorm-floor-2';
            } elseif (strpos($lower, 'gumamela') !== false || strpos($lower, '3rd') !== false) {
              $cardSlug = 'dorm-floor-3';
            } elseif (strpos($lower, 'rosas') !== false || strpos($lower, 'rosal') !== false || strpos($lower, '4th') !== false) {
              $cardSlug = 'dorm-floor-4';
            } elseif (strpos($lower, 'waling') !== false || strpos($lower, '5th') !== false) {
              $cardSlug = 'dorm-floor-5';
            } elseif (strpos($lower, 'tayabak') !== false || strpos($lower, '6th') !== false) {
              $cardSlug = 'dorm-floor-6';
            }

            $imgSrc = !empty($f['image']) ? $f['image'] : 'assets/images/dormitory.jpg';
            $capDisplay = $f['capacity'] . ' PAX / ' . ($f['beds'] ?? 0) . ' BEDS';
            if (stripos($f['capacity'], 'PAX') !== false || stripos($f['capacity'], 'BED') !== false) {
              $capDisplay = $f['capacity'];
            }
            
            $featuresList = [];
            if (!empty($f['features'])) {
              $featuresList = array_map('trim', explode(',', $f['features']));
            } else {
              $featuresList = ['Aircon Rooms', 'Single Bunk Beds', 'Individual Lockers', 'Study Desks & Lounge'];
            }
          ?>
          <article class="facility-choice-card dorm-suite-card <?= $isMaint ? 'under-maintenance' : ($isSelected ? 'selected' : '') ?>"
            data-id="<?= $cardSlug ?>"
            data-facility-db-id="<?= $f['id'] ?>"
            data-name="<?= htmlspecialchars($f['name']) ?>"
            data-rate="<?= htmlspecialchars($f['rate']) ?>"
            data-capacity="<?= htmlspecialchars($capDisplay) ?>"
            data-facility-type="dormitories"
            data-status="<?= htmlspecialchars($f['status']) ?>">
            <div class="facility-card-image">
              <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($f['name']) ?>" loading="lazy">
              <span class="facility-cap-badge"><?= htmlspecialchars($capDisplay) ?></span>
              <?php if ($isMaint): ?>
                <span class="facility-maint-badge" style="position:absolute; top:10px; left:10px; background:#b45309; color:#fff; font-size:0.68rem; font-weight:800; padding:3px 8px; border-radius:6px; letter-spacing:0.5px; box-shadow:0 2px 6px rgba(0,0,0,0.25);">UNDER MAINTENANCE</span>
              <?php endif; ?>
            </div>
            <div class="facility-card-content">
              <span class="facility-category-tag"><?= htmlspecialchars(strtoupper($f['name'])) ?></span>
              <h3 class="facility-title"><?= htmlspecialchars($f['name']) ?></h3>
              <div class="facility-rate-tag">Standard Rate: <?= htmlspecialchars($f['rate']) ?></div>
              <div class="facility-amenities-tags">
                <?php foreach (array_slice($featuresList, 0, 4) as $feat): ?>
                  <span class="amenity-pill"><?= htmlspecialchars($feat) ?></span>
                <?php endforeach; ?>
              </div>
              <div class="dorm-card-selected-room-badge" style="<?= $isSelected ? 'display:flex;' : 'display:none;' ?>">
                <span class="d-room-text">✓ Room Selected</span>
              </div>
              <?php if ($isMaint): ?>
                <button type="button" class="btn-select-facility" disabled title="This dormitory is under maintenance">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                  </svg>
                  <span>Unavailable (Under Maintenance)</span>
                </button>
              <?php else: ?>
                <button type="button" class="btn-select-facility btn-dorm-modal-trigger">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="9" y1="21" x2="9" y2="9"></line>
                  </svg>
                  Select Room &amp; View Floor Plan
                </button>
              <?php endif; ?>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Selection Summary Preview -->
      <div class="step1-selection-summary-card" id="step1SummaryCard" style="<?= $dormCount === 0 ? 'display: none;' : '' ?>">
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
         STEP 2: STAY DATES & NIGHTS (AVAILABILITY SLOT CHECK)
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
                <!-- Dynamically rendered and populated by initDateSlotInteractions() -->
              </div>
            </div>

            <!-- Legend with Available, Pending, Reserved, Suspended, Selected -->
            <div class="slot-legend-row">
              <div class="slot-legend-tag">
                <span class="legend-circle available"></span>
                <span>Available</span>
              </div>
              <div class="slot-legend-tag" title="A reservation request has been submitted and is currently undergoing administrative review">
                <span class="legend-circle pending"></span>
                <span>Pending</span>
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
                    <option value="08:00 AM (Morning)" selected>08:00 AM (Morning)</option>
                    <option value="09:00 AM">09:00 AM</option>
                    <option value="01:00 PM">01:00 PM (Afternoon)</option>
                    <option value="02:00 PM (Check-in)">02:00 PM (Check-in)</option>
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
                    <option value="05:00 PM (Full Day)" selected>05:00 PM (Full Day)</option>
                    <option value="12:00 PM (Check-out)">12:00 PM (Check-out)</option>
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
                <p id="slotStatusDesc">No venue conflicts detected for 1 day duration (<?= date('F j, Y') ?>).</p>
              </div>
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

          <div style="display: grid; grid-template-columns: minmax(160px, 200px) minmax(280px, 420px); gap: 1.25rem; margin-bottom: 1.15rem; align-items: start;">
            <div>
              <label for="dormGuestCount" class="auth-field-label">Total Guests Staying *</label>
              <input type="number" id="dormGuestCount" class="step-text-input" value="16" min="1" max="48"
                style="width: 100%; padding: 0.55rem 0.85rem; font-size: 0.88rem; border: 1.5px solid #c9ded0; border-radius: 8px;">
            </div>
            <div>
              <label for="dormDeptSelectTrigger" class="auth-field-label">Endorsing Division / Unit *</label>
              <div class="dept-select-wrap" id="dormDeptSelectWrap">
                <button type="button" class="dept-select-trigger" id="dormDeptSelectTrigger" aria-haspopup="listbox" aria-expanded="false" style="padding: 0.55rem 0.85rem;">
                  <span class="dept-trigger-text" id="dormDeptTriggerText">Career Development Division (CDD)</span>
                  <svg class="dept-select-arrow" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>

                <div class="dept-dropdown-panel" id="dormDeptDropdownPanel">
                  <div class="dept-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" class="dept-search-input" id="dormDeptSearchInput" placeholder="Quick search division, bureau, agency..." autocomplete="off">
                  </div>

                  <div class="dept-options-scroll" id="dormDeptOptionsScroll" role="listbox">
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
                <input type="hidden" id="dormDivisionUnit" value="Career Development Division (CDD)">
              </div>

              <div id="dormDivisionOtherWrap" style="display: none; margin-top: 0.6rem;">
                <input type="text" id="dormDivisionOtherInput" class="dept-other-input" placeholder="Type your Department, Unit, or Office name...">
                <small class="dept-other-note">Specify your office / organization for administrative routing</small>
              </div>
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
              id="dormRefPreview">ATI-BK-<?= date('Y') ?>-<?= rand(1000, 9999) ?></strong></span>
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
            <strong style="display: block; font-size: 0.95rem; color: #143522; margin-top: 0.2rem;" id="dormReviewDivision">Career Development
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

      <button type="button" class="btn-proceed-step" id="btnProceedStep" style="<?= $dormCount === 0 ? 'display: none;' : '' ?>">
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

    <!-- ==========================================================================
         VALIDATION WARNING MODAL (ATI Government Clean UI Alert)
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
            <strong style="color: #175432; font-size: 0.95rem; font-family: monospace; letter-spacing: 0.05em;" id="succModalRef">ATI-BK-2026-XXXX</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Facility / Venue:</span>
            <strong style="color: #1f2937;" id="succModalFacility">1st Floor: Sampaguita Dormitory</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Schedule Dates:</span>
            <strong style="color: #1f2937;" id="succModalSchedule">Selected Dates</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #637f70; font-weight: 600;">Time Slot:</span>
            <strong style="color: #1f2937;" id="succModalTime">Overnight Stay</strong>
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
  <script>
    function closeRoomModal() {
      const modal = document.getElementById('roomSelectionModal');
      if (modal) modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  </script>
</body>

</html>