<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait Boosted
{
    /**
     * List of boosts the campaign is receiving
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CampaignBoost, $this>
     */
    public function boosts(): HasMany
    {
        return $this->hasMany('App\Models\CampaignBoost', 'campaign_id', 'id')
            ->with('user:id,name,pledge');
    }

    /**
     * Determine if the campaign is boosted
     */
    public function boosted(bool $superboosted = false): bool
    {
        return true;
    }

    /**
     * Determine if a campaign is superboosted
     */
    public function superboosted(): bool
    {
        return $this->boosted(true);
    }

    public function legacyBoosted(): bool
    {
        return true;
    }

    /**
     * Determine if a campaign is premium
     */
    public function premium(): bool
    {
        return true;
    }

    /**
     * Determine if a campaign is boosted by a wyvern
     */
    public function isWyvern(): bool
    {
        return true;
    }

    /**
     * Determine if a campaign is boosted by an elemental
     */
    public function isElemental(): bool
    {
        return true;
    }
}
