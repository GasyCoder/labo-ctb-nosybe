<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalysesSqlSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('analyses-data.sql');
        if (!file_exists($path)) {
            $this->command->warn("Fichier introuvable: {$path}");
            return;
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            $this->command->warn("Le fichier est vide ou illisible: {$path}");
            return;
        }

        $statements = $this->splitSqlStatements($sql);

        // Compter les lignes INSERT vers `analyses` (en ignorant DDL)
        $totalRows = 0;
        foreach ($statements as $stmt) {
            if ($this->shouldSkip($stmt)) continue;
            $totalRows += $this->countAnalysesRows($stmt);
        }

        $bar = null;
        if ($totalRows > 0) {
            $bar = $this->command->getOutput()->createProgressBar($totalRows);
            $bar->setFormat('debug');
            $bar->start();
        } else {
            $this->command->line('Aucune ligne `analyses` détectée (ok si le fichier ne contient pas d’INSERT).');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Décommente si tu veux repartir propre à chaque seed:
        // DB::table('analyses')->truncate();

        DB::beginTransaction();

        $inserted = 0;

        try {
            foreach ($statements as $stmt) {
                if ($this->shouldSkip($stmt)) {
                    continue; // on n’exécute pas la DDL / bruit
                }

                $rows = $this->countAnalysesRows($stmt); // 0 si pas un INSERT analyses

                DB::unprepared($stmt);

                if ($rows > 0 && $bar) {
                    $bar->advance($rows);
                    $inserted += $rows;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($bar) { $bar->finish(); $this->command->getOutput()->writeln(''); }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->command->error("Erreur pendant l'import: ".$e->getMessage());
            throw $e;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        if ($bar) {
            $bar->finish();
            $this->command->getOutput()->writeln('');
        }

        $this->command->info("Import terminé. Lignes `analyses` insérées: {$inserted}/{$totalRows}");
    }

    private function shouldSkip(string $stmt): bool
    {
        $s = ltrim($stmt);

        // ignorer commentaires MySQL /*! ... */ et lignes SET/START/COMMIT/LOCK...
        if (preg_match('/^(SET|START\s+TRANSACTION|COMMIT|ROLLBACK|LOCK\s+TABLES|UNLOCK\s+TABLES)\b/i', $s)) {
            return true;
        }

        // ignorer CREATE/DROP/ALTER/RENAME/CREATE INDEX/etc.
        if (preg_match('/^(CREATE|DROP|ALTER|RENAME)\s+/i', $s)) {
            return true;
        }

        // ignorer INSERT qui ne concernent pas `analyses`
        if (preg_match('/^INSERT\s+INTO\s+`?analyses`?/i', $s)) {
            return false;
        }

        // Option: ignorer tout le reste (si le fichier ne doit servir qu’à `analyses`)
        // sinon retourne false pour exécuter aussi d’autres tables.
        return true;
    }

    private function splitSqlStatements(string $sql): array
    {
        $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);

        $stmts = [];
        $buffer = '';
        $inString = false;
        $stringChar = '';
        $escape = false;

        $len = strlen($sql);
        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            $buffer .= $ch;

            if ($inString) {
                if ($escape) {
                    $escape = false;
                } elseif ($ch === '\\') {
                    $escape = true;
                } elseif ($ch === $stringChar) {
                    $inString = false;
                    $stringChar = '';
                }
                continue;
            }

            // -- commentaire ligne
            if ($ch === '-' && $i + 1 < $len && $sql[$i+1] === '-') {
                $buffer = substr($buffer, 0, -1);
                $i++;
                while ($i + 1 < $len && $sql[$i+1] !== "\n") { $i++; }
                continue;
            }

            // /* ... */ commentaire bloc
            if ($ch === '/' && $i + 1 < $len && $sql[$i+1] === '*') {
                $buffer = substr($buffer, 0, -1);
                $i += 2;
                while ($i < $len) {
                    if ($sql[$i] === '*' && $i + 1 < $len && $sql[$i+1] === '/') { $i++; break; }
                    $i++;
                }
                continue;
            }

            if ($ch === "'" || $ch === '"') {
                $inString = true;
                $stringChar = $ch;
                continue;
            }

            if ($ch === ';') {
                $stmt = trim($buffer);
                if ($stmt !== ';' && $stmt !== '') {
                    $stmts[] = rtrim($stmt, ';');
                }
                $buffer = '';
            }
        }

        $tail = trim($buffer);
        if ($tail !== '') {
            $stmts[] = $tail;
        }

        return array_values(array_filter(array_map('trim', $stmts), fn ($s) => $s !== ''));
    }

    private function countAnalysesRows(string $stmt): int
    {
        $norm = ltrim($stmt);
        if (!preg_match('/^INSERT\s+INTO\s+`?analyses`?/i', $norm)) {
            return 0;
        }

        if (!preg_match('/\bVALUES\b(.*)\z/si', $stmt, $m)) {
            return 1;
        }

        $valuesPart = $m[1];
        $count = 0; $depth = 0; $inString = false; $stringChar = '';

        for ($i = 0, $len = strlen($valuesPart); $i < $len; $i++) {
            $ch = $valuesPart[$i];

            if ($inString) {
                if ($ch === '\\') { $i++; continue; }
                if ($ch === $stringChar) { $inString = false; $stringChar = ''; }
                continue;
            }

            if ($ch === "'" || $ch === '"') {
                $inString = true;
                $stringChar = $ch;
                continue;
            }

            if ($ch === '(') {
                if ($depth === 0) $count++;
                $depth++;
            } elseif ($ch === ')') {
                if ($depth > 0) $depth--;
            }
        }

        return max(1, $count);
    }
}
