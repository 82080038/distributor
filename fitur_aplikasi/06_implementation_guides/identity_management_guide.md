# Identity & People Management Implementation Guide

## **Overview**
Implementasi sistem manajemen identitas dan data orang untuk mendukung BPS (Badan Pelayanan Statistik), perizinan usaha, dan kepatuhan terhadap regulasi Indonesia.

## **Prerequisites**
- PHP 8.0+ dengan extension GD2 untuk image processing
- MySQL 8.0+ dengan full-text search
- File storage untuk upload dokumen
- OCR (Optical Character Recognition) untuk scan dokumen otomatis
- Validasi NIK dan data kependudukan

## **Database Setup**

### **1. Import Schema**
```bash
mysql -u username -p database_name < database_schema/identity_management_schema.sql
```

### **2. Enable Full-Text Search**
```sql
-- Enable full-text search untuk nama dan alamat
ALTER TABLE orang ADD FULLTEXT(nama_lengkap, alamat);
ALTER TABLE alamat ADD FULLTEXT(alamat_lengkap, kelurahan, kecamatan, kabupaten_kota, provinsi);

-- Create index untuk pencarian cepat
CREATE FULLTEXT INDEX ft_nama_lengkap ON orang(nama_lengkap);
CREATE FULLTEXT INDEX ft_alamat ON alamat(alamat_lengkap);
```

### **3. Data Validation**
```sql
-- Validasi format NIK (16 digit)
ALTER TABLE orang ADD CONSTRAINT chk_nik_length CHECK (LENGTH(no_ktp) = 16);

-- Validasi format NPWP
ALTER TABLE orang ADD CONSTRAINT chk_npwp_format CHECK (no_npwp REGEXP '^[0-9]{2}[0-9]{6}[0-9]{3}$');

-- Validasi email format
ALTER TABLE orang ADD CONSTRAINT chk_email_format CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$');
```

## **File Structure**

### **Backend Files**
```
src/
├── controllers/
│   ├── PersonController.php
│   ├── DocumentController.php
│   ├── AddressController.php
│   ├── EducationController.php
│   ├── WorkExperienceController.php
│   ├── LicenseController.php
│   └── BankAccountController.php
├── models/
│   ├── Person.php
│   ├── Document.php
│   ├── Address.php
│   ├── Education.php
│   ├── WorkExperience.php
│   ├── License.php
│   └── BankAccount.php
├── services/
│   ├── IdentityService.php
│   ├── DocumentService.php
│   ├── ValidationService.php
│   ├── OCRService.php
│   └── FileStorageService.php
├── middleware/
│   ├── AuthMiddleware.php
│   └── ValidationMiddleware.php
└── helpers/
    ├── NIKValidator.php
    ├── NPWPValidator.php
    ├── DocumentHelper.php
    └── FileUploader.php
```

### **Frontend Files**
```
public/
├── identity/
│   ├── dashboard.html
│   ├── person-list.html
│   ├── person-detail.html
│   ├── document-manager.html
│   ├── address-manager.html
│   └── education-history.html
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/
└── api/
    └── identity_api.php
```

## **Implementation Steps**

### **Phase 1: Core Identity Management (Week 1-2)**

#### **1. Master Person Management**
```php
// src/controllers/PersonController.php
class PersonController {
    public function index() {
        // Display person list with search and filters
    }
    
    public function create() {
        // Create new person with NIK validation
    }
    
    public function store() {
        $person = new Person();
        $result = $person->create($_POST);
        
        // Auto-generate NIK if not provided
        if (empty($_POST['no_ktp'])) {
            $_POST['no_ktp'] = $this->generateNIK($_POST['tempat_lahir'], $_POST['tanggal_lahir']);
        }
        
        // Validate NIK format
        $validator = new NIKValidator();
        if (!$validator->validate($_POST['no_ktp'])) {
            return ['error' => 'Invalid NIK format'];
        }
        
        return $result;
    }
    
    public function update($id) {
        // Update person data with audit trail
        $person = new Person();
        return $person->update($id, $_POST);
    }
    
    public function validateNIK($tempat_lahir, $tanggal_lahir) {
        // Generate NIK based on birth place and date
        // Format: PPLLDDMMYY (Provinsi, Kabupaten, etc.)
        // This is simplified - actual NIK generation is more complex
        $provinsi_code = $this->getProvinceCode($tempat_lahir);
        $kabupaten_code = $this->getKabupatenCode($tempat_lahir);
        $date_part = date('dmy', strtotime($tanggal_lahir));
        
        return $provinsi_code . $kabupaten_code . $date_part;
    }
}
```

#### **2. Document Management**
```php
// src/services/DocumentService.php
class DocumentService {
    private $storage_path;
    
    public function __construct() {
        $this->storage_path = __DIR__ . '/../../uploads/documents/';
    }
    
    public function uploadDocument($person_id, $file_data, $document_type) {
        // Validate file type and size
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file_data['type'], $allowed_types)) {
            throw new Exception("Invalid file type");
        }
        
        if ($file_data['size'] > $max_size) {
            throw new Exception("File too large");
        }
        
        // Generate unique filename
        $filename = $document_type . '_' . time() . '_' . uniqid() . '.' . pathinfo($file_data['name'], PATHINFO_EXTENSION);
        
        // Move file to storage
        $upload_path = $this->storage_path . $filename;
        if (!move_uploaded_file($file_data['tmp_name'], $upload_path)) {
            throw new Exception("Failed to upload file");
        }
        
        // Save to database
        $document = new Document();
        $document->create([
            'id_orang' => $person_id,
            'jenis_dokumen' => $document_type,
            'nomor_dokumen' => $this->generateDocumentNumber($person_id, $document_type),
            'file_dokumen' => $upload_path,
            'tanggal_terbit' => $this->getDocumentExpiryDate($document_type),
            'status_dokumen' => 'AKTIF'
        ]);
        
        return $upload_path;
    }
    
    public function generateDocumentNumber($person_id, $document_type) {
        $prefix = $this->getDocumentPrefix($document_type);
        $sequence = $this->getDocumentSequence($person_id, $document_type);
        
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    public function processOCR($file_path) {
        $ocr_service = new OCRService();
        return $ocr_service->extractText($file_path);
    }
}
```

#### **3. Address Management**
```php
// src/models/Address.php
class Address {
    public function getFullAddress($id) {
        $sql = "
            SELECT 
                a.*,
                o.nama_lengkap,
                oa.jenis_alamat,
                oa.status_alamat
            FROM alamat a
            JOIN orang_alamat oa ON a.id = oa.id_alamat
            JOIN orang o ON oa.id_orang = o.id_orang
            WHERE a.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result) {
            return [
                'id' => $result['id'],
                'alamat_lengkap' => $result['alamat_lengkap'],
                'rt' => $result['rt'],
                'rw' => $result['rw'],
                'kelurahan' => $result['kelurahan'],
                'kecamatan' => $result['kecamatan'],
                'kabupaten_kota' => $result['kabupaten_kota'],
                'provinsi' => $result['provinsi'],
                'kode_pos' => $result['kode_pos'],
                'jenis_alamat' => $result['jenis_alamat'],
                'status_alamat' => $result['status_alamat'],
                'owner_name' => $result['nama_lengkap'],
                'owner_phone' => $result['phone'],
                'owner_email' => $result['email']
            ];
        }
        
        return null;
    }
    
    public function validateAddress($data) {
        $errors = [];
        
        // Validate required fields
        $required_fields = ['alamat_lengkap', 'kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field $field is required";
            }
        }
        
        // Validate postal code format
        if (!empty($data['kode_pos']) && !preg_match('/^[0-9]{5}$/', $data['kode_pos'])) {
            $errors[] = 'Invalid postal code format';
        }
        
        return empty($errors) ? true : $errors;
    }
}
```

### **Phase 2: Advanced Features (Week 3-4)**

#### **1. Education & Work History**
```php
// src/models/Education.php
class Education {
    public function getEducationHistory($person_id) {
        $sql = "
            SELECT 
                po.*,
                i.nama_institusi
            FROM pendidikan_orang po
            LEFT JOIN institusi_pendidikan i ON po.nama_institusi = i.nama_institusi
            WHERE po.id_orang = ?
            ORDER BY po.tahun_lulus DESC, po.tahun_masuk ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $person_id);
        $stmt->execute();
        
        $education_history = [];
        while ($row = $stmt->get_result()->fetch_assoc()) {
            $education_history[] = [
                'id' => $row['id'],
                'jenjang_pendidikan' => $row['jenjang_pendidikan'],
                'nama_institusi' => $row['nama_institusi'],
                'jurusan' => $row['jurusan'],
                'tahun_masuk' => $row['tahun_masuk'],
                'tahun_lulus' => $row['tahun_lulus'],
                'predikat_kelulusan' => $row['predikat_kelulusan'],
                'ipk' => $row['ipk'],
                'duration_years' => $row['tahun_lulus'] - $row['tahun_masuk']
            ];
        }
        
        $stmt->close();
        return $education_history;
    }
}
```

#### **2. License & Permit Management**
```php
// src/controllers/LicenseController.php
class LicenseController {
    public function index() {
        // Display all licenses with expiry tracking
    }
    
    public function checkExpiry() {
        $sql = "
            SELECT 
                pu.*,
                o.nama_lengkap,
                o.email,
                DATEDIFF(pu.tanggal_berlaku, CURDATE()) as days_to_expiry
            FROM perizinan_usaha pu
            JOIN orang o ON pu.id_orang = o.id_orang
            WHERE pu.status_perizinan = 'AKTIF'
            ORDER BY pu.tanggal_berlaku ASC
        ";
        
        $result = $this->db->query($sql);
        
        $expiring_soon = [];
        $expired = [];
        
        while ($row = $result->fetch_assoc()) {
            if ($row['days_to_expiry'] <= 30) {
                $expiring_soon[] = $row;
            } elseif ($row['days_to_expiry'] < 0) {
                $expired[] = $row;
            }
        }
        
        return [
            'expiring_soon' => $expiring_soon,
            'expired' => $expired
        ];
    }
    
    public function sendExpiryReminder($license_id) {
        $license = $this->getLicenseDetails($license_id);
        
        if ($license) {
            $email_service = new EmailService();
            $email_service->sendLicenseExpiryReminder($license['email'], $license);
        }
    }
}
```

### **Phase 3: Compliance & Security (Week 5-6)**

#### **1. BPS Integration**
```php
// src/services/BPSIntegrationService.php
class BPSIntegrationService {
    private $api_endpoint;
    private $api_key;
    
    public function __construct() {
        $this->api_endpoint = 'https://api.bps.go.id/v1/'; // Example endpoint
        $this->api_key = 'your_bps_api_key';
    }
    
    public function validateNIK($nik) {
        // Validate NIK format using BPS algorithm
        if (!$this->isValidNIKFormat($nik)) {
            return ['valid' => false, 'error' => 'Invalid NIK format'];
        }
        
        // Check against BPS database (if available)
        $validation_result = $this->checkNIKWithBPS($nik);
        
        return $validation_result;
    }
    
    public function syncPersonData($person_id) {
        $person_data = $this->getPersonCompleteData($person_id);
        
        // Sync with BPS for validation
        $response = $this->sendToBPS('validate-person', $person_data);
        
        return $response;
    }
    
    private function isValidNIKFormat($nik) {
        // BPS NIK format validation
        return preg_match('/^[0-9]{6}[0-9]{6}[0-9]{4}$/', $nik);
    }
    
    private function checkNIKWithBPS($nik) {
        // Implementation for BPS API integration
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_endpoint . 'nik-validation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['nik' => $nik])
        ]);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($http_code === 200) {
            return json_decode($response, true);
        }
        
        return ['valid' => false, 'error' => 'BPS service unavailable'];
    }
}
```

#### **2. Data Privacy & Security**
```php
// src/middleware/DataProtectionMiddleware.php
class DataProtectionMiddleware {
    public static function encryptPersonalData($data) {
        // Encrypt sensitive personal data
        $key = 'your_encryption_key';
        $method = 'AES-256-CBC';
        
        return openssl_encrypt(
            json_encode($data),
            $method,
            $key,
            0,
            base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length($method))),
            base64_encode($key)
        );
    }
    
    public static function logDataAccess($user_id, $action, $data_type, $record_id) {
        // Log all access to personal data
        $sql = "
            INSERT INTO data_access_logs 
            (user_id, action, data_type, record_id, ip_address, user_agent, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param('isssss', 
            $user_id, $action, $data_type, $record_id, 
            $_SERVER['REMOTE_ADDR'], 
            $_SERVER['HTTP_USER_AGENT'], 
            date('Y-m-d H:i:s')
        );
        $stmt->execute();
        $stmt->close();
    }
}
```

## **Security Considerations**

### **1. Data Protection**
- **Encryption** untuk data pribadi
- **Access control** berbasis role
- **Audit trail** untuk semua akses data
- **Data retention** sesuai regulasi
- **Right to be forgotten** (GDPR-like)

### **2. Compliance Features**
- **NIK validation** dengan algoritma BPS
- **Document expiry** tracking otomatis
- **License management** dengan reminder otomatis
- **Data backup** terjadwal dan terenkripsi

### **3. Performance Optimization**
- **Indexing** untuk pencarian cepat
- **Caching** untuk data yang sering diakses
- **Lazy loading** untuk data besar
- **Pagination** untuk dataset besar

## **Testing Strategy**

### **1. Unit Tests**
```php
// tests/IdentityTest.php
class IdentityTest extends PHPUnit\Framework\TestCase {
    public function testNIKValidation() {
        $validator = new NIKValidator();
        
        // Test valid NIK
        $this->assertTrue($validator->validate('3201011234560001'));
        
        // Test invalid NIK
        $this->assertFalse($validator->validate('123456789'));
        $this->assertFalse($validator->validate('32010112345678')); // Too short
    }
    
    public function testDocumentUpload() {
        $document_service = new DocumentService();
        
        // Test valid file upload
        $result = $document_service->uploadDocument(1, [
            'name' => 'test_ktp.jpg',
            'type' => 'image/jpeg',
            'size' => 1024,
            'tmp_name' => '/tmp/test_file'
        ], 'KTP');
        
        $this->assertTrue($result);
        
        // Test invalid file type
        $this->expectException(InvalidArgumentException::class);
        $document_service->uploadDocument(1, [
            'name' => 'test.exe',
            'type' => 'application/exe',
            'size' => 1024,
            'tmp_name' => '/tmp/test_file'
        ], 'KTP');
    }
}
```

### **2. Integration Tests**
```php
// tests/BPSIntegrationTest.php
class BPSIntegrationTest extends PHPUnit\Framework\TestCase {
    public function testBPSConnection() {
        $service = new BPSIntegrationService();
        
        // Mock BPS response
        $service->setMockMode(true);
        
        $result = $service->validateNIK('3201011234560001');
        
        $this->assertTrue($result['valid']);
        $this->assertArrayHasKey('data', $result);
    }
}
```

## **Deployment Checklist**

### **Pre-deployment**
- [ ] Database schema imported
- [ ] All identity tables created
- [ ] Full-text search enabled
- [ ] NIK validation implemented
- [ ] Document upload functionality working
- [ ] BPS integration tested
- [ ] Data encryption implemented
- [ ] Access control configured

### **Post-deployment**
- [ ] Monitor data access logs
- [ ] Test NIK validation with real data
- [ ] Verify document expiry tracking
- [ ] Check BPS API integration
- [ ] Performance monitoring enabled

---

**Timeline Estimate:** 8 weeks for full implementation
**Team Size:** 2-3 developers
**Success Criteria:** All CRUD operations working, NIK validation functional, document management complete, BPS integration tested
