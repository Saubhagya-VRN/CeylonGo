<?php

/**
 * Shared input validation for auth and admin flows.
 */
class Validation
{
    public const ADMIN_ROLES = [
        'Senior Administrator',
        'Junior Administrator',
        'Content Manager',
        'Customer Support',
        'Finance Officer',
    ];

    private const EMAIL_MAX = 255;
    private const NAME_MAX = 100;
    /** Bcrypt truncates at 72 bytes — reject longer passwords to avoid confusion */
    private const PASSWORD_MAX_BYTES = 72;

    public static function isValidEmail(string $email): bool
    {
        if (strlen($email) > self::EMAIL_MAX) {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isValidPhoneLk(string $phone): bool
    {
        return preg_match('/^\d{10}$/', $phone) === 1;
    }

    public static function isValidAdminRole(string $role): bool
    {
        return in_array($role, self::ADMIN_ROLES, true);
    }

    /**
     * Login email: non-empty, length cap, RFC-like check.
     * @return string|null error message or null
     */
    public static function loginEmail(string $email): ?string
    {
        $email = trim($email);
        if ($email === '') {
            return 'Please enter your email address.';
        }
        if (strlen($email) > self::EMAIL_MAX) {
            return 'Email address is too long.';
        }
        if (!self::isValidEmail($email)) {
            return 'Please enter a valid email address.';
        }
        return null;
    }

    /**
     * Login password: non-empty, reasonable max length.
     * @return string|null error message or null
     */
    public static function loginPassword(string $password): ?string
    {
        if ($password === '') {
            return 'Please enter your password.';
        }
        if (strlen($password) > self::PASSWORD_MAX_BYTES) {
            return 'Password is too long.';
        }
        return null;
    }

    /**
     * Optional new password for admin profile: empty = OK; otherwise strength rules (aligned with guide registration).
     * @return string|null error message or null
     */
    public static function optionalAdminPassword(string $password): ?string
    {
        if ($password === '') {
            return null;
        }
        if (strlen($password) < 8) {
            return 'Password must be at least 8 characters.';
        }
        if (strlen($password) > self::PASSWORD_MAX_BYTES) {
            return 'Password is too long.';
        }
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/\d/', $password);
        $hasSpecial = preg_match('/[@$!%*?&]/', $password);
        if (!$hasUpper || !$hasLower || !$hasNumber || !$hasSpecial) {
            return 'Password must include uppercase, lowercase, a number, and a special character (@$!%*?&).';
        }
        return null;
    }

    /**
     * @return string[] list of error messages (empty if valid)
     */
    public static function adminProfileErrors(array $data): array
    {
        $errors = [];
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $role = trim($data['role'] ?? '');
        $password = $data['password'] ?? '';

        if ($username === '') {
            $errors[] = 'Username is required.';
        } elseif (mb_strlen($username) > self::NAME_MAX) {
            $errors[] = 'Username is too long.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!self::isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($phone === '') {
            $errors[] = 'Phone number is required.';
        } elseif (!self::isValidPhoneLk($phone)) {
            $errors[] = 'Phone number must be exactly 10 digits.';
        }

        if ($role === '') {
            $errors[] = 'Role is required.';
        } elseif (!self::isValidAdminRole($role)) {
            $errors[] = 'Invalid role selected.';
        }

        $pwdErr = self::optionalAdminPassword(is_string($password) ? $password : '');
        if ($pwdErr !== null) {
            $errors[] = $pwdErr;
        }

        return $errors;
    }

    /**
     * Tourist user edit from admin panel (name and contact only; email is not editable).
     * @return string[] list of error messages (empty if valid)
     */
    public static function touristAdminEditErrors(array $data): array
    {
        $errors = [];
        $userId = (int) ($data['user_id'] ?? 0);
        $first = trim($data['first_name'] ?? '');
        $last = trim($data['last_name'] ?? '');
        $contact = trim($data['contact'] ?? '');

        if ($userId < 1) {
            $errors[] = 'Invalid user.';
        }
        if ($first === '') {
            $errors[] = 'First name is required.';
        } elseif (mb_strlen($first) > self::NAME_MAX) {
            $errors[] = 'First name is too long.';
        }
        if ($last === '') {
            $errors[] = 'Last name is required.';
        } elseif (mb_strlen($last) > self::NAME_MAX) {
            $errors[] = 'Last name is too long.';
        }
        if ($contact === '') {
            $errors[] = 'Contact number is required.';
        } elseif (!self::isValidPhoneLk($contact)) {
            $errors[] = 'Contact number must be exactly 10 digits.';
        }

        return $errors;
    }
}
