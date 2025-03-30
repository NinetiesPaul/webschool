<?php declare(strict_types=1);

use App\DB\Storage\DiarioDeClasseStorage;
use PHPUnit\Framework\TestCase;

class DiarioDeClasseTest extends TestCase
{
    public function testInserirDiarioDeClasseRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->inserirDiarioDeClasse([
            'idAluno' => 1,
            'idDisciplina' => 1,
            'idTurma' => 1,
        ]);

        $this->assertEquals(null, $resposta);
    }

    public function testVerFaltasDoAlunoDaTurmaRetornoValido(): void
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

        $enderecoMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $faltas = $enderecoMock->verFaltasDoAlunoDaTurma(1, 1);

        $this->assertIsArray($faltas);
    }

    public function testVerFaltasDoAlunoDaturmaPorDataRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn((object) []);

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $falta = $diarioDeClasseMock->verFaltasDoAlunoDaturmaPorData(1, 1, 1, 'data');

        $this->assertInstanceOf(stdClass::class, $falta);
    }

    public function testAdicionarFaltaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->adicionarFalta(1, 1, 1, 'data');

        $this->assertEquals(null, $resposta);
    }

    public function testAlterarFaltaRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->alterarFalta(true, 1);

        $this->assertEquals(null, $resposta);
    }

    public function testVerComentariosDoAlunoDaTurmaRetornoValido(): void
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

        $enderecoMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($enderecoMock, $pdoMock);

        $comentarios = $enderecoMock->verComentariosDoAlunoDaTurma(1, 1, 1, 'data', 1);

        $this->assertIsArray($comentarios);
    }

    public function testAdicionarComentarioRetornoValido(): void
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

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $result = $diarioDeClasseMock->adicionarComentario(1, 1, 1, 'mensagem', 'data', 1);

        $this->assertEquals(1, $result);
    }

    public function testRemoverComentarioRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->removerComentario(1);

        $this->assertEquals(null, $resposta);
    }

    public function testVerDiarioDeClassePorProfessorRetornoValido(): void
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

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->verDiarioDeClassePorProfessor(1);

        $this->assertIsArray($resposta);
    }

    public function testVerDiarioDeClassePorAlunoRetornoValido(): void
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

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->verDiarioDeClassePorAluno(1);

        $this->assertIsArray($resposta);
    }

    public function testVerFaltasPorAlunoDaMateriaETurmaRetornoValido(): void
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

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->verFaltasPorAlunoDaMateriaETurma(1, 1, 1);

        $this->assertIsArray($resposta);
    }

    public function testVerComentariosPorAlunoDaMateriaETurmaRetornoValido(): void
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

        $diarioDeClasseMock = $this->getMockBuilder(DiarioDeClasseStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(DiarioDeClasseStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($diarioDeClasseMock, $pdoMock);

        $resposta = $diarioDeClasseMock->verComentariosPorAlunoDaMateriaETurma(1, 1, 1);

        $this->assertIsArray($resposta);
    }
}
