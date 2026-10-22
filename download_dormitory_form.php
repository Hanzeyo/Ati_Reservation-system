<?php
require_once __DIR__ . '/includes/init.php';

$mode = isset($_GET['mode']) ? strtolower(trim($_GET['mode'])) : '';
$templateInfo = SystemSetting::getTemplateInfo('dormitory');

if ($mode !== 'html' && !empty($templateInfo['is_custom']) && !empty($templateInfo['file_path'])) {
    $absPath = __DIR__ . '/' . ltrim($templateInfo['file_path'], '/\\');
    if (file_exists($absPath)) {
        $downloadName = $templateInfo['file_name'] ?: 'Official_ATI_Dormitory_Reservation_Form.pdf';
        $mime = $templateInfo['mime_type'] ?: 'application/octet-stream';
        $isDownload = isset($_GET['download']) && $_GET['download'] === '1';
        $disposition = $isDownload ? 'attachment' : 'inline';
        
        header('Content-Type: ' . $mime);
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($downloadName) . '"');
        header('Content-Length: ' . filesize($absPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        readfile($absPath);
        exit;
    }
}

// Blank template variables (ready for manual filling / printing)
$controlNo = '';
$formDate = '';
$clientName = '';
$agency = '';
$address = '';
$phone = '';
$email = '';

$roomsPreferred = '';
$numRooms = '';
$inclusiveDates = '';
$numDays = '';
$numGuests = '';
$maleGuests = '';
$femaleGuests = '';
$purpose = '';
$arrangements = '';
$remarks = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ATI Dormitory Reservation Form - ATI-QF/DTS-22</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <style>
    @page {
      size: A4 portrait;
      margin: 8mm 12mm 6mm 12mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    body {
      font-family: Arial, 'Plus Jakarta Sans', sans-serif;
      font-size: 8.5pt;
      color: #111827;
      background: #f3f4f6;
      margin: 0;
      padding: 16px 0;
      line-height: 1.25;
    }
    .page-sheet {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto 20px auto;
      padding: 12mm 15mm 10mm 15mm;
      box-shadow: 0 4px 14px rgba(0,0,0,0.1);
      position: relative;
    }
    .print-controls-bar {
      width: 210mm;
      margin: 0 auto 12px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #175432;
      color: #ffffff;
      padding: 10px 18px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(23,84,50,0.25);
    }
    .btn-print {
      background: #facc15;
      color: #1c1917;
      border: none;
      padding: 8px 18px;
      border-radius: 6px;
      font-weight: 800;
      font-size: 0.9rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 7px;
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
      padding: 7px 14px;
      border-radius: 6px;
      font-size: 0.85rem;
      text-decoration: none;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .btn-back:hover {
      background: rgba(255,255,255,0.1);
    }

    /* Form Header Layout */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 4px;
    }
    .header-table td {
      vertical-align: middle;
      padding: 0;
    }
    .logo-cell {
      width: 58px;
      text-align: left;
    }
    .logo-cell img {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }
    .inst-cell {
      text-align: left;
      padding-left: 8px !important;
    }
    .inst-cell .p-rep {
      margin: 0;
      font-size: 8.5pt;
      font-weight: 600;
      color: #1f2937;
    }
    .inst-cell .p-da {
      margin: 0;
      font-size: 8.5pt;
      font-weight: 600;
      color: #1f2937;
    }
    .inst-cell .p-inst {
      margin: 1px 0;
      font-size: 10.5pt;
      font-weight: 800;
      color: #000000;
      letter-spacing: 0.02em;
    }
    .inst-cell .p-contact {
      margin: 0;
      font-size: 7.2pt;
      color: #374151;
    }
    .ctrl-cell {
      width: 200px;
      text-align: right;
      vertical-align: bottom !important;
      font-size: 8.5pt;
    }
    .ctrl-line {
      display: inline-block;
      min-width: 110px;
      border-bottom: 1px solid #000;
      font-weight: 700;
      text-align: center;
      padding: 0 4px;
    }

    /* Form Title */
    .form-title-wrapper {
      text-align: center;
      margin: 10px 0 6px 0;
    }
    .form-main-title {
      font-size: 12pt;
      font-weight: 800;
      letter-spacing: 0.04em;
      color: #000;
      margin: 0;
    }
    .form-sub-title {
      font-size: 8.2pt;
      font-style: italic;
      margin: 1px 0 0 0;
      color: #1f2937;
    }

    .date-row {
      text-align: right;
      margin-bottom: 6px;
      font-size: 8.5pt;
      font-weight: 700;
    }
    .date-line {
      display: inline-block;
      min-width: 150px;
      border-bottom: 1px solid #000;
      text-align: center;
      font-weight: 600;
      padding: 0 6px;
    }

    /* Form Fields */
    .field-row {
      display: flex;
      align-items: flex-end;
      margin-bottom: 4px;
      font-size: 8.2pt;
    }
    .field-label {
      font-weight: 700;
      color: #000;
      white-space: nowrap;
      margin-right: 4px;
    }
    .field-line {
      flex: 1;
      border-bottom: 1px solid #000;
      min-height: 15px;
      padding: 0 4px;
      font-weight: 500;
      color: #111827;
    }

    /* Particulars */
    .particulars-title {
      font-weight: 800;
      font-size: 8.5pt;
      margin: 5px 0 3px 0;
      color: #000;
    }
    .part-item {
      display: flex;
      align-items: flex-end;
      margin-bottom: 3.5px;
      font-size: 8pt;
      padding-left: 4px;
    }
    .part-bullet {
      font-size: 8pt;
      margin-right: 4px;
      color: #000;
    }

    /* Signatures Section */
    .client-sig-box {
      text-align: center;
      margin-top: 10px;
      margin-bottom: 8px;
    }
    .client-sig-line {
      width: 280px;
      margin: 0 auto;
      border-bottom: 1px solid #000;
      min-height: 16px;
      font-weight: 700;
      font-size: 8.5pt;
    }
    .client-sig-caption {
      font-size: 7.8pt;
      font-style: italic;
      margin-top: 2px;
      color: #111827;
    }

    .approval-row {
      display: flex;
      justify-content: space-between;
      margin-top: 8px;
      margin-bottom: 6px;
      font-size: 8pt;
    }
    .approval-col {
      width: 44%;
      text-align: center;
    }
    .approval-head {
      font-weight: 700;
      margin-bottom: 22px;
      text-align: left;
    }
    .approval-sig-line {
      border-top: 1px solid #000;
      padding-top: 2px;
      font-weight: 600;
      font-size: 8pt;
    }

    .section-divider {
      border-bottom: 1.5px solid #000;
      margin: 6px 0 6px 0;
    }

    /* Rates, Terms & Conditions */
    .terms-heading {
      text-align: center;
      font-size: 9.5pt;
      font-weight: 800;
      margin: 4px 0 5px 0;
      letter-spacing: 0.03em;
    }
    .terms-ol {
      margin: 0;
      padding-left: 14px;
      font-size: 6.8pt;
      line-height: 1.25;
      color: #111;
    }
    .terms-ol li {
      margin-bottom: 2.2px;
      text-align: justify;
    }
    .room-rates-subgroup {
      margin: 2px 0 3px 0;
      padding-left: 2px;
      display: flex;
      flex-wrap: wrap;
      gap: 4px 10px;
      font-size: 6.8pt;
    }
    .rate-cb-item {
      display: inline-flex;
      align-items: center;
      gap: 3px;
    }
    .cb-square {
      width: 9px;
      height: 9px;
      border: 1px solid #000;
      display: inline-block;
      flex-shrink: 0;
    }

    /* Conforme */
    .conforme-section {
      margin-top: 5px;
      font-size: 7.5pt;
    }
    .conforme-label {
      font-weight: 800;
      margin-bottom: 12px;
    }
    .conforme-line {
      width: 280px;
      border-bottom: 1px solid #000;
      min-height: 14px;
      font-weight: 700;
      text-align: center;
    }
    .conforme-caption {
      font-size: 7.2pt;
      font-style: italic;
      margin-top: 2px;
    }

    /* Form Footer */
    .form-official-footer {
      margin-top: 6px;
      font-size: 7pt;
      font-weight: 700;
      color: #000;
    }

    @media print {
      body {
        background: none;
        padding: 0;
        margin: 0;
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
        page-break-after: avoid;
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Bar (Visible in browser, hidden when printed) -->
  <div class="print-controls-bar">
    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="dormitory_booking.php" class="btn-back">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Dormitory Booking</span>
      </a>
      <span style="font-weight: 700; font-size: 0.95rem;">Official ATI Dormitory Reservation Form (ATI-QF/DTS-22)</span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
      <?php if (!empty($templateInfo['is_custom'])): ?>
      <a href="download_dormitory_form.php?download=1" class="btn-print" style="background: #22c55e; color: #ffffff;" title="Download the uploaded official PDF/Doc file">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <span>Download Uploaded Official File</span>
      </a>
      <?php endif; ?>
      <button type="button" class="btn-print" onclick="window.print()">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        <span>Print / Save as PDF</span>
      </button>
    </div>
  </div>

  <!-- ==========================================
       OFFICIAL ATI DORMITORY RESERVATION FORM
       Form Code: ATI-QF/DTS-22 Rev. 02
       ========================================== -->
  <div class="page-sheet">

    <!-- Header Table -->
    <table class="header-table">
      <tr>
        <td class="logo-cell">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo">
        </td>
        <td class="inst-cell">
          <p class="p-rep">Republic of the Philippines</p>
          <p class="p-da">Department of Agriculture</p>
          <p class="p-inst">AGRICULTURAL TRAINING INSTITUTE</p>
          <p class="p-contact">Contact Nos. (02) 89298541 to 49 local 283 &amp; (0999) 220-0064 &bull; e-mail address: dormitory.co@ati.da.gov.ph</p>
        </td>
        <td class="ctrl-cell">
          Control Number: <span class="ctrl-line"><?= htmlspecialchars($controlNo) ?></span>
        </td>
      </tr>
    </table>

    <!-- Title Banner -->
    <div class="form-title-wrapper">
      <h1 class="form-main-title">ATI DORMITORY RESERVATION FORM</h1>
      <p class="form-sub-title">(To be filled-up by the Requesting Party)</p>
    </div>

    <!-- Date -->
    <div class="date-row">
      DATE: <span class="date-line"><?= htmlspecialchars($formDate) ?></span>
    </div>

    <!-- Client Info Rows -->
    <div class="field-row">
      <span class="field-label">NAME OF REQUESTING CLIENT/PROJECT OFFICER:</span>
      <div class="field-line"><?= htmlspecialchars($clientName) ?></div>
    </div>

    <div class="field-row">
      <span class="field-label">AGENCY:</span>
      <div class="field-line" style="flex: 0 0 280px;"><?= htmlspecialchars($agency) ?></div>
      <span class="field-label" style="margin-left: 12px;">ADDRESS:</span>
      <div class="field-line"><?= htmlspecialchars($address) ?></div>
    </div>

    <div class="field-row">
      <span class="field-label">CONTACT TEL. NO.:</span>
      <div class="field-line" style="flex: 0 0 240px;"><?= htmlspecialchars($phone) ?></div>
      <span class="field-label" style="margin-left: 12px;">E-mail ADDRESS:</span>
      <div class="field-line"><?= htmlspecialchars($email) ?></div>
    </div>

    <!-- Particulars -->
    <div class="particulars-title">PARTICULARS:</div>

    <div class="part-item">
      <span class="part-bullet">&#10148;</span>
      <span class="field-label">Room/s Preferred:</span>
      <div class="field-line" style="flex: 1;"><?= htmlspecialchars($roomsPreferred) ?></div>
      <span class="field-label" style="margin-left: 14px;">Number of Room/s:</span>
      <div class="field-line" style="flex: 0 0 110px; text-align: center;"><?= htmlspecialchars($numRooms) ?></div>
    </div>

    <div class="part-item">
      <span class="part-bullet">&#10148;</span>
      <span class="field-label">Inclusive Dates:</span>
      <div class="field-line" style="flex: 1;"><?= htmlspecialchars($inclusiveDates) ?></div>
      <span class="field-label" style="margin-left: 14px;">Number of Day/s:</span>
      <div class="field-line" style="flex: 0 0 110px; text-align: center;"><?= htmlspecialchars($numDays) ?></div>
    </div>

    <div class="part-item">
      <span class="part-bullet">&#10148;</span>
      <span class="field-label">No. of Guest/s (Attached List of Guest/s reserved) :</span>
      <div class="field-line" style="flex: 0 0 80px; text-align: center;"><?= htmlspecialchars($numGuests) ?></div>
      <span class="field-label" style="margin-left: 16px;">Male:</span>
      <div class="field-line" style="flex: 0 0 70px; text-align: center;"><?= htmlspecialchars($maleGuests) ?></div>
      <span class="field-label" style="margin-left: 16px;">Female:</span>
      <div class="field-line" style="flex: 0 0 70px; text-align: center;"><?= htmlspecialchars($femaleGuests) ?></div>
    </div>

    <div class="part-item">
      <span class="part-bullet">&#10148;</span>
      <span class="field-label">Purpose (Title of Training/Activity, Others):</span>
      <div class="field-line" style="flex: 1;"><?= htmlspecialchars($purpose) ?></div>
    </div>

    <div class="part-item">
      <span class="part-bullet">&#10148;</span>
      <span class="field-label">Arrangements (Paying or Non-Paying, Others):</span>
      <div class="field-line" style="flex: 1;"><?= htmlspecialchars($arrangements) ?></div>
    </div>

    <div class="part-item">
      <span class="part-bullet">&#10148;</span>
      <span class="field-label">Remarks:</span>
      <div class="field-line" style="flex: 1;"><?= htmlspecialchars($remarks) ?></div>
    </div>

    <!-- Client Signature -->
    <div class="client-sig-box">
      <div class="client-sig-line"><?= htmlspecialchars($clientName) ?></div>
      <div class="client-sig-caption">(Printed Name &amp; Signature of Client/Project Officer)</div>
    </div>

    <!-- Approvals -->
    <div class="approval-row">
      <div class="approval-col">
        <div class="approval-head">RECOMMENDING APPROVAL:</div>
        <div class="approval-sig-line">Dormitory Manager</div>
      </div>
      <div class="approval-col">
        <div class="approval-head" style="text-align: right; padding-right: 20px;">APPROVED BY:</div>
        <div class="approval-sig-line">Authorized Representative</div>
      </div>
    </div>

    <div class="section-divider"></div>

    <!-- Rates and Terms & Conditions -->
    <div class="terms-heading">RATES AND TERMS &amp; CONDITIONS</div>

    <ol class="terms-ol">
      <li>Office hours are from 8:00 am to 10:00 pm after 10pm no transaction is allowed.</li>
      <li>The Requesting Party shall shoulder the following prescribed room rates kindly check your preferred room:</li>
      <li style="list-style: none; margin-left: -14px;">
        <div class="room-rates-subgroup">
          <span class="rate-cb-item"><span class="cb-square"></span> <strong>Regular Room:</strong> &#8369;600.00 per head per day 3 to 4 pax in a room without towel and toiletries</span>
          <span class="rate-cb-item"><span class="cb-square"></span> <strong>Regular Room:</strong> &#8369;700.00 per head per day 3 to 4 pax in a room with towel and toiletries</span>
          <span class="rate-cb-item"><span class="cb-square"></span> <strong>Regular Room:</strong> &#8369;1,200.00 per room for single occupancy with towel and toiletries</span>
          <span class="rate-cb-item"><span class="cb-square"></span> <strong>Executive Room:</strong> &#8369;1,500.00 per room/day maximum of 2 occupancies</span>
          <span class="rate-cb-item"><span class="cb-square"></span> <strong>Dormitory room:</strong> &#8369;500.00 per head per day 4 to 6 pax in a room without towel and toiletries</span>
          <span class="rate-cb-item"><span class="cb-square"></span> <strong>Dormitory room:</strong> &#8369;500.00 per head per day 4 to 6 pax in a room with towel and toiletries</span>
        </div>
      </li>
      <li>Request/s for accommodation shall be addressed to the ATI Director, Attention: Chief, General Services. Only approved request/s by the ATI Director accompanied by list of participants, number of participants indicating gender, purpose, duration of stay and room preference/s will be entertained. Discounts are discouraged except to PWD, Senior Citizens.</li>
      <li>Reservations shall be made at least one (1) month ahead of scheduled date and a down payment of 50% of the total cost is required for confirmation two weeks before the activity date, otherwise the reservation shall automatically be cancelled. If reservation is cancelled a week before the scheduled activity date only 50% of the down payment paid will be refunded. Requesting party should pay the reserved room even the guest did not arrive</li>
      <li>Guests shall register at the security guard stationed at the Main Entrance RDEC Building (Firearms and other deadly weapons are strictly not allowed and must be deposited to the guard for safe keeping)</li>
      <li>Guests shall fill-up the prescribed Registration Form at the RDEC Dormitory Managers Office.</li>
      <li>Guests shall pay on or before they check out directly to the ATI Cashier or Designated Collecting Officer, who shall issue the corresponding Official Receipt/s.</li>
      <li>After settlement of payment, Official Receipt/s should be given to the Dormitory Manager for proper documentation and room assignment, Check-in Checklist Form must be signed by the guest at the conforme portion; ID must be presented and deposited in exchange of the key.</li>
      <li>For requesting parties coming from GOs, NGOs, LGUs, SUCs, whose payment shall be settled thru check, prior payment arrangement shall be coordinated with the Dormitory Manager, to be approved by the Director or his/her designated Representative.</li>
      <li>ATI reserves the right to inspect the rooms/facilities occupied by the guests prior to his/her departure/check out. Check-out Checklist Form must be signed by the Dormitory Manager for release/clearance. Any loss or damage to dormitory facilities shall be charged to the room occupant. Key must be surrendered in exchange of the ID deposited. Lost key must be paid before checking out. Lost key will be charged at &#8369;500.00 per piece.</li>
      <li>Equipment, furniture and other items found inside the room should not be removed from its designated places, unless otherwise allowed. Any damage/loss inside the facilities shall be charged to the occupant.</li>
      <li>ATI shall not be held liable for any loss of personal belongings and other valuables inside the room/facilities. Guests are advised to thoroughly check their valuables and personal belongings before checking out.</li>
      <li>Guest should only use the paid bed in deference to the other room occupants&rsquo; rooms are for sharing.</li>
      <li>Dormitory does not provide laundry services, washing of cloths are discouraged</li>
      <li>Damage property or stained linens/towels shall be settled by the guest/s before checking out.</li>
      <li>Check in time is 2:00 pm and Check-out time is 12:00 pm. (Extension of stay will be charge at &#8369;50.00 per hour with the maximum of two hours stay only, exceeding the maximum time will be considered as one-night stay and will be charged automatically.</li>
    </ol>

    <!-- Conforme -->
    <div class="conforme-section">
      <div class="conforme-label">CONFORME:</div>
      <div class="conforme-line"><?= htmlspecialchars($clientName) ?></div>
      <div class="conforme-caption">(Printed Name &amp; Signature of Client/ Project Officer)</div>
    </div>

    <!-- Official Form Code Footer -->
    <div class="form-official-footer">
      ATI-QF/DTS-22 Rev. 02 Effectivity Date: March 7, 2022
    </div>

  </div>

</body>
</html>
