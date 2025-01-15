# API Pedidos

Esta es una API RESTful para gestionar usuarios, productos y pedidos. Está desarrollada utilizando PHP con el framework Slim y requiere ciertas dependencias para funcionar correctamente.

## Requisitos

Asegúrate de tener los siguientes requisitos instalados en tu entorno de desarrollo:

- **PHP 7.4 o superior**.
- **Composer** para la gestión de dependencias.

## Instalación

### Paso 1: Clonar el repositorio

Primero, clona el repositorio en tu máquina local:

```bash
git clone -b master https://github.com/Ronny2004/API_Pedidos.git

Paso 2: Instalar dependencias
Navega al directorio del proyecto y ejecuta el siguiente comando para instalar todas las dependencias necesarias:
cd APIpedidos
composer install

Este comando instalará las dependencias listadas en el archivo composer.json, incluyendo:
Slim Framework: Un micro-framework para construir APIs en PHP.
Slim PSR-7: Implementación de PSR-7 (interfaz de HTTP).
Monolog: Para el manejo de logs.
Dotenv: Para gestionar variables de entorno.
PHP-fig/psr-15: Para manejar middleware.
PHP-fig/psr-7: Para trabajar con objetos de solicitud y respuesta.

Paso 3: Configurar la base de datos
Configura tu base de datos en el archivo .env (si es que usas variables de entorno para gestionar la configuración de la base de datos). Si no estás utilizando variables de entorno, asegúrate de actualizar la configuración de la base de datos en el archivo config/database.php:
# Configuración de la base de datos
DB_HOST=localhost
DB_PORT=3306
DB_NAME=restaurante
DB_USER=root
DB_PASSWORD=

# Clave secreta para JWT
JWT_SECRET_KEY=Ronny292004


Paso 4: Ejecutar el servidor
Una vez que hayas configurado todo, puedes ejecutar el servidor incorporado de PHP para probar la API:
php -S localhost:8000 -t public
Esto levantará un servidor local en el puerto 8000. Ahora podrás acceder a la API en http://localhost:8000.

Rutas Disponibles
Usuarios
POST /usuarios: Crear un nuevo usuario.
GET /usuarios: Obtener todos los usuarios.
GET /usuarios/{id}: Obtener un usuario por ID.
PUT /usuarios/{id}: Actualizar un usuario.
DELETE /usuarios/{id}: Eliminar un usuario.
Productos
POST /productos: Crear un nuevo producto.
GET /productos: Obtener todos los productos.
GET /productos/{id}: Obtener un producto por ID.
PUT /productos/{id}: Actualizar un producto.
DELETE /productos/{id}: Eliminar un producto.
Pedidos
POST /pedidos: Crear un nuevo pedido.
GET /pedidos: Obtener todos los pedidos.
GET /pedidos/{id}: Obtener un pedido por ID.
PUT /pedidos/{id}/estado: Actualizar el estado de un pedido.
DELETE /pedidos/{id}: Eliminar un pedido.
Contribución
Si deseas contribuir al proyecto, siéntete libre de hacer un fork del repositorio y enviar un pull request. Asegúrate de seguir las convenciones de codificación y realizar las pruebas necesarias antes de enviar un pull request.

Licencia
Este proyecto está licenciado bajo la Licencia MIT. Para más detalles, revisa el archivo LICENSE.

### Dependencias clave:

1. **Slim Framework**: `slim/slim`
   - Framework para crear aplicaciones y APIs RESTful.
   
2. **PSR-7**: `slim/psr7`
   - Implementación de las interfaces PSR-7 para trabajar con peticiones y respuestas HTTP.

3. **Monolog**: `monolog/monolog`
   - Biblioteca para registro de logs.

4. **Dotenv**: `vlucas/phpdotenv`
   - Permite gestionar configuraciones a través de un archivo `.env`.

5. **PHP-fig/psr-15**: `php-fig/psr-15`
   - Interfaz estándar para middleware.

6. **PHP-fig/psr-7**: `php-fig/psr-7`
   - Interfaz estándar para el manejo de objetos de solicitud y respuesta HTTP.
