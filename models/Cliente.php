<?php
class Cliente {
    public function __construct(private PDO $db) {}

    public function listarActivos(): array {
        return $this->db->query("SELECT id_cliente,documento,nombre FROM clientes WHERE estado=1 ORDER BY id_cliente=1 DESC,nombre")->fetchAll();
    }
    public function existeActivo(int $id): bool {
        $s=$this->db->prepare("SELECT 1 FROM clientes WHERE id_cliente=:id AND estado=1");
        $s->execute(['id'=>$id]); return (bool)$s->fetchColumn();
    }
    public function listar(array $f=[]): array {
        return $this->consultarListado($f, 500, 0);
    }

    public function contarFiltrados(array $f): int {
        [$where, $parametros] = $this->condicionesListado($f);
        $consulta = $this->db->prepare("SELECT COUNT(*) FROM clientes c WHERE $where");
        $consulta->execute($parametros);
        return (int) $consulta->fetchColumn();
    }

    public function listarPagina(array $f, int $limite, int $inicio): array {
        return $this->consultarListado($f, $limite, $inicio);
    }

    private function consultarListado(array $f, int $limite, int $inicio): array {
        [$where, $parametros] = $this->condicionesListado($f);
        $orden = 'c.estado DESC, c.id_cliente = 1 DESC, c.nombre, c.id_cliente';
        // Seleccionar primero la página para calcular compras solo de esos clientes.
        $consulta = $this->db->prepare("SELECT c.*, COUNT(v.id_venta) compras,
                COALESCE(SUM(v.total), 0) total_compras, MAX(v.fecha_venta) ultima_compra
            FROM (
                SELECT c.id_cliente FROM clientes c WHERE $where
                ORDER BY $orden LIMIT :limite OFFSET :inicio
            ) pagina
            JOIN clientes c ON c.id_cliente = pagina.id_cliente
            LEFT JOIN ventas v ON v.id_cliente = c.id_cliente AND v.estado <> 'anulada'
            GROUP BY c.id_cliente ORDER BY $orden");
        foreach ($parametros as $nombre => $valor) {
            $consulta->bindValue(':' . $nombre, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    private function condicionesListado(array $f): array {
        $where = ['1=1'];
        $parametros = [];
        // q busca por nombre, documento, teléfono o email; ciudad usa igualdad.
        if (($f['q'] ?? '') !== '') {
            $where[] = '(c.nombre LIKE :q1 OR c.documento LIKE :q2 OR c.telefono LIKE :q3 OR c.email LIKE :q4)';
            $parametros['q1'] = $parametros['q2'] = $parametros['q3'] = $parametros['q4'] = '%' . $f['q'] . '%';
        }
        if (($f['estado'] ?? '') !== '') {
            $where[] = 'c.estado = :estado';
            $parametros['estado'] = (int) $f['estado'];
        }
        if (($f['ciudad'] ?? '') !== '') {
            $where[] = 'c.ciudad = :ciudad';
            $parametros['ciudad'] = $f['ciudad'];
        }
        return [implode(' AND ', $where), $parametros];
    }
    public function buscar(int $id): ?array {
        $s=$this->db->prepare("SELECT * FROM clientes WHERE id_cliente=:id");
        $s->execute(['id'=>$id]); return $s->fetch() ?: null;
    }
    public function existeDocumento(string $documento, int $excluirId=0): bool {
        $s=$this->db->prepare("SELECT 1 FROM clientes WHERE documento=:d AND id_cliente<>:id LIMIT 1");
        $s->execute(['d'=>$documento,'id'=>$excluirId]); return (bool)$s->fetchColumn();
    }
    public function existeEmail(string $email, int $excluirId=0): bool {
        $s=$this->db->prepare("SELECT 1 FROM clientes WHERE CAST(LOWER(email) AS BINARY)=CAST(LOWER(:email) AS BINARY) AND id_cliente<>:id LIMIT 1");
        $s->execute(['email'=>$email,'id'=>$excluirId]); return (bool)$s->fetchColumn();
    }

    public function crear(array $d): int {
        $this->db->prepare("INSERT INTO clientes (documento,nombre,telefono,email,direccion,ciudad)
            VALUES (:documento,:nombre,:telefono,:email,:direccion,:ciudad)")->execute($d);
        return (int)$this->db->lastInsertId();
    }
    public function actualizar(int $id, array $d): void {
        $this->db->prepare("UPDATE clientes SET documento=:documento,nombre=:nombre,telefono=:telefono,email=:email,
            direccion=:direccion,ciudad=:ciudad WHERE id_cliente=:id")->execute($d+['id'=>$id]);
    }
    public function cambiarEstado(int $id, int $estado): void {
        $this->db->prepare("UPDATE clientes SET estado=:e WHERE id_cliente=:id")->execute(['e'=>$estado,'id'=>$id]);
    }
    // Resumen de compras (sin anuladas). $usuarioId limita a las ventas de ese vendedor.
    public function resumenCompras(int $id, ?int $usuarioId=null): array {
        $s=$this->db->prepare("SELECT COUNT(*) compras,COALESCE(SUM(total),0) total,COALESCE(AVG(total),0) promedio,MAX(fecha_venta) ultima
            FROM ventas WHERE id_cliente=:id AND estado<>'anulada'".($usuarioId ? " AND id_usuario=:u" : ""));
        $s->execute(['id'=>$id]+($usuarioId ? ['u'=>$usuarioId] : []));
        return $s->fetch();
    }
    public function contarVentas(int $id): int {
        $s=$this->db->prepare("SELECT COUNT(*) FROM ventas WHERE id_cliente=:id");
        $s->execute(['id'=>$id]); return (int)$s->fetchColumn();
    }
    public function eliminar(int $id): void {
        $this->db->prepare("DELETE FROM clientes WHERE id_cliente=:id")->execute(['id'=>$id]);
    }
}
