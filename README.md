# 🎓 University Admission System

## 🌟 Overview
The **University Admission System** is a robust, enterprise-grade web portal designed to automate the entire student intake process. It replaces manual spreadsheets with a streamlined, dual-interface platform that handles applicant data collection, automated scoring, and admission decision dissemination.

Built with **Laravel 12** and **Docker**, it is cloud-ready and features "Zero-Config" deployment capabilities.

---

## 🚀 Key Features

### 👤 Applicant Portal
*   **Secure Identity Management:** Dedicated centralized authentication for thousands of applicants.
*   **Smart Application Form:** Validates JAMB scores (0-400), subject combinations, and O-Level grades before submission.
*   **Real-Time Status Dashboard:** Applicants can track their lifecycle state (`Pending` → `Processing` → `Admitted/Rejected`) instantly.
*   **Admission Letters:** (Planned) Automated generation of provisional admission offers.

### 🛡️ Admin Command Center
*   **Operational Dashboard:** High-level metrics on total applications, eligible candidates, and admission quotas.
*   **Bulk Data Ingestion:**
    *   **CSV Importer:** Robust parser capable of handling thousands of records with error skipping and detailed logging.
    *   **Manual Entry:** Rapid single-entry forms for walk-in candidates.
*   **🤖 Automated Admission Engine:**
    *   **Single-Click Processing:** Runs complex logic against the entire applicant pool in seconds.
    *   **Cut-off Logic:** Automatically compares Applicant Scores vs. Course Thresholds.
    *   **Quota Management:** Respects department capacity limits (configurable).
    *   **Notification Dispatch:** Automatically triggers email/in-app notifications upon decision.
*   **Visual Analytics:** Interactive `Chart.js` reports showing "Intake by Department" and "Admitted vs. Rejected" ratios.

---

## 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Framework** | Laravel 11/12 (PHP 8.2) |
| **Frontend** | Blade Templates, Bootstrap 5, FontAwesome |
| **Database** | MySQL (Local), PostgreSQL (Production) |
| **Server** | Apache (via Docker Container) |
| **Deployment** | Render.com (Auto-Healing, Zero-Config) |
| **Security** | BCrypt Hashing, CSRF Protection, Signed URLs |

---

## ⚙️ Installation & Setup

### Local Development (XAMPP/Docker)
1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/Emzy123/admission-system.git
    cd admission-system
    ```
2.  **Install Dependencies:**
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Environment Setup:**
    ```bash
    cp .env.example .env
    # Configure DB_DATABASE, DB_USERNAME, etc. in .env
    php artisan key:generate
    ```
4.  **Database Initialization:**
    ```bash
    php artisan migrate --seed
    ```
5.  **Serve:**
    ```bash
    php artisan serve
    ```

### Production (Render/Cloud)
*   **Zero-Config Deployment:** The system detects production environments and automatically runs migrations and seeds the Admin user on first boot in `AppServiceProvider`.
*   **Emergency Routes:**
    *   `/system/fix-admin`: Resets admin credentials if locked out.
    *   `/system/clear-cache`: Force clears stale application configs.

---

## 🗺️ Roadmap to Standardization (Enterprise Ready)
To upgrade this system to a fully standard, commercial-grade product, the following enhancements are recommended:

### 1. Robust Access Control (RBAC)
*   [ ] **Current:** Single "Admin" role.
*   **Upgrade:** Implement **Spatie Permissions**. Create roles like `Admission Officer` (View Only), `Registrar` (Can Approve), and `Super Admin` (System Config).

### 2. Advanced Scoring Algorithm
*   [ ] **Current:** Simple `JAMB Score >= Cutoff`.
*   **Upgrade:** Implement **Weighted O-Level Calculation** (e.g., A1=6 points, B2=5 points) combined with JAMB scores for a precise "Aggregate Point System".

### 3. Financial Integration
*   [ ] **Current:** Free application.
*   **Upgrade:** Integrate **Paystack** or **Flutterwave**. Require payment of an "Application Fee" before form submission, and an "Acceptance Fee" after admission.

### 4. Document Management
*   [ ] **Current:** Data entry only.
*   **Upgrade:** Allow applicants to upload scanned PDFs of WAEC/NECO results and Birth Certificates. Implement S3/Cloudinary storage.

### 5. Audit Trails
*   [ ] **Current:** No logs of admin actions.
*   **Upgrade:** Record every action (e.g., *"Admin X changed Applicant Y's status to Admitted at 10:00 AM"*) for accountability.

### 6. PDF Generation
*   [ ] **Current:** Screen notification.
*   **Upgrade:** Generate official, downloadable PDF Admission Letters with QR Code verification.

---

## 📝 License
Proprietary / University Internal Use.
