<?php
require_once 'app/models/AccountModel.php';
require_once 'app/config/database.php';
require_once 'app/utils/JWTHandler.php';

class AccountController
{
    private $accountModel;
    private $oauthModel;
    private $jwtHandler;
    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->accountModel = new AccountModel($db);
        $this->oauthModel = new OauthAccountModel($db);
        $this->jwtHandler = new JWTHandler();
    }

    public function login()
    {
        try {
            $title = "Login";
            $description = "Login to your account";

            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $username = $data['username'] ?? '';
                        $password = $data['password'] ?? '';
                    } else {
                        $username = $_POST['username'] ?? '';
                        $password = $_POST['password'] ?? '';
                    }

                    $account = $this->accountModel->getAccountByUsername($username);
                    if (!$account) {
                        http_response_code(401);
                        echo json_encode(['message' => 'Invalid username or password.', 'success' => false]);
                        return false;
                    }

                    if ($account && password_verify($password, $account->password)) {
                        session_start();
                        $_SESSION['user_id'] = $account->id;
                        $_SESSION['username'] = $account->username;
                        $_SESSION['fullname'] = $account->fullname;
                        $_SESSION['image'] = $account->image ?? null;
                        $_SESSION['role'] = $account->role;

                        $token = $this->jwtHandler->encode([
                            'user_id' => $account->id,
                            'username' => $account->username,
                            'fullname' => $account->fullname,
                            'role' => $account->role
                        ]);
                        echo json_encode(['message' => 'Login successful', 'success' => true, 'jwtToken' => $token]);
                        return true;
                    }
                    http_response_code(401);
                    echo json_encode(['message' => 'Invalid username or password.', 'success' => false]);
                    return false;

                case 'GET':
                    $title = "Login";
                    $description = "Login to your account";
                    include 'app/views/account/login.php';
                    break;

                default:
                    include 'app/views/account/login.php';
                    break;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function loginWithFacebook($facebookAccount)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            // Validate input
            if (!$facebookAccount) {
                throw new Exception("Invalid Facebook account data");
            }

            // Find or create user
            $OAuthAccount = $this->oauthModel->findByProviderAndAccountId('facebook', $facebookAccount->id);
            if ($OAuthAccount == false) {
                throw new Exception("Linked account not found");
            }

            // Get the local account
            $localAccount = $this->accountModel->getAccountById($OAuthAccount->account_id);
            if ($localAccount == false) {
                throw new Exception("Linked account not found");
            }

            // Set session
            $_SESSION['user_id'] = $localAccount->id;
            $_SESSION['username'] = $localAccount->username;
            $_SESSION['fullname'] = $localAccount->fullname;
            $_SESSION['role'] = $localAccount->role;

            // Generate JWT token
            $token = $this->jwtHandler->encode([
                'user_id' => $localAccount->id,
                'username' => $localAccount->username,
                'fullname' => $localAccount->fullname,
                'role' => $localAccount->role
            ]);
            
            // Store JWT token in session for OAuth logins
            $_SESSION['jwtToken'] = $token;
            // Set JWT token as cookie with secure settings
            setcookie('jwtToken', $token, [
                'expires' => time() + 3600, // 1 hour expiration
                'path' => '/',
                'secure' => true,     // Only send over HTTPS
                'httponly' => true,   // Not accessible via JavaScript
                'samesite' => 'Strict' // Prevent CSRF attacks
            ]);

            // Also store in localStorage via JavaScript
            echo "
            <script>
                localStorage.setItem('jwtToken', '" . $token . "');
                window.location.href = '/webbanhang';
            </script>";
            exit();
        } catch (Exception $e) {
            error_log("Facebook login error: " . $e->getMessage());
            throw new Exception("Login failed. Please try again.");
        }
    }

    public function registerWithFacebook($facebookAccount, $email = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            // Validate input
            if (!$facebookAccount) {
                throw new Exception("Error: Invalid account data provided for Facebook registration.");
            }

            if (!$email) {
                $email = $facebookAccount->getEmail();
            }
            // Find or create user account
            $existingAccount = $this->accountModel->getAccountByEmail($email);

            if ($existingAccount) {
                $oauthAccount = $this->oauthModel->findByProviderAndProviderUserId('facebook', $facebookAccount->getId());
                if ($oauthAccount) {
                    return $this->loginWithFacebook($existingAccount);
                }

                $oauthCreated = $this->oauthModel->create(
                    $existingAccount->id,
                    'facebook',
                    $facebookAccount->getId(),
                    $facebookAccount->getEmail(),
                    $facebookAccount->getPictureUrl()
                );

                if (!$oauthCreated) {
                    throw new Exception("Error: Failed to link Facebook account.");
                }

                return $this->loginWithFacebook($existingAccount);
            }

            $newUserId = $this->accountModel->save(
                $facebookAccount->getEmail(),
                $facebookAccount->getName(),
                $facebookAccount->getEmail(),
                null,
                $facebookAccount->getPictureUrl(),
                'user'
            );

            if (!$newUserId) {
                throw new Exception("Failed to create local account");
            }

            // Create OAuth connection
            $oauthCreated = $this->oauthModel->create(
                $newUserId,
                'facebook',
                $facebookAccount->getId(),
                $facebookAccount->getEmail(),
                $facebookAccount->getPictureUrl()
            );

            // Get the newly created account and login
            $newAccount = $this->accountModel->getAccountById($newUserId);
            if (!$newAccount) {
                throw new Exception("Account not found after registration");
            }

            return $this->loginWithFacebook($facebookAccount);
        } catch (Exception $e) {
            error_log("Facebook registration error: " . $e->getMessage());
            throw new Exception("Error: Failed to register with Facebook. Please try again later.");
        }
    }
    public function loginWithGoogle($googleAccount)
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Validate input
            if (!$googleAccount) {
                throw new Exception("Invalid Google account data");
            }
            
            // Find OAuth account
            $OAuthAccount = $this->oauthModel->findByProviderAndProviderUserId('google', $googleAccount['sub']);
            if ($OAuthAccount == false) {
                throw new Exception("Linked account not found");
            }

            // Find local account linked to OAuth account
            $localAccount = $this->accountModel->getAccountById($OAuthAccount->account_id);
            if ($localAccount == false) {
                throw new Exception("Linked account not found");
            }

            // Set session
            $_SESSION['user_id'] = $localAccount->id;
            $_SESSION['username'] = $localAccount->username;
            $_SESSION['fullname'] = $localAccount->fullname;
            $_SESSION['role'] = $localAccount->role;

            // Generate JWT token
            $token = $this->jwtHandler->encode([
                'user_id' => $localAccount->id,
                'username' => $localAccount->username,
                'fullname' => $localAccount->fullname,
                'role' => $localAccount->role
            ]);
            
            // Store JWT token in session for OAuth logins
            $_SESSION['jwtToken'] = $token;
            // Set JWT token as cookie with secure settings
            setcookie('jwtToken', $token, [
                'expires' => time() + 3600, // 1 hour expiration
                'path' => '/',
                'secure' => true,     // Only send over HTTPS
                'httponly' => true,   // Not accessible via JavaScript
                'samesite' => 'Strict' // Prevent CSRF attacks
            ]);

            // Also store in localStorage via JavaScript
            echo "
            <script>
                localStorage.setItem('jwtToken', '" . $token . "');
                window.location.href = '/webbanhang';
            </script>";
            
        } catch (Exception $e) {
            error_log("Google login error: " . $e->getMessage());
            throw new Exception("Login failed. Please try again.");
        }
    }

    public function registerWithGoogle($googleAccount)
    {
        try {
            $title = "Register with Google";
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $account = $this->accountModel->getAccountByEmail($googleAccount['email']);
            if ($account) {
                // If account found but not linked, needs to be linked right here
                $oauthAccount = $this->oauthModel->findByProviderAndProviderUserId('google', $googleAccount['sub']);
                if ($oauthAccount == false) {
                    $oauthAccount = $this->oauthModel->create(
                        $account->id,
                        'google',
                        $googleAccount['sub'],
                        $googleAccount['email'],
                        $googleAccount['picture']
                    );
                    if (!$oauthAccount) {
                        throw new Exception("Failed to link Google account.");
                    }
                }
                return $this->loginWithGoogle($googleAccount);
            } 
        
            $newUserId = $this->accountModel->save(
                $googleAccount['email'],
                $googleAccount['name'],
                $googleAccount['email'],
                null,
                $googleAccount['picture'],
                'user'
            );

            $this->oauthModel->create(
                $newUserId,
                'google',
                $googleAccount['sub'],
                $googleAccount['email'],
                $googleAccount['picture']
            );
                
            $newAccount = $this->accountModel->getAccountById($newUserId);
            if (!$newAccount) {
                throw new Exception("Account not found after registration");
            }
            return $this->loginWithGoogle($googleAccount);
            
        } catch (Exception $e) {
            error_log("Google registration error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function register()
    {
        try {
            $title = "Register";
            $description = "Register new account";
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $username = $_POST['username'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $fullname = $_POST['fullname'] ?? '';
                    $confirmPassword = $_POST['confirmPassword'] ?? '';
                    $role = 'user';

                    $errors = [];
                    if (empty($username))
                        $errors[] = 'Username is required.';
                    if (empty($fullname))
                        $errors[] = 'Full name is required.';

                    if (empty($password))
                        $errors[] = 'Password is required.';

                    if ($password != $confirmPassword)
                        $errors[] = 'Password do not match.';

                    if (!in_array($role, ['admin', 'user']))
                        $role = 'user';

                    $account = $this->accountModel->getAccountByUsername($username);
                    if ($account) {
                        $errors[] = 'Account already exists.';
                    }

                    // if ($account && password_verify($password, $account->password)) {
                    //     session_start();
                    //     $_SESSION['user_id'] = $account->id;
                    //     $_SESSION['role'] = $account->role;
                    //     header('Location: /webbanhang/product/list');
                    // } else {
                    //     echo "Invalid username or password.";
                    // }

                    if (count($errors) > 0) {
                        include 'app/views/account/register.php';
                    } else {
                        $result = $this->accountModel->save($username, $fullname, $password, $role);
                        if ($result) {
                            header('Location: /webbanhang/account/login');
                        } else {
                            echo "Error: Failed to register new account!.";
                        }
                    }
                    break;
                default:
                    include 'app/views/account/register.php';
                    break;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /webbanhang');
    }
}
