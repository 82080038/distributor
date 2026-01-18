USE distributor;

ALTER TABLE purchases
    ADD INDEX idx_purchases_purchase_date (purchase_date),
    ADD INDEX idx_purchases_supplier (supplier_id),
    ADD INDEX idx_purchases_branch_date (branch_id, purchase_date);

CREATE TABLE IF NOT EXISTS purchase_audit_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT UNSIGNED NOT NULL,
    action VARCHAR(20) NOT NULL,
    total_before DECIMAL(15,2) NULL,
    total_after DECIMAL(15,2) NOT NULL,
    performed_by INT UNSIGNED NOT NULL,
    performed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_purchase_audit_purchase (purchase_id),
    INDEX idx_purchase_audit_performed_at (performed_at),
    CONSTRAINT fk_purchase_audit_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    CONSTRAINT fk_purchase_audit_user FOREIGN KEY (performed_by) REFERENCES user(id_user)
);

