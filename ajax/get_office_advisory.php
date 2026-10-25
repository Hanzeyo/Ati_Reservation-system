<?php
/**
 * Agriculture Training Institute - Facility Reservation System
 * Public API: Real-time Office Advisory & PAGASA Bad Weather Suspension Status
 */

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

try {
    // 1. Fetch current weather suspension configuration from system settings
    $weatherSuspension = SystemSetting::get('weather_suspension', null);

    $today = date('Y-m-d');
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

    // Default response payload
    $response = [
        'success' => true,
        'today' => $today,
        'current_time_pst' => date('h:i A'),
        'is_weather_suspension' => $isActive,
        'advisory' => null,
        'pagasa_reference' => [
            'agency' => 'Philippine Atmospheric, Geophysical and Astronomical Services Administration (PAGASA-DOST)',
            'office_location' => 'ATI Central Office, Diliman, Quezon City, Metro Manila',
            'legal_basis' => 'Executive Order No. 66 (s. 2012) & Memorandum Circular No. 57 (OP)'
        ]
    ];

    if ($isActive && $suspensionData) {
        $typhoonName = trim($suspensionData['typhoon_name'] ?? 'Tropical Cyclone / Severe Weather');
        $signalLevel = trim($suspensionData['signal_level'] ?? 'TCWS Signal No. 2');
        $memoRef = trim($suspensionData['memo_ref'] ?? 'Presidential Directive EO 66 / NDRRMC Memo');
        $directiveSource = trim($suspensionData['directive_source'] ?? 'PAGASA-DOST • NDRRMC • Presidential Directive EO 66');
        $badgeText = trim($suspensionData['badge_text'] ?? 'PAGASA SUSPENSION');
        $resumptionNote = trim($suspensionData['resumption_note'] ?? 'Regular on-site counter operations resume next business day at 8:00 AM (PST).');

        $defaultTitle = "Work Suspended: {$typhoonName} ({$signalLevel})";
        $title = !empty($suspensionData['title']) ? $suspensionData['title'] : $defaultTitle;

        $defaultDesc = "Pursuant to PAGASA Severe Weather Bulletin and Malacañang / NDRRMC directives, government office work and counter operations at the ATI Central Office are suspended today due to inclement weather conditions. Online facility and dormitory booking requests remain open 24/7.";
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
