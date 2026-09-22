<?php declare(strict_types=1);

use App\DB\Storage\UsuarioStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class UsuarioStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testInserirUsuarioRetornoValido(): void
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

        $result = $this->createUsuarioStorage($pdoMock)->inserirUsuario([
            'name' => 'file_name',
            'email' => 'urlThumbFinal',
            'password' => 'urlFinal',
            'endereco' => 'dataComentario',
            'salt' => 1,
        ]);

        $this->assertEquals(1, $result);
    }

    public function testAlterarUsuarioRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $usuarioMock = Mockery::mock(UsuarioStorage::class, [$pdoMock])->makePartial();
        $usuarioMock->shouldReceive('loginTaken')
            ->once()
            ->andReturn(false);

        $resultado = $usuarioMock->alterarUsuario(1, 'nome', 'email', 'password', 'salt', 'tipo', true, true);

        $this->assertIsBool($resultado);
    }

    public function testAlterarUsuarioRetornoFalso(): void
    {
        $pdoMock = Mockery::mock(PDO::class);

        $usuarioMock = Mockery::mock(UsuarioStorage::class, [$pdoMock])->makePartial();
        $usuarioMock->shouldReceive('loginTaken')
            ->once()
            ->andReturn(true);

        $resultado = $usuarioMock->alterarUsuario(1, 'nome', 'email', 'password', 'salt', 'tipo', true, true);

        $this->assertFalse($resultado);
    }

    public function testLoginTakenRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) []);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $usuario = $this->createUsuarioStorage($pdoMock)->loginTaken('login', 'tipo', true);

        $this->assertIsBool($usuario);
    }

    public function testVerificarUsuarioRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) []);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $usuario = $this->createUsuarioStorage($pdoMock)->verificarUsuario('alias', 1, 'tipo', 'email');

        $this->assertInstanceOf(stdClass::class, $usuario);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createUsuarioStorage($pdoMock): UsuarioStorage
    {
        return new UsuarioStorage($pdoMock);
    }
}
