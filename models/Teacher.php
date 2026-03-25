<?php

namespace models;

class Teacher {
    private $db;
    private $table = 'teachers';

    public function __construct($db) {
        $this->db = $db;
    }

    // Create a new teacher
    public function create($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (name, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword]);
    }

    // Read a teacher by ID
    public function read($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update a teacher
    public function update($id, $name, $email, $password = null) {
        $fields = "name = ?, email = ?";
        $values = [$name, $email];

        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $fields .= ", password = ?";
            $values[] = $hashedPassword;
        }

        $fields .= " WHERE id = ?";
        $values[] = $id;

        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET " . $fields);
        return $stmt->execute($values);
    }

    // Delete a teacher
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
