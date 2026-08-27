<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per completed AI Assistant turn -- the permanent audit trail of
 * who asked what, which tools Gemini called, whether each call was
 * permission-checked and allowed, and what actually happened. This is
 * separate from the live conversation state (kept in session) so a chat
 * transcript can be cleared without losing accountability for what the
 * assistant actually did.
 */
class AiAssistantLog extends Model
{
    protected $fillable = [
        'user_id',
        'interaction_ref',
        'prompt',
        'tool_calls',
        'tool_results',
        'response_text',
        'was_write_action',
        'was_allowed',
        'status',
        'ip_address',
    ];

    protected $casts = [
        'tool_calls' => 'array',
        'tool_results' => 'array',
        'was_write_action' => 'boolean',
        'was_allowed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
