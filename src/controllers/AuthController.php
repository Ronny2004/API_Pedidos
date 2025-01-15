<?php
namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Database;
use Dotenv\Dotenv;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthController {
    private $db;
    private $secretKey;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
        $dotenv->load();
        
        // Verificar que la clave secreta esté definida
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'Ronny292004';
        if (!$this->secretKey) {
            throw new \Exception('Clave secreta no definida en el archivo .env');
        }

        $this->db = (new Database())->connect();
    }

    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $correo = $data['correo'] ?? '';
        $contrasena = $data['contrasena'] ?? ''; 

        if (empty($correo) || empty($contrasena)) {
            $response->getBody()->write(json_encode(['error' => 'Correo y contraseña son requeridos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $sql = "SELECT * FROM usuarios WHERE correo = :correo AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            $payload = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol' => $usuario['rol'],
                'iat' => time(),
                'exp' => time() + 3600
            ];

            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
            $response->getBody()->write(json_encode(['token' => $jwt]));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Credenciales incorrectas o usuario inactivo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarJWT(Request $request, Response $response, $next) {
        $headers = $request->getHeaders();
        $authorizationHeader = $headers['Authorization'][0] ?? null;
    
        // Verificación explícita del tipo de autorización
        if ($authorizationHeader && strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $authorizationHeader);
    
            try {
                // No es necesario pasar los encabezados por referencia
                $decoded = JWT::decode($token, $this->secretKey);
                $request = $request->withAttribute('usuario', $decoded);
                $response = $next($request, $response);
            } catch (ExpiredException $e) {
                $response->getBody()->write(json_encode(['error' => 'Token expirado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            } catch (SignatureInvalidException $e) {
                $response->getBody()->write(json_encode(['error' => 'Firma del token inválida']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            } catch (\Exception $e) {
                $response->getBody()->write(json_encode(['error' => 'Token inválido']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        } else {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado o formato incorrecto']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    
        return $response;
    }
    
}
