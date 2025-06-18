<?php

require_once 'app/models/AccountModel.php';
require_once 'app/controllers/AccountController.php';
use Google\Client as GoogleClient;
class OAuthController
{
    private $googleClient;
    private $facebookClient;
    private $githubClient;

    public function __construct()
    {
        $this->googleClient = new \League\OAuth2\Client\Provider\Google(
            [
                'clientId' => '',
                'clientSecret' => '',
                'redirectUri' => 'http://localhost/webbanhang/oauth/google/callback',
                'scopes' => ['email', 'profile']
            ]
        );
        $this->facebookClient = new \League\OAuth2\Client\Provider\Facebook(
            [
                'clientId' => '',
                'clientSecret' => '',
                'redirectUri' => 'http://localhost/webbanhang/oauth/facebook/callback',
                'graphApiVersion' => 'v10.0'
            ]
        );
        $this->githubClient = new \League\OAuth2\Client\Provider\Github();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function google() {
        try {
            if (!isset($_GET['code'])) {
                $authUrl = $this->googleClient->getAuthorizationUrl([
                    'scope' => ['email', 'profile']
                ]);
                $_SESSION['google_oauth2state'] = $this->googleClient->getState() ?? '';
                header('Location: ' . $authUrl);
                exit;
            } else {
                error_log('Received state: ' . $_GET['state'] ?? 'No state received');
                error_log('Stored state: ' . $_SESSION['google_oauth2state'] ?? 'No stored state');

                if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['google_oauth2state'])) {
                    unset($_SESSION['google_oauth2state']);
                    exit('Invalid state:' . $_GET['state']);
                }
                
                try {
                    $token = $this->googleClient->getAccessToken(
                        'authorization_code',
                        ['code' => $_GET['code']]
                    );

                    if (!$token) {
                        exit('Failed to get access token');
                    }

                    $user = $this->googleClient->getResourceOwner($token);
                    $userData = $user->toArray();

                    $accountController = new AccountController();
                    $accountController->registerWithGoogle($userData);

                    echo 'Error: An error occurred while registering with Google';
                    exit;
                } catch (Exception $e) {
                    throw new Exception("Error: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function facebook()
    {
        try {
            if (!isset($_GET['code'])) {
                // Store state to session
                $authUrl = $this->facebookClient->getAuthorizationUrl([
                    'scope' => ['email', 'public_profile']
                ]);
                $_SESSION['facebook_oauth2state'] = $this->facebookClient->getState() ?? '';

                // Redirect to Facebook's authorization page
                header('Location: ' . $authUrl);
                exit;
            } else {
                // If state is not valid, unset it
                if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['facebook_oauth2state'])) {
                    unset($_SESSION['facebook_oauth2state']);
                    exit('Invalid state:' . $_GET['state']);
                }

                try {
                    $token = $this->facebookClient->getAccessToken(
                        'authorization_code',
                        ['code' => $_GET['code']]
                    );

                    if (!$token) {
                        exit('Failed to get access token');
                    }

                    // Get user info
                    $user = $this->facebookClient->getResourceOwner($token);
                    $userData = $user->toArray();

                    $accountController = new AccountController();
                    $accountController->registerWithFacebook($user);

                    // If register with facebook failed, print error message
                    echo 'Error: An error occurred while registering with Facebook';
                    exit;
                } catch (Exception $e) {
                    throw new Exception("Error: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function github() {}

    public function googleCallback() {
        try {
            $token = $this->googleClient->getAccessToken(
                'authorization_code',
                [
                    'code' => $_GET['code']
                ]
            );

            $user = $this->googleClient->getResourceOwner($token);
            $userData = $user->toArray();

            $accountController = new AccountController();
            $accountController->registerWithGoogle($user);
            

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function facebookCallback()
    {
        try {
            $token = $this->facebookClient->getAccessToken(
                'authorization_code',
                [
                    'code' => $_GET['code']
                ]
            );

            $user = $this->facebookClient->getResourceOwner(
                $token
            );

            $db = new Database();
            $accountController = new AccountController();
            $oauthModel = new OauthAccountModel($db);
            $oauth = $oauthModel->findByProviderAndProviderUserId('facebook', $user->getId());

            // If account is null, create new account
            if ($oauth) {
                $accountController->loginWithFacebook($oauth);
            } else {
                $accountController->registerWithFacebook($user);
            }

            echo '<pre>';
            var_dump($user);
            echo '</pre>';
            exit;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    public function githubCallback() {}
}
