<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $guarded = ['id'];

    // ini function search yang ada di livewire
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function domicile()
    {
        return $this->belongsTo(Domicile::class);
    }

    public function parentMember()
    {
        return $this->belongsTo(Member::class, 'parent_member_id');
    }
   
}
