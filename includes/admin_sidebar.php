<?php
/**
 * ATI Reservation System - Standardized Admin Sidebar Component
 * Dynamically rendered based on Role-Based Access Control (RBAC)
 */

if (!isset($db)) {
    $db = (new Database())->getConnection();
}

// Auto-detect active page if not explicitly set
if (!isset($activeNav)) {
    $currentScript = basename($_SERVER['PHP_SELF'], '.php');
    $activeNav = str_replace('admin_', '', $currentScript);
}

// Fetch pending count if not provided
if (!isset($sidebarPendingCount)) {
    try {
        $pendingStmt = $db->query("SELECT COUNT(*) FROM reservations WHERE status IN ('Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance', 'Awaiting Endorsement', 'Awaiting Recommendation', 'Awaiting Clearance') AND deleted_at IS NULL");
        $sidebarPendingCount = (int)$pendingStmt->fetchColumn();
    } catch (Exception $e) {
        $sidebarPendingCount = 0;
    }
}

// Fetch total users count for super_admin
if (!isset($sidebarUsersCount) && !empty($canManageUsers)) {
    try {
        $usersStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
        $sidebarUsersCount = (int)$usersStmt->fetchColumn();
    } catch (Exception $e) {
        $sidebarUsersCount = 0;
    }
}

// Generate initials for avatar
$sidebarUserInitials = getUserInitials($_SESSION['full_name'] ?? 'Admin');
$sidebarUserFullName = htmlspecialchars($_SESSION['full_name'] ?? 'Authorized Officer');

// Fetch user profile picture from DB if available
$sidebarUserAvatar = '';
if (!empty($_SESSION['user_id'])) {
    try {
        $avStmt = $db->prepare("SELECT avatar_path FROM users WHERE id = ?");
        $avStmt->execute([$_SESSION['user_id']]);
        $sidebarUserAvatar = (string)($avStmt->fetchColumn() ?: '');
    } catch (Exception $e) {
        $sidebarUserAvatar = '';
    }
}
?>
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar" id="adminSidebar">
      <!-- Sidebar Header / Branding -->
      <div class="admin-sidebar-header">
        <a href="admin_dashboard.php" class="admin-sidebar-brand">
          <div class="admin-sidebar-logo-box">
            <img src="assets/images/ATI_Logo.png" alt="Agricultural Training Institute Logo" class="admin-sidebar-logo">
          </div>
          <div class="admin-sidebar-brand-text">
            <h3>ATI Portal</h3>
            <p>Central Office</p>
          </div>
        </a>
        <div class="admin-badge-executive <?= htmlspecialchars($currentAdminRole ?? 'super_admin') ?>">
          <span class="executive-dot"></span>
          <?= htmlspecialchars($roleBadgeLabel ?? 'Super Admin') ?>
        </div>
      </div>

      <!-- Navigation Section -->
      <nav class="admin-sidebar-menu" aria-label="Admin Navigation">
        <div class="sidebar-section-title">CORE COMMAND</div>

        <a href="admin_dashboard.php" class="sidebar-menu-item <?= in_array($activeNav, ['dashboard', 'admin_dashboard']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </div>
          <span class="menu-label">Dashboard</span>
        </a>

        <a href="admin_approvals.php<?= ($currentAdminRole !== 'super_admin') ? '?role=' . urlencode($currentAdminRole) : '' ?>" class="sidebar-menu-item <?= in_array($activeNav, ['approvals', 'admin_approvals', 'admin_approval_details']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
          </div>
          <span class="menu-label">Approvals Queue</span>
          <span class="sidebar-count-badge amber" id="sidebarPendingCount"><?= $sidebarPendingCount ?></span>
        </a>

        <a href="admin_reservations.php" class="sidebar-menu-item <?= in_array($activeNav, ['reservations', 'admin_reservations']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
          </div>
          <span class="menu-label">All Reservations</span>
        </a>

        <?php if (!empty($canManageUsers)): ?>
        <div class="sidebar-section-title">ACCESS &amp; GOVERNANCE</div>

        <a href="admin_users.php" class="sidebar-menu-item <?= in_array($activeNav, ['users', 'admin_users']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <span class="menu-label">User &amp; Role Access</span>
          <span class="sidebar-count-badge green"><?= $sidebarUsersCount ?? 0 ?></span>
        </a>
        <?php endif; ?>

        <div class="sidebar-section-title">FACILITY MANAGEMENT</div>

        <!-- Accommodations: Visible to Endorsement Officer, Super Admin, and Officers -->
        <a href="admin_facilities.php" class="sidebar-menu-item <?= in_array($activeNav, ['facilities', 'admin_facilities']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 4v16"></path>
              <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
              <path d="M2 17h20"></path>
              <path d="M6 8v9"></path>
            </svg>
          </div>
          <span class="menu-label">Accommodations</span>
        </a>

        <a href="admin_schedule.php" class="sidebar-menu-item <?= in_array($activeNav, ['schedule', 'admin_schedule']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <span class="menu-label">Master Schedule</span>
        </a>

        <?php if (!empty($canViewAuditLogs)): ?>
        <a href="admin_audit.php" class="sidebar-menu-item <?= in_array($activeNav, ['audit', 'admin_audit']) ? 'active' : '' ?>">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </div>
          <span class="menu-label">Audit Trail Log</span>
        </a>
        <?php endif; ?>

        <div class="sidebar-section-title">PORTAL SWITCH</div>

        <a href="home.php" class="sidebar-menu-item client-switch">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
          </div>
          <span class="menu-label">Client / Staff Portal</span>
        </a>
      </nav>

      <!-- Sidebar Sign Out Footer -->
      <div class="admin-sidebar-footer">
        <a href="logout.php" class="sidebar-signout-btn" title="Sign Out">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Sign Out</span>
        </a>
      </div>
    </aside>

    <!-- Global Auto-Sync from Main Profile (profile.php / localStorage) -->
    <script>
      (function() {
        function syncAdminProfilePicture() {
          try {
            var savedAvatar = localStorage.getItem('ati_user_avatar');
            if (savedAvatar) {
              document.querySelectorAll('.topbar-user-avatar').forEach(function(container) {
                var img = container.querySelector('.topbar-avatar-img');
                var initials = container.querySelector('.topbar-avatar-initials');
                if (img) {
                  img.src = savedAvatar;
                  img.style.display = 'block';
                  if (initials) initials.style.display = 'none';
                }
              });
            }
            var savedProfile = localStorage.getItem('ati_user_profile');
            if (savedProfile) {
              var p = JSON.parse(savedProfile);
              if (p && p.fullName) {
                document.querySelectorAll('.topbar-user-name').forEach(function(el) {
                  el.textContent = p.fullName;
                });
              }
            }
          } catch(e) {}
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', syncAdminProfilePicture);
        } else {
          syncAdminProfilePicture();
        }
      })();
    </script>
