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
        public function log($actorId, $eventCode, $targetId = null, $details = '') {
            try {
                // 1. Capture the IP Address automatically
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

                // 2. Prepare the Direct Insert Statement
                // We use a subquery to find the 'event_type_id' using the 'event_code' string
                $sql = "INSERT INTO audit_logs 
                        (user_id, target_id, event_type_id, ip_address, action_details, created_at) 
                        VALUES 
                        (?, ?, (SELECT event_type_id FROM log_event_types WHERE event_code = ? LIMIT 1), ?, ?, NOW())";

                $stmt = $this->db->prepare($sql);

                // 3. Handle $details input
                // If an array was passed (like JSON), encode it; otherwise, use the string directly
                $finalDetails = is_array($details) ? json_encode($details) : $details;

                return $stmt->execute([
                    $actorId,       // The User DOING the action
                    $targetId,      // The User receiving the action (can be null)
                    $eventCode,     // The text code (e.g., 'AUTH_LOGIN', 'USER_UPDATE')
                    $ipAddress,     // The IP
                    $finalDetails   // The string we built (e.g., "Ref No: Q-100")
                ]);

            } catch (PDOException $e) {
                // Log to file so the user experience isn't broken by a background logging error
                error_log("Audit Log Insert Failed: " . $e->getMessage());
                return false;
            }
        }

        public function getLogs($limit = 5) {
            $sql = "SELECT 
                al.created_at,
                al.action_details,
                al.ip_address,
                et.event_code,
                et.description as template_string,
                CONCAT(u1.first_name, ' ', u1.last_name) AS actor_name,
                CONCAT(u2.first_name, ' ', u2.last_name) AS target_name
            FROM audit_logs al
            JOIN log_event_types et ON al.event_type_id = et.event_type_id
            LEFT JOIN users u1 ON al.user_id = u1.user_id
            LEFT JOIN users u2 ON al.target_id = u2.user_id
            ORDER BY al.created_at DESC 
            LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rawLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formattedLogs = [];
            
            foreach ($rawLogs as $row) {
                // A. Format Description
                $placeholders = [
                    '%actor%'   => $row['actor_name'] ?? 'System',
                    '%target%'  => $row['target_name'] ?? 'N/A',
                    '%details%' => $row['action_details'],
                    '%ip%'      => $row['ip_address'] ?? 'Unknown IP' /* <--- ADD THIS LINE */
                ];

                if (empty($row['action_details'])) {
                    $row['template_string'] = str_replace("Details: %details%", "", $row['template_string']);
                }
                $finalDescription = strtr($row['template_string'], $placeholders);

                // B. Determine Badge
                $badgeColor = 'secondary';
                if (strpos($row['event_code'], 'CREATE') !== false) $badgeColor = 'success';
                if (strpos($row['event_code'], 'UPDATE') !== false) $badgeColor = 'warning';
                if (strpos($row['event_code'], 'DELETE') !== false) $badgeColor = 'danger';
                if (strpos($row['event_code'], 'LOGIN') !== false)  $badgeColor = 'primary';
                
                // C. Calculate Time Ago (FIXED HERE)
                $timeAgo = $this->time_elapsed_string($row['created_at']);

                $formattedLogs[] = [
                    'user'        => $row['actor_name'] ?? 'System',
                    'event_code'  => $row['event_code'],
                    'description' => $finalDescription,
                    'time'        => $timeAgo,
                    'badge'       => $badgeColor
                ];
            }

            return $formattedLogs;
        }

        public function time_elapsed_string($datetime, $full = false) {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            // 1. Calculate weeks manually (DateInterval doesn't support 'w' natively)
            $weeks = floor($diff->d / 7);
            $days = $diff->d - ($weeks * 7);

            // 2. Create a clean array of values
            $periods = [
                'year'   => $diff->y,
                'month'  => $diff->m,
                'week'   => $weeks,
                'day'    => $days,
                'hour'   => $diff->h,
                'minute' => $diff->i,
                'second' => $diff->s,
            ];

            // 3. Build the string
            $parts = [];
            foreach ($periods as $label => $value) {
                if ($value > 0) {
                    // Pluralize if value > 1 (e.g., "2 weeks" vs "1 week")
                    $parts[] = $value . ' ' . $label . ($value > 1 ? 's' : '');
                }
            }

            // 4. Handle "Just now" case
            if (empty($parts)) {
                return 'just now';
            }

            // 5. Return strictly the first part (e.g., "5 mins ago") or full list
            if (!$full) {
                $parts = array_slice($parts, 0, 1);
            }

            return implode(', ', $parts) . ' ago';
        }
}