<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $guarded = ['id'];

    public function scopeSearch($query, $term)
    {
        if ($term) {
            $term = "%{$term}%";

            $query->where('type', 'like', $term)
                ->orWhere('description', 'like', $term)
                ->orWhereHas('user', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
