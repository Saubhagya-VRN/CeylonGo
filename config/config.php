<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ceylon_go');
define('DB_USER', 'root');
define('DB_PASS', '');

// App path constants
// Project root directory (one level up from this config folder)
define('BASE_PATH', dirname(__DIR__));

// Load `.env` from project root (see `.env.example`). Does not override existing OS/server env vars.
$ceylonGoEnv = BASE_PATH . DIRECTORY_SEPARATOR . '.env';
if (is_readable($ceylonGoEnv)) {
    foreach (file($ceylonGoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if ($name === '') {
            continue;
        }
        if (
            strlen($value) >= 2
            && (($value[0] === '"' && str_ends_with($value, '"'))
                || ($value[0] === "'" && str_ends_with($value, "'")))
        ) {
            $value = substr($value, 1, -1);
        }
        if (getenv($name) !== false) {
            continue;
        }
        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
    }
}
// Absolute path to the public web root
define('PUBLIC_PATH', BASE_PATH . '/public');
// Absolute path to views directory
define('VIEW_PATH', BASE_PATH . '/views');
// Absolute path to uploads directory (served from public)
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Base URL for the app (used for API links in views)
define('BASE_URL', '/CeylonGo/public');

// PayHere — use credentials from Sandbox → Apps & Credentials (not Live), with PAYHERE_SANDBOX true.
// If you see "Unauthorized payment request": (1) Add domain "localhost" under Integrations, (2) Fix hash secret mode below.
// Sandbox test cards (only these work; others → "Unknown card" / decline). Visa ok: 4916217501611292 | MC: 5307732125531191 | Amex: 346781005510225 — use any future expiry, any CVV (Amex 4 digits). See https://support.payhere.lk/sandbox-and-testing
define('PAYHERE_SANDBOX', true);
define('PAYHERE_MERCHANT_ID', '1234899');
define('PAYHERE_MERCHANT_SECRET', 'MTM3NTM0MDM4MDMzNDU3MDYxNTkxNjAzMjM2NDc0MjY2Nzg0NDI5Mw==');
// false = use MERCHANT_SECRET exactly as copied from PayHERE (typical). true = if your secret is Base64 *only* of a numeric id, use its decoded digits in MD5 (try alternate if PayHERE still rejects).
define('PAYHERE_MERCHANT_SECRET_BASE64_NUMERIC', false);

// Per-transaction card limit (LKR) for your PayHERE plan (dashboard / plan; Lite is often 50,000, Plus often 250,000). If > 0 and booking total exceeds it, card is disabled and bank transfer is suggested. Set 0 to skip (PayHERE may still reject). Use e.g. 50000 while on Lite.
define('PAYHERE_PER_TRANSACTION_MAX_LKR', 0);

// Sandbox only: after PayHere, the return page often has no parameters and notify cannot reach localhost — set true to mark the pending booking as paid when the user lands on return (so My Bookings shows Completed). Set false when using a public notify_url or live PayHERE.
define('PAYHERE_SANDBOX_TRUST_EMPTY_RETURN', true);

// Bank transfer (manual): shown on the payment page when the tourist selects Bank transfer. Replace with your real bank name, account name, and account number.
define('BANK_TRANSFER_DETAILS', "Bank: Your Bank Name\nAccount name: Ceylon Go (Pvt) Ltd\nAccount number: 1862793051\nBranch: Boralesgamuwa");

// Google Maps API Key for stop locations (server-side only)
define('GOOGLE_MAPS_API_KEY', 'AIzaSyBFdoF5vZo-egKRXYOOoySVbtuvkpFEOKY');
