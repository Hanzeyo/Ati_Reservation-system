<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    $query = "SELECT r.*, 
                     f.name AS facility_name, 
                     f.type AS facility_type, 
                     f.image AS facility_image,
                     rm.room_number, 
                     rm.floor AS room_floor,
                     u.full_name, 
                     u.office_agency,
                     u_end.full_name AS endorser_name,
                     u_rec.full_name AS recommender_name,
                     u_clr.full_name AS clearer_name
              FROM reservations r
              LEFT JOIN facilities f ON r.facility_id = f.id
              LEFT JOIN rooms rm ON r.room_id = rm.id
              LEFT JOIN users u ON r.user_id = u.id
              LEFT JOIN users u_end ON r.endorsed_by = u_end.id
              LEFT JOIN users u_rec ON r.recommended_by = u_rec.id
              LEFT JOIN users u_clr ON r.cleared_by = u_clr.id
              WHERE r.user_id = :user_id 
                AND r.deleted_at IS NULL
              ORDER BY r.id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $activeCount = 0;
    $historyCount = 0;
    $pendingCount = 0;
    $approvedCount = 0;
    $completedCount = 0;
    $cancelledCount = 0;

    $items = [];
    foreach ($rows as $item) {
        $rawStatus = $item['status'] ?? 'Pending';
        $isHistory = in_array($rawStatus, ['Completed', 'Cancelled', 'Rejected']);
        if ($isHistory) {
            $historyCount++;
            if ($rawStatus === 'Completed') {
                $completedCount++;
            } else {
                $cancelledCount++;
            }
        } else {
            $activeCount++;
            if ($rawStatus === 'Approved') {
                $approvedCount++;
            } else {
                $pendingCount++;
            }
        }

        $isGuesthouse = ($item['facility_type'] === 'guesthouses') || str_starts_with($item['reference_no'] ?? '', 'ATI-GH');
        $isDorm = !$isGuesthouse && (($item['facility_type'] === 'dormitories') || !empty($item['room_id']));
        $category = $isGuesthouse ? 'guesthouse' : ($isDorm ? 'dormitory' : 'facility');

        $venueName = $item['facility_name'] ?? 'Facility';
        $facilityImgRaw = trim($item['facility_image'] ?? '');
        $slug = 'function-hall';
        if (stripos($venueName, 'boardroom') !== false) {
            $slug = 'boardroom';
        } elseif (stripos($venueName, 'training') !== false) {
            $slug = 'training-hall';
        } elseif (stripos($venueName, 'mess') !== false) {
            $slug = 'mess-hall';
        } elseif (stripos($venueName, 'serrano') !== false) {
            $slug = 'serrano-hall';
        } elseif ($isGuesthouse) {
            $slug = 'guesthouse';
        } elseif ($isDorm) {
            $slug = 'dormitory';
        }

        $venueThumb = '';
        if (!empty($facilityImgRaw)) {
            if (file_exists(__DIR__ . '/../' . ltrim($facilityImgRaw, '/\\'))) {
                $venueThumb = ltrim($facilityImgRaw, '/\\');
            } elseif (file_exists(__DIR__ . '/../assets/images/' . ltrim($facilityImgRaw, '/\\'))) {
                $venueThumb = 'assets/images/' . ltrim($facilityImgRaw, '/\\');
            } else {
                $venueThumb = $facilityImgRaw;
            }
        }
        if (empty($venueThumb)) {
            if (stripos($venueName, 'carandang') !== false || stripos($venueName, 'boardroom') !== false) {
                $venueThumb = 'assets/images/carandang_room.jpg';
            } elseif (stripos($venueName, 'orosa') !== false) {
                $venueThumb = 'assets/images/orosa_hall.jpg';
            } elseif (stripos($venueName, 'rdec') !== false || stripos($venueName, 'training') !== false) {
                $venueThumb = 'assets/images/rdec_training_1.jpg';
            } elseif (stripos($venueName, 'serrano') !== false) {
                $venueThumb = 'assets/images/serrano_hall.jpg';
            } elseif ($isGuesthouse) {
                $venueThumb = 'assets/images/guesthouse_1.jpg';
            } elseif ($isDorm) {
                $venueThumb = 'assets/images/dormitory.jpg';
            } else {
                $venueThumb = 'assets/images/function_hall.jpg';
            }
        }

        $startTs = strtotime($item['start_date']);
        $endTs = strtotime($item['end_date'] ?: $item['start_date']);
        $daysCount = max(1, round(($endTs - $startTs) / 86400) + 1);
        $daysSuffix = $daysCount . ($daysCount > 1 ? ' Days' : ' Day');
        if ($isGuesthouse || $isDorm) {
            $nightsCount = max(1, $daysCount - 1);
            $nightsSuffix = $daysCount === 1 ? '1 Night (Overnight)' : $nightsCount . ' Nights';
            if ($startTs === $endTs) {
                $formattedDates = date('M d, Y', $startTs) . " (Overnight)";
            } else {
                $formattedDates = date('M d', $startTs) . ' – ' . date('M d, Y', $endTs) . " ({$nightsSuffix})";
            }
        } else {
            if ($startTs === $endTs) {
                $formattedDates = date('M d, Y', $startTs) . " (1 Day)";
            } else {
                $formattedDates = date('M d', $startTs) . ' – ' . date('M d, Y', $endTs) . " ({$daysSuffix})";
            }
        }

        $filterStatus = 'pending';
        if ($rawStatus === 'Approved') {
            $filterStatus = 'approved';
            $statusBadgeClass = 'approved';
            $statusBadgeText = 'Confirmed & Approved';
        } elseif ($rawStatus === 'Completed') {
            $filterStatus = 'completed';
            $statusBadgeClass = 'completed';
            $statusBadgeText = 'Completed & Archived';
        } elseif ($rawStatus === 'Cancelled') {
            $filterStatus = 'cancelled';
            $statusBadgeClass = 'cancelled';
            $statusBadgeText = 'Cancelled by Requestor';
        } elseif ($rawStatus === 'Rejected') {
            $filterStatus = 'rejected';
            $statusBadgeClass = 'rejected';
            $statusBadgeText = 'Disapproved / Returned';
        } elseif ($rawStatus === 'Pending Recommendation') {
            $statusBadgeClass = 'pending recommendation';
            $statusBadgeText = 'Stage 2: Awaiting Recommendation';
        } elseif ($rawStatus === 'Pending Clearance') {
            $statusBadgeClass = 'pending clearance';
            $statusBadgeText = 'Stage 3: Awaiting Clearance';
        } else {
            $statusBadgeClass = 'pending endorsement';
            $statusBadgeText = 'Stage 1: Awaiting Endorsement';
        }

        $s1Done = in_array($rawStatus, ['Pending Recommendation', 'Pending Clearance', 'Approved', 'Completed']) || !empty($item['endorsed_at']);
        $s2Done = in_array($rawStatus, ['Pending Clearance', 'Approved', 'Completed']) || !empty($item['recommended_at']);
        $s3Done = in_array($rawStatus, ['Approved', 'Completed']) || !empty($item['cleared_at']);

        $isRejected = ($rawStatus === 'Rejected');
        $isCancelled = ($rawStatus === 'Cancelled');

        if ($isRejected) {
            if ($s2Done) {
                $step1State = 'done'; $step2State = 'done'; $step3State = 'rejected';
                $currentStageLabel = 'Stage 3: Disapproved'; $currentStageColor = '#dc2626';
            } elseif ($s1Done) {
                $step1State = 'done'; $step2State = 'rejected'; $step3State = 'pending';
                $currentStageLabel = 'Stage 2: Returned'; $currentStageColor = '#dc2626';
            } else {
                $step1State = 'rejected'; $step2State = 'pending'; $step3State = 'pending';
                $currentStageLabel = 'Stage 1: Returned'; $currentStageColor = '#dc2626';
            }
        } elseif ($isCancelled) {
            $step1State = $s1Done ? 'done' : 'cancelled';
            $step2State = $s2Done ? 'done' : 'cancelled';
            $step3State = $s3Done ? 'done' : 'cancelled';
            $currentStageLabel = 'Request Cancelled'; $currentStageColor = '#64748b';
        } elseif ($rawStatus === 'Approved') {
            $step1State = 'done'; $step2State = 'done'; $step3State = 'done';
            $currentStageLabel = 'Governance Clearance Complete'; $currentStageColor = '#16a34a';
        } elseif ($rawStatus === 'Completed') {
            $step1State = 'done'; $step2State = 'done'; $step3State = 'done';
            $currentStageLabel = 'Concluded & Archived'; $currentStageColor = '#475569';
        } elseif ($rawStatus === 'Pending Clearance') {
            $step1State = 'done'; $step2State = 'done'; $step3State = 'active';
            $currentStageLabel = 'Stage 3: Directorate Clearance Active'; $currentStageColor = '#047857';
        } elseif ($rawStatus === 'Pending Recommendation') {
            $step1State = 'done'; $step2State = 'active'; $step3State = 'pending';
            $currentStageLabel = 'Stage 2: Logistics Recommendation Active'; $currentStageColor = '#2563eb';
        } else {
            $step1State = 'active'; $step2State = 'pending'; $step3State = 'pending';
            $currentStageLabel = 'Stage 1: Division Endorsement Active'; $currentStageColor = '#d97706';
        }

        $unitSuffix = !empty($item['room_number']) ? ($isGuesthouse ? ' - ' . $item['room_number'] : ' - Room ' . $item['room_number']) : '';

        $items[] = [
            'id' => (int)$item['id'],
            'reference_no' => $item['reference_no'],
            'facility_id' => (int)$item['facility_id'],
            'facility_name' => $venueName,
            'facility_type' => $item['facility_type'],
            'category' => $category,
            'slug' => $slug,
            'venue_thumb' => $venueThumb,
            'venue_title' => $venueName . $unitSuffix,
            'event_title' => $item['event_title'] ?: 'Official Institutional Activity',
            'pax_text' => ($item['pax_count'] ?: 0) . (($isGuesthouse || $isDorm) ? ' Guests' : ' Attendees'),
            'pax_count' => (int)$item['pax_count'],
            'division' => $item['office_agency'] ?: 'ATI Personnel',
            'applicant_name' => $item['full_name'] ?? 'Applicant',
            'created_at_text' => !empty($item['created_at']) ? date('M d, Y • h:i A', strtotime($item['created_at'])) : 'Recently Submitted',
            'created_date' => !empty($item['created_at']) ? date('M d, Y', strtotime($item['created_at'])) : '',
            'formatted_dates' => $formattedDates,
            'time_slot' => $item['time_slot'] ?: '08:00 AM – 05:00 PM',
            'raw_status' => $rawStatus,
            'filter_status' => $filterStatus,
            'status_badge_class' => $statusBadgeClass,
            'status_badge_text' => $statusBadgeText,
            'is_history' => $isHistory,
            'is_guesthouse' => $isGuesthouse,
            'is_dorm' => $isDorm,
            'step1_state' => $step1State,
            'step2_state' => $step2State,
            'step3_state' => $step3State,
            'current_stage_label' => $currentStageLabel,
            'current_stage_color' => $currentStageColor,
            'special_notes' => $item['special_notes'] ?? '',
            'admin_remarks' => $item['admin_remarks'] ?? '',
            'document_path' => $item['document_path'] ?? '',
            'endorsed_by' => $item['endorser_name'] ?? '',
            'endorsed_at' => !empty($item['endorsed_at']) ? date('M d, Y • h:i A', strtotime($item['endorsed_at'])) : '',
            'endorsement_remarks' => $item['endorsement_remarks'] ?? '',
            'recommended_by' => $item['recommender_name'] ?? '',
            'recommended_at' => !empty($item['recommended_at']) ? date('M d, Y • h:i A', strtotime($item['recommended_at'])) : '',
            'recommendation_remarks' => $item['recommendation_remarks'] ?? '',
            'recommendation_doc_path' => $item['recommendation_doc_path'] ?? '',
            'cleared_by' => $item['clearer_name'] ?? '',
            'cleared_at' => !empty($item['cleared_at']) ? date('M d, Y • h:i A', strtotime($item['cleared_at'])) : '',
            'clearance_remarks' => $item['clearance_remarks'] ?? '',
            'clearance_doc_path' => $item['clearance_doc_path'] ?? '',
        ];
    }

    $hash = md5(json_encode($items));

    echo json_encode([
        'success' => true,
        'hash' => $hash,
        'counts' => [
            'active' => $activeCount,
            'history' => $historyCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'completed' => $completedCount,
            'cancelled' => $cancelledCount,
            'total' => count($items)
        ],
        'reservations' => $items
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error querying user reservations: ' . $e->getMessage()
    ]);
}
