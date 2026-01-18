USE distributor;

CREATE TABLE sppg_materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    material_code VARCHAR(50) UNIQUE NOT NULL,
    material_name VARCHAR(255) NOT NULL,
    category ENUM('B', 'K', 'M', 'O', 'D', 'T') NOT NULL,
    subcategory VARCHAR(50) NOT NULL,
    brand VARCHAR(100) NULL,
    unit VARCHAR(20) NOT NULL,
    package_size VARCHAR(50) NOT NULL,
    shelf_life_months INT NOT NULL,
    supplier_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sppg_material_code (material_code),
    INDEX idx_sppg_material_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE sppg_materials
    ADD COLUMN protein_per_100g DECIMAL(5,2) NULL,
    ADD COLUMN carb_per_100g DECIMAL(5,2) NULL,
    ADD COLUMN fat_per_100g DECIMAL(5,2) NULL,
    ADD COLUMN fiber_per_100g DECIMAL(5,2) NULL,
    ADD COLUMN calories_per_100g INT NULL,
    ADD COLUMN vitamin_a_mcg INT NULL,
    ADD COLUMN vitamin_c_mcg INT NULL,
    ADD COLUMN iron_mcg DECIMAL(5,2) NULL,
    ADD COLUMN calcium_mcg DECIMAL(5,2) NULL;

CREATE TABLE plu_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plu_number VARCHAR(10) UNIQUE NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    scientific_name VARCHAR(255) NULL,
    category VARCHAR(100) NOT NULL,
    subcategory VARCHAR(100) NULL,
    variety VARCHAR(100) NULL,
    size_grade VARCHAR(50) NULL,
    description TEXT NULL,
    commodity_code VARCHAR(50) NULL,
    is_organic BOOLEAN DEFAULT FALSE,
    is_conventional BOOLEAN DEFAULT TRUE,
    is_gmo BOOLEAN DEFAULT FALSE,
    standard_unit VARCHAR(20) DEFAULT 'KG',
    country_origin VARCHAR(3) NULL,
    seasonality JSON NULL,
    storage_requirements JSON NULL,
    shelf_life_days INT NULL,
    nutrition_info JSON NULL,
    allergen_info JSON NULL,
    certification JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    effective_date DATE NULL,
    expiry_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_plu_number (plu_number),
    INDEX idx_plu_name (product_name),
    INDEX idx_plu_category (category),
    INDEX idx_plu_subcategory (subcategory),
    INDEX idx_plu_variety (variety),
    INDEX idx_plu_active (is_active),
    INDEX idx_plu_origin (country_origin),
    FULLTEXT idx_plu_search (product_name, description, variety)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sppg_material_plu_mappings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    material_code VARCHAR(50) NOT NULL,
    plu_number VARCHAR(10) NOT NULL,
    mapping_type ENUM('direct', 'approximation') NOT NULL DEFAULT 'direct',
    conversion_factor DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
    is_primary BOOLEAN NOT NULL DEFAULT TRUE,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sppg_mapping_material FOREIGN KEY (material_code) REFERENCES sppg_materials(material_code),
    CONSTRAINT fk_sppg_mapping_plu FOREIGN KEY (plu_number) REFERENCES plu_codes(plu_number),
    INDEX idx_sppg_mapping_material (material_code),
    INDEX idx_sppg_mapping_plu (plu_number),
    INDEX idx_sppg_mapping_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sppg_daily_material_demand (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sppg_id VARCHAR(50) NOT NULL,
    demand_date DATE NOT NULL,
    material_code VARCHAR(50) NOT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    beneficiaries_count INT NOT NULL,
    total_quantity_grams DECIMAL(14,3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_demand_sppg_date (sppg_id, demand_date),
    INDEX idx_demand_material (material_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sppg_menus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_code VARCHAR(50) UNIQUE NOT NULL,
    menu_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_menus_code (menu_code),
    INDEX idx_menus_target_group (target_group),
    INDEX idx_menus_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sppg_menu_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id BIGINT UNSIGNED NOT NULL,
    material_code VARCHAR(50) NOT NULL,
    quantity_grams_per_portion DECIMAL(10,3) NOT NULL,
    is_optional BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES sppg_menus(id),
    FOREIGN KEY (material_code) REFERENCES sppg_materials(material_code),
    INDEX idx_menu_items_menu (menu_id),
    INDEX idx_menu_items_material (material_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sppg_menu_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sppg_id VARCHAR(50) NOT NULL,
    menu_date DATE NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    menu_id BIGINT UNSIGNED NOT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    portions INT NOT NULL,
    beneficiaries_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES sppg_menus(id),
    INDEX idx_menu_logs_sppg_date (sppg_id, menu_date),
    INDEX idx_menu_logs_menu (menu_id),
    INDEX idx_menu_logs_target_group (target_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sppg_material_ai_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    material_code VARCHAR(50) NOT NULL,
    ai_product_id VARCHAR(50) NOT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ai_map_material (material_code),
    INDEX idx_ai_map_product (ai_product_id),
    INDEX idx_ai_map_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE VIEW v_sppg_material_plu_nutrition AS
SELECT
    sm.material_code,
    sm.material_name,
    sm.category,
    sm.subcategory,
    sm.unit,
    sm.package_size,
    mpm.plu_number,
    pc.product_name AS plu_product_name,
    pc.category AS plu_category,
    pc.subcategory AS plu_subcategory,
    sm.protein_per_100g,
    sm.carb_per_100g,
    sm.fat_per_100g,
    sm.fiber_per_100g,
    sm.calories_per_100g
FROM sppg_materials sm
LEFT JOIN sppg_material_plu_mappings mpm
    ON mpm.material_code = sm.material_code
    AND mpm.is_primary = TRUE
LEFT JOIN plu_codes pc
    ON pc.plu_number = mpm.plu_number
WHERE sm.is_active = TRUE;

CREATE VIEW v_sppg_material_demand_weekly AS
SELECT
    d.sppg_id,
    YEARWEEK(d.demand_date, 3) AS demand_week,
    MIN(d.demand_date) AS week_start_date,
    MAX(d.demand_date) AS week_end_date,
    d.material_code,
    sm.material_name,
    d.target_group,
    SUM(d.total_quantity_grams) / 1000 AS total_quantity_kg,
    SUM(d.beneficiaries_count) AS total_beneficiaries
FROM sppg_daily_material_demand d
LEFT JOIN sppg_materials sm
    ON sm.material_code = d.material_code
GROUP BY
    d.sppg_id,
    YEARWEEK(d.demand_date, 3),
    d.material_code,
    sm.material_name,
    d.target_group;

CREATE VIEW v_sppg_material_demand_monthly AS
SELECT
    d.sppg_id,
    DATE_FORMAT(d.demand_date, '%Y-%m') AS demand_month,
    MIN(d.demand_date) AS month_start_date,
    MAX(d.demand_date) AS month_end_date,
    d.material_code,
    sm.material_name,
    d.target_group,
    SUM(d.total_quantity_grams) / 1000 AS total_quantity_kg,
    SUM(d.beneficiaries_count) AS total_beneficiaries
FROM sppg_daily_material_demand d
LEFT JOIN sppg_materials sm
    ON sm.material_code = d.material_code
GROUP BY
    d.sppg_id,
    DATE_FORMAT(d.demand_date, '%Y-%m'),
    d.material_code,
    sm.material_name,
    d.target_group;
