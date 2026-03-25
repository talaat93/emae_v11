<?php

declare(strict_types=1);

function initializeDatabase(): void
{
    if (!is_file(DATASTORE_PATH)) {
        $seed = [
            'users' => [],
            'quotes' => [],
            'contacts' => [],
            'documents' => [
                ['id' => 1, 'title' => 'Plaquette EMAE', 'filename' => 'plaquette-emae.pdf', 'audience' => 'public', 'created_at' => date('c')],
                ['id' => 2, 'title' => 'Checklist maintenance CVC', 'filename' => 'checklist-maintenance-cvc.pdf', 'audience' => 'client', 'created_at' => date('c')],
                ['id' => 3, 'title' => 'Guide préparation intervention', 'filename' => 'guide-preparation-intervention.pdf', 'audience' => 'client', 'created_at' => date('c')],
            ],
            'last_ids' => ['users' => 0, 'quotes' => 0, 'contacts' => 0, 'documents' => 3],
        ];
        db_write($seed);
    }
}

function db_read(): array
{
    if (!is_file(DATASTORE_PATH)) {
        initializeDatabase();
    }
    $content = @file_get_contents(DATASTORE_PATH);
    $data = $content ? json_decode($content, true) : null;
    return is_array($data) ? $data : [];
}

function db_write(array $data): void
{
    $dir = dirname(DATASTORE_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents(DATASTORE_PATH, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function db_all(string $table): array
{
    $db = db_read();
    return $db[$table] ?? [];
}

function db_insert(string $table, array $record): array
{
    $db = db_read();
    $db['last_ids'][$table] = ($db['last_ids'][$table] ?? 0) + 1;
    $record['id'] = $db['last_ids'][$table];
    $db[$table][] = $record;
    db_write($db);
    return $record;
}

function db_find_one(string $table, callable $predicate): ?array
{
    foreach (db_all($table) as $record) {
        if ($predicate($record)) {
            return $record;
        }
    }
    return null;
}

function db_filter(string $table, callable $predicate): array
{
    return array_values(array_filter(db_all($table), $predicate));
}
