-- Update appointments table to include new fields from the official form
ALTER TABLE appointments
ADD COLUMN is_minor BOOLEAN DEFAULT FALSE AFTER purpose,
ADD COLUMN parent_guardian_name VARCHAR(200) AFTER is_minor,
ADD COLUMN parent_guardian_contact VARCHAR(20) AFTER parent_guardian_name,
ADD COLUMN counseling_concerns TEXT AFTER parent_guardian_contact,
ADD COLUMN current_medications TEXT AFTER counseling_concerns,
ADD COLUMN medical_conditions TEXT AFTER current_medications,
ADD COLUMN student_address TEXT AFTER medical_conditions,
ADD COLUMN consent_acknowledged BOOLEAN DEFAULT FALSE AFTER student_address;

-- Update students table to add address if not exists
ALTER TABLE students
ADD COLUMN address TEXT AFTER contact_number;
