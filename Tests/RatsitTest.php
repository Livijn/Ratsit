<?php
namespace livijn\Ratsit\Tests;

use livijn\Ratsit\Ratsit;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class RatsitTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFindPersonBySocialSecurityNumber()
    {
        $ratsit = new Ratsit('xxx');

        var_dump($ratsit->findAmountOfDogsBySocialSecurityNumber('xxx'));
    }
}
