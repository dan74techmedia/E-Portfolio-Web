<?php

class Student {
    private $conn;
    private $table_name = "students";

    // Object properties
    public $id;
    public $name;
    public $admission_no;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create student
    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, admission_no=:admission_no, created_at=:created_at";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->admission_no=htmlspecialchars(strip_tags($this->admission_no));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":admission_no", $this->admission_no);
        $stmt->bindParam(":created_at", $this->created_at);

        // execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read students
    function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Update student
    function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, admission_no=:admission_no, updated_at=:updated_at WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->admission_no=htmlspecialchars(strip_tags($this->admission_no));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":admission_no", $this->admission_no);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":updated_at", $this->updated_at);

        // execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete student
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Find student by admission number
    function findByAdmissionNo($admission_no) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE admission_no = :admission_no LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admission_no", $admission_no);
        $stmt->execute();

        return $stmt;
    }
}
