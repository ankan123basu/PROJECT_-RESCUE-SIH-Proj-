-- Drop the existing activities table if it exists
DROP TABLE IF EXISTS activities;

-- Create the activities table
CREATE TABLE activities (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    activity_type VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    icon_color VARCHAR(50) NOT NULL,
    bg_color VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample activities with real-time timestamps
INSERT INTO activities (activity_type, title, description, icon, icon_color, bg_color, timestamp) VALUES
('success', 'Mission Completed', 'Successfully evacuated 23 civilians from flood zone', 'fa-check', 'text-green-400', 'bg-green-500/20', NOW() - INTERVAL 3 HOUR),
('update', 'Status Update', 'Resources reallocated to northern sector', 'fa-sync', 'text-blue-400', 'bg-blue-500/20', NOW() - INTERVAL 5 HOUR),
('alert', 'Alert Received', 'Weather warning: Heavy rainfall expected', 'fa-exclamation', 'text-yellow-400', 'bg-yellow-500/20', NOW() - INTERVAL 7 HOUR),
('emergency', 'Earthquake Detected', 'Magnitude 5.8 earthquake reported in Western Region', 'fa-house-damage', 'text-orange-500', 'bg-orange-500/20', NOW() - INTERVAL 9 HOUR),
('deployment', 'Team Deployed', 'Search and rescue team deployed to Eastern Mountains', 'fa-helicopter', 'text-purple-400', 'bg-purple-500/20', NOW() - INTERVAL 11 HOUR),
('medical', 'Medical Emergency', 'Medical supplies dispatched to Southern Region', 'fa-first-aid', 'text-red-400', 'bg-red-500/20', NOW() - INTERVAL 13 HOUR),
('rescue', 'Rescue Operation', 'Water rescue team deployed to flooded areas', 'fa-life-ring', 'text-blue-400', 'bg-blue-500/20', NOW() - INTERVAL 15 HOUR),
('supply', 'Supplies Delivered', 'Emergency food and water delivered to affected communities', 'fa-box', 'text-green-400', 'bg-green-500/20', NOW() - INTERVAL 17 HOUR),
('training', 'Training Completed', 'Emergency response team completed disaster simulation', 'fa-graduation-cap', 'text-purple-400', 'bg-purple-500/20', NOW() - INTERVAL 19 HOUR);
