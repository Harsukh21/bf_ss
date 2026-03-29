<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LabelWhitelabel extends Model {
    use SoftDeletes;
    protected $fillable = ['label_id','name','whatsapp_group','color','domain','logo_link','is_active','settings','created_by'];
    protected $casts = ['is_active' => 'boolean', 'settings' => 'array'];
    public function label() { return $this->belongsTo(Label::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
