<?php
namespace App\Controllers;

use App\Models\User_model;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
class Login extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User_model();
    }

    public function loginOn()
    {
        // Obtener los datos de login enviados desde Angular
        $data = $this->request->getJSON();
        $email = $data->email;
        $password = $data->contrasenia;

        // Realizar la lógica de autenticación
        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['contrasenia'])) {
            // Las credenciales son válidas, generar el token de acceso
            $token = $this->generateToken($user['id_user'], $user['email'], $user['perfil']);

            // Devolver la respuesta con el token
            return $this->respond(['token' => $token], 200);
        } else {
            // Las credenciales son inválidas, devolver un error
            return $this->failUnauthorized('Credenciales inválidas');
        }
    }

    protected function generateToken($userId, $email, $rol)
    {
        try {
            // Clave secreta para firmar el token
            $key = 'generatokens';
            $algorithm = 'HS256';
            $payload = [
                'sub' => $userId,
                'email' => $email,
                'rol' => $rol,
                'iat' => time(),
                'exp' => time() + 3600 // El token expirará en una hora (3600 segundos)
            ];

            $token = JWT::encode($payload, $key, $algorithm);

            return $token;
        } catch (\Exception $e) {
            return $this->failServerError('Error al generar el token');
        }
    }

    protected function verifyToken($token)
    {
        $key = 'generatokens'; // Clave secreta para verificar el token

        try {
            $decoded = JWT::decode($token, $key, ['HS256']);
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function someProtectedRoute()
    {
        // Obtener el token de acceso enviado desde Angular
        $token = $this->request->getHeaderLine('Authorization');

        // Verificar y decodificar el token
        $decodedToken = $this->verifyToken($token);

        if ($decodedToken) {
            // El token es válido, puedes acceder a la ruta protegida
            // Realizar las operaciones necesarias...
            return $this->respond(['message' => 'Acceso permitido'], 200);
        } else {
            // El token es inválido o ha expirado, devuelve un error de autorización
            return $this->failUnauthorized('Token inválido o expirado');
        }
    }
}
