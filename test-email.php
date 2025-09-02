<?php

// Simple email test script
// Run this with: php test-email.php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test email sending
    Mail::raw('This is a test email from Laravel!', function (Message $message) {
        $message->to('test@example.com')
                ->subject('Test Email from Laravel');
    });

    echo "✅ Email sent successfully!\n";
    echo "Check your email inbox or the log file at storage/logs/laravel.log\n";

} catch (Exception $e) {
    echo "❌ Email failed to send: " . $e->getMessage() . "\n";
    echo "Make sure your .env file is configured correctly.\n";
}
