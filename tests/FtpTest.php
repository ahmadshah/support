<?php namespace Orchestra\Support\Tests;

class FtpTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Support\Ftp
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub1_ftp_';

		$this->stub = new \Orchestra\Support\Ftp(array(
			'host'     => 'sftp://localhost:22',
			'user'     => 'foo',
			'password' => 'foobar',
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = 'ftp_';
	}

	/**
	 * Test instance of Orchestra\Support\Ftp.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfFTP()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub1_ftp_';

		$stub = \Orchestra\Support\Ftp::make(array(
			'stream' => new StreamStub,
		));

		$this->assertInstanceOf('\Orchestra\Support\Ftp', $stub);
		$this->assertTrue($stub->connected());
		$stub->close();
		$this->assertFalse($stub->connected());

		$refl     = new \ReflectionObject($this->stub);
		$host     = $refl->getProperty('host');
		$port     = $refl->getProperty('port');
		$ssl      = $refl->getProperty('ssl');
		$user     = $refl->getProperty('user');
		$password = $refl->getProperty('password');

		$host->setAccessible(true);
		$port->setAccessible(true);
		$ssl->setAccessible(true);
		$user->setAccessible(true);
		$password->setAccessible(true);

		$this->assertEquals('localhost', $host->getValue($this->stub));
		$this->assertTrue($ssl->getValue($this->stub));
		$this->assertEquals('22', $port->getValue($this->stub));
		$this->assertEquals('foo', $user->getValue($this->stub));
		$this->assertEquals('foobar', $password->getValue($this->stub));
	}

	/**
	 * Test Orchestra\Support\Ftp::connect() method successful.
	 *
	 * @test
	 * @group support
	 */
	public function testConnectMethodSuccessful()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub1_ftp_';

		$this->assertFalse($this->stub->connected());
		$this->assertTrue($this->stub->connect());
		$this->assertTrue($this->stub->connected());

		$stub = new \Orchestra\Support\Ftp(array(
			'host'     => 'ftp://localhost:21',
			'user'     => 'foo',
			'password' => 'foobar',
		));

		$this->assertFalse($stub->connected());
		$this->assertTrue($stub->connect());
		$this->assertTrue($stub->connected());
	}

	/**
	 * Test Orchestra\Support\Ftp::connect() method with ftp_ssl_connect() throws exception.
	 *
	 * @expectedException \Orchestra\Support\Ftp\ServerException
	 * @group support
	 */
	public function testConnectMethodSFTPConnectThrowsException()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub2_ftp_';

		$stub = new \Orchestra\Support\Ftp(array(
			'host'     => 'sftp://localhost:22',
			'user'     => 'foo',
			'password' => 'foobar',
		));

		$stub->connect();
	}

	/**
	 * Test Orchestra\Support\Ftp::connect() method with ftp_connect() throws exception.
	 *
	 * @expectedException \Orchestra\Support\Ftp\ServerException
	 * @group support
	 */
	public function testConnectMethodFTPConnectThrowsException()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub2_ftp_';

		$stub = new \Orchestra\Support\Ftp(array(
			'host'     => 'ftp://localhost:21',
			'user'     => 'foo',
			'password' => 'foobar',
		));

		$stub->connect();
	}

	/**
	 * Test Orchestra\Support\Ftp::connect() method with ftp_login() throws exception.
	 *
	 * @expectedException \Orchestra\Support\Ftp\ServerException
	 * @group support
	 */
	public function testConnectMethodFTPLoginThrowsException()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub3_ftp_';

		$stub = new \Orchestra\Support\Ftp(array(
			'host'     => 'ftp://localhost:21',
			'user'     => 'foo',
			'password' => 'foobar',
		));

		$stub->connect();
	}

	/**
	 * Test Orchestra\Support\Ftp\Facade methods.
	 *
	 * @test
	 * @group support
	 */
	public function testFTPFacadeMethodsSuccessful()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub1_ftp_';

		$this->stub->connect();

		$this->assertEquals('/var/www/', $this->stub->pwd());
		$this->assertTrue($this->stub->cd('/var/www/'));
		$this->assertTrue($this->stub->get('/var/www/home.php', '/var/www/home.php'));
		$this->assertTrue($this->stub->put('/var/www/home.php', '/var/www/home.php'));
		$this->assertTrue($this->stub->rename('/var/www/home.php', '/var/www/dashboard.php'));
		$this->assertTrue($this->stub->delete('/var/www/home.php'));
		$this->assertTrue($this->stub->chmod('/var/www/index.php', 755));
		$this->assertEquals(array('foo.php', 'foobar.php'), $this->stub->ls('/var/www/foo/'));
		$this->assertTrue($this->stub->mkdir('/var/www/orchestra'));
		$this->assertTrue($this->stub->rmdir('/var/www/orchestra'));
	}

	/**
	 * Test Orchestra\Support\Ftp\Facade method throws Exception.
	 *
	 * @expectedException \Orchestra\Support\Ftp\RuntimeException
	 * @group support
	 */
	public function testFTPFacadeThrowsException()
	{
		\Orchestra\Support\Ftp\Facade::$prefix = '\Orchestra\Support\Tests\stub1_ftp_';
		
		\Orchestra\Support\Ftp\Facade::fire('invalid_method', array());
	}
}

class StreamStub {}

function stub1_ftp_ssl_connect() {
	return new StreamStub;
}

function stub1_ftp_connect() {
	return new StreamStub;
}

function stub1_ftp_login() {
	return true;
}

function stub1_ftp_close() {
	return true;
}

function stub1_ftp_pasv() {
	return true;
}

function stub1_ftp_pwd() {
	return '/var/www/';
}

function stub1_ftp_systype() {
	return 'unix';
}

function stub1_ftp_chdir() {
	return true;
}

function stub1_ftp_get() {
	return true;
}

function stub1_ftp_put() {
	return true;
}

function stub1_ftp_rename() {
	return true;
}

function stub1_ftp_delete() {
	return true;
}

function stub1_ftp_chmod() {
	return true;
}

function stub1_ftp_nlist() {
	return array('foo.php', 'foobar.php');
}

function stub1_ftp_mkdir() {
	return true;
}

function stub1_ftp_rmdir() {
	return true;
}

function stub2_ftp_ssl_connect() {
	return false;
}

function stub2_ftp_connect() {
	return false;
}

function stub3_ftp_ssl_connect() {
	return new StreamStub;
}

function stub3_ftp_connect() {
	return new StreamStub;
}

function stub3_ftp_login() {
	return false;
}