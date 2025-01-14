<?php
namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Database;

class UsuarioController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Crear un nuevo usuario
    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();

        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (:nombre, :correo, :contrasena, :rol)";
        $stmt = $this->db->prepare($sql);

        // Encriptar contraseña
        $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);

        try {
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':correo' => $data['correo'],
                ':contrasena' => $data['contrasena'],
                ':rol' => $data['rol'],
            ]);
            $response->getBody()->write(json_encode(['message' => 'Usuario creado correctamente']));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Listar todos los usuarios
    public function getAll(Request $request, Response $response) {
        $sql = "SELECT id, nombre, correo, rol, estado, fecha_creacion FROM usuarios";
        $stmt = $this->db->query($sql);

        $usuarios = $stmt->fetchAll();
        $response->getBody()->write(json_encode($usuarios));

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Obtener un usuario por ID
    public function getById(Request $request, Response $response, $args) {
        $id = $args['id'];

        $sql = "SELECT id, nombre, correo, rol, estado FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $usuario = $stmt->fetch();
        if ($usuario) {
            $response->getBody()->write(json_encode($usuario));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Usuario no encontrado']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function store(Request $request, Response $response) {
        // Obtener el cuerpo de la solicitud
        $data = $request->getParsedBody();
    
        // Validar que los datos estén presentes
        if (!isset($data['nombre'], $data['correo'], $data['contrasena'], $data['rol'])) {
            return $response->getBody()->write(json_encode(['error' => 'Todos los campos son obligatorios: nombre, correo, contrasena, rol.']));
        }
    
        // Asignar los datos a variables
        $nombre = $data['nombre'];
        $correo = $data['correo'];
        $contrasena = password_hash($data['contrasena'], PASSWORD_DEFAULT);
        $rol = $data['rol'];
    
        // Preparar la consulta SQL
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol, estado, fecha_creacion) 
                VALUES (:nombre, :correo, :contrasena, :rol, 'activo', NOW())";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Ejecutar la consulta
            $stmt->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':contrasena' => $contrasena,
                ':rol' => $rol,
            ]);
    
            // Enviar respuesta exitosa
            return $response->getBody()->write(json_encode(['message' => 'Usuario creado correctamente']));
        } catch (\PDOException $e) {
            // Manejar errores de la base de datos
            return $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
    
    
    // Actualizar un usuario
    public function update(Request $request, Response $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();

        $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol, estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $data['nombre'],
                ':correo' => $data['correo'],
                ':rol' => $data['rol'],
                ':estado' => $data['estado'],
            ]);
            $response->getBody()->write(json_encode(['message' => 'Usuario actualizado correctamente']));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Eliminar (inactivar) un usuario
    public function delete(Request $request, Response $response, $args) {
        $id = $args['id'];

        $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([':id' => $id]);
            $response->getBody()->write(json_encode(['message' => 'Usuario inactivado correctamente']));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    
}
