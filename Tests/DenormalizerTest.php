<?php

declare(strict_types=1);

namespace livijn\Ratsit\Tests;

use livijn\Ratsit\Denormalizer;
use livijn\Ratsit\Exception\InvalidJsonException;
use livijn\Ratsit\Model\Address;
use livijn\Ratsit\Model\Person;
use livijn\Ratsit\Model\SearchResult;
use PHPUnit\Framework\TestCase;

class DenormalizerTest extends TestCase
{
    /**
     * @var Denormalizer
     */
    private $denormalizer;

    public function setUp() : void
    {
        $this->denormalizer = new Denormalizer();
    }

    /** @test It should denormalize Person Information */
    public function it_should_denormalize_person_information()
    {
        $person = $this->denormalizer->denormalizerPersonInformation(
            json_decode(file_get_contents(__DIR__ . '/personInformation.json'), true)
        );

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('Per Fredrik', $person->getFirstName());
        $this->assertEquals('Per', $person->getGivenName());
        $this->assertEquals('Hedlund', $person->getSurName());
        $this->assertEquals('H', $person->getMiddleName());
        $this->assertEquals('Hedlund', $person->getLastName());
        $this->assertInstanceOf(Address::class, $person->getAddress());
        $this->assertEquals('Åkervägen 2 lgh 1202', $person->getAddress()->getStreet());
        $this->assertEquals('co', $person->getAddress()->getCo());
        $this->assertEquals('26051', $person->getAddress()->getPostalCode());
        $this->assertEquals('EKEBY', $person->getAddress()->getCity());
        $this->assertEquals(9, count($person->getPhoneNumbers()));
        $this->assertEquals('070-4107021', $person->getPhoneNumbers()[0]);
    }

    /** @test It should denormalize Person Search */
    public function it_should_denormalize_person_search()
    {
        $persons = $this->denormalizer->denormalizerPersonSearch(
            json_decode(file_get_contents(__DIR__ . '/personSearch.json'), true)
        );

        $this->assertInstanceOf(SearchResult::class, $persons);
        $this->assertEquals(1, count($persons));
        /** @var Person $person */
        $person = $persons->first();
        $this->assertEquals('194107081111', $person->getSocialSecurityNumber());
        $this->assertEquals('Per Fredrik', $person->getFirstName());
        $this->assertEquals('Per', $person->getGivenName());
        $this->assertNull($person->getSurName());
        $this->assertNull($person->getMiddleName());
        $this->assertEquals('Hedlund', $person->getLastName());
        $this->assertInstanceOf(Address::class, $person->getAddress());
        $this->assertEquals('Åkervägen 2 lgh 1202', $person->getAddress()->getStreet());
        $this->assertNull($person->getAddress()->getCo());
        $this->assertEquals('26051', $person->getAddress()->getPostalCode());
        $this->assertEquals('EKEBY', $person->getAddress()->getCity());
        $this->assertEquals(9, count($person->getPhoneNumbers()));
        $this->assertEquals('070-4107021', $person->getPhoneNumbers()[0]);
        $this->assertNull($person->getBirthDate());
    }

    /** @test It should throw an exception if person information format is not correct */
    public function it_should_throw_an_exception_if_person_information_format_is_not_correct()
    {
        $this->expectException(InvalidJsonException::class);
        $this->expectExceptionMessage('Provided json is invalid');
        $this->denormalizer->denormalizerPersonInformation($this->invalidPersonProvider());
    }

    /** @test It should throw an exception if person search format is not correct */
    public function it_should_throw_an_exception_if_person_search_format_is_not_correct()
    {
        $this->expectException(InvalidJsonException::class);
        $this->expectExceptionMessage('Provided json is invalid');
        $this->denormalizer->denormalizerPersonSearch($this->invalidPersonProvider());
    }

    /**
     * @return array
     */
    public function invalidPersonProvider()
    {
        return [
            [['']],
            [null],
            [[]],
            [['foo' => 'bar']],
        ];
    }
}
