<?php

namespace App\Traits;

use App\Models\Posts\LoveReaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait Loveable
{
    public function LoveReactions()
    {
        return $this->hasMany(LoveReaction::class);
    }

    public function love($user = null)
    {
        $user_id = $user;
        if ($user instanceof Model) {
            $user_id = $user->id;
        } elseif (is_null($user)) {
            $user_id = auth()->id();
        }

        return $this->LoveReactions()->create(['user_id' => $user_id]);
    }

    public function unLove($user = null)
    {
        $user_id = $user;
        if ($user instanceof Model) {
            $user_id = $user->id;
        } elseif (is_null($user)) {
            $user_id = auth()->id();
        }

        return $this->LoveReactions()->ofUser($user_id)->delete();
    }

    public function toggleLove($user = null)
    {
        $user_id = $user;
        if ($user instanceof Model) {
            $user_id = $user->id;
        } elseif (is_null($user)) {
            $user_id = auth()->id();
        }

        return $this->loved($user_id) ? $this->unlove($user_id) : $this->love($user_id);
    }

    public function removeLoves()
    {
        return $this->LoveReactions()->delete();
    }

    public function loved($user =  null)
    {
        $user_id = $user;
        if ($user instanceof Model) {
            $user_id = $user->id;
        } elseif (is_null($user)) {
            $user_id = auth()->id();
        }

        if ($this->relationLoaded('LoveReactions')) {
            return $this->LoveReactions->contains("user_id", $user_id);
        }

        return $this->LoveReactions()->ofUser($user_id)->exists();
    }

    public function loveCount()
    {
        if ($this->relationLoaded('LoveReactions')) {
            return $this->LoveReactions->count();
        }
        return $this->LoveReactions()->count();
    }

    public function collectLovers()
    {
        if ($this->relationLoaded('LoveReactions')) {
            return $this->LoveReactions->pluck(['user']);
        }
        return $this->LoveReactions()->with(['user'])->get()->pluck('user');
    }

    public function scopeWhereLovedBy($query, $user)
    {
        $user_id = $user;
        if ($user instanceof Model) {
            $user_id = $user->id;
        }

        return $this->whereHas('LoveReactions', function ($query) use ($user_id) {
            return $query->ofUser($user_id);
        });
    }

    public function scopeWhereLovedByAuthenticatedUser($query)
    {
        return $this->whereHas('LoveReactions', function ($query) {
            return $query->ofUser(auth()->id());
        });
    }

    public function scopeOrderByLikesCount($query, $dir = 'desc')
    {
        return $query->withCount('LoveReactions')->orderBy('love_reactions_count', $dir);
    }
}
