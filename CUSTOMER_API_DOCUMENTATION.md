# Customer Management API Documentation

This document describes the API endpoints for managing customers in the cold storage system.

## Authentication

All customer management endpoints require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your_token}
```

## Customer Endpoints

### 1. Get All Customers

**GET** `/api/customers`

Retrieve a paginated list of customers with optional filtering and search.

**Query Parameters:**
- `status` (optional): Filter by status (`active` or `inactive`)
- `search` (optional): Search by name, CNIC, or phone number
- `page` (optional): Page number for pagination

**Example Request:**
```bash
GET /api/customers?status=active&search=john&page=1
```

**Example Response:**
```json
{
    "message": "Customers retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "full_name": "John Doe",
                "cnic_number": "12345-1234567-1",
                "phone_number": "+92-300-1234567",
                "address": "123 Main Street, Karachi",
                "is_active": true,
                "created_at": "2025-09-02T17:52:12.000000Z",
                "updated_at": "2025-09-02T17:52:12.000000Z",
                "baskets": []
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### 2. Create New Customer

**POST** `/api/customers`

Create a new customer.

**Request Body:**
```json
{
    "full_name": "John Doe",
    "cnic_number": "12345-1234567-1",
    "phone_number": "+92-300-1234567",
    "address": "123 Main Street, Karachi"
}
```

**Validation Rules:**
- `full_name`: Required, string, max 255 characters
- `cnic_number`: Required, string, unique, format: 12345-1234567-1
- `phone_number`: Required, string, max 20 characters
- `address`: Required, string, max 1000 characters

**Example Response:**
```json
{
    "message": "Customer created successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "cnic_number": "12345-1234567-1",
        "phone_number": "+92-300-1234567",
        "address": "123 Main Street, Karachi",
        "is_active": true,
        "created_at": "2025-09-02T17:52:12.000000Z",
        "updated_at": "2025-09-02T17:52:12.000000Z",
        "baskets": []
    }
}
```

### 3. Get Single Customer

**GET** `/api/customers/{id}`

Retrieve a specific customer by ID.

**Example Request:**
```bash
GET /api/customers/1
```

**Example Response:**
```json
{
    "message": "Customer retrieved successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "cnic_number": "12345-1234567-1",
        "phone_number": "+92-300-1234567",
        "address": "123 Main Street, Karachi",
        "is_active": true,
        "created_at": "2025-09-02T17:52:12.000000Z",
        "updated_at": "2025-09-02T17:52:12.000000Z",
        "baskets": []
    }
}
```

### 4. Update Customer

**PUT/PATCH** `/api/customers/{id}`

Update customer information. All fields are optional for PATCH requests.

**Request Body (PUT - all fields required):**
```json
{
    "full_name": "John Smith",
    "cnic_number": "12345-1234567-1",
    "phone_number": "+92-300-1234567",
    "address": "456 New Street, Lahore"
}
```

**Request Body (PATCH - partial update):**
```json
{
    "phone_number": "+92-300-7654321",
    "address": "789 Updated Street, Islamabad"
}
```

**Example Response:**
```json
{
    "message": "Customer updated successfully",
    "data": {
        "id": 1,
        "full_name": "John Smith",
        "cnic_number": "12345-1234567-1",
        "phone_number": "+92-300-7654321",
        "address": "789 Updated Street, Islamabad",
        "is_active": true,
        "created_at": "2025-09-02T17:52:12.000000Z",
        "updated_at": "2025-09-02T17:52:12.000000Z",
        "baskets": []
    }
}
```

### 5. Deactivate Customer

**PATCH** `/api/customers/{id}/deactivate`

Deactivate a customer (soft delete - sets is_active to false).

**Example Request:**
```bash
PATCH /api/customers/1/deactivate
```

**Example Response:**
```json
{
    "message": "Customer deactivated successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "cnic_number": "12345-1234567-1",
        "phone_number": "+92-300-1234567",
        "address": "123 Main Street, Karachi",
        "is_active": false,
        "created_at": "2025-09-02T17:52:12.000000Z",
        "updated_at": "2025-09-02T17:52:12.000000Z"
    }
}
```

### 6. Activate Customer

**PATCH** `/api/customers/{id}/activate`

Reactivate a previously deactivated customer.

**Example Request:**
```bash
PATCH /api/customers/1/activate
```

**Example Response:**
```json
{
    "message": "Customer activated successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "cnic_number": "12345-1234567-1",
        "phone_number": "+92-300-1234567",
        "address": "123 Main Street, Karachi",
        "is_active": true,
        "created_at": "2025-09-02T17:52:12.000000Z",
        "updated_at": "2025-09-02T17:52:12.000000Z"
    }
}
```

### 7. Get Customer's Baskets

**GET** `/api/customers/{id}/baskets`

Retrieve all baskets belonging to a specific customer.

**Example Request:**
```bash
GET /api/customers/1/baskets
```

**Example Response:**
```json
{
    "message": "Customer baskets retrieved successfully",
    "data": {
        "customer": {
            "id": 1,
            "full_name": "John Doe",
            "cnic_number": "12345-1234567-1",
            "phone_number": "+92-300-1234567",
            "address": "123 Main Street, Karachi",
            "is_active": true,
            "created_at": "2025-09-02T17:52:12.000000Z",
            "updated_at": "2025-09-02T17:52:12.000000Z"
        },
        "baskets": [
            {
                "id": 1,
                "customer_id": 1,
                "basket_number": "BASKET-001",
                "description": "Storage basket for electronics",
                "is_active": true,
                "created_at": "2025-09-02T17:52:12.000000Z",
                "updated_at": "2025-09-02T17:52:12.000000Z"
            }
        ]
    }
}
```

## Error Responses

### Validation Errors (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "cnic_number": [
            "CNIC number must be in format: 12345-1234567-1"
        ],
        "full_name": [
            "Full name is required."
        ]
    }
}
```

### Not Found (404)
```json
{
    "message": "No query results for model [App\\Models\\Customer] 1"
}
```

### Unauthorized (401)
```json
{
    "message": "Unauthenticated."
}
```

## Database Schema

### Customers Table
- `id` (bigint, primary key)
- `full_name` (varchar)
- `cnic_number` (varchar, unique)
- `phone_number` (varchar)
- `address` (text)
- `is_active` (boolean, default: true)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Baskets Table
- `id` (bigint, primary key)
- `customer_id` (bigint, foreign key)
- `basket_number` (varchar, unique)
- `description` (text, nullable)
- `is_active` (boolean, default: true)
- `created_at` (timestamp)
- `updated_at` (timestamp)

## Notes

1. **CNIC Format**: CNIC numbers must follow the Pakistani format: `12345-1234567-1`
2. **Soft Delete**: Customers are not permanently deleted, they are deactivated by setting `is_active` to false
3. **Authentication**: All endpoints require a valid Bearer token
4. **Pagination**: The index endpoint returns paginated results (15 per page by default)
5. **Search**: The search functionality works across name, CNIC, and phone number fields
6. **Relationships**: Each customer can have multiple baskets, and each basket belongs to one customer









