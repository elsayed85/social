<?php

namespace App\Traits;

trait Publishable
{
    public function publishNow()
    {
        $this->update(['published_at' => now(), 'deleted_at' => null]);
        return $this;
    }

    public function makeAsDraft()
    {
        $this->update(['published_at' => null]);
        return $this;
    }

    public function scopePublished($query)
    {
        return $query->withoutTrashed()->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->withoutTrashed()->whereNotNull('published_at');
    }

    public function scopeTrashed($query)
    {
        return $this->withTrashed();
    }

    public function isPublished()
    {
        return !is_null($this->published_at) && !$this->isTrashed();
    }

    public function isDraft()
    {
        return is_null($this->published_at) && !$this->isTrashed();;
    }

    public function isTrashed()
    {
        return !is_null($this->deleted_at);
    }
}
