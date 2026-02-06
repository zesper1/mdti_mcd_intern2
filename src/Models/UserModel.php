<?php
class UserModel extends BaseModel {
    public function create($adminId, $firstName, $lastName, $email, $passwordHash, $roleName) {
        $sql = "CALL sp_add_new_user(?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$adminId, $firstName, $lastName, $email, $passwordHash, $roleName]);
        
        return $stmt->fetch(); // Returns ['new_user_id' => X]
    }

    public function getByEmail($email) {
        // We JOIN the tables so we get 'role_name' (e.g., 'Admin', 'Member') in the result
        $sql = "SELECT u.*, r.role_name 
                FROM users u
                LEFT JOIN user_roles ur ON u.user_id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.role_id
                WHERE u.email = ? AND u.is_active = 1
                LIMIT 1"; // Assuming 1 main role for now
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function getPermissions($userId) {
        $sql = "SELECT DISTINCT p.permission_key 
                FROM permissions p
                JOIN role_permissions rp ON p.permission_id = rp.permission_id
                JOIN user_roles ur ON rp.role_id = ur.role_id
                WHERE ur.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns a simple array of keys
    }

    public function getAllUsers() {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.is_active, r.role_name 
                FROM users u 
                LEFT JOIN user_roles ur ON u.user_id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.role_id 
                ORDER BY u.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function deleteUser($id) {
        // Protect against self-deletion (handled in controller usually, but safe to add here)
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt->execute([$id]);
    }

    public function saveUser($data) {
        $this->db->beginTransaction();
        try {
            $id = $data['id'] ?? null;
            $password = $data['password'] ?? '';

            // 1. Password Validation (Criteria Check)
            if (!empty($password)) {
                $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,16}$/';
                if (!preg_match($pattern, $password)) {
                    throw new Exception("Password must be 8-16 chars with 1 Upper, 1 Lower, 1 Number, & 1 Special Char.");
                }
                $hash = password_hash($password, PASSWORD_DEFAULT);
            }

            // 2. Insert or Update User Table
            if (empty($id)) {
                // INSERT
                if (empty($password)) throw new Exception("Password is required for new users.");
                
                $sql = "INSERT INTO users (first_name, last_name, email, password_hash, is_active) VALUES (?, ?, ?, ?, 1)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $hash]);
                $id = $this->db->lastInsertId();
            } else {
                // UPDATE
                if (!empty($password)) {
                    $sql = "UPDATE users SET first_name=?, last_name=?, email=?, password_hash=? WHERE user_id=?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $hash, $id]);
                } else {
                    $sql = "UPDATE users SET first_name=?, last_name=?, email=? WHERE user_id=?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $id]);
                }
            }

            // 3. Update Role (Transaction safe)
            if (!empty($data['role'])) {
                $stmt = $this->db->prepare("SELECT role_id FROM roles WHERE role_name = ?");
                $stmt->execute([$data['role']]);
                $roleId = $stmt->fetchColumn();

                if ($roleId) {
                    $this->db->prepare("DELETE FROM user_roles WHERE user_id=?")->execute([$id]);
                    $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$id, $roleId]);
                }
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'User saved successfully.'];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>