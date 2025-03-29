<?php declare(strict_types=1);

use App\DB\Storage\MateriaStorage;
use PHPUnit\Framework\TestCase;

class MateriaStorageTest extends TestCase
{
    public function testVerMateriasRetornoValido(): void
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

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $materias = $materiaMock->verMaterias();

        $this->assertIsArray($materias);
    }

    public function testVerMateriaRetornoValido(): void
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

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $materiaMock->verMateria(1);
    }

    public function testAdicionarMateriaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $result = $materiaMock->adicionarMateria('string', 2000);

        $this->assertEquals(null, $result);
    }

    public function testAlterarMateriaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $result = $materiaMock->alterarMateria('string', 2000, 1);

        $this->assertEquals(null, $result);
    }

    public function testRemoverMateriaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);

        $dbProperty->setValue($materiaMock, $pdoMock);

        $result = $materiaMock->removerMateria(1);

        $this->assertEquals(null, $result);
    }

    public function testRemoverMateriaRetornoExcecao(): void
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

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $this->expectException(Exception::class);

        $materiaMock->removerMateria(1);
    }

    public function testVerMateriaPorProfessorPorTurmaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $materias = $materiaMock->verMateriaPorProfessorPorTurma(1);

        $this->assertIsArray($materias);
    }

    public function testVerMateriasDoProfessorRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $materias = $materiaMock->verMateriasDoProfessor(1);

        $this->assertIsArray($materias);
    }

    public function testVerMateriasDoProfessorAdminRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $materias = $materiaMock->verMateriasDoProfessorAdmin(1);

        $this->assertIsArray($materias);
    }

    public function testVerMateriaDoProfessorRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn([]);

        $materiaMock = $this->getMockBuilder(MateriaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(MateriaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($materiaMock, $pdoMock);

        $materia = $materiaMock->verMateriaDoProfessor(1);

        $this->assertIsArray($materia);
    }
}
