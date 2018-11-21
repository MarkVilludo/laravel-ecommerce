<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ActivityLogsResource;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    public function scopeGetRecentActivity($query, $numberOfItems)
    {
        $activityLogs = $query->where('user_id', auth()->user()->id)
                                ->orderBy('created_at', 'desc')
                                ->paginate($numberOfItems);

        return ActivityLogsResource::collection($activityLogs);
    }
}
