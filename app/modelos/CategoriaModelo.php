<?php
declare(strict_types=1);

final class CategoriaModelo
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(string $busqueda, int $pagina, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $st = $this->pdo->prepare(
            'SELECT id, nombre, descripcion
             FROM categorias
             WHERE nombre LIKE :busqueda
             ORDER BY nombre ASC LIMIT :limite OFFSET :offset'
        );
        $st->bindValue(':busqueda', '%' . $busqueda . '%');
        $st->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function total(string $busqueda): int
    {
        $st = $this->pdo->prepare(
            'SELECT COUNT(*) FROM categorias WHERE nombre LIKE :busqueda'
        );
        $st->execute([':busqueda' => '%' . $busqueda . '%']);
        return (int)$st->fetchColumn();
    }

    public function obtener(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM categorias WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO categorias (nombre, descripcion)
             VALUES (:nombre, :descripcion)'
        );
        $st->execute($datos);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datos[':id'] = $id;
        return $this->pdo->prepare(
            'UPDATE categorias SET nombre = :nombre, descripcion = :descripcion
             WHERE id = :id'
        )->execute($datos);
    }

    public function eliminar(int $id): bool
    {
        return $this->pdo->prepare('DELETE FROM categorias WHERE id = :id')
            ->execute([':id' => $id]);
    }
}

