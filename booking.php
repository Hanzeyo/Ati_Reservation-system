<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Step-by-Step Facility Booking Portal
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve ATI Venue & Facilities - Agricultural Training Institute</title>
  <meta name="description" content="Submit official reservation requests for ATI function halls, training venues, boardrooms, and dormitory suites.">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/booking.css">
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
      <a href="booking.php" class="booking-brand">
        <img src="assets/images/ATI_Logo.png" alt="ATI Official Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility and Dormitory Reservation System</p>
        </div>
      </a>

      <!-- Center & Right: Navigation Actions -->
      <nav class="booking-nav-center">
        <!-- New Reservation (Active) -->
        <a href="booking.php" class="booking-nav-item active">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          <span>New Reservation</span>
        </a>

        <!-- My Reservations with count badge -->
        <a href="my_reservations.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none">
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count">2</span>
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
            <a href="javascript:void(0)" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="my_reservations.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Booking History</span>
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
      <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileDrawer(true)" aria-label="Open Navigation Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <!-- ==========================================================================
       MOBILE NAVIGATION DRAWER OVERLAY
       ========================================================================== -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="if(event.target===this) toggleMobileDrawer(false);" style="display: none;">
    <div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">
      
      <!-- Drawer Header -->
      <div class="drawer-header">
        <div class="drawer-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="drawer-logo" style="width: 36px; height: 36px; object-fit: contain;">
          <div>
            <h4>ATI Portal</h4>
            <p>Central Office</p>
          </div>
        </div>
        <button type="button" class="drawer-close-btn" id="mobileDrawerClose" onclick="toggleMobileDrawer(false)" aria-label="Close Navigation Menu">&times;</button>
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
          
          <a href="booking.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
            </div>
            <span class="drawer-link-text">New Reservation</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </div>
            <span class="drawer-link-text">My Reservations</span>
            <span class="drawer-badge-count">2</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="schedule.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <span class="drawer-link-text">Master Schedule</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>
        </div>

        <!-- Account Settings Section -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">ACCOUNT & SETTINGS</div>
          
          <a href="javascript:void(0)" class="drawer-nav-link" onclick="alert('Profile management available in next administrative release.');">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="drawer-link-text">Booking History</span>
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
        setTimeout(function() {
          if (!overlay.classList.contains('show')) {
            overlay.style.display = 'none';
          }
        }, 280);
      }
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        var overlay = document.getElementById('mobileDrawerOverlay');
        if (overlay && overlay.classList.contains('show')) {
          toggleMobileDrawer(false);
        }
      }
    });

    window.addEventListener('resize', function() {
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

    <!-- Stepper Navigation Bar (5 Steps) -->
    <div class="stepper-bar-card">
      <div class="stepper-grid">
        <button type="button" class="step-tab-btn active" data-step="1">
          <span class="step-number">1</span>
          <span>Facility</span>
        </button>
        <button type="button" class="step-tab-btn" data-step="2">
          <span class="step-number">2</span>
          <span>Date & Time</span>
        </button>
        <button type="button" class="step-tab-btn" data-step="3">
          <span class="step-number">3</span>
          <span>Event Details</span>
        </button>
        <button type="button" class="step-tab-btn" data-step="4">
          <span class="step-number">4</span>
          <span>Documents</span>
        </button>
        <button type="button" class="step-tab-btn" data-step="5">
          <span class="step-number">5</span>
          <span>Review & Submit</span>
        </button>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 1: FACILITY SELECTION
         ========================================================================== -->
    <div class="wizard-step-view active" data-step="1">
      <!-- Filter Bar -->
      <div class="facilities-filter-bar">
        <div class="filter-label-group">
          <svg viewBox="0 0 24 24" fill="none">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          <span>FILTER AVAILABLE FACILITIES:</span>
        </div>

        <div class="filter-pills-list">
          <button type="button" class="filter-pill active" data-filter="all">All Facilities</button>
          <button type="button" class="filter-pill" data-filter="large-halls">Large Halls</button>
          <button type="button" class="filter-pill" data-filter="meeting-rooms">Meeting Rooms</button>
          <button type="button" class="filter-pill" data-filter="lodging-suites">Lodging & Suites</button>
        </div>
      </div>

      <!-- Facility Cards Grid -->
      <div class="facility-selection-grid">
        <!-- 1. Function Hall (Selected by default) -->
        <article class="facility-choice-card selected" data-id="function-hall" data-name="Function Hall" data-rate="₱5,000/day" data-capacity="150 - 200 PAX" data-category="large-halls">
          <div class="facility-card-image">
            <img src="assets/images/function_hall.jpg" alt="Function Hall" loading="lazy">
            <span class="facility-cap-badge">150 - 200 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">LARGE EVENT VENUE</span>
            <h3 class="facility-title">Function Hall</h3>
            <div class="facility-rate-tag">Standard Rate: ₱5,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Central Aircon</span>
              <span class="amenity-pill">PA Sound System</span>
              <span class="amenity-pill">HD Projector</span>
              <span class="amenity-pill">Stage Setup</span>
              <span class="amenity-pill">VIP Waiting Lounge</span>
            </div>
            <button type="button" class="btn-select-facility">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              Selected Venue
            </button>
          </div>
        </article>

        <!-- 2. Training Hall A -->
        <article class="facility-choice-card" data-id="training-hall-a" data-name="Training Hall A" data-rate="₱3,000/day" data-capacity="50 - 80 PAX" data-category="large-halls">
          <div class="facility-card-image">
            <img src="assets/images/training_hall.jpg" alt="Training Hall A" loading="lazy">
            <span class="facility-cap-badge">50 - 80 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">INTERACTIVE TRAINING HALL</span>
            <h3 class="facility-title">Training Hall A</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Modular Desks</span>
              <span class="amenity-pill">Smart Display</span>
              <span class="amenity-pill">High-Speed Wifi</span>
              <span class="amenity-pill">Breakout Corners</span>
            </div>
            <button type="button" class="btn-select-facility">
              Select This Facility
            </button>
          </div>
        </article>

        <!-- 3. Mess Hall & Dining Area -->
        <article class="facility-choice-card" data-id="mess-hall" data-name="Mess Hall & Dining Area" data-rate="₱3,500/day" data-capacity="100 PAX DINING" data-category="large-halls">
          <div class="facility-card-image">
            <img src="assets/images/mess_hall.jpg" alt="Mess Hall & Dining Area" loading="lazy">
            <span class="facility-cap-badge">100 PAX DINING</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">DINING & CATERING VENUE</span>
            <h3 class="facility-title">Mess Hall & Dining Area</h3>
            <div class="facility-rate-tag">Standard Rate: ₱3,500/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Buffet Counters</span>
              <span class="amenity-pill">Kitchen Access</span>
              <span class="amenity-pill">Washing Station</span>
              <span class="amenity-pill">Outdoor Patio Deck</span>
            </div>
            <button type="button" class="btn-select-facility">
              Select This Facility
            </button>
          </div>
        </article>

        <!-- 4. Executive Boardroom -->
        <article class="facility-choice-card" data-id="executive-boardroom" data-name="Executive Boardroom" data-rate="₱2,500/day" data-capacity="20 - 30 PAX" data-category="meeting-rooms">
          <div class="facility-card-image">
            <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" loading="lazy">
            <span class="facility-cap-badge">20 - 30 PAX</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">VIP CONFERENCE ROOM</span>
            <h3 class="facility-title">Executive Boardroom</h3>
            <div class="facility-rate-tag">Standard Rate: ₱2,500/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Executive Leather Chairs</span>
              <span class="amenity-pill">Video Conference Cam</span>
              <span class="amenity-pill">Coffee Machine</span>
              <span class="amenity-pill">Acoustic Wall Paneling</span>
            </div>
            <button type="button" class="btn-select-facility">
              Select This Facility
            </button>
          </div>
        </article>

        <!-- 5. Dormitory Suites (Building B) -->
        <article class="facility-choice-card" data-id="dormitory-suites" data-name="Dormitory Suites (Building B)" data-rate="₱4,000/day" data-capacity="40 GUESTS (10 ROOMS)" data-category="lodging-suites">
          <div class="facility-card-image">
            <img src="assets/images/dormitory.jpg" alt="Dormitory Suites" loading="lazy">
            <span class="facility-cap-badge">40 GUESTS (10 ROOMS)</span>
          </div>
          <div class="facility-card-content">
            <span class="facility-category-tag">ACCOMMODATION LODGING</span>
            <h3 class="facility-title">Dormitory Suites (Building B)</h3>
            <div class="facility-rate-tag">Standard Rate: ₱4,000/day</div>
            <div class="facility-amenities-tags">
              <span class="amenity-pill">Air-Conditioned Rooms</span>
              <span class="amenity-pill">Hot/Cold Shower</span>
              <span class="amenity-pill">Shared Lounge</span>
              <span class="amenity-pill">24/7 Security</span>
            </div>
            <button type="button" class="btn-select-facility">
              Select This Facility
            </button>
          </div>
        </article>
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
              <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span class="slot-box-title">INTERACTIVE AVAILABILITY SLOT CHECK</span>
                <div class="slot-mode-selector" id="slotModeSelector">
                  <button type="button" class="slot-mode-btn active" id="btnModeSingle" title="Select single day reservation">Single Day</button>
                  <button type="button" class="slot-mode-btn" id="btnModeRange" title="Select multi-day range">Multi-Day Range</button>
                </div>
              </div>
              <span class="slot-month-pill">Month: October 2026</span>
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
                <button type="button" class="slot-day-btn available" data-day="1">1</button>
                <button type="button" class="slot-day-btn available" data-day="2">2</button>
                <button type="button" class="slot-day-btn available" data-day="3">3</button>

                <button type="button" class="slot-day-btn available" data-day="4">4</button>
                <button type="button" class="slot-day-btn available" data-day="5">5</button>
                <button type="button" class="slot-day-btn reserved" data-day="6">6</button>
                <button type="button" class="slot-day-btn available" data-day="7">7</button>
                <button type="button" class="slot-day-btn available" data-day="8">8</button>
                <button type="button" class="slot-day-btn available" data-day="9">9</button>
                <button type="button" class="slot-day-btn available" data-day="10">10</button>

                <button type="button" class="slot-day-btn available" data-day="11">11</button>
                <button type="button" class="slot-day-btn reserved" data-day="12">12</button>
                <button type="button" class="slot-day-btn available" data-day="13">13</button>
                <button type="button" class="slot-day-btn selected" data-day="14">14</button>
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
                <button type="button" class="slot-day-btn suspended" data-day="29" title="Suspended: Facility Maintenance">29</button>
                <button type="button" class="slot-day-btn available" data-day="30">30</button>
                <button type="button" class="slot-day-btn available" data-day="31">31</button>
              </div>
            </div>

            <!-- Legend with Suspended Added -->
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
                <input type="text" id="startDateInput" value="10/14/2026">
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
                <input type="text" id="endDateInput" value="10/14/2026">
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
        <h3 class="wizard-form-title">Step 3: Event & Activity Information</h3>
        <p class="wizard-form-desc">Provide details regarding the nature of your activity, participants, and specific requirements.</p>

        <div class="auth-field-group">
          <label class="auth-field-label" for="eventTitleInput">Activity / Event Title</label>
          <input type="text" id="eventTitleInput" class="auth-input" placeholder="e.g. Regional Agricultural Extension Coordinators Training 2026" style="padding-left: 1rem;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
          <div class="auth-field-group">
            <label class="auth-field-label" for="eventPaxInput">Estimated Number of Attendees</label>
            <input type="number" id="eventPaxInput" class="auth-input" placeholder="e.g. 120" style="padding-left: 1rem;">
          </div>
          <div class="auth-field-group">
            <label class="auth-field-label" for="divisionInput">Requesting Division / Unit</label>
            <input type="text" id="divisionInput" class="auth-input" value="Career Development Division (CDD)" style="padding-left: 1rem;">
          </div>
        </div>

        <div class="auth-field-group">
          <label class="auth-field-label" for="specialNotes">Special Equipment / Setup Notes (Optional)</label>
          <textarea id="specialNotes" class="auth-input" rows="3" placeholder="e.g. Needs 4 wireless microphones, podium banner stand, and registration tables." style="padding: 0.8rem 1rem; resize: vertical;"></textarea>
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         STEP 4: DOCUMENTS
         ========================================================================== -->
    <div class="wizard-step-view" data-step="4">
      <div class="wizard-form-card">
        <h3 class="wizard-form-title">Step 4: Supporting Documents Upload</h3>
        <p class="wizard-form-desc">Attach approved Special Order, Activity Design, or official request endorsement memo.</p>

        <div style="border: 2px dashed #b8ccbe; border-radius: 12px; padding: 3rem 2rem; text-align: center; background: #fbfdfc; cursor: pointer;">
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="1.8" style="margin-bottom: 0.75rem;">
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
        <p class="wizard-form-desc">Please verify your reservation particulars before final submission to the administrative approving authority.</p>

        <div style="background: #f7faf8; border: 1.5px solid #dce8e0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.25rem;">
            <div>
              <span style="font-size: 0.78rem; font-weight: 700; color: #60796b; text-transform: uppercase;">Selected Venue</span>
              <h4 id="summaryVenueName" style="color: #175432; font-size: 1.2rem; margin-top: 0.2rem;">Function Hall</h4>
            </div>
            <div>
              <span style="font-size: 0.78rem; font-weight: 700; color: #60796b; text-transform: uppercase;">Capacity</span>
              <h4 id="summaryVenueCapacity" style="color: #192e22; font-size: 1.1rem; margin-top: 0.2rem;">150 - 200 PAX</h4>
            </div>
            <div>
              <span style="font-size: 0.78rem; font-weight: 700; color: #60796b; text-transform: uppercase;">Rate / Tariff</span>
              <h4 id="summaryVenueRate" style="color: #192e22; font-size: 1.1rem; margin-top: 0.2rem;">₱5,000/day</h4>
            </div>
          </div>
          <div style="font-size: 0.88rem; color: #435b4d; border-top: 1px solid #e1ece4; padding-top: 1rem;">
            <span>Requested by: <strong>Juan Dela Cruz</strong> (ATI Staff, CDD)</span> &bull; 
            <span>Status after submission: <strong>Pending Administrative Officer Review</strong></span>
          </div>
        </div>

        <button type="button" class="btn-proceed-step" id="btnSubmitFinalReservation" style="width: 100%; justify-content: center; border-radius: 10px; padding: 1rem;">
          <svg viewBox="0 0 24 24" fill="none">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Submit Official Reservation Request</span>
        </button>
      </div>
    </div>

    <!-- Bottom Step Navigation Actions -->
    <div class="booking-bottom-actions">
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
  </main>

  <script src="js/booking.js?v=<?php echo time(); ?>"></script>
</body>
</html>
