<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class TokenService
{
    private $secret_Key  = '%aaSWvtJ98os_b<IQ_c$j<_A%bo_[xgct+j$d6LJ}^<pYhf+53k^-R;Xs<l%5dF';
    private $domainName = "https://127.0.0.1";
    public function __construct()
    {
    }
 
    public function __destruct()
    {
    }
    public function generateJWT($user)
        {

            $secret_Key = $this->secret_Key;
            $date = new DateTimeImmutable();
            $expire_at = $date->modify('+1 day')->getTimestamp();
            $domainName = $this->domainName;

            $request_data = [
                'iat' => $date->getTimestamp(),
                'iss' => $domainName,
                'nbf' => $date->getTimestamp(),
                'exp' => $expire_at,
                'userId' => $user->getId(),
                'username' => $user->getUsername(),
            ];

            $token = JWT::encode($request_data, $secret_Key, 'HS512'); // Generate the JWT token


            if (setcookie('token', $token, time() + 86400, '/')) {
            // echo "Token saved in the cookie.";
            } else {
                echo "Failed to save the token in the cookie.";
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['token' => $token]);

            return $response;

        }

        public function checkJWTExistance () {

            if (! preg_match('/Bearer\s(\S+)/', $this -> getAuthorizationHeader(), $matches)) {
                header('HTTP/1.0 400 Bad Request');
                echo 'Token not found in request';
                exit;
            }
            return $matches[1];
        }

        public function getAuthorizationHeader(){
            $headers = null;
            if (isset($_SERVER['Authorization'])) {
                $headers = trim($_SERVER["Authorization"]);
            }
            else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }

        public function validateJWT( $jwt ) {
            $secret_Key = $this -> secret_Key;

            try {
                $token = JWT::decode($jwt, new Key($secret_Key, 'HS512'));
            } catch (Exception $e) {
                header('HTTP/1.1 401 Unauthorized');
                exit;
            }
            $now = new DateTimeImmutable();
            $domainName = $this -> domainName;

            if ($token->iss !== $domainName ||
                $token->nbf > $now->getTimestamp() ||
                $token->exp < $now->getTimestamp())
            {
                header('HTTP/1.1 401 Unauthorized');
                exit;
            }
        }
}