<?php declare(strict_types=1);

use App\DB\Storage\ArquivoStorage;
use PHPUnit\Framework\TestCase;

class ArquivoStorageTest extends TestCase
{
    public function testAdicionarArquivoRetornoValido(): void
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

        $arquivoMock = $this->getMockBuilder(ArquivoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ArquivoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($arquivoMock, $pdoMock);

        $result = $arquivoMock->adicionarArquivo('file_name', 'urlThumbFinal', 'urlFinal', 'dataComentario', 1);

        $this->assertEquals(1, $result);
    }

    public function testVerArquivoDoDiarioRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object) []);

        $arquivoMock = $this->getMockBuilder(ArquivoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ArquivoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($arquivoMock, $pdoMock);

        $arquivo = $arquivoMock->verArquivoDoDiario(1);

        $this->assertInstanceOf(stdClass::class, $arquivo);
    }

    public function testRemoverArquivoDoComentarioRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $arquivoMock = $this->getMockBuilder(ArquivoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ArquivoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($arquivoMock, $pdoMock);

        $arquivo = $arquivoMock->removerArquivoDoComentario(1);

        $this->assertEquals(null, $arquivo);
    }

    public function testVerArquivoPorIdRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object) []);

        $arquivoMock = $this->getMockBuilder(ArquivoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ArquivoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($arquivoMock, $pdoMock);

        $arquivo = $arquivoMock->verArquivoPorId(1);

        $this->assertInstanceOf(stdClass::class, $arquivo);
    }

    public function testVerArquivoPorAlunoRetornoValido(): void
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

        $enderecoMock = $this->getMockBuilder(ArquivoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ArquivoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $estados = $enderecoMock->verArquivoPorAluno(1);

        $this->assertIsArray($estados);
    }

    public function testVerArquivoPorProfessorRetornoValido(): void
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

        $enderecoMock = $this->getMockBuilder(ArquivoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ArquivoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $estados = $enderecoMock->verArquivoPorProfessor(1);

        $this->assertIsArray($estados);
    }
}
