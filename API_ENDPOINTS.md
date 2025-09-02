# Authentication API Endpoints

This document describes the authentication endpoints available in the API.

## Base URL
All endpoints are prefixed with `/api/`

## Public Endpoints (No Authentication Required)

### 1. User Registration
**POST** `/api/signup`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
}
```

### 2. User Login
**POST** `/api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "2|def456...",
    "token_type": "Bearer"
}
```

### 3. Forgot Password
**POST** `/api/forgot-password`

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response (Production with email configured):**
```json
{
    "message": "Password reset link sent to your email"
}
```

**Response (Development/Testing - when email is not configured):**
```json
{
    "message": "Password reset token generated (for development)",
    "token": "abc123def456...",
    "email": "john@example.com",
    "note": "In production, this token would be sent via email"
}
```

### 4. Reset Password
**POST** `/api/reset-password`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123",
    "token": "reset_token_from_email"
}
```

**Response:**
```json
{
    "message": "Password reset successfully"
}
```

## Protected Endpoints (Authentication Required)

Include the Bearer token in the Authorization header:
```
Authorization: Bearer your_token_here
```

### 5. Get Current User
**GET** `/api/me`

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

### 6. Logout
**POST** `/api/logout`

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

## Validation Rules

### Signup Request
- `name`: required, string, max 255 characters
- `email`: required, valid email, unique in users table
- `password`: required, string, minimum 8 characters, must be confirmed

### Login Request
- `email`: required, valid email
- `password`: required, string, minimum 8 characters

### Forgot Password Request
- `email`: required, valid email, must exist in users table

### Reset Password Request
- `email`: required, valid email, must exist in users table
- `password`: required, string, minimum 8 characters, must be confirmed
- `token`: required, string (from password reset email)

## Error Responses

All endpoints return appropriate HTTP status codes and error messages:

- `400 Bad Request`: Validation errors
- `401 Unauthorized`: Invalid credentials or missing/invalid token
- `422 Unprocessable Entity`: Validation failed
- `500 Internal Server Error`: Server error

Example error response:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```
