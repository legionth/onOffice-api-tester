<?php

use PHPUnit\Framework\TestCase;
use jr\ooapi\CredentialStorage;
use jr\ooapi\dataObjects\Credentials;
use jr\ooapi\Config;
use jr\ooapi\EncrypterOpenSSL;

class CredentialStorageTest extends TestCase
{
    const PASSWORD = 'test';

    /** @var string */
    private $testDir = '';

    protected function setUp(): void
    {
        $config = new Config('config/ooapi.ini');
        $this->testDir = $config->getCredentialDir().'/test-ooapi-'.rand(0, 1000000000);

        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testDir.'/ooapi_credentials'))
        {
            unlink($this->testDir.'/ooapi_credentials');
        }

        rmdir($this->testDir);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(CredentialStorage::class, new CredentialStorage(''));
    }

    public function testSave(): void
    {
        $credentials = $this->createCredentials();
        $storage = new CredentialStorage($this->testDir);
        $storage->activateEncryption(new EncrypterOpenSSL(self::PASSWORD));
        $storage->save($credentials);

        $fileContentEncrypted = file_get_contents($this->testDir);

        $this->assertFileExists($this->testDir.'/ooapi_credentials');
        $this->assertStringNotContainsString('testToken', $fileContentEncrypted);
        $this->assertStringNotContainsString('testSecret', $fileContentEncrypted);
    }

    public function testLoad(): void
    {
        $credentials = $this->createCredentials();
        $storage = new CredentialStorage($this->testDir);
        $storage->activateEncryption(new EncrypterOpenSSL(self::PASSWORD));
        $storage->save($credentials);
        $credentials = $storage->load();

        $this->assertEquals('testToken', $credentials->getToken());
        $this->assertEquals('testSecret', $credentials->getSecret());
    }

    public function testFailedLoading(): void
    {
        $storage = new CredentialStorage($this->testDir);

        $this->expectException('jr\ooapi\MissingCredentialFileException');
        $storage->load();
    }

    private function createCredentials(): Credentials
    {
        return new Credentials('testToken', 'testSecret');
    }
}
