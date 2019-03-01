<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Storage;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * Tests File::getPath
     */
    public function testGetPath()
    {
        $file = new File(__FILE__);

        $this->assertEquals(__FILE__, $file->getPath());
    }

    /**
     * Tests File::read
     */
    public function testRead()
    {
        $file    = new File(__FILE__);
        $content = $file->read();

        $this->assertStringContainsString('<?php', $content);
    }

    /**
     * Tests File::read
     */
    public function testReadFail()
    {
        $this->expectException(\Exception::class);

        $file    = new File(__FILE__ . '.absent');
        $content = $file->read();

        $this->assertTrue(false);
    }

    /**
     * Tests File::write
     */
    public function testWrite()
    {
        $tmpDir = sys_get_temp_dir();
        $path   = tempnam($tmpDir, 'foo');
        $file   = new File($path);
        $file->write('foo');

        $this->assertEquals('foo', file_get_contents($path));
        $this->assertTrue(unlink($path));
    }

    /**
     * Tests File::write
     */
    public function testWriteFailNoDir()
    {
        $this->expectException(\Exception::class);

        $path   = __FILE__ . DIRECTORY_SEPARATOR . 'foo.txt';
        $file   = new File($path);
        $file->write('foo');

        $this->assertTrue(false);
    }

    /**
     * Tests File::write
     */
    public function testNoWritePermission()
    {
        $this->expectException(\Exception::class);

        $path = tempnam(sys_get_temp_dir(), 'noPermission');
        chmod($path, 0000);

        $file = new File($path);
        $file->write('test');
    }

    /**
     * Tests File::write
     */
    public function testCantCreateDirectory()
    {
        $this->expectException(\Exception::class);

        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('basedir');
        mkdir($baseDir, 0000);

        $path = $baseDir . '/foo/bar.txt';
        $file = new File($path);
        $file->write('test');
    }
}
