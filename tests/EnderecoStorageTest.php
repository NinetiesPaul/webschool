<?php declare(strict_types=1);

use App\DB\Storage\EnderecoStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class EnderecoStorageTest extends TestCase
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
        $pdoMock->shouldReceive('lastInsertId')
            ->once()
            ->andReturn('1');

        $result = $this->createEnderecoStorage($pdoMock)->inserirEndereco();

        $this->assertEquals(1, $result);
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

        $resultado = $this->createEnderecoStorage($pdoMock)->atualizarEndereco(
            'rua',
            '1',
            'bairro',
            'complemento',
            'cidade',
            'cep',
            'estado',
            'endereco'
        );

        $this->assertNull($resultado);
    }

    public function testVerEnderecoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $endereco = $this->createEnderecoStorage($pdoMock)->verEndereco(1);

        $this->assertInstanceOf(stdClass::class, $endereco);
    }

    public function testPegarEstadoPeloEstadoRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['nome' => 'nome', 'sigla' => 'sigla']);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $estado = $this->createEnderecoStorage($pdoMock)->pegarEstadoPeloEstado(1);

        $this->assertEquals('nome, sigla', $estado);
    }

    public function testPegarEstadosRetornoValido(): void
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

        $estados = $this->createEnderecoStorage($pdoMock)->pegarEstados();

        $this->assertIsArray($estados);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createEnderecoStorage($pdoMock): EnderecoStorage
    {
        return new EnderecoStorage($pdoMock);
    }
}
