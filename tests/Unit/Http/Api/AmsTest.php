<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class AmsTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testSubAccessorsAreCached(): void
    {
        $ams = new Ams($this->makeClient([]));

        $this->assertSame($ams->openCampaign(), $ams->openCampaign());
        $this->assertSame($ams->targetedCampaign(), $ams->targetedCampaign());
        $this->assertSame($ams->affiliate(), $ams->affiliate());
        $this->assertSame($ams->performance(), $ams->performance());
    }
}
