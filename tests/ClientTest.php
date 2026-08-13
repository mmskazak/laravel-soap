<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Artisaninweb\SoapWrapper\Client;

class ClientTest extends TestCase
{
    private function makeClient(array $headers = []): Client
    {
        return new Client(null, [
            'location' => 'http://example.com/soap',
            'uri'      => 'http://example.com/soap',
        ], $headers);
    }

    public function testClientCanBeConstructedInNonWsdlMode(): void
    {
        $client = $this->makeClient();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testLocationReturnsClientForChaining(): void
    {
        $client = $this->makeClient();

        $this->assertSame($client, $client->location('http://example.com/other'));
    }

    public function testCookieReturnsClientForChaining(): void
    {
        $client = $this->makeClient();

        $this->assertSame($client, $client->cookie('name', 'value'));
    }

    public function testCallDelegatesToNamedMethod(): void
    {
        $client = $this->makeClient();

        $this->assertSame($client, $client->call('location', ['http://example.com/new']));
    }
}
