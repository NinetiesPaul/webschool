<?php declare(strict_types=1);

use App\DB\Storage\TurmaStorage;
use PHPUnit\Framework\TestCase;

class TurmaStorageTest extends TestCase
{
    public function testVerTurmasRetornoValido(): void
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

        $turmaMock = $this->getMockBuilder(TurmaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(TurmaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);

        $dbProperty->setValue($turmaMock, $pdoMock);

        $turmas = $turmaMock->verTurmas();

        $this->assertIsArray($turmas);
        $this->assertCount(2, $turmas);
        $this->assertEquals(1, $turmas[0]->id);
        $this->assertEquals('Turma A', $turmas[0]->nome);
        $this->assertEquals(2021, $turmas[0]->ano);

        $this->assertEquals(2, $turmas[1]->id);
        $this->assertEquals('Turma B', $turmas[1]->nome);
        $this->assertEquals(2022, $turmas[1]->ano);
    }

    public function testVerTurmaRetornoValido(): void
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

        $turmaMock = $this->getMockBuilder(TurmaStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(TurmaStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);

        $dbProperty->setValue($turmaMock, $pdoMock);

        $turmas = $turmaMock->verTurma(1);

        $this->assertInstanceOf(stdClass::class, $turmas);
        $this->assertEquals(1, $turmas->id);
        $this->assertEquals('Turma A', $turmas->nome);
        $this->assertEquals(2021, $turmas->ano);
    }
}
