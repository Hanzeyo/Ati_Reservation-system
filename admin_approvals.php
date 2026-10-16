<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Dual-Stage Approvals Queue & Endorsement Center
 */
$validRoles = ['endorsement', 'recommendation', 'clearance', 'all'];
if ($currentAdminRole === 'super_admin') {
    $currentRole = isset($_GET['role']) && in_array($_GET['role'], $validRoles) ? $_GET['role'] : 'all';
} else {
    $currentRole = $currentAdminRole;
}
$isRecommendation = ($currentRole === 'recommendation');

// Fetch all reservations from the database joined with facilities, rooms, users, and approvers
$query = "SELECT r.*, 
                 f.name AS facility_name, 
                 f.type AS facility_type,
                 rm.room_number,
                 rm.floor AS room_floor,
                 u.full_name AS applicant_name,
                 u.email AS applicant_email,
                 u.contact_number AS applicant_phone,
                 u.office_agency AS applicant_agency,
                 end_u.full_name AS endorsed_by_name,
                 rec_u.full_name AS recommended_by_name,
                 clr_u.full_name AS cleared_by_name
          FROM reservations r
          LEFT JOIN facilities f ON r.facility_id = f.id
          LEFT JOIN rooms rm ON r.room_id = rm.id
          LEFT JOIN users u ON r.user_id = u.id
          LEFT JOIN users end_u ON r.endorsed_by = end_u.id
          LEFT JOIN users rec_u ON r.recommended_by = rec_u.id
          LEFT JOIN users clr_u ON r.cleared_by = clr_u.id
          WHERE r.deleted_at IS NULL
          ORDER BY r.id DESC";

$stmt = $db->query($query);
$rawReservations = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$approvalsData = [];
$nowTime = time();

$kpiStage1 = 0; // Endorsement Queue
$kpiStage2 = 0; // Recommendation Queue
$kpiStage3 = 0; // Final Clearance Queue
$kpiApproved = 0;
$kpiUrgent = 0;

foreach ($rawReservations as $row) {
    $ref = $row['reference_no'];
    $st = $row['status'] ?? 'Pending';

    // Map DB status to 3-stage approvals workflow:
    // Stage 1: Endorsement -> Stage 2: Recommendation -> Stage 3: Clearance
    if (in_array($st, ['Pending', 'Pending Endorsement'])) {
        $stage = 'stage1'; // Awaiting Endorsement
    } elseif ($st === 'Pending Recommendation') {
        $stage = 'stage2'; // Awaiting Recommendation
    } elseif ($st === 'Pending Clearance') {
        $stage = 'stage3'; // Awaiting Clearance
    } elseif (in_array($st, ['Approved', 'Completed'])) {
        $stage = 'approved';
    } elseif (in_array($st, ['Rejected', 'Cancelled'])) {
        $stage = 'rejected';
    } else {
        $stage = 'stage1';
    }

    // Urgency calculation: within 48 hours of today
    $startDateTimestamp = strtotime($row['start_date']);
    $diffSeconds = $startDateTimestamp - $nowTime;
    $isUrgent = ($diffSeconds >= -86400 && $diffSeconds <= 172800);
    $urgency = $isUrgent ? 'urgent' : 'normal';

    if ($stage === 'stage1') $kpiStage1++;
    if ($stage === 'stage2') $kpiStage2++;
    if ($stage === 'stage3') $kpiStage3++;
    if ($stage === 'approved') $kpiApproved++;
    if ($isUrgent && in_array($stage, ['stage1', 'stage2', 'stage3'])) $kpiUrgent++;

    // Format target dates
    $startDateStr = date('M d, Y', $startDateTimestamp);
    if (!empty($row['end_date']) && $row['end_date'] !== $row['start_date']) {
        $datesStr = date('M d', $startDateTimestamp) . ' – ' . date('M d, Y', strtotime($row['end_date']));
    } else {
        $datesStr = $startDateStr;
    }

    $venueCategory = (($row['facility_type'] ?? '') === 'dormitories' || !empty($row['room_number'])) ? 'dorms' : 'halls';
    $venueDisplay = $row['facility_name'] ?: 'Official Facility';
    if (!empty($row['room_number'])) {
        $venueDisplay .= ' (Room ' . $row['room_number'] . ')';
    }

    $approvalsData[$ref] = [
        'id' => (int)$row['id'],
        'ref' => $ref,
        'applicant' => $row['applicant_name'] ?: 'Official Guest',
        'agency' => $row['applicant_agency'] ?: 'ATI Central Office',
        'email' => $row['applicant_email'] ?: 'N/A',
        'phone' => $row['applicant_phone'] ?: 'N/A',
        'venue' => $venueDisplay,
        'venueCategory' => $venueCategory,
        'dates' => $datesStr,
        'timeSlot' => $row['time_slot'] ?: 'Full Day (8:00 AM – 5:00 PM)',
        'pax' => (int)($row['pax_count'] ?? 0),
        'eventTitle' => $row['event_title'] ?: 'Official Institutional Activity',
        'equipment' => $row['special_notes'] ?: 'Standard Logistics & Venue Equipment',
        'specialNotes' => $row['special_notes'] ?: '',
        'documentPath' => $row['document_path'] ?: '',
        'stage' => $stage,
        'stage1By' => $row['endorsed_by_name'] ?? null,
        'stage1Date' => !empty($row['endorsed_at']) ? date('M d, Y h:i A', strtotime($row['endorsed_at'])) : null,
        'stage1Remarks' => $row['endorsement_remarks'] ?? null,
        'stage2By' => $row['recommended_by_name'] ?? null,
        'stage2Date' => !empty($row['recommended_at']) ? date('M d, Y h:i A', strtotime($row['recommended_at'])) : null,
        'stage2Remarks' => $row['recommendation_remarks'] ?? null,
        'stage3By' => $row['cleared_by_name'] ?? null,
        'stage3Date' => !empty($row['cleared_at']) ? date('M d, Y h:i A', strtotime($row['cleared_at'])) : null,
        'stage3Remarks' => $row['clearance_remarks'] ?? null,
        'urgency' => $urgency,
        'dateFiled' => !empty($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : date('Y-m-d'),
        'status' => $st,
        'rejectionGround' => $row['admin_remarks'] ?: null,
        'rejectionNotes' => $row['admin_remarks'] ?: null
    ];
}

if ($currentRole === 'endorsement') {
    $kpiAwaiting = $kpiStage1;
} elseif ($currentRole === 'recommendation') {
    $kpiAwaiting = $kpiStage2;
} elseif ($currentRole === 'clearance') {
    $kpiAwaiting = $kpiStage3;
} else {
    $kpiAwaiting = $kpiStage1 + $kpiStage2 + $kpiStage3;
}
$sidebarPendingCount = $kpiStage1 + $kpiStage2 + $kpiStage3;

$userCountStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
$totalUsersCount = $userCountStmt ? (int)$userCountStmt->fetchColumn() : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Approvals Queue & Endorsement Desk | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Dual-Stage Approvals Queue, Recommendation Workflow, and Institutional Clearance Engine for Agricultural Training Institute Central Office.">

  <!-- Google Fonts (Terno with Portal) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/my_reservations.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_dashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_facilities.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_approvals.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="admin-app-body" data-current-role="<?= htmlspecialchars($currentRole) ?>">

  <div class="admin-layout-container">

    <!-- ==========================================================================
         1. LEFT SIDEBAR NAVIGATION (TERNO ATI BRAND THEME)
         ========================================================================== -->
    <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>

    <!-- Overlay for Mobile Sidebar -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(false)"></div>

    <!-- ==========================================================================
         2. MAIN WORKSPACE AREA (RIGHT SIDE)
         ========================================================================== -->
    <div class="admin-workspace">

      <!-- Topbar Header -->
      <header class="admin-topbar">
        <div class="admin-topbar-left">
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()"
            aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Administrative Governance</span>
            <h1 class="topbar-page-title">Executive Approvals Queue</h1>
          </div>
        </div>

        <div class="admin-topbar-right">


          <!-- Date Badge -->
          <div class="topbar-date-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span id="currentDateDisplay">October 2026</span>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="admin-content-container">

        <!-- Page Header -->
        <section class="my-res-header admin-page-header">
          <div class="my-res-header-text">
            <div class="my-res-top-badge admin-gold-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Three-Stage Governance Engine</span>
            </div>
            <h2>Institutional Endorsement &amp; Clearance Desk</h2>
          </div>

          <div class="admin-header-actions"
            style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; justify-content: flex-end;">
            <?php if ($currentAdminRole === 'super_admin'): ?>
            <div class="persona-switch-buttons"
              style="display: flex; align-items: center; gap: 0.35rem; background: #ffffff; padding: 0.35rem 0.55rem; border-radius: 10px; border: 1.5px solid #dce8e0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); flex-wrap: wrap;">
              <span
                style="font-size: 0.72rem; font-weight: 800; color: #627b6c; text-transform: uppercase; margin-right: 0.2rem;">Authority:</span>
              <button type="button" id="btnRoleAll" class="btn-persona-toggle <?= $currentRole === 'all' ? 'active' : '' ?>"
                onclick="switchAdminRole('all')">
                All Stages
              </button>
              <button type="button" id="btnRoleEnd" class="btn-persona-toggle <?= $currentRole === 'endorsement' ? 'active' : '' ?>"
                onclick="switchAdminRole('endorsement')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                1. Endorsement
              </button>
              <button type="button" id="btnRoleRec" class="btn-persona-toggle <?= $currentRole === 'recommendation' ? 'active' : '' ?>"
                onclick="switchAdminRole('recommendation')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                2. Recommendation
              </button>
              <button type="button" id="btnRoleClear"
                class="btn-persona-toggle <?= $currentRole === 'clearance' ? 'active director' : '' ?>"
                onclick="switchAdminRole('clearance')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                3. Clearance
              </button>
            </div>
            <?php else: ?>
            <div class="persona-switch-buttons"
              style="display: flex; align-items: center; gap: 0.5rem; background: #ffffff; padding: 0.42rem 0.85rem; border-radius: 10px; border: 1.5px solid #dce8e0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
              <span style="font-size: 0.72rem; font-weight: 800; color: #627b6c; text-transform: uppercase;">Active Desk:</span>
              <span class="btn-persona-toggle active <?= $currentAdminRole === 'clearance' ? 'director' : '' ?>" style="cursor: default; pointer-events: none;">
                <?= htmlspecialchars($roleOfficerTitle) ?>
              </span>
            </div>
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem;">
              <button type="button" class="btn-system-secondary" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 6 2 18 2 18 9"></polyline>
                  <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                  <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print Sheet</span>
              </button>
              <a href="admin_schedule.php" class="btn-system-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Master Schedule</span>
              </a>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (3-STAGE WORKFLOW)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));" aria-label="Approvals Queue Metrics">
          <!-- Awaiting Action -->
          <div class="my-res-stat-card admin-stat-highlight" onclick="applyApprovalsFilterTab('awaiting')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiAwaitingCount"><?= $kpiAwaiting ?></span>
                <span class="stat-pill-urgent">Queue</span>
              </div>
              <span class="stat-label">Awaiting My Action</span>
            </div>
          </div>

          <!-- Stage 1: Endorsement Queue -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('stage1')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStage1Count"><?= $kpiStage1 ?></span>
                <span class="stat-trend">Stage 1</span>
              </div>
              <span class="stat-label">1. Awaiting Endorsement</span>
            </div>
          </div>

          <!-- Stage 2: Recommendation Queue -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('stage2')">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStage2Count"><?= $kpiStage2 ?></span>
                <span class="stat-trend positive">Stage 2</span>
              </div>
              <span class="stat-label">2. Awaiting Recommendation</span>
            </div>
          </div>

          <!-- Stage 3: Clearance Queue -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('stage3')">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStage3Count"><?= $kpiStage3 ?></span>
                <span class="stat-trend positive">Stage 3</span>
              </div>
              <span class="stat-label">3. Awaiting Clearance</span>
            </div>
          </div>

          <!-- Cleared & Permitted -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('approved')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiApprovedCount"><?= $kpiApproved ?></span>
                <span class="stat-trend positive">Official</span>
              </div>
              <span class="stat-label">Approved &amp; Permitted</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             TOOLBAR & FILTER CONTROLS
             ========================================================================== -->
        <div class="approvals-toolbar">
          <!-- Search Box -->
          <div class="approvals-search-wrapper">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="approvalsSearchInput" placeholder="Filter by Ref #, agency, applicant, or event..."
              oninput="renderApprovalsTable()">
          </div>

          <div class="approvals-toolbar-actions">
            <button type="button" class="btn-system-secondary" onclick="applyApprovalsFilterTab('all')"
              style="padding: 0.52rem 0.85rem; border-radius: 10px; height: 100%;">
              View All
            </button>

            <!-- Venue Category Filter -->
            <select id="approvalsVenueFilter" onchange="renderApprovalsTable()"
              style="padding: 0.52rem 0.85rem; border: 1.5px solid #dce8e0; border-radius: 10px; font-family: inherit; font-size: 0.82rem; font-weight: 600; color: #172c1f; background: #fff; height: 100%;">
              <option value="all">All Facility Types</option>
              <option value="halls">Training & Function Halls</option>
              <option value="dorms">Dormitory Suites</option>
            </select>
          </div>
        </div>

        <!-- ==========================================================================
             APPROVALS QUEUE DATA TABLE
             ========================================================================== -->
        <div class="approvals-table-card">
          <table class="approvals-table" id="approvalsTable">
            <thead>
              <tr>
                <th>Request Ref & Urgency</th>
                <th>Applicant & Agency</th>
                <th>Facility & PAX</th>
                <th>Target Dates & Time</th>
                <th>Approval Progress</th>
                <th style="text-align: right;">Action Decisions</th>
              </tr>
            </thead>
            <tbody id="approvalsTableBody">
              <!-- Dynamically rendered via renderApprovalsTable() -->
              <tr>
                <td colspan="6" style="text-align: center; padding: 3.5rem 1rem; color: #637f6f;">
                  <div style="display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 50%; background: #eef7f2; margin-bottom: 0.75rem;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="8" x2="12" y2="12"></line>
                      <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                  </div>
                  <h4 style="font-size: 1.05rem; font-weight: 700; color: #172c1f; margin: 0 0 0.35rem 0;">No reservation found</h4>
                  <p style="color: #637f6f; font-size: 0.85rem; margin: 0;">There are currently no reservation requests for this view.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <style>
          #approvalsTable th:last-child,
          #approvalsTable td:last-child {
            background: #f4fdf8;
            border-left: 1px solid #dce8e0;
          }

          #approvalsTable td:last-child {
            box-shadow: inset 3px 0 0 #a9d6bb;
          }
        </style>
      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 1: RESERVATION DETAILS & APPROVAL REVIEW
       ========================================================================== -->
  <div class="admin-modal-overlay" id="dossierModal" style="display: none;">
    <div class="admin-modal-card" style="max-width: 900px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <div class="my-res-top-badge admin-gold-badge" style="margin-bottom: 0.35rem; font-size: 0.82rem; font-weight: 700;">Reservation Details</div>
          <h4 style="font-size: 1.25rem;">Reservation No: <span id="dossierModalRef" style="font-family: monospace; color: #174d2f; font-weight: 800;">REF</span>
          </h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDossierModal()" aria-label="Close modal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body" style="padding: 1.5rem 1.85rem;">
        <div class="dossier-grid">
          <!-- Left Column: Details -->
          <div>
            <div class="dossier-card-section" style="margin-bottom: 1.15rem;">
              <div class="dossier-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Applicant Information</span>
              </div>
              <div class="dossier-data-grid">
                <div class="dossier-field-item">
                  <label>Full Name</label>
                  <div class="field-value" id="dossierApplicantName">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Agency / Office</label>
                  <div class="field-value" id="dossierApplicantAgency">--</div>
                </div>
                <div class="dossier-field-item" style="grid-column: 1 / -1;">
                  <label>Contact Number & Email</label>
                  <div class="field-value" id="dossierContact">--</div>
                </div>
              </div>
            </div>

            <div class="dossier-card-section">
              <div class="dossier-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Event & Venue Details</span>
              </div>
              <div class="dossier-data-grid" style="margin-bottom: 1rem;">
                <div class="dossier-field-item">
                  <label>Facility / Venue</label>
                  <div class="field-value" id="dossierVenue" style="color: #174d2f;">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Number of Attendees</label>
                  <div class="field-value" id="dossierPax">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Date of Event</label>
                  <div class="field-value" id="dossierDates">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Time Slot</label>
                  <div class="field-value" id="dossierTimeSlot">--</div>
                </div>
              </div>
              <div class="dossier-field-item" style="margin-bottom: 0.85rem;">
                <label>Activity Title</label>
                <div class="field-value" id="dossierEventTitle" style="font-weight: 800; color: #174d2f; font-size: 1rem;">--</div>
              </div>
              <div class="dossier-field-item" style="margin-bottom: 0.85rem;">
                <label>Requested Equipment / Audio-Visual</label>
                <div class="field-value" id="dossierEquipment" style="font-size: 0.9rem; font-weight: 600;">--</div>
              </div>
              <div class="dossier-field-item">
                <label>Special Notes & Remarks</label>
                <div class="field-value" id="dossierNotes"
                  style="font-size: 0.88rem; font-weight: 500; color: #3b5042;">--</div>
              </div>
              <div class="dossier-field-item" style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1.5px dashed #dce8e0;">
                <label>Attached Supporting Document</label>
                <div id="dossierDocContainer" style="margin-top: 0.45rem;">
                  <span id="dossierDocNone" style="font-size: 0.88rem; color: #728a7c; font-style: italic;">No document attached</span>
                  <a id="dossierDocLink" href="#" target="_blank" class="btn-system-secondary" style="display: none; align-items: center; gap: 0.55rem; padding: 0.65rem 1rem; font-size: 0.9rem; border-radius: 9px; text-decoration: none; color: #174d2f; font-weight: 700; background: #eaf3ed; border: 1px solid #c2ded0;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span id="dossierDocName">Open Attached Document</span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Workflow Timeline & Decision Box -->
          <div>
            <div class="dossier-card-section">
              <div class="dossier-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Approval Process</span>
              </div>
              <div id="dossierTimelineList">
                <!-- Timeline items rendered via JS -->
              </div>
            </div>

            <!-- Action Box -->
            <div id="dossierDecisionBox">
              <!-- Rendered based on stage -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 2: RETURN FOR REVISION / DISAPPROVAL
       ========================================================================== -->
  <div class="admin-modal-overlay" id="disapproveModal" style="display: none;">
    <div class="admin-modal-card sm" style="max-width: 560px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #b91c1c; font-size: 1.15rem; font-weight: 800;">Decline or Return Reservation</h4>
          <span style="font-size: 0.85rem; color: #4b6354;">Reference: <strong id="disapproveRef" style="font-family: monospace;">REF</strong></span>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDisapproveModal()" aria-label="Close modal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body" style="padding: 1.4rem;">
        <div class="form-group-decline" style="margin-bottom: 1rem;">
          <label
            style="font-size: 0.88rem; font-weight: 800; color: #172c1f; display: block; margin-bottom: 0.4rem;">Reason for Decline / Return:</label>
          <select id="disapproveReasonSelect"
            style="width: 100%; padding: 0.75rem; border: 1.5px solid #d4e4da; border-radius: 9px; font-family: inherit; font-size: 0.92rem; color: #172c1f; background: #ffffff;">
            <option value="Schedule Conflict with Priority Official Activity">Schedule Conflict with Priority Activity</option>
            <option value="Facility Scheduled for Maintenance">Facility Scheduled for Maintenance</option>
            <option value="Incomplete Supporting Documents / Missing Letter of Intent">Incomplete Supporting Documents / Missing Letter</option>
            <option value="Number of Attendees Exceeds Room Capacity">Number of Attendees Exceeds Room Capacity</option>
            <option value="Administrative Grounds / Special Conditions">Administrative Grounds / Other Official Reasons</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-bottom: 1.25rem;">
          <label
            style="font-size: 0.88rem; font-weight: 800; color: #172c1f; display: block; margin-bottom: 0.4rem;">Instructions / Notes for Applicant:</label>
          <textarea id="disapproveRemarks" rows="3"
            placeholder="Specify reason or instructions for the applicant (e.g. choose alternative dates or attach signed letter)..."
            style="width: 100%; box-sizing: border-box; padding: 0.75rem; border: 1.5px solid #d4e4da; border-radius: 9px; font-family: inherit; font-size: 0.92rem; color: #172c1f; resize: vertical;"></textarea>
        </div>

        <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef; display: flex; gap: 0.75rem; justify-content: flex-end;">
          <button type="button" class="btn-system-secondary" style="padding: 0.65rem 1.2rem; font-size: 0.9rem; font-weight: 700;" onclick="closeDisapproveModal()">Cancel</button>
          <button type="button" class="btn-confirm-decline" style="background: #dc2626; color: #ffffff; padding: 0.65rem 1.2rem; font-size: 0.9rem; font-weight: 700; border: none; border-radius: 8px; cursor: pointer;" onclick="confirmDisapproval()">Submit Notice</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 3: OFFICIAL PERMIT & GATE PASS PREVIEW
       ========================================================================== -->
  <div class="admin-modal-overlay" id="permitPreviewModal" style="display: none;">
    <div class="admin-modal-card" style="max-width: 780px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4>Official Reservation Permit & Campus Gate Pass</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closePermitModal()" aria-label="Close modal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div id="permitPrintWrapper">
          <div class="permit-document-sheet">
            <div class="permit-header">
              <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="permit-header-logo">
              <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #4b5563;">
                Republic of the Philippines • Department of Agriculture</div>
              <h2 class="permit-title">Agricultural Training Institute</h2>
              <p class="permit-subtitle">ATI Central Office, Elliptical Road, Diliman, Quezon City</p>
              <div
                style="margin-top: 0.85rem; display: inline-block; background: #eaf3ed; border: 1px solid #174d2f; color: #174d2f; font-weight: 800; font-size: 0.82rem; padding: 0.25rem 0.85rem; border-radius: 999px;">
                OFFICIAL FACILITY RESERVATION PERMIT & GATE PASS
              </div>
            </div>

            <table class="permit-table">
              <tr>
                <td class="label-cell">Permit Reference ID</td>
                <td><strong id="permitModalRef" style="font-family: monospace; color: #174d2f;">--</strong></td>
              </tr>
              <tr>
                <td class="label-cell">Issuance Date</td>
                <td id="permitIssueDate">--</td>
              </tr>
              <tr>
                <td class="label-cell">Requesting Division / Agency</td>
                <td><strong id="permitAgency">--</strong> (<span id="permitApplicant">--</span>)</td>
              </tr>
              <tr>
                <td class="label-cell">Approved Activity Title</td>
                <td><strong id="permitEventTitle">--</strong></td>
              </tr>
              <tr>
                <td class="label-cell">Authorized Facility / Dorm</td>
                <td><strong id="permitVenue" style="color: #174d2f;">--</strong> (<span id="permitPax">--</span>)</td>
              </tr>
              <tr>
                <td class="label-cell">Approved Duration & Dates</td>
                <td><strong id="permitSchedule">--</strong></td>
              </tr>
            </table>

            <div class="permit-signatures-row" style="display: flex; gap: 1.5rem; justify-content: space-between;">
              <div class="signature-block" style="flex: 1;">
                <div class="signature-line">
                  <p class="signature-name" id="permitEndorserName">ENDORSING OFFICER</p>
                  <p class="signature-title">Division / Originating Unit Head</p>
                  <small style="color: #6b7280; font-size: 0.7rem;">Official Endorsement Issued</small>
                </div>
              </div>
              <div class="signature-block" style="flex: 1;">
                <div class="signature-line">
                  <p class="signature-name" id="permitRecommenderName">RECOMMENDING OFFICER</p>
                  <p class="signature-title">Administrative Services & Logistics Unit</p>
                  <small style="color: #6b7280; font-size: 0.7rem;">Verified Logistical Feasibility</small>
                </div>
              </div>
              <div class="signature-block" style="flex: 1;">
                <div class="signature-line">
                  <p class="signature-name" id="permitClearerName">CLEARANCE AUTHORITY</p>
                  <p class="signature-title">Office of the Executive Director</p>
                  <small style="color: #6b7280; font-size: 0.7rem;">Executive Clearance Granted</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="admin-modal-footer"
          style="padding: 1.25rem 0 0 0; background: none; border-top: 1px solid #edf3ef; justify-content: space-between;">
          <button type="button" class="btn-system-secondary" onclick="closePermitModal()">Close Window</button>
          <button type="button" class="btn-system-primary" onclick="printPermitDocument()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Print Official Permit (Gate Pass)</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- JavaScript Engine Data & Script -->
  <script>
    window.currentUserRole = "<?= $currentAdminRole ?>";
    window.initialApprovalsData = <?= json_encode((object)$approvalsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/admin_approvals.js?v=<?php echo time(); ?>"></script>
</body>

</html>