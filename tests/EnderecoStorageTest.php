<?php declare(strict_types=1);

use App\DB\Storage\EnderecoStorage;
use PHPUnit\Framework\TestCase;

class EnderecoStorageTest extends TestCase
{
    public function testInserirNotaRetornoValido(): void
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

        $enderecoMock = $this->getMockBuilder(EnderecoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(EnderecoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $result = $enderecoMock->inserirEndereco();

        $this->assertEquals(1, $result);
    }

    public function testAdicionarNotaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $enderecoMock = $this->getMockBuilder(EnderecoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(EnderecoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $result = $enderecoMock->atualizarEndereco('rua', '1', 'bairro', 'complemento', 'cidade', 'cep', 'estado', 'endereco');

        $this->assertEquals(null, $result);
    }

    public function testVerEnderecoRetornoValido(): void
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

        $enderecoMock = $this->getMockBuilder(EnderecoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(EnderecoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $endereco = $enderecoMock->verEndereco(1);

        $this->assertInstanceOf(stdClass::class, $endereco);
    }

    public function testPegarEstadoPeloEstadoRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn(
                (object) ['nome' => 'nome', 'sigla' => 'sigla' ],
                );

        $enderecoMock = $this->getMockBuilder(EnderecoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(EnderecoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $estado = $enderecoMock->pegarEstadoPeloEstado(1);

        $this->assertEquals('nome, sigla', $estado);
    }

    public function testPegarEstadosRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                (object)['id' => 1, 'nome' => 'Turma A', 'ano' => 2021],
                (object)['id' => 2, 'nome' => 'Turma B', 'ano' => 2022],
            ]);

        $enderecoMock = $this->getMockBuilder(EnderecoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(EnderecoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $estados = $enderecoMock->pegarEstados();

        $this->assertIsArray($estados);
    }
}
