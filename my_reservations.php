<?php
/**
 * ATI Facility & Dormitory Reservation Portal
 * User Dashboard: My Reservations
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Reservations | ATI Facility Reservation Portal</title>
  <meta name="description" content="View and track your facility and dormitory reservation requests with the Agricultural Training Institute.">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/my_reservations.css">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
</head>
<body class="my-res-body">

  <!-- ==========================================================================
       TOPBAR NAVIGATION
       ========================================================================== -->
  <header class="booking-topbar">
    <div class="booking-topbar-container">
      <!-- Brand Logo & Title -->
      <a href="home.php" class="booking-brand" style="text-decoration: none;" title="ATI Reservation Portal">
        <img src="assets/images/ATI_Logo.png" alt="Agricultural Training Institute Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>ATI Facility Reservation Portal</h1>
          <p>Department of Agriculture &bull; Central Office</p>
        </div>
      </a>

      <!-- Navigation Links -->
      <nav class="booking-nav-center" aria-label="Portal Navigation">
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
            <path d="M12 5v14"></path>
            <path d="M5 12h14"></path>
          </svg>
          <span>New Reservation</span>
        </a>

        <!-- My Reservations (Active) -->
        <a href="my_reservations.php" class="booking-nav-item active">
          <svg viewBox="0 0 24 24" fill="none">
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count" id="navBadgeCount">2</span>
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
          
          <a href="home.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <span class="drawer-link-text">Home</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="booking.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
            </div>
            <span class="drawer-link-text">New Reservation</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link active">
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
          
          <a href="javascript:void(0)" class="drawer-nav-link" onclick="showToast('Profile management available in next administrative release.');">
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
       MAIN CONTENT
       ========================================================================== -->
  <main class="my-res-main-wrapper">
    <!-- Header Section -->
    <section class="my-res-header">
      <div class="my-res-header-text">
        <div class="my-res-top-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span>Official Request Tracker</span>
        </div>
        <h2>My Facility & Dormitory Reservations</h2>
        <p>Monitor your reservation requests, follow live routing through approving administrative units, download official booking slips, or view security gate passes.</p>
      </div>

      <a href="booking.php" class="btn-new-res-action">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>New Reservation</span>
      </a>
    </section>

    <!-- Summary KPI Stat Cards -->
    <section class="my-res-stats-grid" aria-label="Reservation Summary Statistics">
      <!-- Total -->
      <div class="my-res-stat-card active-filter" data-filter="all">
        <div class="stat-icon-box green">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiTotal">5</span>
          <span class="stat-label">Total Submitted</span>
        </div>
      </div>

      <!-- Pending -->
      <div class="my-res-stat-card" data-filter="pending">
        <div class="stat-icon-box amber">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiPending">2</span>
          <span class="stat-label">Pending Review</span>
        </div>
      </div>

      <!-- Confirmed -->
      <div class="my-res-stat-card" data-filter="approved">
        <div class="stat-icon-box blue">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiApproved">2</span>
          <span class="stat-label">Confirmed & Approved</span>
        </div>
      </div>

      <!-- Completed -->
      <div class="my-res-stat-card" data-filter="completed">
        <div class="stat-icon-box slate">
          <svg viewBox="0 0 24 24" fill="none">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiCompleted">1</span>
          <span class="stat-label">Past / Completed</span>
        </div>
      </div>
    </section>

    <!-- Filter & Search Toolbar -->
    <div class="my-res-toolbar-card">
      <!-- Tabs -->
      <div class="filter-tabs-group" role="tablist">
        <button type="button" class="filter-tab-btn active" data-filter="all">
          <span>All Requests</span>
          <span class="tab-count-pill" id="tabCountAll">5</span>
        </button>
        <button type="button" class="filter-tab-btn" data-filter="pending">
          <span>Pending Review</span>
          <span class="tab-count-pill" id="tabCountPending">2</span>
        </button>
        <button type="button" class="filter-tab-btn" data-filter="approved">
          <span>Confirmed & Approved</span>
          <span class="tab-count-pill" id="tabCountApproved">2</span>
        </button>
        <button type="button" class="filter-tab-btn" data-filter="completed">
          <span>Completed</span>
          <span class="tab-count-pill" id="tabCountCompleted">1</span>
        </button>
      </div>

      <!-- Controls -->
      <div class="toolbar-controls-right">
        <!-- Search Input -->
        <div class="search-box-wrap">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" id="resSearchInput" class="search-input" placeholder="Search by Ref #, Activity title...">
        </div>

        <!-- Venue Filter -->
        <select class="venue-filter-select" id="resVenueFilter" aria-label="Filter by venue">
          <option value="all">All Facilities</option>
          <option value="function-hall">Function Hall</option>
          <option value="training-hall">Training Hall A</option>
          <option value="boardroom">Boardroom</option>
          <option value="mess-hall">Mess Hall</option>
          <option value="dormitory">Dormitory</option>
        </select>
      </div>
    </div>

    <!-- ==========================================================================
         RESERVATIONS LIST CARDS
         ========================================================================== -->
    <section class="reservations-container" id="reservationsList">

      <!-- CARD 1: FUNCTION HALL (PENDING ADMIN REVIEW) -->
      <article class="res-card" 
               data-status="pending" 
               data-venue="function-hall" 
               data-ref="ATI-RES-2026-1042" 
               data-venue-title="Function Hall"
               data-dates="Oct 14 - 16, 2026 (3 Days)"
               data-time="08:00 AM - 05:00 PM (Full Day)"
               data-pax="120 Attendees"
               data-division="Career Development Division (CDD)"
               data-status-text="Pending Administrative Clearance">
        
        <!-- Card Top Bar -->
        <div class="res-card-topbar">
          <div class="res-ref-group">
            <span class="res-ref-tag">ATI-RES-2026-1042</span>
            <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-1042" title="Copy Reference Code">
              <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
            <span class="res-timestamp">&bull; Submitted on Oct 02, 2026</span>
          </div>

          <div class="res-status-badge pending">
            <span class="status-dot-pulse"></span>
            <span>Pending Administrative Clearance</span>
          </div>
        </div>

        <!-- Card Main Body -->
        <div class="res-card-body">
          <div class="res-venue-thumb">
            <img src="assets/images/function_hall.jpg" alt="ATI Function Hall">
            <span class="venue-thumb-pill">Function Hall</span>
          </div>

          <div class="res-event-info">
            <h3 class="res-event-title">Regional Agricultural Extension Coordinators Training 2026</h3>
            
            <div class="res-details-chips">
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><strong>Date:</strong> Oct 14 – 16, 2026 (3 Days)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><strong>Time:</strong> 08:00 AM – 05:00 PM</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span><strong>Attendees:</strong> 120 Delegates</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span><strong>Division:</strong> Career Development Division (CDD)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><strong>Tariff:</strong> Official ATI Activity (100% Waived)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Workflow Stepper -->
        <div class="res-workflow-section">
          <div class="workflow-section-title">
            <span>Official Government Routing Progress</span>
            <span style="color: #b45309; font-weight: 700;">Step 3 of 4: In Progress</span>
          </div>

          <div class="workflow-stepper">
            <!-- Step 1 -->
            <div class="wf-step completed">
              <div class="wf-step-node">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Request Submitted</span>
                <span class="wf-step-sub">Oct 02, 09:15 AM</span>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="wf-step completed">
              <div class="wf-step-node">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Division Endorsed</span>
                <span class="wf-step-sub">Dr. M. Villacorta (CDD)</span>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="wf-step current">
              <div class="wf-step-node">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Admin Services Clearance</span>
                <span class="wf-step-sub">Under Evaluation</span>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="wf-step upcoming">
              <div class="wf-step-node">4</div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Approval Of Request</span>
                <span class="wf-step-sub">Awaiting Approval</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="res-card-actions">
          <div class="res-actions-left">
            <span>Special Setup: 4 wireless mics, podium & AV projector requested</span>
          </div>

          <div class="res-actions-right">
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
              <span>View Details & Routing</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-1042">
              <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              <span>Download Slip</span>
            </button>
            <button type="button" class="btn-card-action outline-danger js-cancel-res">
              <svg viewBox="0 0 24 24" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              <span>Cancel Request</span>
            </button>
          </div>
        </div>
      </article>

      <!-- CARD 2: TRAINING HALL A (PENDING DIVISION ENDORSEMENT) -->
      <article class="res-card" 
               data-status="pending" 
               data-venue="training-hall" 
               data-ref="ATI-RES-2026-1039" 
               data-venue-title="Training Hall A"
               data-dates="Oct 22 - 23, 2026 (2 Days)"
               data-time="08:30 AM - 04:30 PM"
               data-pax="45 Attendees"
               data-division="Partnership & Accreditation Division (PAD)"
               data-status-text="Pending Division Endorsement">
        
        <!-- Card Top Bar -->
        <div class="res-card-topbar">
          <div class="res-ref-group">
            <span class="res-ref-tag">ATI-RES-2026-1039</span>
            <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-1039" title="Copy Reference Code">
              <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
            <span class="res-timestamp">&bull; Submitted on Oct 01, 2026</span>
          </div>

          <div class="res-status-badge pending">
            <span class="status-dot-pulse"></span>
            <span>Pending Division Endorsement</span>
          </div>
        </div>

        <!-- Card Main Body -->
        <div class="res-card-body">
          <div class="res-venue-thumb">
            <img src="assets/images/training_hall.jpg" alt="ATI Training Hall A">
            <span class="venue-thumb-pill">Training Hall A</span>
          </div>

          <div class="res-event-info">
            <h3 class="res-event-title">Smart Agriculture Technologies Demonstration Workshop</h3>
            
            <div class="res-details-chips">
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><strong>Date:</strong> Oct 22 – 23, 2026 (2 Days)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><strong>Time:</strong> 08:30 AM – 04:30 PM</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span><strong>Attendees:</strong> 45 Participants</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span><strong>Division:</strong> Partnership & Accreditation Division (PAD)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Workflow Stepper -->
        <div class="res-workflow-section">
          <div class="workflow-section-title">
            <span>Official Government Routing Progress</span>
            <span style="color: #b45309; font-weight: 700;">Step 2 of 4: In Progress</span>
          </div>

          <div class="workflow-stepper">
            <div class="wf-step completed">
              <div class="wf-step-node">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Request Submitted</span>
                <span class="wf-step-sub">Oct 01, 04:30 PM</span>
              </div>
            </div>

            <div class="wf-step current">
              <div class="wf-step-node">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Division Endorsement</span>
                <span class="wf-step-sub">For Division Chief Sign</span>
              </div>
            </div>

            <div class="wf-step upcoming">
              <div class="wf-step-node">3</div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Admin Clearance</span>
                <span class="wf-step-sub">Next Queue</span>
              </div>
            </div>

            <div class="wf-step upcoming">
              <div class="wf-step-node">4</div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Approval Of Request</span>
                <span class="wf-step-sub">Final Step</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="res-card-actions">
          <div class="res-actions-left">
            <span>Requested layout: Classroom style with 6 discussion tables</span>
          </div>

          <div class="res-actions-right">
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
              <span>View Details & Routing</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-1039">
              <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              <span>Download Slip</span>
            </button>
            <button type="button" class="btn-card-action outline-danger js-cancel-res">
              <svg viewBox="0 0 24 24" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              <span>Cancel Request</span>
            </button>
          </div>
        </div>
      </article>

      <!-- CARD 3: BOARDROOM (APPROVED & CONFIRMED) -->
      <article class="res-card" 
               data-status="approved" 
               data-venue="boardroom" 
               data-ref="ATI-RES-2026-0985" 
               data-venue-title="Boardroom"
               data-dates="Oct 08, 2026 (1 Day)"
               data-time="09:00 AM - 05:00 PM"
               data-pax="25 Dignitaries"
               data-division="Office of the Director (OD)"
               data-status-text="Confirmed & Approved">
        
        <!-- Card Top Bar -->
        <div class="res-card-topbar">
          <div class="res-ref-group">
            <span class="res-ref-tag">ATI-RES-2026-0985</span>
            <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0985" title="Copy Reference Code">
              <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
            <span class="res-timestamp">&bull; Approved on Sep 28, 2026</span>
          </div>

          <div class="res-status-badge approved">
            <span class="status-dot-pulse"></span>
            <span>Confirmed & Approved</span>
          </div>
        </div>

        <!-- Card Main Body -->
        <div class="res-card-body">
          <div class="res-venue-thumb">
            <img src="assets/images/boardroom.jpg" alt="ATI Boardroom">
            <span class="venue-thumb-pill">Executive Boardroom</span>
          </div>

          <div class="res-event-info">
            <h3 class="res-event-title">Executive Directorate Quarterly Planning & Strategy Session</h3>
            
            <div class="res-details-chips">
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><strong>Date:</strong> Oct 08, 2026 (1 Day)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><strong>Time:</strong> 09:00 AM – 05:00 PM</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span><strong>Attendees:</strong> 25 Attendees</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span><strong>Division:</strong> Office of the Director (OD)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span><strong>Approved by:</strong> Engr. R. Santos (Admin Chief)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Workflow Stepper -->
        <div class="res-workflow-section">
          <div class="workflow-section-title">
            <span>Official Government Routing Progress</span>
            <span style="color: #16a34a; font-weight: 700;">Completed & Confirmed</span>
          </div>

          <div class="workflow-stepper">
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Submitted</span>
                <span class="wf-step-sub">Sep 26, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Division Head Endorsed</span>
                <span class="wf-step-sub">Sep 27, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Admin Cleared</span>
                <span class="wf-step-sub">Sep 28, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Pass Issued</span>
                <span class="wf-step-sub">Verified & Active</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="res-card-actions">
          <div class="res-actions-left">
            <span style="color: #166534; font-weight: 700;">&#10003; Security notice sent to Building Guards & Audio Visual Technicians</span>
          </div>

          <div class="res-actions-right">
            <button type="button" class="btn-card-action primary" onclick="showGatePassModal('ATI-RES-2026-0985', 'Executive Directorate Quarterly Planning & Strategy Session', 'Executive Boardroom', 'Oct 08, 2026')">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
              <span>View Gate Pass & QR</span>
            </button>
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
              <span>View Details</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0985">
              <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              <span>Download Slip</span>
            </button>
          </div>
        </div>
      </article>

      <!-- CARD 4: DORMITORY (APPROVED & CONFIRMED) -->
      <article class="res-card" 
               data-status="approved" 
               data-venue="dormitory" 
               data-ref="ATI-RES-2026-0941" 
               data-venue-title="ATI Dormitory (Executive Wing)"
               data-dates="Oct 05 - 07, 2026 (2 Nights)"
               data-time="Check-in 02:00 PM • Check-out 12:00 PM"
               data-pax="8 Delegates (4 Twin Rooms)"
               data-division="Career Development Division (CDD)"
               data-status-text="Confirmed & Approved">
        
        <!-- Card Top Bar -->
        <div class="res-card-topbar">
          <div class="res-ref-group">
            <span class="res-ref-tag">ATI-RES-2026-0941</span>
            <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0941" title="Copy Reference Code">
              <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
            <span class="res-timestamp">&bull; Approved on Sep 22, 2026</span>
          </div>

          <div class="res-status-badge approved">
            <span class="status-dot-pulse"></span>
            <span>Confirmed & Approved</span>
          </div>
        </div>

        <!-- Card Main Body -->
        <div class="res-card-body">
          <div class="res-venue-thumb">
            <img src="assets/images/dormitory.jpg" alt="ATI Dormitory">
            <span class="venue-thumb-pill">Dormitory</span>
          </div>

          <div class="res-event-info">
            <h3 class="res-event-title">Guest Lecturers & Resource Persons Lodging (Visayas & Mindanao Trainers)</h3>
            
            <div class="res-details-chips">
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><strong>Duration:</strong> Oct 05 – 07, 2026 (2 Nights)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><strong>Check-in:</strong> 02:00 PM &bull; Check-out: 12:00 PM</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span><strong>Rooms:</strong> Rooms 201, 202, 203, 204 (Executive Wing)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span><strong>Guests:</strong> 8 Resource Speakers</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Workflow Stepper -->
        <div class="res-workflow-section">
          <div class="workflow-section-title">
            <span>Official Government Routing Progress</span>
            <span style="color: #16a34a; font-weight: 700;">Completed & Confirmed</span>
          </div>

          <div class="workflow-stepper">
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Submitted</span>
                <span class="wf-step-sub">Sep 20, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Division Endorsed</span>
                <span class="wf-step-sub">Sep 21, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Dorm Custodian Cleared</span>
                <span class="wf-step-sub">Sep 22, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Keys Allocation Ready</span>
                <span class="wf-step-sub">Lobby Desk</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="res-card-actions">
          <div class="res-actions-left">
            <span>Dormitory Guidelines: Towels & Linens provided. Curfew: 10:00 PM for external visitors.</span>
          </div>

          <div class="res-actions-right">
            <button type="button" class="btn-card-action primary" onclick="showGatePassModal('ATI-RES-2026-0941', 'Guest Lecturers & Resource Persons Lodging', 'ATI Dormitory Executive Wing', 'Oct 05 - 07, 2026')">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
              <span>View Gate Pass & QR</span>
            </button>
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
              <span>View Details</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0941">
              <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              <span>Download Slip</span>
            </button>
          </div>
        </div>
      </article>

      <!-- CARD 5: MESS HALL (COMPLETED / HISTORICAL) -->
      <article class="res-card" 
               data-status="completed" 
               data-venue="mess-hall" 
               data-ref="ATI-RES-2026-0812" 
               data-venue-title="Mess Hall"
               data-dates="Sep 25, 2026 (1 Day)"
               data-time="11:00 AM - 03:00 PM"
               data-pax="180 Attendees"
               data-division="Administrative and Finance Unit"
               data-status-text="Completed">
        
        <!-- Card Top Bar -->
        <div class="res-card-topbar">
          <div class="res-ref-group">
            <span class="res-ref-tag">ATI-RES-2026-0812</span>
            <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0812" title="Copy Reference Code">
              <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
            <span class="res-timestamp">&bull; Concluded on Sep 25, 2026</span>
          </div>

          <div class="res-status-badge completed">
            <span class="status-dot-pulse"></span>
            <span>Completed & Cleared</span>
          </div>
        </div>

        <!-- Card Main Body -->
        <div class="res-card-body">
          <div class="res-venue-thumb">
            <img src="assets/images/mess_hall.jpg" alt="ATI Mess Hall">
            <span class="venue-thumb-pill">Mess Hall</span>
          </div>

          <div class="res-event-info">
            <h3 class="res-event-title">ATI Mid-Year General Assembly & Fellowship Gathering</h3>
            
            <div class="res-details-chips">
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><strong>Date:</strong> Sep 25, 2026 (1 Day)</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><strong>Time:</strong> 11:00 AM – 03:00 PM</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span><strong>Attendance:</strong> 180 Personnel</span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span><strong>Division:</strong> Administrative & Finance Unit</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Workflow Stepper -->
        <div class="res-workflow-section">
          <div class="workflow-section-title">
            <span>Official Government Routing Progress</span>
            <span style="color: #64748b; font-weight: 700;">Concluded & Post-Event Inspected</span>
          </div>

          <div class="workflow-stepper">
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Submitted</span>
                <span class="wf-step-sub">Sep 10, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Endorsed</span>
                <span class="wf-step-sub">Sep 12, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Executed</span>
                <span class="wf-step-sub">Sep 25, 2026</span>
              </div>
            </div>
            <div class="wf-step completed">
              <div class="wf-step-node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
              <div class="wf-step-text-wrap">
                <span class="wf-step-label">Archived</span>
                <span class="wf-step-sub">Facility Cleared</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="res-card-actions">
          <div class="res-actions-left">
            <span>Post-activity facility condition report: Clean and undamaged.</span>
          </div>

          <div class="res-actions-right">
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
              <span>View Archived Details</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0812">
              <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              <span>Download Slip</span>
            </button>
          </div>
        </div>
      </article>

    </section>

    <!-- Empty State Result -->
    <div class="empty-res-state" id="emptyResState">
      <div class="empty-res-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></line>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
      </div>
      <h3>No Reservations Found</h3>
      <p>We couldn't find any reservation requests matching your selected filter criteria. Try adjusting your search query or venue selection.</p>
      <button type="button" class="btn-new-res-action" onclick="document.getElementById('resSearchInput').value=''; document.getElementById('resVenueFilter').value='all'; document.querySelector('.filter-tab-btn[data-filter=\'all\']').click();">
        <span>Reset Filters</span>
      </button>
    </div>
  </main>

  <!-- ==========================================================================
       MODAL 1: VIEW DETAILS & OFFICIAL ROUTING SLIP
       ========================================================================== -->
  <div class="modal-overlay" id="resDetailsModal" role="dialog" aria-modal="true">
    <div class="modal-card">
      <button type="button" class="modal-close-btn js-close-res-modal" aria-label="Close modal">&times;</button>
      
      <div class="modal-header-banner">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
          <span style="font-size: 0.82rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.75rem; border-radius: 9999px;" id="modalResVenue">Function Hall</span>
          <span class="res-status-badge" id="modalResStatusBadge"></span>
        </div>
        <h3 id="modalResTitle" style="font-size: 1.5rem; color: #ffffff; line-height: 1.3;">Event Title</h3>
        <p style="font-size: 0.85rem; opacity: 0.85; margin-top: 0.35rem;">Reference Code: <strong id="modalResRef" style="color: #fde047;">ATI-RES-2026-1042</strong></p>
      </div>

      <div class="modal-content-area">
        <!-- Event Particulars Grid -->
        <div class="modal-meta-grid">
          <div class="modal-meta-item">
            <div class="modal-meta-label">Schedule & Duration</div>
            <div class="modal-meta-val" id="modalResDates">Oct 14 - 16, 2026</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Time Window</div>
            <div class="modal-meta-val" id="modalResTime">08:00 AM - 05:00 PM</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Requesting Unit / Division</div>
            <div class="modal-meta-val" id="modalResDivision">Career Development Division</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Estimated Attendees</div>
            <div class="modal-meta-val" id="modalResPax">120 Delegates</div>
          </div>
        </div>

        <!-- Attached Documents -->
        <div style="margin-bottom: 1.5rem;">
          <h4 style="font-size: 0.88rem; font-weight: 800; color: #174d2f; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Attached Official Documents</h4>
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1px solid #dce8e0; padding: 0.7rem 0.9rem; border-radius: 8px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
              <div style="overflow: hidden;">
                <div style="font-size: 0.82rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Activity_Design_Approved.pdf</div>
                <div style="font-size: 0.7rem; color: #627b6c;">1.8 MB &bull; Signed Copy</div>
              </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1px solid #dce8e0; padding: 0.7rem 0.9rem; border-radius: 8px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
              <div style="overflow: hidden;">
                <div style="font-size: 0.82rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Special_Order_2026_CDD.pdf</div>
                <div style="font-size: 0.7rem; color: #627b6c;">640 KB &bull; Verified</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Official Routing Trail Card -->
        <div class="modal-routing-card">
          <div class="routing-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span>Official Government Action Routing Trail</span>
          </div>
          <div id="modalRoutingList">
            <!-- Dynamically populated via JS -->
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="modal-footer-actions">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Close</button>
          <button type="button" class="btn-card-action primary js-download-slip" onclick="showToast('Printing official routing pass...');">
            <svg viewBox="0 0 24 24" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            <span>Print Official Slip</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 2: OFFICIAL GATE PASS & QR CODE
       ========================================================================== -->
  <div class="modal-overlay" id="gatePassModal" role="dialog" aria-modal="true">
    <div class="modal-card" style="max-width: 540px;">
      <button type="button" class="modal-close-btn js-close-res-modal" aria-label="Close modal">&times;</button>
      
      <div class="modal-header-banner" style="text-align: center; padding: 1.75rem;">
        <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.85rem; border-radius: 9999px;">ATI Security & Venue Access Pass</span>
        <h3 style="font-size: 1.35rem; color: #ffffff; margin-top: 0.5rem;" id="gpVenueName">Executive Boardroom</h3>
        <p style="font-size: 0.85rem; opacity: 0.88;" id="gpEventTitle">Executive Directorate Quarterly Planning Session</p>
      </div>

      <div class="modal-content-area" style="padding: 1.75rem;">
        <div class="gatepass-qr-card">
          <!-- Stylized QR Code SVG -->
          <div class="qr-code-box">
            <svg viewBox="0 0 100 100" fill="#174d2f">
              <rect x="0" y="0" width="30" height="30" rx="3" fill="#174d2f"></rect>
              <rect x="5" y="5" width="20" height="20" rx="2" fill="#ffffff"></rect>
              <rect x="10" y="10" width="10" height="10" rx="1" fill="#174d2f"></rect>

              <rect x="70" y="0" width="30" height="30" rx="3" fill="#174d2f"></rect>
              <rect x="75" y="5" width="20" height="20" rx="2" fill="#ffffff"></rect>
              <rect x="80" y="10" width="10" height="10" rx="1" fill="#174d2f"></rect>

              <rect x="0" y="70" width="30" height="30" rx="3" fill="#174d2f"></rect>
              <rect x="5" y="75" width="20" height="20" rx="2" fill="#ffffff"></rect>
              <rect x="10" y="80" width="10" height="10" rx="1" fill="#174d2f"></rect>

              <!-- Central QR matrices -->
              <rect x="36" y="8" width="8" height="8" rx="1"></rect>
              <rect x="48" y="16" width="8" height="8" rx="1"></rect>
              <rect x="36" y="24" width="8" height="8" rx="1"></rect>
              <rect x="40" y="40" width="20" height="20" rx="2"></rect>
              <rect x="12" y="44" width="8" height="8" rx="1"></rect>
              <rect x="24" y="44" width="8" height="8" rx="1"></rect>
              <rect x="68" y="44" width="8" height="8" rx="1"></rect>
              <rect x="80" y="44" width="8" height="8" rx="1"></rect>
              <rect x="36" y="68" width="8" height="8" rx="1"></rect>
              <rect x="48" y="76" width="8" height="8" rx="1"></rect>
              <rect x="68" y="68" width="8" height="8" rx="1"></rect>
              <rect x="80" y="80" width="8" height="8" rx="1"></rect>
            </svg>
          </div>

          <div style="font-family: monospace; font-size: 1.1rem; font-weight: 800; color: #174d2f; letter-spacing: 0.05em;" id="gpRefCode">ATI-RES-2026-0985</div>
          <div style="font-size: 0.78rem; color: #556c5e; margin-top: 0.2rem;">Scan upon arrival at Central Office Guardhouse / Venue Entrance</div>
        </div>

        <div style="background: #edf8f1; padding: 0.9rem 1.1rem; border-radius: 10px; border-left: 3px solid #175432; font-size: 0.82rem; color: #284433; margin-bottom: 1.25rem;">
          <strong>Authorized Schedule:</strong> <span id="gpEventDates">Oct 08, 2026</span><br>
          <em>Please present this digital pass or printed copy along with your official government ID.</em>
        </div>

        <div class="modal-footer-actions">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Close</button>
          <button type="button" class="btn-card-action primary" onclick="showToast('Gate pass saved to device.');">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Save / Download Pass</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 3: CANCEL RESERVATION CONFIRMATION
       ========================================================================== -->
  <div class="modal-overlay" id="cancelConfirmModal" role="dialog" aria-modal="true">
    <div class="modal-card" style="max-width: 500px;">
      <div style="padding: 1.75rem 2rem;">
        <div style="width: 52px; height: 52px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        </div>

        <h3 style="font-size: 1.3rem; font-weight: 800; color: #182c20; margin-bottom: 0.5rem;">Cancel Reservation Request?</h3>
        <p style="font-size: 0.88rem; color: #556c5e; line-height: 1.45; margin-bottom: 1.25rem;">
          Are you sure you want to cancel request <strong id="cancelResRef" style="color: #174d2f;">ATI-RES-2026-1042</strong> for <span id="cancelResTitle">this activity</span>? This will release the scheduled slot back to the master availability board.
        </p>

        <div style="margin-bottom: 1.5rem;">
          <label for="cancelReasonSelect" style="display: block; font-size: 0.78rem; font-weight: 800; color: #3b5042; text-transform: uppercase; margin-bottom: 0.4rem;">Reason for Cancellation</label>
          <select id="cancelReasonSelect" style="width: 100%; padding: 0.7rem 0.9rem; border: 1.5px solid #d5e2d8; border-radius: 8px; font-family: inherit; font-size: 0.88rem; color: #192b21; outline: none;">
            <option value="Schedule adjustment / postponement">Schedule adjustment / postponed by division</option>
            <option value="Change of preferred venue or facility">Change of preferred venue / room size</option>
            <option value="Alternative online / virtual platform used">Event shifted to virtual (Zoom/Teams)</option>
            <option value="Other administrative directives">Other official administrative directive</option>
          </select>
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Keep Reservation</button>
          <button type="button" class="btn-card-action outline-danger" id="btnConfirmCancel" style="background: #dc2626; color: #ffffff; border-color: #dc2626;">
            <span>Confirm Cancellation</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="js/my_reservations.js?v=<?php echo time(); ?>"></script>
</body>
</html>
