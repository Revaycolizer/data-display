<?php

use PHPUnit\Framework\TestCase;
use Revaycolizer\DataDisplay\DataDisplay;
use App\Types\DataSourceType;
class DummyEntity {}

class DataDisplayTest extends TestCase
{
    public function testCanBeCreated()
    {
        $dataDisplay = DataDisplay::create(null, DummyEntity::class,DataSourceType::CLASSES);
        $this->assertInstanceOf(DataDisplay::class, $dataDisplay);
    }

}
