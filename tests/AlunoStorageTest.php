<?php declare(strict_types=1);

use App\DB\Storage\AlunoStorage;
use PHPUnit\Framework\TestCase;

class AlunoStorageTest extends TestCase
{
    public function testVerAlunosRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $alunos = $alunoMock->verAlunos();

        $this->assertIsArray($alunos);
    }

    public function testVerAlunoRetornoValido(): void
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

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $alunos = $alunoMock->verAluno(1);

        $this->assertInstanceOf(stdClass::class, $alunos);
    }

    public function testAdicionarAlunoRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('query')
            ->willReturn($stmtMock);

        $stmtMock->method('fetch')
            ->willReturn(false);

        $pdoMock->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->method('execute')
            ->willReturn(true);

        $stmtMock
            ->method('fetchAll')
            ->willReturn(
                [
                    (object)[ 'disciplina' => 1 ],
                    (object)[ 'disciplina' => 2 ]
                ]
            );

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $resultado = $alunoMock->adicionarAluno('email', 'nome', 'password', 'salt', 1);

        $this->assertEquals(null, $resultado);
    }

    public function testAdicionarAlunoUsuarioJaExiste(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('query')
            ->willReturn($stmtMock);

        $stmtMock->method('fetch')
            ->willReturn(true);

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $resultado = $alunoMock->adicionarAluno('email', 'nome', 'password', 'salt', 1);

        $this->assertEquals(false, $resultado);
    }

    public function testAlterarAlunoRetornoValido(): void
    {
        
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('query')
            ->willReturnOnConsecutiveCalls($stmtMock, $stmtMock, $stmtMock);

        $stmtMock->method('fetch')
            ->willReturn(false);

        $pdoMock->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->method('execute')
            ->willReturn(true);

        $stmtMock
            ->method('fetchAll')
            ->willReturnOnConsecutiveCalls(
                [ (object)[ 'disciplina' => 1 ] ],
                false,
                false
            );

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $resultado = $alunoMock->alterarAluno(1, 1, 1);

        $this->assertEquals(null, $resultado);
    }

    public function testRemoverAlunoRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->method('execute')
            ->willReturn(true);

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $resultado = $alunoMock->removerAluno(1, 1, 1, [ 'usuario' => (object) [ 'nome' => 'responsavel' ] ]);

        $this->assertEquals(null, $resultado);
    }

    public function testDesativarAlunoRetornoValido(): void
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

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $alunos = $alunoMock->desativarAluno(1);

        $this->assertEquals(true, true);
    }

    public function testDesativarAlunoRetornoExcecao(): void
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

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $this->expectException(Exception::class);
        $alunoMock->desativarAluno(1);
    }

    public function testDesativarAlunoRetornoUsuarioDesconhecido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $this->expectException(Exception::class);
        $alunoMock->desativarAluno(1);
    }

    public function testVerAlunoDoResponsavelRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $alunos = $alunoMock->verAlunosDoResponsavel(1);

        $this->assertIsArray($alunos);
    }

    public function testPegarNomeDoAlunoPorAlunoIdRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn(
                (object) [ 'nome' => 'nome_do_aluno' ]
            );

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $alunos = $alunoMock->pegarNomeDoAlunoPorAlunoId(1);

        $this->assertIsString($alunos);

        $this->assertEquals('nome_do_aluno', $alunos);
    }

    public function testPegarIdDaTurmaDoAlunoPorAlunoIdRetornoValido(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->willReturn(
                (object) [ 'turma' => 1 ]
            );

        $alunoMock = $this->getMockBuilder(AlunoStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();

        $reflection = new ReflectionClass(AlunoStorage::class);
        $dbProperty = $reflection->getParentClass()->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($alunoMock, $pdoMock);

        $alunos = $alunoMock->pegarIdDaTurmaDoAlunoPorAlunoId(1);

        $this->assertIsInt($alunos);

        $this->assertEquals(1, $alunos);
    }
}
