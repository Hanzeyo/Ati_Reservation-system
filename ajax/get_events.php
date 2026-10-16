<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Query all Approved reservations along with user and facility info
    $query = "SELECT 
                r.id,
                r.reference_no,
                r.event_title,
                r.start_date,
                r.end_date,
                r.time_slot,
                r.pax_count,
                r.status,
                r.special_notes,
                f.name AS facility_name,
                f.type AS facility_type,
                rm.room_number,
                u.full_name AS requester_name,
                u.office_agency AS requester_office,
                u.category AS requester_category
              FROM reservations r
              LEFT JOIN facilities f ON r.facility_id = f.id
              LEFT JOIN rooms rm ON r.room_id = rm.id
              LEFT JOIN users u ON r.user_id = u.id
              WHERE r.status = 'Approved'
                AND r.deleted_at IS NULL
              ORDER BY r.start_date ASC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = [];

    foreach ($rows as $row) {
        $facilityName = $row['facility_name'] ?: 'General Facility';
        $lowerFac = strtolower($facilityName);

        // Map facility name to front-end facilityKey for styling & filtering
        $facilityKey = 'function-hall';
        if (strpos($lowerFac, 'training') !== false) {
            $facilityKey = 'training-hall';
        } elseif (strpos($lowerFac, 'mess') !== false || strpos($lowerFac, 'dining') !== false) {
            $facilityKey = 'mess-hall';
        } elseif (strpos($lowerFac, 'board') !== false || strpos($lowerFac, 'executive') !== false) {
            $facilityKey = 'boardroom';
        } elseif (strpos($lowerFac, 'dorm') !== false || ($row['facility_type'] ?? '') === 'dormitories') {
            $facilityKey = 'dormitory';
        }

        $startDateStr = $row['start_date'];
        $endDateStr = !empty($row['end_date']) ? $row['end_date'] : $startDateStr;

        $startDate = new DateTime($startDateStr);
        $endDate = new DateTime($endDateStr);

        // Loop through multi-day ranges so events appear across every booked day
        $currentDate = clone $startDate;
        $dayIndex = 1;
        $totalDays = (int)$startDate->diff($endDate)->format("%r%a") + 1;

        while ($currentDate <= $endDate) {
            $dateFormatted = $currentDate->format('Y-m-d');
            $titleSuffix = ($totalDays > 1) ? " (Day {$dayIndex})" : "";
            $title = (!empty($row['event_title']) ? $row['event_title'] : 'Official Activity') . $titleSuffix;

            $division = $row['requester_office'] ?: ($row['requester_name'] ?: 'Agricultural Training Institute');
            $attendees = ($row['pax_count'] > 0) ? "{$row['pax_count']} Participants" : 'Official Attendees';
            $timeSlot = !empty($row['time_slot']) ? $row['time_slot'] : 'Whole Day / Regular Hours';

            $events[] = [
                'id' => (int)$row['id'] * 1000 + $dayIndex,
                'db_id' => (int)$row['id'],
                'ref' => $row['reference_no'],
                'title' => $title,
                'facility' => $facilityName . ($row['room_number'] ? " (Room {$row['room_number']})" : ""),
                'facilityKey' => $facilityKey,
                'date' => $dateFormatted,
                'time' => $timeSlot,
                'division' => $division,
                'attendees' => $attendees,
                'status' => 'Approved & Confirmed',
                'source' => 'database'
            ];

            $currentDate->modify('+1 day');
            $dayIndex++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'count' => count($events),
        'events' => $events
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'events' => []
    ]);
}
