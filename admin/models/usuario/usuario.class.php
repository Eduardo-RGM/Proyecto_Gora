<?php
require_once __DIR__ . '/../sistema.class.php';

class usuario extends sistema
{
    function create($data)
    {
        $datos = [];
        if (!isset($data['correo']) || !isset($data['contrasena'])) {
            $datos['error'] = 'Faltan datos requeridos';
            return $datos;
        }

        $data['correo'] = strtolower(trim($data['correo']));
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL) || trim($data['contrasena']) === '') {
            return ['error' => 'Ingresa un correo válido y una contraseña.'];
        }
        $data['contrasena'] = md5($data['contrasena']);
        $sql = "INSERT INTO usuario(correo, contrasena) VALUES(:correo, :contrasena)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $data['correo'], PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $data['contrasena'], PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1062) {
                return ['error' => 'Ya existe un usuario con ese correo.'];
            }
            throw $exception;
        }

        $datos = $data;
        $datos['afectados'] = $stmt->rowCount();
        $datos['id_usuario'] = $this->db->lastInsertId();
        $contenido = '<p>Bienvenido a GORA.</p><p>Tu usuario fue creado con el correo <strong>'
            . htmlspecialchars($data['correo'], ENT_QUOTES, 'UTF-8')
            . '</strong>.</p>';
        $datos['correo_enviado'] = $this->enviarCorreo(
            [$data['correo']],
            'Bienvenido a GORA',
            $contenido
        );
        return $datos;
    }

    function getone($id)
    {
        $datos = [];
        if (is_numeric($id)) {
            $sql = "SELECT * FROM usuario WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    function getall()
    {
        $datos = [];
        $sql = "SELECT * FROM usuario";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    function getAllRoles()
    {
        $datos = [];
        $sql = "SELECT * FROM rol";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    function getRolesUsuario($id_usuario)
    {
        $datos = [];
        if (is_numeric($id_usuario)) {
            $sql = "SELECT id_rol FROM usuario_rol WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $datos;
    }

    function updateRoles($id_usuario, $roles)
    {
        $afectados = 0;
        if (is_numeric($id_usuario)) {
            $sql = "DELETE FROM usuario_rol WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();

            if (is_array($roles)) {
                $sql = "INSERT INTO usuario_rol(id_usuario, id_rol) VALUES(:id_usuario, :id_rol)";
                foreach ($roles as $id_rol) {
                    if (is_numeric($id_rol)) {
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
                        $stmt->execute();
                        $afectados += $stmt->rowCount();
                    }
                }
            }
        }
        return $afectados;
    }

    function update($id, $data)
    {
        $afectados = 0;
        if (is_numeric($id) && isset($data['correo'])) {
            $data['correo'] = strtolower(trim($data['correo']));
            $cambiarContrasena = isset($data['contrasena']) && $data['contrasena'] !== '';
            $sql = $cambiarContrasena
                ? "UPDATE usuario SET correo = :correo, contrasena = :contrasena WHERE id_usuario = :id_usuario"
                : "UPDATE usuario SET correo = :correo WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':correo', $data['correo'], PDO::PARAM_STR);
            if ($cambiarContrasena) {
                $data['contrasena'] = md5($data['contrasena']);
                $stmt->bindParam(':contrasena', $data['contrasena'], PDO::PARAM_STR);
            }
            $stmt->bindParam(':id_usuario', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    function delete($id)
    {
        $afectados = 0;
        if (is_numeric($id)) {
            try {
                $this->db->beginTransaction();

                $stmt = $this->db->prepare(
                    "DELETE FROM usuario_rol WHERE id_usuario = :id_usuario"
                );
                $stmt->execute([':id_usuario' => $id]);

                $stmt = $this->db->prepare(
                    "DELETE FROM usuario WHERE id_usuario = :id_usuario"
                );
                $stmt->execute([':id_usuario' => $id]);
                $afectados = $stmt->rowCount();

                $this->db->commit();
            } catch (PDOException $exception) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                throw $exception;
            }
        }
        return $afectados;
    }
}
