<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'context' => 'array',
        'extra' => 'array',
        'unix_time' => 'integer',
        'level' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'channel',
        'level',
        'level_name',
        'unix_time',
        'datetime',
        'context',
        'extra',
    ];

    /**
     * Get the level badge color based on log level.
     *
     * @return string
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->level_name) {
            'DEBUG' => 'gray',
            'INFO' => 'blue',
            'NOTICE' => 'indigo',
            'WARNING' => 'yellow',
            'ERROR' => 'orange',
            'CRITICAL' => 'red',
            'ALERT' => 'red',
            'EMERGENCY' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the level icon based on log level.
     *
     * @return string
     */
    public function getLevelIconAttribute(): string
    {
        return match ($this->level_name) {
            'DEBUG' => 'fas-bug',
            'INFO' => 'fas-info-circle',
            'NOTICE' => 'fas-bell',
            'WARNING' => 'fas-exclamation-triangle',
            'ERROR' => 'fas-times-circle',
            'CRITICAL' => 'fas-exclamation-circle',
            'ALERT' => 'fas-exclamation-circle',
            'EMERGENCY' => 'fas-radiation',
            default => 'fas-file-alt',
        };
    }

    /**
     * Format context for display.
     *
     * @return string|null
     */
    public function getFormattedContextAttribute(): ?string
    {
        if (empty($this->context)) {
            return null;
        }

        return json_encode($this->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Scope a query to only include logs of a certain level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLevel($query, string $level)
    {
        return $query->where('level_name', strtoupper($level));
    }

    /**
     * Scope a query to only include logs of a certain channel.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $channel
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope a query to search in messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('message', 'LIKE', '%' . $search . '%');
    }
}

