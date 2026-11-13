<?php
/**
 * Base Model — abstraksi CRUD sederhana di atas PDO.
 * Setiap model cukup mendefinisikan $table, $primaryKey, dan method kustomnya.
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    protected static function db(): PDO
    {
        return Database::connect();
    }

    /** Semua baris. */
    public static function all(?string $orderBy = null): array
    {
        $sql = 'SELECT * FROM ' . static::$table;
        if ($orderBy) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        return static::db()->query($sql)->fetchAll();
    }

    /** Ambil 1 baris berdasarkan primary key. */
    public static function find($id): ?array
    {
        $st = static::db()->prepare(
            'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ? LIMIT 1'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Ambil baris berdasarkan kolom tertentu. */
    public static function where(string $column, $value, ?string $orderBy = null): array
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE `' . $column . '` = ?';
        if ($orderBy) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        $st = static::db()->prepare($sql);
        $st->execute([$value]);
        return $st->fetchAll();
    }

    /** Insert 1 baris, mengembalikan ID baru. */
    public static function insert(array $data): int
    {
        $cols   = array_keys($data);
        $sql    = 'INSERT INTO ' . static::$table
            . ' (`' . implode('`,`', $cols) . '`) VALUES ('
            . implode(',', array_fill(0, count($cols), '?')) . ')';
        $st = static::db()->prepare($sql);
        $st->execute(array_values($data));
        return (int) static::db()->lastInsertId();
    }

    /** Update baris berdasarkan primary key. */
    public static function update($id, array $data): bool
    {
        if (!$data) {
            return true;
        }
        $sets = [];
        foreach (array_keys($data) as $col) {
            $sets[] = '`' . $col . '` = ?';
        }
        $st = static::db()->prepare(
            'UPDATE ' . static::$table . ' SET ' . implode(', ', $sets)
            . ' WHERE ' . static::$primaryKey . ' = ?'
        );
        $params = array_values($data);
        $params[] = $id;
        return $st->execute($params);
    }

    /** Hapus baris berdasarkan primary key. */
    public static function delete($id): bool
    {
        $st = static::db()->prepare(
            'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?'
        );
        return $st->execute([$id]);
    }

    /** Jumlah total baris. */
    public static function count(): int
    {
        return (int) static::db()
            ->query('SELECT COUNT(*) FROM ' . static::$table)
            ->fetchColumn();
    }
}
