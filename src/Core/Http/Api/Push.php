<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Push API domain (`v2.push.*`) — app push CONFIG endpoints (get/set which
 * push events are delivered to your callback URL, and reconcile lost push
 * messages). This is NOT the webhook-receiving side of Push (signature
 * verification, event parsing for inbound callbacks) — that lives under
 * `Http\Push\*` as a separate concern.
 *
 * Every endpoint in this domain signs at the **Public** level (no
 * `access_token`/`shop_id` at all) — these are app-wide settings, not
 * scoped to a shop or merchant.
 *
 * @see .shopee-docs/API Reference/Push
 * @see .docs/api/Push
 */
final class Push
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Push/get_app_push_config.md
     */
    public function getAppPushConfig(array $params = []): array
    {
        return $this->client->public('GET', '/api/v2/push/get_app_push_config', $params);
    }

    /**
     * @param array<string, mixed> $params callback_url, set_push_config_on,
     *     set_push_config_off, blocked_shop_id_list (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Push/set_app_push_config.md
     */
    public function setAppPushConfig(array $params): array
    {
        return $this->client->public('POST', '/api/v2/push/set_app_push_config', $params);
    }

    /**
     * Returns the earliest 100 push messages lost within the last 3 days and
     * not yet confirmed consumed. Use `last_message_id` from the response
     * with {@see confirmConsumedLostPushMessage()} once processed, and check
     * `has_next_page` to know whether to call again.
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Push/get_lost_push_message.md
     */
    public function getLostPushMessage(array $params = []): array
    {
        return $this->client->public('GET', '/api/v2/push/get_lost_push_message', $params);
    }

    /**
     * @param array<string, mixed> $params last_message_id (required) — from
     *     {@see getLostPushMessage()}'s response.
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Push/confirm_consumed_lost_push_message.md
     */
    public function confirmConsumedLostPushMessage(array $params): array
    {
        return $this->client->public('POST', '/api/v2/push/confirm_consumed_lost_push_message', $params);
    }
}
