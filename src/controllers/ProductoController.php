<?php
namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Database;

class ProductoController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Crear un nuevo producto
    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();

        $sql = "INSERT INTO productos (nombre, descripcion, precio, tipo, estado) VALUES (:nombre, :descripcion, :precio, :tipo, :estado)";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion'],
                ':precio' => $data['precio'],
                ':tipo' => $data['tipo'],
                ':estado' => $data['estado'] ?? 'disponible', // Valor por defecto 'disponible'
            ]);
            $response->getBody()->write(json_encode(['message' => 'Producto creado correctamente']));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Listar todos los productos
    public function getAll(Request $request, Response $response) {
        $sql = "SELECT id, nombre, descripcion, precio, tipo, estado, fecha_creacion FROM productos";
        $stmt = $this->db->query($sql);

        $productos = $stmt->fetchAll();
        $response->getBody()->write(json_encode($productos));

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Obtener un producto por ID
    public function getById(Request $request, Response $response, $args) {
        $id = $args['id'];

        $sql = "SELECT id, nombre, descripcion, precio, tipo, estado FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $producto = $stmt->fetch();
        if ($producto) {
            $response->getBody()->write(json_encode($producto));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Producto no encontrado']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Actualizar un producto
    public function update(Request $request, Response $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();

        $sql = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, tipo = :tipo, estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion'],
                ':precio' => $data['precio'],
                ':tipo' => $data['tipo'],
                ':estado' => $data['estado'],
            ]);
            $response->getBody()->write(json_encode(['message' => 'Producto actualizado correctamente']));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Eliminar (inactivar) un producto
    public function delete(Request $request, Response $response, $args) {
        $id = $args['id'];

        $sql = "UPDATE productos SET estado = 'agotado' WHERE id = :id"; // Inactivar producto
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([':id' => $id]);
            $response->getBody()->write(json_encode(['message' => 'Producto inactivado correctamente']));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
