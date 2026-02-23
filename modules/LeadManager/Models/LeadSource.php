<?php

namespace Modules\LeadManager\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    protected $table = 'lead_sources';

    protected $fillable = ['company_id', 'name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public static function optionsForCompany(int $companyId): array
    {
        $predefined = Lead::sources();
        $custom = static::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
        return $predefined + $custom;
    }

    public static function findOrCreateCustom(int $companyId, string $name): string
    {
        $name = trim($name);
        if (empty($name)) return Lead::SOURCE_MANUAL;

        $existing = static::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('name', $name)
            ->first();
        if ($existing) return $name;

        static::withoutGlobalScopes()->create([
            'company_id' => $companyId,
            'name' => $name,
        ]);
        return $name;
    }
}
