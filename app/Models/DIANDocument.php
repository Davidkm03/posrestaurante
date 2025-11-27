<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DIANDocument extends Model
{
    use HasFactory;

    protected $table = 'dian_documents';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_type',
        'cufe',
        'uuid',
        'qr_code',
        'xml_content',
        'pdf_path',
        'status',
        'dian_response',
        'sent_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
