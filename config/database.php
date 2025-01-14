<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'restaurante';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            // Crear la conexión PDO
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            // Configurar opciones de PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Solo índices asociativos
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Preparación nativa de consultas
        } catch (PDOException $e) {
            // Manejo de errores de conexión
            echo 'Error de conexión: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
