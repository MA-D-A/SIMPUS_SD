<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('kategori')]
#[Fillable(['nama'])]
class Kategori extends Model
{
    use HasFactory;

    /** @return HasMany<Buku, $this> */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class);
    }
}

