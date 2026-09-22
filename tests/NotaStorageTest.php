<?php declare(strict_types=1);

use App\DB\Storage\NotaStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class NotaStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testInserirNotaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createNotaStorage($pdoMock)->inserirNota([
            'idAluno' => 1,
            'idDisciplina' => 1,
            'idTurma' => 1,
        ]);

        $this->assertNull($resultado);
    }

    public function testAdicionarNotaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createNotaStorage($pdoMock)->adicionarNota([
            'aluno' => 1,
            'turma' => 1,
            'disciplina' => 1,
            'tipo' => 'string',
            'nota' => 1,
        ]);

        $this->assertNull($resultado);
    }

    public function testVerTurmasComNotaDoAlunoRetornoValido(): void
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

        $turmasENotas = $this->createNotaStorage($pdoMock)->verTurmasComNotaDoAluno(1);

        $this->assertIsArray($turmasENotas);
    }

    public function testVerNotasPorTrumaRetornoValido(): void
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

        $notaPorTurma = $this->createNotaStorage($pdoMock)->verNotasPorTruma(1, 1);

        $this->assertIsArray($notaPorTurma);
    }

    public function testVerTurmasEMateriasComNotasDoAlunoRetornoValido(): void
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

        $turmas = $this->createNotaStorage($pdoMock)->verTurmasEMateriasComNotasDoAluno(1);

        $this->assertIsArray($turmas);
    }

    public function testVerNotasPorAlunosDaDisciplinaETurmaRetornoValido(): void
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

        $turmas = $this->createNotaStorage($pdoMock)->verNotasPorAlunosDaDisciplinaETurma(1, 1);

        $this->assertIsArray($turmas);
    }

    public function testVerNotasPorAlunoRetornoValido(): void
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

        $notas = $this->createNotaStorage($pdoMock)->verNotasPorAluno(1);

        $this->assertIsArray($notas);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createNotaStorage($pdoMock): NotaStorage
    {
        return new NotaStorage($pdoMock);
    }
}
