<?php

class Modelo {

    private $pdo;

    public function __CONSTRUCT() {
        try {
            $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4");
            $this->pdo = new PDO('mysql:host=mysql.railway.internal;port=3306;dbname=railway', 'root', 'GrrhwMEzCtHgGXUUutBCYSpxeVckJbxn', $opciones);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }




    // ============================================================
    //  LISTAR Y OBTENER
    // ============================================================


    public function ListarAnimales() {
        try {
            // Calculamos los días en espera directamente en SQL
            $sc = "SELECT id_animal, nombre, tipo, edad, tamano, foto, descripcion,
                          disponibilidad, fecha_registro, entidad,
                          DATEDIFF(CURRENT_DATE, fecha_registro) AS dias_espera
                   FROM animales
                   ORDER BY disponibilidad DESC, fecha_registro ASC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ListarAnimalesDisponibles() {
        try {
            $sc = "SELECT id_animal, nombre, tipo, edad, tamano, foto, descripcion,
                          disponibilidad, fecha_registro, entidad,
                          DATEDIFF(CURRENT_DATE, fecha_registro) AS dias_espera
                   FROM animales
                   WHERE disponibilidad = 1
                   ORDER BY fecha_registro ASC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ObtenerAnimalId($id) {
        try {
            $sc = "SELECT id_animal, nombre, tipo, edad, tamano, foto, descripcion,
                          disponibilidad, fecha_registro, entidad,
                          DATEDIFF(CURRENT_DATE, fecha_registro) AS dias_espera
                   FROM animales
                   WHERE id_animal = ?";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($id));
            $res = $stm->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $res->visitas     = $this->ListarVisitasAnimalId($id);
                $res->solicitudes = $this->ListarSolicitudesAnimalId($id);
            }
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ListarVisitasAnimalId($id) {
        try {
            $sc = "SELECT id_visita, id_animal, fecha_visita, observaciones
                   FROM visitas
                   WHERE id_animal = ?
                   ORDER BY fecha_visita ASC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($id));
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ObtenerVisitaId($id) {
        try {
            $sc = "SELECT id_visita, id_animal, fecha_visita, observaciones
                   FROM visitas
                   WHERE id_visita = ?";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($id));
            $res = $stm->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $res->animal = $this->ObtenerAnimalId($res->id_animal);
            }
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ListarSolicitudes() {
        try {
            // JOIN para obtener nombre de usuario y animal en el mismo resultado
            $sc = "SELECT s.id_solicitud, s.id_usuario, s.id_animal,
                          s.descripcion, s.fecha_envio, s.estado,
                          u.nombre AS nombreUsuario, u.email, u.telefono,
                          a.nombre AS nombreAnimal, a.tipo
                   FROM solicitudes s
                   INNER JOIN usuarios  u ON (s.id_usuario = u.id_usuario)
                   INNER JOIN animales  a ON (s.id_animal  = a.id_animal)
                   ORDER BY s.fecha_envio DESC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ListarSolicitudesAnimalId($id) {
        try {
            $sc = "SELECT s.id_solicitud, s.id_usuario, s.id_animal,
                          s.descripcion, s.fecha_envio, s.estado,
                          u.nombre AS nombreUsuario
                   FROM solicitudes s
                   INNER JOIN usuarios u ON (s.id_usuario = u.id_usuario)
                   WHERE s.id_animal = ?
                   ORDER BY s.fecha_envio DESC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($id));
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ObtenerSolicitudId($id) {
        try {
            $sc = "SELECT id_solicitud, id_usuario, id_animal,
                          descripcion, fecha_envio, estado
                   FROM solicitudes
                   WHERE id_solicitud = ?";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($id));
            $res = $stm->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $res->usuario = $this->ObtenerUsuarioId($res->id_usuario);
                $res->animal  = $this->ObtenerAnimalId($res->id_animal);
            }
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ListarUsuarios() {
        try {
            $sc = "SELECT id_usuario, nombre, email, telefono, rol
                   FROM usuarios
                   ORDER BY rol DESC, nombre ASC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ObtenerUsuarioId($id) {
        try {
            $sc = "SELECT id_usuario, nombre, email, telefono, rol
                   FROM usuarios
                   WHERE id_usuario = ?";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($id));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ObtenerUsuarioEmail($email) {
        try {
            $sc = "SELECT id_usuario, nombre, email, telefono, rol
                   FROM usuarios
                   WHERE email = ?";
            $stm = $this->pdo->prepare($sc);
            $stm->execute(array($email));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function ListarVisitas() {
        try {
            $sc = "SELECT v.id_visita, v.id_animal, v.fecha_visita, v.observaciones,
                          a.nombre AS nombreAnimal, a.tipo
                   FROM visitas v
                   INNER JOIN animales a ON (v.id_animal = a.id_animal)
                   ORDER BY v.fecha_visita ASC";
            $stm = $this->pdo->prepare($sc);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }




    // ============================================================
    //  ESTADÍSTICAS (para el panel de administración)
    // ============================================================


    public function ObtenerEstadisticas() {
        try {
            $stats = new stdClass();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM animales");
            $stm->execute(); $stats->totalAnimales = (int)$stm->fetchColumn();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM animales WHERE disponibilidad = 1");
            $stm->execute(); $stats->animalesDisponibles = (int)$stm->fetchColumn();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM animales WHERE disponibilidad = 0");
            $stm->execute(); $stats->animalesAdoptados = (int)$stm->fetchColumn();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM solicitudes");
            $stm->execute(); $stats->totalSolicitudes = (int)$stm->fetchColumn();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM solicitudes WHERE estado = 'pendiente'");
            $stm->execute(); $stats->solicitudesPendientes = (int)$stm->fetchColumn();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM visitas");
            $stm->execute(); $stats->totalVisitas = (int)$stm->fetchColumn();

            $stm = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios");
            $stm->execute(); $stats->totalUsuarios = (int)$stm->fetchColumn();

            return $stats;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }




    // ============================================================
    //  AÑADIR (INSERTAR)
    // ============================================================


    public function AnadeAnimal($data) {
        try {
            $sql = "INSERT INTO animales (nombre, tipo, edad, tamano, foto, descripcion, disponibilidad, fecha_registro, entidad)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->pdo->prepare($sql)->execute(array(
                $data->nombre,
                $data->tipo,
                $data->edad,
                $data->tamano,
                $data->foto,
                $data->descripcion,
                isset($data->disponibilidad) ? (int)$data->disponibilidad : 1,
                date('Y-m-d'),
                $data->entidad
            ));
            $data->id_animal = $this->pdo->lastInsertId();
            return $data;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function AnadeSolicitud($data) {
        try {
            // Si viene con email en lugar de id_usuario, intentamos crear/recuperar el usuario
            if (!isset($data->id_usuario) && isset($data->email)) {
                $usuario = $this->ObtenerUsuarioEmail($data->email);
                if (!$usuario) {
                    $nuevoUsuario = new stdClass();
                    $nuevoUsuario->nombre   = $data->nombre;
                    $nuevoUsuario->email    = $data->email;
                    $nuevoUsuario->telefono = isset($data->telefono) ? $data->telefono : '';
                    $nuevoUsuario->rol      = 'usuario';
                    $usuario = $this->AnadeUsuario($nuevoUsuario);
                }
                $data->id_usuario = $usuario->id_usuario;
            }
            $sql = "INSERT INTO solicitudes (id_usuario, id_animal, descripcion, fecha_envio, estado)
                    VALUES (?, ?, ?, ?, 'pendiente')";
            $this->pdo->prepare($sql)->execute(array(
                $data->id_usuario,
                $data->id_animal,
                $data->descripcion,
                date('Y-m-d')
            ));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function AnadeVisita($data) {
        try {
            $sql = "INSERT INTO visitas (id_animal, fecha_visita, observaciones)
                    VALUES (?, ?, ?)";
            $this->pdo->prepare($sql)->execute(array(
                $data->id_animal,
                $data->fecha_visita,
                isset($data->observaciones) ? $data->observaciones : ''
            ));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function AnadeUsuario($data) {
        try {
            $sql = "INSERT INTO usuarios (nombre, email, telefono, rol)
                    VALUES (?, ?, ?, ?)";
            $this->pdo->prepare($sql)->execute(array(
                $data->nombre,
                $data->email,
                isset($data->telefono) ? $data->telefono : '',
                isset($data->rol) ? $data->rol : 'usuario'
            ));
            $data->id_usuario = $this->pdo->lastInsertId();
            return $data;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }




    // ============================================================
    //  BORRAR (ELIMINAR)
    // ============================================================


    public function BorraAnimal($id) {
        try {
            // Las FK tienen ON DELETE CASCADE, pero lo hacemos en transacción
            // por si alguien quita el CASCADE en el futuro
            $this->pdo->beginTransaction();

            $this->pdo->prepare("DELETE FROM solicitudes WHERE id_animal = ?")->execute(array($id));
            $this->pdo->prepare("DELETE FROM visitas    WHERE id_animal = ?")->execute(array($id));
            $this->pdo->prepare("DELETE FROM animales   WHERE id_animal = ?")->execute(array($id));

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die($e->getMessage());
            return false;
        }
    }


    public function BorraSolicitud($id) {
        try {
            $this->pdo->prepare("DELETE FROM solicitudes WHERE id_solicitud = ?")->execute(array($id));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function BorraVisita($id) {
        try {
            $this->pdo->prepare("DELETE FROM visitas WHERE id_visita = ?")->execute(array($id));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function BorraUsuario($id) {
        try {
            $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?")->execute(array($id));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }




    // ============================================================
    //  MODIFICAR (ACTUALIZAR)
    // ============================================================


    public function ModificaAnimal($data) {
        try {
            $sql = "UPDATE animales SET
                        nombre         = ?,
                        tipo           = ?,
                        edad           = ?,
                        tamano         = ?,
                        foto           = ?,
                        descripcion    = ?,
                        disponibilidad = ?,
                        entidad        = ?
                    WHERE id_animal = ?";
            $this->pdo->prepare($sql)->execute(array(
                $data->nombre,
                $data->tipo,
                $data->edad,
                $data->tamano,
                $data->foto,
                $data->descripcion,
                (int)$data->disponibilidad,
                $data->entidad,
                $data->id_animal
            ));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function ModificaEstadoSolicitud($data) {
        try {
            // Si se aprueba, marcamos el animal como no disponible (en transacción)
            $this->pdo->beginTransaction();

            $sql = "UPDATE solicitudes SET estado = ? WHERE id_solicitud = ?";
            $this->pdo->prepare($sql)->execute(array($data->estado, $data->id_solicitud));

            if ($data->estado === 'aprobada') {
                $sol = $this->ObtenerSolicitudId($data->id_solicitud);
                $this->pdo->prepare("UPDATE animales SET disponibilidad = 0 WHERE id_animal = ?")
                           ->execute(array($sol->id_animal));
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die($e->getMessage());
            return false;
        }
    }


    public function ModificaVisita($data) {
        try {
            $sql = "UPDATE visitas SET
                        fecha_visita  = ?,
                        observaciones = ?
                    WHERE id_visita = ?";
            $this->pdo->prepare($sql)->execute(array(
                $data->fecha_visita,
                $data->observaciones,
                $data->id_visita
            ));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


    public function ModificaUsuario($data) {
        try {
            $sql = "UPDATE usuarios SET
                        nombre   = ?,
                        email    = ?,
                        telefono = ?,
                        rol      = ?
                    WHERE id_usuario = ?";
            $this->pdo->prepare($sql)->execute(array(
                $data->nombre,
                $data->email,
                $data->telefono,
                $data->rol,
                $data->id_usuario
            ));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }


}  // class Modelo


?>
