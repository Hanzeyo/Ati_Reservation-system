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
    .portal-category-wrapper {
      max-width: 1040px;
      margin: 2.5rem auto 4rem;
      padding: 0 1.5rem;
    }

    .portal-category-intro-card {
      background: #ffffff;
      border: 1.5px solid #dce8e0;
      border-radius: 20px;
      padding: 3rem 2rem 2.5rem;
      text-align: center;
      margin-bottom: 2.25rem;
      box-shadow: 0 8px 30px rgba(23, 77, 47, 0.04);
    }

    .portal-category-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 2rem;
      margin-bottom: 2.5rem;
    }

    .portal-cat-card {
      background: #ffffff;
      border: 2px solid #dce8e0;
      border-radius: 20px;
      padding: 2.25rem 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
      display: flex;
      flex-direction: column;
      text-decoration: none;
      color: inherit;
      transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .portal-cat-card:hover {
      border-color: #2e7d32;
      transform: translateY(-5px);
      box-shadow: 0 14px 32px rgba(23, 84, 50, 0.12);
    }

    .portal-cat-card:hover .cat-icon-container {
      background: #e8f5ec;
      transform: scale(1.02);
    }

    .portal-cat-card:hover .btn-explore-category {
      background: #175432;
      box-shadow: 0 6px 18px rgba(23, 84, 50, 0.3);
    }

    .portal-cat-card:hover .btn-explore-category svg {
      transform: translateX(4px);
    }

    .btn-explore-category {
      text-decoration: none;
    }

    .btn-explore-category svg {
      transition: transform 0.2s ease;
    }

    /* Quick status bar */
    .portal-quick-status-card {
      background: #fbfdfc;
      border: 1.5px solid #e0ede4;
      border-radius: 14px;
      padding: 1.25rem 1.75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
    }

    .status-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .status-icon-bubble {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: #eaf4ed;
      color: #175432;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .status-text h4 {
      font-size: 0.95rem;
      font-weight: 700;
      color: #153321;
      margin-bottom: 0.2rem;
    }

    .status-text p {
      font-size: 0.82rem;
      color: #556f60;
    }

    .btn-view-bookings {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #ffffff;
      border: 1.5px solid #cce4d3;
      color: #175432;
      font-size: 0.85rem;
      font-weight: 700;
      padding: 0.55rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .btn-view-bookings:hover {
      background: #175432;
      color: #ffffff;
      border-color: #175432;
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
        padding: 2rem 1.25rem 1.75rem;
        border-radius: 16px;
      }
      .portal-cat-card {
        padding: 1.5rem;
        border-radius: 16px;
      }
      .portal-quick-status-card {
        flex-direction: column;
        align-items: flex-start;
      }
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

        <!-- New Reservation -->
        <a href="booking.php" class="booking-nav-item">
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
          aria-label="Close Navigation Menu">&times;</button>
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
              <svg viewBox="0 0 24 24" fill="none">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                <line x1="3" y1="18" x2="3.01" y2="18"></line>
              </svg>
            </div>
            <span class="drawer-link-text">My Reservations</span>
            <span class="drawer-badge-count">2</span>
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

          <a href="javascript:void(0)" class="drawer-nav-link"
            onclick="alert('Profile management available in next administrative release.');">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
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

  <main class="portal-category-wrapper">
    
    <!-- ==========================================================================
         CATEGORY INTRO CARD (Matching Design Reference)
         ========================================================================== -->
    <div class="portal-category-intro-card">
      <span class="cat-sec-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M12 3l9 7H3l9-7z"></path>
        </svg>
        Agriculture & Training Venues
      </span>
      <h1 class="cat-sec-title">OUR FACILITIES</h1>
      <p class="cat-sec-subtitle">Choose a category below to open a dedicated view of available function halls, training rooms, and dormitory accommodations.</p>
      <div class="cat-sec-divider"></div>
    </div>

    <!-- ==========================================================================
         TWO MAIN CATEGORY CARDS (Halls vs Dormitories)
         ========================================================================== -->
    <div class="portal-category-grid">
      
      <!-- 1. Halls Category Card -->
      <a href="booking.php?category=halls" class="portal-cat-card" id="cardCatHalls" title="Explore ATI Halls">
        <div class="cat-card-header">
          <span class="cat-count-badge">4 Venues Available</span>
        </div>
        <div class="cat-icon-container">
          <svg width="72" height="72" viewBox="0 0 64 64" fill="none" stroke="#174d2f" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <!-- Classical Hall Building with Pediment & Columns -->
            <path d="M6 22L32 7L58 22H6Z" fill="#edf7f1"/>
            <path d="M10 22V26H54V22"/>
            <rect x="15" y="26" width="7" height="26" rx="2" fill="#edf7f1"/>
            <rect x="28.5" y="26" width="7" height="26" rx="2" fill="#edf7f1"/>
            <rect x="42" y="26" width="7" height="26" rx="2" fill="#edf7f1"/>
            <path d="M9 52H55V56H9V52Z"/>
            <path d="M5 56H59V60H5V56Z"/>
          </svg>
        </div>
        <h2 class="cat-card-title">Halls</h2>
        <p class="cat-card-desc">Function auditoriums, audio-visual training rooms, executive boardrooms & mess dining facilities.</p>
        <div class="btn-explore-category">
          <span>Explore Halls</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>
      </a>

      <!-- 2. Dormitories Category Card -->
      <a href="booking.php?category=dormitories" class="portal-cat-card" id="cardCatDormitories" title="Explore ATI Dormitories">
        <div class="cat-card-header">
          <span class="cat-count-badge">4 Room Types</span>
        </div>
        <div class="cat-icon-container">
          <svg width="72" height="72" viewBox="0 0 64 64" fill="none" stroke="#174d2f" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <!-- Multi-story Dormitory Accommodation Building -->
            <rect x="23" y="10" width="18" height="46" rx="2" fill="#edf7f1"/>
            <rect x="9" y="22" width="14" height="34" rx="2" fill="#edf7f1"/>
            <rect x="41" y="22" width="14" height="34" rx="2" fill="#edf7f1"/>
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
        <p class="cat-card-desc">Executive VIP suites, shared trainee quarters, guest lecturer rooms & twin accommodations.</p>
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
          <h4>Looking for your active bookings?</h4>
          <p>You currently have 2 upcoming reservations on file. You can monitor verification and approval status anytime.</p>
        </div>
      </div>
      <a href="my_reservations.php" class="btn-view-bookings">
        <span>Go to My Reservations</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </a>
    </div>

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
    });
  </script>
</body>

</html>
