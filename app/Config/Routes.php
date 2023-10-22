<?php

namespace Config;

// Create a new instance of our RouteCollection class.
use App\Controllers\MuralController;

$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

//ruta del post para hacer login
$routes->post('login','loggin::loginOn');
//ruta para obtener los id
$routes->get('mural/getId','MuralController::getIdMurals');
//ruta para obtener las solicitudes
$routes->get('mural/solicitudes','MuralController::getSolicitud');
//ruta para devolver las respuestas a las solicitudes getReject
$routes->get('mural/respuestas','MuralController::getResp');
//ruta para devolver las respuestas a las solicitudes rechazadas
$routes->get('mural/respreject','MuralController::getReject');

//ruta para pasar id del user y que devuelva las solicitudes solo de ese usuario
$routes->post('mural/solbyuser','MuralController::solicitudByIdUser');
//ruta que devuelve todos los murales aprobados
$routes->get("mural/all","MuralController::muralAll");

//rutas que va recibir los post del frontend
// ruta para guardar los murales
$routes->post('mural/insert','MuralController::create');
//ruta para editar o ver en el dashboard los murales
$routes->post('mural/dashboard','MuralController::EditMural');
//ruta que recibe un idMural para editar el mural
$routes->post('mural/edit','MuralController::getMuralbyId');
//rutas para actualizar rechazar
$routes->patch('mural/aprobar','MuralController::updateEstado');
$routes->patch('mural/rechazar','MuralController::rechazar');
//ruta para actualizar el mural
$routes->patch('mural/updateM','MuralController::updateMural');
//ruta para devolver los logs de edicion
$routes->post('mural/logs','MuralController::logs');

//ruta para insertar un nuevo usuario
$routes->post('mural/insertUser','UserController::insertUser');
//ruta que recibe un json con id_user
$routes->post('mural/getUserbyId','UserController::getUserbyId');
//ruta para actualizar usuario
$routes->patch('mural/updateUser','UserController::UpdateUser');
//ruta para devolver la lista de usuarios con los roles
$routes->get('mural/getUsers','UserController::getUserRoles');

//ruta para eliminar un usuario(conceptualmente)
$routes->patch('mural/deleteUser','UserController::deleteUser');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
