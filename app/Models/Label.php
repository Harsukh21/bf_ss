<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Label extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Label $label) {
            $label->slug = Str::slug($label->name);
        });
    }

    public function whitelabels() { return $this->hasMany(LabelWhitelabel::class); }
    public function proofTypes()  { return $this->hasMany(LabelProofType::class); }
    public function sports()      { return $this->hasMany(LabelSport::class); }
    public function proofs()      { return $this->hasMany(LabelProof::class); }
    public function reports()     { return $this->hasMany(LabelReport::class); }
    public function notes()       { return $this->hasMany(LabelNote::class); }
}
