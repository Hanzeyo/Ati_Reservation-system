<?php
/**
 * Agriculture Training Institute - Facility Gallery & Specification Helpers
 */

if (!function_exists('getFacilityGallery')) {
    function getFacilityGallery(array $facility): array {
        $gallery = [];

        // Check if DB has gallery column filled
        if (!empty($facility['gallery'])) {
            $decoded = json_decode($facility['gallery'], true);
            if (is_array($decoded) && !empty($decoded)) {
                foreach ($decoded as $item) {
                    if (is_array($item) && !empty($item['src'])) {
                        $gallery[] = [
                            'src' => $item['src'],
                            'caption' => $item['caption'] ?? 'Facility Photograph'
                        ];
                    } elseif (is_string($item) && trim($item) !== '') {
                        $gallery[] = [
                            'src' => trim($item),
                            'caption' => 'Facility Photograph'
                        ];
                    }
                }
            } elseif (is_string($facility['gallery'])) {
                $parts = array_map('trim', explode(',', $facility['gallery']));
                foreach ($parts as $p) {
                    if ($p !== '') {
                        $gallery[] = ['src' => $p, 'caption' => 'Facility Photograph'];
                    }
                }
            }
        }

        // If gallery is still empty, compose an intelligent gallery based on type & name
        if (empty($gallery)) {
            $isDorm = (strtolower($facility['type'] ?? '') === 'dorms' || stripos($facility['name'] ?? '', 'dorm') !== false);
            $mainImg = !empty($facility['image']) ? $facility['image'] : ($isDorm ? 'assets/images/dormitory.jpg' : 'assets/images/function_hall.jpg');

            $gallery[] = [
                'src' => $mainImg,
                'caption' => $isDorm ? 'Main Room & Bunk Setup' : 'Full Hall & Conference Stage View'
            ];

            if ($isDorm) {
                $gallery[] = [
                    'src' => 'assets/images/dorm_suite_1.png',
                    'caption' => 'Study Workstation, Writing Desks & Air Conditioning'
                ];
                $gallery[] = [
                    'src' => 'assets/images/dorm_suite_2.png',
                    'caption' => 'Individual Secured Steel Lockers & Closets'
                ];
                $gallery[] = [
                    'src' => 'assets/images/dorm_vip_suite.jpg',
                    'caption' => 'Ensuite Restroom & Common Lounge Amenities'
                ];
                $gallery[] = [
                    'src' => 'assets/images/dormitory.jpg',
                    'caption' => 'Dormitory Hallway & Reception Access'
                ];
            } else {
                $lowerName = strtolower($facility['name'] ?? '');
                if (stripos($lowerName, 'carandang') !== false || stripos($lowerName, 'boardroom') !== false) {
                    $gallery[] = [
                        'src' => 'assets/images/boardroom.jpg',
                        'caption' => 'Executive Presentation Podium & Digital Display'
                    ];
                    $gallery[] = [
                        'src' => 'assets/images/function_hall.jpg',
                        'caption' => 'Attendee Ergonomic Seating & Surround Audio'
                    ];
                } elseif (stripos($lowerName, 'training') !== false || stripos($lowerName, 'rdec') !== false) {
                    $gallery[] = [
                        'src' => 'assets/images/rdec_training_1.jpg',
                        'caption' => 'Training Hall Front Stage & Dual Presentation Screens'
                    ];
                    $gallery[] = [
                        'src' => 'assets/images/rdec_training_2.jpg',
                        'caption' => 'Classroom Workshop Workstations & Power Ports'
                    ];
                } elseif (stripos($lowerName, 'orosa') !== false) {
                    $gallery[] = [
                        'src' => 'assets/images/orosa_hall.jpg',
                        'caption' => 'Orosa Function Venue & Banquet Arrangement'
                    ];
                    $gallery[] = [
                        'src' => 'assets/images/function_hall.jpg',
                        'caption' => 'Stage, Sound System & Elevated Rostrum'
                    ];
                } elseif (stripos($lowerName, 'serrano') !== false) {
                    $gallery[] = [
                        'src' => 'assets/images/serrano_hall.jpg',
                        'caption' => 'Serrano Multi-Purpose Auditorium Setup'
                    ];
                    $gallery[] = [
                        'src' => 'assets/images/training_hall.jpg',
                        'caption' => 'Classroom Seminar Seating Configuration'
                    ];
                } else {
                    $gallery[] = [
                        'src' => 'assets/images/function_hall.jpg',
                        'caption' => 'Spacious Multi-Purpose Floor Plan & Stage'
                    ];
                    $gallery[] = [
                        'src' => 'assets/images/boardroom.jpg',
                        'caption' => 'Audio-Visual Control Center & High-Output Projector'
                    ];
                }
                $gallery[] = [
                    'src' => 'assets/images/ATI_building.jpg',
                    'caption' => 'ATI Main Campus Building & Arrival Wing'
                ];
            }
        }

        return $gallery;
    }
}
