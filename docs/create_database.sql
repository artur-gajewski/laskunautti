
CREATE TABLE IF NOT EXISTS invoice (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    hash VARCHAR(100) NOT NULL,
    sender_iban VARCHAR(100) NOT NULL,
    sender_swift VARCHAR(100) NOT NULL,
    sender_name VARCHAR(100) NOT NULL,
    sender_email VARCHAR(100),
    sender_www VARCHAR(100),
    sender_address VARCHAR(100),
    sender_zip VARCHAR(10),
    sender_city VARCHAR(100),
    sender_yt VARCHAR(10),
    payer_name VARCHAR(100) NOT NULL,
    payer_address VARCHAR(100),
    payer_zip VARCHAR(10),
    payer_city VARCHAR(100),
    payer_yt VARCHAR(10),
    bill_description VARCHAR(100) NOT NULL,
    bill_duedate DATETIME NOT NULL,
    bill_total FLOAT NOT NULL,
    bill_includes_vat BOOLEAN NOT NULL,
    bill_vat VARCHAR(100) NOT NULL,
    bill_number VARCHAR(100) NOT NULL,
    bill_reference VARCHAR(100) NOT NULL
);

ALTER TABLE invoice ADD COLUMN payer_yt VARCHAR(100);

ALTER TABLE invoice ADD COLUMN bill_includes_vat BOOLEAN;

ALTER TABLE invoice ADD COLUMN bill_created DATETIME NOT NULL;
