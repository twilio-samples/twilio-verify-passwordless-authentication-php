# Twilio / Slim Base Project

A PHP/Slim Framework skeleton pre-wired with the Twilio PHP SDK, designed as a starting point for building Twilio-powered PHP tutorials and samples.

## Environment Variables

Copy `.env.example` to `.env`. Never commit `.env`.

```bash
cp .env.example .env
```

| Variable | Where to find | Format |
| -------- | ------------- | ------ |
| `TWILIO_ACCOUNT_SID` | Console homepage or Admin dropdown (top right) → Account Management → Keys & Credentials → API Keys & Tokens | Starts with `AC` |
| `TWILIO_AUTH_TOKEN` | Console homepage or Admin dropdown (top right) → Account Management → Keys & Credentials → API Keys & Tokens → click to reveal | 32-char string. Treat as a password. |
| `TWILIO_PHONE_NUMBER` | Console → Phone Numbers → Manage → Active Numbers | E.164 format: `+15551234567` |

## Commands

```bash
# Install
composer install

# Run
composer serve

# Test
composer test
```

## Project Structure

- `public/index.php` — entry point; bootstraps the DI container, Twilio client, and Slim app
- `src/Application.php` — wraps Slim, registers middleware, and defines routes
- `src/Config/` — env var validation specs (`TwilioRestClient`, `TwilioPhoneNumber`)
- `test/` — PHPUnit tests

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

After running `composer serve`, open `http://localhost:8080` in a browser or run:

```bash
curl -i http://localhost:8080/
```

Expect an HTTP 200 response with no body. If the app crashes on startup, check that `.env` is present and that `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN` are set to non-empty values.

## Twilio Resources

- [Twilio Console](https://console.twilio.com) — credentials, phone numbers, webhook configuration
- [Twilio PHP Helper Library](https://www.twilio.com/docs/libraries/php) — SDK reference and usage guide
