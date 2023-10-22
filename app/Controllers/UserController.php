<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\user_model;

class UserController extends Controller
{


    public function insertUser()
    {
        // Obtener los datos del usuario desde la solicitud POST co un JSON
        $userData = $this->request->getJSON();

        // Validar los datos del usuario, por ejemplo, asegurarse de que los campos requeridos estén presentes.

        // Insertar el nuevo usuario en la base de datos
        $UserModel = new user_model();

        $salt = random_bytes(22); // Genera una cadena aleatoria de 22 bytes

        // Crear la contraseña encriptada usando crypt()
        $encryptedPassword = crypt($userData->contrasenia, '$2a$12$' . base64_encode($salt));
        //guarda la contraseña encriptada
        $userData->contrasenia=$encryptedPassword;
        $insertedUserId = $UserModel->insertUser($userData);

        // Verificar si la inserción fue exitosa
        if ($insertedUserId) {
            $resp = [
                'mensaje'=>'Usuario creado con éxito'
            ];
            return $this->response->setJSON($resp);
        } else {
            $resp = [
                'mensaje'=>'error al insertar usuario'
            ];


            return $this->response->setJSON($resp);
        }
    }

    public function getUserRoles()
    {

        $userModel = new user_model();
        $usuarios = $userModel->getUsers();

        // Haz algo con $usuarios, como pasarlos a la vista o realizar otras operaciones.
        $resp = [];
        foreach ($usuarios as $usuario) {
            $resp[] = [ // Agrega un nuevo arreglo asociativo para cada usuario.
                'id_user' => $usuario->id_user,
                'nombre' => $usuario->nombre,
                'apellidos' => $usuario->apellidos,
                'correo' => $usuario->correo,
                'id_rol' => $usuario->idRol,
                'rol' => $usuario->rol
            ];
        }
        return $this->response->setJSON($resp);
    }

    //funcion para recibir un id_user y devuelve la informacion de ese usuario
    public function getUserbyId(){
        $request =  $this->request->getJSON();
        if(!$request){
            $resp=["error"=>"no se recibió ni un dato"];
            return $this->response->setJSON($resp);
        }



        $userModel = new user_model();
        $usuario = $userModel->getUserbyID($request->id_user);



        if ($usuario) {
            $resp = [
                'id_user' => $usuario->id_user,
                'nombre' => $usuario->nombre,
                'apellidos' => $usuario->apellidos,
                'correo' => $usuario->correo,
                'id_rol' => $usuario->idRol,
                'rol' => $usuario->rol
            ];
            return $this->response->setJSON($resp);
        } else {
            // Usuario no encontrado, se devuelve un mensaje de error.
            return $this->response->setJSON(['error' => 'Usuario no encontrado']);
        }

    }

    //funcion para actualizar ususario segun la información que llegue
    public function UpdateUser() {
        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        // Obtener datos principales del mural
        $userData = [
            'id_user' => $request['id_user'],
            'nombre' => $request['nombre'],
            'apellido_p' => $request['apellido_p'],
            'apellido_m' => $request['apellido_m'],
            'id_rol' => $request['id_rol'],
            'email' => $request['correo']
        ];

        // Verificar si 'contrasenia' está presente en los datos proporcionados
        if (isset($request['contrasenia'])) {
            // Encriptar la contraseña si está presente
            $salt = random_bytes(22); // Genera una cadena aleatoria de 22 bytes
            $encryptedPassword = crypt($request['contrasenia'], '$2a$12$' . base64_encode($salt));
            $userData['contrasenia'] = $encryptedPassword;
        }

        // Actualizar los datos del usuario
        $UserModel = new user_model();
        $UserModel->update($userData['id_user'], $userData);

        $resp = [
            'mensaje' => 'Usuario actualizado con éxito'
        ];

        return $this->response->setJSON($resp);
    }

    public function deleteUser(){
        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        // Obtener datos principales del mural
        $userData = [
            'id_user' => $request['id_user'],
            'id_rol' => 4

        ];

        //actualizamos
        $UserModel = new user_model();
        $UserModel->update($userData['id_user'],$userData);

        $resp = [
            'mensaje'=>'Usuario eliminado con éxito'
        ];


        return $this->response->setJSON($resp);

    }

}