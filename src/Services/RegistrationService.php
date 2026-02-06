<?php
    class RegistrationService {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function registerNewStaff($data) {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->userModel->create(
            $data['admin_id'], 
            $data['first_name'], 
            $data['last_name'], 
            $data['email'], 
            $hash, 
            $data['role']
        );
    }
}
?>