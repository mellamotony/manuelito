<?php

namespace App\Controllers;

use App\Models\Mural_Model;
use App\Models\TextoModel;
use App\Models\ImagenModel;
use App\Models\VideoModel;
use App\Models\PdfModel;
use App\Models\publicarModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\SolicitarModel;
use App\Models\editModel;
use App\Models\respuestaModel;
use Cloudinary\Cloudinary;
use Config\Cloudinary as CloudinaryConfig;
use CodeIgniter\Controller; //



class MuralController extends ResourceController
{
    public function create()
    {

        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        //obtenemos la imagen para el dashboard
        $base64Data = str_replace('data:image/png;base64,', '',$request['imgMural']);

        $filename = 'imagen' . uniqid() .'.png'; // Nombre del archivo en el servidor con un ID único
        $filePath = FCPATH  . 'recursos/imagenes/' . $filename;


        file_put_contents($filePath, base64_decode($base64Data));


        // Obtener datos principales del mural
        $muralData = [
            'id_mural' => $request['id_mural'],
            'id_user' => $request['id_user'],
            'height' => $request['height'],
            'imgmural'=>$filePath,
            'width' => $request['width'],
            'estado' => $request['estado'],
            'nombrem' => $request['nombrem']
        ];

        // Guardar datos del mural en la tabla 'mural'
        $muralModel = new Mural_Model();

        $muralModel->insertMural($muralData);

        //id mural, id_user para luego usar
        $id_mural = $muralData['id_mural'];
        $id_user = $muralData['id_user'];
        // Obtener datos de textos, imágenes, videos y pdfs
        $textos = $request['textos'] ?? [];
        $imagenes = $request['imagenes'] ?? [];
        $videos = $request['videos'] ?? [];
        $pdfs = $request['pdfs'] ?? [];

        

        // Guardar datos de textos en la tabla 'txt'
        if (!empty($textos)) {

            $textoModel = new TextoModel();

            $textoModel->insertBatch($textos);
        }
        
        // Guardar datos de imágenes en la tabla 'imagenes'
        if (!empty($imagenes)) {
            $imagenModel = new ImagenModel();
            foreach ($imagenes as $imagen) {
                // Extraer la extensión del archivo de la URL
                //$extension = pathinfo($imagen['url'], PATHINFO_EXTENSION);
                //$fileContent = file_get_contents($imagen['file']);

                // Eliminar el prefijo "data:image/jpeg;base64," y obtener solo los datos codificados en Base64
                if (preg_match('/^https?:/', $imagen['url'])) {
                    //como ya aesta almacenado en un servidor simplemente se guarda en el array a actualizar
                    $imagenReferences[] = ['url' => $imagen['url'], 'height' => $imagen['height'], 'width' => $imagen['width'], 'posx' => $imagen['posx'], 'posy' => $imagen['posy'],'alt'=>$imagen['alt'], 'id_mural' => $imagen['id_mural'], 'border_color' => $imagen['border_color'], 'border_style' => $imagen['border_style'], 'border_radius' => $imagen['border_radius']]; // Guardar ademas la referencia en el array $imagenReferences
                }else{

                    if (strpos($imagen['url'], 'data:image/jpeg;base64,') === 0) {
                        // Si es una imagen JPEG, procesa como tal
                        $base64Data = str_replace('data:image/jpeg;base64,', '', $imagen['url']);
                        $filename = 'imagen' . uniqid() . '.jpg';
                        $filename = 'imagen' . uniqid() .'.jpg'; // Nombre del archivo en el servidor con un ID único
                        $filePath = FCPATH  . 'recursos/imagenes/' . $filename;


                        file_put_contents($filePath, base64_decode($base64Data));
                        $imagenReferences[] = ['url' => $filePath, 'height' => $imagen['height'], 'width' => $imagen['width'], 'posx' => $imagen['posx'], 'posy' => $imagen['posy'],'alt'=>$imagen['alt'], 'id_mural' => $imagen['id_mural'], 'border_color' => $imagen['border_color'], 'border_style' => $imagen['border_style'], 'border_radius' => $imagen['border_radius']]; // Guardar ademas la referencia en el array $imagenReferences

                    } else {
                        // Si no es una imagen JPEG, procesa como PNG (u otro formato si es necesario)
                        $base64Data = str_replace('data:image/png;base64,', '', $imagen['url']);
                        $filename = 'imagen' . uniqid() . '.png';
                        $filename = 'imagen' . uniqid() .'.png'; // Nombre del archivo en el servidor con un ID único
                        $filePath = FCPATH  . 'recursos/imagenes/' . $filename;


                        file_put_contents($filePath, base64_decode($base64Data));
                        $imagenReferences[] = ['url' => $filePath, 'height' => $imagen['height'], 'width' => $imagen['width'], 'posx' => $imagen['posx'], 'posy' => $imagen['posy'],'alt'=>$imagen['alt'], 'id_mural' => $imagen['id_mural'], 'border_color' => $imagen['border_color'], 'border_style' => $imagen['border_style'], 'border_radius' => $imagen['border_radius']]; // Guardar ademas la referencia en el array $imagenReferences

                    }



                }


            }

            // Insertar las referencias de pdfs en la tabla 'pdfs'
            if (!empty($imagenReferences)) {
                $imagenModel->insertBatch($imagenReferences);
            }

        }
        



        // Guardar datos de videos en la tabla 'videos'
        if (!empty($videos)) {
            $videoModel = new VideoModel();
            foreach ($videos as $video) {
                //extraer la ruta
                //$extension = pathinfo($video['url_video'], PATHINFO_EXTENSION);

                if (preg_match('/^https?:/', $video['url_video'])) {
                    $videoReferences[] = ['url_video' => $video['url_video'], 'height' => $video['height'], 'width' => $video['width'], 'posx' => $video['posx'], 'posy' => $video['posy'],'formato'=>$video['formato'],'duracion'=>$video['duration'], 'id_mural' => $video['id_mural'], 'border_color' => $video['border_color'], 'border_style' => $video['border_style'], 'border_radius' => $video['border_radius']]; // Guardar ademas la referencia en el array $videoReferences
                }else{
                    $base64Data = str_replace('data:video/mp4;base64,', '', $video['url_video']);
                    $filename = 'video_' . uniqid() .'.mp4'; // Nombre del archivo en el servidor con un ID único
                    $filePath = FCPATH  . 'recursos/videos/' . $filename;
                    file_put_contents($filePath, base64_decode( $base64Data));
                    $videoReferences[] = ['url_video' => $filePath, 'height' => $video['height'], 'width' => $video['width'], 'posx' => $video['posx'], 'posy' => $video['posy'],'formato'=>$video['formato'],'duracion'=>$video['duration'], 'id_mural' => $video['id_mural'], 'border_color' => $video['border_color'], 'border_style' => $video['border_style'], 'border_radius' => $video['border_radius']]; // Guardar ademas la referencia en el array $videoReferences
                }


            }

            // Insertar las referencias de pdfs en la tabla 'pdfs'
            if (!empty($videoReferences)) {
                $videoModel->insertBatch($videoReferences);
            }


        }

        // Guardar datos de pdfs en la tabla 'pdfs'
        if (!empty($pdfs)) {
            $pdfModel = new PdfModel();

            foreach ($pdfs as $pdf) {
                if (preg_match('/^https?:/',  $pdf['url_pdfs'])) {
                    $pdfReferences[] = ['url_pdfs' =>  $pdf['url_pdfs'], 'height' => $pdf['height'], 'width' => $pdf['width'], 'posx' => $pdf['posx'], 'posy' => $pdf['posy'], 'id_mural' => $pdf['id_mural'], 'border_color' => $pdf['border_color'], 'border_style' => $pdf['border_style'], 'border_radius' => $pdf['border_radius']]; // Guardar ademas la referencia en el array $pdfReferences
                }else{
                    // Eliminar el prefijo "data:application/pdf;base64," y obtener solo los datos codificados en Base64
                    $base64Data = str_replace('data:application/pdf;base64,', '', $pdf['url_pdfs']);
                    $filename = 'pdf_' . uniqid() . '.pdf'; // Nombre del archivo en el servidor con un ID único
                    $filePath = FCPATH  . 'recursos/pdfs/' . $filename;
                    file_put_contents($filePath, base64_decode($base64Data));
                    $pdfReferences[] = ['url_pdfs' => $filePath, 'height' => $pdf['height'], 'width' => $pdf['width'], 'posx' => $pdf['posx'], 'posy' => $pdf['posy'], 'id_mural' => $pdf['id_mural'], 'border_color' => $pdf['border_color'], 'border_style' => $pdf['border_style'], 'border_radius' => $pdf['border_radius']]; // Guardar ademas la referencia en el array $pdfReferences
                }



            }

            // Insertar las referencias de pdfs en la tabla 'pdfs'
            if (!empty($pdfReferences)) {
                $pdfModel->insertBatch($pdfReferences);
            }


        }

        // Guardar los datos en la tabla "solicitar"
        $solicitarModel = new SolicitarModel(); // Instanciar el modelo SolicitarModel
        $solicitarData = [
            'id_mural' => $id_mural,
            'id_disenador' => $id_user,
            'id_editor' => 2, //to do 

        ];
        //Guardar la solicitud
        $solicitarModel->insertSolicitud($solicitarData); // Insertar los datos en la tabla "solicitar"




        $resp = [
            'mensaje'=>'Mural creado con éxito'
        ];


        return $this->response->setJSON($resp);



    }

    public function getIdMurals()
    {
        $muralModel = new Mural_Model();
        $idMurals = $muralModel->getIdMural();

        return $this->response->setJSON($idMurals);
    }

    public function getSolicitud(){
        $muralModel = new Mural_Model();
        $muralData = $muralModel->getSolicitudData();

        return $this->response->setJSON($muralData);
    }
    //devuelve las respuesta a las solicitudes aprobadas
    public function getResp(){
        $muralModel = new Mural_Model();
        $muralData = $muralModel->getSolRespuestas();

        return $this->response->setJSON($muralData);
    }
    //devuelve las solicitudes rechazadas
    public function  getReject(){
        $muralModel = new Mural_Model();
        $muralData = $muralModel->getsolreject();

        return $this->response->setJSON($muralData);
    }

    public function solicitudByIdUser(){
        $request = $this->request->getJSON(true);
        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }
        $id_user = $request['id_user'];
        $muralModel = new Mural_Model();
        $muralData = $muralModel->getSolicitudById($id_user);

        return $this->response->setJSON($muralData);

    }

    //para editar mural
    public function EditMural(){
        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        $id_user = $request['id_user'];

        $muralModel = new Mural_Model();
        $muralData = $muralModel->getMuralByUser($id_user);

        $formattedResults = [];
        foreach ($muralData as $row) {
            $formattedRow = [
                'id_mural' => $row['id_mural'],
                'nombrem'=>$row['nombrem'],
                'imgmural' =>$row['imgmural'],
                'imagenes' => $row['imagenes'],
                'textos' => $row['txts'],
                'videos' => $row['videos'],
                'pdfs' => $row['pdfs'],
            ];
            $formattedResults[] = $formattedRow;
        }

        // Convierte el array en JSON y envía la respuesta al frontend
        return $this->response->setJSON($formattedResults);
    }

    //funcion para usar al momento de recibir un Id de un mural //aqui es donde se envia la mural para que se pueda  editar
    public function getMuralbyId()
    {
        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }



        $id_mural = $request['id_mural'];
        $muralModel = new Mural_Model();
        $muralDetails = $muralModel->getMuralbyId($id_mural);
        $muralData = [];
        //funciones para diferentes usos
        function my_dump($data) {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        function removeParentesis($input) {
            return trim($input, '()');
        }

        function removeSlash($input){
          return trim($input, '"\\');
        }

        function decodeFilePath($input) {
            $withoutSlashes = str_replace('\\\\', '\\', $input);
            $decoded = json_decode('"' . $withoutSlashes . '"');
            return  trim($decoded, '"');



        }



        //my_dump( decodeFilePath($randomizera));


        foreach ($muralDetails as $row) {



            $imgs = [];
            if ($row['imagenes']!= '{NULL}') {
            $imgValues = explode('","', substr($row['imagenes'], 2, -2)); // Remove leading and trailing "{" and "}"

            foreach ($imgValues as $imgValue) {
                $imgArray = explode(',', $imgValue);

                $imgs[] = [
                    'id_imagenes' => $imgArray[0],
                    'url' => $imgArray[1],
                    'height' => $imgArray[2],
                    'width' => $imgArray[3],
                    'posx' => $imgArray[4],
                    'posy' => $imgArray[5],
                    'id_mural' => $imgArray[6],
                    'border_color' => $imgArray[7] . ',' . $imgArray[8] . ',' . $imgArray[9],
                    'border_style' => $imgArray[10],
                    'border_radius' => $imgArray[11],
                    'alt' => $imgArray[12],
                ];
            }
            }


            //obtenemos el array de videos y lo separamos en arrays mas pequeños
            $videos = [];
            if ($row['videos']!= '{NULL}') {
            $videosValues = explode('","', substr($row['videos'], 2, -2)); // Remove leading and trailing "{" and "}"
            foreach ($videosValues as $videosValue) {
                $vidArray = explode(',', $videosValue);

                $videos[] = [
                    'id_video' => $vidArray[0],
                    'url_video' => $vidArray[1],
                    'posx' => $vidArray[2],
                    'posy' => $vidArray[3],
                    'height' => $vidArray[4],
                    'width' => $vidArray[5],
                    'formato' => $vidArray[6],
                    'duration' => $vidArray[7],
                    'id_mural' => $vidArray[8],
                    'border_color' => $vidArray[9] . ',' . $vidArray[10]. ',' . $vidArray[11],
                    'border_style' => $vidArray[12],
                    'border_radius' => $vidArray[13],

                    ];


                }
            }

           ;

            $pdfs = [];
            if ($row['pdfs']!= '{NULL}') {
            $pdfsValues = explode('","', substr($row['pdfs'], 2, -2)); // Remove leading and trailing "{" and "}"
            foreach ($pdfsValues as $pdfsValue) {
                $pdfsArray = explode(',', $pdfsValue);

                $pdfs[] = [
                    'id_pdfs' => $pdfsArray[0],
                    'url_pdfs' => $pdfsArray[1],
                    'posx' => $pdfsArray[2],
                    'posy' => $pdfsArray[3],
                    'height' => $pdfsArray[4],
                    'width' => $pdfsArray[5],
                    'id_mural' => $pdfsArray[6],
                    'border_color' => $pdfsArray[7] . ',' . $pdfsArray[8] . ',' . $pdfsArray[9],
                    'border_style' => $pdfsArray[10],
                    'border_radius' => $pdfsArray[11],

                ];
            }

            }

            //obtener los datos de la tabla txts  
            $txts = [];
            if ($row['txts']!= '{NULL}') {
            $txtValues = explode('","', substr($row['txts'], 2, -2)); // Remueve "{" and "}"
            foreach ($txtValues as $txtValue) {
                $txtArray = explode(',', $txtValue);
                //my_dump($txtArray);
                $txts[] = [
                    'id_txt' => $txtArray[0],
                    'font' => $txtArray[1],
                    'font_size' => $txtArray[2],
                    'posx' => $txtArray[3],
                    'posy' => $txtArray[4],
                    'height' => $txtArray[5],
                    'width' => $txtArray[6],
                    'id_mural' => $txtArray[7],
                    'valor' => $txtArray[8],
                    'border_color' => $txtArray[9] . ',' . $txtArray[10] . ',' . $txtArray[11],
                    'border_style' => $txtArray[12],
                    'color' => $txtArray[13] . ',' . $txtArray[14] . ',' . $txtArray[15],
                    'font_weight' => $txtArray[16],
                    'sangria' => $txtArray[17],
                    'backgroundcolor' => $txtArray[18] . ',' . $txtArray[19] . ',' . $txtArray[20],
                    'border_radius' => $txtArray[21],
                ];

            }
            }






            $formattedRow = [
                'id_mural' => $row['id_mural'],
                'id_user' => $row['id_user'],
                'height' => $row['height'],
                'width' => $row['width'],
                'estado' => $row['estado'],
                'nombrem' => $row['nombrem'],
                'imagenes' => array_map(function ($url) {
                    return [
                        'id_imagenes' => removeParentesis($url['id_imagenes']),
                        'url' => decodeFilePath($url['url']),
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'border_radius' => $url['border_radius'],
                        'alt' => removeParentesis($url['alt']),

                    ];
                }, $imgs),
                'textos' => array_map(function ($url) {
                    return [
                        'valor' => removeSlash($url['valor']),
                        'id_txt' => removeParentesis($url['id_txt']),
                        'font' => removeSlash($url['font']),
                        'font_size' => $url['font_size'],
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'color' => removeSlash($url['color']),
                        'font_weight' => $url['font_weight'],
                        'sangria' => $url['sangria'],
                        'backgroundcolor' => removeSlash($url['backgroundcolor']),
                        'border_radius' => removeParentesis($url['border_radius']),
                    ];
                }, $txts),
                'videos' => array_map(function ($url) {
                    return [
                        'id_video' => removeParentesis($url['id_video']),
                        'url_video' => decodeFilePath($url['url_video']),
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'formato' => $url['formato'],
                        'duration' => $url['duration'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'border_radius' => removeParentesis($url['border_radius']),

                    ];
                }, $videos),
                'pdfs' => array_map(function ($url) {
                    return [
                        'id_pdfs' => removeParentesis($url['id_pdfs']),
                        'url_pdfs' => decodeFilePath($url['url_pdfs']),
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'border_radius' => removeParentesis($url['border_radius']),

                    ];
                }, $pdfs),
            ];

            $muralData[] = $formattedRow;
        }

        // Devuelve el array de objetos JSON
        //return json_encode($muralData);
        return $this->response->setJSON($muralData);

    }
    //update del estado del mural dependiendo si es , aceptado o rechazado
    public function updateEstado(){
        $MuralModel = new Mural_Model();

        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        // Obtener datos principales del mural
        $muralData = [
            'id_mural' => $request['id_mural'],
            'id_user' => $request['id_user'],
            'estado' => $request['estado']

        ];


        $MuralModel->update($muralData['id_mural'], $muralData);
        $publicarData = new publicarModel();
        $publicarAr = [
          'id_mural'=>$request['id_mural'],
          'id_user' =>$request['id_user'],
          'fecha_publicacion' =>$request['fecha_publicacion'],
          'fin_publicacion' =>$request['fin_publicacion'],

        ];
        $publicarData->insertPublicacion($publicarAr);//insertamos la publicacion
        //insertamos la fecha de respuesta
        $respuesta = new respuestaModel();
        $respuestaAr = [
            'id_mural'=>$request['id_mural'],
            'id_user' =>$request['id_user'],
            'fecha_respuesta' =>$request['fechaAprobado'],
        ];
        $respuesta->insertRespuesta($respuestaAr);

        $rep = [
          'mensaje'=>'actualización de estado exitosamente'
        ];



        return $this->response->setJSON($rep);

    }

    public  function rechazar(){
        $MuralModel = new Mural_Model();

        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        // Obtener datos principales del mural
        $muralData = [
            'id_mural' => $request['id_mural'],
            'estado' => $request['estado']
        ];
    //fechaRechazado

        $MuralModel->update($muralData['id_mural'], $muralData);

        $respuesta = new respuestaModel();
        $respuestaAr = [
            'id_mural'=>$request['id_mural'],
            'id_user' =>$request['id_user'],
            'fecha_respuesta' =>$request['fechaRechazado'],
        ];
        $respuesta->insertRespuesta($respuestaAr);

        $rep = [
            'mensaje'=>'actualización de estado exitosamente'
        ];
        return $this->response->setJSON($rep);

    }


    //update de la tabla mural y lo que contenga
    public function updateMural(){
        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }
        //obtenemos la imagen para el dashboard
        $base64Data = str_replace('data:image/png;base64,', '',$request['imgMural']);

        $filename = 'imagen' . uniqid() .'.png'; // Nombre del archivo en el servidor con un ID único
        $filePath = FCPATH  . 'recursos/imagenes/' . $filename;

        file_put_contents($filePath, base64_decode($base64Data));
        function my_dump($data) {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }

        // Obtener datos principales del mural
        $muralData = [
            'id_mural' => $request['id_mural'],
            'id_user' => $request['id_user'],
            'height' => $request['height'],
            'width' => $request['width'],
            'estado' => $request['estado'],
            'nombrem' => $request['nombrem'],
            'imgmural'=>$filePath
        ];

        $fechaModificacion = $request['fecha_modificacion'];

        // actualizar datos del mural en la tabla 'mural'
        $muralModel = new Mural_Model();
        $muralModel->update($muralData['id_mural'], $muralData);


        // Obtener datos de textos, imágenes, videos y pdfs
        $textos = $request['textos'] ?? [];
        $imagenes = $request['imagenes'] ?? [];
        $videos = $request['videos'] ?? [];
        $pdfs = $request['pdfs'] ?? [];

        $textoModel = new TextoModel();
        $imagenModel = new ImagenModel();
        $videoModel = new VideoModel();
        $pdfModel = new PdfModel();
        $editM = new editModel();

        // Obtener los IDs actuales en la base de datos
        $textosActuales = $textoModel->where('id_mural', $muralData['id_mural'])->findAll();
        $idsTextosActuales = array_column($textosActuales, 'id_txt');

        // Guardar datos de textos en la tabla 'txt'
        if (!empty($textos)) {


            foreach ($textos as $texto) {
                if (isset($texto['id_txt'])) {
                    // Si el texto tiene un ID, actualízalo
                    $textoModel->update($texto['id_txt'], $texto);
                } else {
                    // Si no tiene un ID, inserta un nuevo registro
                    $textoModel->insertTexto($texto);

                }
            }


        }

        // Guardar datos de imágenes en la tabla 'imagenes'
        $imagenesActuales = $imagenModel->where('id_mural', $muralData['id_mural'])->findAll();

        $idsImagenesActuales = array_column($imagenesActuales, 'id_imagenes');
        if (!empty($imagenes)) {

            foreach ($imagenes as $imagen) {
                if (isset($imagen['id_imagenes'])) {
                    // Si la imagen tiene un ID, actualízala
                    $imagenModel->update($imagen['id_imagenes'], $imagen);
                } else {

                    if (strpos($imagen['url'], 'data:image/jpeg;base64,') === 0) {
                        // Si es una imagen JPEG, procesa como tal
                        $base64Data = str_replace('data:image/jpeg;base64,', '', $imagen['url']);

                        $filename = 'imagen' . uniqid() .'.jpg'; // Nombre del archivo en el servidor con un ID único
                        $filePath = FCPATH  . 'recursos/imagenes/' . $filename;


                        file_put_contents($filePath, base64_decode($base64Data));
                        $imagenReferences[] = ['url' => $filePath, 'height' => $imagen['height'], 'width' => $imagen['width'], 'posx' => $imagen['posx'], 'posy' => $imagen['posy'],'alt'=>$imagen['alt'], 'id_mural' => $imagen['id_mural'], 'border_color' => $imagen['border_color'], 'border_style' => $imagen['border_style'], 'border_radius' => $imagen['border_radius']]; // Guardar ademas la referencia en el array $imagenReferences

                    } else {
                        // Si no es una imagen JPEG, procesa como PNG (u otro formato si es necesario)
                        $base64Data = str_replace('data:image/png;base64,', '', $imagen['url']);
                        $filename = 'imagen' . uniqid() .'.png'; // Nombre del archivo en el servidor con un ID único
                        $filePath = FCPATH  . 'recursos/imagenes/' . $filename;


                        file_put_contents($filePath, base64_decode($base64Data));
                        $imagenReferences[] = ['url' => $filePath, 'height' => $imagen['height'], 'width' => $imagen['width'], 'posx' => $imagen['posx'], 'posy' => $imagen['posy'],'alt'=>$imagen['alt'], 'id_mural' => $imagen['id_mural'], 'border_color' => $imagen['border_color'], 'border_style' => $imagen['border_style'], 'border_radius' => $imagen['border_radius']]; // Guardar ademas la referencia en el array $imagenReferences

                    }
                }
            }

            // Insertar las referencias de pdfs en la tabla 'img'
            if (!empty($imagenReferences)) {
                $imagenModel->insertBatch($imagenReferences);
            }

        }




        // Guardar datos de videos en la tabla 'videos'
        $videosActuales = $videoModel->where('id_mural', $muralData['id_mural'])->findAll();
        $idsVideosActuales = array_column($videosActuales, 'id_video');
        if (!empty($videos)) {

            foreach ($videos as $video) {
                //extraer la ruta
                //$extension = pathinfo($video['url_video'], PATHINFO_EXTENSION);

                if (isset($video['id_video'])) {
                    // Si el video tiene un ID, actualízalo
                    $videoModel->update($video['id_video'], $video);
                } else {
                    // Si no tiene un ID, inserta un nuevo registro
                    $base64Data = str_replace('data:video/mp4;base64,', '', $video['url_video']);
                    $filename = 'video_' . uniqid() .'.mp4'; // Nombre del archivo en el servidor con un ID único
                    $filePath = FCPATH  . 'recursos/videos/' . $filename;
                    file_put_contents($filePath, base64_decode( $base64Data));
                    $videoReferences[] = ['url_video' => $filePath, 'height' => $video['height'], 'width' => $video['width'], 'posx' => $video['posx'], 'posy' => $video['posy'],'formato'=>$video['formato'],'duracion'=>$video['duration'], 'id_mural' => $video['id_mural'], 'border_color' => $video['border_color'], 'border_style' => $video['border_style'], 'border_radius' => $video['border_radius']]; // Guardar ademas la referencia en el array $videoReferences


                }


            }

            // Insertar las referencias de pdfs en la tabla 'video'
            if (!empty($videoReferences)) {

                $videoModel->insertBatch($videoReferences);
            }


        }

        // Guardar datos de pdfs en la tabla 'pdfs'
        $pdfsActuales = $pdfModel->where('id_mural', $muralData['id_mural'])->findAll();
        $idsPdfsActuales = array_column($pdfsActuales, 'id_pdfs');
        if (!empty($pdfs)) {


            foreach ($pdfs as $pdf) {
                if (isset($pdf['id_pdfs'])) {
                    // Si el PDF tiene un ID, actualízalo
                    $pdfModel->update($pdf['id_pdfs'], $pdf);
                } else {
                    // Si no tiene un ID, inserta un nuevo registro
                    // Eliminar el prefijo "data:application/pdf;base64," y obtener solo los datos codificados en Base64
                    $base64Data = str_replace('data:application/pdf;base64,', '', $pdf['url_pdfs']);
                    $filename = 'pdf_' . uniqid() . '.pdf'; // Nombre del archivo en el servidor con un ID único
                    $filePath = FCPATH  . 'recursos/pdfs/' . $filename;
                    file_put_contents($filePath, base64_decode($base64Data));
                    $pdfReferences[] = ['url_pdfs' => $filePath, 'height' => $pdf['height'], 'width' => $pdf['width'], 'posx' => $pdf['posx'], 'posy' => $pdf['posy'], 'id_mural' => $pdf['id_mural'], 'border_color' => $pdf['border_color'], 'border_style' => $pdf['border_style'], 'border_radius' => $pdf['border_radius']]; // Guardar ademas la referencia en el array $pdfReferences
                }

            }

            // Insertar las referencias de pdfs en la tabla 'pdfs'
            if (!empty($pdfReferences)) {
                $pdfModel->insertBatch($pdfReferences);
            }


        }

        // Manejar elementos eliminados (eliminar los que no se actualizaron)

        // Obtener los IDs de textos, imágenes, videos y pdfs en la solicitud
        /*
        $idsTextosEnSolicitud = array_column($textos, 'id_txt');
        $idsImagenesEnSolicitud = array_column($imagenes, 'id_imagenes');
        $idsVideosEnSolicitud = array_column($videos, 'id_video');
        $idsPdfsEnSolicitud = array_column($pdfs, 'id_pdfs');*/










        // Identificar elementos de textos, imágenes, videos y pdfs que deben eliminarse

        // Encuentra los IDs de textos que se deben eliminar (los que están en la base de datos pero no en la solicitud)
        $idsTextosAEliminar = array_diff($idsTextosActuales, array_column($textos, 'id_txt'));
        $idsImagenesAEliminar = array_diff($idsImagenesActuales,array_column($imagenes, 'id_imagenes'));
        $idsVideosAEliminar = array_diff($idsVideosActuales, array_column($videos, 'id_video'));
        $idsPdfsAEliminar = array_diff($idsPdfsActuales,array_column($pdfs, 'id_pdfs'));

        // Eliminar elementos marcados para eliminación
        foreach ($idsTextosAEliminar as $idTexto) {

                $textoModel->delete($idTexto);

        }
        foreach ($idsImagenesAEliminar as $idImagen) {
            $imagenModel->delete($idImagen);
        }
        foreach ($idsVideosAEliminar as $idVideo) {
            $videoModel->delete($idVideo);
        }
        foreach ($idsPdfsAEliminar as $idPdf) {
            $pdfModel->delete($idPdf);
        }

         // Guardar los datos en la tabla "solicitar"

        $editarData = [
            'id_mural' => $muralData['id_mural'],
            'id_user' => $muralData['id_user'],
            'fecha_edicion' => $fechaModificacion
        ];
        //Guardar la fecha de edicion para el log
        $editM->insertEdit($editarData); // Insertar los datos en la tabla "editar"/

        $resp = [
            'mensaje'=>'Mural actuaizado con exito'
        ];


        return $this->response->setJSON($resp);




    }
    //funcion para el cliente que devuelve todos los murales aprobados
    public function muralAll(){

        $muralModel = new Mural_Model();
        $muralDetails = $muralModel->obtenerMuralesAprobados();
        $muralData = [];
        //funciones para diferentes usos
        function my_dump($data) {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        function removeParentesis($input) {
            return trim($input, '()');
        }

        function removeSlash($input){
            return trim($input, '"\\');
        }

        function decodeFilePath($input) {
            $withoutSlashes = str_replace('\\\\', '\\', $input);
            $decoded = json_decode('"' . $withoutSlashes . '"');
            return  trim($decoded, '"');



        }



        //my_dump( decodeFilePath($randomizera));


        foreach ($muralDetails as $row) {



            $imgs = [];
            if ($row['imagenes']!= '{NULL}') {
                $imgValues = explode('","', substr($row['imagenes'], 2, -2)); // Remove leading and trailing "{" and "}"

                foreach ($imgValues as $imgValue) {
                    $imgArray = explode(',', $imgValue);

                    $imgs[] = [
                        'id_imagenes' => $imgArray[0],
                        'url' => $imgArray[1],
                        'height' => $imgArray[2],
                        'width' => $imgArray[3],
                        'posx' => $imgArray[4],
                        'posy' => $imgArray[5],
                        'id_mural' => $imgArray[6],
                        'border_color' => $imgArray[7] . ',' . $imgArray[8] . ',' . $imgArray[9],
                        'border_style' => $imgArray[10],
                        'border_radius' => $imgArray[11],
                        'alt' => $imgArray[12],
                    ];
                }
            }


            //obtenemos el array de videos y lo separamos en arrays mas pequeños
            $videos = [];
            if ($row['videos']!= '{NULL}') {
                $videosValues = explode('","', substr($row['videos'], 2, -2)); // Remove leading and trailing "{" and "}"
                foreach ($videosValues as $videosValue) {
                    $vidArray = explode(',', $videosValue);

                    $videos[] = [
                        'id_video' => $vidArray[0],
                        'url_video' => $vidArray[1],
                        'posx' => $vidArray[2],
                        'posy' => $vidArray[3],
                        'height' => $vidArray[4],
                        'width' => $vidArray[5],
                        'formato' => $vidArray[6],
                        'duration' => $vidArray[7],
                        'id_mural' => $vidArray[8],
                        'border_color' => $vidArray[9] . ',' . $vidArray[10]. ',' . $vidArray[11],
                        'border_style' => $vidArray[12],
                        'border_radius' => $vidArray[13],

                    ];


                }
            }

            ;

            $pdfs = [];
            if ($row['pdfs']!= '{NULL}') {
                $pdfsValues = explode('","', substr($row['pdfs'], 2, -2)); // Remove leading and trailing "{" and "}"
                foreach ($pdfsValues as $pdfsValue) {
                    $pdfsArray = explode(',', $pdfsValue);

                    $pdfs[] = [
                        'id_pdfs' => $pdfsArray[0],
                        'url_pdfs' => $pdfsArray[1],
                        'posx' => $pdfsArray[2],
                        'posy' => $pdfsArray[3],
                        'height' => $pdfsArray[4],
                        'width' => $pdfsArray[5],
                        'id_mural' => $pdfsArray[6],
                        'border_color' => $pdfsArray[7] . ',' . $pdfsArray[8] . ',' . $pdfsArray[9],
                        'border_style' => $pdfsArray[10],
                        'border_radius' => $pdfsArray[11],

                    ];
                }

            }

            //obtener los datos de la tabla txts
            $txts = [];
            if ($row['txts']!= '{NULL}') {
                $txtValues = explode('","', substr($row['txts'], 2, -2)); // Remueve "{" and "}"
                foreach ($txtValues as $txtValue) {
                    $txtArray = explode(',', $txtValue);
                    //my_dump($txtValue);
                    $txts[] = [
                        'id_txt' => $txtArray[0],
                        'font' => $txtArray[1],
                        'font_size' => $txtArray[2],
                        'posx' => $txtArray[3],
                        'posy' => $txtArray[4],
                        'height' => $txtArray[5],
                        'width' => $txtArray[6],
                        'id_mural' => $txtArray[7],
                        'valor' => $txtArray[8],
                        'border_color' => $txtArray[9] . ',' . $txtArray[10] . ',' . $txtArray[11],
                        'border_style' => $txtArray[12],
                        'color' => $txtArray[13] . ',' . $txtArray[14] . ',' . $txtArray[15],
                        'font_weight' => $txtArray[16],
                        'sangria' => $txtArray[17],
                        'backgroundcolor' => $txtArray[18] . ',' . $txtArray[19] . ',' . $txtArray[20],
                        'border_radius' => $txtArray[21],
                    ];

                }
            }






            $formattedRow = [
                'id_mural' => $row['id_mural'],
                'id_user' => $row['id_user'],
                'height' => $row['height'],
                'width' => $row['width'],
                'estado' => $row['estado'],
                'nombrem' => $row['nombrem'],
                'fecha_publicacion' =>$row['fecha_publicacion'],
                'fin_publicacion' => $row['fin_publicacion'],
                'imagenes' => array_map(function ($url) {
                    return [
                        'id_imagenes' => removeParentesis($url['id_imagenes']),
                        'url' => decodeFilePath($url['url']),
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'border_radius' => $url['border_radius'],
                        'alt' => removeParentesis($url['alt']),

                    ];
                }, $imgs),
                'textos' => array_map(function ($url) {
                    return [
                        'valor' => removeSlash($url['valor']),
                        'id_txt' => removeParentesis($url['id_txt']),
                        'font' => removeSlash($url['font']),
                        'font_size' => $url['font_size'],
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'color' => removeSlash($url['color']),
                        'font_weight' => $url['font_weight'],
                        'sangria' => $url['sangria'],
                        'backgroundcolor' => removeSlash($url['backgroundcolor']),
                        'border_radius' => removeParentesis($url['border_radius']),
                    ];
                }, $txts),
                'videos' => array_map(function ($url) {
                    return [
                        'id_video' => removeParentesis($url['id_video']),
                        'url_video' => decodeFilePath($url['url_video']),
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'formato' => $url['formato'],
                        'duration' => $url['duration'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'border_radius' => removeParentesis($url['border_radius']),

                    ];
                }, $videos),
                'pdfs' => array_map(function ($url) {
                    return [
                        'id_pdfs' => removeParentesis($url['id_pdfs']),
                        'url_pdfs' => decodeFilePath($url['url_pdfs']),
                        'posx' => $url['posx'],
                        'posy' => $url['posy'],
                        'height' => $url['height'],
                        'width' => $url['width'],
                        'id_mural' => $url['id_mural'],
                        'border_color' => removeSlash($url['border_color']),
                        'border_style' => $url['border_style'],
                        'border_radius' => removeParentesis($url['border_radius']),

                    ];
                }, $pdfs),
            ];

            $muralData[] = $formattedRow;
        }

        // Devuelve el array de objetos JSON
        //return json_encode($muralData);
        return $this->response->setJSON($muralData);

    }

    public function logs(){

        $request = $this->request->getJSON(true);

        if (empty($request)) {
            return $this->fail('Invalid JSON data', 400);
        }

        $id_user = $request['id_user'];

        $editM = new editModel();
        $editsDetails = $editM->getEditsDetail($id_user);
        $responseData = [];

        foreach ($editsDetails as $detail) {
            $entry = [
                'modificado' => $detail->modificado_por,
                'nombre_mural' => $detail->nombre_mural,
                'fecha_modificacion' => $detail->ultima_modificacion,
            ];

            $responseData[] = $entry;
        }

        return $this->response->setJSON($responseData);
    }






}




