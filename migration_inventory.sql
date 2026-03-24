-- ═══════════════════════════════════════════════════════════════════════════
-- EduTrack — Inventory Management Module — SQL Migration
-- Run this in phpMyAdmin on the 'EduTrackDB' database.
-- ═══════════════════════════════════════════════════════════════════════════

-- Product table (owned by Product Management teammate).
-- Created here so the FK on product_journal works.
-- If this table already exists, this statement is safely skipped.
CREATE TABLE IF NOT EXISTS `product` (
  `id` INT AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50),
  `size` VARCHAR(20),
  `dept` VARCHAR(20),
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `qty` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Product Journal table (owned by Inventory Management — YOU).
-- This is an append-only audit log of all stock changes.
CREATE TABLE IF NOT EXISTS `product_journal` (
  `id` INT AUTO_INCREMENT,
  `prod_id` INT NOT NULL,
  `qty` INT NOT NULL DEFAULT 0,
  `notes` ENUM('Add', 'Edit', 'Delete', 'Sale') NOT NULL,
  `total_qty` INT NOT NULL DEFAULT 0,
  `date_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` VARCHAR(100),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`prod_id`) REFERENCES `product`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ═══════════════════════════════════════════════════════════════════════════
-- TEST DATA (optional — run this to verify the module works)
-- ═══════════════════════════════════════════════════════════════════════════

-- INSERT INTO product (name, type, size, dept, price, qty)
-- VALUES ('Uniform 001', 'Uniform', 'Medium', 'CICT', 350.00, 8);

-- INSERT INTO product_journal (prod_id, qty, notes, total_qty, created_by)
-- VALUES (1, 5, 'Add', 5, 'Admin');

-- INSERT INTO product_journal (prod_id, qty, notes, total_qty, created_by)
-- VALUES (1, 10, 'Edit', 10, 'Admin');

-- INSERT INTO product_journal (prod_id, qty, notes, total_qty, created_by)
-- VALUES (1, 2, 'Sale', 8, 'Admin');
