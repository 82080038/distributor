# 🏷️ PLU (Price Look-Up) DATABASE
# Standardisasi Produk Lintas Provinsi untuk SPPG

## 📋 OVERVIEW

Dokumen ini berisi database PLU lengkap untuk SPPG Distribution Management System:
- Standar PLU internasional (IFPS)
- Produk lokal Indonesia
- Mapping per provinsi
- Data nutrisi dan sertifikasi
- Seasonal availability

---

## 🌐 PLU STANDARDS & SOURCES

### 1. International Federation for Produce Standards (IFPS)
- **Website:** https://www.plucodes.com
- **Database:** 4010-4999 range (conventional)
- **Database:** 94000-94999 range (organic)
- **Update:** Quarterly updates
- **Access:** Free for basic codes

### 2. GS1 Indonesia
- **Website:** https://www.gs1id.org
- **Standar:** GTIN + GLN untuk Indonesia
- **Coverage:** National product registry
- **Integration:** Barcode compatibility

### 3. Badan POM RI
- **Website:** https://pom.go.id
- **Standar:** Makanan dan minuman
- **Coverage:** Produk terdaftar
- **Compliance:** Regulasi lokal

---

## 📊 MASTER PLU DATA

### FRUITS (4000-4099)

#### Apples (4011-4019)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4011 | Red Delicious Apples | RED DELICIOUS | ALL | KG | ❌ | ✅ |
| 4012 | Golden Delicious Apples | GOLDEN DELICIOUS | ALL | KG | ❌ | ✅ |
| 4015 | Gala Apples | GALA | ALL | KG | ❌ | ✅ |
| 4016 | Granny Smith Apples | GRANNY SMITH | ALL | KG | ❌ | ✅ |
| 4017 | Fuji Apples | FUJI | ALL | KG | ❌ | ✅ |
| 94011 | Organic Red Delicious Apples | RED DELICIOUS | ALL | KG | ✅ | ❌ |
| 94012 | Organic Golden Delicious Apples | GOLDEN DELICIOUS | ALL | KG | ✅ | ❌ |

#### Bananas (4030-4039)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4030 | Bananas | CAVENDISH | ALL | KG | ❌ | ✅ |
| 4031 | Organic Bananas | CAVENDISH | ALL | KG | ✅ | ❌ |
| 4032 | Plantains | COOKING | ALL | KG | ❌ | ✅ |
| 4033 | Red Bananas | RED | ALL | KG | ❌ | ✅ |

#### Citrus (4040-4059)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4046 | Oranges | NAVEL | ALL | KG | ❌ | ✅ |
| 4047 | Organic Oranges | NAVEL | ALL | KG | ✅ | ❌ |
| 4048 | Valencia Oranges | VALENCIA | ALL | KG | ❌ | ✅ |
| 4050 | Lemons | EUREKA | ALL | KG | ❌ | ✅ |
| 4052 | Limes | PERSIAN | ALL | KG | ❌ | ✅ |
| 4053 | Organic Lemons | EUREKA | ALL | KG | ✅ | ❌ |

#### Grapes (4060-4069)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4060 | Grapes | RED GLOBE | ALL | KG | ❌ | ✅ |
| 4062 | Green Grapes | THOMPSON | ALL | KG | ❌ | ✅ |
| 4063 | Organic Grapes | RED GLOBE | ALL | KG | ✅ | ❌ |
| 4064 | Black Grapes | CONCORD | ALL | KG | ❌ | ✅ |

#### Berries (4065-4069)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4066 | Strawberries | ALBION | PINT | KG | ❌ | ✅ |
| 4067 | Organic Strawberries | ALBION | PINT | KG | ✅ | ❌ |
| 4068 | Blueberries | Highbush | PINT | KG | ❌ | ✅ |
| 4069 | Organic Blueberries | Highbush | PINT | KG | ✅ | ❌ |

#### Tropical (4080-4089)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4080 | Mangoes | TOMMY ATKINS | EACH | PCS | ❌ | ✅ |
| 4081 | Organic Mangoes | TOMMY ATKINS | EACH | PCS | ✅ | ❌ |
| 4082 | Pineapples | QUEEN | EACH | PCS | ❌ | ✅ |
| 4083 | Papayas | SOLO | EACH | PCS | ❌ | ✅ |
| 4084 | Organic Papayas | SOLO | EACH | PCS | ✅ | ❌ |

### VEGETABLES (4070-4099)

#### Tomatoes (4068-4069)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4068 | Tomatoes | BEEFSTEAK | ALL | KG | ❌ | ✅ |
| 4069 | Cherry Tomatoes | CHERRY | PINT | KG | ❌ | ✅ |
| 4070 | Organic Tomatoes | BEEFSTEAK | ALL | KG | ✅ | ❌ |
| 4071 | Roma Tomatoes | ROMA | ALL | KG | ❌ | ✅ |

#### Leafy Greens (4090-4099)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4090 | Lettuce | ICEBERG | HEAD | PCS | ❌ | ✅ |
| 4091 | Romaine Lettuce | ROMAINE | HEAD | PCS | ❌ | ✅ |
| 4092 | Organic Lettuce | ICEBERG | HEAD | PCS | ✅ | ❌ |
| 4093 | Spinach | SAVOY | BUNCH | KG | ❌ | ✅ |
| 4094 | Organic Spinach | SAVOY | BUNCH | KG | ✅ | ❌ |
| 4095 | Kale | CURLY | BUNCH | KG | ❌ | ✅ |
| 4096 | Organic Kale | CURLY | BUNCH | KG | ✅ | ❌ |

#### Root Vegetables (4075-4084)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4075 | Carrots | NANTES | BUNCH | KG | ❌ | ✅ |
| 4076 | Organic Carrots | NANTES | BUNCH | KG | ✅ | ❌ |
| 4082 | Potatoes | RUSSET | BAG | KG | ❌ | ✅ |
| 4083 | Sweet Potatoes | BEAUREGARD | BAG | KG | ❌ | ✅ |
| 4084 | Organic Potatoes | RUSSET | BAG | KG | ✅ | ❌ |

#### Alliums (4085-4089)
| PLU | Product Name | Variety | Size | Unit | Organic | Conventional |
|-----|-------------|---------|------|------|---------|--------------|
| 4085 | Onions | YELLOW | BAG | KG | ❌ | ✅ |
| 4086 | Red Onions | RED | BAG | KG | ❌ | ✅ |
| 4087 | Organic Onions | YELLOW | BAG | KG | ✅ | ❌ |
| 4088 | Garlic | SOFT | BUNCH | KG | ❌ | ✅ |
| 4089 | Organic Garlic | SOFT | BUNCH | KG | ✅ | ❌ |

---

## 🇮🇩 INDONESIAN LOCAL PRODUCTS (9000-9999)

### Sayuran Lokal (9000-9099)

#### Cabai (Chili)
| PLU | Product Name | Local Name | Variety | Unit | Province | Notes |
|-----|-------------|-------------|---------|------|----------|-------|
| 9001 | Red Large Chili | Cabai Merah Besar | BESAR | KG | ALL | Common cooking chili |
| 9002 | Bird's Eye Chili | Cabai Rawit | RAWIT | KG | ALL | Very spicy |
| 9003 | Green Chili | Cabai Hijau | HIJAU | KG | ALL | Unripe red chili |
| 9004 | Cayenne Pepper | Cabai Keriting | KERITING | KG | ALL | Curly variety |
| 9005 | Organic Red Chili | Cabai Merah Organik | BESAR | KG | JAVA | Certified organic |

#### Terong (Eggplant)
| PLU | Product Name | Local Name | Variety | Unit | Province | Notes |
|-----|-------------|-------------|---------|------|----------|-------|
| 9010 | Purple Eggplant | Terong Ungu | UNGU | KG | ALL | Most common |
| 9011 | Green Eggplant | Terong Hijau | HIJAU | KG | ALL | Local variety |
| 9012 | Thai Eggplant | Terong Belut | BELUT | KG | SUMATRA | Long variety |
| 9013 | Mini Eggplant | Terong Bulat | BULAT | KG | BALI | Small round |

#### Sayuran Hijau (Leafy Greens)
| PLU | Product Name | Local Name | Variety | Unit | Province | Notes |
|-----|-------------|-------------|---------|------|----------|-------|
| 9020 | Water Spinach | Kangkung | AIR | IKAT | ALL | Aquatic vegetable |
| 9021 | Amaranth | Bayam | MERAH | IKAT | ALL | Red amaranth |
| 9022 | Chinese Cabbage | Sawi | PAHIT | IKAT | ALL | Bitter variety |
| 9023 | Cassava Leaves | Daun Singkong | TIPAR | IKAT | SUMATRA | Local delicacy |
| 9024 | Papaya Leaves | Daun Pepaya | MUDA | IKAT | JAVA | Bitter taste |

### Buah-Buahan Lokal (9100-9199)

#### Pisang (Bananas)
| PLU | Product Name | Local Name | Variety | Unit | Province | Notes |
|-----|-------------|-------------|---------|------|----------|-------|
| 9030 | Ambon Banana | Pisang Ambon | AMBON | SISIR | JAVA | Sweet variety |
| 9031 | King Banana | Pisang Raja | RAJA | SISIR | SUMATRA | Premium variety |
| 9032 | Kepok Banana | Pisang Kepok | KEPOK | SISIR | ALL | Cooking banana |
| 9033 | Tanduk Banana | Pisang Tanduk | TANDUK | PCS | ALL | Large variety |
| 9034 | Cavendish Local | Pisang Susu | SUSU | SISIR | JAVA | Local cavendish |

#### Buah Tropis (Tropical Fruits)
| PLU | Product Name | Local Name | Variety | Unit | Province | Notes |
|-----|-------------|-------------|---------|------|----------|-------|
| 9040 | Pineapple | Nanas | QUEEN | PCS | SUMATRA | Sweet variety |
| 9041 | Watermelon | Semangka | MERAH | PCS | JAVA | Red flesh |
| 9042 | Melon | Melon | GALIA | PCS | JAVA | Green flesh |
| 9043 | Durian | Durian | MONTHONG | PCS | SUMATRA | Premium variety |
| 9044 | Mangosteen | Manggis | TANPA BJIH | PCS | SUMATRA | Queen of fruits |
| 9045 | Rambutan | Rambutan | BINJAI | PCS | SUMATRA | Sweet variety |
| 9046 | Salak | Salak | PONDOK | PCS | JAVA | Snake fruit |
| 9047 | Jackfruit | Nangka | MINI | PCS | JAVA | Small variety |

#### Jeruk (Citrus)
| PLU | Product Name | Local Name | Variety | Unit | Province | Notes |
|-----|-------------|-------------|---------|------|----------|-------|
| 9050 | Sunkist Orange | Jeruk Sunkist | IMPORT | KG | ALL | Imported variety |
| 9051 | Local Orange | Jeruk Keprok | KEPROK | KG | JAVA | Local variety |
| 9052 | Mandarin Orange | Jeruk Mandarin | MANDARIN | KG | SUMATRA | Sweet variety |
| 9053 | Pomelo | Jeruk Bali | BALI | PCS | BALI | Large citrus |

---

## 🗺️ PROVINCIAL MAPPING

### DKI Jakarta
| PLU | Standard Name | Local Name | Price Range | Season |
|-----|---------------|-------------|--------------|--------|
| 4011 | Red Delicious Apples | Apel Merah | Rp 45.000-55.000/kg | Year-round |
| 4030 | Bananas | Pisang Ambon | Rp 25.000-35.000/kg | Year-round |
| 9001 | Red Large Chili | Cabai Merah | Rp 30.000-45.000/kg | Year-round |
| 9020 | Water Spinach | Kangkung | Rp 8.000-12.000/ikat | Year-round |

### Jawa Barat
| PLU | Standard Name | Local Name | Price Range | Season |
|-----|---------------|-------------|--------------|--------|
| 4011 | Red Delicious Apples | Apel Malang | Rp 40.000-50.000/kg | Year-round |
| 4030 | Bananas | Pisang Raja | Rp 20.000-30.000/kg | Year-round |
| 9001 | Red Large Chili | Lado Merah | Rp 25.000-40.000/kg | Year-round |
| 9030 | Ambon Banana | Pisang Ambon | Rp 22.000-32.000/sisir | Year-round |

### Jawa Tengah
| PLU | Standard Name | Local Name | Price Range | Season |
|-----|---------------|-------------|--------------|--------|
| 4011 | Red Delicious Apples | Apel | Rp 38.000-48.000/kg | Year-round |
| 4030 | Bananas | Pisang | Rp 18.000-28.000/kg | Year-round |
| 9001 | Red Large Chili | Cabe Merah | Rp 20.000-35.000/kg | Year-round |
| 9020 | Water Spinach | Kangkung | Rp 5.000-8.000/ikat | Year-round |

### Jawa Timur
| PLU | Standard Name | Local Name | Price Range | Season |
|-----|---------------|-------------|--------------|--------|
| 4011 | Red Delicious Apples | Apel Malang | Rp 35.000-45.000/kg | Year-round |
| 4030 | Bananas | Pisang | Rp 17.000-27.000/kg | Year-round |
| 9001 | Red Large Chili | Cabe Rawit | Rp 22.000-32.000/kg | Year-round |
| 9040 | Pineapple | Nanas Madura | Rp 15.000-25.000/pcs | Year-round |

### Sumatera Utara
| PLU | Standard Name | Local Name | Price Range | Season |
|-----|---------------|-------------|--------------|--------|
| 4011 | Red Delicious Apples | Apel Import | Rp 50.000-60.000/kg | Year-round |
| 4030 | Bananas | Pisang Raja | Rp 15.000-25.000/kg | Year-round |
| 9001 | Red Large Chili | Cabe Merah | Rp 18.000-28.000/kg | Year-round |
| 9043 | Durian | Durian Monthong | Rp 60.000-80.000/pcs | Jun-Aug |

### Bali
| PLU | Standard Name | Local Name | Price Range | Season |
|-----|---------------|-------------|--------------|--------|
| 4011 | Red Delicious Apples | Apel | Rp 55.000-65.000/kg | Year-round |
| 4030 | Bananas | Pisang | Rp 20.000-30.000/kg | Year-round |
| 9001 | Red Large Chili | Cabe Merah | Rp 35.000-50.000/kg | Year-round |
| 9046 | Salak | Salak Pondoh | Rp 25.000-35.000/kg | Dec-Feb |

---

## 📈 SEASONAL AVAILABILITY

### Fruits Seasonality
| Product | Peak Season | Available | Notes |
|---------|--------------|------------|-------|
| Mangoes | Oct-Jan | Sep-Feb | Best quality Dec-Jan |
| Durian | Jun-Aug | May-Sep | Peak Jul |
| Rambutan | Oct-Jan | Sep-Feb | Best quality Nov |
| Mangosteen | Oct-Jan | Sep-Feb | Peak Nov-Dec |
| Salak Pondoh | Dec-Feb | Nov-Mar | Best quality Jan |
| Strawberries | Year-round | Year-round | Greenhouse grown |

### Vegetables Seasonality
| Product | Peak Season | Available | Notes |
|---------|--------------|------------|-------|
| Tomatoes | Year-round | Year-round | Greenhouse supplement |
| Chili | Year-round | Year-round | Higher prices Apr-Jun |
| Kangkung | Year-round | Year-round | Better quality rainy season |
| Bayam | Year-round | Year-round | Faster growth rainy season |
| Cabai Rawit | Year-round | Year-round | Spicier in dry season |

---

## 🥗 NUTRITION INFORMATION

### Common Fruits
| Product | Calories | Protein | Carbs | Fiber | Vitamin C |
|---------|----------|---------|--------|-------|-----------|
| Apples | 52 | 0.3g | 14g | 2.4g | 4.6mg |
| Bananas | 89 | 1.1g | 23g | 2.6g | 8.7mg |
| Oranges | 47 | 0.9g | 12g | 2.4g | 53.2mg |
| Grapes | 69 | 0.7g | 18g | 0.9g | 3.2mg |
| Strawberries | 32 | 0.7g | 8g | 2.0g | 58.8mg |

### Common Vegetables
| Product | Calories | Protein | Carbs | Fiber | Vitamin A |
|---------|----------|---------|--------|-------|-----------|
| Tomatoes | 18 | 0.9g | 3.9g | 1.2g | 833 IU |
| Carrots | 41 | 0.9g | 10g | 2.8g | 16706 IU |
| Spinach | 23 | 2.9g | 3.6g | 2.2g | 4696 IU |
| Broccoli | 34 | 2.8g | 7g | 2.6g | 623 IU |
| Onions | 40 | 1.1g | 9g | 1.7g | 0 IU |

---

## 🏪 CERTIFICATION STANDARDS

### Organic Certification
| Standard | Country | Code | Validity | Notes |
|----------|----------|-------|-----------|-------|
| USDA Organic | USA | 94000-94999 | 1 year | International standard |
| EU Organic | EU | 94000-94999 | 1 year | European standard |
| Indonesia Organic | ID | 9000-9999 | 1 year | Local standard |

### Halal Certification
| Standard | Country | Code | Validity | Notes |
|----------|----------|-------|-----------|-------|
| MUI Halal | Indonesia | HALAL-ID | 2 years | National standard |
| MUI Halal | Indonesia | HALAL-ID | 2 years | For processed foods |

---

## 🔄 MAINTENANCE & UPDATES

### Quarterly Updates
- **IFPS PLU updates** - New product codes
- **Seasonal pricing** - Market adjustments
- **New local products** - Regional additions
- **Certification renewals** - Organic/Halal status

### Annual Reviews
- **Price range analysis** - Inflation adjustments
- **Product lifecycle** - Discontinued items
- **Regional expansion** - New provinces
- **Standard compliance** - Regulation updates

---

## 📱 IMPLEMENTATION GUIDE

### Database Integration
```sql
-- Import PLU master data
INSERT INTO plu_codes (plu_number, product_name, category, subcategory, variety, standard_unit, is_organic, is_conventional)
VALUES 
('4011', 'Red Delicious Apples', 'FRUITS', 'APPLES', 'RED DELICIOUS', 'KG', FALSE, TRUE),
('4030', 'Bananas', 'FRUITS', 'BANANAS', 'CAVENDISH', 'KG', FALSE, TRUE),
('9001', 'Red Large Chili', 'VEGETABLES', 'CHILI', 'BESAR', 'KG', FALSE, TRUE);

-- Provincial mapping
INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date)
VALUES 
(1, 1, 31, 'Apel Merah', TRUE, '2024-01-01'),
(1, 1, 32, 'Apel Malang', TRUE, '2024-01-01'),
(1, 1, 33, 'Apel', TRUE, '2024-01-01');
```

### API Integration
```json
{
  "plu_lookup": {
    "code": "4011",
    "standard_name": "Red Delicious Apples",
    "category": "FRUITS",
    "subcategory": "APPLES",
    "variety": "RED DELICIOUS",
    "unit": "KG",
    "organic": false,
    "conventional": true
  },
  "provincial_data": {
    "province_id": 31,
    "local_name": "Apel Merah",
    "price_range": "45000-55000",
    "season": "year-round"
  }
}
```

---

## 📞 SUPPORT & CONTACTS

### PLU Standards Support
- **IFPS:** info@plucodes.com
- **GS1 Indonesia:** info@gs1id.org
- **Badan POM:** info@pom.go.id

### Local Product Support
- **Kementan:** contact@pertanian.go.id
- **Distan Provinsi:** Regional agricultural offices

---

*Document ini akan di-update setiap quarter untuk menjaga keakuratan data PLU dan informasi produk lokal.*
