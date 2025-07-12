<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/helpers.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all users
     */
    public function getAll() {
        $sql = "SELECT * FROM dbusers ORDER BY userID ASC";
        $result = $this->db->query($sql);
        
        if (!$result) {
            return [];
        }
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $this->formatUserData($row);
        }
        
        return $users;
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $id = $this->db->escape($id);
        $sql = "SELECT * FROM dbusers WHERE userID = '$id'";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $this->formatUserData($result->fetch_assoc());
        }
        
        return null;
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        $username = $this->db->escape($data['userName']);
        $password = password_hash($data['userPassword'], PASSWORD_DEFAULT);
        $role = $this->db->escape($data['userRole']);
        
        $sql = "INSERT INTO dbusers (userName, userPassword, userRole, created_at, updated_at) 
                VALUES ('$username', '$password', '$role', NOW(), NOW())";
        
        if ($this->db->query($sql)) {
            return $this->db->getConnection()->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $id = $this->db->escape($id);
        $username = $this->db->escape($data['userName']);
        $role = $this->db->escape($data['userRole']);
        
        $sql = "UPDATE dbusers SET userName = '$username', userRole = '$role', updated_at = NOW() 
                WHERE userID = '$id'";
        
        return $this->db->query($sql);
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $id = $this->db->escape($id);
        $sql = "DELETE FROM dbusers WHERE userID = '$id'";
        
        return $this->db->query($sql);
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $username = $this->db->escape($username);
        $sql = "SELECT userID FROM dbusers WHERE userName = '$username'";
        
        if ($excludeId) {
            $excludeId = $this->db->escape($excludeId);
            $sql .= " AND userID != '$excludeId'";
        }
        
        $result = $this->db->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    /**
     * Format user data for display
     */
    private function formatUserData($row) {
        return [
            'userID' => $row['userID'],
            'userName' => $row['userName'],
            'userRole' => $row['userRole'],
            'created_at' => Helpers::formatDate($row['created_at']),
            'updated_at' => Helpers::formatDate($row['updated_at']),
            'canDelete' => Helpers::canDeleteUser($_SESSION['userRole'] ?? '', $row['userRole'])
        ];
    }
    
    /**
     * Get users for AJAX response
     */
    public function getUsersForAjax() {
        $users = $this->getAll();
        return array_map(function($user) {
            return [
                'userID' => $user['userID'],
                'userName' => $user['userName'],
                'userRole' => $user['userRole'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
                'canDelete' => $user['canDelete']
            ];
        }, $users);
    }
} 