<?php
/**
 * SystemSetting - Manages dynamic system configurations and official form templates
 */
class SystemSetting extends BaseModel {
    protected $table_name = "system_settings";

    public static function get($key, $default = null) {
        try {
            global $db;
            $conn = $db;
            if (!$conn && class_exists('Database')) {
                $conn = (new Database())->getConnection();
            }
            if (!$conn) return $default;

            $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ? LIMIT 1");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();

            if ($val === false || $val === null) {
                return $default;
            }

            $decoded = json_decode($val, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
        } catch (Throwable $e) {
            return $default;
        }
    }

    public static function set($key, $value, $userId = null) {
        try {
            global $db;
            $conn = $db;
            if (!$conn && class_exists('Database')) {
                $conn = (new Database())->getConnection();
            }
            if (!$conn) return false;

            $storeVal = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$value;
            $uid = $userId ?: ($_SESSION['user_id'] ?? null);

            $stmt = $conn->prepare("
                INSERT INTO system_settings (setting_key, setting_value, updated_by, updated_at) 
                VALUES (?, ?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_by = VALUES(updated_by), updated_at = NOW()
            ");
            return $stmt->execute([$key, $storeVal, $uid]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function remove($key) {
        try {
            global $db;
            $conn = $db;
            if (!$conn && class_exists('Database')) {
                $conn = (new Database())->getConnection();
            }
            if (!$conn) return false;

            $stmt = $conn->prepare("DELETE FROM system_settings WHERE setting_key = ?");
            return $stmt->execute([$key]);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Get details for an official form template ('facility' or 'dormitory')
     */
    public static function getTemplateInfo($type = 'facility') {
        $settingKey = ($type === 'dorm' || $type === 'dormitory' || $type === 'dorms') 
            ? 'dormitory_form_template' 
            : 'facility_form_template';

        $data = self::get($settingKey, null);
        $defaultLabel = ($settingKey === 'dormitory_form_template')
            ? 'ATI Dormitory Reservation Form (ATI-QF/DTS-22)'
            : 'Official ATI Facility Reservation Form & Terms';

        $defaultDesc = ($settingKey === 'dormitory_form_template')
            ? 'Official 1-page dorm accommodation slip with rates and terms.'
            : 'Official 2-page training room/hall reservation slip with terms.';

        $info = [
            'type' => ($settingKey === 'dormitory_form_template') ? 'dormitory' : 'facility',
            'setting_key' => $settingKey,
            'default_title' => $defaultLabel,
            'default_desc' => $defaultDesc,
            'is_custom' => false,
            'file_name' => null,
            'file_path' => null,
            'file_size' => null,
            'file_size_formatted' => null,
            'mime_type' => null,
            'uploaded_at' => null,
            'uploaded_at_formatted' => null,
            'uploaded_by' => null,
            'uploaded_by_name' => null,
            'download_url' => ($settingKey === 'dormitory_form_template') ? 'download_dormitory_form.php' : 'download_form.php',
            'html_preview_url' => ($settingKey === 'dormitory_form_template') ? 'download_dormitory_form.php?mode=html' : 'download_form.php?mode=html'
        ];

        if (is_array($data) && !empty($data['file_path'])) {
            $absPath = __DIR__ . '/../' . ltrim($data['file_path'], '/\\');
            if (file_exists($absPath)) {
                $info['is_custom'] = true;
                $info['file_name'] = $data['original_name'] ?? basename($absPath);
                $info['file_path'] = $data['file_path'];
                $sizeBytes = filesize($absPath);
                $info['file_size'] = $sizeBytes;
                $info['file_size_formatted'] = self::formatBytes($sizeBytes);
                $info['mime_type'] = $data['mime_type'] ?? mime_content_type($absPath);
                $info['uploaded_at'] = $data['uploaded_at'] ?? null;
                $info['uploaded_at_formatted'] = !empty($data['uploaded_at']) ? date('M d, Y • h:i A', strtotime($data['uploaded_at'])) : 'Recently Updated';
                $info['uploaded_by'] = $data['uploaded_by'] ?? null;
                $info['uploaded_by_name'] = $data['uploaded_by_name'] ?? 'System Administrator';
            }
        }

        return $info;
    }

    public static function formatBytes($bytes, $precision = 1) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
