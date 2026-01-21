-- ========================================
-- PLU DATABASE IMPORT SCRIPT
-- SPPG Distribution Management System
-- ========================================

-- Disable foreign key checks for import
SET FOREIGN_KEY_CHECKS = 0;

-- ========================================
-- 1. MASTER PLU CODES
-- ========================================

-- International Fruits (4000-4099)
INSERT INTO plu_codes (plu_number, product_name, category, subcategory, variety, standard_unit, is_organic, is_conventional, description, shelf_life_days) VALUES
-- Apples
('4011', 'Red Delicious Apples', 'FRUITS', 'APPLES', 'RED DELICIOUS', 'KG', FALSE, TRUE, 'Crispy and sweet red apples', 180),
('4012', 'Golden Delicious Apples', 'FRUITS', 'APPLES', 'GOLDEN DELICIOUS', 'KG', FALSE, TRUE, 'Sweet yellow apples with thin skin', 180),
('4015', 'Gala Apples', 'FRUITS', 'APPLES', 'GALA', 'KG', FALSE, TRUE, 'Sweet and crisp apples', 180),
('4016', 'Granny Smith Apples', 'FRUITS', 'APPLES', 'GRANNY SMITH', 'KG', FALSE, TRUE, 'Tart green apples', 180),
('4017', 'Fuji Apples', 'FRUITS', 'APPLES', 'FUJI', 'KG', FALSE, TRUE, 'Very sweet and crisp Japanese apples', 180),

-- Organic Apples
('94011', 'Organic Red Delicious Apples', 'FRUITS', 'APPLES', 'RED DELICIOUS', 'KG', TRUE, FALSE, 'Organic crispy and sweet red apples', 180),
('94012', 'Organic Golden Delicious Apples', 'FRUITS', 'APPLES', 'GOLDEN DELICIOUS', 'KG', TRUE, FALSE, 'Organic sweet yellow apples', 180),

-- Bananas
('4030', 'Bananas', 'FRUITS', 'BANANAS', 'CAVENDISH', 'KG', FALSE, TRUE, 'Standard yellow bananas', 14),
('4031', 'Organic Bananas', 'FRUITS', 'BANANAS', 'CAVENDISH', 'KG', TRUE, FALSE, 'Organic yellow bananas', 14),
('4032', 'Plantains', 'FRUITS', 'BANANAS', 'COOKING', 'KG', FALSE, TRUE, 'Cooking bananas, starchy', 21),
('4033', 'Red Bananas', 'FRUITS', 'BANANAS', 'RED', 'KG', FALSE, TRUE, 'Sweet red bananas', 14),

-- Citrus
('4046', 'Oranges', 'FRUITS', 'CITRUS', 'NAVEL', 'KG', FALSE, TRUE, 'Sweet seedless oranges', 56),
('4047', 'Organic Oranges', 'FRUITS', 'CITRUS', 'NAVEL', 'KG', TRUE, FALSE, 'Organic sweet seedless oranges', 56),
('4048', 'Valencia Oranges', 'FRUITS', 'CITRUS', 'VALENCIA', 'KG', FALSE, TRUE, 'Juicy oranges, great for juicing', 56),
('4050', 'Lemons', 'FRUITS', 'CITRUS', 'EUREKA', 'KG', FALSE, TRUE, 'Tart yellow lemons', 42),
('4052', 'Limes', 'FRUITS', 'CITRUS', 'PERSIAN', 'KG', FALSE, TRUE, 'Tart green limes', 42),

-- Grapes
('4060', 'Grapes', 'FRUITS', 'GRAPES', 'RED GLOBE', 'KG', FALSE, TRUE, 'Sweet red grapes', 21),
('4062', 'Green Grapes', 'FRUITS', 'GRAPES', 'THOMPSON', 'KG', FALSE, TRUE, 'Sweet green grapes', 21),
('4063', 'Organic Grapes', 'FRUITS', 'GRAPES', 'RED GLOBE', 'KG', TRUE, FALSE, 'Organic sweet red grapes', 21),

-- Berries
('4066', 'Strawberries', 'FRUITS', 'BERRIES', 'ALBION', 'KG', FALSE, TRUE, 'Sweet red strawberries', 7),
('4067', 'Organic Strawberries', 'FRUITS', 'BERRIES', 'ALBION', 'KG', TRUE, FALSE, 'Organic sweet red strawberries', 7),
('4068', 'Blueberries', 'FRUITS', 'BERRIES', 'HIGHBUSH', 'KG', FALSE, TRUE, 'Sweet blueberries', 14),
('4069', 'Organic Blueberries', 'FRUITS', 'BERRIES', 'HIGHBUSH', 'KG', TRUE, FALSE, 'Organic sweet blueberries', 14),

-- Tropical
('4080', 'Mangoes', 'FRUITS', 'TROPICAL', 'TOMMY ATKINS', 'PCS', FALSE, TRUE, 'Sweet tropical mangoes', 10),
('4081', 'Organic Mangoes', 'FRUITS', 'TROPICAL', 'TOMMY ATKINS', 'PCS', TRUE, FALSE, 'Organic sweet tropical mangoes', 10),
('4082', 'Pineapples', 'FRUITS', 'TROPICAL', 'QUEEN', 'PCS', FALSE, TRUE, 'Sweet pineapples', 7),
('4083', 'Papayas', 'FRUITS', 'TROPICAL', 'SOLO', 'PCS', FALSE, TRUE, 'Sweet papayas', 7),
('4084', 'Organic Papayas', 'FRUITS', 'TROPICAL', 'SOLO', 'PCS', TRUE, FALSE, 'Organic sweet papayas', 7);

-- International Vegetables (4070-4099)
INSERT INTO plu_codes (plu_number, product_name, category, subcategory, variety, standard_unit, is_organic, is_conventional, description, shelf_life_days) VALUES
-- Tomatoes
('4068', 'Tomatoes', 'VEGETABLES', 'TOMATOES', 'BEEFSTEAK', 'KG', FALSE, TRUE, 'Large red tomatoes', 14),
('4069', 'Cherry Tomatoes', 'VEGETABLES', 'TOMATOES', 'CHERRY', 'KG', FALSE, TRUE, 'Small sweet cherry tomatoes', 10),
('4070', 'Organic Tomatoes', 'VEGETABLES', 'TOMATOES', 'BEEFSTEAK', 'KG', TRUE, FALSE, 'Organic large red tomatoes', 14),
('4071', 'Roma Tomatoes', 'VEGETABLES', 'TOMATOES', 'ROMA', 'KG', FALSE, TRUE, 'Plum tomatoes, great for cooking', 14),

-- Leafy Greens
('4090', 'Lettuce', 'VEGETABLES', 'LEAFY', 'ICEBERG', 'PCS', FALSE, TRUE, 'Crispy iceberg lettuce', 21),
('4091', 'Romaine Lettuce', 'VEGETABLES', 'LEAFY', 'ROMAINE', 'PCS', FALSE, TRUE, 'Nutritious romaine lettuce', 21),
('4092', 'Organic Lettuce', 'VEGETABLES', 'LEAFY', 'ICEBERG', 'PCS', TRUE, FALSE, 'Organic crispy iceberg lettuce', 21),
('4093', 'Spinach', 'VEGETABLES', 'LEAFY', 'SAVOY', 'KG', FALSE, TRUE, 'Nutritious green spinach', 7),
('4094', 'Organic Spinach', 'VEGETABLES', 'LEAFY', 'SAVOY', 'KG', TRUE, FALSE, 'Organic nutritious green spinach', 7),

-- Root Vegetables
('4075', 'Carrots', 'VEGETABLES', 'ROOT', 'NANTES', 'KG', FALSE, TRUE, 'Sweet orange carrots', 21),
('4076', 'Organic Carrots', 'VEGETABLES', 'ROOT', 'NANTES', 'KG', TRUE, FALSE, 'Organic sweet orange carrots', 21),
('4082', 'Potatoes', 'VEGETABLES', 'ROOT', 'RUSSET', 'KG', FALSE, TRUE, 'Starchy russet potatoes', 90),
('4083', 'Sweet Potatoes', 'VEGETABLES', 'ROOT', 'BEAUREGARD', 'KG', FALSE, TRUE, 'Sweet orange sweet potatoes', 60),
('4084', 'Organic Potatoes', 'VEGETABLES', 'ROOT', 'RUSSET', 'KG', TRUE, FALSE, 'Organic starchy russet potatoes', 90),

-- Alliums
('4085', 'Onions', 'VEGETABLES', 'ALLIUM', 'YELLOW', 'KG', FALSE, TRUE, 'Pungent yellow onions', 90),
('4086', 'Red Onions', 'VEGETABLES', 'ALLIUM', 'RED', 'KG', FALSE, TRUE, 'Mild red onions', 90),
('4087', 'Organic Onions', 'VEGETABLES', 'ALLIUM', 'YELLOW', 'KG', TRUE, FALSE, 'Organic pungent yellow onions', 90);

-- Indonesian Local Products (9000-9999)
INSERT INTO plu_codes (plu_number, product_name, category, subcategory, variety, standard_unit, is_organic, is_conventional, description, shelf_life_days) VALUES
-- Local Vegetables
('9001', 'Red Large Chili', 'VEGETABLES', 'CHILI', 'BESAR', 'KG', FALSE, TRUE, 'Large red cooking chili', 7),
('9002', 'Bird\'s Eye Chili', 'VEGETABLES', 'CHILI', 'RAWIT', 'KG', FALSE, TRUE, 'Very small spicy chili', 5),
('9003', 'Green Chili', 'VEGETABLES', 'CHILI', 'HIJAU', 'KG', FALSE, TRUE, 'Unripe red chili', 7),
('9004', 'Cayenne Pepper', 'VEGETABLES', 'CHILI', 'KERITING', 'KG', FALSE, TRUE, 'Curly red chili', 7),

('9010', 'Purple Eggplant', 'VEGETABLES', 'EGGPLANT', 'UNGU', 'KG', FALSE, TRUE, 'Common purple eggplant', 10),
('9011', 'Green Eggplant', 'VEGETABLES', 'EGGPLANT', 'HIJAU', 'KG', FALSE, TRUE, 'Local green eggplant', 10),
('9012', 'Thai Eggplant', 'VEGETABLES', 'EGGPLANT', 'BELUT', 'KG', FALSE, TRUE, 'Long thin Thai eggplant', 7),

('9020', 'Water Spinach', 'VEGETABLES', 'LEAFY', 'AIR', 'IKAT', FALSE, TRUE, 'Aquatic vegetable, popular in stir-fry', 3),
('9021', 'Amaranth', 'VEGETABLES', 'LEAFY', 'MERAH', 'IKAT', FALSE, TRUE, 'Red amaranth leaves', 3),
('9022', 'Chinese Cabbage', 'VEGETABLES', 'LEAFY', 'PAHIT', 'IKAT', FALSE, TRUE, 'Bitter Chinese cabbage', 4),
('9023', 'Cassava Leaves', 'VEGETABLES', 'LEAFY', 'TIPAR', 'IKAT', FALSE, TRUE, 'Young cassava leaves, local delicacy', 2),

-- Local Fruits
('9030', 'Ambon Banana', 'FRUITS', 'BANANAS', 'AMBON', 'SISIR', FALSE, TRUE, 'Sweet local banana variety', 7),
('9031', 'King Banana', 'FRUITS', 'BANANAS', 'RAJA', 'SISIR', FALSE, TRUE, 'Premium local banana', 7),
('9032', 'Kepok Banana', 'FRUITS', 'BANANAS', 'KEPOK', 'SISIR', FALSE, TRUE, 'Cooking banana variety', 10),
('9033', 'Horn Banana', 'FRUITS', 'BANANAS', 'TANDUK', 'PCS', FALSE, TRUE, 'Large banana variety', 7),

('9040', 'Pineapple', 'FRUITS', 'TROPICAL', 'QUEEN', 'PCS', FALSE, TRUE, 'Sweet local pineapple', 7),
('9041', 'Watermelon', 'FRUITS', 'MELONS', 'MERAH', 'PCS', FALSE, TRUE, 'Red flesh watermelon', 10),
('9042', 'Melon', 'FRUITS', 'MELONS', 'GALIA', 'PCS', FALSE, TRUE, 'Green flesh galia melon', 10),
('9043', 'Durian', 'FRUITS', 'TROPICAL', 'MONTHONG', 'PCS', FALSE, TRUE, 'King of fruits, premium variety', 5),
('9044', 'Mangosteen', 'FRUITS', 'TROPICAL', 'TANPA BJIH', 'PCS', FALSE, TRUE, 'Queen of fruits', 7),
('9045', 'Rambutan', 'FRUITS', 'TROPICAL', 'BINJAI', 'PCS', FALSE, TRUE, 'Hairy sweet fruit', 5),
('9046', 'Salak', 'FRUITS', 'TROPICAL', 'PONDOK', 'PCS', FALSE, TRUE, 'Snake fruit, sweet and tangy', 14),
('9047', 'Jackfruit', 'FRUITS', 'TROPICAL', 'MINI', 'PCS', FALSE, TRUE, 'Small variety jackfruit', 7),

-- Local Citrus
('9050', 'Sunkist Orange', 'FRUITS', 'CITRUS', 'SUNKIST', 'KG', FALSE, TRUE, 'Imported Sunkist oranges', 56),
('9051', 'Local Orange', 'FRUITS', 'CITRUS', 'KEPROK', 'KG', FALSE, TRUE, 'Local sweet oranges', 42),
('9052', 'Mandarin Orange', 'FRUITS', 'CITRUS', 'MANDARIN', 'KG', FALSE, TRUE, 'Easy peel mandarin oranges', 35),
('9053', 'Pomelo', 'FRUITS', 'CITRUS', 'BALI', 'PCS', FALSE, TRUE, 'Large citrus from Bali', 30);

-- ========================================
-- 2. PROVINCIAL MAPPING
-- ========================================

-- DKI Jakarta (Province ID: 31)
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES
-- Assuming product_id 1-10 exist for basic products
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 31, 'Apel Merah', TRUE, '2024-01-01'),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 31, 'Pisang Ambon', TRUE, '2024-01-01'),
(3, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 31, 'Cabai Merah', TRUE, '2024-01-01'),
(4, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 31, 'Kangkung', TRUE, '2024-01-01'),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 31, 'Tomat', TRUE, '2024-01-01'),
(6, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 31, 'Wortel', TRUE, '2024-01-01'),
(7, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 31, 'Bawang Bombay', TRUE, '2024-01-01'),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9040'), 31, 'Nanas', TRUE, '2024-01-01'),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9041'), 31, 'Semangka', TRUE, '2024-01-01'),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9050'), 31, 'Jeruk Import', TRUE, '2024-01-01');

-- Jawa Barat (Province ID: 32)
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 32, 'Apel Malang', TRUE, '2024-01-01'),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 32, 'Pisang Raja', TRUE, '2024-01-01'),
(3, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 32, 'Lado Merah', TRUE, '2024-01-01'),
(4, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 32, 'Kangkung', TRUE, '2024-01-01'),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 32, 'Tomat', TRUE, '2024-01-01'),
(6, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 32, 'Wortel', TRUE, '2024-01-01'),
(7, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 32, 'Bawang Bombay', TRUE, '2024-01-01'),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9030'), 32, 'Pisang Ambon', TRUE, '2024-01-01'),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9041'), 32, 'Semangka', TRUE, '2024-01-01'),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9051'), 32, 'Jeruk Lokal', TRUE, '2024-01-01');

-- Jawa Tengah (Province ID: 33)
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 33, 'Apel', TRUE, '2024-01-01'),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 33, 'Pisang', TRUE, '2024-01-01'),
(3, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 33, 'Cabe Merah', TRUE, '2024-01-01'),
(4, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 33, 'Kangkung', TRUE, '2024-01-01'),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 33, 'Tomat', TRUE, '2024-01-01'),
(6, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 33, 'Wortel', TRUE, '2024-01-01'),
(7, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 33, 'Bawang Bombay', TRUE, '2024-01-01'),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9040'), 33, 'Nanas', TRUE, '2024-01-01'),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9042'), 33, 'Melon', TRUE, '2024-01-01'),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9051'), 33, 'Jeruk Keprok', TRUE, '2024-01-01');

-- Jawa Timur (Province ID: 34)
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 34, 'Apel Malang', TRUE, '2024-01-01'),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 34, 'Pisang', TRUE, '2024-01-01'),
(3, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 34, 'Cabe Rawit', TRUE, '2024-01-01'),
(4, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 34, 'Kangkung', TRUE, '2024-01-01'),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 34, 'Tomat', TRUE, '2024-01-01'),
(6, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 34, 'Wortel', TRUE, '2024-01-01'),
(7, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 34, 'Bawang Bombay', TRUE, '2024-01-01'),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9040'), 34, 'Nanas Madura', TRUE, '2024-01-01'),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9041'), 34, 'Semangka', TRUE, '2024-01-01'),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9051'), 34, 'Jeruk Lokal', TRUE, '2024-01-01');

-- Sumatera Utara (Province ID: 63)
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 63, 'Apel Import', TRUE, '2024-01-01'),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 63, 'Pisang Raja', TRUE, '2024-01-01'),
(3, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 63, 'Cabe Merah', TRUE, '2024-01-01'),
(4, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 63, 'Kangkung', TRUE, '2024-01-01'),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 63, 'Tomat', TRUE, '2024-01-01'),
(6, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 63, 'Wortel', TRUE, '2024-01-01'),
(7, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 63, 'Bawang Bombay', TRUE, '2024-01-01'),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9043'), 63, 'Durian', TRUE, '2024-01-01'),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9044'), 63, 'Manggis', TRUE, '2024-01-01'),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9045'), 63, 'Rambutan', TRUE, '2024-01-01');

-- Bali (Province ID: 51)
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 51, 'Apel', TRUE, '2024-01-01'),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 51, 'Pisang', TRUE, '2024-01-01'),
(3, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 51, 'Cabe Merah', TRUE, '2024-01-01'),
(4, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 51, 'Kangkung', TRUE, '2024-01-01'),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 51, 'Tomat', TRUE, '2024-01-01'),
(6, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 51, 'Wortel', TRUE, '2024-01-01'),
(7, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 51, 'Bawang Bombay', TRUE, '2024-01-01'),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9046'), 51, 'Salak Pondoh', TRUE, '2024-01-01'),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9040'), 51, 'Nanas', TRUE, '2024-01-01'),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9053'), 51, 'Pomelo Bali', TRUE, '2024-01-01');

-- ========================================
-- 3. BARCODE INTEGRATION
-- ========================================

-- Create barcode entries for PLU codes
INSERT INTO product_barcodes (product_id, plu_code_id, barcode_type, barcode_value, is_primary, description, is_active) VALUES
-- International PLU barcodes (4-5 digits)
(1, (SELECT id FROM plu_codes WHERE plu_number = '4011'), 'PLU', '4011', TRUE, 'Red Delicious Apples PLU', TRUE),
(2, (SELECT id FROM plu_codes WHERE plu_number = '4030'), 'PLU', '4030', TRUE, 'Bananas PLU', TRUE),
(3, (SELECT id FROM plu_codes WHERE plu_number = '4068'), 'PLU', '4068', TRUE, 'Tomatoes PLU', TRUE),
(4, (SELECT id FROM plu_codes WHERE plu_number = '4075'), 'PLU', '4075', TRUE, 'Carrots PLU', TRUE),
(5, (SELECT id FROM plu_codes WHERE plu_number = '4085'), 'PLU', '4085', TRUE, 'Onions PLU', TRUE),

-- Local Indonesian PLU barcodes (9000-9999)
(6, (SELECT id FROM plu_codes WHERE plu_number = '9001'), 'PLU', '9001', TRUE, 'Red Large Chili PLU', TRUE),
(7, (SELECT id FROM plu_codes WHERE plu_number = '9020'), 'PLU', '9020', TRUE, 'Water Spinach PLU', TRUE),
(8, (SELECT id FROM plu_codes WHERE plu_number = '9030'), 'PLU', '9030', TRUE, 'Ambon Banana PLU', TRUE),
(9, (SELECT id FROM plu_codes WHERE plu_number = '9040'), 'PLU', '9040', TRUE, 'Pineapple PLU', TRUE),
(10, (SELECT id FROM plu_codes WHERE plu_number = '9043'), 'PLU', '9043', TRUE, 'Durian PLU', TRUE);

-- ========================================
-- 4. NUTRITION INFORMATION
-- ========================================

-- Update nutrition info for common items
UPDATE plu_codes SET 
nutrition_info = '{"calories": 52, "protein": 0.3, "carbs": 14, "fiber": 2.4, "vitamin_c": 4.6, "serving_size": "100g"}'
WHERE plu_number = '4011';

UPDATE plu_codes SET 
nutrition_info = '{"calories": 89, "protein": 1.1, "carbs": 23, "fiber": 2.6, "vitamin_c": 8.7, "serving_size": "100g"}'
WHERE plu_number = '4030';

UPDATE plu_codes SET 
nutrition_info = '{"calories": 18, "protein": 0.9, "carbs": 3.9, "fiber": 1.2, "vitamin_c": 13.7, "vitamin_a": 833, "serving_size": "100g"}'
WHERE plu_number = '4068';

UPDATE plu_codes SET 
nutrition_info = '{"calories": 41, "protein": 0.9, "carbs": 10, "fiber": 2.8, "vitamin_a": 16706, "serving_size": "100g"}'
WHERE plu_number = '4075';

UPDATE plu_codes SET 
nutrition_info = '{"calories": 40, "protein": 1.1, "carbs": 9, "fiber": 1.7, "vitamin_c": 7.4, "serving_size": "100g"}'
WHERE plu_number = '4085';

-- ========================================
-- 5. SEASONALITY DATA
-- ========================================

-- Update seasonality for tropical fruits
UPDATE plu_codes SET 
seasonality = '{"peak": ["Jun", "Jul", "Aug"], "available": ["May", "Jun", "Jul", "Aug", "Sep"]}'
WHERE plu_number = '9043'; -- Durian

UPDATE plu_codes SET 
seasonality = '{"peak": ["Oct", "Nov", "Dec"], "available": ["Sep", "Oct", "Nov", "Dec", "Jan"]}'
WHERE plu_number = '9044'; -- Mangosteen

UPDATE plu_codes SET 
seasonality = '{"peak": ["Oct", "Nov", "Dec"], "available": ["Sep", "Oct", "Nov", "Dec", "Jan"]}'
WHERE plu_codes SET 
seasonality = '{"peak": ["Oct", "Nov", "Dec"], "available": ["Sep", "Oct", "Nov", "Dec", "Jan"]}'
WHERE plu_number = '9045'; -- Rambutan

UPDATE plu_codes SET 
seasonality = '{"peak": ["Dec", "Jan", "Feb"], "available": ["Nov", "Dec", "Jan", "Feb", "Mar"]}'
WHERE plu_number = '9046'; -- Salak

-- ========================================
-- 6. STORAGE REQUIREMENTS
-- ========================================

-- Update storage requirements
UPDATE plu_codes SET 
storage_requirements = '{"temp_min": 0, "temp_max": 4, "humidity": "90-95%", "ethanol_sensitive": false}'
WHERE category = 'FRUITS' AND subcategory IN ('APPLES', 'PEARS');

UPDATE plu_codes SET 
storage_requirements = '{"temp_min": 7, "temp_max": 13, "humidity": "85-90%", "ethanol_sensitive": true}'
WHERE category = 'FRUITS' AND subcategory = 'BANANAS';

UPDATE plu_codes SET 
storage_requirements = '{"temp_min": 0, "temp_max": 2, "humidity": "95-98%", "ethanol_sensitive": false}'
WHERE category = 'VEGETABLES' AND subcategory = 'LEAFY';

UPDATE plu_codes SET 
storage_requirements = '{"temp_min": 0, "temp_max": 5, "humidity": "85-90%", "ethanol_sensitive": false}'
WHERE category = 'VEGETABLES' AND subcategory = 'ROOT';

-- ========================================
-- 7. CERTIFICATION INFORMATION
-- ========================================

-- Add certification info for organic items
UPDATE plu_codes SET 
certification = '["USDA Organic", "EU Organic", "Indonesia Organic"]'
WHERE is_organic = TRUE;

UPDATE plu_codes SET 
certification = '["MUI Halal"]'
WHERE category IN ('FRUITS', 'VEGETABLES') AND subcategory IN ('BANANAS', 'TROPICAL');

-- ========================================
-- 8. FINAL CLEANUP
-- ========================================

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_plu_lookup ON plu_codes(plu_number, is_active);
CREATE INDEX IF NOT EXISTS idx_plu_category ON plu_codes(category, subcategory);
CREATE INDEX IF NOT EXISTS idx_mapping_lookup ON product_plu_mapping(plu_code_id, province_id, is_primary_plu);
CREATE INDEX IF NOT EXISTS idx_barcode_plu ON product_barcodes(plu_code_id, barcode_type);

-- ========================================
-- IMPORT COMPLETE
-- ========================================

SELECT 'PLU Database Import Complete!' as status;
SELECT COUNT(*) as total_plu_codes FROM plu_codes WHERE is_active = TRUE;
SELECT COUNT(*) as total_mappings FROM product_plu_mapping WHERE is_primary_plu = TRUE;
SELECT COUNT(*) as total_barcodes FROM product_barcodes WHERE barcode_type = 'PLU' AND is_active = TRUE;
