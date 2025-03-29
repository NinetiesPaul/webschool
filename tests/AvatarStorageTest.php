<?php declare(strict_types=1);

use App\DB\Storage\AvatarStorage;
use PHPUnit\Framework\TestCase;

class AvatarStorageTest extends TestCase
{
    public function testInserirUsuarioNaAvatarRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $avatarMock = $this->getMockBuilder(AvatarStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AvatarStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($avatarMock, $pdoMock);

        $result = $avatarMock->inserirUsuarioNaAvatar(1);

        $this->assertEquals(null, $result);
    }

    public function testAtualizarAvatarRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $avatarMock = $this->getMockBuilder(AvatarStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AvatarStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($avatarMock, $pdoMock);

        $avatar = $avatarMock->atualizarAvatar('urlFinal', 'urlThumbFinal', 1);

        $this->assertEquals(null, $avatar);
    }

    // todo: encontrar um jeito de mocar chamadas
    public function testAtualizarAvatarExcecao(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object) [ 'endereco_thumb' => 'endereco_thumb_mock', 'endereco' => 'endereco_mock' ]);

        $pdoMock->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->exactly(2))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(true, true);

        $avatarMock = $this->getMockBuilder(AvatarStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AvatarStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($avatarMock, $pdoMock);

        $avatar = $avatarMock->atualizarAvatar('endereco_mock', 'endereco_thumb_mock', 1);

        $this->assertEquals(null, $avatar);
    }

    public function testVerAvatarRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn(
                (object)['id' => 1, 'nome' => 'Turma A', 'ano' => 2021],
                );

        $avatarMock = $this->getMockBuilder(AvatarStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AvatarStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);

        $dbProperty->setValue($avatarMock, $pdoMock);

        $avatar = $avatarMock->verAvatar(1);

        $this->assertInstanceOf(stdClass::class, $avatar);
    }
}
