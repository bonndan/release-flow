<?php
namespace bonndan\ReleaseFlow\Test;

use bonndan\ReleaseFlow\ComposerFile;
use bonndan\ReleaseFlow\Version;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;


/**
 * Tests the ComposerFile
 * 
 */
class ComposerFileTest extends PHPUnit_Framework_TestCase
{

    /**
     * system under test
     * 
     * @var ComposerFile
     */
    private $file;

    /**
     * Test setup
     * 
     */
    public function setUp()
    {
        parent::setUp();
        copy(__DIR__ . '/testdata/composer.json', sys_get_temp_dir() . '/composer.json');

        $file = new \SplFileObject(sys_get_temp_dir() . '/composer.json');
        $this->file = new ComposerFile($file);
    }

    /**
     * Ensures that the version string is replaced.
     */
    public function testSetVersion()
    {
        $newVersion = '1.0.1';
        $this->file->setVersion(new Version($newVersion));

        $contents = file_get_contents(sys_get_temp_dir() . '/composer.json');
        $this->assertContains('"version": "1.0.1"', $contents);
    }


    /**
     * Ensures that the current version can be read.
     */
    public function testGetCurrentVersion()
    {
        $this->assertEquals("0.2.0", $this->file->getCurrentVersion());
    }

    public function testGetCurrentVersionFails()
    {
        copy(__DIR__ . '/testdata/composer_noversion.json', sys_get_temp_dir() . '/composer.json');
        $file = new \SplFileObject(sys_get_temp_dir() . '/composer.json');
        $this->file = new ComposerFile($file);

        $this->assertNull($this->file->getCurrentVersion());
    }

    /**
     * 
     */
    public function testSavePreventsReplacementOfEmptyProperties()
    {
        $tmpFile = tempnam(sys_get_temp_dir(), '');
        copy(__DIR__ . '/testdata/empty.json', $tmpFile);
        $this->file->setComposerFile(new \SplFileObject($tmpFile));
        $this->file->setVersion(new Version('1.2.3'));

        $contents = file_get_contents($tmpFile);
        $this->assertNotContains('_empty_', $contents);
    }

    public function testGetProjectName()
    {
        $this->assertEquals('bonndan/ReleaseManager', $this->file->getProjectName());
    }

    public function testIndent()
    {
        $this->assertEquals(<<<JSON
{
   "key1": "val1",
   "key2": [
      "item1",
      2,
      "item[{2"
   ],
   "key3": {
      "bool": false,
      "int": 17,
      "float": 17.9
   }
}
JSON
                , ComposerFile::format('{"key1":"val1","key2":["item1",2,"item[{2"],"key3":{"bool":false,"int":    17    ,"float":17.9}}'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalid()
    {
        ComposerFile::format('{invalidKey: false}');
    }

}
