<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Dedicated Reservation Review & Approval Page (Option A)
 */
$ref = trim($_GET['ref'] ?? '');

if (empty($ref)) {
    header("Location: admin_approvals.php");
    exit;
}

// Fetch reservation details
$query = "SELECT r.*, 
                 f.name AS facility_name, 
                 f.type AS facility_type,
                 f.image AS facility_image,
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
          WHERE r.reference_no = :ref
          LIMIT 1";

$stmt = $db->prepare($query);
$stmt->execute([':ref' => $ref]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$res) {
    header("Location: admin_approvals.php?error=notfound");
    exit;
}

// Map stage
$st = $res['status'] ?? 'Pending';
if (in_array($st, ['Pending', 'Pending Endorsement'])) {
    $stage = 'stage1';
} elseif ($st === 'Pending Recommendation') {
    $stage = 'stage2';
} elseif ($st === 'Pending Clearance') {
    $stage = 'stage3';
} elseif (in_array($st, ['Approved', 'Completed'])) {
    $stage = 'approved';
} elseif (in_array($st, ['Rejected', 'Cancelled'])) {
    $stage = 'rejected';
} else {
    $stage = 'stage1';
}

$startDateTimestamp = strtotime($res['start_date']);
$startDateStr = date('F d, Y', $startDateTimestamp);
if (!empty($res['end_date']) && $res['end_date'] !== $res['start_date']) {
    $datesStr = date('M d', $startDateTimestamp) . ' – ' . date('M d, Y', strtotime($res['end_date']));
} else {
    $datesStr = $startDateStr;
}

$venueDisplay = $res['facility_name'] ?: 'Official Facility';
if (!empty($res['room_number'])) {
    $venueDisplay .= ' (Room ' . htmlspecialchars($res['room_number']) . ')';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Reservation <?= htmlspecialchars($ref) ?> | ATI System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/admin_dashboard.css">
  <link rel="stylesheet" href="css/admin_approvals.css">
  <style>
    .review-page-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 1.5rem 1rem 3rem;
    }
    .review-top-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .btn-back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.65rem 1.15rem;
      background: #ffffff;
      border: 1.5px solid #d4e4da;
      border-radius: 9px;
      font-size: 0.92rem;
      font-weight: 700;
      color: #174d2f;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(0,0,0,0.03);
      transition: all 0.2s ease;
    }
    .btn-back-link:hover {
      background: #eaf3ed;
      border-color: #174d2f;
    }
    .review-main-card {
      background: #ffffff;
      border: 1.5px solid #d4e4da;
      border-radius: 16px;
      padding: 2rem 2.25rem;
      box-shadow: 0 4px 18px rgba(0,0,0,0.04);
      margin-bottom: 1.75rem;
    }
    .review-header-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 1.25rem;
      border-bottom: 2px solid #edf4f0;
      margin-bottom: 1.75rem;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .review-header-title h2 {
      margin: 0;
      font-family: var(--font-serif);
      font-size: 1.65rem;
      color: #11291b;
      font-weight: 800;
    }
    .review-status-pill {
      font-size: 0.88rem;
      font-weight: 800;
      padding: 0.45rem 1rem;
      border-radius: 9999px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .review-status-pill.stage1 { background: #fef3c7; color: #92400e; border: 1.5px solid #fde68a; }
    .review-status-pill.stage2 { background: #dbeafe; color: #1e40af; border: 1.5px solid #bfdbfe; }
    .review-status-pill.stage3 { background: #d1fae5; color: #065f46; border: 1.5px solid #a7f3d0; }
    .review-status-pill.approved { background: #dcfce7; color: #166534; border: 1.5px solid #86efac; }
    .review-status-pill.rejected { background: #fee2e2; color: #991b1b; border: 1.5px solid #fecaca; }

    .review-section-box {
      background: #fbfdfc;
      border: 1.5px solid #e1ede5;
      border-radius: 12px;
      padding: 1.35rem 1.5rem;
      margin-bottom: 1.5rem;
    }
    .review-box-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #174d2f;
      display: flex;
      align-items: center;
      gap: 0.55rem;
      margin-bottom: 1.15rem;
      border-bottom: 1px solid #edf4f0;
      padding-bottom: 0.55rem;
    }
    .review-grid-2 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.25rem;
    }
    .review-field-unit label {
      display: block;
      font-size: 0.82rem;
      font-weight: 800;
      color: #4b6756;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      margin-bottom: 0.3rem;
    }
    .review-field-unit .val {
      font-size: 1rem;
      font-weight: 700;
      color: #172d1f;
    }

    .timeline-full-box {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 0.5rem;
    }
    .timeline-item-full {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 0.85rem 1rem;
      background: #ffffff;
      border: 1.5px solid #dce8e0;
      border-radius: 10px;
    }
    .timeline-badge-circle {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      font-weight: 800;
      color: #ffffff;
      flex-shrink: 0;
    }

    .review-action-banner {
      background: #ffffff;
      border: 2px solid #174d2f;
      border-radius: 14px;
      padding: 1.75rem 2rem;
      box-shadow: 0 8px 24px rgba(23, 77, 47, 0.08);
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .action-prompt-title {
      font-size: 1.15rem;
      font-weight: 800;
      color: #11291b;
      margin: 0;
    }
    .action-prompt-desc {
      font-size: 0.92rem;
      color: #4b6756;
      margin: 0;
    }
    .review-textarea {
      width: 100%;
      box-sizing: border-box;
      padding: 0.85rem 1rem;
      font-size: 0.95rem;
      border: 1.5px solid #c8dcd0;
      border-radius: 10px;
      font-family: inherit;
      resize: vertical;
    }
    .review-textarea:focus {
      outline: none;
      border-color: #174d2f;
      box-shadow: 0 0 0 3px rgba(23,77,47,0.12);
    }
    .review-buttons-row {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .btn-act-large {
      padding: 0.8rem 1.65rem;
      font-size: 0.98rem;
      font-weight: 800;
      border-radius: 10px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
      transition: all 0.2s ease;
    }
    .btn-act-large.approve {
      background: #174d2f;
      color: #ffffff;
      box-shadow: 0 4px 14px rgba(23,77,47,0.25);
    }
    .btn-act-large.approve:hover {
      background: #0f331f;
      transform: translateY(-2px);
    }
    .btn-act-large.decline {
      background: #ffffff;
      color: #dc2626;
      border: 2px solid #fca5a5;
    }
    .btn-act-large.decline:hover {
      background: #fee2e2;
    }
  </style>
</head>
<body style="background: #f4f8f5; min-height: 100vh; margin: 0; padding: 0; font-family: var(--font-sans); color: var(--text-main);">
  <!-- Simple Official Header Bar -->
  <header style="background: #ffffff; border-bottom: 1.5px solid #dce8e0; padding: 0.85rem 1.75rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <a href="admin_approvals.php" class="btn-back-link" style="background: #f0f7f2; border-color: #c4ded0; color: #174d2f;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Approvals</span>
      </a>
      <div style="height: 24px; width: 1.5px; background: #dce8e0;"></div>
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <img src="assets/images/ATI_Logo.png" alt="ATI Logo" style="height: 34px; width: auto;">
        <div>
          <h4 style="margin: 0; font-size: 0.98rem; font-weight: 800; color: #174d2f; line-height: 1.1;">ATI Reservation Review</h4>
          <span style="font-size: 0.78rem; color: #617d6d; font-weight: 600;">Central Office Facility System</span>
        </div>
      </div>
    </div>
    <div>
      <span class="review-status-pill <?= htmlspecialchars($stage) ?>">
        Status: <?= htmlspecialchars($st) ?>
      </span>
    </div>
  </header>

  <!-- Main Content Page (Full Width, Centered, Spacious) -->
  <main class="review-page-container" style="max-width: 980px; margin: 1.75rem auto 3.5rem; padding: 0 1.25rem;">
    <!-- Master Reservation Card -->
    <div class="review-main-card">
      <div class="review-header-title">
        <div>
          <span style="font-size: 0.85rem; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.05em;">Reservation Application</span>
          <h2>Reference No: <?= htmlspecialchars($ref) ?></h2>
        </div>
        <div style="font-size: 0.9rem; color: #557262; font-weight: 600;">
          Submitted on <?= date('F d, Y', strtotime($res['created_at'] ?? 'now')) ?>
        </div>
      </div>

          <!-- Section 1: Applicant Information -->
          <div class="review-section-box">
            <div class="review-box-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>1. Applicant Information</span>
            </div>
            <div class="review-grid-2">
              <div class="review-field-unit">
                <label>Contact Person / Full Name</label>
                <div class="val"><?= htmlspecialchars($res['applicant_name'] ?: 'N/A') ?></div>
              </div>
              <div class="review-field-unit">
                <label>Agency / Division / Office</label>
                <div class="val"><?= htmlspecialchars($res['applicant_agency'] ?: 'ATI Central Office') ?></div>
              </div>
              <div class="review-field-unit">
                <label>Email Address</label>
                <div class="val"><?= htmlspecialchars($res['applicant_email'] ?: 'N/A') ?></div>
              </div>
              <div class="review-field-unit">
                <label>Contact Number</label>
                <div class="val"><?= htmlspecialchars($res['applicant_phone'] ?: 'N/A') ?></div>
              </div>
            </div>
          </div>

          <!-- Section 2: Event & Venue Details -->
          <div class="review-section-box">
            <div class="review-box-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>2. Event & Venue Details</span>
            </div>
            <div class="review-grid-2" style="margin-bottom: 1.25rem;">
              <div class="review-field-unit">
                <label>Reserved Facility / Room</label>
                <div class="val" style="color: #174d2f; display: flex; align-items: center; gap: 0.65rem;">
                  <?php if (!empty($res['facility_image'])): ?>
                    <img src="<?= htmlspecialchars($res['facility_image']) ?>" alt="Facility" style="width: 38px; height: 38px; border-radius: 8px; object-fit: cover; border: 1.5px solid #c8dcd0;" onerror="this.style.display='none'">
                  <?php endif; ?>
                  <span><?= htmlspecialchars($venueDisplay) ?></span>
                </div>
              </div>
              <div class="review-field-unit">
                <label>Expected Attendees</label>
                <div class="val"><?= (int)($res['pax_count'] ?? 0) ?> Participants</div>
              </div>
              <div class="review-field-unit">
                <label>Event Date</label>
                <div class="val"><?= htmlspecialchars($datesStr) ?></div>
              </div>
              <div class="review-field-unit">
                <label>Time Slot</label>
                <div class="val"><?= htmlspecialchars($res['time_slot'] ?: 'Full Day (8:00 AM – 5:00 PM)') ?></div>
              </div>
            </div>

            <div class="review-field-unit" style="margin-bottom: 1.25rem;">
              <label>Official Activity Title</label>
              <div class="val" style="font-size: 1.15rem; color: #174d2f;"><?= htmlspecialchars($res['event_title'] ?: 'Official Institutional Activity') ?></div>
            </div>

            <div class="review-field-unit" style="margin-bottom: 1.25rem;">
              <label>Requested Audio-Visual & Equipment</label>
              <div class="val" style="font-size: 0.95rem; font-weight: 500; color: #284433;"><?= htmlspecialchars($res['special_notes'] ?: 'None specified') ?></div>
            </div>

            <div class="review-field-unit">
              <label>Attached Supporting Document</label>
              <div style="margin-top: 0.5rem;">
                <?php if (!empty($res['document_path'])): ?>
                  <a href="<?= htmlspecialchars($res['document_path']) ?>" target="_blank" class="btn-back-link" style="border-color: #174d2f; background: #f0fdf4;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <span>View Official Attached File (PDF / Image)</span>
                  </a>
                <?php else: ?>
                  <span style="font-size: 0.92rem; color: #889f92; font-style: italic;">No document uploaded for this request.</span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Section 3: Approval Progress / Timeline -->
          <div class="review-section-box">
            <div class="review-box-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>3. Three-Step Approval Workflow</span>
            </div>
            <div class="timeline-full-box">
              <!-- Step 1 -->
              <div class="timeline-item-full">
                <div class="timeline-badge-circle" style="background: <?= !empty($res['endorsed_by_name']) ? '#d97706' : '#9ca3af' ?>;">
                  <?= !empty($res['endorsed_by_name']) ? '✓' : '1' ?>
                </div>
                <div>
                  <strong style="font-size: 1rem; color: <?= !empty($res['endorsed_by_name']) ? '#b45309' : '#374151' ?>;">
                    Step 1: Endorsement
                  </strong>
                  <?php if (!empty($res['endorsed_by_name'])): ?>
                    <div style="font-size: 0.88rem; color: #4b6756; margin-top: 0.2rem;">
                      Endorsed by <strong><?= htmlspecialchars($res['endorsed_by_name']) ?></strong> on <?= date('M d, Y h:i A', strtotime($res['endorsed_at'])) ?>
                      <br><em>"<?= htmlspecialchars($res['endorsement_remarks'] ?: 'Endorsed') ?>"</em>
                    </div>
                  <?php else: ?>
                    <div style="font-size: 0.88rem; color: #6b7280; margin-top: 0.2rem;">Awaiting endorsement from Division / Unit Head.</div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Step 2 -->
              <div class="timeline-item-full">
                <div class="timeline-badge-circle" style="background: <?= !empty($res['recommended_by_name']) ? '#2563eb' : '#9ca3af' ?>;">
                  <?= !empty($res['recommended_by_name']) ? '✓' : '2' ?>
                </div>
                <div>
                  <strong style="font-size: 1rem; color: <?= !empty($res['recommended_by_name']) ? '#1d4ed8' : '#374151' ?>;">
                    Step 2: Recommendation
                  </strong>
                  <?php if (!empty($res['recommended_by_name'])): ?>
                    <div style="font-size: 0.88rem; color: #4b6756; margin-top: 0.2rem;">
                      Recommended by <strong><?= htmlspecialchars($res['recommended_by_name']) ?></strong> on <?= date('M d, Y h:i A', strtotime($res['recommended_at'])) ?>
                      <br><em>"<?= htmlspecialchars($res['recommendation_remarks'] ?: 'Recommended') ?>"</em>
                    </div>
                  <?php else: ?>
                    <div style="font-size: 0.88rem; color: #6b7280; margin-top: 0.2rem;">Awaiting room and equipment verification from Administrative Section.</div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Step 3 -->
              <div class="timeline-item-full">
                <div class="timeline-badge-circle" style="background: <?= !empty($res['cleared_by_name']) ? '#10b981' : '#9ca3af' ?>;">
                  <?= !empty($res['cleared_by_name']) ? '✓' : '3' ?>
                </div>
                <div>
                  <strong style="font-size: 1rem; color: <?= !empty($res['cleared_by_name']) ? '#047857' : '#374151' ?>;">
                    Step 3: Clearance
                  </strong>
                  <?php if (!empty($res['cleared_by_name'])): ?>
                    <div style="font-size: 0.88rem; color: #4b6756; margin-top: 0.2rem;">
                      Approved by <strong><?= htmlspecialchars($res['cleared_by_name']) ?></strong> on <?= date('M d, Y h:i A', strtotime($res['cleared_at'])) ?>
                      <br><em>"<?= htmlspecialchars($res['clearance_remarks'] ?: 'Approved') ?>"</em>
                    </div>
                  <?php else: ?>
                    <div style="font-size: 0.88rem; color: #6b7280; margin-top: 0.2rem;">Awaiting executive clearance from Office of the Director.</div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 4: Action Decision Banner -->
        <?php if ($stage === 'stage1'): ?>
          <?php if ($currentAdminRole === 'endorsement' || $currentAdminRole === 'super_admin'): ?>
            <div class="review-action-banner" style="border-color: #d97706; background: #fffdfa;">
              <h3 class="action-prompt-title" style="color: #b45309;">Action: Endorsement Desk</h3>
              <p class="action-prompt-desc">Verify that this activity is official, room/hall is available, and has valid justification before endorsing.</p>
              <textarea id="pageRemarksInput" class="review-textarea" rows="2" placeholder="Add remarks or instructions (optional)..."></textarea>
              <div class="review-buttons-row">
                <button type="button" class="btn-act-large decline" onclick="openDeclineModal()">Decline / Return Request</button>
                <button type="button" class="btn-act-large approve" style="background: #d97706;" onclick="submitPageAction('stage1')">Endorse Booking &rarr;</button>
              </div>
            </div>
          <?php else: ?>
            <div class="review-action-banner" style="background: #fbf8f2; border-color: #f0dcbc;">
              <h3 class="action-prompt-title" style="color: #b45309;">Awaiting Endorsement</h3>
              <p class="action-prompt-desc">This application is currently waiting for review and endorsement by an <strong>Endorsement Officer</strong>.</p>
            </div>
          <?php endif; ?>
        <?php elseif ($stage === 'stage2'): ?>
          <?php if ($currentAdminRole === 'recommendation' || $currentAdminRole === 'super_admin'): ?>
            <div class="review-action-banner" style="border-color: #2563eb; background: #f8faff;">
              <h3 class="action-prompt-title" style="color: #1d4ed8;">Action: Recommendation Desk</h3>
              <p class="action-prompt-desc">Confirm that the room is available, capacity is adequate, and equipment is ready.</p>
              <textarea id="pageRemarksInput" class="review-textarea" rows="2" placeholder="Add logistics verification notes (optional)..."></textarea>
              <div class="review-buttons-row">
                <button type="button" class="btn-act-large decline" onclick="openDeclineModal()">Decline / Return Request</button>
                <button type="button" class="btn-act-large approve" style="background: #2563eb;" onclick="submitPageAction('stage2')">Recommend Booking &rarr;</button>
              </div>
            </div>
          <?php else: ?>
            <div class="review-action-banner" style="background: #f2f7fd; border-color: #bcd4f5;">
              <h3 class="action-prompt-title" style="color: #1d4ed8;">Awaiting Recommendation</h3>
              <p class="action-prompt-desc">This application has been endorsed and is currently awaiting action by a <strong>Recommending Officer</strong>.</p>
            </div>
          <?php endif; ?>
        <?php elseif ($stage === 'stage3'): ?>
          <?php if ($currentAdminRole === 'clearance' || $currentAdminRole === 'super_admin'): ?>
            <div class="review-action-banner" style="border-color: #10b981; background: #f7fdf9;">
              <h3 class="action-prompt-title" style="color: #047857;">Action: Director Final Clearance</h3>
              <p class="action-prompt-desc">Endorsed and verified. Click approve to authorize the booking and generate the official permit.</p>
              <textarea id="pageRemarksInput" class="review-textarea" rows="2" placeholder="Final approval remarks or permit conditions (optional)..."></textarea>
              <div class="review-buttons-row">
                <button type="button" class="btn-act-large decline" onclick="openDeclineModal()">Decline / Return Request</button>
                <button type="button" class="btn-act-large approve" style="background: #10b981;" onclick="submitPageAction('stage3')">Approve Reservation &check;</button>
              </div>
            </div>
          <?php else: ?>
            <div class="review-action-banner" style="background: #f0faf4; border-color: #bbf0ce;">
              <h3 class="action-prompt-title" style="color: #047857;">Awaiting Director Clearance</h3>
              <p class="action-prompt-desc">This application is awaiting final directorial clearance and permit issuance by the <strong>Clearance Authority</strong>.</p>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="review-action-banner" style="background: #f8faf8; border-color: #c9e0d1;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
              <div>
                <h3 class="action-prompt-title" style="color: #174d2f;">Review Completed (Status: <?= htmlspecialchars(strtoupper($st)) ?>)</h3>
                <p class="action-prompt-desc">This reservation has already been processed in the system.</p>
              </div>
              <a href="download_form.php?ref=<?= urlencode($ref) ?>" target="_blank" class="btn-act-large approve" style="text-decoration: none;">
                Download Official Form (PDF)
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div> <!-- End .review-main-card -->

      <!-- Return Navigation Footer -->
      <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 0.5rem 0;">
        <a href="admin_approvals.php" class="btn-back-link" style="background: #ffffff;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
          <span>Back to Approvals Queue</span>
        </a>
        <button type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn-back-link" style="color: #4b6756; border-color: #e0ece4; cursor: pointer; background: #ffffff;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
          <span>Back to Top</span>
        </button>
      </div>
    </main>

  <!-- Decline Modal -->
  <div class="admin-modal-overlay" id="pageDeclineModal" style="display: none;">
    <div class="admin-modal-card sm" style="max-width: 560px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #b91c1c; font-size: 1.15rem; font-weight: 800;">Decline or Return Reservation</h4>
          <span style="font-size: 0.85rem; color: #4b6354;">Reference: <strong style="font-family: monospace;"><?= htmlspecialchars($ref) ?></strong></span>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDeclineModal()" aria-label="Close modal">&times;</button>
      </div>
      <div class="admin-modal-body" style="padding: 1.4rem;">
        <div class="form-group-decline" style="margin-bottom: 1rem;">
          <label style="font-size: 0.88rem; font-weight: 800; color: #172c1f; display: block; margin-bottom: 0.4rem;">Reason for Decline / Return:</label>
          <select id="declineReasonSelect" style="width: 100%; padding: 0.75rem; border: 1.5px solid #d4e4da; border-radius: 9px; font-family: inherit; font-size: 0.92rem; color: #172c1f; background: #ffffff;">
            <option value="Schedule Conflict with Priority Activity">Schedule Conflict with Priority Activity</option>
            <option value="Facility Scheduled for Maintenance">Facility Scheduled for Maintenance</option>
            <option value="Incomplete Supporting Documents / Missing Letter">Incomplete Supporting Documents / Missing Letter</option>
            <option value="Number of Attendees Exceeds Room Capacity">Number of Attendees Exceeds Room Capacity</option>
            <option value="Administrative Grounds / Other Official Reasons">Administrative Grounds / Other Official Reasons</option>
          </select>
        </div>
        <div class="form-group-decline" style="margin-bottom: 1.25rem;">
          <label style="font-size: 0.88rem; font-weight: 800; color: #172c1f; display: block; margin-bottom: 0.4rem;">Instructions / Notes for Applicant:</label>
          <textarea id="declineRemarksText" rows="3" placeholder="Specify reason or instructions for the applicant..." style="width: 100%; box-sizing: border-box; padding: 0.75rem; border: 1.5px solid #d4e4da; border-radius: 9px; font-family: inherit; font-size: 0.92rem; color: #172c1f; resize: vertical;"></textarea>
        </div>
        <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef; display: flex; gap: 0.75rem; justify-content: flex-end;">
          <button type="button" class="btn-system-secondary" style="padding: 0.65rem 1.2rem; font-size: 0.9rem; font-weight: 700;" onclick="closeDeclineModal()">Cancel</button>
          <button type="button" class="btn-confirm-decline" style="background: #dc2626; color: #ffffff; padding: 0.65rem 1.2rem; font-size: 0.9rem; font-weight: 700; border: none; border-radius: 8px; cursor: pointer;" onclick="submitDeclineAction()">Submit Notice</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const currentRef = "<?= addslashes($ref) ?>";

    function openDeclineModal() {
      document.getElementById('pageDeclineModal').style.display = 'flex';
    }
    function closeDeclineModal() {
      document.getElementById('pageDeclineModal').style.display = 'none';
    }

    async function submitPageAction(actionType) {
      const remarks = document.getElementById('pageRemarksInput') ? document.getElementById('pageRemarksInput').value.trim() : '';
      
      const formData = new FormData();
      formData.append('action', actionType);
      formData.append('ref', currentRef);
      formData.append('remarks', remarks);

      try {
        const response = await fetch('ajax/update_approval_stage.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          alert('Success: ' + result.message);
          window.location.href = 'admin_approvals.php';
        } else {
          alert('Error: ' + result.message);
        }
      } catch (err) {
        alert('Network or server error while updating.');
      }
    }

    async function submitDeclineAction() {
      const reason = document.getElementById('declineReasonSelect').value;
      const remarks = document.getElementById('declineRemarksText').value.trim();

      const formData = new FormData();
      formData.append('action', 'disapprove');
      formData.append('ref', currentRef);
      formData.append('reason', reason);
      formData.append('remarks', remarks);

      try {
        const response = await fetch('ajax/update_approval_stage.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          alert('Notice submitted successfully.');
          window.location.href = 'admin_approvals.php';
        } else {
          alert('Error: ' + result.message);
        }
      } catch (err) {
        alert('Network error while declining.');
      }
    }
  </script>
</body>
</html>
