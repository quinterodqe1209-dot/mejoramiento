<?php
declare(strict_types=1);

final class UsuarioModelo
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(string $busqueda, int $pagina, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $st = $this->pdo->prepare(
            'SELECT id, nombre, correo, rol, activo, creado_en
             FROM usuarios
             WHERE nombre LIKE :busqueda OR correo LIKE :busqueda2
             ORDER BY id DESC LIMIT :limite OFFSET :offset'
        );
        $texto = '%' . $busqueda . '%';
        $st->bindValue(':busqueda', $texto);
        $st->bindValue(':busqueda2', $texto);
        $st->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function total(string $busqueda): int
    {
        $st = $this->pdo->prepare(
            'SELECT COUNT(*) FROM usuarios
             WHERE nombre LIKE :busqueda OR correo LIKE :busqueda2'
        );
        $texto = '%' . $busqueda . '%';
        $st->execute([':busqueda' => $texto, ':busqueda2' => $texto]);
        return (int)$st->fetchColumn();
    }

    public function obtener(int $id): ?array
    {
        $st = $this->pdo->prepare(
            'SELECT id, nombre, correo, rol, activo FROM usuarios WHERE id = :id'
        );
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO usuarios (nombre, correo, clave_hash, rol, activo)
             VALUES (:nombre, :correo, :clave_hash, :rol, 1)'
        );
        $st->execute($datos);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datos[':id'] = $id;
        return $this->pdo->prepare(
            'UPDATE usuarios SET nombre = :nombre, correo = :correo,
             rol = :rol, activo = :activo WHERE id = :id'
        )->execute($datos);
    }

    public function cambiarClave(int $id, string $nuevaClaveHash): bool
    {
        return $this->pdo->prepare(
            'UPDATE usuarios SET clave_hash = :clave_hash WHERE id = :id'
        )->execute([':clave_hash' => $nuevaClaveHash, ':id' => $id]);
    }

    public function desactivar(int $id): bool
    {
        return $this->pdo->prepare(
            'UPDATE usuarios SET activo = 0 WHERE id = :id'
        )->execute([':id' => $id]);
    }

    public function correoExiste(string $correo, int $excluirId = 0): bool
    {
        $st = $this->pdo->prepare(
            'SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id <> :id'
        );
        $st->execute([':correo' => $correo, ':id' => $excluirId]);
        return (int)$st->fetchColumn() > 0;
    }
}

