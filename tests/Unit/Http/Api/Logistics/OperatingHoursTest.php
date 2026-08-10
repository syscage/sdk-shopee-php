<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Logistics;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\OperatingHours;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class OperatingHoursTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getOperatingHours' => ['getOperatingHours', 'GET', '/api/v2/logistics/get_operating_hours', []],
            'updateOperatingHours' => ['updateOperatingHours', 'POST', '/api/v2/logistics/update_operating_hours', ['regular_operating_hour' => []]],
            'getOperatingHourRestrictions' => ['getOperatingHourRestrictions', 'GET', '/api/v2/logistics/get_operating_hour_restrictions', []],
            'deleteSpecialOperatingHour' => ['deleteSpecialOperatingHour', 'POST', '/api/v2/logistics/delete_special_operating_hour', ['name' => 'x']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new OperatingHours($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
