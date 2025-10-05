# API Documentation - Barangay Submission and Report System

## 🌐 API Overview

The Barangay Submission and Report System provides a comprehensive RESTful API for managing report submissions, user management, and system administration. This API follows REST conventions and returns JSON responses.

### **Base URL**
```
Production: https://your-domain.com/api
Development: http://localhost:8000/api
```

### **Authentication**
The API uses Laravel's built-in authentication system with session-based authentication for web clients and token-based authentication for mobile/third-party clients.

---

## 🔐 Authentication Endpoints

### **Login**
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "user_type": "barangay",
        "cluster_id": 1,
        "is_active": true
    }
}
```

**Response (Error - 401)**
```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

### **Logout**
```http
POST /api/logout
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "Logout successful"
}
```

### **Get Current User**
```http
GET /api/user
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "user_type": "barangay",
        "cluster_id": 1,
        "is_active": true,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

---

## 📊 Report Management Endpoints

### **Get Report Types**
```http
GET /api/report-types
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "Weekly Report",
            "frequency": "weekly",
            "deadline": "2024-01-07",
            "instructions": "Submit weekly activity report",
            "allowed_file_types": ["pdf", "docx"],
            "file_naming_format": "WEEKLY_{YEAR}_{MONTH}_{WEEK}",
            "archived_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

### **Submit Report**
```http
POST /api/reports/submit
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "report_type_id": 1,
    "file": [file],
    "remarks": "Optional remarks"
}
```

**Response (Success - 201)**
```json
{
    "status": "success",
    "message": "Report submitted successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "report_type_id": 1,
        "status": "submitted",
        "submitted_at": "2024-01-01T12:00:00.000000Z",
        "file_path": "reports/weekly/2024/01/weekly_report_1.pdf"
    }
}
```

### **Get User Submissions**
```http
GET /api/reports/submissions
Authorization: Bearer {token}
Query Parameters:
- page: Page number (default: 1)
- per_page: Items per page (default: 15)
- status: Filter by status (submitted, approved, rejected, overdue)
- report_type_id: Filter by report type
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "report_type": {
                    "id": 1,
                    "name": "Weekly Report",
                    "frequency": "weekly"
                },
                "status": "submitted",
                "submitted_at": "2024-01-01T12:00:00.000000Z",
                "file_path": "reports/weekly/2024/01/weekly_report_1.pdf",
                "remarks": "Optional remarks"
            }
        ],
        "first_page_url": "http://localhost:8000/api/reports/submissions?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/reports/submissions?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost:8000/api/reports/submissions",
        "per_page": 15,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```

### **Get Overdue Reports**
```http
GET /api/reports/overdue
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "report_type": {
                "id": 1,
                "name": "Weekly Report",
                "frequency": "weekly",
                "deadline": "2024-01-07"
            },
            "days_overdue": 5,
            "submitted_at": null
        }
    ]
}
```

### **Resubmit Report**
```http
POST /api/reports/{id}/resubmit
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "file": [file],
    "remarks": "Updated report with corrections"
}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "Report resubmitted successfully",
    "data": {
        "id": 1,
        "status": "submitted",
        "resubmitted_at": "2024-01-01T15:00:00.000000Z",
        "file_path": "reports/weekly/2024/01/weekly_report_1_v2.pdf"
    }
}
```

---

## 👥 User Management Endpoints (Admin Only)

### **Get All Users**
```http
GET /api/admin/users
Authorization: Bearer {admin_token}
Query Parameters:
- page: Page number (default: 1)
- per_page: Items per page (default: 15)
- user_type: Filter by user type (admin, facilitator, barangay)
- cluster_id: Filter by cluster
- is_active: Filter by active status
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "user_type": "barangay",
                "cluster": {
                    "id": 1,
                    "name": "Cluster 1"
                },
                "is_active": true,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "pagination": {...}
    }
}
```

### **Create User**
```http
POST /api/admin/users
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "password123",
    "user_type": "barangay",
    "cluster_id": 1,
    "is_active": true
}
```

**Response (Success - 201)**
```json
{
    "status": "success",
    "message": "User created successfully",
    "data": {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "user_type": "barangay",
        "cluster_id": 1,
        "is_active": true,
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

### **Update User**
```http
PUT /api/admin/users/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Jane Smith Updated",
    "email": "jane.updated@example.com",
    "is_active": false
}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "User updated successfully",
    "data": {
        "id": 2,
        "name": "Jane Smith Updated",
        "email": "jane.updated@example.com",
        "user_type": "barangay",
        "cluster_id": 1,
        "is_active": false,
        "updated_at": "2024-01-01T15:00:00.000000Z"
    }
}
```

### **Delete User**
```http
DELETE /api/admin/users/{id}
Authorization: Bearer {admin_token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "User deleted successfully"
}
```

---

## 📋 Report Type Management Endpoints (Admin Only)

### **Get All Report Types**
```http
GET /api/admin/report-types
Authorization: Bearer {admin_token}
Query Parameters:
- archived: Include archived types (true/false)
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "Weekly Report",
            "frequency": "weekly",
            "deadline": "2024-01-07",
            "instructions": "Submit weekly activity report",
            "allowed_file_types": ["pdf", "docx"],
            "file_naming_format": "WEEKLY_{YEAR}_{MONTH}_{WEEK}",
            "archived_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

### **Create Report Type**
```http
POST /api/admin/report-types
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Monthly Report",
    "frequency": "monthly",
    "deadline": "2024-02-01",
    "instructions": "Submit monthly activity report",
    "allowed_file_types": ["pdf", "docx", "xlsx"],
    "file_naming_format": "MONTHLY_{YEAR}_{MONTH}"
}
```

**Response (Success - 201)**
```json
{
    "status": "success",
    "message": "Report type created successfully",
    "data": {
        "id": 2,
        "name": "Monthly Report",
        "frequency": "monthly",
        "deadline": "2024-02-01",
        "instructions": "Submit monthly activity report",
        "allowed_file_types": ["pdf", "docx", "xlsx"],
        "file_naming_format": "MONTHLY_{YEAR}_{MONTH}",
        "archived_at": null,
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

### **Update Report Type**
```http
PUT /api/admin/report-types/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Monthly Report Updated",
    "instructions": "Updated instructions for monthly report",
    "allowed_file_types": ["pdf", "docx", "xlsx", "jpg"]
}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "Report type updated successfully",
    "data": {
        "id": 2,
        "name": "Monthly Report Updated",
        "frequency": "monthly",
        "deadline": "2024-02-01",
        "instructions": "Updated instructions for monthly report",
        "allowed_file_types": ["pdf", "docx", "xlsx", "jpg"],
        "file_naming_format": "MONTHLY_{YEAR}_{MONTH}",
        "updated_at": "2024-01-01T15:00:00.000000Z"
    }
}
```

### **Archive Report Type**
```http
DELETE /api/admin/report-types/{id}
Authorization: Bearer {admin_token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "Report type archived successfully"
}
```

---

## 📊 Analytics Endpoints

### **Get Dashboard Statistics**
```http
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": {
        "total_reports": 150,
        "submitted_reports": 120,
        "pending_reports": 20,
        "overdue_reports": 10,
        "approval_rate": 85.5,
        "recent_submissions": [
            {
                "id": 1,
                "report_type": "Weekly Report",
                "submitted_at": "2024-01-01T12:00:00.000000Z",
                "status": "approved"
            }
        ]
    }
}
```

### **Get Submission Analytics**
```http
GET /api/analytics/submissions
Authorization: Bearer {admin_token}
Query Parameters:
- start_date: Start date (YYYY-MM-DD)
- end_date: End date (YYYY-MM-DD)
- cluster_id: Filter by cluster
- report_type_id: Filter by report type
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": {
        "total_submissions": 500,
        "submissions_by_type": {
            "weekly": 200,
            "monthly": 150,
            "quarterly": 100,
            "semestral": 30,
            "annual": 20
        },
        "submissions_by_status": {
            "submitted": 400,
            "approved": 350,
            "rejected": 30,
            "overdue": 20
        },
        "submissions_by_cluster": [
            {
                "cluster_id": 1,
                "cluster_name": "Cluster 1",
                "submission_count": 150
            }
        ],
        "monthly_trend": [
            {
                "month": "2024-01",
                "submissions": 100
            },
            {
                "month": "2024-02",
                "submissions": 120
            }
        ]
    }
}
```

---

## 🔔 Notification Endpoints

### **Get User Notifications**
```http
GET /api/notifications
Authorization: Bearer {token}
Query Parameters:
- unread_only: Show only unread notifications (true/false)
- page: Page number (default: 1)
- per_page: Items per page (default: 15)
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "type": "App\\Notifications\\NewSubmissionReceivedNotification",
                "data": {
                    "message": "New report submission received",
                    "report_type": "Weekly Report",
                    "submitted_by": "John Doe"
                },
                "read_at": null,
                "created_at": "2024-01-01T12:00:00.000000Z"
            }
        ],
        "pagination": {...}
    }
}
```

### **Mark Notification as Read**
```http
PUT /api/notifications/{id}/read
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "Notification marked as read"
}
```

### **Mark All Notifications as Read**
```http
PUT /api/notifications/read-all
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "message": "All notifications marked as read"
}
```

---

## 📁 File Management Endpoints

### **Download File**
```http
GET /api/files/{id}/download
Authorization: Bearer {token}
```

**Response (Success - 200)**
```
File download with appropriate headers
Content-Type: application/pdf
Content-Disposition: attachment; filename="report.pdf"
```

### **Get File Information**
```http
GET /api/files/{id}
Authorization: Bearer {token}
```

**Response (Success - 200)**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "original_name": "weekly_report.pdf",
        "file_path": "reports/weekly/2024/01/weekly_report_1.pdf",
        "file_size": 1024000,
        "mime_type": "application/pdf",
        "uploaded_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

---

## 🚨 Error Responses

### **Validation Error (422)**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### **Authentication Error (401)**
```json
{
    "status": "error",
    "message": "Unauthenticated"
}
```

### **Authorization Error (403)**
```json
{
    "status": "error",
    "message": "Insufficient permissions"
}
```

### **Not Found Error (404)**
```json
{
    "status": "error",
    "message": "Resource not found"
}
```

### **Server Error (500)**
```json
{
    "status": "error",
    "message": "Internal server error"
}
```

---

## 📝 Rate Limiting

The API implements rate limiting to prevent abuse:

- **Authentication endpoints**: 5 requests per minute
- **Report submission**: 10 requests per minute
- **General API**: 60 requests per minute

**Rate Limit Headers**
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

**Rate Limit Exceeded Response (429)**
```json
{
    "status": "error",
    "message": "Too many requests",
    "retry_after": 60
}
```

---

## 🔧 SDK Examples

### **JavaScript/Node.js**
```javascript
const axios = require('axios');

const api = axios.create({
    baseURL: 'https://your-domain.com/api',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    }
});

// Submit report
const submitReport = async (reportData) => {
    const formData = new FormData();
    formData.append('report_type_id', reportData.reportTypeId);
    formData.append('file', reportData.file);
    
    return await api.post('/reports/submit', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    });
};
```

### **PHP**
```php
<?php
$client = new GuzzleHttp\Client([
    'base_uri' => 'https://your-domain.com/api/',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ]
]);

// Get submissions
$response = $client->get('reports/submissions');
$data = json_decode($response->getBody(), true);
?>
```

### **Python**
```python
import requests

headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json'
}

# Get dashboard stats
response = requests.get(
    'https://your-domain.com/api/dashboard/stats',
    headers=headers
)
data = response.json()
```

---

## 📚 Additional Resources

- **Postman Collection**: Available for download
- **OpenAPI Specification**: Swagger documentation
- **SDK Libraries**: Available for major programming languages
- **Webhook Documentation**: Real-time event notifications

---

*This API documentation is maintained alongside the codebase and updated with each release.*
