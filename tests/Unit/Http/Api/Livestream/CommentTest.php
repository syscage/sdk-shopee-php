<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Livestream;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream\Comment;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class CommentTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'postComment' => ['postComment', 'POST', '/api/v2/livestream/post_comment', ['session_id' => 1, 'content' => 'Welcome!']],
            'getLatestCommentList' => ['getLatestCommentList', 'GET', '/api/v2/livestream/get_latest_comment_list', ['session_id' => 1]],
            'banUserComment' => ['banUserComment', 'POST', '/api/v2/livestream/ban_user_comment', ['session_id' => 1, 'ban_user_id' => 555]],
            'unbanUserComment' => ['unbanUserComment', 'POST', '/api/v2/livestream/unban_user_comment', ['session_id' => 1, 'unban_user_id' => 555]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        (new Comment($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
