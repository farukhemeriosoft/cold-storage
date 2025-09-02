<?php

// Test script to demonstrate the authentication flow
// Run with: php test-auth-flow.php

echo "🔐 Laravel Sanctum Authentication Flow Test\n";
echo "==========================================\n\n";

// Step 1: Login (replace with your actual credentials)
echo "1. Testing Login...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$loginResponse = makeRequest('POST', 'http://127.0.0.1:8005/api/login', $loginData);
echo "Login Response: " . $loginResponse . "\n\n";

// Extract token from response
$loginResult = json_decode($loginResponse, true);
$token = $loginResult['token'] ?? null;

if ($token) {
    echo "✅ Token received: " . substr($token, 0, 20) . "...\n\n";

    // Step 2: Test /me endpoint
    echo "2. Testing /me endpoint...\n";
    $meResponse = makeRequest('GET', 'http://127.0.0.1:8005/api/me', null, $token);
    echo "Me Response: " . $meResponse . "\n\n";

    // Step 3: Test logout
    echo "3. Testing logout...\n";
    $logoutResponse = makeRequest('POST', 'http://127.0.0.1:8005/api/logout', null, $token);
    echo "Logout Response: " . $logoutResponse . "\n\n";

    // Step 4: Test /me after logout (should fail)
    echo "4. Testing /me after logout (should fail)...\n";
    $meAfterLogoutResponse = makeRequest('GET', 'http://127.0.0.1:8005/api/me', null, $token);
    echo "Me After Logout Response: " . $meAfterLogoutResponse . "\n\n";

} else {
    echo "❌ No token received. Make sure you have a user account.\n";
    echo "Try signing up first with: POST /api/signup\n";
}

function makeRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $headers = ['Content-Type: application/json'];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return "HTTP $httpCode: $response";
}
