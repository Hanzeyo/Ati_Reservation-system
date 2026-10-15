<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();

    $facility_name = trim($_GET['facility_name'] ?? '');
    $rawFacId = $_GET['facility_id'] ?? 0;
    $facility_id = 0;
    if (is_numeric($rawFacId)) {
        $facility_id = (int)$rawFacId;
    } elseif (is_string($rawFacId) && preg_match('/(\d+)/', $rawFacId, $m)) {
        $facility_id = (int)$m[1];
    }

    $room_number = isset($_GET['room_number']) ? trim($_GET['room_number']) : null;
    $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n'); // 1-12

    // Resolve facility_id if facility_name is passed
    if (!$facility_id && !empty($facility_name)) {
        $stmt = $conn->prepare("SELECT id FROM facilities WHERE name = ? LIMIT 1");
        $stmt->execute([$facility_name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $facility_id = (int)$row['id'];
        }
    }

    // Resolve room_id if room_number is provided
    $room_id = null;
    if ($facility_id && !empty($room_number) && $room_number !== 'null') {
        $stmt = $conn->prepare("SELECT id FROM rooms WHERE facility_id = ? AND room_number = ? LIMIT 1");
        $stmt->execute([$facility_id, $room_number]);
        $rRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rRow) {
            $room_id = (int)$rRow['id'];
        }
    }

    $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
    $monthStart = "{$year}-{$monthPadded}-01";
    $monthEnd = date("Y-m-t", strtotime($monthStart));

    // Check if facility is a dormitory and count active rooms
    $isDormFacility = false;
    $totalDormRooms = 0;
    if ($facility_id > 0 && $room_id === null) {
        $fTypeStmt = $conn->prepare("SELECT type FROM facilities WHERE id = ? LIMIT 1");
        $fTypeStmt->execute([$facility_id]);
        $fTypeRow = $fTypeStmt->fetch(PDO::FETCH_ASSOC);
        if ($fTypeRow && in_array(strtolower($fTypeRow['type']), ['dorms', 'dormitories'])) {
            $isDormFacility = true;
            $rmCountStmt = $conn->prepare("SELECT COUNT(*) FROM rooms WHERE facility_id = ? AND status != 'maintenance'");
            $rmCountStmt->execute([$facility_id]);
            $totalDormRooms = (int)$rmCountStmt->fetchColumn();
        }
    }

    // =========================================================================
    // 1. QUERY RESERVED DATES (Approved / Active Reservations)
    // =========================================================================
    $query = "SELECT r.id, r.event_title, r.start_date, r.end_date, r.time_slot,
                     f.name AS facility_name, rm.room_number
              FROM reservations r
              LEFT JOIN facilities f ON r.facility_id = f.id
              LEFT JOIN rooms rm ON r.room_id = rm.id
              WHERE r.status IN ('Approved', 'Completed')
                AND (r.start_date <= :monthEnd AND r.end_date >= :monthStart)";

    $params = [
        ':monthStart' => $monthStart,
        ':monthEnd' => $monthEnd
    ];

    if ($facility_id > 0) {
        $query .= " AND r.facility_id = :facility_id";
        $params[':facility_id'] = $facility_id;
    }

    if ($room_id !== null) {
        $query .= " AND r.room_id = :room_id";
        $params[':room_id'] = $room_id;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build map of booked dates (YYYY-MM-DD -> array of event titles)
    $rawBookedDates = [];
    foreach ($reservations as $res) {
        $start = new DateTime(max($res['start_date'], $monthStart));
        $end = new DateTime(min($res['end_date'], $monthEnd));

        $curr = clone $start;
        while ($curr <= $end) {
            $dKey = $curr->format('Y-m-d');
            if (!isset($rawBookedDates[$dKey])) {
                $rawBookedDates[$dKey] = [];
            }
            $rawBookedDates[$dKey][] = [
                'title' => $res['event_title'] ?: 'Official Activity',
                'facility' => $res['facility_name'],
                'room' => $res['room_number']
            ];
            $curr->modify('+1 day');
        }
    }

    // If dorm facility without specific room, only mark as booked if ALL rooms are reserved
    $bookedDates = [];
    if ($isDormFacility && $totalDormRooms > 0) {
        foreach ($rawBookedDates as $dKey => $events) {
            if (count($events) >= $totalDormRooms) {
                $bookedDates[$dKey] = $events;
            }
        }
    } else {
        $bookedDates = $rawBookedDates;
    }

    // =========================================================================
    // 1B. QUERY PENDING RESERVATIONS (Submitted Requests Awaiting Approval)
    // =========================================================================
    $pQuery = "SELECT r.id, r.event_title, r.start_date, r.end_date, r.time_slot,
                      f.name AS facility_name, rm.room_number, r.status
               FROM reservations r
               LEFT JOIN facilities f ON r.facility_id = f.id
               LEFT JOIN rooms rm ON r.room_id = rm.id
               WHERE r.status IN ('Pending', 'Pending Review', 'Under Review', 'Endorsed', 'Recommended')
                 AND (r.start_date <= :pMonthEnd AND r.end_date >= :pMonthStart)";

    $pParams = [
        ':pMonthStart' => $monthStart,
        ':pMonthEnd' => $monthEnd
    ];

    if ($facility_id > 0) {
        $pQuery .= " AND r.facility_id = :p_facility_id";
        $pParams[':p_facility_id'] = $facility_id;
    }

    if ($room_id !== null) {
        $pQuery .= " AND r.room_id = :p_room_id";
        $pParams[':p_room_id'] = $room_id;
    }

    $pStmt = $conn->prepare($pQuery);
    $pStmt->execute($pParams);
    $pendingReservations = $pStmt->fetchAll(PDO::FETCH_ASSOC);

    $pendingDates = [];
    foreach ($pendingReservations as $pRes) {
        $pStart = new DateTime(max($pRes['start_date'], $monthStart));
        $pEnd = new DateTime(min($pRes['end_date'], $monthEnd));
        $pCurr = clone $pStart;
        while ($pCurr <= $pEnd) {
            $pKey = $pCurr->format('Y-m-d');
            // If the date is already officially booked/approved, booked status takes priority
            if (!isset($bookedDates[$pKey])) {
                if (!isset($pendingDates[$pKey])) {
                    $pendingDates[$pKey] = [];
                }
                $pendingDates[$pKey][] = [
                    'id' => $pRes['id'],
                    'title' => $pRes['event_title'] ?: 'Pending Reservation Request',
                    'facility' => $pRes['facility_name'],
                    'room' => $pRes['room_number'],
                    'status' => $pRes['status']
                ];
            }
            $pCurr->modify('+1 day');
        }
    }

    // =========================================================================
    // 2. QUERY SUSPENDED DATES (Administrative Holds, Holidays, Maintenance)
    // =========================================================================
    $suspendedDates = [];

    // A. Administrative schedule_blocks (Hold / Blackouts)
    try {
        $bQuery = "SELECT * FROM schedule_blocks 
                   WHERE (facility_id = 'all' OR facility_id = :fac_str)
                     AND (start_date <= :bMonthEnd AND end_date >= :bMonthStart)";
        $bStmt = $conn->prepare($bQuery);
        $bStmt->execute([
            ':fac_str' => (string)$facility_id,
            ':bMonthEnd' => $monthEnd,
            ':bMonthStart' => $monthStart
        ]);
        $blocks = $bStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($blocks as $blk) {
            $bStart = new DateTime(max($blk['start_date'], $monthStart));
            $bEnd = new DateTime(min($blk['end_date'], $monthEnd));
            $bCurr = clone $bStart;
            while ($bCurr <= $bEnd) {
                $bKey = $bCurr->format('Y-m-d');
                $suspendedDates[$bKey] = [
                    'title' => $blk['title'],
                    'reason' => !empty($blk['reason']) ? $blk['reason'] : $blk['title'],
                    'facility' => $blk['facility_name'] ?: 'All Facilities',
                    'is_hold' => true
                ];
                $bCurr->modify('+1 day');
            }
        }
    } catch (Exception $e) {
        // Table may be absent in edge environments
    }

    // B. Facility-level active maintenance hold
    if ($facility_id > 0) {
        try {
            $fStmt = $conn->prepare("SELECT status, maintenance_reason, est_resume_date FROM facilities WHERE id = ?");
            $fStmt->execute([$facility_id]);
            $facRow = $fStmt->fetch(PDO::FETCH_ASSOC);
            if ($facRow && $facRow['status'] === 'maintenance') {
                $mReason = !empty($facRow['maintenance_reason']) ? $facRow['maintenance_reason'] : 'Scheduled Facility Maintenance';
                $todayStr = date('Y-m-d');
                $mStartStr = max($todayStr, $monthStart);
                $mEndStr = !empty($facRow['est_resume_date']) ? min($facRow['est_resume_date'], $monthEnd) : $monthEnd;
                
                if ($mStartStr <= $mEndStr) {
                    $mStart = new DateTime($mStartStr);
                    $mEnd = new DateTime($mEndStr);
                    $mCurr = clone $mStart;
                    while ($mCurr <= $mEnd) {
                        $mKey = $mCurr->format('Y-m-d');
                        if (!isset($suspendedDates[$mKey])) {
                            $suspendedDates[$mKey] = [
                                'title' => 'Facility Maintenance Hold',
                                'reason' => $mReason,
                                'facility' => $facility_name,
                                'is_hold' => true
                            ];
                        }
                        $mCurr->modify('+1 day');
                    }
                }
            }
        } catch (Exception $e) {}
    }

    // C. Room-level active maintenance hold (if room selected)
    if ($room_id !== null && $room_id > 0) {
        try {
            $rStmt = $conn->prepare("SELECT status, room_number FROM rooms WHERE id = ?");
            $rStmt->execute([$room_id]);
            $rmRow = $rStmt->fetch(PDO::FETCH_ASSOC);
            if ($rmRow && $rmRow['status'] === 'maintenance') {
                $todayStr = date('Y-m-d');
                $rmStart = new DateTime(max($todayStr, $monthStart));
                $rmEnd = new DateTime($monthEnd);
                $rmCurr = clone $rmStart;
                while ($rmCurr <= $rmEnd) {
                    $rmKey = $rmCurr->format('Y-m-d');
                    if (!isset($suspendedDates[$rmKey])) {
                        $suspendedDates[$rmKey] = [
                            'title' => "Room {$rmRow['room_number']} Maintenance",
                            'reason' => "Room {$rmRow['room_number']} is under administrative maintenance hold",
                            'facility' => $facility_name,
                            'is_hold' => true
                        ];
                    }
                    $rmCurr->modify('+1 day');
                }
            }
        } catch (Exception $e) {}
    }

    echo json_encode([
        'status' => 'success',
        'facility_id' => $facility_id,
        'room_id' => $room_id,
        'year' => $year,
        'month' => $month,
        'booked_dates' => array_keys($bookedDates),
        'booked_details' => $bookedDates,
        'pending_dates' => array_keys($pendingDates),
        'pending_details' => $pendingDates,
        'suspended_dates' => array_keys($suspendedDates),
        'suspended_details' => $suspendedDates
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'booked_dates' => [],
        'booked_details' => [],
        'pending_dates' => [],
        'pending_details' => [],
        'suspended_dates' => [],
        'suspended_details' => []
    ]);
}
