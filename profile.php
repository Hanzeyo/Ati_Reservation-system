<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';

$userId = $_SESSION['user_id'] ?? null;
$db = (new Database())->getConnection();

// Fetch official user record from database
$userStmt = $db->prepare("SELECT id, full_name, email, contact_number, office_agency, category, role, status, created_at FROM users WHERE id = ? AND deleted_at IS NULL");
$userStmt->execute([$userId]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$userFullName = !empty($userData['full_name']) ? $userData['full_name'] : ($_SESSION['full_name'] ?? 'Authorized User');
$userEmail = !empty($userData['email']) ? $userData['email'] : ($_SESSION['email'] ?? '');
$userPhone = !empty($userData['contact_number']) ? $userData['contact_number'] : '';
$userAgency = !empty($userData['office_agency']) ? $userData['office_agency'] : 'ATI Central Office';
$userCategory = !empty($userData['category']) ? $userData['category'] : 'ATI Personnel';
$userRole = !empty($userData['role']) ? $userData['role'] : 'user';
$userStatus = !empty($userData['status']) ? ucfirst($userData['status']) : 'Active';
$userDateCreated = !empty($userData['created_at']) ? date('F d, Y', strtotime($userData['created_at'])) : 'October 2026';

// Format user role for display
$roleLabels = [
    'super_admin' => 'Super Administrator',
    'admin' => 'Administrator',
    'director' => 'Directorate / Final Clearance',
    'clearance' => 'Clearance Authority (Stage 3)',
    'recommendation' => 'Recommending Officer (Stage 2)',
    'endorsement' => 'Endorsing Division Head (Stage 1)',
    'user' => 'Requesting Client / Staff'
];
$displayRole = $roleLabels[strtolower($userRole)] ?? ucfirst(str_replace('_', ' ', $userRole));

// Real reservation statistics from MySQL
$totalResStmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND deleted_at IS NULL");
$totalResStmt->execute([$userId]);
$totalUserRes = (int)$totalResStmt->fetchColumn();

$approvedResStmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status = 'Approved' AND deleted_at IS NULL");
$approvedResStmt->execute([$userId]);
$approvedUserRes = (int)$approvedResStmt->fetchColumn();

$pendingResStmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status IN ('Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance', 'Awaiting Endorsement', 'Awaiting Recommendation', 'Awaiting Clearance') AND deleted_at IS NULL");
$pendingResStmt->execute([$userId]);
$pendingUserRes = (int)$pendingResStmt->fetchColumn();

$historyResStmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status IN ('Completed', 'Cancelled', 'Rejected') AND deleted_at IS NULL");
$historyResStmt->execute([$userId]);
$historyUserRes = (int)$historyResStmt->fetchColumn();

$userInitials = getUserInitials($userFullName);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Agricultural Training Institute</title>
  <meta name="description" content="View and manage your official ATI reservation profile, contact details, and account security credentials.">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="profile-body">

  <!-- ==========================================================================
       TOPBAR NAVIGATION
       ========================================================================== -->
  <header class="booking-topbar">
    <div class="booking-topbar-container">
      <a href="home.php" class="booking-brand" title="Return to Home">
        <img src="assets/images/ATI_Logo.png" alt="ATI Official Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility and Dormitory Reservation System</p>
        </div>
      </a>

      <nav class="booking-nav-center">
        <a href="home.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Home</span>
        </a>

        <a href="booking.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          <span>New Reservation</span>
        </a>

        <a href="my_reservations.php" class="booking-nav-item" title="My Reservations">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count"><?= $currentUserResCount ?></span>
        </a>

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
            <div class="user-avatar-circle" id="topbarAvatarCircle"><?= htmlspecialchars($userInitials) ?></div>
            <div class="user-details">
              <div class="user-name" id="topbarUserName"><?= htmlspecialchars($userFullName) ?></div>
              <div class="user-role" id="topbarUserRole"><?= htmlspecialchars($displayRole) ?></div>
            </div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-user-name" id="dropdownUserName"><?= htmlspecialchars($userFullName) ?></div>
              <div class="dropdown-user-email" id="dropdownUserRole"><span class="user-verified-dot"></span> <?= htmlspecialchars($userAgency) ?></div>
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
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
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
            <a href="logout.php" class="dropdown-item danger">
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

      <!-- Mobile Hamburger Button -->
      <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileDrawer(true)"
        aria-label="Open Navigation Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <!-- Mobile Drawer -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="if(event.target===this) toggleMobileDrawer(false);" style="display: none;">
    <div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">
      <div class="drawer-header">
        <div class="drawer-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="drawer-logo" style="width: 36px; height: 36px; object-fit: contain;">
          <div>
            <h4>ATI Portal</h4>
            <p>Central Office</p>
          </div>
        </div>
        <button type="button" class="drawer-close-btn" id="mobileDrawerClose" onclick="toggleMobileDrawer(false)" aria-label="Close Menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="drawer-body">
        <div class="drawer-profile-card">
          <div class="drawer-avatar" id="drawerAvatarCircle"><?= htmlspecialchars($userInitials) ?></div>
          <div class="drawer-profile-info">
            <h5 id="drawerUserName"><?= htmlspecialchars($userFullName) ?></h5>
            <p id="drawerUserRole"><?= htmlspecialchars($displayRole) ?></p>
            <span class="drawer-badge"><?= htmlspecialchars($userAgency) ?></span>
          </div>
        </div>

        <div class="drawer-nav-section">
          <div class="drawer-section-label">PORTAL NAVIGATION</div>
          <a href="home.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <span class="drawer-link-text">Home</span>
          </a>
          <a href="booking.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
            <span class="drawer-link-text">New Reservation</span>
          </a>
          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <span class="drawer-link-text">My Reservations</span>
            <span class="drawer-badge-count"><?= $currentUserResCount ?></span>
          </a>
          <a href="booking_history.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="drawer-link-text">Booking History</span>
          </a>
          <a href="schedule.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <span class="drawer-link-text">Master Schedule</span>
          </a>
        </div>

        <div class="drawer-nav-section">
          <div class="drawer-section-label">ACCOUNT & SETTINGS</div>
          <a href="profile.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
          </a>
          <?php if (isAdmin()): ?>
          <a href="admin_dashboard.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            <span class="drawer-link-text">Admin Dashboard</span>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="drawer-footer">
        <a href="logout.php" class="drawer-logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
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

    <!-- Page Location Badge -->
    <nav class="profile-breadcrumb" aria-label="Page Location">
      <span class="current">My Profile</span>
    </nav>

    <!-- Hidden File Input for Avatar Upload -->
    <input type="file" id="profilePicInput" accept="image/png, image/jpeg, image/webp" aria-label="Upload profile photo" style="display: none;">

    <!-- ==========================================================================
         HERO PROFILE IDENTITY BANNER CARD
         ========================================================================== -->
    <section class="profile-hero-card" aria-label="Staff Identity Banner">
      <div class="profile-hero-cover">
        <div class="profile-hero-cover-overlay"></div>
      </div>

      <div class="profile-hero-body">
        <div class="profile-hero-top-row">
          <div class="profile-avatar-wrap">
            <div class="profile-avatar-inner" id="heroAvatarContainer">
              <img src="" alt="Profile Picture" class="profile-avatar-img" id="heroAvatarImg" style="display: none;">
              <span class="profile-avatar-initials" id="heroAvatarInitials"><?= htmlspecialchars($userInitials) ?></span>
            </div>
            <button type="button" class="btn-upload-photo" id="btnTriggerPhotoUpload" title="Upload or change profile photo" aria-label="Upload photo">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </button>
          </div>

          <!-- Hero Actions (View Mode vs Edit Mode) -->
          <div class="profile-hero-actions">
            <!-- View Mode -->
            <button type="button" class="btn-profile-primary btn-view-mode" id="btnStartEditing">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
              <span>New Reservation</span>
            </a>

            <!-- Edit Mode Actions -->
            <button type="button" class="btn-profile-primary btn-edit-mode" id="btnSaveProfile">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Save Changes</span>
            </button>
            <button type="button" class="btn-profile-secondary btn-edit-mode" id="btnCancelEditing">
              <span>Cancel</span>
            </button>
          </div>
        </div>

        <!-- Identity Details -->
        <div class="profile-hero-identity">
          <div class="profile-name-row">
            <h1 class="profile-name-display" id="displayFullName"><?= htmlspecialchars($userFullName) ?></h1>
            <span class="profile-verified-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Verified User</span>
            </span>
            <span class="edit-mode-indicator" id="editModeIndicator" style="display: none;">
              <span class="status-dot-pulse" style="background: #d97706;"></span>
              <span>Edit Mode Active</span>
            </span>
          </div>

          <p class="profile-role-subtitle" id="displayRoleSubtitle">
            <?= htmlspecialchars($displayRole) ?> &bull; <span id="displayAgencyText"><?= htmlspecialchars($userAgency) ?></span>
          </p>

          <div class="profile-tag-ribbon">
            <span class="profile-tag-pill">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
              <span id="displayCategoryTag"><?= htmlspecialchars($userCategory) ?></span>
            </span>
            <span class="profile-tag-pill" style="color: #174d2f; background: #eaf5ee; border-color: #cce4d3;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>Status: <?= htmlspecialchars($userStatus) ?></span>
            </span>
            <button type="button" class="btn-profile-danger-text" id="btnRemovePhoto" style="display: none;" title="Remove uploaded photo">
              Remove Photo
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ==========================================================================
         EDIT MODE ALERT BANNER
         ========================================================================== -->
    <div class="profile-edit-banner" id="profileEditBanner">
      <div class="profile-edit-banner-content">
        <div class="profile-edit-banner-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <div class="profile-edit-banner-text">
          <h4>Editing Profile Information</h4>
          <p>You can modify your full name, official email, contact number, and office/agency below. Click <strong>Save Changes</strong> to update your account immediately.</p>
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
         RESERVATION ACTIVITY KPI RIBBON (DIRECT LINKS, ZERO POPUPS)
         ========================================================================== -->
    <section class="profile-stats-ribbon" aria-label="Reservation Summary Statistics">
      <!-- 1. Total Reservations -->
      <a href="my_reservations.php" class="profile-stat-box" style="text-decoration: none;" title="View all active reservations">
        <div class="profile-stat-icon green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val"><?= $totalUserRes ?></span>
          <span class="profile-stat-lbl">Total Reservations</span>
          <span class="stat-click-hint">View active bookings &rarr;</span>
        </div>
      </a>

      <!-- 2. Confirmed & Active -->
      <a href="my_reservations.php?filter=approved" class="profile-stat-box" style="text-decoration: none;" title="View approved bookings">
        <div class="profile-stat-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val"><?= $approvedUserRes ?></span>
          <span class="profile-stat-lbl">Confirmed &amp; Approved</span>
          <span class="stat-click-hint">View confirmed &rarr;</span>
        </div>
      </a>

      <!-- 3. Under Admin Review -->
      <a href="my_reservations.php?filter=pending" class="profile-stat-box" style="text-decoration: none;" title="View reservations under evaluation">
        <div class="profile-stat-icon amber">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val"><?= $pendingUserRes ?></span>
          <span class="profile-stat-lbl">Under Admin Routing</span>
          <span class="stat-click-hint">View pending &rarr;</span>
        </div>
      </a>

      <!-- 4. Booking History -->
      <a href="booking_history.php" class="profile-stat-box" style="text-decoration: none;" title="View completed and concluded bookings archive">
        <div class="profile-stat-icon slate">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="profile-stat-info">
          <span class="profile-stat-val"><?= $historyUserRes ?></span>
          <span class="profile-stat-lbl">Concluded in History</span>
          <span class="stat-click-hint">View history archive &rarr;</span>
        </div>
      </a>
    </section>

    <!-- ==========================================================================
         PROFILE DETAILS SECTIONS (2-CARD BALANCED GRID)
         ========================================================================== -->
    <div class="profile-cards-grid">

      <!-- CARD 1: OFFICIAL PROFILE & CONTACT DETAILS -->
      <section class="profile-card" aria-label="Official Profile Details">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Personal &amp; Contact Details</span>
          </h3>
          <span class="profile-card-tag">Account Info</span>
        </div>

        <form id="profileDetailsForm" onsubmit="handleProfileSubmit(event)">
          <div class="profile-fields-grid">
            <!-- Full Legal Name -->
            <div class="profile-field-group profile-field-full">
              <label class="profile-label" for="inpFullName">Full Legal Name</label>
              <div class="profile-value-display" id="valFullName"><?= htmlspecialchars($userFullName) ?></div>
              <input type="text" id="inpFullName" name="full_name" class="profile-input-control" value="<?= htmlspecialchars($userFullName) ?>" required placeholder="First Name Last Name">
            </div>

            <!-- Official Email Address -->
            <div class="profile-field-group">
              <label class="profile-label" for="inpEmail">Official Email Address</label>
              <div class="profile-value-display" id="valEmail"><?= htmlspecialchars($userEmail) ?></div>
              <input type="email" id="inpEmail" name="email" class="profile-input-control" value="<?= htmlspecialchars($userEmail) ?>" required placeholder="username@domain.com">
            </div>

            <!-- Mobile Contact Number -->
            <div class="profile-field-group">
              <label class="profile-label" for="inpPhone">Mobile / Contact Number</label>
              <div class="profile-value-display" id="valPhone"><?= htmlspecialchars($userPhone ?: 'Not specified') ?></div>
              <input type="tel" id="inpPhone" name="contact_number" class="profile-input-control" value="<?= htmlspecialchars($userPhone) ?>" placeholder="+63 9XX XXX XXXX">
            </div>

            <!-- Office / Agency / Operating Division -->
            <div class="profile-field-group profile-field-full">
              <label class="profile-label" for="inpAgency">Office / Agency / Operating Division</label>
              <div class="profile-value-display" id="valAgency"><?= htmlspecialchars($userAgency) ?></div>
              <input type="text" id="inpAgency" name="office_agency" class="profile-input-control" value="<?= htmlspecialchars($userAgency) ?>" required placeholder="e.g. Career Development Division (CDD) / DA-RFO">
            </div>

            <!-- Account Category (Read-only System Classification) -->
            <div class="profile-field-group">
              <label class="profile-label">Classification Category</label>
              <div class="profile-value-display" style="background: #f1f7f3; color: #174d2f; font-weight: 800;">
                <?= htmlspecialchars($userCategory) ?>
              </div>
            </div>

            <!-- System Role (Read-only Governance Role) -->
            <div class="profile-field-group">
              <label class="profile-label">Portal System Role</label>
              <div class="profile-value-display" style="background: #f1f7f3; color: #174d2f; font-weight: 800;">
                <?= htmlspecialchars($displayRole) ?>
              </div>
            </div>
          </div>
        </form>
      </section>

      <!-- CARD 2: SECURITY & ACCOUNT MANAGEMENT -->
      <section class="profile-card" aria-label="Security and Credentials">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Security &amp; Account Access</span>
          </h3>
          <span class="profile-card-tag">Credentials</span>
        </div>

        <div class="security-row-item">
          <div class="security-info-text">
            <h5>Account Login Password</h5>
            <p>Protected by secure bcrypt hashing. Change your password regularly to protect your account.</p>
          </div>
          <button type="button" class="btn-security-action" id="btnOpenPasswordModal">
            Change Password
          </button>
        </div>

        <div class="security-row-item">
          <div class="security-info-text">
            <h5>Account Status</h5>
            <p style="color: #166534; font-weight: 700;">Verified &amp; Active &bull; Full Reservation Permissions</p>
          </div>
          <span class="profile-verified-badge" style="background: #e8f5e9;">Active</span>
        </div>

        <div class="security-row-item">
          <div class="security-info-text">
            <h5>Portal Registration Date</h5>
            <p>Member since <?= htmlspecialchars($userDateCreated) ?></p>
          </div>
          <span style="font-size: 0.8rem; font-weight: 700; color: #52705e;">Official Account</span>
        </div>

        <!-- Reservation Guidelines Box -->
        <div style="margin-top: 1.5rem; background: #f0f7f2; border: 1.5px solid #d0e7d7; border-radius: 12px; padding: 1.1rem; display: flex; gap: 0.85rem; align-items: flex-start;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
          <div style="font-size: 0.84rem; color: #1e3d29; line-height: 1.5;">
            <strong style="color: #14532d; display: block; margin-bottom: 0.25rem;">Reservation Profile Reminders:</strong>
            Keep your official contact number and operating division updated to ensure fast routing, SMS event clearances, and accurate Gate Pass slips.
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- ==========================================================================
       MODAL: CHANGE PASSWORD (FUNCTIONAL WITH REAL MYSQL UPDATE)
       ========================================================================== -->
  <div class="profile-modal-overlay" id="changePasswordModal" role="dialog" aria-modal="true" aria-labelledby="pwdModalTitle" style="display: none;">
    <div class="profile-modal-card">
      <div class="profile-modal-header">
        <h3 id="pwdModalTitle">Change Account Password</h3>
        <p>Ensure your account remains protected with a strong password.</p>
        <button type="button" class="profile-modal-close-btn" id="btnClosePasswordModal" aria-label="Close dialog">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <form id="changePasswordForm">
        <div class="profile-modal-body">
          <div id="pwdModalAlert" class="profile-modal-alert error" style="display: none;"></div>

          <div class="profile-field-group" style="margin-bottom: 1.15rem;">
            <label class="profile-label" for="inpCurrentPwd">Current Password <span style="color:#dc2626;">*</span></label>
            <div class="pwd-input-wrap">
              <input type="password" id="inpCurrentPwd" name="current_password" class="profile-input-control" required placeholder="Enter current password" autocomplete="current-password">
              <button type="button" class="btn-toggle-pwd" data-target="inpCurrentPwd" aria-label="Toggle password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>

          <div class="profile-field-group" style="margin-bottom: 1.15rem;">
            <label class="profile-label" for="inpNewPwd">New Password <span style="color:#dc2626;">*</span></label>
            <div class="pwd-input-wrap">
              <input type="password" id="inpNewPwd" name="new_password" class="profile-input-control" minlength="8" required placeholder="Minimum 8 characters" autocomplete="new-password">
              <button type="button" class="btn-toggle-pwd" data-target="inpNewPwd" aria-label="Toggle password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">Must be at least 8 characters long.</div>
          </div>

          <div class="profile-field-group">
            <label class="profile-label" for="inpConfirmPwd">Confirm New Password <span style="color:#dc2626;">*</span></label>
            <div class="pwd-input-wrap">
              <input type="password" id="inpConfirmPwd" name="confirm_password" class="profile-input-control" minlength="8" required placeholder="Re-enter new password" autocomplete="new-password">
              <button type="button" class="btn-toggle-pwd" data-target="inpConfirmPwd" aria-label="Toggle password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="profile-modal-footer">
          <button type="button" class="btn-profile-secondary" id="btnCancelPasswordModal">Cancel</button>
          <button type="submit" class="btn-profile-primary" id="btnSubmitPassword">Update Password</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast Notification -->
  <div class="profile-toast" id="profileToast" role="status" aria-live="polite">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#86efac" stroke-width="2.5">
      <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span id="profileToastMsg">Profile information updated successfully!</span>
  </div>

  <!-- Controller Script -->
  <script src="js/profile.js?v=<?php echo time(); ?>"></script>
</body>

</html>