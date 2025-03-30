<?php declare(strict_types=1);

use App\DB\Storage\UsuarioStorage;
use PHPUnit\Framework\TestCase;

class UsuarioStorageTest extends TestCase
{
    public function testInserirUsuarioRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $pdoMock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(1);

        $usuarioMock = $this->getMockBuilder(UsuarioStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(UsuarioStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($usuarioMock, $pdoMock);

        $result = $usuarioMock->inserirUsuario('file_name', 'urlThumbFinal', 'urlFinal', 'dataComentario', 1);

        $this->assertEquals(1, $result);
    }

    public function testAlterarUsuarioRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $usuarioMock = $this->getMockBuilder(UsuarioStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct', 'loginTaken'])
            ->getMock();

        $reflection = new ReflectionClass(UsuarioStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($usuarioMock, $pdoMock);

        $usuarioMock->method('loginTaken')
            ->willReturn(false);

        $resultado = $usuarioMock->alterarUsuario(1, 'nome', 'email', 'password', 'salt', 'tipo', true, true);

        $this->assertIsBool($resultado);
    }

    public function testAlterarUsuarioRetornoFalso(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $usuarioMock = $this->getMockBuilder(UsuarioStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct', 'loginTaken'])
            ->getMock();

        $reflection = new ReflectionClass(UsuarioStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($usuarioMock, $pdoMock);

        $usuarioMock->method('loginTaken')
            ->willReturn(true);

        $resultado = $usuarioMock->alterarUsuario(1, 'nome', 'email', 'password', 'salt', 'tipo', true, true);

        $this->assertIsBool($resultado);
    }

    public function testLoginTakenRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object) []);

        $usuarioMock = $this->getMockBuilder(UsuarioStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(UsuarioStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($usuarioMock, $pdoMock);

        $usuario = $usuarioMock->loginTaken('login', 'tipo', true);

        $this->assertIsBool($usuario);
    }

    public function testVerificarUsuarioRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object) []);

        $usuarioMock = $this->getMockBuilder(UsuarioStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(UsuarioStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($usuarioMock, $pdoMock);

        $usuario = $usuarioMock->verificarUsuario('alias', 1, 'tipo', 'email');

        $this->assertInstanceOf(stdClass::class, $usuario);
    }
}
