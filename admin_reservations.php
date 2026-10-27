<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator Executive Dashboard (Sidebar Layout - Terno with ATI Portal)
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

// Fetch all reservations joined with facilities, rooms, and applicants
$resQuery = "SELECT r.*, 
                    f.name AS facility_name, 
                    f.type AS facility_type,
                    f.rate AS facility_rate,
                    f.capacity AS facility_capacity,
                    rm.room_number,
                    rm.floor AS room_floor,
                    u.full_name AS applicant_name,
                    u.email AS applicant_email,
                    u.contact_number AS applicant_phone,
                    u.office_agency AS applicant_agency,
                    u_end.full_name AS endorser_name,
                    u_rec.full_name AS recommender_name,
                    u_clr.full_name AS clearer_name,
                    u_chk.full_name AS checked_in_by_name
             FROM reservations r
             LEFT JOIN facilities f ON r.facility_id = f.id
             LEFT JOIN rooms rm ON r.room_id = rm.id
             LEFT JOIN users u ON r.user_id = u.id
             LEFT JOIN users u_end ON r.endorsed_by = u_end.id
             LEFT JOIN users u_rec ON r.recommended_by = u_rec.id
             LEFT JOIN users u_clr ON r.cleared_by = u_clr.id
             LEFT JOIN users u_chk ON r.check_in_by = u_chk.id
             ORDER BY r.id DESC";

$stmt = $db->query($resQuery);
$allReservations = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

if (!function_exists('calculateReservationUtilizationDetails')) {
    /**
     * Calculates dynamic estimated monetary utilization value and breakdown details
     * for an ATI reservation based on official facility rates, booked duration, time slot factor, and headcount.
     */
    function calculateReservationUtilizationDetails(array $res): array {
        $rawRate = $res['facility_rate'] ?? '';
        $cleanRate = (float) preg_replace('/[^0-9.]/', '', (string)$rawRate);

        $isGuesthouse = (($res['facility_type'] ?? '') === 'guesthouses' 
                         || ($res['facility_type'] ?? '') === 'guesthouse' 
                         || str_starts_with($res['reference_no'] ?? '', 'ATI-GH') 
                         || stripos($res['facility_name'] ?? '', 'guesthouse') !== false);
        $isDorm = !$isGuesthouse && (($res['facility_type'] ?? '') === 'dormitories' || ($res['facility_type'] ?? '') === 'dorms' || !empty($res['room_number']));

        // Institutional baseline standard fallback if rate is missing
        if ($cleanRate <= 0) {
            $cleanRate = $isGuesthouse ? 1800.0 : ($isDorm ? 500.0 : 5000.0);
        }

        $startTs = !empty($res['start_date']) ? strtotime($res['start_date']) : time();
        $endTs = !empty($res['end_date']) ? strtotime($res['end_date']) : $startTs;
        $days = max(1, round(($endTs - $startTs) / 86400) + 1);

        if ($isGuesthouse) {
            $nights = max(1, $days - 1);
            $val = round($cleanRate * $nights, 2);
            $category = 'guesthouse';
            $categoryLabel = 'GuestHouse';
            $rateDisplay = '₱' . number_format($cleanRate) . ' / night';
            $durationStr = $nights . ($nights > 1 ? ' Nights' : ' Night');
            $formulaStr = $nights . ' night' . ($nights > 1 ? 's' : '') . ' × ₱' . number_format($cleanRate);
        } elseif ($isDorm) {
            $nights = max(1, $days - 1);
            $pax = max(1, (int)($res['pax_count'] ?? 1));
            $val = $cleanRate <= 1000 ? round($cleanRate * $nights * $pax, 2) : round($cleanRate * $nights, 2);
            $category = 'dorm';
            $categoryLabel = 'Dormitory Suite';
            $rateDisplay = '₱' . number_format($cleanRate) . ' / night';
            $durationStr = $nights . ($nights > 1 ? ' Nights' : ' Night') . ' (' . $pax . ' PAX)';
            $formulaStr = $cleanRate <= 1000 
                ? ($nights . ' night' . ($nights > 1 ? 's' : '') . ' × ' . $pax . ' beds × ₱' . number_format($cleanRate))
                : ($nights . ' night' . ($nights > 1 ? 's' : '') . ' × ₱' . number_format($cleanRate));
        } else {
            $slot = strtolower($res['time_slot'] ?? '');
            $isHalfDay = (strpos($slot, 'half') !== false || strpos($slot, 'morning') !== false || strpos($slot, 'afternoon') !== false) 
                         && (strpos($slot, 'full') === false);
            $multiplier = $isHalfDay ? 0.5 : 1.0;
            $val = round($cleanRate * $days * $multiplier, 2);
            $category = 'hall';
            $categoryLabel = 'Function Hall';
            $rateDisplay = '₱' . number_format($cleanRate) . ' / day';
            $durationStr = $days . ($days > 1 ? ' Days' : ' Day') . ($isHalfDay ? ' (Half Day)' : '');
            $formulaStr = $days . ' day' . ($days > 1 ? 's' : '') . ($isHalfDay ? ' × 0.5' : '') . ' × ₱' . number_format($cleanRate);
        }

        return [
            'value' => $val,
            'cleanRate' => $cleanRate,
            'category' => $category,
            'categoryLabel' => $categoryLabel,
            'rateDisplay' => $rateDisplay,
            'durationStr' => $durationStr,
            'formulaStr' => $formulaStr
        ];
    }
}

if (!function_exists('calculateReservationUtilization')) {
    function calculateReservationUtilization(array $res): float {
        $details = calculateReservationUtilizationDetails($res);
        return $details['value'];
    }
}

$totalActiveReservations = 0;
$pendingReservations = 0;
$approvedReservations = 0;
$checkedInReservations = 0;
$noShowReservations = 0;
$completedReservations = 0;
$hallsCount = 0;
$dormsCount = 0;
$guesthousesCount = 0;
$archivedReservationsCount = 0;
$totalEstimatedUtilization = 0;
$confirmedUtilization = 0;
$totalPaxServed = 0;
$utilizationHalls = 0;
$utilizationDorms = 0;
$utilizationGuesthouses = 0;
$utilizationReservationsList = [];

$reservationsData = [];

foreach ($allReservations as $row) {
    $isArchived = !empty($row['deleted_at']);
    $st = $row['status'] ?? 'Pending';
    $isPending = in_array($st, ['Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance']);
    $isApproved = ($st === 'Approved');
    $isCheckedIn = ($st === 'Checked In');
    $isNoShow = ($st === 'No-Show');
    $isCompleted = ($st === 'Completed');
    $isRejected = in_array($st, ['Rejected', 'Cancelled']);

    $utilInfo = calculateReservationUtilizationDetails($row);
    $resUtil = $utilInfo['value'];

    if ($isArchived) {
        $archivedReservationsCount++;
    } else {
        $totalActiveReservations++;
        $totalEstimatedUtilization += $resUtil;

        if ($utilInfo['category'] === 'guesthouse') {
            $utilizationGuesthouses += $resUtil;
            $guesthousesCount++;
        } elseif ($utilInfo['category'] === 'dorm') {
            $utilizationDorms += $resUtil;
            $dormsCount++;
        } else {
            $utilizationHalls += $resUtil;
            $hallsCount++;
        }

        if ($isPending) $pendingReservations++;
        if ($isApproved) {
            $approvedReservations++;
            $confirmedUtilization += $resUtil;
            $totalPaxServed += (int)($row['pax_count'] ?? 0);
        }
        if ($isCheckedIn) {
            $checkedInReservations++;
            $confirmedUtilization += $resUtil;
            $totalPaxServed += (int)($row['pax_count'] ?? 0);
        }
        if ($isNoShow) {
            $noShowReservations++;
        }
        if ($isCompleted) {
            $completedReservations++;
            $confirmedUtilization += $resUtil;
            $totalPaxServed += (int)($row['pax_count'] ?? 0);
        }
    }

    $ref = $row['reference_no'];
    $startDateTimestamp = strtotime($row['start_date']);
    $startDateStr = date('M d, Y', $startDateTimestamp);
    if (!empty($row['end_date']) && $row['end_date'] !== $row['start_date']) {
        $datesStr = date('M d', $startDateTimestamp) . ' – ' . date('M d, Y', strtotime($row['end_date']));
    } else {
        $datesStr = $startDateStr;
    }

    $venueDisplay = $row['facility_name'] ?: 'Official Facility';
    if (!empty($row['room_number'])) {
        $venueDisplay .= ' (Room ' . $row['room_number'] . ')';
    }

    $isStage1 = in_array($st, ['Pending', 'Pending Endorsement', 'Awaiting Endorsement']);
    $isStage2 = in_array($st, ['Pending Recommendation', 'Awaiting Recommendation']);
    $isStage3 = in_array($st, ['Pending Clearance', 'Awaiting Clearance']);
    $stageKey = $isCompleted ? 'completed' : ($isCheckedIn ? 'checked_in' : ($isNoShow ? 'no_show' : ($isApproved ? 'approved' : ($isRejected ? 'rejected' : ($isStage1 ? 'stage1' : ($isStage2 ? 'stage2' : 'stage3'))))));

    $normalizedStatus = $isCompleted ? 'completed' : ($isCheckedIn ? 'checked_in' : ($isNoShow ? 'no_show' : ($isApproved ? 'approved' : ($isRejected ? 'rejected' : 'pending'))));

    $reservationsData[$ref] = [
        'id' => (int)$row['id'],
        'ref' => $ref,
        'applicant' => !empty($row['applicant_name']) ? $row['applicant_name'] : 'Official Guest',
        'agency' => !empty($row['applicant_agency']) ? $row['applicant_agency'] : 'ATI Central Office',
        'email' => !empty($row['applicant_email']) ? $row['applicant_email'] : 'N/A',
        'phone' => !empty($row['applicant_phone']) ? $row['applicant_phone'] : 'N/A',
        'venue' => $venueDisplay,
        'schedule' => $datesStr . ' (' . (!empty($row['time_slot']) ? $row['time_slot'] : 'Full Day') . ')',
        'rawDates' => $datesStr,
        'timeSlot' => !empty($row['time_slot']) ? $row['time_slot'] : 'Full Day (8:00 AM – 5:00 PM)',
        'pax' => (int)($row['pax_count'] ?? 0) . ' PAX',
        'paxCount' => (int)($row['pax_count'] ?? 0),
        'eventTitle' => !empty($row['event_title']) ? $row['event_title'] : 'Official Institutional Activity',
        'equipment' => !empty($row['special_notes']) ? $row['special_notes'] : 'Standard Logistics & Venue Equipment',
        'documentPath' => $row['document_path'] ?? '',
        'recommendationDocPath' => $row['recommendation_doc_path'] ?? '',
        'clearanceDocPath' => $row['clearance_doc_path'] ?? '',
        'endorserName' => $row['endorser_name'] ?? '',
        'endorsedAt' => !empty($row['endorsed_at']) ? date('M d, Y • h:i A', strtotime($row['endorsed_at'])) : '',
        'endorsementRemarks' => $row['endorsement_remarks'] ?? '',
        'recommenderName' => $row['recommender_name'] ?? '',
        'recommendedAt' => !empty($row['recommended_at']) ? date('M d, Y • h:i A', strtotime($row['recommended_at'])) : '',
        'recommendationRemarks' => $row['recommendation_remarks'] ?? '',
        'clearerName' => $row['clearer_name'] ?? '',
        'clearedAt' => !empty($row['cleared_at']) ? date('M d, Y • h:i A', strtotime($row['cleared_at'])) : '',
        'clearanceRemarks' => $row['clearance_remarks'] ?? '',
        'checkInAt' => !empty($row['check_in_at']) ? date('M d, Y • h:i A', strtotime($row['check_in_at'])) : '',
        'checkInAtTime' => !empty($row['check_in_at']) ? date('h:i A', strtotime($row['check_in_at'])) : '',
        'checkedInByName' => $row['checked_in_by_name'] ?? '',
        'status' => $normalizedStatus,
        'stage' => $stageKey,
        'rawStatus' => $st,
        'category' => $utilInfo['category'] === 'guesthouse' ? 'guesthouses' : ($utilInfo['category'] === 'dorm' ? 'dorms' : 'halls'),
        'facilityRate' => $row['facility_rate'] ?? 'N/A',
        'estimatedValue' => $resUtil,
        'estimatedValueFormatted' => '₱' . number_format($resUtil),
        'dateFiled' => !empty($row['created_at']) ? date('M d, Y • h:i A', strtotime($row['created_at'])) : date('M d, Y • h:i A'),
        'isArchived' => $isArchived,
        'deletedAt' => !empty($row['deleted_at']) ? date('M d, Y • h:i A', strtotime($row['deleted_at'])) : null
    ];

    if (!$isArchived) {
        $utilizationReservationsList[] = [
            'ref' => $ref,
            'applicant' => !empty($row['applicant_name']) ? $row['applicant_name'] : 'Official Guest',
            'agency' => !empty($row['applicant_agency']) ? $row['applicant_agency'] : 'ATI Central Office',
            'venue' => $venueDisplay,
            'eventTitle' => !empty($row['event_title']) ? $row['event_title'] : 'Official Institutional Activity',
            'schedule' => $datesStr . ' (' . (!empty($row['time_slot']) ? $row['time_slot'] : 'Full Day') . ')',
            'status' => $st,
            'category' => $utilInfo['category'],
            'categoryLabel' => $utilInfo['categoryLabel'],
            'rateDisplay' => $utilInfo['rateDisplay'],
            'durationStr' => $utilInfo['durationStr'],
            'formulaStr' => $utilInfo['formulaStr'],
            'value' => $resUtil,
            'valueFormatted' => '₱' . number_format($resUtil)
        ];
    }
}
$totalReservations = $totalActiveReservations;
$occupancyRate = ($totalActiveReservations > 0) 
    ? round((($approvedReservations + $checkedInReservations + $completedReservations) / $totalActiveReservations) * 100, 1) 
    : 0;
$estimatedUtilization = number_format($totalEstimatedUtilization);
$hallsUtilPct = $totalEstimatedUtilization > 0 ? round(($utilizationHalls / $totalEstimatedUtilization) * 100, 1) : 0.0;
$dormsUtilPct = $totalEstimatedUtilization > 0 ? round(($utilizationDorms / $totalEstimatedUtilization) * 100, 1) : 0.0;
$ghUtilPct = $totalEstimatedUtilization > 0 ? round(($utilizationGuesthouses / $totalEstimatedUtilization) * 100, 1) : 0.0;

// Fetch User count for sidebar
$userCountStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
$totalUsersCount = $userCountStmt ? (int)$userCountStmt->fetchColumn() : 0;

// Fetch unique facilities for filter and live monitoring ordered by customized display sequence
$today = date('Y-m-d');
$facStmt = $db->query("SELECT * FROM facilities WHERE deleted_at IS NULL ORDER BY display_order ASC, id ASC");
$monitoredFacilities = $facStmt ? $facStmt->fetchAll(PDO::FETCH_ASSOC) : [];
$facilitiesList = $monitoredFacilities;

// Group facilities by official categories for filters
$hallsList = [];
$dormsList = [];
$guesthousesList = [];
foreach ($facilitiesList as $f) {
    $fType = strtolower($f['type'] ?? '');
    if ($fType === 'guesthouses') {
        $guesthousesList[] = $f;
    } elseif ($fType === 'dormitories' || $fType === 'dorms') {
        $dormsList[] = $f;
    } else {
        $hallsList[] = $f;
    }
}

// Fetch approved reservations covering today
$activeResStmt = $db->prepare("SELECT r.facility_id, r.room_id, r.reference_no, r.event_title, r.start_date, r.end_date, r.time_slot, u.full_name AS applicant_name 
                               FROM reservations r
                               LEFT JOIN users u ON r.user_id = u.id
                               WHERE r.status IN ('Approved', 'Completed') 
                                 AND r.start_date <= :today 
                                 AND (r.end_date >= :today OR r.end_date IS NULL)
                                 AND r.deleted_at IS NULL");
$activeResStmt->execute([':today' => $today]);
$activeTodayList = $activeResStmt->fetchAll(PDO::FETCH_ASSOC);

$facilityActiveToday = [];
foreach ($activeTodayList as $res) {
    $fid = (int)$res['facility_id'];
    if (!isset($facilityActiveToday[$fid])) {
        $facilityActiveToday[$fid] = $res;
    }
}

// Fetch upcoming approved reservations
$upcResStmt = $db->prepare("SELECT r.facility_id, r.room_id, r.reference_no, r.event_title, r.start_date, r.end_date, r.time_slot, u.full_name AS applicant_name 
                            FROM reservations r
                            LEFT JOIN users u ON r.user_id = u.id
                            WHERE r.status IN ('Approved', 'Completed') 
                              AND r.start_date > :today 
                              AND r.deleted_at IS NULL
                            ORDER BY r.start_date ASC");
$upcResStmt->execute([':today' => $today]);
$upcomingList = $upcResStmt->fetchAll(PDO::FETCH_ASSOC);

$facilityUpcoming = [];
foreach ($upcomingList as $res) {
    $fid = (int)$res['facility_id'];
    if (!isset($facilityUpcoming[$fid])) {
        $facilityUpcoming[$fid] = $res;
    }
}

$processedFacilities = [];
foreach ($monitoredFacilities as $fac) {
    $fid = (int)$fac['id'];
    $isMaint = ($fac['status'] === 'maintenance');
    $isActiveToday = isset($facilityActiveToday[$fid]);
    $hasUpcoming = isset($facilityUpcoming[$fid]);

    if ($isMaint) {
        $stateKey = 'maintenance';
        $stateLabel = 'Maintenance Hold';
    } elseif ($isActiveToday) {
        $stateKey = 'occupied';
        $todayRes = $facilityActiveToday[$fid];
        $timeStr = !empty($todayRes['time_slot']) ? ' (' . $todayRes['time_slot'] . ')' : '';
        $stateLabel = 'Occupied Today' . $timeStr;
    } elseif ($hasUpcoming) {
        $stateKey = 'reserved';
        $upRes = $facilityUpcoming[$fid];
        $upStart = date('M d', strtotime($upRes['start_date']));
        $upEnd = (!empty($upRes['end_date']) && $upRes['end_date'] !== $upRes['start_date']) ? '–' . date('d', strtotime($upRes['end_date'])) : '';
        $stateLabel = 'Reserved (' . $upStart . $upEnd . ')';
    } else {
        $stateKey = 'available';
        $stateLabel = 'Available';
    }

    $processedFacilities[] = array_merge($fac, [
        'stateKey' => $stateKey,
        'stateLabel' => $stateLabel
    ]);
}

// ============================================================================
// REAL-TIME OCCUPANCY & UTILIZATION ANALYTICS
// ============================================================================
$totalInstitutionalCapacity = 0;
$totalHallsCapacity = 0;
$totalDormsCapacity = 0;
$totalGuesthousesCapacity = 0;

$facilityOccupancyMap = [];
foreach ($processedFacilities as $f) {
    $fid = (int)$f['id'];
    $isGuesthouse = (($f['type'] ?? '') === 'guesthouses' || ($f['type'] ?? '') === 'guesthouse' || stripos($f['name'] ?? '', 'guesthouse') !== false);
    $isDorm = !$isGuesthouse && (($f['type'] ?? '') === 'dorms' || ($f['type'] ?? '') === 'dormitories');
    $cap = (int)preg_replace('/[^0-9]/', '', (string)$f['capacity']);
    if ($cap <= 0) {
        $cap = $isDorm ? (int)($f['beds'] ?: 2) : ($isGuesthouse ? 4 : 50);
    }
    
    if ($isGuesthouse) {
        $totalGuesthousesCapacity += $cap;
    } elseif ($isDorm) {
        $totalDormsCapacity += $cap;
    } else {
        $totalHallsCapacity += $cap;
    }
    $totalInstitutionalCapacity += $cap;
    
    $shortName = $f['name'];
    $shortName = preg_replace('/^(RDEC\s+)/i', 'RDEC ', $shortName);
    $shortName = str_ireplace('Training Hall', 'Hall', $shortName);
    $shortName = str_ireplace('Function Hall', 'Func Hall', $shortName);
    $shortName = str_ireplace('Carandang Room', 'Carandang', $shortName);
    $shortName = str_ireplace('Serrano Hall', 'Serrano', $shortName);
    $shortName = str_ireplace('Orosa Hall', 'Orosa', $shortName);
    $shortName = trim($shortName);

    $facilityOccupancyMap[$fid] = [
        'id' => $fid,
        'name' => $f['name'],
        'shortName' => $shortName,
        'type' => $f['type'],
        'capacity' => $cap,
        'beds' => (int)($f['beds'] ?? 0),
        'booked_pax' => 0,
        'occupancy_pct' => 0.0,
        'stateLabel' => $f['stateLabel'],
        'stateKey' => $f['stateKey']
    ];
}

$liveTotalBookedPax = 0;
$liveHallsBookedPax = 0;
$liveDormsBookedPax = 0;
$liveGuesthousesBookedPax = 0;

$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));
$timelineLabel = 'Current Week (' . date('M j', strtotime($startOfWeek)) . ' – ' . date('M j, Y', strtotime($endOfWeek)) . ')';

$dailyPaxTrend = [];
for ($i = 0; $i < 7; $i++) {
    $dDate = date('Y-m-d', strtotime("$startOfWeek +$i days"));
    $dailyPaxTrend[$dDate] = [
        'dayName' => date('D', strtotime($dDate)),
        'dateStr' => date('M j', strtotime($dDate)),
        'isToday' => ($dDate === date('Y-m-d')),
        'pax' => 0,
        'pct' => 0.0
    ];
}

foreach ($allReservations as $res) {
    if (!empty($res['deleted_at'])) continue;
    $st = $res['status'] ?? 'Pending';
    if (in_array($st, ['Rejected', 'Cancelled'])) continue;

    $fid = (int)($res['facility_id'] ?? 0);
    $pax = (int)($res['pax_count'] ?? 0);
    $isResGuesthouse = (($res['facility_type'] ?? '') === 'guesthouses' 
                        || ($res['facility_type'] ?? '') === 'guesthouse' 
                        || str_starts_with($res['reference_no'] ?? '', 'ATI-GH') 
                        || stripos($res['facility_name'] ?? '', 'guesthouse') !== false);
    $isResDorm = !$isResGuesthouse && (($res['facility_type'] ?? '') === 'dormitories' 
                                       || ($res['facility_type'] ?? '') === 'dorms' 
                                       || !empty($res['room_number']));

    if (isset($facilityOccupancyMap[$fid])) {
        $facilityOccupancyMap[$fid]['booked_pax'] += $pax;
    }

    $liveTotalBookedPax += $pax;
    if ($isResGuesthouse) {
        $liveGuesthousesBookedPax += $pax;
    } elseif ($isResDorm) {
        $liveDormsBookedPax += $pax;
    } else {
        $liveHallsBookedPax += $pax;
    }

    $rStart = !empty($res['start_date']) ? $res['start_date'] : date('Y-m-d');
    $rEnd = !empty($res['end_date']) ? $res['end_date'] : $rStart;

    foreach ($dailyPaxTrend as $dKey => &$dInfo) {
        if ($dKey >= $rStart && $dKey <= $rEnd) {
            $dInfo['pax'] += $pax;
        }
    }
    unset($dInfo);
}

foreach ($facilityOccupancyMap as &$fItem) {
    $fItem['occupancy_pct'] = $fItem['capacity'] > 0 
        ? round(($fItem['booked_pax'] / $fItem['capacity']) * 100, 1) 
        : 0.0;
}
unset($fItem);

$liveOverallOccupancyPct = $totalInstitutionalCapacity > 0 
    ? min(100, round(($liveTotalBookedPax / $totalInstitutionalCapacity) * 100, 1)) 
    : 0.0;
$liveHallsOccupancyPct = $totalHallsCapacity > 0 
    ? min(100, round(($liveHallsBookedPax / $totalHallsCapacity) * 100, 1)) 
    : 0.0;
$liveDormsOccupancyPct = $totalDormsCapacity > 0 
    ? min(100, round(($liveDormsBookedPax / $totalDormsCapacity) * 100, 1)) 
    : 0.0;
$liveGuesthousesOccupancyPct = $totalGuesthousesCapacity > 0 
    ? min(100, round(($liveGuesthousesBookedPax / $totalGuesthousesCapacity) * 100, 1)) 
    : 0.0;

$peakDayLabel = date('l, M j');
$peakDayPax = 0;
foreach ($dailyPaxTrend as $dKey => &$dInfo) {
    $dInfo['pct'] = $totalInstitutionalCapacity > 0 
        ? min(100, round(($dInfo['pax'] / $totalInstitutionalCapacity) * 100, 1)) 
        : 0.0;
    if ($dInfo['pax'] >= $peakDayPax) {
        $peakDayPax = $dInfo['pax'];
        $peakDayLabel = date('l, M j', strtotime($dKey));
    }
}
unset($dInfo);

$peakDayPct = $totalInstitutionalCapacity > 0 
    ? min(100, round(($peakDayPax / $totalInstitutionalCapacity) * 100, 1)) 
    : 0.0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Dashboard | ATI Facility Reservation Portal</title>
  <meta name="description"
    content="Super Administrator Executive Dashboard for Agricultural Training Institute. Review reservations, oversee facility occupancy, and manage institutional bookings.">

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
  <link rel="stylesheet" href="css/admin_approvals.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="admin-app-body">

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

      <!-- Executive Top Bar -->
      <header class="admin-topbar">
        <div class="admin-topbar-left">
          <!-- Toggle Sidebar Button -->
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator</span>
            <h1 class="topbar-page-title">Executive Command Center</h1>
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
            <span id="currentDateDisplay"><?= date('M d, Y') ?></span>
          </div>

          <!-- User Profile Badge -->
          <a href="profile.php" class="topbar-user-badge" title="<?= $sidebarUserFullName ?> (<?= htmlspecialchars($roleOfficerTitle ?? 'Administrator') ?>)">
            <div class="topbar-user-avatar <?= htmlspecialchars($currentAdminRole ?? 'super_admin') ?>" id="topbarUserAvatar">
              <img src="<?= !empty($sidebarUserAvatar) ? htmlspecialchars($sidebarUserAvatar) : '' ?>" alt="Profile Picture" class="topbar-avatar-img" id="topbarAvatarImg" style="<?= empty($sidebarUserAvatar) ? 'display: none;' : '' ?>">
              <span class="topbar-avatar-initials" id="topbarAvatarInitials" style="<?= !empty($sidebarUserAvatar) ? 'display: none;' : '' ?>"><?= htmlspecialchars($roleInitials ?? $sidebarUserInitials) ?></span>
            </div>
            <div class="topbar-user-meta">
              <span class="topbar-user-name" id="topbarUserName"><?= $sidebarUserFullName ?></span>
              <span class="topbar-user-role"><?= htmlspecialchars($roleOfficerTitle ?? 'Administrator') ?></span>
            </div>
          </a>
        </div>
      </header>

      <!-- Main Scrollable Body (TERNO WITH PORTAL) -->
      <main class="admin-content-container">

        <!-- Header Section -->
        <section class="my-res-header admin-page-header">
          <div class="my-res-header-text">
            <h2>All Reservations</h2>
            <p>Complete master list of all reservation requests across ATI Central Office facilities and dormitories.</p>
          </div>
          <div class="admin-header-actions" style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn-frontdesk-checkin" onclick="openQuickCheckInModal()" title="Manual Entry / Quick Client Check-In">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
              <span>Client Check-In / Pass Lookup</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (TERNO WITH SYSTEM DESIGN)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Executive KPI Overview">
          <!-- Total Reservations -->
          <div class="my-res-stat-card active-filter" data-kpi="all" onclick="applyTabFilter('all')"
            title="Click to view all reservations">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotal"><?= $totalActiveReservations ?></span>
                <span class="stat-trend positive">Live</span>
              </div>
              <span class="stat-label">Total Reservations</span>
            </div>
          </div>

          <!-- Pending Approvals -->
          <div class="my-res-stat-card admin-stat-highlight" data-kpi="pending"
            onclick="applyTabFilter('pending')" title="Click to filter pending approval requests">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiPending"><?= $pendingReservations ?></span>
                <span class="stat-pill-urgent">Needs Action</span>
              </div>
              <span class="stat-label">Pending Approval Requests</span>
            </div>
          </div>

          <!-- Venue Occupancy -->
          <div class="my-res-stat-card" data-kpi="occupancy" id="cardOccupancyKpi" onclick="openOccupancyModal()"
            title="Click to open occupancy analytics & graphs">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOccupancy"><?= $liveOverallOccupancyPct ?>%</span>
                <span class="stat-trend neutral"><?= ($approvedReservations + $completedReservations) ?> Confirmed</span>
              </div>
              <span class="stat-label">Venue & Dorm Occupancy</span>
            </div>
          </div>

          <!-- Monthly Revenue / Utilization -->
          <div class="my-res-stat-card" data-kpi="revenue" id="cardUtilizationKpi"
            onclick="openUtilizationModal()"
            title="Click to view estimated facility utilization analytics & financial valuation breakdown">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M8.5 17V7h4.5a3 3 0 0 1 0 6H8.5"></path>
                <line x1="6" y1="9.5" x2="15.5" y2="9.5"></line>
                <line x1="6" y1="12" x2="15.5" y2="12"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiRevenue">₱<?= $estimatedUtilization ?></span>
                <span class="stat-trend positive">Live Est.</span>
              </div>
              <span class="stat-label">Estimated Facility Utilization</span>
            </div>
          </div>
        </section>



        <!-- ==========================================================================
             FILTER & SEARCH TOOLBAR (MATCHED WITH my_reservations.php)
             ========================================================================== -->
        <div class="my-res-toolbar-card admin-toolbar-card" id="reservationsSection">
          <!-- Filter Tabs -->
          <div class="filter-tabs-group" role="tablist">
            <button type="button" class="filter-tab-btn active" data-filter="all" onclick="applyTabFilter('all')">
              <span>All Requests</span>
              <span class="tab-count-pill" id="tabCountAll"><?= $totalReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="pending" onclick="applyTabFilter('pending')">
              <span>Pending Review</span>
              <span class="tab-count-pill tab-count-pending" id="tabCountPending"><?= $pendingReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="approved" onclick="applyTabFilter('approved')">
              <span>Approved</span>
              <span class="tab-count-pill" id="tabCountApproved"><?= $approvedReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="checkedin" onclick="applyTabFilter('checkedin')">
              <span>Checked In</span>
              <span class="tab-count-pill" id="tabCountCheckedIn"><?= $checkedInReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="completed" onclick="applyTabFilter('completed')">
              <span>Completed</span>
              <span class="tab-count-pill" id="tabCountCompleted"><?= $completedReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="halls" onclick="applyTabFilter('halls')">
              <span>Function Halls</span>
              <span class="tab-count-pill" id="tabCountHalls"><?= $hallsCount ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="dorms" onclick="applyTabFilter('dorms')">
              <span>Dormitories</span>
              <span class="tab-count-pill" id="tabCountDorms"><?= $dormsCount ?></span>
            </button>
            <button type="button" class="filter-tab-btn tab-archived" data-filter="archived" onclick="applyTabFilter('archived')">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Archived / Trash</span>
              <span class="tab-count-pill tab-count-archived" id="tabCountArchived"><?= $archivedReservationsCount ?></span>
            </button>
          </div>

          <!-- Controls Row: Search on Left, Filter on Right -->
          <div class="toolbar-controls-row toolbar-controls-right">
            <div class="search-box-wrap admin-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input type="text" id="adminSearchInput" class="search-input" placeholder="Search applicant, agency, venue, or ref #..."
                oninput="handleAdminSearch(this.value)" autocomplete="off">
              <button type="button" class="search-clear-btn" id="adminSearchClear" onclick="clearAdminSearch()" title="Clear search" style="display: none;" aria-label="Clear search">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <div class="custom-facility-dropdown" id="venueCustomDropdown">
              <input type="hidden" id="venueFilterSelect" value="all">
              <button type="button" class="facility-dropdown-btn venue-filter-btn" id="venueFilterBtn"
                aria-haspopup="listbox" aria-expanded="false" onclick="toggleVenueDropdown(event)" title="Filter by Facility or Venue">
                <span class="facility-btn-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  </svg>
                </span>
                <span class="facility-btn-label" id="venueFilterLabel">All Venues &amp; Rooms</span>
                <svg class="facility-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <div class="facility-dropdown-menu venue-filter-menu" id="venueFilterMenu" role="listbox" aria-label="Filter by Venue">
                <!-- Option: All Venues & Rooms -->
                <div class="facility-option active" data-value="all" role="option" aria-selected="true"
                  onclick="selectAdminVenueOption('all', 'All Venues &amp; Rooms', 'all')">
                  <span class="opt-bullet all"></span>
                  <span class="opt-name">All Venues &amp; Rooms</span>
                  <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>

                <!-- Category Row 1: Training & Function Halls (Different shade of color: fresh green/mint) -->
                <div class="dropdown-category-separator category-sep-halls"
                  onclick="selectAdminVenueOption('category:halls', 'All Halls &amp; Facilities', 'halls')"
                  title="Filter by Training &amp; Function Halls">
                  <div class="cat-sep-left">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    <span>Training &amp; Function Halls</span>
                  </div>
                  <span class="cat-sep-pill halls">All Halls (<?= count($hallsList) ?>)</span>
                </div>

                <!-- Halls items -->
                <?php foreach ($hallsList as $fac): ?>
                  <div class="facility-option sub-opt" data-value="<?= htmlspecialchars($fac['name']) ?>" data-category="halls" role="option"
                    onclick="selectAdminVenueOption('<?= htmlspecialchars(addslashes($fac['name'])) ?>', '<?= htmlspecialchars(addslashes($fac['name'])) ?>', 'halls')">
                    <span class="opt-bullet" style="background: #16a34a;"></span>
                    <span class="opt-name"><?= htmlspecialchars($fac['name']) ?></span>
                    <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </div>
                <?php endforeach; ?>

                <!-- Category Row 2: Dormitories & Lodgings (Different shade of color: warm honey amber) -->
                <div class="dropdown-category-separator category-sep-dorms"
                  onclick="selectAdminVenueOption('category:dorms', 'All Dormitories &amp; Suites', 'dorms')"
                  title="Filter by Dormitory Suites">
                  <div class="cat-sep-left">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                      <path d="M2 4v16"></path>
                      <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                      <path d="M2 17h20"></path>
                      <path d="M6 8v9"></path>
                    </svg>
                    <span>Dormitories &amp; Suites</span>
                  </div>
                  <span class="cat-sep-pill dorms">All Dorms (<?= count($dormsList) ?>)</span>
                </div>

                <!-- Dorms items -->
                <?php foreach ($dormsList as $fac): ?>
                  <div class="facility-option sub-opt" data-value="<?= htmlspecialchars($fac['name']) ?>" data-category="dorms" role="option"
                    onclick="selectAdminVenueOption('<?= htmlspecialchars(addslashes($fac['name'])) ?>', '<?= htmlspecialchars(addslashes($fac['name'])) ?>', 'dorms')">
                    <span class="opt-bullet" style="background: #f59e0b;"></span>
                    <span class="opt-name"><?= htmlspecialchars($fac['name']) ?></span>
                    <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </div>
                <?php endforeach; ?>

                <!-- Category Row 3: Guesthouses & Private Villas (Different shade of color: cool teal/cyan) -->
                <div class="dropdown-category-separator category-sep-guesthouses"
                  onclick="selectAdminVenueOption('category:guesthouses', 'All Guesthouses &amp; Villas', 'guesthouses')"
                  title="Filter by Guesthouses &amp; Villas">
                  <div class="cat-sep-left">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Guesthouses &amp; Villas</span>
                  </div>
                  <span class="cat-sep-pill guesthouses">All Villas (<?= count($guesthousesList) ?>)</span>
                </div>

                <!-- Guesthouses items -->
                <?php foreach ($guesthousesList as $fac): 
                  $rawName = $fac['name'];
                  $villaTitle = $rawName;
                  $subBadge = '';
                  if (preg_match('/^(Guesthouse\s*\d+)\s*\(([^)]+)\)/i', $rawName, $m)) {
                    $subBadge = $m[1];
                    $villaTitle = $m[2];
                    if (stripos($villaTitle, 'Executive Villa') !== false) {
                      $villaTitle = 'Presidential Villa';
                    }
                  } elseif (preg_match('/\(([^)]+)\)/', $rawName, $m)) {
                    $villaTitle = $m[1];
                  }
                ?>
                  <div class="facility-option sub-opt" data-value="<?= htmlspecialchars($rawName) ?>" data-category="guesthouses" role="option"
                    onclick="selectAdminVenueOption('<?= htmlspecialchars(addslashes($rawName)) ?>', '<?= htmlspecialchars(addslashes($villaTitle)) ?>', 'guesthouses')">
                    <span class="opt-bullet" style="background: #0f766e;"></span>
                    <span class="opt-name"><?= htmlspecialchars($villaTitle) ?></span>
                    <?php if (!empty($subBadge)): ?>
                      <span class="opt-sub"><?= htmlspecialchars($subBadge) ?></span>
                    <?php endif; ?>
                    <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             RESERVATIONS QUEUE TABLE (SYSTEM CARD DESIGN)
             ========================================================================== -->
        <div class="admin-table-card">
          <div class="admin-card-header">
            <div class="admin-card-title-group">
              <h3>Action & Routing Approval Queue</h3>
              <p>Review submissions, verify attachments, and execute official Director IV approvals.</p>
            </div>
            <div class="admin-card-header-badge">
              <span class="indicator-live-dot"></span>
              Live Institutional Feed
            </div>
          </div>

          <div class="admin-table-responsive">
            <table class="admin-data-table" id="adminReservationsTable">
              <thead>
                <tr>
                  <th>Reference & Date</th>
                  <th>Applicant &amp; Org</th>
                  <th>Facility</th>
                  <th>Schedule / Duration</th>
                  <th>Attendees</th>
                  <th>Current Status</th>
                  <th style="text-align: right;">Executive Actions</th>
                </tr>
              </thead>
              <tbody id="adminTableBody">
                <!-- Empty search state row -->
                <tr id="adminNoResultsRow" style="display: none;">
                  <td colspan="7" class="admin-empty-table-state">
                    <div class="empty-state-content">
                      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#8ca394" stroke-width="1.8">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      </svg>
                      <h4>No reservation found</h4>
                      <p>No records matched your search query or selected filters.</p>
                      <button type="button" class="btn-system-secondary" style="font-size: 0.82rem; padding: 0.45rem 1.15rem;" onclick="resetAllAdminFilters()">Reset Filters</button>
                    </div>
                  </td>
                </tr>
                <?php if (empty($reservationsData)): ?>
                <tr>
                  <td colspan="7" class="admin-empty-table-state" style="text-align: center; padding: 3.5rem 1rem;">
                    <div class="empty-state-content">
                      <div style="display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 50%; background: #eef7f2; margin-bottom: 0.75rem;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="8" x2="12" y2="12"></line>
                          <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                      </div>
                      <h4 style="font-size: 1.05rem; font-weight: 700; color: #172c1f; margin: 0 0 0.35rem 0;">No reservation found</h4>
                      <p style="color: #637f6f; font-size: 0.85rem; margin: 0;">There are currently no reservations recorded in the database.</p>
                    </div>
                  </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($reservationsData as $item): 
                  $initials = getUserInitials($item['applicant']);
                  $isArchived = !empty($item['isArchived']);
                ?>
                <tr data-stage="<?=$item['stage']?>" 
                    data-ref="<?= htmlspecialchars($item['ref']) ?>" 
                    data-res-id="<?= $item['id'] ?>"
                    data-status="<?= htmlspecialchars($item['status']) ?>" 
                    data-category="<?= htmlspecialchars($item['category']) ?>" 
                    data-venue="<?= htmlspecialchars($item['venue']) ?>"
                    data-archived="<?= $isArchived ? '1' : '0' ?>"
                    class="<?= $isArchived ? 'row-reservation-archived' : '' ?>"
                    style="<?= $isArchived ? 'display: none;' : '' ?>">
                  <td>
                    <div class="td-ref-group">
                      <span class="td-ref-id"><?= htmlspecialchars($item['ref']) ?></span>
                      <span class="td-sub-date"><?= htmlspecialchars($item['dateFiled']) ?></span>
                    </div>
                  </td>
                  <td>
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm"><?= htmlspecialchars($initials) ?></div>
                      <div class="td-user-details">
                        <span class="td-name"><?= htmlspecialchars($item['applicant']) ?></span>
                        <span class="td-org"><?= htmlspecialchars($item['agency']) ?></span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="td-facility-pill <?= $item['category'] === 'dorms' ? 'dorm' : 'hall' ?>">
                      <?php if ($item['category'] === 'dorms'): ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12h18M3 18h18M5 12V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6"></path></svg>
                      <?php else: ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <?php endif; ?>
                      <span><?= htmlspecialchars($item['venue']) ?></span>
                    </div>
                  </td>
                  <td>
                    <div class="td-schedule-text">
                      <strong><?= htmlspecialchars($item['rawDates']) ?></strong>
                      <span><?= htmlspecialchars($item['timeSlot']) ?></span>
                    </div>
                  </td>
                  <td>
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                      <span><?= htmlspecialchars($item['pax']) ?></span>
                    </div>
                  </td>
                  <td>
                    <?php if ($isArchived): ?>
                      <span class="status-pill status-archived" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                        <span class="status-dot" style="background: #dc2626;"></span>
                        Archived / Trash
                      </span>
                      <?php if (!empty($item['deletedAt'])): ?>
                        <span style="display: block; font-size: 0.71rem; color: #dc2626; font-weight: 600; margin-top: 3px;">
                          Archived: <?= $item['deletedAt'] ?>
                        </span>
                      <?php endif; ?>
                    <?php elseif ($item['stage'] === 'completed' || $item['rawStatus'] === 'Completed'): ?>
                      <span class="status-pill status-completed">
                        <span class="status-dot"></span>
                        Completed &amp; Concluded
                      </span>
                    <?php elseif ($item['stage'] === 'checked_in' || $item['rawStatus'] === 'Checked In'): ?>
                      <span class="status-pill status-checkedin">
                        <span class="status-dot"></span>
                        Checked In
                      </span>
                      <?php if (!empty($item['checkInAt'])): ?>
                        <span style="display: block; font-size: 0.71rem; color: #059669; font-weight: 600; margin-top: 3px;">
                          <?= htmlspecialchars($item['checkInAt']) ?>
                        </span>
                      <?php endif; ?>
                    <?php elseif ($item['stage'] === 'no_show' || $item['rawStatus'] === 'No-Show'): ?>
                      <span class="status-pill status-noshow">
                        <span class="status-dot"></span>
                        No-Show
                      </span>
                    <?php elseif ($item['stage'] === 'approved'): ?>
                      <span class="status-pill status-approved">
                        <span class="status-dot"></span>
                        Confirmed &amp; Approved
                      </span>
                    <?php elseif ($item['stage'] === 'rejected'): ?>
                      <span class="status-pill status-cancelled">
                        <span class="status-dot"></span>
                        Declined
                      </span>
                    <?php elseif ($item['stage'] === 'stage1'): ?>
                      <span class="status-pill status-stage1">
                        <span class="status-dot"></span>
                        Awaiting Endorsement
                      </span>
                    <?php elseif ($item['stage'] === 'stage2'): ?>
                      <span class="status-pill status-stage2">
                        <span class="status-dot"></span>
                        Awaiting Recommendation
                      </span>
                    <?php elseif ($item['stage'] === 'stage3'): ?>
                      <span class="status-pill status-stage3">
                        <span class="status-dot"></span>
                        Awaiting Clearance
                      </span>
                    <?php else: ?>
                      <span class="status-pill status-pending">
                        <span class="status-dot"></span>
                        Pending Review
                      </span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <?php if ($isArchived): ?>
                        <button type="button" class="btn-table-action restore" onclick="confirmRestoreReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Restore Reservation to Active Queue">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                          <span>Restore</span>
                        </button>
                        <?php if ($currentAdminRole === 'super_admin'): ?>
                          <button type="button" class="btn-table-action purge" onclick="confirmPermanentDeleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Permanently delete reservation from database">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Purge</span>
                          </button>
                        <?php endif; ?>
                        <button type="button" class="btn-table-action view" onclick="viewReservationDetails('<?= htmlspecialchars($item['ref']) ?>')" title="View Booking Form">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                      <?php else: ?>
                        <?php if ($item['stage'] === 'stage1'): ?>
                          <?php if ($currentAdminRole === 'endorsement' || $currentAdminRole === 'super_admin'): ?>
                            <button type="button" class="btn-table-action endorse" onclick="endorseReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Endorse Request (Stage 1)">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                              <span>Endorse</span>
                            </button>
                            <button type="button" class="btn-table-action decline" onclick="openDeclineModal('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Decline Request (Stage 1)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; font-weight:700; color:#b45309; background:#fffbeb; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #fde68a;">Awaiting Endorsement</span>
                          <?php endif; ?>
                        <?php elseif ($item['stage'] === 'stage2'): ?>
                          <?php if ($currentAdminRole === 'recommendation' || $currentAdminRole === 'super_admin'): ?>
                            <button type="button" class="btn-table-action recommend" onclick="recommendReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Recommend Request (Stage 2)">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                              <span>Recommend</span>
                            </button>
                            <button type="button" class="btn-table-action decline" onclick="openDeclineModal('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Decline Request (Stage 2)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; font-weight:700; color:#1d4ed8; background:#eff6ff; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #bfdbfe;">Awaiting Recommendation</span>
                          <?php endif; ?>
                        <?php elseif ($item['stage'] === 'stage3'): ?>
                          <?php if ($currentAdminRole === 'clearance' || $currentAdminRole === 'super_admin'): ?>
                            <button type="button" class="btn-table-action approve" onclick="clearanceReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Grant Director Clearance (Stage 3)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                              <span>Approve</span>
                            </button>
                            <button type="button" class="btn-table-action decline" onclick="openDeclineModal('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Decline Request (Stage 3)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; font-weight:700; color:#047857; background:#ecfdf5; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #a7f3d0;">Awaiting Clearance</span>
                          <?php endif; ?>
                        <?php elseif ($item['stage'] === 'completed' || $item['rawStatus'] === 'Completed'): ?>
                          <span class="badge-completed-note">Completed</span>
                        <?php elseif ($item['stage'] === 'checked_in' || $item['rawStatus'] === 'Checked In'): ?>
                          <button type="button" class="btn-table-action complete" onclick="confirmCompleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Conclude and Mark as Completed">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Complete</span>
                          </button>
                          <span style="font-size:0.75rem; font-weight:700; color:#047857; background:#ecfdf5; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #a7f3d0;">Checked In</span>
                        <?php elseif ($item['stage'] === 'no_show' || $item['rawStatus'] === 'No-Show'): ?>
                          <span style="font-size:0.75rem; font-weight:700; color:#b91c1c; background:#fef2f2; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #fecaca;">No-Show</span>
                        <?php elseif ($item['stage'] === 'approved'): ?>
                          <button type="button" class="btn-table-action checkin-btn" onclick="openQuickCheckInModal('<?= htmlspecialchars($item['ref']) ?>')" title="Check In Client at Front Desk">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>Check-In</span>
                          </button>
                          <button type="button" class="btn-table-action complete" onclick="confirmCompleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Mark Reservation as Completed &amp; Concluded">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Complete</span>
                          </button>
                          <span class="badge-approved-note">Approved</span>
                        <?php else: ?>
                          <span class="badge-declined-note">Declined</span>
                        <?php endif; ?>

                        <button type="button" class="btn-table-action view" onclick="viewReservationDetails('<?= htmlspecialchars($item['ref']) ?>')" title="View Booking Form">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>

                        <button type="button" class="btn-table-action delete-res" onclick="confirmDeleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Move Reservation to Trash (Soft Delete)">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>



      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: FRONT DESK QUICK CHECK-IN & LOOKUP (MANUAL REFERENCE ENTRY)
       ========================================================================== -->
  <div class="admin-modal-overlay" id="quickCheckInModal" style="display: none;" onclick="if(event.target===this) closeQuickCheckInModal()">
    <div class="admin-modal-card" style="max-width: 620px;">
      <div class="admin-modal-header" style="background: linear-gradient(135deg, #15803d 0%, #166534 100%); color: #ffffff; padding: 1.1rem 1.4rem;">
        <div class="modal-title-wrap">
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span style="background: rgba(255,255,255,0.2); color: #ffffff; font-size: 0.72rem; font-weight: 800; padding: 0.2rem 0.55rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">Front Desk Action</span>
            <span style="color: #bbf7d0; font-size: 0.8rem; font-weight: 600;">Manual Reference Entry</span>
          </div>
          <h4 style="margin: 0; color: #ffffff; font-size: 1.25rem; font-weight: 700;">Client Quick Check-In</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeQuickCheckInModal()" aria-label="Close dialog" style="color: #ffffff; background: rgba(255,255,255,0.15); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body" style="padding: 1.4rem;">
        <!-- Search Bar Section -->
        <div style="margin-bottom: 1.1rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.45rem;">
            Reference Code (From Gate Pass or Booking Slip)
          </label>
          <div class="checkin-search-bar">
            <input type="text" id="quickCheckInInput" class="checkin-search-input" placeholder="e.g. ATI-2026-0042 or last digits (0042)..." autocomplete="off" onkeydown="if(event.key==='Enter') executeQuickCheckInLookup()">
            <button type="button" class="btn-checkin-search" id="btnExecuteLookup" onclick="executeQuickCheckInLookup()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              <span>Search</span>
            </button>
          </div>
          <div style="font-size: 0.77rem; color: #64748b; margin-top: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>Tip: You can paste the full reference number or enter the trailing digits only.</span>
          </div>
        </div>

        <!-- Dynamic Results Container -->
        <div id="quickCheckInResultsArea">
          <!-- Initial Default State Card -->
          <div id="checkinDefaultPrompt" style="text-align: center; padding: 2.2rem 1.5rem; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 12px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background: #dcfce7; margin-bottom: 0.85rem; color: #15803d;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <h5 style="margin: 0 0 0.35rem 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Ready for Client Check-In Verification</h5>
            <p style="margin: 0; font-size: 0.84rem; color: #64748b; max-width: 440px; margin-inline: auto;">Enter the client's reference code above to review reservation details and confirm arrival at the facility.</p>
          </div>

          <!-- Loading State -->
          <div id="checkinLoadingState" style="display: none; text-align: center; padding: 2.5rem 1rem;">
            <div style="display: inline-block; width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #15803d; border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 0.75rem;"></div>
            <div style="font-size: 0.9rem; font-weight: 600; color: #334155;">Verifying reservation records...</div>
          </div>

          <!-- Found Reservation Card Template -->
          <div id="checkinFoundCard" style="display: none;"></div>

          <!-- Not Found Alert State -->
          <div id="checkinNotFoundState" style="display: none; padding: 1.5rem; background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 12px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: #fee2e2; margin-bottom: 0.65rem; color: #dc2626;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </div>
            <h5 style="margin: 0 0 0.35rem 0; font-size: 0.98rem; font-weight: 700; color: #991b1b;">Reservation Not Found</h5>
            <p id="checkinNotFoundMsg" style="margin: 0; font-size: 0.84rem; color: #b91c1c;">No active reservation matched the provided reference number.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: RESERVATION DETAILS & DOCUMENT REVIEW
       ========================================================================== -->
  <div class="admin-modal-overlay" id="reservationDetailsModal" style="display: none;" onclick="if(event.target===this) closeReservationModal()">
    <div class="admin-modal-card" style="max-width: 840px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref" id="modalRefId">R-2026-0891</span>
          <h4>Official Reservation Application Form</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeReservationModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div class="modal-applicant-summary">
          <div class="user-avatar-circle" id="modalAvatar">JD</div>
          <div class="applicant-meta">
            <h5 id="modalApplicantName">Engr. Juan Dela Cruz</h5>
            <p id="modalApplicantOrg">ATI - Career Development Division (CDD)</p>
            <span class="applicant-contact" id="modalApplicantContact">juan.delacruz@ati.da.gov.ph &bull; +63 917 123 4567</span>
          </div>
        </div>

        <div class="modal-section-grid">
          <div class="modal-data-box">
            <label>Requested Venue</label>
            <span id="modalVenueName">Serrano Function Hall</span>
          </div>
          <div class="modal-data-box">
            <label>Schedule & Duration</label>
            <span id="modalSchedule">October 12 – 14, 2026 (3 Days)</span>
          </div>
          <div class="modal-data-box">
            <label>Expected Headcount</label>
            <span id="modalPax">85 Participants</span>
          </div>
          <div class="modal-data-box">
            <label>Nature of Event</label>
            <span id="modalEventTitle">National TOT on Climate-Resilient Agriculture</span>
          </div>
        </div>

        <div class="modal-data-box" style="margin-top: 1rem;">
          <label>Requested Equipment & Setup</label>
          <span id="modalEquipment">Projector & Screen, 4 Wireless Microphones, Stage Rostrum, Classroom Type Seating</span>
        </div>

        <!-- Supporting / Signed Document Attachment -->
        <div class="modal-data-box" style="margin-top: 1rem;">
          <label id="modalDocSectionLabel">Original Client Supporting Document (Letter of Intent / Request Memo)</label>
          <div class="attachment-preview-box" id="modalDocBox">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <div class="attachment-meta">
              <strong id="modalDocTitle">Official_Request_Document.pdf</strong>
              <small id="modalDocSub">Verified Document Attachment</small>
            </div>
            <a id="btnModalPreviewDoc" href="#" target="_blank" class="btn-preview-file" style="text-decoration: none;">View Document</a>
            <span id="modalNoDocText" style="display:none; font-size:0.8rem; color:#8a9e92; font-style:italic;">No document attached</span>
          </div>
          <!-- Clean secondary link for original client request memo if a signed document is active -->
          <div id="modalClientDocAltWrap" style="display: none; margin-top: 0.5rem; font-size: 0.8rem; padding: 0.4rem 0.75rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px;">
            <span style="color: #64748b;">Original Client Upload (Request Memo): </span>
            <a id="modalClientDocAltLink" href="#" target="_blank" style="color: #166534; font-weight: 600; text-decoration: underline;">View Client Request Letter</a>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeReservationModal()">Close Review</button>
        <button type="button" class="btn-complete-modal" id="btnModalComplete" onclick="modalTriggerComplete()" style="display: none;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>Mark as Completed</span>
        </button>
        <button type="button" class="btn-decline-modal" id="btnModalDecline" onclick="modalTriggerDecline()">Decline Request</button>
        <button type="button" class="btn-approve-modal" id="btnModalApprove" onclick="modalTriggerApprove()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>Approve & Endorse</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: DECLINE RESERVATION WITH JUSTIFICATION
       ========================================================================== -->
  <div class="admin-modal-overlay" id="declineModal" style="display: none;" onclick="if(event.target===this) closeDeclineModal()">
    <div class="admin-modal-card sm">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #991b1b;">Decline Reservation Request</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDeclineModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <p style="font-size: 0.92rem; color: #4b6354; margin-bottom: 1rem;">
          Please state the official administrative reason for declining reservation <strong id="declineModalRef">R-2026-0891</strong> by <span id="declineModalApplicant">Applicant</span>.
        </p>

        <div class="form-group-decline">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Select Justification</label>
          <select id="declineReasonSelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit;">
            <option value="Conflict with Official ATI Institutional Activity">Conflict with Official ATI Institutional Activity</option>
            <option value="Venue Under Scheduled Maintenance">Venue Under Scheduled Maintenance</option>
            <option value="Exceeds Maximum Venue Capacity">Exceeds Maximum Venue Capacity</option>
            <option value="Incomplete Supporting Documents / Request Letter">Incomplete Supporting Documents / Request Letter</option>
            <option value="Other Administrative Ground">Other Administrative Ground</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-top: 1rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Executive Remarks / Instructions to Applicant</label>
          <textarea id="declineRemarks" rows="3" placeholder="Enter remarks to be sent via official notification to the applicant..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeDeclineModal()">Cancel</button>
        <button type="button" class="btn-confirm-decline" onclick="submitDeclineAction()">Confirm & Send Notice</button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: OCCUPANCY & UTILIZATION ANALYTICS (REAL-TIME COMPACT POPUP)
       ========================================================================== -->
  <div class="admin-modal-overlay" id="occupancyModal" style="display: none;"
    onclick="if(event.target===this) closeOccupancyModal()">
    <div class="admin-modal-card occupancy-analytics-modal">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <div style="display: flex; align-items: center; gap: 0.55rem;">
            <div class="stat-icon-box blue" style="width: 30px; height: 30px; min-width: 30px; border-radius: 8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
            </div>
            <div class="modal-header-title">
              <h4>Venue &amp; Dormitory Occupancy Analytics</h4>
              <p>Real-time capacity utilization, statistics, and booking trend breakdowns</p>
            </div>
          </div>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeOccupancyModal()" aria-label="Close dialog">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body occupancy-modal-scroll">
        <!-- 1. Top Summary Metric Cards (Dynamic Live Data & Interactive Filters) -->
        <div class="occ-metrics-grid">
          <div class="occ-metric-card primary active" id="occCardAll" onclick="filterOccupancyCategory('all')" role="button" tabindex="0" title="Click to filter breakdown by All Facilities">
            <span class="occ-metric-lbl">Overall Occupancy</span>
            <div class="occ-metric-val"><?= $liveOverallOccupancyPct ?>%</div>
            <div class="occ-metric-sub"><strong><?= $liveTotalBookedPax ?></strong> / <?= $totalInstitutionalCapacity ?> Total PAX</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $liveOverallOccupancyPct) ?>%; background: #174d2f;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="occCardHall" onclick="filterOccupancyCategory('hall')" role="button" tabindex="0" title="Click to filter breakdown by Function Halls">
            <span class="occ-metric-lbl">Function Halls</span>
            <div class="occ-metric-val" style="color: #1d4ed8;"><?= $liveHallsOccupancyPct ?>%</div>
            <div class="occ-metric-sub"><strong><?= $liveHallsBookedPax ?></strong> / <?= $totalHallsCapacity ?> Hall PAX</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $liveHallsOccupancyPct) ?>%; background: #2563eb;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="occCardDorm" onclick="filterOccupancyCategory('dorm')" role="button" tabindex="0" title="Click to filter breakdown by Dormitory Suites">
            <span class="occ-metric-lbl">Dormitory Suites</span>
            <div class="occ-metric-val" style="color: #b45309;"><?= $liveDormsOccupancyPct ?>%</div>
            <div class="occ-metric-sub"><strong><?= $liveDormsBookedPax ?></strong> / <?= $totalDormsCapacity ?> Dorm Beds</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $liveDormsOccupancyPct) ?>%; background: #d97706;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="occCardGuesthouse" onclick="filterOccupancyCategory('guesthouse')" role="button" tabindex="0" title="Click to filter breakdown by Guesthouses &amp; Villas">
            <span class="occ-metric-lbl">Guesthouses &amp; Villas</span>
            <div class="occ-metric-val" style="color: #0f766e;"><?= $liveGuesthousesOccupancyPct ?>%</div>
            <div class="occ-metric-sub"><strong><?= $liveGuesthousesBookedPax ?></strong> / <?= $totalGuesthousesCapacity ?> Guest PAX</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $liveGuesthousesOccupancyPct) ?>%; background: #0d9488;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="occCardPeak" onclick="focusPeakLoadDay()" role="button" tabindex="0" title="Click to view Peak Load Day trend on Line Graph">
            <span class="occ-metric-lbl">Peak Load Day</span>
            <div class="occ-metric-val" style="color: #dc2626;"><?= $peakDayPct ?>%</div>
            <div class="occ-metric-sub"><?= htmlspecialchars($peakDayLabel) ?> &bull; <?= $peakDayPax ?> PAX</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $peakDayPct) ?>%; background: #ef4444;"></div>
            </div>
          </div>
        </div>

        <!-- 2. Controls & Switcher Toolbar -->
        <div class="occ-graph-toolbar">
          <div class="occ-toggle-group">
            <button type="button" class="occ-btn-toggle active" id="btnToggleBarGraph"
              onclick="switchOccupancyGraph('bar')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
              <span>Bar Graph (By Venue)</span>
            </button>
            <button type="button" class="occ-btn-toggle" id="btnToggleLineGraph" onclick="switchOccupancyGraph('line')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
              <span>Line Graph (Trend %)</span>
            </button>
          </div>

          <div class="occ-range-group">
            <label for="occTimeRangeSelect"
              style="font-size: 0.74rem; font-weight: 700; color: #4b6354;">Timeline:</label>
            <select id="occTimeRangeSelect" class="occ-range-select" onchange="updateOccupancyTimeRange(this.value)">
              <option value="week" selected><?= htmlspecialchars($timelineLabel) ?></option>
              <option value="month">Full Month of <?= date('F Y') ?></option>
              <option value="q4">Quarter <?= ceil(date('n') / 3) ?> Institutional Projection</option>
            </select>
          </div>
        </div>

        <!-- 3. Graph Container -->
        <div class="occ-chart-surface">
          <!-- VIEW A: REAL-TIME BAR GRAPH (Facility Capacity Breakdown) -->
          <div id="occupancyBarGraphView" class="occ-graph-pane">
            <div class="occ-chart-caption">
              <span class="occ-legend-pill"><span class="occ-dot hall"></span> Function Halls</span>
              <span class="occ-legend-pill"><span class="occ-dot dorm"></span> Dormitory Suites</span>
              <span class="occ-legend-pill"><span class="occ-dot" style="background: #0f766e;"></span> Guesthouses &amp; Villas</span>
              <span class="occ-legend-pill"><span class="occ-target-line"></span> Benchmark Target (80%)</span>
            </div>

            <!-- SVG Bar Graph -->
            <div class="occ-svg-container">
              <svg viewBox="0 0 580 170" class="occ-bar-svg" preserveAspectRatio="xMidYMid meet">
                <!-- Grid Lines (100px total height, baseline at y=130) -->
                <line x1="42" y1="30" x2="560" y2="30" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="55" x2="560" y2="55" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="80" x2="560" y2="80" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="105" x2="560" y2="105" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="130" x2="560" y2="130" stroke="#d5e3d8" stroke-width="1.2"></line>

                <!-- 80% Benchmark Line (y = 130 - 80 = 50) -->
                <line x1="42" y1="50" x2="560" y2="50" stroke="#eab308" stroke-dasharray="5 3" stroke-width="1.5"></line>
                <text x="562" y="53" font-size="8.5" fill="#b45309" font-weight="700">80% Target</text>

                <!-- Y Axis Labels -->
                <text x="36" y="33" font-size="9" fill="#789080" text-anchor="end">100%</text>
                <text x="36" y="58" font-size="9" fill="#789080" text-anchor="end">75%</text>
                <text x="36" y="83" font-size="9" fill="#789080" text-anchor="end">50%</text>
                <text x="36" y="108" font-size="9" fill="#789080" text-anchor="end">25%</text>
                <text x="36" y="133" font-size="9" fill="#789080" text-anchor="end">0%</text>

                <!-- Dynamic Real Facility Bars -->
                <?php 
                $facCount = count($facilityOccupancyMap);
                $barIdx = 0;
                $usableWidth = 500;
                $stepX = $facCount > 1 ? ($usableWidth / $facCount) : 60;
                $barW = min(36, max(24, round($stepX * 0.65)));

                foreach ($facilityOccupancyMap as $fItem):
                  $bx = round(48 + ($barIdx * $stepX) + (($stepX - $barW) / 2));
                  $pctVal = (float)$fItem['occupancy_pct'];
                  $cappedPct = min(100.0, max(0.0, $pctVal));
                  $bHeight = round(($cappedPct / 100.0) * 100);
                  $by = 130 - $bHeight;
                  $bcx = $bx + round($barW / 2);
                  $isGuesthouseFac = ($fItem['type'] === 'guesthouses' || $fItem['type'] === 'guesthouse' || stripos($fItem['name'], 'guesthouse') !== false);
                  $isDormFac = !$isGuesthouseFac && ($fItem['type'] === 'dorms' || $fItem['type'] === 'dormitories');
                  $barGrad = $isGuesthouseFac ? 'url(#gradGuesthouse)' : ($isDormFac ? 'url(#gradDorm)' : 'url(#gradHall)');
                  $barColor = $isGuesthouseFac ? '#0f766e' : ($isDormFac ? '#d97706' : '#174d2f');
                  $unitLabel = $isGuesthouseFac ? 'PAX' : ($isDormFac ? 'Beds' : 'PAX');
                  $itemCategory = $isGuesthouseFac ? 'guesthouse' : ($isDormFac ? 'dorm' : 'hall');
                  $barIdx++;
                ?>
                <g class="occ-bar-group" data-category="<?= $itemCategory ?>" tabindex="0" onclick="filterOccupancyCategory('<?= $itemCategory ?>')">
                  <title><?= htmlspecialchars($fItem['name']) ?>: <?= $fItem['booked_pax'] ?> / <?= $fItem['capacity'] ?> <?= $unitLabel ?> (<?= $pctVal ?>%)</title>
                  <rect x="<?= $bx ?>" y="<?= $by ?>" width="<?= $barW ?>" height="<?= max(2, $bHeight) ?>" rx="4" fill="<?= $barGrad ?>" class="bar-rect"></rect>
                  <text x="<?= $bcx ?>" y="<?= max(18, $by - 4) ?>" font-size="8.5" font-weight="800" fill="<?= $barColor ?>" text-anchor="middle"><?= $pctVal ?>%</text>
                  <text x="<?= $bcx ?>" y="142" font-size="8" font-weight="700" fill="#2d4233" text-anchor="middle"><?= htmlspecialchars($fItem['shortName']) ?></text>
                  <text x="<?= $bcx ?>" y="153" font-size="7" fill="#698572" text-anchor="middle"><?= $fItem['booked_pax'] ?>/<?= $fItem['capacity'] ?> <?= $unitLabel ?></text>
                </g>
                <?php endforeach; ?>

                <!-- Gradients -->
                <defs>
                  <linearGradient id="gradHall" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#1b5e33"></stop>
                    <stop offset="100%" stop-color="#2a824b"></stop>
                  </linearGradient>
                  <linearGradient id="gradDorm" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#d97706"></stop>
                    <stop offset="100%" stop-color="#f59e0b"></stop>
                  </linearGradient>
                  <linearGradient id="gradGuesthouse" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#0f766e"></stop>
                    <stop offset="100%" stop-color="#14b8a6"></stop>
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>

          <!-- VIEW B: REAL-TIME LINE GRAPH (Daily Occupancy Percentage Trend) -->
          <div id="occupancyLineGraphView" class="occ-graph-pane" style="display: none;">
            <div class="occ-chart-caption">
              <span class="occ-legend-pill"><span class="occ-dot" style="background: #174d2f;"></span> Daily Occupancy Rate (%)</span>
              <span class="occ-legend-pill"><span class="occ-target-line"></span> Weekly Average (<?= $liveOverallOccupancyPct ?>%)</span>
            </div>

            <!-- SVG Line Graph -->
            <div class="occ-svg-container">
              <svg viewBox="0 0 580 170" class="occ-line-svg" preserveAspectRatio="xMidYMid meet">
                <!-- Grid Lines -->
                <line x1="42" y1="30" x2="560" y2="30" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="55" x2="560" y2="55" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="80" x2="560" y2="80" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="105" x2="560" y2="105" stroke="#e8efe9" stroke-dasharray="3 3" stroke-width="0.8"></line>
                <line x1="42" y1="130" x2="560" y2="130" stroke="#d5e3d8" stroke-width="1.2"></line>

                <!-- Average Line -->
                <?php $avgY = 130 - round((min(100.0, $liveOverallOccupancyPct) / 100.0) * 100); ?>
                <line x1="42" y1="<?= $avgY ?>" x2="560" y2="<?= $avgY ?>" stroke="#eab308" stroke-dasharray="5 3" stroke-width="1.5"></line>
                <text x="562" y="<?= $avgY + 3 ?>" font-size="8.5" fill="#b45309" font-weight="700"><?= $liveOverallOccupancyPct ?>% Avg</text>

                <!-- Y Axis Labels -->
                <text x="36" y="33" font-size="9" fill="#789080" text-anchor="end">100%</text>
                <text x="36" y="58" font-size="9" fill="#789080" text-anchor="end">75%</text>
                <text x="36" y="83" font-size="9" fill="#789080" text-anchor="end">50%</text>
                <text x="36" y="108" font-size="9" fill="#789080" text-anchor="end">25%</text>
                <text x="36" y="133" font-size="9" fill="#789080" text-anchor="end">0%</text>

                <?php 
                $linePoints = [];
                $dayIdx = 0;
                foreach ($dailyPaxTrend as $dKey => $dVal) {
                  $px = 65 + ($dayIdx * 74);
                  $py = 130 - round((min(100.0, (float)$dVal['pct']) / 100.0) * 100);
                  $linePoints[] = ['x' => $px, 'y' => $py, 'info' => $dVal];
                  $dayIdx++;
                }

                $pathD = '';
                $areaD = '';
                if (!empty($linePoints)) {
                  $firstP = $linePoints[0];
                  $pathD = "M {$firstP['x']} {$firstP['y']}";
                  $areaD = "M {$firstP['x']} {$firstP['y']}";
                  for ($i = 1; $i < count($linePoints); $i++) {
                    $pathD .= " L {$linePoints[$i]['x']} {$linePoints[$i]['y']}";
                    $areaD .= " L {$linePoints[$i]['x']} {$linePoints[$i]['y']}";
                  }
                  $lastP = end($linePoints);
                  $areaD .= " L {$lastP['x']} 130 L {$firstP['x']} 130 Z";
                }
                ?>

                <!-- Area Gradient -->
                <path d="<?= $areaD ?>" fill="url(#gradLineArea)"></path>
                <!-- Line -->
                <path d="<?= $pathD ?>" fill="none" stroke="#174d2f" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"></path>

                <!-- Data Points & Labels -->
                <?php foreach ($linePoints as $lp): 
                  $isPk = ($lp['info']['pax'] > 0 && $lp['info']['pax'] === $peakDayPax);
                  $ptCircleFill = $isPk ? '#f59e0b' : '#ffffff';
                  $ptCircleStroke = $isPk ? '#b45309' : '#174d2f';
                  $ptRadius = $isPk ? 5.5 : 4;
                ?>
                <g class="occ-pt-group" tabindex="0">
                  <title><?= htmlspecialchars($lp['info']['dayName']) ?>, <?= htmlspecialchars($lp['info']['dateStr']) ?>: <?= $lp['info']['pax'] ?> PAX (<?= $lp['info']['pct'] ?>%)</title>
                  <circle cx="<?= $lp['x'] ?>" cy="<?= $lp['y'] ?>" r="<?= $ptRadius ?>" fill="<?= $ptCircleFill ?>" stroke="<?= $ptCircleStroke ?>" stroke-width="2"></circle>
                  <text x="<?= $lp['x'] ?>" y="<?= max(18, $lp['y'] - 6) ?>" font-size="8.5" font-weight="<?= $isPk ? '800' : '700' ?>" fill="<?= $isPk ? '#b45309' : '#174d2f' ?>" text-anchor="middle"><?= $lp['info']['pct'] ?>%</text>
                  <text x="<?= $lp['x'] ?>" y="142" font-size="8" font-weight="700" fill="#2d4233" text-anchor="middle"><?= htmlspecialchars($lp['info']['dayName']) ?></text>
                  <text x="<?= $lp['x'] ?>" y="153" font-size="7" fill="<?= $lp['info']['isToday'] ? '#174d2f' : '#698572' ?>" font-weight="<?= $lp['info']['isToday'] ? '700' : '400' ?>" text-anchor="middle"><?= htmlspecialchars($lp['info']['dateStr']) ?><?= $lp['info']['isToday'] ? ' *' : '' ?></text>
                </g>
                <?php endforeach; ?>

                <defs>
                  <linearGradient id="gradLineArea" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#174d2f" stop-opacity="0.22"></stop>
                    <stop offset="100%" stop-color="#174d2f" stop-opacity="0.01"></stop>
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>
        </div>

        <!-- 4. Detailed Facility Asset Breakdown Table (Dynamic Live Data) -->
        <div class="occ-breakdown-section">
          <h5 style="margin: 0 0 0.55rem; font-size: 0.88rem; font-weight: 800; color: #174d2f; display: flex; align-items: center; justify-content: space-between;">
            <span>Monitored Facility Utilization Breakdown</span>
            <span id="occBreakdownCount" style="font-size: 0.72rem; font-weight: 600; color: #60796b;">Total <?= count($facilityOccupancyMap) ?> Institutional Assets</span>
          </h5>

          <div class="occ-table-wrap">
            <table class="occ-breakdown-table">
              <thead>
                <tr>
                  <th>Facility / Venue</th>
                  <th>Category</th>
                  <th>Capacity</th>
                  <th>Booked Headcount</th>
                  <th>Occupancy Rate</th>
                  <th>Current Availability Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($facilityOccupancyMap)): ?>
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 1.5rem; color: #627b6c;">No facilities recorded.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($facilityOccupancyMap as $fItem): 
                    $isGuesthouse = ($fItem['type'] === 'guesthouses' || $fItem['type'] === 'guesthouse' || stripos($fItem['name'], 'guesthouse') !== false);
                    $isDorm = !$isGuesthouse && ($fItem['type'] === 'dorms' || $fItem['type'] === 'dormitories');
                    
                    if ($isGuesthouse) {
                      $tagClass = 'guesthouse';
                      $tagLabel = 'GuestHouse';
                    } elseif ($isDorm) {
                      $tagClass = 'dorm';
                      $tagLabel = 'Dormitory Suite';
                    } else {
                      $tagClass = 'hall';
                      $tagLabel = 'Function Hall';
                    }

                    $capStr = $isDorm ? ($fItem['capacity'] . ' PAX (' . $fItem['beds'] . ' Beds)') : ($fItem['capacity'] . ' PAX');
                    $bookedStr = $isDorm ? ($fItem['booked_pax'] . ' Beds') : ($fItem['booked_pax'] . ' PAX');
                    $barFillColor = $isGuesthouse ? '#0d9488' : ($isDorm ? '#d97706' : '#174d2f');
                    
                    $statusBadge = '<span class="occ-status-badge active">' . htmlspecialchars($fItem['stateLabel']) . '</span>';
                    if ($fItem['stateKey'] === 'maintenance') {
                      $statusBadge = '<span class="occ-status-badge high">Maintenance Hold</span>';
                    } elseif ($fItem['stateKey'] === 'available') {
                      $statusBadge = '<span class="occ-status-badge active">Available</span>';
                    } elseif ($fItem['stateKey'] === 'occupied') {
                      $statusBadge = '<span class="occ-status-badge dorm">' . htmlspecialchars($fItem['stateLabel']) . '</span>';
                    } elseif ($fItem['stateKey'] === 'reserved') {
                      $statusBadge = '<span class="occ-status-badge" style="background:#fffbeb; color:#b45309; border:1px solid #fde68a;">' . htmlspecialchars($fItem['stateLabel']) . '</span>';
                    }
                  ?>
                  <tr class="occ-breakdown-row" data-category="<?= $tagClass ?>">
                    <td><strong><?= htmlspecialchars($fItem['name']) ?></strong></td>
                    <td><span class="occ-tag <?= $tagClass ?>"><?= $tagLabel ?></span></td>
                    <td><?= $capStr ?></td>
                    <td><strong><?= $bookedStr ?></strong></td>
                    <td>
                      <div class="occ-tbl-rate">
                        <div class="occ-tbl-bar">
                          <div class="occ-tbl-fill" style="width: <?= min(100, $fItem['occupancy_pct']) ?>%; background: <?= $barFillColor ?>;"></div>
                        </div>
                        <span style="font-weight: 700; min-width: 42px;"><?= $fItem['occupancy_pct'] ?>%</span>
                      </div>
                    </td>
                    <td><?= $statusBadge ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeOccupancyModal()" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;">Close Analytics</button>
        <button type="button" class="btn-export-analytics" onclick="exportOccupancyReport()"
          style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.95rem; background: #174d2f; color: #ffffff; border: none; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Export Analytics Report</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       REAL-TIME FACILITY UTILIZATION & VALUATION ANALYTICS MODAL
       ========================================================================== -->
  <div class="admin-modal-overlay" id="utilizationModal" style="display: none;" onclick="if(event.target === this) closeUtilizationModal()">
    <div class="admin-modal-card occupancy-modal-wide">
      <div class="admin-modal-header" style="background: linear-gradient(135deg, #0d3820 0%, #155734 100%);">
        <div class="modal-header-lead">
          <div class="modal-header-icon" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M8.5 17V7h4.5a3 3 0 0 1 0 6H8.5"></path>
              <line x1="6" y1="9.5" x2="15.5" y2="9.5"></line>
              <line x1="6" y1="12" x2="15.5" y2="12"></line>
            </svg>
          </div>
          <div class="modal-header-title">
            <h4 style="color: #ffffff;">Estimated Facility Utilization &amp; Value Analytics</h4>
            <p style="color: #a7f3d0;">Official institutional monetary valuation, category shares, and active reservation breakdown</p>
          </div>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeUtilizationModal()" aria-label="Close dialog" title="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#173822" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body occupancy-modal-scroll">
        <!-- 1. Top Summary Metric Cards -->
        <div class="occ-metrics-grid" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
          <div class="occ-metric-card primary active" id="utilCardAll" onclick="filterUtilizationCategory('all')" role="button" tabindex="0" title="Click to view all facility valuations">
            <span class="occ-metric-lbl">Total Estimated Value</span>
            <div class="occ-metric-val" style="color: #174d2f;">₱<?= $estimatedUtilization ?></div>
            <div class="occ-metric-sub">Across <?= count($utilizationReservationsList) ?> Active Requests</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: 100%; background: #174d2f;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="utilCardHall" onclick="filterUtilizationCategory('hall')" role="button" tabindex="0" title="Click to filter by Function Halls">
            <span class="occ-metric-lbl">Function Halls</span>
            <div class="occ-metric-val" style="color: #1d4ed8;">₱<?= number_format($utilizationHalls) ?></div>
            <div class="occ-metric-sub"><?= $hallsUtilPct ?>% Portfolio Share</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $hallsUtilPct) ?>%; background: #2563eb;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="utilCardDorm" onclick="filterUtilizationCategory('dorm')" role="button" tabindex="0" title="Click to filter by Dormitory Suites">
            <span class="occ-metric-lbl">Dormitory Suites</span>
            <div class="occ-metric-val" style="color: #b45309;">₱<?= number_format($utilizationDorms) ?></div>
            <div class="occ-metric-sub"><?= $dormsUtilPct ?>% Portfolio Share</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $dormsUtilPct) ?>%; background: #d97706;"></div>
            </div>
          </div>

          <div class="occ-metric-card" id="utilCardGuesthouse" onclick="filterUtilizationCategory('guesthouse')" role="button" tabindex="0" title="Click to filter by Guesthouses &amp; Villas">
            <span class="occ-metric-lbl">Guesthouses &amp; Villas</span>
            <div class="occ-metric-val" style="color: #0f766e;">₱<?= number_format($utilizationGuesthouses) ?></div>
            <div class="occ-metric-sub"><?= $ghUtilPct ?>% Portfolio Share</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: <?= min(100, $ghUtilPct) ?>%; background: #0d9488;"></div>
            </div>
          </div>
        </div>

        <!-- 2. Value Distribution Visual Progress Bar -->
        <div style="background: #f8faf9; border: 1.5px solid #dce8e0; border-radius: 12px; padding: 0.85rem 1rem; margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.55rem;">
            <span style="font-size: 0.8rem; font-weight: 700; color: #153823;">Asset Category Valuation Distribution</span>
            <span style="font-size: 0.75rem; font-weight: 600; color: #587363;">100% Monetized Portfolio Share</span>
          </div>
          <div style="height: 12px; background: #e5ede7; border-radius: 9999px; overflow: hidden; display: flex; width: 100%; box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);">
            <div style="width: <?= $hallsUtilPct ?>%; background: #2563eb; transition: width 0.6s ease;" title="Function Halls: ₱<?= number_format($utilizationHalls) ?> (<?= $hallsUtilPct ?>%)"></div>
            <div style="width: <?= $dormsUtilPct ?>%; background: #d97706; transition: width 0.6s ease;" title="Dormitories: ₱<?= number_format($utilizationDorms) ?> (<?= $dormsUtilPct ?>%)"></div>
            <div style="width: <?= $ghUtilPct ?>%; background: #0d9488; transition: width 0.6s ease;" title="Guesthouses: ₱<?= number_format($utilizationGuesthouses) ?> (<?= $ghUtilPct ?>%)"></div>
          </div>
          <div style="display: flex; gap: 1.25rem; margin-top: 0.6rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.74rem; font-weight: 600; color: #374151;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; display: inline-block;"></span>
              <span>Function Halls: <strong>₱<?= number_format($utilizationHalls) ?></strong> (<?= $hallsUtilPct ?>%)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.74rem; font-weight: 600; color: #374151;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #d97706; display: inline-block;"></span>
              <span>Dormitory Suites: <strong>₱<?= number_format($utilizationDorms) ?></strong> (<?= $dormsUtilPct ?>%)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.74rem; font-weight: 600; color: #374151;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #0d9488; display: inline-block;"></span>
              <span>Guesthouses &amp; Villas: <strong>₱<?= number_format($utilizationGuesthouses) ?></strong> (<?= $ghUtilPct ?>%)</span>
            </div>
          </div>
        </div>

        <!-- 3. Detailed Utilization Breakdown Ledger Table -->
        <div class="occ-breakdown-section">
          <h5 style="margin: 0 0 0.55rem; font-size: 0.88rem; font-weight: 800; color: #174d2f; display: flex; align-items: center; justify-content: space-between;">
            <span>Individual Reservation Valuation Ledger</span>
            <span id="utilBreakdownCount" style="font-size: 0.72rem; font-weight: 600; color: #60796b;">Total <?= count($utilizationReservationsList) ?> Utilization Records</span>
          </h5>

          <div class="occ-table-wrap">
            <table class="occ-breakdown-table">
              <thead>
                <tr>
                  <th>Reference # &amp; Event</th>
                  <th>Facility &amp; Category</th>
                  <th>Booking Schedule</th>
                  <th>Standard Rate</th>
                  <th>Duration &amp; Formula</th>
                  <th style="text-align: right;">Estimated Value</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($utilizationReservationsList)): ?>
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #627b6c;">No active reservation utilizations recorded.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($utilizationReservationsList as $uItem): 
                    $tagClass = $uItem['category'];
                    $tagLabel = $uItem['categoryLabel'];
                  ?>
                  <tr class="util-breakdown-row" data-category="<?= $tagClass ?>" data-val="<?= $uItem['value'] ?>">
                    <td>
                      <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                        <strong style="color: #174d2f; font-family: monospace; font-size: 0.84rem;"><?= htmlspecialchars($uItem['ref']) ?></strong>
                        <span style="color: #4b6354; font-size: 0.76rem;"><?= htmlspecialchars($uItem['eventTitle']) ?></span>
                        <span style="color: #839b8c; font-size: 0.7rem;"><?= htmlspecialchars($uItem['agency']) ?></span>
                      </div>
                    </td>
                    <td>
                      <div style="display: flex; flex-direction: column; gap: 0.25rem; align-items: flex-start;">
                        <strong><?= htmlspecialchars($uItem['venue']) ?></strong>
                        <span class="occ-tag <?= $tagClass ?>"><?= $tagLabel ?></span>
                      </div>
                    </td>
                    <td>
                      <span style="font-size: 0.78rem; color: #2d4233;"><?= htmlspecialchars($uItem['schedule']) ?></span>
                    </td>
                    <td>
                      <span style="font-weight: 700; color: #174d2f; font-size: 0.8rem;"><?= $uItem['rateDisplay'] ?></span>
                    </td>
                    <td>
                      <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                        <span style="font-weight: 600; color: #1b4d2e; font-size: 0.78rem;"><?= $uItem['durationStr'] ?></span>
                        <span style="font-size: 0.7rem; color: #698572; font-family: monospace;"><?= $uItem['formulaStr'] ?></span>
                      </div>
                    </td>
                    <td style="text-align: right;">
                      <span style="font-size: 0.96rem; font-weight: 800; color: #0f766e;"><?= $uItem['valueFormatted'] ?></span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeUtilizationModal()" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;">Close Utilization</button>
        <button type="button" class="btn-export-analytics" onclick="exportUtilizationReport()"
          style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.95rem; background: #0f766e; color: #ffffff; border: none; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Export Utilization Report</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       TOAST NOTIFICATION CONTAINER
       ========================================================================== -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script>
    window.initialReservationsData = <?= json_encode((object)$reservationsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/admin_reservations.js?v=<?php echo time(); ?>"></script>
</body>

</html>
