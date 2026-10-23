<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();

// Check authentication
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    // 1. Fetch active facilities
    $facStmt = $db->query("SELECT id, name, type, status, maintenance_reason, est_resume_date FROM facilities WHERE deleted_at IS NULL ORDER BY display_order ASC, id ASC");
    $facilitiesList = $facStmt ? $facStmt->fetchAll(PDO::FETCH_ASSOC) : [];

    // 2. Fetch all reservations
    $resQuery = "SELECT r.*, 
                        f.name AS facility_name, 
                        f.type AS facility_type,
                        rm.room_number,
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
                 WHERE r.status NOT IN ('Rejected', 'Cancelled')
                   AND r.deleted_at IS NULL
                 ORDER BY r.start_date ASC";

    $stmt = $db->query($resQuery);
    $dbReservations = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    // 3. Fetch schedule blocks
    $blockStmt = $db->query("SELECT * FROM schedule_blocks ORDER BY start_date ASC");
    $dbBlocks = $blockStmt ? $blockStmt->fetchAll(PDO::FETCH_ASSOC) : [];

    $calendarEvents = [];

    // Process reservations with 3-stage governance and guesthouse/dormitory recognition
    foreach ($dbReservations as $row) {
        $st = $row['status'] ?? 'Pending';
        
        $isGuesthouse = ($row['facility_type'] === 'guesthouses') || str_starts_with($row['reference_no'] ?? '', 'ATI-GH');
        $isDorm = !$isGuesthouse && (($row['facility_type'] === 'dormitories' || $row['facility_type'] === 'dorms') || !empty($row['room_id']));
        $category = $isGuesthouse ? 'guesthouses' : ($isDorm ? 'dormitories' : 'halls');

        // Status classification aligned with 3-Stage Governance Workflow
        if (in_array($st, ['Approved', 'Completed'])) {
            $colorClass = 'status-cleared';
            $statusKey = 'cleared';
            $statusLabel = 'Approved & Confirmed (Master Schedule)';
        } elseif (in_array($st, ['Pending Clearance', 'Awaiting Clearance'])) {
            $colorClass = 'status-pending-clear';
            $statusKey = 'pending_clear';
            $statusLabel = 'Stage 3: Pending Directorate Clearance';
        } elseif (in_array($st, ['Pending Recommendation', 'Awaiting Recommendation'])) {
            $colorClass = 'status-pending-rec';
            $statusKey = 'pending_rec';
            $statusLabel = 'Stage 2: Pending Property Recommendation';
        } else {
            $colorClass = 'status-pending-endorsement';
            $statusKey = 'pending_endorsement';
            $statusLabel = 'Stage 1: Pending Division Endorsement';
        }

        $facName = $row['facility_name'] ?: ($isGuesthouse ? 'Guesthouse Villa' : ($isDorm ? 'ATI Dormitory' : 'General Facility'));
        if (!empty($row['room_number'])) {
            $facName .= $isGuesthouse ? " - {$row['room_number']}" : " (Room {$row['room_number']})";
        }

        $timeSlot = !empty($row['time_slot']) ? $row['time_slot'] : '08:00 AM - 05:00 PM';
        $applicantName = $row['applicant_name'] ?: 'Official Applicant';
        $agency = $row['applicant_agency'] ?: 'Department of Agriculture';

        // Venue Tag for event chip
        $venueTag = 'HALL';
        if ($isGuesthouse) {
            $venueTag = 'VILLA';
        } elseif ($isDorm) {
            $venueTag = 'DORM';
        } elseif (stripos($facName, 'boardroom') !== false) {
            $venueTag = 'BOARD';
        } elseif (stripos($facName, 'training') !== false) {
            $venueTag = 'TRAIN';
        } elseif (stripos($facName, 'mess') !== false) {
            $venueTag = 'MESS';
        }

        $calendarEvents[] = [
            'ref' => $row['reference_no'],
            'title' => !empty($row['event_title']) ? $row['event_title'] : 'Official Activity',
            'facility' => (string)$row['facility_id'],
            'facilityName' => $facName,
            'facilityType' => $row['facility_type'] ?? 'halls',
            'category' => $category,
            'isGuesthouse' => $isGuesthouse,
            'isDorm' => $isDorm,
            'startDate' => $row['start_date'],
            'endDate' => !empty($row['end_date']) ? $row['end_date'] : $row['start_date'],
            'time' => $timeSlot,
            'applicant' => $applicantName,
            'agency' => $agency,
            'email' => $row['applicant_email'] ?: '',
            'phone' => $row['applicant_phone'] ?: '',
            'pax' => (int)($row['pax_count'] ?? 0),
            'rawStatus' => $st,
            'status' => $statusKey,
            'statusLabel' => $statusLabel,
            'colorClass' => $colorClass,
            'venueTag' => $venueTag,
            'stage1Reviewer' => !empty($row['endorsed_by_name']) ? "Endorsed by {$row['endorsed_by_name']}" : 'Pending Endorsement',
            'stage2Reviewer' => !empty($row['recommended_by_name']) ? "Recommended by {$row['recommended_by_name']}" : 'Pending Recommendation',
            'stage3Reviewer' => !empty($row['cleared_by_name']) ? "Cleared by {$row['cleared_by_name']}" : 'Pending Directorate Clearance'
        ];
    }

    // Process administrative blocks
    foreach ($dbBlocks as $blk) {
        $calendarEvents[] = [
            'ref' => $blk['ref'],
            'title' => $blk['title'],
            'facility' => (string)$blk['facility_id'],
            'facilityName' => $blk['facility_name'] ?: 'All ATI Facilities (Blackout)',
            'facilityType' => 'all',
            'category' => 'all',
            'isGuesthouse' => false,
            'isDorm' => false,
            'startDate' => $blk['start_date'],
            'endDate' => !empty($blk['end_date']) ? $blk['end_date'] : $blk['start_date'],
            'time' => 'Full Day Administrative Hold',
            'applicant' => 'Executive Directorate',
            'agency' => 'Office of the Director / Admin Services',
            'email' => 'admin@ati.da.gov.ph',
            'phone' => 'Loc 101',
            'pax' => 0,
            'rawStatus' => 'Blackout',
            'status' => 'blackout',
            'statusLabel' => 'Administrative Hold (' . ($blk['reason'] ?: 'Hold') . ')',
            'colorClass' => 'status-blackout',
            'venueTag' => 'HOLD',
            'stage1Reviewer' => !empty($blk['memo_ref']) ? "Memo: {$blk['memo_ref']}" : 'Executive Administrative Order',
            'stage2Reviewer' => 'Logistics Hold Verification',
            'stage3Reviewer' => 'Official Calendar Blackout'
        ];
    }

    // Process facilities with active maintenance hold
    foreach ($facilitiesList as $fac) {
        if ($fac['status'] === 'maintenance') {
            $calendarEvents[] = [
                'ref' => 'MAINT-FAC-' . $fac['id'],
                'title' => 'Maintenance Hold: ' . $fac['name'],
                'facility' => (string)$fac['id'],
                'facilityName' => $fac['name'],
                'facilityType' => $fac['type'] ?? 'halls',
                'category' => ($fac['type'] === 'guesthouses') ? 'guesthouses' : (($fac['type'] === 'dorms' || $fac['type'] === 'dormitories') ? 'dormitories' : 'halls'),
                'isGuesthouse' => ($fac['type'] === 'guesthouses'),
                'isDorm' => ($fac['type'] === 'dorms' || $fac['type'] === 'dormitories'),
                'startDate' => date('Y-m-01'),
                'endDate' => !empty($fac['est_resume_date']) ? $fac['est_resume_date'] : date('Y-m-t'),
                'time' => 'Facility Under Maintenance',
                'applicant' => 'General Services Unit',
                'agency' => 'Administrative Services',
                'email' => 'gsu@ati.da.gov.ph',
                'phone' => 'Loc 104',
                'pax' => 0,
                'rawStatus' => 'Maintenance',
                'status' => 'blackout',
                'statusLabel' => 'Facility Maintenance Hold (' . ($fac['maintenance_reason'] ?: 'Scheduled Servicing') . ')',
                'colorClass' => 'status-blackout',
                'venueTag' => 'MAINT',
                'stage1Reviewer' => 'Facility Maintenance Directive',
                'stage2Reviewer' => 'General Services Unit Inspection',
                'stage3Reviewer' => 'Logistics Maintenance Authorization'
            ];
        }
    }

    $hash = md5(json_encode($calendarEvents));

    echo json_encode([
        'success' => true,
        'hash' => $hash,
        'events' => $calendarEvents,
        'total_events' => count($calendarEvents)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error querying schedule events: ' . $e->getMessage()
    ]);
}
