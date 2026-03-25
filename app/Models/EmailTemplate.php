<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'subject',
        'body_html',
        'body_text',
        'is_active',
        'available_variables',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'available_variables' => 'array',
    ];

    public function emailSends(): HasMany
    {
        return $this->hasMany(CompanyEmailSend::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function render(array $data): string
    {
        $html = $this->body_html;

        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value ?? '', $html);
        }

        return $html;
    }

    public function renderSubject(array $data): string
    {
        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value ?? '', $subject);
        }

        return $subject;
    }

    public function getAvailableVariables(): array
    {
        return $this->available_variables ?? [
            'company_name',
            'category',
            'city',
            'street',
            'postal_code',
            'phone',
            'website',
            'email',
        ];
    }

    public function getSentCount(): int
    {
        return $this->emailSends()->where('status', 'sent')->count();
    }

    public function getFailedCount(): int
    {
        return $this->emailSends()->where('status', 'failed')->count();
    }
}
