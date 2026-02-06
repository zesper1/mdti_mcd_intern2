<?php
    class AuditModel extends BaseModel {
        public function getLatestLogs($limit = 50) {
            $sql = "SELECT 
                        l.created_at, 
                        u.first_name, 
                        u.last_name, 
                        et.event_code, 
                        l.action_details
                    FROM audit_logs l
                    JOIN log_event_types et ON l.event_type_id = et.event_type_id
                    LEFT JOIN users u ON l.user_id = u.user_id
                    ORDER BY l.created_at DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        }
        public function log($userId, $eventCode, $details = []) {
            try {
                // We call the stored procedure to keep logic inside the DB
                $sql = "CALL sp_log_event(?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                
                return $stmt->execute([
                    $userId,
                    $eventCode,
                    json_encode($details)
                ]);
            } catch (PDOException $e) {
                // Silently fail or log to a file so a logging error doesn't crash the app
                error_log("Audit Log Failed: " . $e->getMessage());
                return false;
            }
        }
    }
?>