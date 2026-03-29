<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LabelNote extends Model {
    use SoftDeletes;
    protected $fillable = ['label_id','note_date','origin','agent','user_name','whatsapp_group','note','title','content','created_by'];
    protected $casts = ['note_date' => 'date'];
    public function label() { return $this->belongsTo(Label::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
