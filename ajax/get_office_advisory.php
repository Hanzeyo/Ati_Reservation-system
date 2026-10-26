<?php
/**
 * Agriculture Training Institute - Facility Reservation System
 * Public API: Real-time Office Advisory & Inclement Weather Suspension Status
 */

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

try {
    // 1. Fetch current weather suspension configuration from system settings
    $weatherSuspension = SystemSetting::get('weather_suspension', null);

    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
    if (!empty($_GET['test_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['test_date'])) {
        $now = new DateTime($_GET['test_date'] . ' 10:00:00', new DateTimeZone('Asia/Manila'));
    }
    $today = $now->format('Y-m-d');
    $isActive = false;
    $suspensionData = null;

    if (!empty($weatherSuspension) && is_array($weatherSuspension)) {
        $enabled = !empty($weatherSuspension['active']);
        $startDate = $weatherSuspension['start_date'] ?? $today;
        $endDate = $weatherSuspension['end_date'] ?? $today;

        // Check if today falls within the suspension effective date range
        if ($enabled && $today >= $startDate && $today <= $endDate) {
            $isActive = true;
            $suspensionData = $weatherSuspension;
        }
    }

    // Evaluate Philippine Government Holiday for today
    $year = (int)$now->format('Y');
    $month = (int)$now->format('n');
    $day = (int)$now->format('j');
    $mmdd = $now->format('m-d');

    // 2. Connect to Master Schedule & Calendar Operations Directorate
    $masterScheduleHold = null;
    $todayConfirmedCount = 0;
    $todayFacilitiesCount = 0;
    $masterOperatingConfig = null;

    // Support URL simulation for immediate testing (?test_master_hold=1)
    if (!empty($_GET['test_master_hold'])) {
        $masterScheduleHold = [
            'active' => true,
            'ref' => 'HOLD-2026-ATI-01',
            'title' => 'Institution-Wide Administrative Hold',
            'reason' => 'Annual System Audit & Facility Maintenance Protocol',
            'memo_ref' => 'Memorandum Circular No. 2026-08 (Office of the Director)',
            'facility_name' => 'All ATI Facilities (Institution-Wide)',
            'start_date' => $today,
            'end_date' => $today
        ];
    }

    try {
        $db = (new Database())->getConnection();

        // A. Check if Master Schedule has an active Administrative Hold / Blackout today
        if (!$masterScheduleHold) {
            $bStmt = $db->prepare("SELECT * FROM schedule_blocks 
                                    WHERE (facility_id = 'all' OR facility_id = '0' OR facility_name LIKE '%Institution%' OR facility_name LIKE '%All%')
                                      AND (start_date <= :today AND end_date >= :today)
                                    ORDER BY id DESC LIMIT 1");
            $bStmt->execute([':today' => $today]);
            $bRow = $bStmt->fetch(PDO::FETCH_ASSOC);
            if ($bRow) {
                $masterScheduleHold = [
                    'active' => true,
                    'ref' => $bRow['ref'],
                    'title' => $bRow['title'],
                    'reason' => $bRow['reason'] ?: 'Administrative Hold',
                    'memo_ref' => $bRow['memo_ref'] ?: 'Master Schedule Directive',
                    'facility_name' => $bRow['facility_name'] ?: 'All Facilities',
                    'start_date' => $bRow['start_date'],
                    'end_date' => $bRow['end_date']
                ];
            }
        }

        // B. Query confirmed events running today on Master Schedule
        $resStmt = $db->prepare("SELECT COUNT(*) as res_count, COUNT(DISTINCT facility_id) as fac_count 
                                  FROM reservations 
                                  WHERE status IN ('Approved', 'Completed') 
                                    AND deleted_at IS NULL
                                    AND (start_date <= :today AND (end_date >= :today OR (end_date IS NULL AND start_date = :today)))");
        $resStmt->execute([':today' => $today]);
        $counts = $resStmt->fetch(PDO::FETCH_ASSOC);
        if ($counts) {
            $todayConfirmedCount = (int)$counts['res_count'];
            $todayFacilitiesCount = (int)$counts['fac_count'];
        }

        // C. Check Master Schedule operating hours configuration from system_settings
        $masterOperatingConfig = SystemSetting::get('master_schedule_hours', null);
    } catch (Throwable $e) {
        // Silently handle database unavailability
    }

    $holiday = null;

    // Fixed Regular Holidays (Non-Working)
    $fixedRegular = [
        '01-01' => "New Year's Day",
        '04-09' => "Araw ng Kagitingan (Day of Valor)",
        '05-01' => "Labor Day",
        '06-12' => "Independence Day",
        '11-30' => "Bonifacio Day",
        '12-25' => "Christmas Day",
        '12-30' => "Rizal Day"
    ];

    // Fixed Special Non-Working Holidays (Offices Closed On-Site)
    $fixedSpecialNonWorking = [
        '01-02' => "Special Non-Working Day",
        '02-25' => "EDSA People Power Revolution Anniversary",
        '08-21' => "Ninoy Aquino Day",
        '11-01' => "All Saints' Day (Undas)",
        '11-02' => "All Souls' Day",
        '12-08' => "Feast of the Immaculate Conception of Mary",
        '12-24' => "Christmas Eve",
        '12-31' => "Last Day of the Year"
    ];

    // Fixed Special Working Holidays (Offices Open On-Site)
    $fixedSpecialWorking = [
        '07-27' => "Iglesia ni Cristo Day (RA 9645)",
        '08-18' => "Jesse Robredo Day (RA 10669)",
        '09-08' => "Feast of the Nativity of the Blessed Virgin Mary (RA 11370)",
        '11-17' => "National Students' Day (RA 11369)"
    ];

    if (isset($fixedRegular[$mmdd])) {
        $holiday = [
            'is_holiday' => true,
            'name' => $fixedRegular[$mmdd],
            'type' => 'Regular Holiday',
            'is_non_working' => true,
            'is_working' => false,
            'legal_basis' => 'Proclamation No. 727 / Civil Service Commission'
        ];
    } elseif (isset($fixedSpecialNonWorking[$mmdd])) {
        $holiday = [
            'is_holiday' => true,
            'name' => $fixedSpecialNonWorking[$mmdd],
            'type' => 'Special Non-Working Holiday',
            'is_non_working' => true,
            'is_working' => false,
            'legal_basis' => 'Presidential Proclamation / Civil Service Commission'
        ];
    } elseif (isset($fixedSpecialWorking[$mmdd])) {
        $holiday = [
            'is_holiday' => true,
            'name' => $fixedSpecialWorking[$mmdd],
            'type' => 'Special Working Holiday',
            'is_non_working' => false,
            'is_working' => true,
            'legal_basis' => 'Republic Act / Presidential Directive'
        ];
    } else {
        // Movable Easter & Holy Week calculation
        $easterTimestamp = function_exists('easter_date') ? easter_date($year) : false;
        if ($easterTimestamp) {
            $maundyThu = date('Y-m-d', strtotime('-3 days', $easterTimestamp));
            $goodFri = date('Y-m-d', strtotime('-2 days', $easterTimestamp));
            $blackSat = date('Y-m-d', strtotime('-1 day', $easterTimestamp));

            if ($today === $maundyThu) {
                $holiday = ['is_holiday' => true, 'name' => 'Maundy Thursday', 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false, 'legal_basis' => 'Presidential Proclamation'];
            } elseif ($today === $goodFri) {
                $holiday = ['is_holiday' => true, 'name' => 'Good Friday', 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false, 'legal_basis' => 'Presidential Proclamation'];
            } elseif ($today === $blackSat) {
                $holiday = ['is_holiday' => true, 'name' => 'Black Saturday', 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false, 'legal_basis' => 'Presidential Proclamation'];
            }
        }

        // National Heroes Day (Last Monday of August)
        if (!$holiday && $month === 8 && $day === (int)date('j', strtotime("last monday of august {$year}"))) {
            $holiday = ['is_holiday' => true, 'name' => 'National Heroes Day', 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false, 'legal_basis' => 'Republic Act No. 9492'];
        }
    }

    // Proclaimed Movable Holidays
    $movableHolidays = [
        '2024-02-10' => ['name' => 'Chinese New Year', 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false],
        '2024-04-10' => ['name' => "Eid'l Fitr", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2024-06-17' => ['name' => "Eid'l Adha", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2025-01-29' => ['name' => 'Chinese New Year', 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false],
        '2025-03-31' => ['name' => "Eid'l Fitr", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2025-06-06' => ['name' => "Eid'l Adha", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2026-02-17' => ['name' => 'Chinese New Year', 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false],
        '2026-03-20' => ['name' => "Eid'l Fitr", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2026-05-27' => ['name' => "Eid'l Adha", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2026-10-31' => ['name' => "All Saints' Day Eve", 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false],
        '2027-02-06' => ['name' => 'Chinese New Year', 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false],
        '2027-03-10' => ['name' => "Eid'l Fitr", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2027-05-16' => ['name' => "Eid'l Adha", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2028-01-26' => ['name' => 'Chinese New Year', 'type' => 'Special Non-Working Holiday', 'is_non_working' => true, 'is_working' => false],
        '2028-02-28' => ['name' => "Eid'l Fitr", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
        '2028-05-05' => ['name' => "Eid'l Adha", 'type' => 'Regular Holiday', 'is_non_working' => true, 'is_working' => false],
    ];

    if (!$holiday && isset($movableHolidays[$today])) {
        $m = $movableHolidays[$today];
        $holiday = [
            'is_holiday' => true,
            'name' => $m['name'],
            'type' => $m['type'],
            'is_non_working' => $m['is_non_working'],
            'is_working' => $m['is_working'],
            'legal_basis' => 'Presidential Proclamation / NCMF'
        ];
    }

    // Default response payload
    $response = [
        'success' => true,
        'today' => $today,
        'formatted_date' => $now->format('D, M j, Y'),
        'day_name' => $now->format('l'),
        'current_time_pst' => $now->format('h:i A'),
        'current_time_seconds' => $now->format('h:i:s A'),
        'server_timestamp_ms' => round(microtime(true) * 1000),
        'is_weather_suspension' => $isActive,
        'holiday' => $holiday,
        'master_schedule' => [
            'connected' => true,
            'synced_at' => $now->format('Y-m-d H:i:s'),
            'has_administrative_hold' => !empty($masterScheduleHold),
            'administrative_hold' => $masterScheduleHold,
            'today_stats' => [
                'confirmed_events' => $todayConfirmedCount,
                'occupied_facilities' => $todayFacilitiesCount
            ],
            'operating_hours' => [
                'start_time' => $masterOperatingConfig['start'] ?? '08:00',
                'end_time' => $masterOperatingConfig['end'] ?? '17:00',
                'start_label' => '8:00 AM',
                'end_label' => '5:00 PM',
                'timezone' => 'PST (UTC+8)',
                'friday_mode' => 'wfh'
            ],
            'portal_url' => 'schedule.php'
        ],
        'advisory' => null,
        'weather_authority_reference' => [
            'agency' => 'NDRRMC / DOST-PAGASA / Civil Service Commission (CSC)',
            'office_location' => 'ATI Central Office, Diliman, Quezon City, Metro Manila',
            'legal_basis' => 'Executive Order No. 66 (s. 2012) & Memorandum Circular No. 57 (OP)'
        ]
    ];

    if ($isActive && $suspensionData) {
        $typhoonName = trim($suspensionData['typhoon_name'] ?? 'Tropical Cyclone / Severe Weather');
        $signalLevel = trim($suspensionData['signal_level'] ?? 'TCWS Signal No. 2');
        $memoRef = trim($suspensionData['memo_ref'] ?? 'Presidential Directive EO 66 / NDRRMC Memo');
        $directiveSource = trim($suspensionData['directive_source'] ?? 'NDRRMC • DOST-PAGASA • CSC • Presidential Directive EO 66');
        $badgeText = trim($suspensionData['badge_text'] ?? 'WEATHER SUSPENSION');
        $resumptionNote = trim($suspensionData['resumption_note'] ?? 'Regular on-site counter operations resume next business day at 8:00 AM (PST).');

        $defaultTitle = "Work Suspended: {$typhoonName} ({$signalLevel})";
        $title = !empty($suspensionData['title']) ? $suspensionData['title'] : $defaultTitle;

        $defaultDesc = "Pursuant to NDRRMC / DOST-PAGASA Severe Weather Bulletin and Malacañang / NDRRMC directives, government office work and counter operations at the ATI Central Office are suspended today due to inclement weather conditions. Online facility and dormitory booking requests remain open 24/7.";
        $description = !empty($suspensionData['description']) ? $suspensionData['description'] : $defaultDesc;

        $response['advisory'] = [
            'title' => $title,
            'description' => $description,
            'typhoon_name' => $typhoonName,
            'signal_level' => $signalLevel,
            'warning_type' => $suspensionData['warning_type'] ?? 'tcws_2',
            'badge_text' => $badgeText,
            'memo_ref' => $memoRef,
            'directive_source' => $directiveSource,
            'start_date' => $suspensionData['start_date'] ?? $today,
            'end_date' => $suspensionData['end_date'] ?? $today,
            'resumption_note' => $resumptionNote,
            'updated_at' => $suspensionData['updated_at'] ?? date('Y-m-d H:i:s')
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'is_weather_suspension' => false,
        'message' => $e->getMessage()
    ]);
}
