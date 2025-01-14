<?php
namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Database;

class PedidoController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Crear un nuevo pedido
    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $productos = $data['productos']; // Lista de productos con id y cantidad
        $id_usuario = $data['id_usuario']; // Usuario que realiza el pedido

        $total = 0;

        // Primero, obtener el precio de cada producto y calcular el total
        foreach ($productos as $producto) {
            $sql = "SELECT precio FROM productos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $producto['id']]);
            $productoDb = $stmt->fetch();

            if ($productoDb) {
                $total += $productoDb['precio'] * $producto['cantidad'];
            }
        }

        // Crear el pedido con el total calculado
        $sql = "INSERT INTO pedidos (id_usuario, total, estado) VALUES (:id_usuario, :total, :estado)";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':total' => $total,
                ':estado' => 'pendiente', // Estado inicial del pedido
            ]);

            // Obtener el id del nuevo pedido creado
            $pedidoId = $this->db->lastInsertId();

            // Insertar los detalles del pedido (productos y cantidades)
            foreach ($productos as $producto) {
                $sqlDetalle = "INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                               VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
                $stmtDetalle = $this->db->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':id_pedido' => $pedidoId,
                    ':id_producto' => $producto['id'],
                    ':cantidad' => $producto['cantidad'],
                    ':precio_unitario' => $productoDb['precio'],
                ]);
            }

            $response->getBody()->write(json_encode(['message' => 'Pedido creado correctamente', 'pedido_id' => $pedidoId]));
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    // Listar todos los pedidos (solo para los meseros)
    public function getAll(Request $request, Response $response) {
        $sql = "SELECT * FROM pedidos";
        $stmt = $this->db->query($sql);
        $pedidos = $stmt->fetchAll();
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Obtener un pedido por ID
    public function getById(Request $request, Response $response, $args) {
        $id = $args['id'];
        $sql = "SELECT * FROM pedidos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $pedido = $stmt->fetch();

        if ($pedido) {
            $response->getBody()->write(json_encode($pedido));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Pedido no encontrado']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Actualizar el estado de un pedido
    public function updateStatus(Request $request, Response $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $estado = $data['estado']; // Estado nuevo

        // Validar el estado
        $validStates = ['pendiente', 'preparando', 'listo', 'completado'];
        if (!in_array($estado, $validStates)) {
            $response->getBody()->write(json_encode(['message' => 'Estado no valido']));
        }

        // Verificar si el estado es "pendiente" antes de permitir edición
        $sql = "SELECT estado FROM pedidos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $pedido = $stmt->fetch();

        if ($pedido['estado'] != 'pendiente' && $estado != 'completado') {
            $response->getBody()->write(json_encode(['message' => 'No se puede editar el pedido si su estado ya no es "Pendiente"']));
        }

        // Actualizar el estado del pedido
        $sql = "UPDATE pedidos SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':estado' => $estado
        ]);

        $response->getBody()->write(json_encode(['message' => 'Estado del pedido actualizado correctamente']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Eliminar (inactivar) un pedido
    public function delete(Request $request, Response $response, $args) {
        $id = $args['id'];

        // Verificar si el estado es "pendiente" antes de permitir eliminación
        $sql = "SELECT estado FROM pedidos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $pedido = $stmt->fetch();

        if ($pedido['estado'] != 'pendiente') {
            $response->getBody()->write(json_encode(['message' => 'No se puede eliminar un pedido si su estado no es "pendiente"']));
        }

        // Eliminar el pedido
        $sql = "DELETE FROM pedidos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $response->getBody()->write(json_encode(['message' => 'Pedido eliminado correctamente']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
