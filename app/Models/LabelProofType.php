<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class LabelProofType extends Model {
    use SoftDeletes;
    protected $fillable = ['label_id','name','slug','description','is_active','created_by'];
    protected $casts = ['is_active' => 'boolean'];
    protected static function booted(): void {
        static::saving(fn($m) => $m->slug = Str::slug($m->name));
    }
    public function label() { return $this->belongsTo(Label::class); }
    public function proofs() { return $this->hasMany(LabelProof::class, 'proof_type_id'); }
}
