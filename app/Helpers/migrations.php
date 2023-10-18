<?php

use Illuminate\Support\Facades\Schema;

if (!function_exists('database_exists')) {
    /**
     * @param string $database
     * @return bool
     */
    function database_exists(string $database): bool
    {
        $query = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?";
        $result = DB::select($query, [$database]);

        return count($result) > 0;
    }
}

if (!function_exists('table_exists')) {
    /**
     * @param string $table
     * @param string|null $connection
     * @return bool
     */
    function table_exists(string $table, string|null $connection = null): bool
    {
        $connection = $connection ?? config('database.default');
        return Schema::connection($connection)->hasTable($table);
    }
}

if (!function_exists('column_exists')) {
    /**
     * @param string $table
     * @param string $column
     * @param string|null $connection
     * @return bool
     */
    function column_exists(string $table, string $column, string|null $connection = null): bool
    {
        $connection = $connection ?? config('database.default');
        return Schema::connection($connection)->hasColumn($table, $column);
    }
}

if (!function_exists('foreign_exists')) {
    /**
     * @param string $table
     * @param string $constraint
     * @param string|null $connection
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    function foreign_exists(string $table, string $constraint, string|null $connection = null): bool
    {
        $connection = $connection ?? config('database.default');
        $fkColumns = Schema::connection($connection)->getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys($table);
        return collect($fkColumns)->map(function ($fkColumn) {
            return $fkColumn->getColumns();
        })->flatten()->contains($constraint);
    }
}

if (!function_exists('index_exists')) {
    /**
     * @param string $table
     * @param string $index
     * @param string|null $connection
     * @return bool
     */
    function index_exists(string $table, string $index, string|null $connection = null): bool
    {
        $connection = $connection ?? config('database.default');
        $result = Schema::connection($connection)->getConnection()->select("SHOW INDEX FROM $table WHERE Key_name = ?", [$index]);
        return collect($result)->isNotEmpty();
    }
}