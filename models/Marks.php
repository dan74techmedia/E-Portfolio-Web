<?php

class Marks {
    private $marks = [];

    // Create marks for a student
    public function createMarks($studentId, $mark) {
        $this->marks[$studentId][] = $mark;
    }

    // Get marks for a student
    public function getStudentMarks($studentId) {
        return isset($this->marks[$studentId]) ? $this->marks[$studentId] : null;
    }

    // Update marks for a student
    public function updateMarks($studentId, $markIndex, $newMark) {
        if (isset($this->marks[$studentId][$markIndex])) {
            $this->marks[$studentId][$markIndex] = $newMark;
        }
    }
}
