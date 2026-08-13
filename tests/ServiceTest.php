<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Artisaninweb\SoapWrapper\Service;

class ServiceTest extends TestCase
{
    public function testDefaultOptionsDoNotContainNullValues(): void
    {
        $service = new Service();

        $options = $service->getOptions();

        $this->assertSame(false, $options['trace']);
        $this->assertSame(WSDL_CACHE_NONE, $options['cache_wsdl']);
        $this->assertSame([], $options['classmap']);
        $this->assertArrayNotHasKey('local_cert', $options);
    }

    public function testWsdlGetterAndSetter(): void
    {
        $service = new Service();
        $service->wsdl('https://example.com/service.wsdl');

        $this->assertSame('https://example.com/service.wsdl', $service->getWsdl());
    }

    public function testTraceGetterAndSetter(): void
    {
        $service = new Service();
        $service->trace(true);

        $this->assertTrue($service->getTrace());
        $this->assertTrue($service->getOptions()['trace']);
    }

    public function testCacheGetterAndSetter(): void
    {
        $service = new Service();
        $service->cache(WSDL_CACHE_DISK);

        $this->assertSame(WSDL_CACHE_DISK, $service->getCache());
        $this->assertSame(WSDL_CACHE_DISK, $service->getOptions()['cache_wsdl']);
    }

    public function testClassmapIsKeyedByClassBasename(): void
    {
        $service = new Service();
        $service->classmap(['App\\Soap\\Response\\GetConversionAmountResponse']);

        $this->assertSame(
            ['GetConversionAmountResponse' => 'App\\Soap\\Response\\GetConversionAmountResponse'],
            $service->getClassmap()
        );
    }

    public function testCertificateIsOnlySetWhenTruthy(): void
    {
        $service = new Service();
        $service->certificate('');

        $this->assertArrayNotHasKey('local_cert', $service->getOptions());

        $service->certificate('/path/to/cert.pem');

        $this->assertSame('/path/to/cert.pem', $service->getOptions()['local_cert']);
    }

    public function testCustomOptionsAreMergedOnTopOfDefaults(): void
    {
        $service = new Service();
        $service->options(['connection_timeout' => 30, 'trace' => true]);

        $options = $service->getOptions();

        $this->assertSame(30, $options['connection_timeout']);
        $this->assertTrue($options['trace']);
    }

    public function testHeaderAddsSoapHeader(): void
    {
        $service = new Service();
        $service->header('http://example.com/ns', 'AuthHeader', ['token' => 'abc']);

        $headers = $service->getHeaders();

        $this->assertCount(1, $headers);
        $this->assertInstanceOf(\SoapHeader::class, $headers[0]);
    }
}
