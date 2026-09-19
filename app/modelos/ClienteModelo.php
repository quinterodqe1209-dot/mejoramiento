<?php
declare(strict_types=1);

final class ClienteModelo
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(string $busqueda, int $pagina, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $st = $this->pdo->prepare(
            'SELECT id, nombre, documento, telefono, correo, direccion
             FROM clientes
               WHERE nombre LIKE :busquedaNombre OR documento LIKE :busquedaDocumento
             ORDER BY id DESC LIMIT :limite OFFSET :offset'
        );
           $texto = '%' . $busqueda . '%';
           $st->bindValue(':busquedaNombre', $texto);
           $st->bindValue(':busquedaDocumento', $texto);
        $st->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function total(string $busqueda): int
    {
        $st = $this->pdo->prepare(
            'SELECT COUNT(*) FROM clientes
             WHERE nombre LIKE :busquedaNombre OR documento LIKE :busquedaDocumento'
        );
        $texto = '%' . $busqueda . '%';
        $st->execute([
            ':busquedaNombre' => $texto,
            ':busquedaDocumento' => $texto,
        ]);
        return (int)$st->fetchColumn();
    }

    public function obtener(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM clientes WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO clientes (nombre, documento, telefono, correo, direccion)
             VALUES (:nombre, :documento, :telefono, :correo, :direccion)'
        );
        $st->execute($datos);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datos[':id'] = $id;
        return $this->pdo->prepare(
            'UPDATE clientes SET nombre = :nombre, documento = :documento,
             telefono = :telefono, correo = :correo, direccion = :direccion
             WHERE id = :id'
        )->execute($datos);
    }

    public function eliminar(int $id): bool
    {
        return $this->pdo->prepare('DELETE FROM clientes WHERE id = :id')
            ->execute([':id' => $id]);
    }
}
