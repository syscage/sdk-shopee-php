<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Ads;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ads\GmsCampaign;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class GmsCampaignTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'checkEligibility' => ['checkEligibility', 'GET', '/api/v2/ads/check_create_gms_product_campaign_eligibility', []],
            'create' => ['create', 'POST', '/api/v2/ads/create_gms_product_campaign', ['start_date' => '2026-01-01', 'daily_budget' => 10.0]],
            'edit' => ['edit', 'POST', '/api/v2/ads/edit_gms_product_campaign', ['edit_action' => 'pause']],
            'editItems' => ['editItems', 'POST', '/api/v2/ads/edit_gms_item_product_campaign', ['edit_action' => 'add', 'item_id_list' => [1]]],
            'getCampaignPerformance' => ['getCampaignPerformance', 'POST', '/api/v2/ads/get_gms_campaign_performance', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31']],
            'getItemPerformance' => ['getItemPerformance', 'POST', '/api/v2/ads/get_gms_item_performance', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31']],
            'listDeletedItems' => ['listDeletedItems', 'POST', '/api/v2/ads/list_gms_user_deleted_item', []],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new GmsCampaign($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
