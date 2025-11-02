<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RecreateMarketRatesTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market-rates:recreate {--sql-file=data.sql : Path to SQL file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all dynamic market rates tables and recreate them from SQL file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sqlFilePath = base_path($this->option('sql-file'));
        
        if (!File::exists($sqlFilePath)) {
            $this->error("SQL file not found: {$sqlFilePath}");
            return 1;
        }

        $this->info('Starting recreation of market rates tables...');
        
        // Step 1: Get all existing market_rates tables
        $this->info('Step 1: Finding existing market_rates tables...');
        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name LIKE 'market_rates_%'
            ORDER BY table_name
        ");
        
        if (count($tables) > 0) {
            $this->info('Found ' . count($tables) . ' existing tables to drop.');
            
            // Step 2: Drop all existing tables and sequences
            $this->info('Step 2: Dropping existing tables and sequences...');
            DB::beginTransaction();
            
            try {
                foreach ($tables as $table) {
                    $tableName = $table->table_name;
                    $this->line("  Dropping table: {$tableName}");
                    
                    // Drop sequence if exists
                    DB::statement("DROP SEQUENCE IF EXISTS {$tableName}_id_seq CASCADE");
                    
                    // Drop table
                    DB::statement("DROP TABLE IF EXISTS \"{$tableName}\" CASCADE");
                }
                
                DB::commit();
                $this->info('All existing tables dropped successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Error dropping tables: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('No existing market_rates tables found.');
        }

        // Step 3: Read and execute SQL file
        $this->info('Step 3: Reading SQL file and recreating tables...');
        
        try {
            $sql = File::get($sqlFilePath);
            
            // Extract all market_rates table blocks using a more robust pattern
            // Split the SQL file by finding DROP TABLE statements
            $this->info('Extracting market_rates table definitions from SQL file...');
            
            // Find all DROP TABLE statements for market_rates
            preg_match_all('/^DROP TABLE IF EXISTS "market_rates_[^"]+";/m', $sql, $dropMatches, PREG_OFFSET_CAPTURE);
            
            if (empty($dropMatches[0])) {
                // Fallback: execute entire SQL file
                $this->info('No market_rates tables found. Executing entire SQL file directly...');
                DB::unprepared($sql);
            } else {
                $this->info('Found ' . count($dropMatches[0]) . ' market_rates table definitions.');
                
                $successCount = 0;
                $errorCount = 0;
                
                // Process each table separately (no transaction to avoid rollback on partial failure)
                for ($i = 0; $i < count($dropMatches[0]); $i++) {
                    $startPos = $dropMatches[0][$i][1];
                    $endPos = ($i < count($dropMatches[0]) - 1) 
                        ? $dropMatches[0][$i + 1][1] 
                        : strlen($sql);
                    
                    $tableSql = substr($sql, $startPos, $endPos - $startPos);
                    $tableSql = trim($tableSql);
                    
                    // Remove trailing semicolon if present to avoid syntax errors
                    $tableSql = rtrim($tableSql, ';');
                    
                    // Extract table name
                    $tableName = null;
                    if (preg_match('/DROP TABLE IF EXISTS "([^"]+)"/', $tableSql, $tableMatch)) {
                        $tableName = $tableMatch[1];
                    }
                    
                    try {
                        DB::beginTransaction();
                        DB::unprepared($tableSql);
                        DB::commit();
                        
                        $successCount++;
                        
                        if ($tableName) {
                            $this->line("  ✓ Created table: {$tableName}");
                        } else {
                            $this->line("  ✓ Processed table block " . ($i + 1));
                        }
                        
                        if (($i + 1) % 5 == 0) {
                            $this->info("  Progress: " . ($i + 1) . " / " . count($dropMatches[0]) . " tables");
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $errorCount++;
                        
                        // Show detailed error for debugging
                        $errorMsg = $e->getMessage();
                        $this->warn("  ⚠ Error processing table " . ($i + 1) . ($tableName ? " ({$tableName})" : "") . ": " . substr($errorMsg, 0, 150));
                        
                        // If it's a syntax error, show more context
                        if (strpos($errorMsg, 'unterminated') !== false || strpos($errorMsg, 'syntax') !== false) {
                            $this->warn("     SQL preview: " . substr($tableSql, 0, 200) . "...");
                        }
                    }
                }
                
                $this->info("Processed {$successCount} tables successfully, {$errorCount} errors.");
            }
            
            // Verify tables were created
            $newTables = DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name LIKE 'market_rates_%'
                ORDER BY table_name
            ");
            
            $this->info('Created ' . count($newTables) . ' market_rates tables.');
            $this->info('Market rates tables recreation completed successfully!');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error executing SQL file: ' . $e->getMessage());
            $this->error('Stack trace: ' . substr($e->getTraceAsString(), 0, 300));
            return 1;
        }
    }
}

