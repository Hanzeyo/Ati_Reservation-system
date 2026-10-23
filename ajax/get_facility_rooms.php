<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();

$rawFacId = $_GET['facility_id'] ?? $_POST['facility_id'] ?? 0;
$facilityId = 0;
if (is_numeric($rawFacId)) {
    $facilityId = (int)$rawFacId;
} elseif (is_string($rawFacId) && preg_match('/(\d+)/', $rawFacId, $m)) {
    $facilityId = (int)$m[1];
}

if ($facilityId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Facility ID is required.']);
    exit;
}

$startDate = $_GET['start_date'] ?? $_GET['date'] ?? date('Y-m-d');
$endDate = $_GET['end_date'] ?? $startDate;

// Validate date format YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $startDate = date('Y-m-d');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    $endDate = $startDate;
}
if ($endDate < $startDate) {
    $endDate = $startDate;
}

try {
    // 1. Fetch Facility Info
    $facStmt = $db->prepare("SELECT id, name, type, beds, capacity, location, status FROM facilities WHERE id = ?");
    $facStmt->execute([$facilityId]);
    $facility = $facStmt->fetch(PDO::FETCH_ASSOC);

    if (!$facility) {
        echo json_encode(['success' => false, 'message' => 'Facility not found.']);
        exit;
    }

    $isFacilityUnderMaintenance = ($facility['status'] === 'maintenance');

    // 2. Fetch existing rooms
    $stmt = $db->prepare("SELECT * FROM rooms WHERE facility_id = ? ORDER BY id ASC");
    $stmt->execute([$facilityId]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Auto-seed default rooms if dormitory or guesthouse has no rooms yet
    if (empty($rooms) && ($facility['type'] === 'dorms' || $facility['type'] === 'dormitories')) {
        $defaultNums = ['101', '102', '103', '104', '105', '106'];
        $insStmt = $db->prepare("INSERT INTO rooms (facility_id, room_number, floor, status) VALUES (?, ?, '1st Floor', 'available')");
        foreach ($defaultNums as $dNum) {
            $insStmt->execute([$facilityId, $dNum]);
        }
        $stmt->execute([$facilityId]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (empty($rooms) && ($facility['type'] === 'guesthouses' || stripos($facility['name'], 'guesthouse') !== false)) {
        $defaultVillaRooms = [
            ['Master Suite (King Bed)', 'Main Villa Floor'],
            ['Deluxe Guest Room (Queen Bed)', 'Garden Wing'],
            ['Twin Bedroom (2 Single Beds)', 'Upper Wing'],
            ['Full Villa Allotment (Entire Property)', 'Entire Villa']
        ];
        $insStmt = $db->prepare("INSERT INTO rooms (facility_id, room_number, floor, status) VALUES (?, ?, ?, 'available')");
        foreach ($defaultVillaRooms as $vr) {
            $insStmt->execute([$facilityId, $vr[0], $vr[1]]);
        }
        $stmt->execute([$facilityId]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Check occupied rooms in date range
    $occStmt = $db->prepare("SELECT room_id, reference_no FROM reservations 
                             WHERE facility_id = ? 
                               AND room_id IS NOT NULL
                               AND status IN ('Approved', 'Completed', 'Endorsed', 'Recommended') 
                               AND start_date <= ? AND end_date >= ?");
    $occStmt->execute([$facilityId, $endDate, $startDate]);
    
    $occupiedById = [];
    while ($row = $occStmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['room_id'])) {
            $occupiedById[(int)$row['room_id']] = $row;
        }
    }

    // 4. Map room items
    $roomsData = array_map(function($r) use ($occupiedById, $isFacilityUnderMaintenance) {
        $rid = (int)$r['id'];
        $rnum = (string)$r['room_number'];
        $isRoomMaint = ($r['status'] === 'maintenance') || $isFacilityUnderMaintenance;
        $isOccupied = isset($occupiedById[$rid]);

        $status = 'available';
        if ($isRoomMaint) {
            $status = 'maintenance';
        } elseif ($isOccupied) {
            $status = 'reserved';
        }

        return [
            'id' => $rid,
            'num' => $rnum,
            'room_number' => $rnum,
            'floor' => $r['floor'] ?: 'Standard Floor',
            'status' => $status,
            'available' => ($status === 'available'),
            'is_maintenance' => $isRoomMaint,
            'is_reserved' => $isOccupied
        ];
    }, $rooms);

    echo json_encode([
        'success' => true,
        'facility' => [
            'id' => (int)$facility['id'],
            'name' => $facility['name'],
            'type' => $facility['type'],
            'maintenance' => $isFacilityUnderMaintenance,
            'beds' => (int)($facility['beds'] ?? 0),
            'capacity' => (int)($facility['capacity'] ?? 0),
            'location' => $facility['location'] ?? ''
        ],
        'query_dates' => [
            'start_date' => $startDate,
            'end_date' => $endDate
        ],
        'rooms' => $roomsData,
        'total_rooms' => count($roomsData)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error querying rooms: ' . $e->getMessage()
    ]);
}
