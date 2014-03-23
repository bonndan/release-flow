<?php
namespace bonndan\ReleaseFlow\Test\Unit;

use bonndan\ReleaseFlow\Version;

/**
 * Test the config value object.
 *
 *
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInitial()
    {
        $version = new Version(Version::INITIAL);
        $this->assertTrue($version->isInitial());
    }

    public function testIsNotInitial()
    {
        $version = new Version('1.2.3');
        $this->assertFalse($version->isInitial());
    }

    public function testCreateInitialVersion()
    {
        $version = Version::createInitialVersion();
        $this->assertInstanceOf("bonndan\ReleaseFlow\Version", $version);
    }

    /**
     * @dataProvider versionProvider
     */
    public function testGetDifferenceType(Version $higherVersion, $expected)
    {
        $version = new Version('1.0.0');
        $result = $version->getDifferenceType($higherVersion);
        $this->assertEquals($expected, $result);
    }

    public function versionProvider()
    {
        return array(
            array(new Version('1.0.0'), null),
            array(new Version('1.0.1'), Version::TYPE_PATCH),
            array(new Version('1.1.1'), Version::TYPE_MINOR),
            array(new Version('2.1.3'), Version::TYPE_MAJOR),
        );
    }
    
    /**
     * @dataProvider getTagData
     */
    public function testIsValid($tag, $result)
    {
        $valid = Version::isValid($tag);
        $this->assertEquals($result, $valid);
    }

    public function getTagData()
    {
        $simpleRegEx = '\d+';
        $semanticRegEx = '\d+\.\d+\.\d+';
        return array(
            array('1', false, $simpleRegEx),
            array('23', false, $simpleRegEx),
            array('3d', false, $simpleRegEx),
            array('v_23', false, $simpleRegEx, 'v_'),
            array('v-23',  false, $simpleRegEx, 'v_'),
            array('v_3d',  false, $simpleRegEx, 'v_'),
            array('1.0.3', true, $semanticRegEx ),
            array('3.0.3.7', false, $semanticRegEx),
            array('3.b.3',  false, $semanticRegEx),
            array('dev_3.3.3', false, $semanticRegEx, 'dev_'),
            array('dev_3.3.3.7', false, $semanticRegEx, 'dev_')
        );
    }
}
