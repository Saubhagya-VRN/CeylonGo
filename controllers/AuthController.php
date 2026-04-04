<?php
class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function loginView() {
        view('login');
    }

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            view('login', ['error' => 'Please fill in all fields.']);
            return;
        }

        $authUser = new AuthUser($this->db);
        $user = $authUser->getUserByEmail($email);

        // Debug: Log what we found
        error_log("Login attempt for: $email");
        error_log("User found: " . ($user ? 'YES' : 'NO'));
        if ($user) {
            error_log("User role: " . $user['role']);
            error_log("User ref_id: " . ($user['ref_id'] ?? 'NULL'));
        }

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['ref_id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_type'] = $user['role'];
            $_SESSION['user_email'] = $email;

            // Get user name for tourists
            if ($user['role'] === 'tourist') {
                $touristModel = new Tourist($this->db);
                $tourist = $touristModel->getTouristById($user['ref_id']);
                if ($tourist) {
                    $_SESSION['user_name'] = trim($tourist['first_name'] . ' ' . $tourist['last_name']);
                }
            }

            $redirect = trim($_POST['redirect'] ?? '');
            if (!empty($redirect) && strpos($redirect, '/CeylonGo/public/') === 0 && strpos($redirect, '//') === false) {
                // Allow same-site redirect from login (e.g. customise trip → old-dashboard)
                $redirectTo = $redirect;
            } else {
                $redirectTo = null;
            }
            // Get user name for guides
            if ($user['role'] === 'guide') {
                $guideModel = new Guide($this->db);
                $guide = $guideModel->getGuideById($user['ref_id']);
                if ($guide) {
                    $_SESSION['user_name'] = trim($guide['first_name'] . ' ' . $guide['last_name']);
                }
            }

            switch ($user['role']) {
                case 'tourist':
                    header("Location: " . ($redirectTo ?: "/CeylonGo/public/tourist/dashboard"));
                    break;
                case 'hotel':
                    header("Location: /CeylonGo/public/hotel/dashboard");
                    break;
                case 'guide':
                    header("Location: /CeylonGo/public/guide/dashboard");
                    break;
                case 'transport':
                    $_SESSION['transporter_id'] = trim($user['ref_id']);
                    header("Location: /CeylonGo/public/transporter/dashboard");
                    break;
                case 'admin':
                    $_SESSION['user_ref_id'] = $user['ref_id'];
                    header("Location: /CeylonGo/public/admin/dashboard");
                    break;
                default:
                    view('login', ['error' => 'Invalid role.']);
                    return;
            }
            exit();
        } else {
            view('login', ['error' => 'Incorrect email or password.']);
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /CeylonGo/public/tourist/dashboard");
        exit();
    }

    public function registerView() {
        view('register');
    }
}
?>

