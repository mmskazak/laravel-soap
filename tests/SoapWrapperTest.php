<?php

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Artisaninweb\SoapWrapper\Service;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Artisaninweb\SoapWrapper\Exceptions\ServiceNotFound;
use Artisaninweb\SoapWrapper\Exceptions\ServiceAlreadyExists;
use Artisaninweb\SoapWrapper\Exceptions\ServiceMethodNotExists;

class SoapWrapperTest extends TestCase
{
    public function testAddRegistersAService(): void
    {
        $wrapper = new SoapWrapper();

        $wrapper->add('Currency', function (Service $service) {
            $service->wsdl('https://example.com/service.wsdl');
        });

        $this->assertTrue($wrapper->has('Currency'));
    }

    public function testAddThrowsWhenServiceAlreadyExists(): void
    {
        $wrapper = new SoapWrapper();
        $wrapper->add('Currency', function (Service $service) {
            $service->wsdl('https://example.com/service.wsdl');
        });

        $this->expectException(ServiceAlreadyExists::class);

        $wrapper->add('Currency', function (Service $service) {
            $service->wsdl('https://example.com/other.wsdl');
        });
    }

    public function testAddByArrayRegistersServices(): void
    {
        $wrapper = new SoapWrapper();

        $wrapper->addByArray([
            'Currency' => [
                'wsdl'  => 'https://example.com/service.wsdl',
                'trace' => true,
            ],
        ]);

        $this->assertTrue($wrapper->has('Currency'));
    }

    public function testAddByArrayThrowsOnUnknownMethod(): void
    {
        $wrapper = new SoapWrapper();

        $this->expectException(ServiceMethodNotExists::class);

        $wrapper->addByArray([
            'Currency' => [
                'notAMethod' => true,
            ],
        ]);
    }

    public function testAddByArrayThrowsWhenServiceAlreadyExists(): void
    {
        $wrapper = new SoapWrapper();
        $wrapper->addByArray(['Currency' => ['wsdl' => 'https://example.com/service.wsdl']]);

        $this->expectException(ServiceAlreadyExists::class);

        $wrapper->addByArray(['Currency' => ['wsdl' => 'https://example.com/other.wsdl']]);
    }

    public function testClientThrowsWhenServiceNotFound(): void
    {
        $wrapper = new SoapWrapper();

        $this->expectException(ServiceNotFound::class);

        $wrapper->client('Unknown', function () {
        });
    }

    public function testCallThrowsOnInvalidCallFormat(): void
    {
        $wrapper = new SoapWrapper();

        $this->expectException(InvalidArgumentException::class);

        $wrapper->call('CurrencyWithoutMethod');
    }

    public function testHasReturnsFalseForUnknownService(): void
    {
        $wrapper = new SoapWrapper();

        $this->assertFalse($wrapper->has('Unknown'));
    }
}
