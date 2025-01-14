<?php
use Slim\Factory\AppFactory;
use Src\Controllers\UsuarioController;
use Src\Controllers\ProductoController;
use Src\Controllers\PedidoController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

$app = AppFactory::create();

// Middleware para analizar el cuerpo de las solicitudes
$app->addBodyParsingMiddleware();

// Middleware de enrutamiento y manejo de errores
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Instancia de los controladores
$usuarioController = new UsuarioController();
$productoController = new ProductoController();
$pedidoController = new PedidoController();

// Rutas de usuarios
$app->post('/usuarios', [$usuarioController, 'create']);
$app->get('/usuarios', [$usuarioController, 'getAll']);
$app->get('/usuarios/{id}', [$usuarioController, 'getById']);
$app->put('/usuarios/{id}', [$usuarioController, 'update']);
$app->delete('/usuarios/{id}', [$usuarioController, 'delete']);

// Rutas para la API de productos
$app->post('/productos', [$productoController, 'create']); 
$app->get('/productos', [$productoController, 'getAll']); 
$app->get('/productos/{id}', [$productoController, 'getById']); 
$app->put('/productos/{id}', [$productoController, 'update']); 
$app->delete('/productos/{id}', [$productoController, 'delete']); 

// Rutas para la API de pedidos
$app->post('/pedidos', [$pedidoController, 'create']);
$app->get('/pedidos', [$pedidoController, 'getAll']);
$app->get('/pedidos/{id}', [$pedidoController, 'getById']);
$app->put('/pedidos/{id}/estado', [$pedidoController, 'updateStatus']);
$app->delete('/pedidos/{id}', [$pedidoController, 'delete']);


// Ejecutar la aplicación
$app->run();
