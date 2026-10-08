<?php
require_once __DIR__ . '/includes/init.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Master Schedule & Institution-Wide Calendar View
 */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$isAdmin = (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'super_admin', 'director', 'recommending_officer']))
  || (isset($_COOKIE['ati_role']) && in_array($_COOKIE['ati_role'], ['admin', 'super_admin', 'director']));

if ($isAdmin || isset($_GET['admin'])) {
  header('Location: admin_schedule.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Master Schedule - Agricultural Training Institute</title>
  <meta name="description"
    content="Official institution-wide master schedule and facility reservation calendar for ATI halls, meeting rooms, and dormitory suites.">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/booking.css">
  <link rel="stylesheet" href="css/schedule.css">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="schedule-body">

  <!-- ==========================================================================
       TOPBAR NAVIGATION
       ========================================================================== -->
  <header class="booking-topbar">
    <div class="booking-topbar-container">
      <a href="home.php" class="booking-brand" title="ATI Reservation Portal">
        <img src="assets/images/ATI_Logo.png" alt="ATI Official Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility and Dormitory Reservation System</p>
        </div>
      </a>

      <nav class="booking-nav-center">
        <!-- Home -->
        <a href="home.php" class="booking-nav-item">
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
          <span class="nav-badge-count">4</span>
        </a>

        <!-- Master Schedule (Active) -->
        <a href="schedule.php" class="booking-nav-item active">
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
              <div class="dropdown-user-name">Juan Dela Cruz</div>
              <div class="dropdown-user-email"><span class="user-verified-dot"></span> ATI Personnel &bull; CDD</div>
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

          <a href="schedule.php" class="drawer-nav-link active">
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
       MAIN CONTENT
       ========================================================================== -->
  <main class="schedule-main-wrapper">
    <!-- Header Title & Action -->
    <section class="schedule-header">
      <div class="schedule-header-text">
        <div class="schedule-top-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
          </svg>
          <span>Institution-Wide Calendar View</span>
        </div>
        <h2>ATI Facilities & Dormitory Master Schedule</h2>
        <p>Comprehensive real-time overview of official schedules, venue occupancy, and reserved activities across all
          ATI facilities.</p>
      </div>

      <a href="booking.php" class="btn-book-from-schedule">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Reserve a Venue</span>
      </a>
    </section>

    <!-- Summary Stats Strip (Interactive System Status Cards) -->
    <div class="schedule-stats-grid">

      <!-- Card 1: Total Events Scheduled -->
      <div class="schedule-stat-card green" id="statCardEvents" role="button" tabindex="0"
        title="Click to view scheduled events stats">
        <div class="stat-icon-box green">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="statValueEvents">18 Events</span>
          <span class="stat-label" id="statLabelEvents">Scheduled this Month</span>
        </div>
      </div>

      <!-- Card 2: Highest Demand Venue -->
      <div class="schedule-stat-card blue" id="statCardDemand" role="button" tabindex="0"
        title="Click to filter to highest demand venue">
        <div class="stat-icon-box blue">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="statValueDemand">Function Hall</span>
          <span class="stat-label" id="statLabelDemand">Highest Demand Venue</span>
        </div>
      </div>

      <!-- Card 3: Overall Monthly Occupancy -->
      <div class="schedule-stat-card amber" id="statCardOccupancy" role="button" tabindex="0"
        title="Click to view monthly occupancy details">
        <div class="stat-icon-box amber">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="statValueOccupancy">68%</span>
          <span class="stat-label" id="statLabelOccupancy">Overall Monthly Occupancy</span>
        </div>
      </div>

      <!-- Card 4: Days with Available Slots -->
      <div class="schedule-stat-card purple" id="statCardAvailableDays" role="button" tabindex="0"
        title="Click to highlight available slots">
        <div class="stat-icon-box purple">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="statValueAvailableDays">12 Days</span>
          <span class="stat-label" id="statLabelAvailableDays">With Available Slots</span>
        </div>
      </div>

    </div>

    <!-- Stats Details Panel on the bottom of the cards (appears when a card is clicked) -->
    <div class="stat-detail-panel" id="statDetailPanel" style="display: none;" role="region" aria-live="polite">
      <button type="button" class="stat-detail-close-btn" id="statDetailCloseBtn" aria-label="Close stats details"
        title="Close stats details">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <div class="stat-detail-main">
        <div class="stat-detail-icon-wrap" id="statDetailIconWrap">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
          </svg>
        </div>
        <div class="stat-detail-info">
          <div class="stat-detail-header">
            <span class="stat-detail-title" id="statDetailTitle">Card Stats</span>
            <span class="stat-detail-badge" id="statDetailBadge">Details</span>
          </div>
          <p class="stat-detail-desc" id="statDetailDesc">Description of statistics.</p>
          <div class="stat-detail-chips" id="statDetailChips">
            <!-- Breakdown pills dynamically added here -->
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar Controls Bar -->
    <div class="calendar-controls-bar">
      <!-- Month Navigation Controls -->
      <div class="month-nav-group">
        <button type="button" class="nav-arrow-btn" id="btnPrevMonth" aria-label="Previous Month">
          &lsaquo;
        </button>
        <span class="current-month-display" id="currentMonthDisplay">October 2026</span>
        <button type="button" class="nav-arrow-btn" id="btnNextMonth" aria-label="Next Month">
          &rsaquo;
        </button>
        <button type="button" class="btn-today-pill" id="btnToday">Today</button>
      </div>

      <!-- Facility Filter Pills -->
      <div class="facility-filter-pills">
        <button type="button" class="fac-pill active" data-facility="all">All Facilities</button>
        <button type="button" class="fac-pill" data-facility="function-hall">Function Hall</button>
        <button type="button" class="fac-pill" data-facility="training-hall">Training Hall A</button>
        <button type="button" class="fac-pill" data-facility="mess-hall">Mess Hall</button>
        <button type="button" class="fac-pill" data-facility="boardroom">Boardroom</button>
        <button type="button" class="fac-pill" data-facility="dormitory">Dormitory</button>
      </div>
    </div>

    <!-- Master Calendar Board -->
    <div class="master-calendar-board">
      <!-- Day Headers (Sun - Sat) -->
      <div class="board-weekdays-row">
        <div class="weekday-header-cell">Sun</div>
        <div class="weekday-header-cell">Mon</div>
        <div class="weekday-header-cell">Tue</div>
        <div class="weekday-header-cell">Wed</div>
        <div class="weekday-header-cell">Thu</div>
        <div class="weekday-header-cell">Fri</div>
        <div class="weekday-header-cell">Sat</div>
      </div>

      <!-- Days Grid (Dynamically Populated via JS) -->
      <div class="board-days-grid" id="boardDaysGrid"></div>
    </div>
  </main>

  <!-- ==========================================================================
       EVENT DETAILS MODAL
       ========================================================================== -->
  <div class="modal-overlay" id="scheduleEventModal" role="dialog" aria-modal="true">
    <div class="modal-card modal-lg">
      <button type="button" class="modal-close-btn js-close-modal" aria-label="Close modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="modal-header-banner" id="modalEventBanner">
        <span
          style="font-size: 0.78rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; opacity: 0.9;"
          id="modalEventFacility">Function Hall</span>
        <h3 id="modalEventTitle" style="font-size: 1.5rem; margin-top: 0.35rem; color: #ffffff;">Regional Rice
          Specialists Briefing</h3>
      </div>

      <div class="schedule-modal-content">
        <div class="event-meta-grid">
          <div class="event-meta-item">
            <span class="meta-label">Schedule Date</span>
            <div class="meta-val" id="modalEventDate">2026-10-15</div>
          </div>
          <div class="event-meta-item">
            <span class="meta-label">Time Window</span>
            <div class="meta-val" id="modalEventTime">08:00 AM - 05:00 PM</div>
          </div>
          <div class="event-meta-item">
            <span class="meta-label">Requesting Unit / Division</span>
            <div class="meta-val" id="modalEventDivision">Career Development Division</div>
          </div>
          <div class="event-meta-item">
            <span class="meta-label">Estimated Attendees</span>
            <div class="meta-val" id="modalEventAttendees">200 Delegates</div>
          </div>
        </div>

        <div
          style="background: #edf8f1; padding: 1rem 1.25rem; border-radius: 10px; border-left: 4px solid #175432; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
          <div>
            <strong style="color: #175432;">Status:</strong>
            <span id="modalEventStatus" style="font-weight: 700; color: #175432; margin-left: 0.35rem;">Approved &
              Confirmed</span>
          </div>
          <span style="font-size: 0.8rem; color: #526f5e;">Authorized by ATI Admin Services</span>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" class="btn-step-back js-close-modal">Close</button>
          <a href="booking.php" class="btn-proceed-step" style="padding: 0.8rem 1.5rem; border-radius: 9999px;">
            <span>Book Another Date</span>
          </a>
        </div>
      </div>
    </div>
    <!-- ==========================================================================
       MONTHLY SCHEDULED EVENTS LIST MODAL (Activated by Card 1)
       ========================================================================== -->
    <div class="modal-overlay" id="monthlyEventsModal" role="dialog" aria-modal="true">
      <div class="modal-card modal-lg" style="max-width: 820px;">
        <button type="button" class="modal-close-btn js-close-modal" aria-label="Close modal">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="modal-header-banner"
          style="background: linear-gradient(135deg, #175432, #107545); padding: 1.35rem 1.85rem;">
          <span
            style="font-size: 0.78rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; opacity: 0.9; color: #ffffff;">SYSTEM
            ACTIVITY BREAKDOWN</span>
          <h3 id="monthlyEventsModalTitle" style="font-size: 1.45rem; margin-top: 0.35rem; color: #ffffff;">Scheduled
            Events for October 2026</h3>
        </div>

        <div class="schedule-modal-content" style="padding: 1.5rem 1.75rem;">
          <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div style="font-size: 0.88rem; color: #175432; font-weight: 700;" id="monthlyEventsModalCount">Showing 18
              confirmed events across ATI facilities</div>
            <input type="text" id="eventsModalSearchInput" placeholder="Quick search title, facility, division..."
              style="padding: 0.5rem 0.95rem; border: 1.5px solid #d1ded5; border-radius: 8px; font-size: 0.82rem; min-width: 260px; outline: none;">
          </div>

          <div class="monthly-events-list-container" id="monthlyEventsListContainer"
            style="max-height: 380px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem; padding-right: 4px;">
            <!-- Dynamically populated by schedule.js -->
          </div>

          <div
            style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5ece7;">
            <button type="button" class="btn-step-back js-close-modal">Close</button>
            <a href="booking.php" class="btn-proceed-step" style="padding: 0.75rem 1.5rem; border-radius: 9999px;">
              <span>Reserve a Facility</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <script src="js/schedule.js?v=<?php echo time(); ?>"></script>
</body>

</html>