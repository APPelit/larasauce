<?php

namespace Tests\HelperTests;

use APPelit\LaraSauce\Util\UuidGen;
use Ramsey\Uuid\UuidInterface;
use Tests\TestCase;

class UuidGenTest extends TestCase
{
    public function testCanGenerateUuid1()
    {
        $this->assertInstanceOf(UuidInterface::class, UuidGen::generate(1));
    }

    public function testCanGenerateUuid3()
    {
        $this->assertInstanceOf(UuidInterface::class, UuidGen::generate(3, '6ba7b810-9dad-11d1-80b4-00c04fd430c8', 'www.example.org'));
    }

    public function testGeneratedUuid3Matches()
    {
        $this->assertEquals('0012416f-9eec-3ed4-a8b0-3bceecde1cd9', UuidGen::generate(3, '6ba7b810-9dad-11d1-80b4-00c04fd430c8', 'www.example.org')->toString());
    }

    public function testThrowsOnMissingUuid3Arguments()
    {
        try {
            UuidGen::generate(3);
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\ArgumentCountError::class, $t);
        }
    }

    public function testCanGenerateUuid4()
    {
        $this->assertInstanceOf(UuidInterface::class, UuidGen::generate(4));
    }

    public function testCanGenerateUuid5()
    {
        $this->assertInstanceOf(UuidInterface::class, UuidGen::generate(5, '6ba7b810-9dad-11d1-80b4-00c04fd430c8', 'www.example.org'));
    }

    public function testGeneratedUuid5Matches()
    {
        $this->assertEquals('74738ff5-5367-5958-9aee-98fffdcd1876', UuidGen::generate(5, '6ba7b810-9dad-11d1-80b4-00c04fd430c8', 'www.example.org'));
    }

    public function testThrowsOnMissingUuid5Arguments()
    {
        try {
            UuidGen::generate(5);
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\ArgumentCountError::class, $t);
        }
    }

    public function testThrowsOnInvalidUuidVersion()
    {
        try {
            UuidGen::generate(2);
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $t);
        }
    }
}