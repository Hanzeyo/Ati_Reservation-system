<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();

// Check administrator authentication
$userId = $_SESSION['user_id'] ?? null;
$userRole = strtolower($_SESSION['role'] ?? $_SESSION['user_role'] ?? '');
$isAllowedAdmin = isAdmin() || in_array($userRole, ['superadmin', 'super_admin', 'admin']);

if (!$userId || !$isAllowedAdmin) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrator privileges required.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    // =========================================================================
    // 1. GET ROOMS FOR A FACILITY
    // =========================================================================
    if ($action === 'get_rooms') {
        $facilityId = (int)($_GET['facility_id'] ?? $_POST['facility_id'] ?? 0);
        if ($facilityId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid facility ID.']);
            exit;
        }

        // Fetch facility details
        $facStmt = $db->prepare("SELECT id, name, type, beds, capacity FROM facilities WHERE id = ?");
        $facStmt->execute([$facilityId]);
        $facility = $facStmt->fetch(PDO::FETCH_ASSOC);

        if (!$facility) {
            echo json_encode(['success' => false, 'message' => 'Facility not found.']);
            exit;
        }

        // Fetch existing rooms
        $stmt = $db->prepare("SELECT * FROM rooms WHERE facility_id = ? ORDER BY id ASC");
        $stmt->execute([$facilityId]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Auto-seed default rooms (101-106) if dormitory has zero rooms configured yet
        if (empty($rooms) && ($facility['type'] === 'dorms' || $facility['type'] === 'dormitories')) {
            $defaultNums = ['101', '102', '103', '104', '105', '106'];
            $insStmt = $db->prepare("INSERT INTO rooms (facility_id, room_number, floor, status) VALUES (?, ?, '1st Floor', 'available')");
            foreach ($defaultNums as $dNum) {
                $insStmt->execute([$facilityId, $dNum]);
            }
            // Re-fetch
            $stmt->execute([$facilityId]);
            $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Check active reservations today for each room
        $today = date('Y-m-d');
        $occStmt = $db->prepare("SELECT r.id AS reservation_id,
                                        r.room_id, 
                                        r.reference_no, 
                                        r.event_title, 
                                        r.start_date, 
                                        r.end_date, 
                                        r.time_slot, 
                                        r.pax_count,
                                        r.user_id,
                                        u.full_name AS client_name,
                                        u.contact_number AS client_contact,
                                        u.email AS client_email,
                                        u.office_agency AS client_agency
                                 FROM reservations r 
                                 LEFT JOIN users u ON r.user_id = u.id
                                 WHERE r.facility_id = ? 
                                   AND r.room_id IS NOT NULL 
                                   AND r.status = 'Approved'
                                   AND r.start_date <= ? AND r.end_date >= ?
                                   AND r.deleted_at IS NULL");
        $occStmt->execute([$facilityId, $today, $today]);
        $occupiedList = [];
        while ($row = $occStmt->fetch(PDO::FETCH_ASSOC)) {
            $occupiedList[$row['room_id']] = $row;
        }

        // Map rooms with dynamic real-time occupancy status
        $roomsData = array_map(function($r) use ($occupiedList) {
            $rid = $r['id'];
            $isMaint = ($r['status'] === 'maintenance');
            $isOccupied = isset($occupiedList[$rid]);
            
            $computedStatus = 'available';
            if ($isMaint) {
                $computedStatus = 'maintenance';
            } elseif ($isOccupied) {
                $computedStatus = 'occupied';
            }

            return [
                'id' => (int)$r['id'],
                'facility_id' => (int)$r['facility_id'],
                'room_number' => $r['room_number'],
                'floor' => $r['floor'] ?: 'Standard Floor',
                'status' => $computedStatus,
                'is_maintenance' => $isMaint,
                'is_occupied' => $isOccupied,
                'occupant' => $isOccupied ? [
                    'reservation_id' => (int)$occupiedList[$rid]['reservation_id'],
                    'booking_ref' => $occupiedList[$rid]['reference_no'],
                    'event_title' => $occupiedList[$rid]['event_title'],
                    'client_name' => $occupiedList[$rid]['client_name'] ?? 'Authorized Guest',
                    'client_contact' => $occupiedList[$rid]['client_contact'] ?? '',
                    'client_email' => $occupiedList[$rid]['client_email'] ?? '',
                    'client_agency' => $occupiedList[$rid]['client_agency'] ?? 'ATI Central Office',
                    'start_date' => $occupiedList[$rid]['start_date'],
                    'end_date' => $occupiedList[$rid]['end_date'],
                    'time_slot' => $occupiedList[$rid]['time_slot'] ?? '',
                    'pax_count' => $occupiedList[$rid]['pax_count'] ?? 1
                ] : null,
                'booking_ref' => $occupiedList[$rid]['reference_no'] ?? null
            ];
        }, $rooms);

        echo json_encode([
            'success' => true,
            'facility' => $facility,
            'rooms' => $roomsData,
            'total_rooms' => count($roomsData)
        ]);
        exit;
    }

    // =========================================================================
    // 2. ADD A NEW ROOM
    // =========================================================================
    if ($action === 'add_room') {
        $facilityId = (int)($_POST['facility_id'] ?? 0);
        $roomNumber = trim($_POST['room_number'] ?? '');
        $floor = trim($_POST['floor'] ?? '1st Floor');

        if ($facilityId <= 0 || empty($roomNumber)) {
            echo json_encode(['success' => false, 'message' => 'Facility ID at Room Number ay kailangan.']);
            exit;
        }

        // Check if room number already exists for this facility
        $chk = $db->prepare("SELECT id FROM rooms WHERE facility_id = ? AND room_number = ? LIMIT 1");
        $chk->execute([$facilityId, $roomNumber]);
        if ($chk->fetch()) {
            echo json_encode(['success' => false, 'message' => "Mayroon nang Room {$roomNumber} sa dormitory na ito."]);
            exit;
        }

        $ins = $db->prepare("INSERT INTO rooms (facility_id, room_number, floor, status) VALUES (?, ?, ?, 'available')");
        $ins->execute([$facilityId, $roomNumber, $floor]);
        $newRoomId = (int)$db->lastInsertId();

        // Audit Log
        try {
            $auditLog = new AuditLog($db);
            $auditLog->user_id = $userId;
            $auditLog->action = 'ADD_ROOM';
            $auditLog->details = "Added Room {$roomNumber} ({$floor}) to Facility #{$facilityId}";
            $auditLog->log();
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Matagumpay na naidagdag ang Room {$roomNumber}.",
            'room' => [
                'id' => $newRoomId,
                'facility_id' => $facilityId,
                'room_number' => $roomNumber,
                'floor' => $floor,
                'status' => 'available',
                'is_maintenance' => false,
                'is_occupied' => false
            ]
        ]);
        exit;
    }

    // =========================================================================
    // 3. TOGGLE ROOM MAINTENANCE STATUS
    // =========================================================================
    if ($action === 'toggle_maintenance') {
        $roomId = (int)($_POST['room_id'] ?? 0);
        $setMaintenance = filter_var($_POST['maintenance'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($roomId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid room ID.']);
            exit;
        }

        $newStatus = $setMaintenance ? 'maintenance' : 'available';

        $stmt = $db->prepare("UPDATE rooms SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $roomId]);

        // Audit Log
        try {
            $auditLog = new AuditLog($db);
            $auditLog->user_id = $userId;
            $auditLog->action = $setMaintenance ? 'ROOM_MAINTENANCE_HOLD' : 'ROOM_RESTORE_AVAILABLE';
            $auditLog->details = ($setMaintenance ? "Placed Room #{$roomId} on Maintenance Hold" : "Restored Room #{$roomId} to Available");
            $auditLog->log();
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => $setMaintenance 
                ? "Inilagay sa Maintenance Hold ang kuwarto." 
                : "Ibinalik sa Available status ang kuwarto.",
            'status' => $newStatus,
            'is_maintenance' => $setMaintenance
        ]);
        exit;
    }

    // =========================================================================
    // 4. DELETE A ROOM
    // =========================================================================
    if ($action === 'delete_room') {
        $roomId = (int)($_POST['room_id'] ?? 0);
        if ($roomId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid room ID.']);
            exit;
        }

        // Check if there are active reservations referencing this room
        $chk = $db->prepare("SELECT COUNT(*) FROM reservations WHERE room_id = ? AND status NOT IN ('Rejected', 'Cancelled', 'Completed')");
        $chk->execute([$roomId]);
        $activeCount = (int)$chk->fetchColumn();

        if ($activeCount > 0) {
            echo json_encode([
                'success' => false, 
                'message' => "Hindi maaaring burahin ang kuwartong ito dahil may {$activeCount} aktibong reservation(s) nakatala rito. Maaari itong i-set sa Maintenance Hold pansamantala."
            ]);
            exit;
        }

        $db->beginTransaction();
        try {
            // Unlink room reference from any past completed/cancelled reservations
            $unlink = $db->prepare("UPDATE reservations SET room_id = NULL WHERE room_id = ?");
            $unlink->execute([$roomId]);

            $del = $db->prepare("DELETE FROM rooms WHERE id = ?");
            $del->execute([$roomId]);

            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }

        // Audit Log
        try {
            $auditLog = new AuditLog($db);
            $auditLog->user_id = $userId;
            $auditLog->action = 'DELETE_ROOM';
            $auditLog->details = "Deleted Room #{$roomId}";
            $auditLog->log();
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => 'Matagumpay na nabura ang kuwarto.'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
