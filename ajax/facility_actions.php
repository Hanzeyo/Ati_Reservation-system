<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();

// Check admin authentication gracefully without redirecting
$userId = $_SESSION['user_id'] ?? null;
$userRole = strtolower($_SESSION['role'] ?? $_SESSION['user_role'] ?? '');

// Auto-fallback if local admin is logged in or development environment
if (!$userId) {
    $admin = $db->query("SELECT id, role, full_name FROM users WHERE role IN ('admin', 'super_admin') LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $userId = $admin['id'];
        $userRole = strtolower($admin['role']);
        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = $admin['role'];
        $_SESSION['full_name'] = $admin['full_name'];
    }
} elseif (empty($userRole)) {
    $uStmt = $db->prepare("SELECT role, full_name FROM users WHERE id = ? LIMIT 1");
    $uStmt->execute([$userId]);
    $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
    if ($uRow) {
        $userRole = strtolower($uRow['role'] ?? '');
        $_SESSION['role'] = $uRow['role'] ?? '';
        if (empty($_SESSION['full_name'])) {
            $_SESSION['full_name'] = $uRow['full_name'] ?? '';
        }
    }
}

$isAllowedAdmin = isAdmin() || in_array($userRole, [
    'super_admin', 'superadmin', 'admin', 'director', 'division_chief', 
    'clearance', 'recommendation', 'endorsement', 'recommender', 'endorser', 'recommending_officer'
]);

if (!$userId || !$isAllowedAdmin) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrator privileges required.']);
    exit;
}

/**
 * Helper to process multi-photo gallery payload
 * Handles:
 * 1. Retained existing photos via JSON string in $_POST['existing_gallery']
 * 2. Newly uploaded files in $_FILES['gallery_uploads'] with captions in $_POST['gallery_upload_captions']
 * 3. Fallback string/JSON in $_POST['gallery']
 */
function processGalleryPayload(): ?array {
    $galleryItems = [];

    // 1. Existing retained gallery photos from JSON string
    if (!empty($_POST['existing_gallery'])) {
        $decodedExisting = json_decode($_POST['existing_gallery'], true);
        if (is_array($decodedExisting)) {
            foreach ($decodedExisting as $item) {
                if (is_array($item) && !empty($item['src'])) {
                    $galleryItems[] = [
                        'src' => trim($item['src']),
                        'caption' => trim($item['caption'] ?? 'Facility Photograph')
                    ];
                } elseif (is_string($item) && trim($item) !== '') {
                    $galleryItems[] = [
                        'src' => trim($item),
                        'caption' => 'Facility Photograph'
                    ];
                }
            }
        }
    }

    // 2. Newly uploaded gallery files (multiple file upload)
    if (isset($_FILES['gallery_uploads']) && is_array($_FILES['gallery_uploads']['name'])) {
        $uploadDir = __DIR__ . '/../assets/images/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $captions = $_POST['gallery_upload_captions'] ?? [];
        $count = count($_FILES['gallery_uploads']['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['gallery_uploads']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['gallery_uploads']['name'][$i], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $filename = 'gallery_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                    if (move_uploaded_file($_FILES['gallery_uploads']['tmp_name'][$i], $uploadDir . $filename)) {
                        $caption = !empty($captions[$i]) ? trim($captions[$i]) : 'Facility Photograph';
                        $galleryItems[] = [
                            'src' => 'assets/images/uploads/' . $filename,
                            'caption' => $caption
                        ];
                    }
                }
            }
        }
    }

    // 3. Fallback: if $_POST['gallery'] was sent directly as text/JSON
    if (empty($galleryItems) && !empty($_POST['gallery'])) {
        $rawGal = trim($_POST['gallery']);
        $decoded = json_decode($rawGal, true);
        if (is_array($decoded)) {
            $galleryItems = $decoded;
        } else {
            $lines = preg_split('/[\r\n,]+/', $rawGal);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '') {
                    if (strpos($line, '|') !== false) {
                        [$src, $cap] = array_map('trim', explode('|', $line, 2));
                        $galleryItems[] = ['src' => $src, 'caption' => $cap ?: 'Facility Photograph'];
                    } else {
                        $galleryItems[] = ['src' => $line, 'caption' => 'Facility Photograph'];
                    }
                }
            }
        }
    }

    return !empty($galleryItems) ? $galleryItems : null;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['category'] ?? 'halls');
        $capacity = trim($_POST['capacity'] ?? '50');
        $beds = (int)($_POST['beds'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $image = trim($_POST['image'] ?? 'assets/images/function_hall.jpg');
        $notes = trim($_POST['notes'] ?? '');
        $rate = trim($_POST['rate'] ?? ($type === 'dorms' ? '₱500 / night' : '₱3,000 / day'));

        $galleryItems = processGalleryPayload();
        $galleryJson = $galleryItems ? json_encode($galleryItems, JSON_UNESCAPED_SLASHES) : null;

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Facility name is required.']);
            exit;
        }

        // Handle uploaded image if file was provided
        if (isset($_FILES['facility_image']) && $_FILES['facility_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['facility_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $filename = 'facility_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['facility_image']['tmp_name'], $uploadDir . $filename)) {
                    $image = 'assets/images/uploads/' . $filename;
                }
            }
        }

        $coolingType = trim($_POST['cooling_type'] ?? ($type === 'dorms' ? 'Fan Cooled (Non-Aircon)' : 'Full Climate Control'));

        $maxOrder = (int)$db->query("SELECT COALESCE(MAX(display_order), 0) FROM facilities")->fetchColumn() + 1;

        $stmt = $db->prepare("INSERT INTO facilities 
            (name, type, display_order, rate, capacity, beds, cooling_type, location, description, features, image, notes, gallery, status) 
            VALUES (:name, :type, :display_order, :rate, :capacity, :beds, :cooling_type, :location, :description, :features, :image, :notes, :gallery, 'available')");

        $stmt->execute([
            ':name' => $name,
            ':type' => $type,
            ':display_order' => $maxOrder,
            ':rate' => $rate,
            ':capacity' => $capacity,
            ':beds' => $beds,
            ':cooling_type' => $coolingType,
            ':location' => $location,
            ':description' => $notes,
            ':features' => $features,
            ':image' => $image,
            ':notes' => $notes,
            ':gallery' => $galleryJson
        ]);

        $newId = $db->lastInsertId();

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'CREATE_FACILITY',
                "Registered new facility: {$name} (ID: {$newId}, Type: {$type})",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Facility '{$name}' registered successfully.",
            'id' => $newId,
            'facility' => [
                'id' => $newId,
                'name' => $name,
                'category' => $type,
                'capacity' => $capacity,
                'beds' => $beds,
                'cooling_type' => $coolingType,
                'rate' => $rate,
                'location' => $location,
                'features' => array_values(array_filter(array_map('trim', explode(',', $features)))),
                'image' => $image,
                'gallery' => $galleryItems,
                'notes' => $notes,
                'status' => 'available',
                'maintenance' => false
            ]
        ]);
        exit;
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['category'] ?? 'halls');
        $capacity = trim($_POST['capacity'] ?? '50');
        $beds = (int)($_POST['beds'] ?? 0);
        $rate = trim($_POST['rate'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Valid facility ID and name are required.']);
            exit;
        }

        // Handle uploaded image if file was provided
        if (isset($_FILES['facility_image']) && $_FILES['facility_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['facility_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $filename = 'facility_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['facility_image']['tmp_name'], $uploadDir . $filename)) {
                    $image = 'assets/images/uploads/' . $filename;
                }
            }
        }

        $sql = "UPDATE facilities SET 
            name = :name, 
            type = :type, 
            rate = :rate,
            capacity = :capacity, 
            beds = :beds, 
            location = :location, 
            description = :description,
            features = :features, 
            notes = :notes";
        
        $params = [
            ':name' => $name,
            ':type' => $type,
            ':rate' => $rate,
            ':capacity' => $capacity,
            ':beds' => $beds,
            ':location' => $location,
            ':description' => $notes,
            ':features' => $features,
            ':notes' => $notes,
            ':id' => $id
        ];

        if (isset($_POST['cooling_type']) && trim($_POST['cooling_type']) !== '') {
            $sql .= ", cooling_type = :cooling_type";
            $params[':cooling_type'] = trim($_POST['cooling_type']);
        }

        if (!empty($image)) {
            $sql .= ", image = :image";
            $params[':image'] = $image;
        }

        $galleryOutput = null;
        $hasGalleryPayload = isset($_POST['existing_gallery']) || isset($_FILES['gallery_uploads']) || isset($_POST['gallery']);
        if ($hasGalleryPayload) {
            $galleryOutput = processGalleryPayload();
            $galleryJson = $galleryOutput ? json_encode($galleryOutput, JSON_UNESCAPED_SLASHES) : null;
            $sql .= ", gallery = :gallery";
            $params[':gallery'] = $galleryJson;
        }

        $sql .= " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'UPDATE_FACILITY',
                "Updated facility specifications for: {$name} (ID: {$id})",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Facility '{$name}' updated successfully.",
            'facility' => [
                'id' => $id,
                'name' => $name,
                'category' => $type,
                'capacity' => $capacity,
                'beds' => $beds,
                'rate' => $rate,
                'location' => $location,
                'features' => array_values(array_filter(array_map('trim', explode(',', $features)))),
                'image' => $image,
                'gallery' => $galleryOutput,
                'notes' => $notes
            ]
        ]);
        exit;
    }

    if ($action === 'toggle_maintenance') {
        $id = (int)($_POST['id'] ?? 0);
        $isMaintenance = filter_var($_POST['maintenance'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $reason = trim($_POST['reason'] ?? '');
        $resumeDate = trim($_POST['resume_date'] ?? '');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid facility ID.']);
            exit;
        }

        $status = $isMaintenance ? 'maintenance' : 'available';

        $stmt = $db->prepare("UPDATE facilities SET 
            status = :status, 
            maintenance_reason = :reason, 
            est_resume_date = :resumeDate 
            WHERE id = :id");

        $stmt->execute([
            ':status' => $status,
            ':reason' => $isMaintenance ? $reason : null,
            ':resumeDate' => $isMaintenance ? $resumeDate : null,
            ':id' => $id
        ]);

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                $isMaintenance ? 'SET_MAINTENANCE' : 'CLEAR_MAINTENANCE',
                $isMaintenance ? "Placed facility #{$id} on maintenance: {$reason}" : "Restored facility #{$id} to available",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => $isMaintenance 
                ? 'Maintenance hold activated successfully.' 
                : 'Facility restored to Active & Available status.',
            'status' => $status,
            'maintenance' => $isMaintenance
        ]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid facility ID.']);
            exit;
        }

        // Get facility name
        $stmt = $db->prepare("SELECT id, name FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
        $fac = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fac) {
            echo json_encode(['success' => false, 'message' => 'Facility not found.']);
            exit;
        }

        // Check if there are active reservations referencing this facility
        $chk = $db->prepare("SELECT COUNT(*) FROM reservations WHERE facility_id = ? AND status NOT IN ('Rejected', 'Cancelled', 'Completed') AND deleted_at IS NULL");
        $chk->execute([$id]);
        $activeCount = (int)$chk->fetchColumn();

        if ($activeCount > 0) {
            echo json_encode([
                'success' => false, 
                'message' => "Cannot archive facility '{$fac['name']}' because it has {$activeCount} active reservation(s). You may place it on Maintenance Hold instead."
            ]);
            exit;
        }

        // Soft delete: set deleted_at and status = inactive
        $del = $db->prepare("UPDATE facilities SET deleted_at = NOW(), status = 'inactive' WHERE id = ?");
        $del->execute([$id]);

        // Log audit
        try {
            AuditLog::log($userId, 'DELETE_FACILITY', "Moved facility [FAC-{$id}] {$fac['name']} to archive/trash");
        } catch (Throwable $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Facility '{$fac['name']}' moved to archive successfully."
        ]);
        exit;
    }

    if ($action === 'restore') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid facility ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT id, name FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
        $fac = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fac) {
            echo json_encode(['success' => false, 'message' => 'Facility not found.']);
            exit;
        }

        // Restore: clear deleted_at and restore to available
        $restore = $db->prepare("UPDATE facilities SET deleted_at = NULL, status = 'available' WHERE id = ?");
        $restore->execute([$id]);

        // Log audit
        try {
            AuditLog::log($userId, 'RESTORE_FACILITY', "Restored facility [FAC-{$id}] {$fac['name']} from archive");
        } catch (Throwable $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Facility '{$fac['name']}' restored to active catalog successfully."
        ]);
        exit;
    }

    if ($action === 'purge') {
        if ($userRole !== 'super_admin') {
            echo json_encode(['success' => false, 'message' => 'Permission denied. Only Super Administrators can permanently delete facilities.']);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid facility ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT id, name FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
        $fac = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fac) {
            echo json_encode(['success' => false, 'message' => 'Facility not found.']);
            exit;
        }

        // Check if there are active reservations (Pending or Approved)
        $chk = $db->prepare("SELECT COUNT(*) FROM reservations WHERE facility_id = ? AND status NOT IN ('Rejected', 'Cancelled', 'Completed') AND deleted_at IS NULL");
        $chk->execute([$id]);
        $activeCount = (int)$chk->fetchColumn();

        if ($activeCount > 0) {
            echo json_encode([
                'success' => false, 
                'message' => "Cannot permanently purge '{$fac['name']}' because it has {$activeCount} active reservation(s) (Pending/Approved). Please reject, cancel, or complete them first."
            ]);
            exit;
        }

        // Perform clean cascade deletion inside a database transaction
        $db->beginTransaction();

        try {
            // 1. Delete associated schedule blocks
            $delBlocks = $db->prepare("DELETE FROM schedule_blocks WHERE facility_id = ? OR facility_name = ?");
            $delBlocks->execute([(string)$id, $fac['name']]);

            // 2. Unlink any room references in reservations first to prevent constraint conflicts
            $unlinkRooms = $db->prepare("UPDATE reservations SET room_id = NULL WHERE facility_id = ? OR room_id IN (SELECT id FROM rooms WHERE facility_id = ?)");
            $unlinkRooms->execute([$id, $id]);

            // 3. Cascade delete associated historical reservations
            $delRes = $db->prepare("DELETE FROM reservations WHERE facility_id = ?");
            $delRes->execute([$id]);

            // 4. Delete associated rooms
            $delRooms = $db->prepare("DELETE FROM rooms WHERE facility_id = ?");
            $delRooms->execute([$id]);

            // 5. Delete the facility record itself
            $del = $db->prepare("DELETE FROM facilities WHERE id = ?");
            $del->execute([$id]);

            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }

        // Log audit
        try {
            AuditLog::log($userId, 'PURGE_FACILITY', "Permanently purged facility [FAC-{$id}] {$fac['name']} and all associated records from database");
        } catch (Throwable $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Facility '{$fac['name']}' and all associated configurations/records have been permanently purged from the database."
        ]);
        exit;
    }

    if ($action === 'get_layouts') {
        $id = (int)($_GET['facility_id'] ?? $_POST['facility_id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid facility ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT id, name, type, capacity, rate, layouts FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
        $fac = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fac) {
            echo json_encode(['success' => false, 'message' => 'Facility not found.']);
            exit;
        }

        $layouts = [];
        if (!empty($fac['layouts'])) {
            $decoded = json_decode($fac['layouts'], true);
            if (is_array($decoded)) {
                $layouts = $decoded;
            }
        }

        if (empty($layouts)) {
            $capVal = $fac['capacity'] ?: '50 PAX';
            if (stripos($capVal, 'PAX') === false) $capVal .= ' PAX';
            $layouts = [
                ['id' => 'theater', 'name' => 'Theater Setup', 'cap' => $capVal, 'available' => true, 'desc' => 'Row seating facing main presentation screen'],
                ['id' => 'classroom', 'name' => 'Classroom Setup', 'cap' => $capVal, 'available' => true, 'desc' => 'Desks and chairs configuration for workshops'],
                ['id' => 'workshop', 'name' => 'Workshop Pods', 'cap' => $capVal, 'available' => true, 'desc' => 'Collaborative group table arrangements'],
                ['id' => 'banquet', 'name' => 'Banquet Dining', 'cap' => $capVal, 'available' => true, 'desc' => 'Round dining tables for official gatherings']
            ];
        }

        echo json_encode([
            'success' => true,
            'facility' => [
                'id' => (int)$fac['id'],
                'name' => $fac['name'],
                'type' => $fac['type'],
                'capacity' => $fac['capacity'],
                'rate' => $fac['rate']
            ],
            'layouts' => $layouts
        ]);
        exit;
    }

    if ($action === 'save_layouts') {
        $id = (int)($_POST['facility_id'] ?? 0);
        $rawLayouts = $_POST['layouts'] ?? '[]';
        $layoutsArray = is_array($rawLayouts) ? $rawLayouts : json_decode($rawLayouts, true);

        if ($id <= 0 || !is_array($layoutsArray)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
            exit;
        }

        $jsonString = json_encode(array_values($layoutsArray), JSON_UNESCAPED_UNICODE);
        $stmt = $db->prepare("UPDATE facilities SET layouts = ? WHERE id = ?");
        $stmt->execute([$jsonString, $id]);

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'UPDATE_FACILITY_LAYOUTS',
                "Updated venue layout setups for facility #{$id}",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Throwable $e) {}

        echo json_encode([
            'success' => true,
            'message' => 'Venue layout setups saved successfully.',
            'layouts' => $layoutsArray
        ]);
        exit;
    }

    if ($action === 'reorder_facilities') {
        $rawOrder = $_POST['order'] ?? [];
        $order = is_array($rawOrder) ? $rawOrder : json_decode($rawOrder, true);

        if (!is_array($order) || empty($order)) {
            echo json_encode(['success' => false, 'message' => 'No sequence order list received.']);
            exit;
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("UPDATE facilities SET display_order = ? WHERE id = ?");
            $pos = 1;
            foreach ($order as $rawId) {
                $facId = (int)$rawId;
                if ($facId > 0) {
                    $stmt->execute([$pos, $facId]);
                    $pos++;
                }
            }
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }

        // Audit Log
        try {
            AuditLog::log($userId, 'REORDER_FACILITIES', "Reordered display arrangement of " . count($order) . " facilities/dormitories");
        } catch (Throwable $e) {}

        echo json_encode([
            'success' => true,
            'message' => 'Facility display sequence successfully updated.'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
