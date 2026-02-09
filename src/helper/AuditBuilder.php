<?php
class AuditLogger {
    public function buildDetails($eventType, $oldData = null, $newData = null) {
        
        switch ($eventType) {
            case 'USER_UPDATE':
                $changes = [];
                if ($oldData['status'] !== $newData['status']) {
                    $changes[] = "Status: {$oldData['status']} -> {$newData['status']}";
                }
                if ($oldData['role'] !== $newData['role']) {
                    $changes[] = "Role: {$oldData['role']} -> {$newData['role']}";
                }
                return implode(", ", $changes); // Returns: "Status: Active -> Suspended"

            case 'QUOTE_CREATE':
                return "Ref No: " . $newData['reference_no'];

            case 'AUTH_LOGIN':
                return "Device: " . $_SERVER['HTTP_USER_AGENT'];

            default:
                return "No specific details provided.";
        }
    }
}