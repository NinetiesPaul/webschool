<?php declare(strict_types=1);

use App\DB\Storage\AlunoStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class AlunoStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testVerAlunosRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $alunos = $this->createAlunoStorage($pdoMock)->verAlunos();

        $this->assertIsArray($alunos);
    }

    public function testVerAlunoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $aluno = $this->createAlunoStorage($pdoMock)->verAluno(1);

        $this->assertInstanceOf(stdClass::class, $aluno);
    }

    public function testAdicionarAlunoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')->andReturn(false);
        $stmtMock->shouldReceive('execute')->andReturn(true);
        $stmtMock->shouldReceive('fetchAll')->andReturn([
            (object) ['disciplina' => 1],
            (object) ['disciplina' => 2],
        ]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);
        $pdoMock->shouldReceive('lastInsertId')->andReturn('1');

        $resultado = $this->createAlunoStorage($pdoMock)
            ->adicionarAluno('email', 'nome', 'password', 'salt', 1);

        $this->assertNull($resultado);
    }

    public function testAdicionarAlunoUsuarioJaExiste(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);

        $resultado = $this->createAlunoStorage($pdoMock)
            ->adicionarAluno('email', 'nome', 'password', 'salt', 1);

        $this->assertFalse($resultado);
    }

    public function testAlterarAlunoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')->andReturn(true);
        $stmtMock->shouldReceive('fetchAll')->andReturn(
            [(object) ['disciplina' => 1]],
            [],
            []
        );

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);

        $resultado = $this->createAlunoStorage($pdoMock)->alterarAluno(1, 1, 1);

        $this->assertNull($resultado);
    }

    public function testRemoverAlunoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);

        $resultado = $this->createAlunoStorage($pdoMock)->removerAluno(
            1,
            1,
            1,
            ['usuario' => (object) ['nome' => 'responsavel']]
        );

        $this->assertNull($resultado);
    }

    public function testDesativarAlunoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('rowCount')->andReturn(1);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['is_deleted' => 1]);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createAlunoStorage($pdoMock)->desativarAluno(1);

        $this->assertNull($resultado);
    }

    public function testDesativarAlunoRetornoExcecao(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('rowCount')->andReturn(1, 0);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['is_deleted' => 1]);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $this->expectException(Exception::class);
        $this->createAlunoStorage($pdoMock)->desativarAluno(1);
    }

    public function testDesativarAlunoRetornoUsuarioDesconhecido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(0);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $this->expectException(Exception::class);
        $this->createAlunoStorage($pdoMock)->desativarAluno(1);
    }

    public function testVerAlunoDoResponsavelRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $alunos = $this->createAlunoStorage($pdoMock)->verAlunosDoResponsavel(1);

        $this->assertIsArray($alunos);
    }

    public function testPegarNomeDoAlunoPorAlunoIdRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['nome' => 'nome_do_aluno']);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $nome = $this->createAlunoStorage($pdoMock)->pegarNomeDoAlunoPorAlunoId(1);

        $this->assertIsString($nome);
        $this->assertEquals('nome_do_aluno', $nome);
    }

    public function testPegarIdDaTurmaDoAlunoPorAlunoIdRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['turma' => 1]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $turmaId = $this->createAlunoStorage($pdoMock)->pegarIdDaTurmaDoAlunoPorAlunoId(1);

        $this->assertIsInt($turmaId);
        $this->assertEquals(1, $turmaId);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createAlunoStorage($pdoMock): AlunoStorage
    {
        return new AlunoStorage($pdoMock);
    }
}
