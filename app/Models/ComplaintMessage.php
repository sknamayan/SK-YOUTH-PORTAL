<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class ComplaintMessage extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'consultation_request_id',
        'sender_id',
        'body',
        'attachment_path',
    ];
 
    /**
     * Get the consultation request/thread this message belongs to.
     */
    public function consultationRequest(): BelongsTo
    {
        return $this->belongsTo(ConsultationRequest::class, 'consultation_request_id');
    }
 
    /**
     * Get the user who sent this message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
