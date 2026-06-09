<?php

header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once 'modelo.php';
$modelo = new Modelo();

$datos  = file_get_contents('php://input');
$objeto = json_decode($datos);

if ($objeto != null) {
    switch ($objeto->accion) {


        // ============================================================
        //  LISTAR Y OBTENER
        // ============================================================

        case "ListarAnimales":
            print json_encode($modelo->ListarAnimales());
            break;

        case "ListarAnimalesDisponibles":
            print json_encode($modelo->ListarAnimalesDisponibles());
            break;


        case "ObtenerAnimalId": //este me da un animal concreto a partir de su id, lo uso para mostrar los detalles del animal en la página de detalles
            print json_encode($modelo->ObtenerAnimalId($objeto->id));
            break;

        case "ListarVisitasAnimalId":
            print json_encode($modelo->ListarVisitasAnimalId($objeto->id));
            break;

        case "ObtenerVisitaId":
            print json_encode($modelo->ObtenerVisitaId($objeto->id));
            break;

        case "ListarSolicitudes":
            print json_encode($modelo->ListarSolicitudes());
            break;

        case "ListarSolicitudesAnimalId":
            print json_encode($modelo->ListarSolicitudesAnimalId($objeto->id));
            break;

        case "ObtenerSolicitudId":
            print json_encode($modelo->ObtenerSolicitudId($objeto->id));
            break;

        case "ListarUsuarios":
            print json_encode($modelo->ListarUsuarios());
            break;

        case "ObtenerUsuarioId":
            print json_encode($modelo->ObtenerUsuarioId($objeto->id));
            break;

        case "ObtenerUsuarioEmail":
            print json_encode($modelo->ObtenerUsuarioEmail($objeto->email));
            break;

        case "ListarVisitas":
            print json_encode($modelo->ListarVisitas());
            break;

        case "ObtenerEstadisticas":
            print json_encode($modelo->ObtenerEstadisticas());
            break;




        // ============================================================
        //  AÑADIR (INSERTAR)
        // ============================================================

        case "AnadeAnimal":
            $res = $modelo->AnadeAnimal($objeto->animal);
            print json_encode($res);   // devuelve el objeto con el id_animal asignado
            break;

        case "AnadeSolicitud":
            if ($modelo->AnadeSolicitud($objeto->solicitud))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "AnadeVisita":
            if ($modelo->AnadeVisita($objeto->visita))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "AnadeUsuario":
            $res = $modelo->AnadeUsuario($objeto->usuario);
            print json_encode($res);   // devuelve el objeto con el id_usuario asignado
            break;




        // ============================================================
        //  BORRAR (ELIMINAR)
        // ============================================================

        case "BorraAnimal":
            if ($modelo->BorraAnimal($objeto->id))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "BorraSolicitud":
            if ($modelo->BorraSolicitud($objeto->id))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "BorraVisita":
            if ($modelo->BorraVisita($objeto->id))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "BorraUsuario":
            if ($modelo->BorraUsuario($objeto->id))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;




        // ============================================================
        //  MODIFICAR (ACTUALIZAR)
        // ============================================================

        case "ModificaAnimal":
            if ($modelo->ModificaAnimal($objeto->animal))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "ModificaEstadoSolicitud":
            // objeto.solicitud = { id_solicitud: X, estado: "aprobada"|"rechazada" }
            if ($modelo->ModificaEstadoSolicitud($objeto->solicitud))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "ModificaVisita":
            if ($modelo->ModificaVisita($objeto->visita))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;

        case "ModificaUsuario":
            if ($modelo->ModificaUsuario($objeto->usuario))
                print '{"result":"OK"}';
            else
                print '{"result":"FAIL"}';
            break;


    }  
}  
?>
