<?php

namespace Modules\LeadManager\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Contacts\Models\Contact;

class Lead extends Model
{
    public const SOURCE_CRM = 'crm';
    public const SOURCE_FACEBOOK = 'facebook';
    public const SOURCE_WHATSAPP_CAMPAIGN = 'whatsapp_campaign';
    public const SOURCE_INSTAGRAM = 'instagram';
    public const SOURCE_GOOGLE = 'google';
    public const SOURCE_WEBSITE = 'website';
    public const SOURCE_WHATSAPP = 'whatsapp';
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_CSV_IMPORT = 'csv_import';

    public const STAGE_NEW = 'New';
    public const STAGE_CONTACTED = 'Contacted';
    public const STAGE_INTERESTED = 'Interested';
    public const STAGE_QUALIFIED = 'Qualified';
    public const STAGE_WON = 'Won';
    public const STAGE_LOST = 'Lost';

    protected $table = 'leads';

    protected $fillable = [
        'company_id',
        'contact_id',
        'source',
        'stage',
        'qualified',
        'lost_reason',
        'notes',
        'tags',
        'location',
        'user_id',
        'notifications',
        'first_contacted_at',
        'next_follow_up_at',
    ];

    protected $casts = [
        'notifications' => 'integer',
        'qualified' => 'boolean',
        'first_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public static function sources(): array
    {
        return [
            self::SOURCE_CRM => __('CRM'),
            self::SOURCE_FACEBOOK => __('Facebook / Meta Ads'),
            self::SOURCE_WHATSAPP_CAMPAIGN => __('WhatsApp Campaign'),
            self::SOURCE_INSTAGRAM => __('Instagram'),
            self::SOURCE_GOOGLE => __('Google Ads'),
            self::SOURCE_WEBSITE => __('Website Form'),
            self::SOURCE_WHATSAPP => __('WhatsApp'),
            self::SOURCE_MANUAL => __('Manual Entry'),
            self::SOURCE_CSV_IMPORT => __('CSV Import'),
        ];
    }

    public static function stages(): array
    {
        return [
            self::STAGE_NEW => __('New'),
            self::STAGE_CONTACTED => __('Contacted'),
            self::STAGE_INTERESTED => __('Interested'),
            self::STAGE_QUALIFIED => __('Qualified'),
            self::STAGE_WON => __('Won'),
            self::STAGE_LOST => __('Lost'),
        ];
    }

    public static function stagesForCompany(?int $companyId): array
    {
        if ($companyId) {
            return \Modules\LeadManager\Models\LeadStage::forCompany($companyId);
        }
        return self::stages();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function isWon(): bool
    {
        return $this->stage === self::STAGE_WON;
    }

    public function isLost(): bool
    {
        return $this->stage === self::STAGE_LOST;
    }

    public function isClosed(): bool
    {
        return $this->isWon() || $this->isLost();
    }

    public function getSourceLabelAttribute(): string
    {
        $sources = self::sources();
        return $sources[$this->source] ?? (string) ($this->source ?? __('Not specified'));
    }
}
