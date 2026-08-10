<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Ams\Affiliate;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams\OpenCampaign;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams\Performance;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams\TargetedCampaign;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Ams API domain (`v2.ams.*`) — Affiliate Marketing Solution: shop-run
 * affiliate commission campaigns. A pure facade over four sub-accessors,
 * each with a genuinely distinct key space/lifecycle — no endpoint stays
 * on a bare `Ams` class:
 *
 * - {@see OpenCampaign} (`->openCampaign()`) — a shop-wide, opt-in
 *   commission mechanism: any affiliate can promote any product the seller
 *   has added, at a set rate. Products are added/edited/removed
 *   individually (synchronous, ≤50 ids) or in bulk across the *entire*
 *   catalog (async, returns a `task_id` polled via its own
 *   `getBatchTaskResult()`).
 * - {@see TargetedCampaign} (`->targetedCampaign()`) — the seller invites
 *   *specific* affiliates to promote *specific* products at custom rates,
 *   with a full CRUD lifecycle (Upcoming/Ongoing/Ended/Cancelled/Draft/
 *   Terminating/Terminated/Paused) that Open Campaign never has.
 * - {@see Affiliate} (`->affiliate()`) — affiliate lookup/discovery,
 *   independent of either campaign type's lifecycle.
 * - {@see Performance} (`->performance()`) — cross-cutting reporting that
 *   spans both campaign types (shop/product/affiliate/content-level
 *   metrics, raw conversion/validation rows). Each campaign type's own
 *   performance endpoint stays on that campaign's own sub-accessor
 *   instead, since it's consumed as "that feature's performance tab," not
 *   a cross-cutting report.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/Ams
 * @see .docs/api/Ams
 */
final class Ams
{
    private ?OpenCampaign $openCampaign = null;
    private ?TargetedCampaign $targetedCampaign = null;
    private ?Affiliate $affiliate = null;
    private ?Performance $performance = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function openCampaign(): OpenCampaign
    {
        return $this->openCampaign ??= new OpenCampaign($this->client);
    }

    public function targetedCampaign(): TargetedCampaign
    {
        return $this->targetedCampaign ??= new TargetedCampaign($this->client);
    }

    public function affiliate(): Affiliate
    {
        return $this->affiliate ??= new Affiliate($this->client);
    }

    public function performance(): Performance
    {
        return $this->performance ??= new Performance($this->client);
    }
}
