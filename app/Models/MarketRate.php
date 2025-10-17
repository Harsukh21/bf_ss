<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class MarketRate extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'market_rates'; // Default table, will be changed dynamically

    protected $fillable = [
        'exMarketId',
        'marketName',
        'runners',
        'inplay',
        'isCompleted',
    ];

    protected $casts = [
        'runners' => 'json',
        'inplay' => 'boolean',
        'isCompleted' => 'boolean',
    ];

    /**
     * Set the table name dynamically based on event ID
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Get market rates for a specific event
     */
    public static function forEvent($exEventId)
    {
        $tableName = "market_rates_{$exEventId}";
        
        // Check if table exists
        $tableExists = DB::select("SELECT to_regclass('public.{$tableName}') as table_name");
        
        if (!$tableExists[0]->table_name) {
            return collect([]); // Return empty collection if table doesn't exist
        }

        $model = new static();
        $model->setTable($tableName);
        
        return $model->newQuery();
    }

    /**
     * Get all available event tables
     */
    public static function getAvailableEventTables()
    {
        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name LIKE 'market_rates_%'
            ORDER BY table_name
        ");
        
        $events = [];
        foreach ($tables as $table) {
            // Extract event ID from table name
            $eventId = str_replace('market_rates_', '', $table->table_name);
            $events[] = $eventId;
        }
        
        return $events;
    }

    /**
     * Check if market rates table exists for an event
     */
    public static function tableExistsForEvent($exEventId)
    {
        $tableName = "market_rates_{$exEventId}";
        $tableExists = DB::select("SELECT to_regclass('public.{$tableName}') as table_name");
        return !is_null($tableExists[0]->table_name);
    }

    /**
     * Get market rate count for an event
     */
    public static function getCountForEvent($exEventId)
    {
        if (!self::tableExistsForEvent($exEventId)) {
            return 0;
        }

        $tableName = "market_rates_{$exEventId}";
        $result = DB::select("SELECT COUNT(*) as count FROM {$tableName}");
        return $result[0]->count ?? 0;
    }

    // Scope for inplay market rates
    public function scopeInplay($query)
    {
        return $query->where('inplay', true);
    }

    // Scope for completed market rates
    public function scopeCompleted($query)
    {
        return $query->where('isCompleted', true);
    }

    // Scope for specific market
    public function scopeByMarket($query, $marketName)
    {
        return $query->where('marketName', $marketName);
    }

    // Accessor for formatted market name
    public function getFormattedMarketNameAttribute()
    {
        return $this->marketName ?? 'Unknown Market';
    }
}
