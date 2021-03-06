<?php

namespace Kirby\Cms;

use PHPUnit\Framework\TestCase;

class UsersRoutesTest extends TestCase
{
    public function setUp(): void
    {
        $this->app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'users' => [
                [
                    'email' => 'admin@getkirby.com',
                ],
                [
                    'email' => 'editor@getkirby.com',
                ]
            ]
        ]);

        $this->app->impersonate('kirby');
    }

    public function testList()
    {
        $app = $this->app;

        $response = $app->api()->call('users');

        $this->assertEquals('admin@getkirby.com', $response['data'][0]['email']);
        $this->assertEquals('editor@getkirby.com', $response['data'][1]['email']);
    }

    public function testGet()
    {
        $app = $this->app;

        $response = $app->api()->call('users/admin@getkirby.com');

        $this->assertEquals('admin@getkirby.com', $response['data']['email']);
    }

    public function testSearchWithGetRequest()
    {
        $app = $this->app;

        $response = $app->api()->call('users/search', 'GET', [
            'query' => [
                'q' => 'editor'
            ]
        ]);

        $this->assertCount(1, $response['data']);
        $this->assertEquals('editor@getkirby.com', $response['data'][0]['email']);
    }

    public function testSearchWithPostRequest()
    {
        $app = $this->app;

        $response = $app->api()->call('users/search', 'POST', [
            'body' => [
                'search' => 'editor'
            ]
        ]);

        $this->assertCount(1, $response['data']);
        $this->assertEquals('editor@getkirby.com', $response['data'][0]['email']);
    }

    public function testFiles()
    {
        $app = $this->app->clone([
            'users' => [
                [
                    'email' => 'test@getkirby.com',
                    'files' => [
                        [
                            'filename' => 'a.jpg',
                        ],
                        [
                            'filename' => 'b.jpg',
                        ]
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $response = $app->api()->call('users/test@getkirby.com/files');

        $this->assertEquals('a.jpg', $response['data'][0]['filename']);
        $this->assertEquals('b.jpg', $response['data'][1]['filename']);
    }

    public function testFilesSorted()
    {
        $app = $this->app->clone([
            'users' => [
                [
                    'email' => 'test@getkirby.com',
                    'files' => [
                        [
                            'filename' => 'a.jpg',
                            'content'  => [
                                'sort' => 2
                            ]
                        ],
                        [
                            'filename' => 'b.jpg',
                            'content'  => [
                                'sort' => 1
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $response = $app->api()->call('users/test@getkirby.com/files');

        $this->assertEquals('b.jpg', $response['data'][0]['filename']);
        $this->assertEquals('a.jpg', $response['data'][1]['filename']);
    }

    public function testFile()
    {
        $app = $this->app->clone([
            'users' => [
                [
                    'email' => 'test@getkirby.com',
                    'files' => [
                        [
                            'filename' => 'a.jpg',
                        ]
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $response = $app->api()->call('users/test@getkirby.com/files/a.jpg');

        $this->assertEquals('a.jpg', $response['data']['filename']);
    }


    public function testAvatar()
    {
        $app = $this->app->clone([
            'users' => [
                [
                    'email' => 'test@getkirby.com',
                    'files' => [
                        [
                            'filename' => 'profile.jpg',
                            'template' => 'avatar'
                        ]
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $response = $app->api()->call('users/test@getkirby.com/avatar');

        $this->assertEquals('profile.jpg', $response['data']['filename']);
    }
}
