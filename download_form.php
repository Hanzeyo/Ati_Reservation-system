<?php
require_once __DIR__ . '/includes/init.php';

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';

if ($type === 'dorm' || $type === 'dormitory' || $type === 'dorms') {
  header('Location: download_dormitory_form.php' . (!empty($ref) ? '?ref=' . urlencode($ref) : ''));
  exit;
}

if (!empty($ref)) {
  try {
    $stmt = $db->prepare("SELECT f.type FROM reservations r LEFT JOIN facilities f ON r.facility_id = f.id WHERE r.reference_no = ? LIMIT 1");
    $stmt->execute([$ref]);
    $resType = $stmt->fetchColumn();
    if ($resType === 'dorms') {
      header('Location: download_dormitory_form.php?ref=' . urlencode($ref));
      exit;
    }
  } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ATI Training Room / Facility Reservation Form &amp; Terms</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <style>
    @page {
      size: A4 portrait;
      margin: 15mm 18mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    body {
      font-family: 'Plus Jakarta Sans', Arial, sans-serif;
      font-size: 11pt;
      color: #111827;
      background: #f3f4f6;
      margin: 0;
      padding: 20px 0;
    }
    .page-sheet {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto 20px auto;
      padding: 18mm 20mm;
      box-shadow: 0 4px 14px rgba(0,0,0,0.1);
      position: relative;
    }
    .print-controls-bar {
      width: 210mm;
      margin: 0 auto 15px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #175432;
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(23,84,50,0.25);
    }
    .btn-print {
      background: #facc15;
      color: #1c1917;
      border: none;
      padding: 9px 20px;
      border-radius: 8px;
      font-weight: 800;
      font-size: 0.95rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-print:hover {
      background: #fde047;
      transform: translateY(-1px);
    }
    .btn-back {
      background: transparent;
      color: #ffffff;
      border: 1px solid rgba(255,255,255,0.4);
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.88rem;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-back:hover {
      background: rgba(255,255,255,0.1);
    }
    /* Official Form Header */
    .form-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 12px;
      text-align: center;
    }
    .form-header-logo {
      width: 68px;
      height: 68px;
      object-fit: contain;
    }
    .form-header-text h4 {
      margin: 0;
      font-size: 9.5pt;
      font-weight: 600;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .form-header-text h3 {
      margin: 2px 0;
      font-size: 11pt;
      font-weight: 700;
      color: #111827;
    }
    .form-header-text h2 {
      margin: 2px 0;
      font-size: 12.5pt;
      font-weight: 800;
      color: #175432;
      letter-spacing: 0.02em;
    }
    .form-header-text p {
      margin: 2px 0 0 0;
      font-size: 8.5pt;
      color: #4b5563;
    }
    .form-title-banner {
      text-align: center;
      font-size: 13pt;
      font-weight: 800;
      color: #111827;
      border-bottom: 2px solid #111827;
      padding-bottom: 6px;
      margin: 14px 0 16px 0;
      letter-spacing: 0.03em;
    }
    .field-row {
      display: flex;
      align-items: flex-end;
      margin-bottom: 9px;
      font-size: 9.5pt;
      line-height: 1.25;
    }
    .field-label {
      font-weight: 700;
      color: #111827;
      white-space: nowrap;
      margin-right: 6px;
    }
    .field-line {
      flex: 1;
      border-bottom: 1px solid #111827;
      min-height: 18px;
    }
    .checkbox-group {
      display: inline-flex;
      align-items: center;
      gap: 16px;
      margin-left: 8px;
    }
    .cb-item {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 9pt;
      font-weight: 600;
    }
    .cb-box {
      width: 13px;
      height: 13px;
      border: 1.5px solid #111827;
      display: inline-block;
    }
    /* Equipment Table */
    .charges-table {
      width: 100%;
      border-collapse: collapse;
      margin: 12px 0 14px 0;
      font-size: 9pt;
    }
    .charges-table th, .charges-table td {
      border: 1px solid #111827;
      padding: 6px 10px;
      text-align: left;
    }
    .charges-table th {
      background: #f3f4f6;
      font-weight: 700;
      text-align: center;
    }
    .charges-note {
      font-size: 8.2pt;
      font-style: italic;
      color: #374151;
      margin-top: 4px;
      margin-bottom: 12px;
      text-align: center;
    }
    .notice-box {
      font-weight: 800;
      font-size: 8.8pt;
      color: #111827;
      margin: 14px 0 16px 0;
      text-transform: uppercase;
    }
    .sig-section-1 {
      margin-top: 24px;
      text-align: center;
    }
    .sig-party-line {
      width: 60%;
      margin: 0 auto;
      border-bottom: 1px solid #111827;
      padding-top: 20px;
    }
    .sig-party-caption {
      font-size: 8.5pt;
      font-style: italic;
      margin-top: 4px;
    }
    .approval-grid {
      display: flex;
      justify-content: space-between;
      margin-top: 24px;
      font-size: 9.5pt;
    }
    .approval-col {
      width: 45%;
    }
    .approval-title {
      font-weight: 700;
      margin-bottom: 28px;
    }
    .approval-name {
      border-top: 1px solid #111827;
      padding-top: 4px;
      font-weight: 700;
      font-size: 9pt;
      text-align: center;
    }
    /* PAGE 2: TERMS AND CONDITIONS */
    .terms-title-banner {
      text-align: center;
      font-size: 13pt;
      font-weight: 800;
      color: #111827;
      border-bottom: 2px solid #111827;
      padding-bottom: 6px;
      margin: 14px 0 14px 0;
      letter-spacing: 0.03em;
    }
    .terms-table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0 14px 0;
      font-size: 8.8pt;
    }
    .terms-table th, .terms-table td {
      border: 1px solid #111827;
      padding: 5px 8px;
      vertical-align: middle;
    }
    .terms-table th {
      background: #f3f4f6;
      font-weight: 700;
      text-align: center;
    }
    .terms-ol {
      margin: 0 0 14px 0;
      padding-left: 20px;
      font-size: 8.6pt;
      line-height: 1.45;
      color: #1f2937;
    }
    .terms-ol li {
      margin-bottom: 6px;
    }
    @media print {
      body {
        background: none;
        padding: 0;
      }
      .print-controls-bar {
        display: none !important;
      }
      .page-sheet {
        box-shadow: none;
        margin: 0;
        width: 100%;
        min-height: auto;
        padding: 0;
        page-break-after: always;
      }
      .page-sheet:last-child {
        page-break-after: auto;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Bar (Visible in browser, hidden when printed) -->
  <div class="print-controls-bar">
    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="booking.php" class="btn-back">&larr; Back to Booking</a>
      <span style="font-weight: 700; font-size: 0.95rem;">Official ATI Facility Reservation Form &amp; Terms Document</span>
    </div>
    <div style="display: flex; gap: 10px;">
      <button type="button" class="btn-print" onclick="window.print()">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        <span>Print / Save as PDF</span>
      </button>
    </div>
  </div>

  <!-- ==========================================
       PAGE 1: RESERVATION REQUEST FORM
       ========================================== -->
  <div class="page-sheet">
    <div class="form-header">
      <img src="assets/images/ATI_Logo.png" alt="Department of Agriculture - ATI Logo" class="form-header-logo">
      <div class="form-header-text">
        <h4>Republic of the Philippines</h4>
        <h3>Department of Agriculture</h3>
        <h2>AGRICULTURAL TRAINING INSTITUTE</h2>
        <p>Contact Nos. (02) 89298541 to 49 local 283 &amp; (0999) 220-0064 &bull; e-mail address: dormitory.co@ati.da.gov.ph</p>
      </div>
    </div>

    <div class="form-title-banner">
      ATI TRAINING ROOM/FACILITY RESERVATION FORM
    </div>

    <div class="field-row" style="justify-content: flex-end; margin-bottom: 14px;">
      <span class="field-label">DATE:</span>
      <div class="field-line" style="flex: 0 0 220px;"></div>
    </div>

    <div class="field-row">
      <span class="field-label">AGENCY:</span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">ADDRESS:</span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">CONTACT PERSON:</span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">Landline Number:</span>
      <div class="field-line" style="flex: 0 0 200px;"></div>
      <span class="field-label" style="margin-left: 20px;">Mobile number:</span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">NAME OF TRAINING ROOM/FACILITY <span style="font-weight: 500; font-size: 8.5pt;">(specify training room/facility to be reserved):</span></span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">PURPOSE:</span>
      <div class="checkbox-group">
        <span class="cb-item"><span class="cb-box"></span> Training/Seminar</span>
        <span class="cb-item"><span class="cb-box"></span> Meeting/Conference</span>
        <span class="cb-item"><span class="cb-box"></span> Others, specify:</span>
      </div>
      <div class="field-line" style="margin-left: 6px;"></div>
    </div>

    <div class="field-row">
      <span class="field-label">TITLE OF ACTIVITY:</span>
      <div class="field-line"></div>
    </div>

    <div style="font-weight: 700; font-size: 9.5pt; margin: 10px 0 4px 0;">DURATION OF USE:</div>
    <div class="field-row" style="padding-left: 14px;">
      <span class="field-label" style="font-weight: 600;">Starting Date and Time:</span>
      <div class="field-line" style="flex: 0 0 220px;"></div>
      <span class="field-label" style="font-weight: 600; margin-left: 18px;">Ending Date and Time:</span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">INTENDED USERS:</span>
      <div class="field-line" style="flex: 0 0 320px;"></div>
      <span class="field-label" style="margin-left: 18px;">NO. OF USERS:</span>
      <div class="field-line"></div>
    </div>

    <div class="field-row">
      <span class="field-label">ASSISTANCE REQUESTED, if any:</span>
      <div class="field-line"></div>
    </div>

    <div style="font-weight: 700; font-size: 9.5pt; margin-top: 10px;">Equipment Charges:</div>
    <table class="charges-table">
      <thead>
        <tr>
          <th style="width: 32%;">Equipment</th>
          <th style="width: 36%;">Rate</th>
          <th style="width: 32%;">Reserved Date and Number of Equipment</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Sound System</td>
          <td>₱ 1,500.00/day for occasion only</td>
          <td></td>
        </tr>
        <tr>
          <td>Videoke</td>
          <td>₱ 1,500.00/day</td>
          <td></td>
        </tr>
        <tr>
          <td>Electricity Charge</td>
          <td>₱ 100.00/unit /day</td>
          <td></td>
        </tr>
        <tr>
          <td>LED WALL</td>
          <td>₱ 15,000.00/8 hrs.</td>
          <td></td>
        </tr>
      </tbody>
    </table>

    <div class="charges-note">
      (Note: The entries under each item/column above will vary depending on the available facilities of the Center)
    </div>

    <div style="font-size: 9.5pt; margin-bottom: 6px;">
      <span style="font-weight: 700;">Mode of Payment:</span>
    </div>
    <div class="field-row" style="padding-left: 18px;">
      <span class="field-label" style="font-weight: 600;">Cash:</span>
      <div class="field-line" style="flex: 0 0 340px;"></div>
    </div>
    <div class="field-row" style="padding-left: 18px;">
      <span class="field-label" style="font-weight: 600;">Check/LDDAP-ADA no.:</span>
      <div class="field-line" style="flex: 0 0 340px;"></div>
    </div>

    <div class="notice-box">
      NOTE: PLEASE COMPLY THE ATTACHED TERMS AND CONDITION BEFORE THE APPROVAL:
    </div>

    <div class="sig-section-1">
      <div class="sig-party-line"></div>
      <div class="sig-party-caption">(Name &amp; Signature of Requesting Party/Representative)</div>
    </div>

    <div class="approval-grid">
      <div class="approval-col">
        <div class="approval-title">Recommending Approval:</div>
        <div class="approval-name">Dormitory Manager</div>
      </div>
      <div class="approval-col">
        <div class="approval-title">APPROVED:</div>
        <div class="approval-name">Head, General Services Office</div>
      </div>
    </div>
  </div>

  <!-- ==========================================
       PAGE 2: TERMS AND CONDITIONS
       ========================================== -->
  <div class="page-sheet">
    <div class="form-header">
      <img src="assets/images/ATI_Logo.png" alt="Department of Agriculture - ATI Logo" class="form-header-logo">
      <div class="form-header-text">
        <h4>Republic of the Philippines</h4>
        <h3>Department of Agriculture</h3>
        <h2>AGRICULTURAL TRAINING INSTITUTE</h2>
        <p>Contact Nos. (02) 89298541 to 49 local 283 &amp; (0999) 220-0064 &bull; e-mail address: dormitory.co@ati.da.gov.ph</p>
      </div>
    </div>

    <div class="terms-title-banner">
      ATI TRAINING ROOM/FACILITY TERMS &amp; CONDITIONS
    </div>

    <p style="font-size: 8.8pt; margin: 0 0 8px 0; font-weight: 600;">
      1. The Requesting Party shall shoulder the following rental charges/fees for the use of training room/facilities:
    </p>

    <table class="terms-table">
      <thead>
        <tr>
          <th style="width: 12%;">Pls. check as appropriate</th>
          <th style="width: 40%;">Training Room/Facility</th>
          <th style="width: 48%;">Rates</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>RDEC Main Function Hall</strong><br><small>(60 to 100 pax)</small></td>
          <td>₱15,000.00/day (8 hrs); ₱1,875.00 for every excess hour or a fraction without LED Wall rental. Extension is until 10 pm only</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>RDEC Main Function Hall</strong><br><small>(60 to 100 pax) For Dry-run/rehearsal with LED Usage</small></td>
          <td>₱30,000.00/day (8 hrs); ₱1,875.00 for every excess hour or a fraction with LED Wall rental. Extension is until 10 pm only.<br>₱1,875.00 for every hour</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>RDEC Training Hall</strong><br><small>(40 to 60 pax)</small></td>
          <td>₱8,000.00/day (8hrs); ₱1,000.00/hour for every excess or a fraction. Extension is until 10 pm only</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>ATI Training Hall 1</strong><br><small>(80 to 100 pax)</small></td>
          <td>₱10,000.00/day (8 hrs); ₱1,250.00 for every excess hour or a fraction. Extension is until 10 pm only</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>Executive Board Room</strong><br><small>(28 seaters)</small></td>
          <td>₱5,000.00/day (8 hrs); ₱750.00/hour for excess or a fraction. Extension is until 10 pm only</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>Serrano Hall</strong><br><small>(60-80 seaters)</small></td>
          <td>₱10,000.00/day (8 hrs); ₱1,250.00 for every excess hour or a fraction. Extension is until 10 pm only</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>Enterprise Laboratory</strong><br><small>(40 to 45 pax)</small></td>
          <td>₱6,000.00/day (8 hrs); ₱750.00/hour for excess or a fraction. Extension is until 10 pm only</td>
        </tr>
        <tr>
          <td style="text-align: center;"><span class="cb-box"></span></td>
          <td><strong>ATI Training Hall 2</strong><br><small>(80 to 100 pax) located at 2nd floor left wing</small></td>
          <td>₱10,000.00/day (8 hrs); ₱1,250.00 for every excess hour or a fraction. Extension is until 10 pm only</td>
        </tr>
      </tbody>
    </table>

    <ol class="terms-ol" start="2">
      <li>Request/s for the use of the training hall/facility shall be addressed to the ATI Director, only approved request will be honored.<br>
      Reservations shall be made at least one (1) month ahead of scheduled date and a down payment of 50% of the total cost is required for confirmation two weeks before the activity date, otherwise the reservation shall automatically be cancelled. If reservation is cancelled a week before the scheduled activity date only 50% of the down payment paid will be refunded.</li>
      <li>The fees/ charges for the use of training room/facility are inclusive of power charges, use of sound system, light system, podium, projector and projector screen and air-conditioning system. But an additional fee of ₱100.00 PER HOUR shall be charged for equipment brought in by the requesting party.</li>
      <li>For non-ATI reservation, which would last beyond regular working hours, including holidays and weekends, the requesting party shall bear the cost of overtime pay of the Audio-Visual Technician and the Utility Man assigned in the training room/facility, which is payable directly to the individual concern.</li>
      <li>The catering needs of the requesting party like foods and beverages shall be outsourced by the requesting party and a minimal fee will be imposed to the caterer if they use light and water.</li>
      <li>Generally, cooking/ re-heating of food inside the training rooms/facility are prohibited.</li>
      <li>Equipment, furniture and other items found inside the training room should not be removed from its designated places, unless otherwise allowed. Any damages/losses inside the facilities shall be charged to the Requesting Party.</li>
      <li>The ATI reserves the right to assign an alternate training room/facility should the contracted function hall unavailable for whatever reason.</li>
      <li>ATI shall not be held liable for any loss of personal belongings and other valuables inside the training room/facilities.</li>
      <li>Firearms, explosive and flammable materials are not allowed inside the ATI building/compound. Guest/visitors are required to surrender any firearms at the Guard-on-duty at the lobby of the main entrance of the building for proper safekeeping.</li>
      <li>Payment for the use of the training rooms shall be made directly to the ATI Cashier or Designated Collection Officer who shall issue the corresponding Official Receipt.</li>
    </ol>

    <div style="margin-top: 18px;">
      <div style="font-weight: 800; font-size: 9pt; margin-bottom: 24px;">CONFORME:</div>
      <div style="width: 70%; border-bottom: 1px solid #111827; margin-bottom: 4px;"></div>
      <div style="font-size: 8.5pt; font-style: italic;">(Printed Name &amp; Signature of Requesting Party/Representative)</div>
    </div>
  </div>

</body>
</html>
