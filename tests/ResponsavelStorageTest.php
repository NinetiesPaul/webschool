<?php declare(strict_types=1);

use App\DB\Storage\ResponsavelStorage;
use PHPUnit\Framework\TestCase;

class ResponsavelStorageTest extends TestCase
{
    public function testVerResponsaveisRetornoValido(): void
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

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $resultado = $responsavelMock->verResponsaveis();

        $this->assertIsArray($resultado);
    }

    public function testVerResponsavelRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object)['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $resultado = $responsavelMock->verResponsavel(1);

        $this->assertInstanceOf(stdClass::class, $resultado);
    }

    public function testAdicionarResponsavelRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('query')
            ->willReturn($stmtMock);

        $stmtMock->method('fetch')
            ->willReturn(false);

        $pdoMock
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock
            ->method('execute')
            ->willReturn(true);

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $resultado = $responsavelMock->adicionarResponsavel('email', 'nome', 'password', 'salt');

        $this->assertEquals(null, $resultado);
    }

    public function testAdicionarResponsavelUsuarioJaExiste(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('query')
            ->willReturn($stmtMock);

        $stmtMock->method('fetch')
            ->willReturn(true);

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $resultado = $responsavelMock->adicionarResponsavel('email', 'nome', 'password', 'salt');

        $this->assertEquals(false, $resultado);
    }

    public function testRemoverResponsavelRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock
            ->method('execute')
            ->willReturn(true);

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);


        $resultado = $responsavelMock->removerResponsavel(1, 1, 1, [ 'usuario' => (object) [ 'nome' => 'responsavel' ] ]);

        $this->assertEquals(null, $resultado);
    }

    public function testAdicionarAlunoPorResponsavelRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $resultado = $responsavelMock->adicionarAlunoPorResponsavel(1, 1);

        $this->assertEquals(null, $resultado);
    }

    public function testRemoverAlunoPorResponsavel(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $stmtMock->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $this->expectException(Exception::class);

        $responsavelMock->removerAlunoPorResponsavel(1);
    }

    public function testVerAlunosDoResponsavelRetornoValido(): void
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

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $turmas = $responsavelMock->verAlunosDoResponsavel(1);

        $this->assertIsArray($turmas);
    }

    public function testVerResponsaveisPeloAlunoRetornoValido(): void
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

        $responsavelMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($responsavelMock, $pdoMock);

        $resultado = $responsavelMock->verResponsaveisPeloAluno(1);

        $this->assertIsArray($resultado);
    }

    public function testDesativarResponsavelAtualizacaoFalhou(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn(
                (object)[ 'is_deleted' => 1 ],
            );

        $stmtMock->expects($this->exactly(2))
            ->method('rowCount')
            ->willReturnOnConsecutiveCalls(1, 0);  

        $professorMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $this->expectException(Exception::class);
        $professorMock->desativarResponsavel(1);
    }
    
    public function testDesativarResponsavelNaoEncontrado(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);  

        $professorMock = $this->getMockBuilder(ResponsavelStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ResponsavelStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $this->expectException(Exception::class);
        $professorMock->desativarResponsavel(1);
    }
}
