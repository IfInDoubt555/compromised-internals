<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppBootsTest extends TestCase
{
    public function test_blog_page_loads(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
    }
}