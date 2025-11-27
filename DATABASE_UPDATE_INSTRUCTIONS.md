# Database Update Instructions

## Step 1: Run SQL Update

1. Open **phpMyAdmin** in your browser: `http://localhost/phpmyadmin/`
2. Select the **gcms_db** database from the left sidebar
3. Click on the **SQL** tab at the top
4. Copy and paste the following SQL code:

```sql
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
```

5. Click **Go** to execute
6. You should see "Query OK" messages

## Step 2: Verify Changes

After running the SQL, verify the changes:
1. Click on the **appointments** table
2. Click **Structure** tab
3. You should see the new columns added

**Then come back and let me know it's done, and I'll continue with the form updates!**
