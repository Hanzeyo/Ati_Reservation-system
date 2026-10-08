<?php
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BaseModel.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Facility.php';
require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/AuditLog.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$facility = new Facility($db);
$reservation = new Reservation($db);
$auditLog = new AuditLog($db);
?>
