<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

try {
    $db = new Database();
    $conn = $db->getConnection();

    $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n'); // 1-12
    if ($month < 1 || $month > 12) {
        $month = (int)date('n');
    }
    if ($year < 2000 || $year > 2100) {
        $year = (int)date('Y');
    }

    $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
    $monthStart = "{$year}-{$monthPadded}-01";
    $monthEnd = date("Y-m-t", strtotime($monthStart));

    // 1. Fetch active facilities (Halls priority, then dorms)
    $facStmt = $conn->prepare("SELECT id, name, type, capacity 
                               FROM facilities 
                               WHERE deleted_at IS NULL 
                               ORDER BY FIELD(type, 'halls', 'dorms'), id ASC");
    $facStmt->execute();
    $facilitiesList = $facStmt->fetchAll(PDO::FETCH_ASSOC);

    // Format facility names nicely for display
    $facilitiesMap = [];
    $formattedFacilities = [];
    foreach ($facilitiesList as $fac) {
        $fId = (int)$fac['id'];
        // Normalize name: title case, clean up abbreviations
        $rawName = trim($fac['name']);
        $cleanName = ucwords(strtolower($rawName));
        if (stripos($cleanName, 'rdec') !== false) {
            $cleanName = preg_replace_callback('/\brdec\b/i', fn() => 'RDEC', $cleanName);
        }
        if (stripos($cleanName, 'vip') !== false) {
            $cleanName = preg_replace_callback('/\bvip\b/i', fn() => 'VIP', $cleanName);
        }

        $facilityItem = [
            'id' => $fId,
            'name' => $cleanName,
            'type' => $fac['type'],
            'capacity' => (int)$fac['capacity']
        ];
        $facilitiesMap[$fId] = $facilityItem;
        $formattedFacilities[] = $facilityItem;
    }

    // 2. Fetch all active reservations for the month
    $resQuery = "SELECT r.id, r.reference_no, r.event_title, r.start_date, r.end_date, 
                        r.time_slot, r.pax_count, r.status, r.facility_id, r.room_id,
                        f.name AS facility_name
                 FROM reservations r
                 LEFT JOIN facilities f ON r.facility_id = f.id
                 WHERE r.deleted_at IS NULL
                   AND r.status NOT IN ('Cancelled', 'Rejected')
                   AND (r.start_date <= :monthEnd AND r.end_date >= :monthStart)
                 ORDER BY r.start_date ASC, r.id ASC";
    $resStmt = $conn->prepare($resQuery);
    $resStmt->execute([
        ':monthStart' => $monthStart,
        ':monthEnd' => $monthEnd
    ]);
    $reservations = $resStmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch schedule blocks / administrative suspensions
    $blocks = [];
    try {
        $blkStmt = $conn->prepare("SELECT * FROM schedule_blocks 
                                   WHERE (start_date <= :bMonthEnd AND end_date >= :bMonthStart)");
        $blkStmt->execute([
            ':bMonthStart' => $monthStart,
            ':bMonthEnd' => $monthEnd
        ]);
        $blocks = $blkStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $blocks = [];
    }

    // 4. Build availability data for each day of the month
    $totalDays = (int)date('t', strtotime($monthStart));
    $daysData = [];

    for ($d = 1; $d <= $totalDays; $d++) {
        $dayPadded = str_pad($d, 2, '0', STR_PAD_LEFT);
        $dateStr = "{$year}-{$monthPadded}-{$dayPadded}";

        // Default state for each facility on this day
        $facStatuses = [];
        foreach ($facilitiesMap as $fId => $fac) {
            $facStatuses[$fId] = [
                'facility_id' => $fId,
                'facility_name' => $fac['name'],
                'facility_type' => $fac['type'],
                'status' => 'available',
                'status_label' => 'AVAILABLE',
                'details' => 'Available All Day',
                'event_title' => null,
                'time_slot' => null,
                'ref' => null
            ];
        }

        // Apply reservations for this date
        foreach ($reservations as $res) {
            $rStart = $res['start_date'];
            $rEnd = !empty($res['end_date']) ? $res['end_date'] : $rStart;

            if ($dateStr >= $rStart && $dateStr <= $rEnd) {
                $fId = (int)$res['facility_id'];
                if (isset($facStatuses[$fId])) {
                    $rawStatus = trim($res['status']);
                    $timeStr = !empty($res['time_slot']) ? $res['time_slot'] : 'Whole Day';
                    $titleStr = !empty($res['event_title']) ? $res['event_title'] : 'Official Activity';

                    $isApproved = in_array($rawStatus, ['Approved', 'Completed']);
                    $isPending = !$isApproved && (stripos($rawStatus, 'pending') !== false || in_array($rawStatus, ['Under Review', 'Endorsed', 'Recommended']));

                    if ($isApproved) {
                        $facStatuses[$fId]['status'] = 'reserved';
                        $facStatuses[$fId]['status_label'] = 'RESERVED';
                        $facStatuses[$fId]['details'] = "Reserved • {$timeStr}";
                        $facStatuses[$fId]['event_title'] = $titleStr;
                        $facStatuses[$fId]['time_slot'] = $timeStr;
                        $facStatuses[$fId]['ref'] = $res['reference_no'];
                    } elseif ($isPending && $facStatuses[$fId]['status'] !== 'reserved') {
                        // Only set to pending if not already reserved by an approved booking
                        $facStatuses[$fId]['status'] = 'pending';
                        $facStatuses[$fId]['status_label'] = 'PENDING';
                        $facStatuses[$fId]['details'] = "Pending Approval • {$timeStr}";
                        $facStatuses[$fId]['event_title'] = $titleStr;
                        $facStatuses[$fId]['time_slot'] = $timeStr;
                        $facStatuses[$fId]['ref'] = $res['reference_no'];
                    }
                }
            }
        }

        // Apply schedule blocks / administrative holds
        foreach ($blocks as $blk) {
            $bStart = $blk['start_date'];
            $bEnd = !empty($blk['end_date']) ? $blk['end_date'] : $bStart;

            if ($dateStr >= $bStart && $dateStr <= $bEnd) {
                $targetFac = $blk['facility_id'];
                $blockReason = !empty($blk['reason']) ? $blk['reason'] : 'Maintenance / Closed';

                if ($targetFac === 'all' || empty($targetFac)) {
                    foreach ($facStatuses as $fId => &$fState) {
                        $fState['status'] = 'suspended';
                        $fState['status_label'] = 'SUSPENDED';
                        $fState['details'] = $blockReason;
                    }
                    unset($fState);
                } elseif (isset($facStatuses[(int)$targetFac])) {
                    $facStatuses[(int)$targetFac]['status'] = 'suspended';
                    $facStatuses[(int)$targetFac]['status_label'] = 'SUSPENDED';
                    $facStatuses[(int)$targetFac]['details'] = $blockReason;
                }
            }
        }

        // Compute overall day status for the calendar dot
        $hasReserved = false;
        $hasPending = false;
        $hasSuspended = false;

        foreach ($facStatuses as $fState) {
            if ($fState['status'] === 'reserved') $hasReserved = true;
            elseif ($fState['status'] === 'pending') $hasPending = true;
            elseif ($fState['status'] === 'suspended') $hasSuspended = true;
        }

        $overallStatus = 'available';
        if ($hasReserved) {
            $overallStatus = 'reserved';
        } elseif ($hasPending) {
            $overallStatus = 'pending';
        } elseif ($hasSuspended) {
            $overallStatus = 'suspended';
        }

        $daysData[$dateStr] = [
            'date' => $dateStr,
            'day' => $d,
            'overall_status' => $overallStatus,
            'facilities' => array_values($facStatuses)
        ];
    }

    echo json_encode([
        'status' => 'success',
        'year' => $year,
        'month' => $month,
        'facilities' => $formattedFacilities,
        'days' => $daysData
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
