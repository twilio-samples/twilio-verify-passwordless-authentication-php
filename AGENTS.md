# Passwordless Authentication With Twilio Verify in PHP

This sample demonstrates how to implement passwordless authentication via OTP in PHP using Twilio Verify.

## Environment Variables

Copy `.env.example` to `.env`. Never commit `.env`.

```bash
cp .env.example .env
```

| Variable | Where to find | Format |
| -------- | ------------- | ------ |
| `TWILIO_ACCOUNT_SID` | Console homepage or Admin dropdown (top right) → Account Management → Keys & Credentials → API Keys & Tokens | Starts with `AC` |
| `TWILIO_AUTH_TOKEN` | Console homepage or Admin dropdown (top right) → Account Management → Keys & Credentials → API Keys & Tokens → click to reveal | 32-char string. Treat as a password. |
| `TWILIO_PHONE_NUMBER` | Console → Phone Numbers → Manage → Active Numbers | E.164 format: `+15551234567` <!-- verify this --> |
| `TWILIO_VERIFY_SERVICE_SID` | Console → Verify → Services | Starts with `VA` |

> **Note:** `TWILIO_VERIFY_SERVICE_SID` is required by the app but is missing from `.env.example`. Add it manually before running.

## Commands

```bash
# Install
composer install

# Run (serves on http://localhost:8080)
composer serve

# Test
composer test
```

## Project Structure

- `src/Application.php` — Slim app with all routes and Twilio Verify logic
- `public/index.php` — Entry point
- `templates/` — Twig templates for sign-in, verify, and result pages
- `.env.example` — Environment variable template

## Agent Boundaries

**Always:**
- Confirm `.env` is configured before running any command
- Use the Environment Variables section to guide the user to each credential — don't ask them to find values without direction
- Confirm the app is running before asking the user to test it

**Never:**
- Run the app with missing or placeholder credentials
- Hardcode credentials or phone numbers in source files
- Skip the `cp .env.example .env` step

## Verify It's Working

1. Start the app with `composer serve`, then open `http://localhost:8080` in a browser.
2. Enter a phone number to receive an OTP via SMS, then enter the code on the verify page — a success message confirms end-to-end verification is working.

## Twilio Resources

- [Twilio Console](https://console.twilio.com) — credentials, phone numbers, webhook configuration
- [Twilio Verify docs](https://www.twilio.com/docs/verify/api) — Verify service API reference
- [Twilio PHP SDK](https://www.twilio.com/docs/libraries/php) — PHP helper library reference
