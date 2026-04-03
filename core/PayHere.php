<?php

/**
 * PayHere Checkout hash helpers (sandbox & live).
 * @see https://support.payhere.lk/api-&-mobile-sdk/payhere-checkout
 */
class PayHere {
    /**
     * Merchant secret for MD5 must match PayHere’s dashboard value. Most accounts: paste secret as-is (BASE64_NUMERIC false).
     * If the portal shows Base64 that decodes to digits-only, try PAYHERE_MERCHANT_SECRET_BASE64_NUMERIC true.
     */
    public static function normalizedSecret(string $secret): string {
        $secret = trim($secret);
        if (defined('PAYHERE_MERCHANT_SECRET_BASE64_NUMERIC') && PAYHERE_MERCHANT_SECRET_BASE64_NUMERIC) {
            $decoded = base64_decode($secret, true);
            if ($decoded !== false && $decoded !== '' && ctype_digit($decoded)) {
                return $decoded;
            }
        }
        return $secret;
    }

    public static function checkoutHash(string $merchantId, string $orderId, string $amount, string $currency, string $secret): string {
        $s = self::normalizedSecret($secret);
        return strtoupper(md5(
            $merchantId . $orderId . $amount . $currency . strtoupper(md5($s))
        ));
    }

    /**
     * Verify POST body from PayHere notify URL.
     */
    public static function notifyValid(array $post, string $secret): bool {
        $merchantId = (string) ($post['merchant_id'] ?? '');
        $orderId = (string) ($post['order_id'] ?? '');
        $amount = (string) ($post['payhere_amount'] ?? '');
        $currency = (string) ($post['payhere_currency'] ?? '');
        $statusCode = (string) ($post['status_code'] ?? '');
        $md5sig = (string) ($post['md5sig'] ?? '');

        if ($md5sig === '') {
            return false;
        }

        $s = self::normalizedSecret($secret);
        $local = strtoupper(md5(
            $merchantId . $orderId . $amount . $currency . $statusCode . strtoupper(md5($s))
        ));

        return hash_equals($local, strtoupper($md5sig));
    }

    public static function checkoutUrl(bool $sandbox): string {
        return $sandbox
            ? 'https://sandbox.payhere.lk/pay/checkout'
            : 'https://www.payhere.lk/pay/checkout';
    }
}
