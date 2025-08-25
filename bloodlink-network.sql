CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
ALTER TABLE admin
ADD COLUMN profile_pic VARCHAR(255) NOT NULL DEFAULT 'default_profile_img.png';



CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
ALTER TABLE users
ADD COLUMN gender VARCHAR(10) AFTER email,
ADD COLUMN bloodgroup VARCHAR(10) AFTER gender,
ADD COLUMN dob DATE AFTER bloodgroup;
ALTER TABLE users
ADD COLUMN creation_date DATE DEFAULT CURRENT_DATE;
ALTER TABLE users
ADD COLUMN profile_img VARCHAR(255) NOT NULL DEFAULT 'default_profile_img.png';



CREATE TABLE donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    bloodqty INT NOT NULL,
    weight INT(20) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    disease VARCHAR(255) NOT NULL,
    date DATE DEFAULT CURRENT_DATE,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE donors
ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'Pending';



CREATE TABLE requests (
    rqst_id INT AUTO_INCREMENT PRIMARY KEY,
    req_bloodGrp VARCHAR(5) NOT NULL,
    bloodQty INT NOT NULL,
    requiredFor VARCHAR(255) NOT NULL,
    contactNo VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    note TEXT,
    rqstDate DATE DEFAULT CURRENT_DATE,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE requests
ADD COLUMN rqst_status VARCHAR(20) NOT NULL DEFAULT 'Pending';



CREATE TABLE stock (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    bloodtype VARCHAR(10) NOT NULL,
    units INT NOT NULL
);
INSERT INTO stock (bloodtype, units) VALUES
('A+', 0),
('A-', 0),
('B+', 0),
('B-', 0),
('AB+', 0),
('AB-', 0),
('O+', 0),
('O-', 0);



CREATE TABLE campaign (
    campaign_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    organizer VARCHAR(255) NOT NULL,
    venue VARCHAR(255) NOT NULL,
    camp_date DATE
);
ALTER TABLE campaign
ADD COLUMN camp_img VARCHAR(255);



CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    star_count INT NOT NULL,
    message TEXT NOT NULL,
    review_date DATE DEFAULT CURRENT_DATE,
    user_id INT,
    item_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (item_id) REFERENCES campaign(campaign_id)
);



-- SELECT CONSTRAINT_NAME
-- FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
-- WHERE TABLE_NAME = 'your_table_name' AND COLUMN_NAME = 'column_name';

-- ALTER TABLE your_table_name DROP FOREIGN KEY constraint_name;
