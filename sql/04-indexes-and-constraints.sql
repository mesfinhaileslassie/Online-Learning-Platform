USE ethioskillfactory;

-- Additional indexes for performance
CREATE INDEX idx_courses_price_status ON courses(price, status);
CREATE INDEX idx_enrollments_user_status ON enrollments(user_id, payment_status);
CREATE INDEX idx_lesson_progress_completed ON lesson_progress(enrollment_id, is_completed);
CREATE INDEX idx_quiz_attempts_user_quiz ON quiz_attempts(user_id, quiz_id);
CREATE INDEX idx_reviews_course_rating ON reviews(course_id, rating);
CREATE INDEX idx_wishlist_user_course ON wishlist(user_id, course_id);

-- Update courses table to auto-update average rating trigger
DELIMITER //
CREATE TRIGGER update_course_rating AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE courses 
    SET average_rating = (
        SELECT AVG(rating) 
        FROM reviews 
        WHERE course_id = NEW.course_id
    )
    WHERE id = NEW.course_id;
END//
DELIMITER ;

-- Update courses total_enrollments trigger
DELIMITER //
CREATE TRIGGER update_course_enrollments AFTER INSERT ON enrollments
FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'completed' THEN
        UPDATE courses 
        SET total_enrollments = total_enrollments + 1
        WHERE id = NEW.course_id;
    END IF;
END//
DELIMITER ;