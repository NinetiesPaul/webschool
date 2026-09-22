<?php declare(strict_types=1);

use App\DB\Storage\TurmaStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class TurmaStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testVerTurmasRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021],
                (object) ['id' => 2, 'nome' => 'Turma B', 'ano' => 2022],
            ]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $turmas = $this->createTurmaStorage($pdoMock)->verTurmas();

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
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $turma = $this->createTurmaStorage($pdoMock)->verTurma(1);

        $this->assertInstanceOf(stdClass::class, $turma);
        $this->assertEquals(1, $turma->id);
        $this->assertEquals('Turma A', $turma->nome);
        $this->assertEquals(2021, $turma->ano);
    }

    public function testAdicionarTurmaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createTurmaStorage($pdoMock)->adicionarTurma('string', 2000);

        $this->assertNull($resultado);
    }

    public function testAlterarTurmaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createTurmaStorage($pdoMock)->alterarTurma('string', 2000, 1);

        $this->assertNull($resultado);
    }

    public function testRemoverTurmaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);
        $stmtMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createTurmaStorage($pdoMock)->removerTurma(1);

        $this->assertNull($resultado);
    }

    public function testRemoverTurmaRetornoExcecao(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);
        $stmtMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(0);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $this->expectException(Exception::class);
        $this->createTurmaStorage($pdoMock)->removerTurma(1);
    }

    public function testVerAlunosDaTurmaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021],
                (object) ['id' => 2, 'nome' => 'Turma B', 'ano' => 2022],
            ]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $alunos = $this->createTurmaStorage($pdoMock)->verAlunosDaTurma(1);

        $this->assertIsArray($alunos);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createTurmaStorage($pdoMock): TurmaStorage
    {
        return new TurmaStorage($pdoMock);
    }
}
