<?php

/*
    Testcase for Test Index
*/

use PHPUnit\Framework\TestCase;

/**
 * This class holds your test methods
 */
class IndexTest extends Asatru\Testing\Test
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $response = $this->request(Asatru\Testing\Test::REQUEST_GET, '/')->getResponse();

        $this->assertInstanceOf(Asatru\View\ViewHandler::class, $response);
        $this->assertStringContainsString('<h1>' . __('app.welcome') . '</h1>', $response->out(true));
    }

    /**
     * @return void
     */
    public function testError404()
    {
        $checkUrl = '/does/not/exist';

        $response = $this->request(Asatru\Testing\Test::REQUEST_GET, $checkUrl)->getResponse();

        $this->assertInstanceOf(Asatru\View\ViewHandler::class, $response);
        $this->assertStringContainsString('<h1>Error 404</h1>', $response->out(true));
        $this->assertStringContainsString('<p>The requested resource ' . env('APP_URL') . $checkUrl . ' was not found on the server.</p>', $response->out(true));
    }
}
    