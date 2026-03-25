-- Database Schema for E-Portfolio Web

-- Table for Departments
CREATE TABLE departments (
    department_id SERIAL PRIMARY KEY,
    department_name VARCHAR(100) UNIQUE NOT NULL
);

-- Table for Teachers
CREATE TABLE teachers (
    teacher_id SERIAL PRIMARY KEY,
    teacher_name VARCHAR(100) NOT NULL,
    department_id INT,
    CONSTRAINT fk_department
        FOREIGN KEY(department_id) 
        REFERENCES departments(department_id)
        ON DELETE SET NULL
);

-- Table for Classes
CREATE TABLE classes (
    class_id SERIAL PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    department_id INT,
    CONSTRAINT fk_department_class
        FOREIGN KEY(department_id) 
        REFERENCES departments(department_id)
        ON DELETE CASCADE
);

-- Table for Units
CREATE TABLE units (
    unit_id SERIAL PRIMARY KEY,
    unit_name VARCHAR(100) NOT NULL,
    class_id INT,
    CONSTRAINT fk_class
        FOREIGN KEY(class_id) 
        REFERENCES classes(class_id)
        ON DELETE CASCADE
);

-- Table for Students
CREATE TABLE students (
    student_id SERIAL PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    class_id INT,
    CONSTRAINT fk_class_student
        FOREIGN KEY(class_id) 
        REFERENCES classes(class_id)
        ON DELETE CASCADE
);

-- Table for Marks
CREATE TABLE marks (
    mark_id SERIAL PRIMARY KEY,
    student_id INT,
    unit_id INT,
    mark DECIMAL CHECK (mark >= 0 AND mark <= 100),
    CONSTRAINT fk_student
        FOREIGN KEY(student_id) 
        REFERENCES students(student_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_unit
        FOREIGN KEY(unit_id) 
        REFERENCES units(unit_id)
        ON DELETE CASCADE
);

-- Indexes
CREATE INDEX idx_teacher_department ON teachers(department_id);
CREATE INDEX idx_class_department ON classes(department_id);
CREATE INDEX idx_unit_class ON units(class_id);
CREATE INDEX idx_student_class ON students(class_id);
CREATE INDEX idx_mark_student ON marks(student_id);
CREATE INDEX idx_mark_unit ON marks(unit_id);