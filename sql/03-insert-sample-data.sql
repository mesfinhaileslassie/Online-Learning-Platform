USE ethioskillfactory;

-- Insert categories
INSERT INTO categories (name, name_amharic, slug, icon) VALUES
('Exam Preparation', 'የፈተና ዝግጅት', 'exam-preparation', 'fa-book'),
('Technology', 'ቴክኖሎጂ', 'technology', 'fa-laptop-code'),
('Business', 'ንግድ', 'business', 'fa-chart-line'),
('Language', 'ቋንቋ', 'language', 'fa-language'),
('Creative', 'ፈጠራ', 'creative', 'fa-palette');

-- Insert sample admin user (password: Admin@123)
-- Password hash for "Admin@123"
INSERT INTO users (full_name, email, phone, password_hash, role, is_verified) VALUES
('Admin User', 'admin@ethioskillfactory.com', '0911000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Insert sample instructor user (password: Instructor@123)
-- Password hash for "Instructor@123"
INSERT INTO users (full_name, email, phone, password_hash, role, is_verified, bio) VALUES
('Tekle Berhan', 'instructor@example.com', '0922000000', '$2y$10$YourHashWillGoHere', 'instructor', 1, 'Expert instructor with 10+ years of experience in technology and business training.');

-- Insert sample student user (password: Student@123)
-- Password hash for "Student@123"
INSERT INTO users (full_name, email, phone, password_hash, role, is_verified) VALUES
('Student User', 'student@example.com', '0933000000', '$2y$10$YourHashWillGoHere', 'student', 1);