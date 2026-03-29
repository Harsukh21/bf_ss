<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LabelProof extends Model {
    use SoftDeletes;
    protected $fillable = [
        'label_id','proof_type_id','whitelabel_id','whatsapp_group',
        'agent_name','user_name','amount','sport_id','event_name',
        'market_name','profit_loss','proof_date','navigation','images',
        'navigation2','navigation2_images','title','description',
        'file_link','status','created_by',
    ];
    protected $casts = [
        'images'            => 'array',
        'navigation2_images'=> 'array',
        'amount'            => 'decimal:2',
        'profit_loss'       => 'decimal:2',
        'proof_date'        => 'date',
    ];
    public function label()      { return $this->belongsTo(Label::class); }
    public function proofType()  { return $this->belongsTo(LabelProofType::class, 'proof_type_id'); }
    public function whitelabel() { return $this->belongsTo(LabelWhitelabel::class, 'whitelabel_id'); }
    public function sport()      { return $this->belongsTo(LabelSport::class, 'sport_id'); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
}
