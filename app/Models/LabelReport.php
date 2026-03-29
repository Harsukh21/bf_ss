<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LabelReport extends Model {
    use SoftDeletes;
    protected $fillable = [
        'label_id','user_name','agent','origin','before_void_balance','after_void_balance',
        'catch_by','proof_type_id','proof_status','void_status','remark','originals',
        'title','report_type','content','file_link','report_date','created_by',
    ];
    protected $casts = [
        'report_date'        => 'date',
        'originals'          => 'array',
        'before_void_balance'=> 'decimal:2',
        'after_void_balance' => 'decimal:2',
    ];
    public function label()     { return $this->belongsTo(Label::class); }
    public function proofType() { return $this->belongsTo(LabelProofType::class, 'proof_type_id'); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
}
