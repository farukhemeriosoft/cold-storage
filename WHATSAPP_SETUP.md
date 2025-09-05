# WhatsApp Integration Setup

## Overview
This system sends WhatsApp messages with invoice details and PDF attachments when payments are processed.

## Configuration

Add these environment variables to your `.env` file:

```env
# WhatsApp Business API Configuration
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
```

## Phone Numbers

### From Number (Your Business)
- **WhatsApp Phone Number ID**: Your business number registered with WhatsApp Business API
- **Format**: `923453561168` (without + sign)
- **Set in**: `WHATSAPP_PHONE_NUMBER_ID`

### To Number (Customer)
- **Default Test Number**: `+923453561168`
- **Customer Number**: Uses `customer.phone` from database
- **Fallback**: If customer has no phone, uses test number

## Testing

### Test Command
```bash
# Test with default number (+923453561168)
php artisan whatsapp:test

# Test with custom number
php artisan whatsapp:test +923001234567
```

### Test Payment Processing
1. Process a payment through the invoice management interface
2. When invoice status becomes "paid", WhatsApp message will be sent automatically
3. Check logs: `storage/logs/laravel.log` for WhatsApp activity

## Message Content

When payment is processed, the system sends:

1. **Text Message** with:
   - Payment confirmation
   - Lot ID
   - Number of baskets
   - Amount paid
   - Payment date

2. **PDF Document** with:
   - Complete invoice details
   - Customer information
   - Payment history
   - Professional formatting

## Setup Requirements

1. **WhatsApp Business Account**
2. **Meta Business Manager Account**
3. **WhatsApp Business API Access**
4. **Phone Number Verification**

## Troubleshooting

- Check `storage/logs/laravel.log` for WhatsApp errors
- Verify phone number format (should include country code)
- Ensure WhatsApp Business API credentials are correct
- Test with `php artisan whatsapp:test` command first
