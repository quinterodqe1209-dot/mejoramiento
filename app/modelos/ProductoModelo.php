<?php
declare(strict_types=1);

final class ProductoModelo
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(string $busqueda, int $pagina, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $st = $this->pdo->prepare(
            'SELECT p.id, p.nombre, p.precio, p.stock, c.nombre AS categoria
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.activo = 1 AND p.nombre LIKE :busqueda
             ORDER BY p.id DESC LIMIT :limite OFFSET :offset'
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
            'SELECT COUNT(*) FROM productos
             WHERE activo = 1 AND nombre LIKE :busqueda'
        );
        $st->execute([':busqueda' => '%' . $busqueda . '%']);
        return (int)$st->fetchColumn();
    }

    public function categorias(): array
    {
        return $this->pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM productos WHERE id = :id AND activo = 1');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO productos (nombre, categoria_id, precio, stock, activo)
             VALUES (:nombre, :categoria, :precio, :stock, 1)'
        );
        $st->execute($datos);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datos[':id'] = $id;
        return $this->pdo->prepare(
            'UPDATE productos SET nombre = :nombre, categoria_id = :categoria,
             precio = :precio, stock = :stock WHERE id = :id AND activo = 1'
        )->execute($datos);
    }

    public function desactivar(int $id): bool
    {
        return $this->pdo->prepare('UPDATE productos SET activo = 0 WHERE id = :id')
            ->execute([':id' => $id]);
    }
}
