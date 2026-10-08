<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Staff Profile Management Page
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Staff Profile - Agricultural Training Institute</title>
  <meta name="description"
    content="View and manage your official ATI staff profile, contact information, credentials, and reservation preferences.">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="profile-body">

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
            <div class="user-avatar-circle" id="topbarAvatarCircle">JD</div>
            <div class="user-details">
              <div class="user-name" id="topbarUserName">Juan Dela Cruz</div>
              <div class="user-role" id="topbarUserRole">ATI Staff (CDD)</div>
            </div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-user-name" id="dropdownUserName">Juan Dela Cruz</div>
              <div class="dropdown-user-email" id="dropdownUserRole"><span class="user-verified-dot"></span> ATI
                Personnel &bull; CDD</div>
            </div>
            <a href="profile.php" class="dropdown-item active">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
              <span class="dropdown-item-badge">Active</span>
            </a>
            <a href="booking_history.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
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
          <div class="drawer-avatar" id="drawerAvatarCircle">JD</div>
          <div class="drawer-profile-info">
            <h5 id="drawerUserName">Juan Dela Cruz</h5>
            <p id="drawerUserRole">ATI Staff (CDD)</p>
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
          <a href="profile.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
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

  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;
      if (open) {
        overlay.style.display = 'block';
        void overlay.offsetHeight;
        overlay.classList.add('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      } else {
        overlay.classList.remove('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        setTimeout(function () {
          if (!overlay.classList.contains('show')) overlay.style.display = 'none';
        }, 280);
      }
    }
  </script>

  <!-- ==========================================================================
       MAIN PROFILE WRAPPER
       ========================================================================== -->
  <main class="profile-main-wrapper" id="profileMainContent">

    <!-- Page Location (My Profile alone) -->
    <nav class="profile-breadcrumb" aria-label="Page Location">
      <span class="current">My Profile</span>
    </nav>

    <!-- Hidden File Input for Avatar Upload -->
    <input type="file" id="profilePicInput" accept="image/png, image/jpeg, image/webp"
      aria-label="Upload profile photo">

    <!-- ==========================================================================
         HERO PROFILE BANNER CARD (CLEAN COVER, PERFECT ALIGNMENT & 100% READABLE)
         ========================================================================== -->
    <section class="profile-hero-card" aria-label="Staff Identity Banner">
      <div class="profile-hero-cover">
        <!-- Protective Contrast Overlay -->
        <div class="profile-hero-cover-overlay"></div>
      </div>

      <div class="profile-hero-body">
        <!-- Top Row: Overlapping Avatar on Left, Action Buttons on Right -->
        <div class="profile-hero-top-row">
          <div class="profile-avatar-wrap">
            <div class="profile-avatar-inner" id="heroAvatarContainer">
              <img src="" alt="Juan Dela Cruz Profile Picture" class="profile-avatar-img" id="heroAvatarImg"
                style="display: none;">
              <span class="profile-avatar-initials" id="heroAvatarInitials">JD</span>
            </div>
            <button type="button" class="btn-upload-photo" id="btnTriggerPhotoUpload"
              title="Upload or change profile photo" aria-label="Upload or change profile photo">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </button>
          </div>

          <!-- Hero Actions (Edit Profile / Save Changes) -->
          <div class="profile-hero-actions">
            <!-- View Mode Actions -->
            <button type="button" class="btn-profile-primary btn-view-mode" id="btnStartEditing">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
              <span>Edit Profile</span>
            </button>
            <a href="booking.php" class="btn-profile-secondary btn-view-mode">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <span>New Booking</span>
            </a>

            <!-- Edit Mode Actions -->
            <button type="button" class="btn-profile-primary btn-edit-mode" id="btnSaveProfile">
              <svg viewBox="0 0 24 24" fill="none">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Save Changes</span>
            </button>
            <button type="button" class="btn-profile-secondary btn-edit-mode" id="btnCancelEditing">
              <span>Cancel</span>
            </button>
          </div>
        </div>

        <!-- Identity Information - Completely situated on the white card background for maximum readability -->
        <div class="profile-hero-identity">
          <div class="profile-name-row">
            <h1 class="profile-name-display" id="displayFullName">Juan Dela Cruz</h1>
            <span class="profile-verified-badge">
              <svg viewBox="0 0 24 24" fill="none">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Verified Personnel</span>
            </span>
            <span class="edit-mode-indicator" id="editModeIndicator">
              <span class="status-dot-pulse" style="background: #d97706;"></span>
              <span>Edit Mode Active</span>
            </span>
          </div>

          <p class="profile-role-subtitle" id="displayRoleSubtitle">Training Specialist III &bull; Career Development
            Division (CDD)</p>

          <div class="profile-tag-ribbon">
            <span class="profile-tag-pill">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>ATI Central Office</span>
            </span>
            <span class="profile-tag-pill">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Regular Full-Time</span>
            </span>
            <span class="profile-tag-pill" style="color: #174d2f; background: #eaf5ee; border-color: #cce4d3;">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span id="displayHiredTag">Hired: March 15, 2018</span>
            </span>
            <button type="button" class="btn-profile-danger-text" id="btnRemovePhoto" style="display: none;"
              title="Remove current photo and restore initials">
              Remove Photo
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ==========================================================================
         EDIT MODE ALERT BANNER (ACTIVATES IN EDIT MODE ONLY)
         ========================================================================== -->
    <div class="profile-edit-banner" id="profileEditBanner">
      <div class="profile-edit-banner-content">
        <div class="profile-edit-banner-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
            </path>
          </svg>
        </div>
        <div class="profile-edit-banner-text">
          <h4>Profile Editing Mode Active</h4>
          <p>You can modify your personal contact and workstation details below. Official civil service records
            (Employee ID &amp; Hire Date) remain protected by HR. Click <strong>Save Changes</strong> to apply your
            updates or <strong>Cancel</strong> to discard.</p>
        </div>
      </div>
      <div class="profile-edit-banner-actions">
        <button type="button" class="btn-banner-save" id="btnBannerSave">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Save Changes</span>
        </button>
        <button type="button" class="btn-banner-cancel" id="btnBannerCancel">Cancel</button>
      </div>
    </div>

    <!-- ==========================================================================
         ACTIVITY KPI RIBBON (FUNCTIONAL, CLICKABLE & SENSIBLE ICONS)
         ========================================================================== -->
    <section class="profile-stats-ribbon" aria-label="Staff Reservation Activity">
      <!-- 1. Total Reservations -->
      <div class="profile-stat-box" data-kpi="total" title="Click to view all filed reservations" tabindex="0"
        role="button">
        <div class="profile-stat-icon green">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val">5</span>
          <span class="profile-stat-lbl">Total Reservations Filed</span>
          <a href="my_reservations.php" class="stat-click-hint" onclick="event.stopPropagation();"
            title="View all 5 bookings in Booking History">View all 5 bookings &rarr;</a>
        </div>
      </div>

      <!-- 2. Confirmed & Approved -->
      <div class="profile-stat-box" data-kpi="approved" title="Click to view approved bookings" tabindex="0"
        role="button">
        <div class="profile-stat-icon blue">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val">2</span>
          <span class="profile-stat-lbl">Confirmed &amp; Approved</span>
          <a href="my_reservations.php?filter=approved" class="stat-click-hint" onclick="event.stopPropagation();"
            title="View approved bookings in Booking History">View 2 approved &rarr;</a>
        </div>
      </div>

      <!-- 3. Under Admin Review -->
      <div class="profile-stat-box" data-kpi="pending" title="Click to view pending reviews" tabindex="0" role="button">
        <div class="profile-stat-icon amber">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val">2</span>
          <span class="profile-stat-lbl">Under Admin Review</span>
          <a href="my_reservations.php?filter=pending" class="stat-click-hint" onclick="event.stopPropagation();"
            title="View pending bookings in Booking History">View 2 pending &rarr;</a>
        </div>
      </div>

      <!-- 4. Government Service Tenure (Sensible Civil Service Medal Icon + Exact Hired Detail) -->
      <div class="profile-stat-box" data-kpi="service" title="Click to view official Civil Service hiring record"
        tabindex="0" role="button">
        <div class="profile-stat-icon gold">
          <!-- Official Public Service Ribbon / Civil Service Medal Icon -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="6"></circle>
            <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"></path>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val">8 Yrs, 7 Mos</span>
          <span class="profile-stat-lbl">ATI Government Service</span>
          <span class="stat-hired-detail">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span id="statHiredText">Hired: March 15, 2018</span>
          </span>
        </div>
      </div>
    </section>

    <!-- ==========================================================================
         PROFILE DETAILS SECTIONS (2x2 GRID)
         ========================================================================== -->
    <div class="profile-cards-grid">

      <!-- CARD 1: OFFICIAL STAFF & EMPLOYMENT INFORMATION -->
      <section class="profile-card" aria-label="Official Staff Details">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Staff Employment Details</span>
          </h3>
          <span class="profile-card-tag">Official Record</span>
        </div>

        <div class="profile-fields-grid">
          <!-- Full Name -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpFullName">Full Legal Name</label>
            <div class="profile-value-display" id="valFullName">Juan Dela Cruz</div>
            <input type="text" id="inpFullName" class="profile-input-control" value="Juan Dela Cruz"
              placeholder="First Name Last Name">
          </div>

          <!-- Employee ID (HR Protected - Readonly to avoid accidental changes) -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpEmployeeId">
              <span>Employee ID Number</span>
              <span class="badge-hr-pill" title="Protected Civil Service item">HR Locked</span>
            </label>
            <div class="profile-value-display profile-copyable-val">
              <span id="valEmployeeId">ATI-EMP-2024-0891</span>
              <button type="button" class="btn-mini-copy" id="btnCopyEmpId" title="Copy Employee ID">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
            </div>
            <input type="text" id="inpEmployeeId" class="profile-input-control profile-input-readonly"
              value="ATI-EMP-2024-0891" readonly placeholder="ATI-EMP-YYYY-XXXX">
            <div class="field-locked-note">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <span>HR Verified Record &bull; Cannot be altered</span>
            </div>
          </div>

          <!-- Position / Job Title -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpPosition">Official Designation / Position</label>
            <div class="profile-value-display" id="valPosition">Training Specialist III</div>
            <input type="text" id="inpPosition" class="profile-input-control" value="Training Specialist III"
              placeholder="e.g. Training Specialist III">
          </div>

          <!-- Division / Unit -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpDivision">Division / Operating Unit</label>
            <div class="profile-value-display" id="valDivision">Career Development Division (CDD)</div>
            <select id="inpDivision" class="profile-input-control profile-select-control">
              <option value="Career Development Division (CDD)" selected>Career Development Division (CDD)</option>
              <option value="Partnership & Accreditation Division (PAD)">Partnership & Accreditation Division (PAD)
              </option>
              <option value="Information Services Division (ISD)">Information Services Division (ISD)</option>
              <option value="Administrative & Finance Unit (AFU)">Administrative & Finance Unit (AFU)</option>
              <option value="Policy & Planning Division (PPD)">Policy & Planning Division (PPD)</option>
              <option value="Office of the Director (OD)">Office of the Director (OD)</option>
            </select>
          </div>

          <!-- Date Originally Hired (Civil Service Appointed - Readonly to avoid accidental changes) -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpDateHired">
              <span>Date Originally Appointed / Hired</span>
              <span class="badge-hr-pill" title="Official Appointment Record">Civil Service</span>
            </label>
            <div class="profile-value-display" id="valDateHired">March 15, 2018 (Regular)</div>
            <input type="text" id="inpDateHired" class="profile-input-control profile-input-readonly"
              value="March 15, 2018" readonly placeholder="March 15, 2018">
            <div class="field-locked-note">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="8" r="6"></circle>
                <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"></path>
              </svg>
              <span>Fixed Civil Service Appointment Date</span>
            </div>
          </div>

          <!-- Employment Status -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpEmpStatus">Employment Status</label>
            <div class="profile-value-display" id="valEmpStatus">Permanent Regular Staff</div>
            <select id="inpEmpStatus" class="profile-input-control profile-select-control">
              <option value="Permanent Regular Staff" selected>Permanent Regular Staff</option>
              <option value="Contract of Service (COS)">Contract of Service (COS)</option>
              <option value="Co-Terminus Personnel">Co-Terminus Personnel</option>
              <option value="Special Detail / Seconded">Special Detail / Seconded</option>
            </select>
          </div>

          <!-- Immediate Supervisor -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpSupervisor">Immediate Division Chief</label>
            <div class="profile-value-display" id="valSupervisor">Dr. Ma. Cecilia Villacorta</div>
            <input type="text" id="inpSupervisor" class="profile-input-control" value="Dr. Ma. Cecilia Villacorta"
              placeholder="Division Chief Name">
          </div>

          <!-- Station / Location -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpStation">Office Station / Assignment</label>
            <div class="profile-value-display" id="valStation">ATI Central Office &bull; Diliman, QC</div>
            <input type="text" id="inpStation" class="profile-input-control" value="ATI Central Office • Diliman, QC"
              placeholder="Office Location">
          </div>
        </div>
      </section>

      <!-- CARD 2: CONTACT & COMMUNICATIONS (8 BALANCED FIELDS MATCHING CARD 1) -->
      <section class="profile-card" aria-label="Contact and Communications">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <svg viewBox="0 0 24 24" fill="none">
              <path
                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
              </path>
            </svg>
            <span>Contact &amp; Workstation</span>
          </h3>
          <span class="profile-card-tag">Communication</span>
        </div>

        <div class="profile-fields-grid">
          <!-- Official Gov Email -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpEmail">Official Gov Email Address</label>
            <div class="profile-value-display" id="valEmail">juan.delacruz@ati.da.gov.ph</div>
            <input type="email" id="inpEmail" class="profile-input-control" value="juan.delacruz@ati.da.gov.ph"
              placeholder="username@ati.da.gov.ph">
          </div>

          <!-- Alternate / Recovery Email -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpAltEmail">Alternate / Personal Email</label>
            <div class="profile-value-display" id="valAltEmail">jdelacruz.ati@gmail.com</div>
            <input type="email" id="inpAltEmail" class="profile-input-control" value="jdelacruz.ati@gmail.com"
              placeholder="personal.email@domain.com">
          </div>

          <!-- Mobile Phone -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpPhone">Mobile / SMS Contact</label>
            <div class="profile-value-display" id="valPhone">+63 917 842 5901</div>
            <input type="tel" id="inpPhone" class="profile-input-control" value="+63 917 842 5901"
              placeholder="+63 9XX XXX XXXX">
          </div>

          <!-- Emergency Contact -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpEmergencyContact">Emergency Contact &amp; Phone</label>
            <div class="profile-value-display" id="valEmergencyContact">Maria Dela Cruz (Spouse) &bull; 0918 123 4567
            </div>
            <input type="text" id="inpEmergencyContact" class="profile-input-control"
              value="Maria Dela Cruz (Spouse) • 0918 123 4567" placeholder="Name (Relation) • Contact No.">
          </div>

          <!-- Landline Trunk -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpLandline">Office Trunkline</label>
            <div class="profile-value-display" id="valLandline">(02) 8929-8541</div>
            <input type="text" id="inpLandline" class="profile-input-control" value="(02) 8929-8541"
              placeholder="(02) 8XXX-XXXX">
          </div>

          <!-- Local Extension -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpExtension">Local Extension</label>
            <div class="profile-value-display" id="valExtension">Local Ext. 214</div>
            <input type="text" id="inpExtension" class="profile-input-control" value="Local Ext. 214"
              placeholder="Ext. XXX">
          </div>

          <!-- Office Building & Station -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpBuilding">Office Building &amp; Floor</label>
            <div class="profile-value-display" id="valBuilding">ATI Central Bldg &bull; 2nd Floor</div>
            <input type="text" id="inpBuilding" class="profile-input-control" value="ATI Central Bldg • 2nd Floor"
              placeholder="Building & Floor">
          </div>

          <!-- Room / Desk -->
          <div class="profile-field-group">
            <label class="profile-label" for="inpDesk">Office Room &amp; Desk</label>
            <div class="profile-value-display" id="valDesk">CDD Wing &bull; Desk 204</div>
            <input type="text" id="inpDesk" class="profile-input-control" value="CDD Wing • Desk 204"
              placeholder="Room / Desk Designation">
          </div>
        </div>
      </section>

      <!-- CARD 3: RESERVATION PREFERENCES & ROUTING ALERTS -->
      <section class="profile-card" aria-label="Routing Alerts and Preferences">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span>Reservation Routing Alerts</span>
          </h3>
          <span class="profile-card-tag">Preferences</span>
        </div>

        <div class="pref-toggle-list">
          <!-- Toggle 1: Routing Step SMS Alert -->
          <div class="pref-toggle-item">
            <div class="pref-toggle-text">
              <h5>SMS Updates for Approval Progress</h5>
              <p>Receive live text notifications whenever an administrative unit signs off or requests adjustments.</p>
            </div>
            <label class="switch-control" aria-label="Toggle SMS Approval Updates">
              <input type="checkbox" id="prefSmsUpdates" checked>
              <span class="switch-slider"></span>
            </label>
          </div>

          <!-- Toggle 2: Gate Pass Auto-Email -->
          <div class="pref-toggle-item">
            <div class="pref-toggle-text">
              <h5>Email Gate Pass PDF Automatically</h5>
              <p>Immediately receive an official QR-coded printable security gate pass once reservation is approved.</p>
            </div>
            <label class="switch-control" aria-label="Toggle Gate Pass Auto-Email">
              <input type="checkbox" id="prefEmailPass" checked>
              <span class="switch-slider"></span>
            </label>
          </div>

          <!-- Toggle 3: Supervisor CC -->
          <div class="pref-toggle-item">
            <div class="pref-toggle-text">
              <h5>CC Division Chief on Submissions</h5>
              <p>Automatically forward a copy of newly filed reservation request slips to division chief's inbox.</p>
            </div>
            <label class="switch-control" aria-label="Toggle Supervisor CC">
              <input type="checkbox" id="prefSupervisorCc" checked>
              <span class="switch-slider"></span>
            </label>
          </div>
        </div>
      </section>

      <!-- CARD 4: SECURITY & CREDENTIALS -->
      <section class="profile-card" aria-label="Security and Credentials">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <svg viewBox="0 0 24 24" fill="none">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Security &amp; Account Access</span>
          </h3>
          <span class="profile-card-tag">Security</span>
        </div>

        <div class="security-row-item">
          <div class="security-info-text">
            <h5>Portal Login Password</h5>
            <p>Last updated 45 days ago &bull; Strong complexity</p>
          </div>
          <button type="button" class="btn-security-action" id="btnOpenPasswordModal">
            Change Password
          </button>
        </div>

        <div class="security-row-item">
          <div class="security-info-text">
            <h5>Two-Factor Authentication (2FA)</h5>
            <p style="color: #166534; font-weight: 600;">Active &bull; Verified DA Gov Email OTP</p>
          </div>
          <span class="profile-verified-badge" style="background: #e8f5e9;">Secured</span>
        </div>

        <div class="security-row-item">
          <div class="security-info-text">
            <h5>Last Intranet Login Session</h5>
            <p>Today, 08:42 AM &bull; ATI Central Office LAN</p>
          </div>
          <span style="font-size: 0.78rem; font-weight: 700; color: #166534;">Active Session</span>
        </div>
      </section>

    </div>
  </main>

  <!-- ==========================================================================
       MODAL: CHANGE PASSWORD
       ========================================================================== -->
  <div class="profile-modal-overlay" id="changePasswordModal" role="dialog" aria-modal="true"
    aria-labelledby="pwdModalTitle">
    <div class="profile-modal-card">
      <div class="profile-modal-header">
        <h3 id="pwdModalTitle">Change Account Password</h3>
        <p>Ensure your account remains protected with a secure password.</p>
        <button type="button" class="profile-modal-close-btn" id="btnClosePasswordModal" aria-label="Close dialog">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <form id="changePasswordForm" onsubmit="handlePasswordSubmit(event)">
        <div class="profile-modal-body">
          <div class="profile-field-group" style="margin-bottom: 1rem;">
            <label class="profile-label" for="inpCurrentPwd">Current Password</label>
            <input type="password" id="inpCurrentPwd" class="profile-input-control" style="display: block;" required
              placeholder="Enter current password">
          </div>

          <div class="profile-field-group" style="margin-bottom: 1rem;">
            <label class="profile-label" for="inpNewPwd">New Password</label>
            <input type="password" id="inpNewPwd" class="profile-input-control" style="display: block;" minlength="8"
              required placeholder="Minimum 8 characters">
          </div>

          <div class="profile-field-group">
            <label class="profile-label" for="inpConfirmPwd">Confirm New Password</label>
            <input type="password" id="inpConfirmPwd" class="profile-input-control" style="display: block;"
              minlength="8" required placeholder="Re-enter new password">
          </div>
        </div>

        <div class="profile-modal-footer">
          <button type="button" class="btn-profile-secondary" id="btnCancelPasswordModal">Cancel</button>
          <button type="submit" class="btn-profile-primary">Update Password</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: CONFIRM PROFILE EDITS (PREVENTS ACCIDENTAL CHANGES)
       ========================================================================== -->
  <div class="profile-modal-overlay" id="saveConfirmModal" role="dialog" aria-modal="true"
    aria-labelledby="saveModalTitle">
    <div class="profile-modal-card" style="max-width: 470px;">
      <div class="profile-modal-header" style="background: linear-gradient(135deg, #174d2f, #22643a);">
        <h3 id="saveModalTitle">Save Profile Updates?</h3>
        <p>Confirm changes before committing to your official staff profile</p>
        <button type="button" class="profile-modal-close-btn" id="btnCloseSaveModal" aria-label="Close dialog">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="profile-modal-body">
        <p style="font-size: 0.92rem; color: #1e3a2b; line-height: 1.55; margin: 0 0 1rem 0;">
          Are you sure you want to save your updated profile details? Your changes will be saved to your local browser
          session and reflected immediately across the reservation portal.
        </p>
        <div
          style="background: #f0f7f2; border: 1.5px solid #d1e7d8; border-radius: 10px; padding: 0.85rem 1rem; font-size: 0.82rem; color: #166534; display: flex; align-items: center; gap: 0.65rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
            style="flex-shrink: 0;">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
          <span>Official Civil Service identification numbers &amp; appointment records are protected by HR.</span>
        </div>
      </div>
      <div class="profile-modal-footer">
        <button type="button" class="btn-profile-secondary" id="btnCancelSaveModal">Review Edits</button>
        <button type="button" class="btn-profile-primary" id="btnExecuteSaveProfile">Yes, Save Changes</button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: DISCARD EDITS CONFIRMATION (PREVENTS ACCIDENTAL LOSS)
       ========================================================================== -->
  <div class="profile-modal-overlay" id="discardConfirmModal" role="dialog" aria-modal="true"
    aria-labelledby="discardModalTitle">
    <div class="profile-modal-card" style="max-width: 440px;">
      <div class="profile-modal-header" style="background: linear-gradient(135deg, #854d0e, #b45309);">
        <h3 id="discardModalTitle">Discard Unsaved Changes?</h3>
        <p>You have unsaved edits on your profile</p>
        <button type="button" class="profile-modal-close-btn" id="btnCloseDiscardModal" aria-label="Close dialog">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="profile-modal-body">
        <p style="font-size: 0.92rem; color: #1e3a2b; line-height: 1.55; margin: 0;">
          Are you sure you want to cancel? Any edits made will be discarded and your previous profile information will
          be preserved without changes.
        </p>
      </div>
      <div class="profile-modal-footer">
        <button type="button" class="btn-profile-secondary" id="btnKeepEditing">Continue Editing</button>
        <button type="button" class="btn-profile-danger-text" id="btnExecuteDiscard"
          style="background: #fee2e2; color: #b91c1c; font-weight: 700; padding: 0.65rem 1.25rem; border-radius: 9999px;">Discard
          Changes</button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: STAT CARD ACTIVITY DETAILS (FUNCTION FOR KPI CARDS)
       ========================================================================== -->
  <div class="profile-modal-overlay" id="statDetailModal" role="dialog" aria-modal="true"
    aria-labelledby="statModalTitle">
    <div class="profile-modal-card" style="max-width: 600px;">
      <div class="profile-modal-header">
        <h3 id="statModalTitle">Staff Reservation Activity</h3>
        <p id="statModalSubtitle">Official ATI staff reservation activity details</p>
        <button type="button" class="profile-modal-close-btn" id="btnCloseStatModal" aria-label="Close dialog">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="profile-modal-body" id="statModalBody"
        style="max-height: 60vh; overflow-y: auto; padding: 1.5rem 1.75rem;">
        <!-- Injected via JavaScript based on clicked stat card -->
      </div>
      <div class="profile-modal-footer">
        <button type="button" class="btn-profile-secondary" id="btnCloseStatModalFooter">Close</button>
        <a href="my_reservations.php" class="btn-profile-primary" id="btnModalGoToBookings">
          <span>Go to Booking History &rarr;</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div class="profile-toast" id="profileToast" role="status" aria-live="polite">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#86efac" stroke-width="2.5">
      <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span id="profileToastMsg">Profile information updated successfully!</span>
  </div>

  <!-- Script Controller -->
  <script src="js/profile.js?v=<?php echo time(); ?>"></script>
</body>

</html>