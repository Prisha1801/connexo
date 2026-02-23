<?php

namespace Modules\LeadManager\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    protected $table = 'lead_stages';

    protected $fillable = ['company_id', 'name', 'sort_order'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public static function forCompany(int $companyId): array
    {
        $stages = static::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        if ($stages->isEmpty()) {
            static::seedDefaults($companyId);
            $stages = static::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }
        return $stages->pluck('name', 'name')->toArray();
    }

    public static function allForCompany(int $companyId)
    {
        $stages = static::forCompany($companyId);
        return static::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public static function seedDefaults(int $companyId): void
    {
        $defaults = [
            ['name' => 'New', 'sort_order' => 10],
            ['name' => 'Contacted', 'sort_order' => 20],
            ['name' => 'Interested', 'sort_order' => 30],
            ['name' => 'Qualified', 'sort_order' => 40],
            ['name' => 'Won', 'sort_order' => 50],
            ['name' => 'Lost', 'sort_order' => 60],
        ];
        foreach ($defaults as $d) {
            static::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $companyId, 'name' => $d['name']],
                ['sort_order' => $d['sort_order']]
            );
        }
    }

    public static function validForCompany(int $companyId, string $stage): bool
    {
        $stages = static::forCompany($companyId);
        return isset($stages[$stage]);
    }
}
