<?php

namespace Test\PHPImageResize;

use PHPImageResize\Resizer;
use PHPImageResize\ImagePath;
use PHPImageResize\Configuration;

date_default_timezone_set('Europe/Berlin');

class ResizerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testNecessaryCollaboration() {
        $resizer = new Resizer('anyNonPathObject');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOptionalCollaboration() {
        $resizer = new Resizer(new ImagePath(''), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('PHPImageResize\Resizer', new Resizer(new ImagePath(''), new Configuration()));
        $this->assertInstanceOf('PHPImageResize\Resizer', new Resizer(new ImagePath('')));
    }

    public function testObtainLocallyCachedFilePath() {
        $configuration = new Configuration(array('width' => 800, 'height' => 600));
        $imagePath = new ImagePath('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $resizer = new Resizer($imagePath, $configuration);

        $stub = $this->getMockBuilder('PHPImageResize\FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');

        $stub->method('file_exists')
            ->willReturn(true);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testLocallyCachedFilePathFail() {
        $configuration = new Configuration(array('width' => 800, 'height' => 600));
        $imagePath = new ImagePath('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $resizer = new Resizer($imagePath, $configuration);

        $stub = $this->getMockBuilder('PHPImageResize\FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);

        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testCreateNewPath() {
        $resizer = new Resizer(new ImagePath('http://martinfowler.com/mf.jpg?query=hello&s=fowler'));
    }

}
