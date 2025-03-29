<?php declare(strict_types=1);

use App\DB\Storage\ProfessorStorage;
use PHPUnit\Framework\TestCase;

class ProfessorStorageTest extends TestCase
{
    public function testVerProfessoresRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $professores = $professorMock->verProfessores();

        $this->assertIsArray($professores);
    }

    public function testVerProfessorRetornoValido(): void
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

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $professor = $professorMock->verProfessor(1);

        $this->assertInstanceOf(stdClass::class, $professor);
    }

    public function testDesativarProfessorRetornoValido(): void
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
                (object)['is_deleted' => 1],
            );

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $resultado = $professorMock->desativarProfessor(1);

        $this->assertEquals(null, $resultado);
    }

    public function testDesativarProfessorRetornoExcecao(): void
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

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $this->expectException(Exception::class);
        $professorMock->desativarProfessor(1);
    }

    public function testDesativarProfessorRetornoUsuarioDesconhecido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $this->expectException(Exception::class);
        $professorMock->desativarProfessor(1);
    }

    /*public function testVerProfessorDoResponsavelRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);

        $dbProperty->setValue($professorMock, $pdoMock);

        $turmas = $professorMock->verProfessorsDoResponsavel(1);

        $this->assertIsArray($turmas);
    }*/

    public function testVerificarMateriaPorProfessorRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn(
                [
                    (object)['id' => 1, 'nome' => 'Turma A', 'ano' => 2021],
                    (object)['id' => 2, 'nome' => 'Turma B', 'ano' => 2022],
                ]
            );

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $resultado = $professorMock->verificarMateriaPorProfessor(1, 1, 'professor');

        $this->assertIsArray($resultado);
    }

    public function testRemoverProfessorPorMateriaRetornoValido()
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $result = $professorMock->removerProfessorPorMateria(1);

        $this->assertEquals(null, $result);
    }

    public function testRemoverProfessorPorMateriaExcecao()
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

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $this->expectException(Exception::class);

        $professorMock->removerProfessorPorMateria(1);
    }

    public function testVerProfessorPorMateriaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $professorMock = $this->getMockBuilder(ProfessorStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(ProfessorStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($professorMock, $pdoMock);

        $resultado = $professorMock->verProfessorPorMateria(1);

        $this->assertIsArray($resultado);
    }
}
