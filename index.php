<?php

use LDAP\Result;

$host = "localhost";
$usuario = "root";
$password = "";
$basededatos = "api";

$conexion = new mysqli($host,$usuario,$password, $basededatos);

if($conexion->connect_error){
    die("Conexión no establecida". $conexion->connect_error);
}

header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];

$path = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';

$buscarId = explode('/', $path);

$id = ($path!=='/' ? end($buscarId):null);

switch($metodo){
    // Consulta SELECT
    case 'GET':
        consulta($conexion,$id);
        break;
    // INSERT
    case 'POST':
        insertar($conexion);
        break;
    // UPDATE
    case 'PUT':
        actualizar($conexion,$id);
        break;
    // DELETE
    case 'DELETE':
        borrar($conexion,$id);
        break;
    default:
    echo "Método no permitido";
}   

function consulta($conexion,$id){
    $sql = ($id === null) ? "SELECT * FROM usuarios" : "SELECT * FROM usuario WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        $datos = array();
        while($fila = $resultado->fetch_assoc()){
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
}

function insertar($conexion){
    $dato = json_decode(file_get_contents('php://input'),true);
    $nombre = $dato['nombre'];

    $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conexion->query($sql);

    if($resultado){
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    }else {
        echo json_encode(array("error" => "Error al insertar el usuario"));
    }
}

function borrar($conexion,$id){

    $sql = "DELETE FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        echo json_encode(array("mensaje" => "Usuario borrado"));
    }else{
        echo json_encode(array("error" => "Error al borrar usuario"));
    }
}

function actualizar($conexion,$id){
    $dato = json_decode(file_get_contents('php://input'),true);
    $nombre = $dato['nombre'];
    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        echo json_encode(array("mensaje" => "Usuario actualizado"));
    } else {
        echo json_encode(array("error" => "Error al actualizar el usuario"));
    }
}

?>