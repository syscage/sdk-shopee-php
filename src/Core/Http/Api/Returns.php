<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Returns\Proof;
use Syscage\Sdk\Shopee\Core\Http\Api\Returns\Shipping;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Returns API domain (`v2.returns.*`) — buyer return/refund requests a
 * seller must respond to. Core negotiation workflow only; two genuinely
 * distinct sub-concerns are split out even though they share the same
 * `return_sn` key space, mirroring Logistics' workflow-based split:
 *
 * - {@see Proof} (via {@see proof()}) — evidence images: query/upload
 *   proof, and the domain's one real file-upload endpoint (`convertImage`).
 * - {@see Shipping} (via {@see shipping()}) — reverse logistics for
 *   TW/BR seller-arranged returns: carrier lookup, tracking, shipping proof.
 *
 * Typical flow: {@see getReturnList()} → {@see getReturnDetail()} → then
 * one of: {@see confirm()} (accept as requested), {@see offer()} /
 * {@see acceptOffer()} (negotiate new terms — a proposal/counter-accept
 * pair, not "seller proposes, buyer silently accepts": either party can
 * call `acceptOffer` on the other's latest proposal), or {@see dispute()}
 * (contest the return; needs a `dispute_reason_id` from
 * {@see getReturnDisputeReason()} and evidence via `proof()->convertImage()`).
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/Returns
 * @see .docs/api/Returns
 */
final class Returns
{
    private ?Proof $proof = null;
    private ?Shipping $shipping = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function proof(): Proof
    {
        return $this->proof ??= new Proof($this->client);
    }

    public function shipping(): Shipping
    {
        return $this->shipping ??= new Shipping($this->client);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size (both
     *     required); create_time_from/to, update_time_from/to, status,
     *     negotiation_status, seller_proof_status,
     *     seller_compensation_status (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/get_return_list.md
     */
    public function getReturnList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/get_return_list', $params);
    }

    /**
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/get_return_detail.md
     */
    public function getReturnDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/get_return_detail', $params);
    }

    /**
     * Accept the return/refund at its currently requested terms — a simple
     * accept, distinct from {@see offer()} (counter-propose new terms).
     *
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/confirm.md
     */
    public function confirm(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/confirm', $params);
    }

    /**
     * Formally contest the return for Shopee to adjudicate — an adversarial
     * escalation, distinct from {@see offer()}'s negotiation. Only allowed
     * while `return_status` is REQUESTED/PROCESSING/ACCEPTED.
     * `dispute_reason_id` comes from {@see getReturnDisputeReason()};
     * `image_list[].image_url` values should come from
     * `proof()->convertImage()`.
     *
     * @param array<string, mixed> $params return_sn, email,
     *     dispute_reason_id (all required); image_list, dispute_text_reason
     *     (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/dispute.md
     */
    public function dispute(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/dispute', $params);
    }

    /**
     * Withdraws a *compensation* dispute only (`return_status` ACCEPTED and
     * `compensation_status` COMPENSATION_REQUESTED) — cannot cancel a
     * normal {@see dispute()}.
     *
     * @param array<string, mixed> $params return_sn, email (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/cancel_dispute.md
     */
    public function cancelDispute(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/cancel_dispute', $params);
    }

    /**
     * Propose new terms for this return. Check
     * {@see getAvailableSolutions()} first for which solutions/amounts are
     * eligible. Pairs with {@see acceptOffer()}, which accepts whichever
     * party's latest proposal is outstanding.
     *
     * @param array<string, mixed> $params return_sn, proposed_solution
     *     (both required); proposed_adjusted_refund_amount (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/offer.md
     */
    public function offer(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/offer', $params);
    }

    /**
     * Accepts the other party's latest outstanding proposal from
     * {@see offer()} (Shopee rejects this if there is no counter-proposal
     * to accept, or if you try to accept your own offer).
     *
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/accept_offer.md
     */
    public function acceptOffer(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/accept_offer', $params);
    }

    /**
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/get_available_solutions.md
     */
    public function getAvailableSolutions(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/get_available_solutions', $params);
    }

    /**
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/get_return_dispute_reason.md
     */
    public function getReturnDisputeReason(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/get_return_dispute_reason', $params);
    }
}
