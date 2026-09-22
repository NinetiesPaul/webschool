<?php declare(strict_types=1);

use App\DB\Storage\MateriaStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class MateriaStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testVerMateriasRetornoValido(): void
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

        $materias = $this->createMateriaStorage($pdoMock)->verMaterias();

        $this->assertIsArray($materias);
    }

    public function testVerMateriaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $this->createMateriaStorage($pdoMock)->verMateria(1);
    }

    public function testAdicionarMateriaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createMateriaStorage($pdoMock)->adicionarMateria('string', 2000);

        $this->assertNull($resultado);
    }

    public function testAlterarMateriaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createMateriaStorage($pdoMock)->alterarMateria('string', 2000, 1);

        $this->assertNull($resultado);
    }

    public function testRemoverMateriaRetornoValido(): void
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

        $resultado = $this->createMateriaStorage($pdoMock)->removerMateria(1);

        $this->assertNull($resultado);
    }

    public function testRemoverMateriaRetornoExcecao(): void
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
        $this->createMateriaStorage($pdoMock)->removerMateria(1);
    }

    public function testVerMateriaPorProfessorPorTurmaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $materias = $this->createMateriaStorage($pdoMock)->verMateriaPorProfessorPorTurma(1);

        $this->assertIsArray($materias);
    }

    public function testVerMateriasDoProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $materias = $this->createMateriaStorage($pdoMock)->verMateriasDoProfessor(1);

        $this->assertIsArray($materias);
    }

    public function testVerMateriasDoProfessorAdminRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $materias = $this->createMateriaStorage($pdoMock)->verMateriasDoProfessorAdmin(1);

        $this->assertIsArray($materias);
    }

    public function testVerMateriaDoProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $materia = $this->createMateriaStorage($pdoMock)->verMateriaDoProfessor(1);

        $this->assertIsArray($materia);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createMateriaStorage($pdoMock): MateriaStorage
    {
        return new MateriaStorage($pdoMock);
    }
}
