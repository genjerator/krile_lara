<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasUuids;

    protected $fillable = [
        'external_id',
        'name',
        'category',
        'street',
        'postal_code',
        'city',
        'phone',
        'website',
        'email',
        'source_url',
        'scraped_at',
    ];

    protected $casts = [
        'scraped_at' => 'datetime',
    ];

    public function emailSends(): HasMany
    {
        return $this->hasMany(CompanyEmailSend::class);
    }

    public function lastEmailSend(): HasOne
    {
        return $this->hasOne(CompanyEmailSend::class)->latestOfMany();
    }

    public function scopeWhereNeedsEmail($query, $templateId = null)
    {
        return $query->whereNotNull('email')
            ->whereDoesntHave('emailSends', function ($q) use ($templateId) {
                $q->where('status', 'sent');
                if ($templateId) {
                    $q->where('email_template_id', $templateId);
                }
            });
    }

    public function scopeWhereHasReceivedEmail($query, $templateId = null)
    {
        return $query->whereHas('emailSends', function ($q) use ($templateId) {
            $q->where('status', 'sent');
            if ($templateId) {
                $q->where('email_template_id', $templateId);
            }
        });
    }

    public function getEmailVariables(): array
    {
        return [
            'company_name' => $this->name,
            'category' => $this->category,
            'city' => $this->city,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'website' => $this->website,
            'email' => $this->email,
        ];
    }

    public function hasReceivedTemplate(string $templateId): bool
    {
        return $this->emailSends()
            ->where('email_template_id', $templateId)
            ->where('status', 'sent')
            ->exists();
    }

    public function getLastEmailStatus(): ?string
    {
        return $this->lastEmailSend?->status;
    }
}
