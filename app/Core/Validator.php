<?php
/**
 * Validação de dados vindos de formulários.
 *
 * Uso:
 *   $v = new Validator($dados);
 *   $v->obrigatorio('titulo', 'Título')->tamanho('titulo', 'Título', 2, 150);
 *   if (!$v->valido()) { $erros = $v->erros(); }
 *
 * Regras que não sejam "obrigatorio" ignoram campos vazios, para que
 * campos opcionais só sejam validados quando preenchidos.
 */
class Validator
{
    /** @var array<string, string> campo => primeira mensagem de erro */
    private array $erros = [];

    /** @param array<string, mixed> $dados */
    public function __construct(private array $dados)
    {
    }

    public function valido(): bool
    {
        return $this->erros === [];
    }

    /** @return array<string, string> */
    public function erros(): array
    {
        return $this->erros;
    }

    // -------------------------------------------------------------- regras

    public function obrigatorio(string $campo, string $rotulo): self
    {
        if ($this->vazio($campo)) {
            $this->adicionarErro($campo, "{$rotulo} é obrigatório.");
        }
        return $this;
    }

    public function tamanho(string $campo, string $rotulo, int $min, int $max): self
    {
        if ($this->vazio($campo)) {
            return $this;
        }
        $tam = mb_strlen((string) $this->dados[$campo]);
        if ($tam < $min || $tam > $max) {
            $this->adicionarErro($campo, "{$rotulo} deve ter entre {$min} e {$max} caracteres.");
        }
        return $this;
    }

    public function email(string $campo, string $rotulo): self
    {
        if (!$this->vazio($campo) && filter_var($this->dados[$campo], FILTER_VALIDATE_EMAIL) === false) {
            $this->adicionarErro($campo, "{$rotulo} inválido.");
        }
        return $this;
    }

    public function inteiro(string $campo, string $rotulo, ?int $min = null, ?int $max = null): self
    {
        if ($this->vazio($campo)) {
            return $this;
        }
        $valor = filter_var($this->dados[$campo], FILTER_VALIDATE_INT);
        if ($valor === false) {
            $this->adicionarErro($campo, "{$rotulo} deve ser um número inteiro.");
        } elseif (($min !== null && $valor < $min) || ($max !== null && $valor > $max)) {
            $this->adicionarErro($campo, "{$rotulo} deve estar entre {$min} e {$max}.");
        }
        return $this;
    }

    public function decimal(string $campo, string $rotulo, ?float $min = null): self
    {
        if ($this->vazio($campo)) {
            return $this;
        }
        // Aceita vírgula como separador decimal (padrão brasileiro)
        $valor = filter_var(str_replace(',', '.', (string) $this->dados[$campo]), FILTER_VALIDATE_FLOAT);
        if ($valor === false) {
            $this->adicionarErro($campo, "{$rotulo} deve ser um número.");
        } elseif ($min !== null && $valor < $min) {
            $this->adicionarErro($campo, "{$rotulo} deve ser maior ou igual a {$min}.");
        }
        return $this;
    }

    /**
     * Valida data/hora no formato informado (padrão: o enviado por <input type="datetime-local">).
     */
    public function dataHora(string $campo, string $rotulo, string $formato = 'Y-m-d\TH:i'): self
    {
        if ($this->vazio($campo)) {
            return $this;
        }
        $data = DateTime::createFromFormat($formato, (string) $this->dados[$campo]);
        if ($data === false || $data->format($formato) !== $this->dados[$campo]) {
            $this->adicionarErro($campo, "{$rotulo} com valor inválido.");
        }
        return $this;
    }

    /**
     * Valor deve ser uma das opções permitidas (espelha os ENUMs do banco).
     * @param array<int, string> $opcoes
     */
    public function opcoes(string $campo, string $rotulo, array $opcoes): self
    {
        if (!$this->vazio($campo) && !in_array((string) $this->dados[$campo], $opcoes, true)) {
            $this->adicionarErro($campo, "{$rotulo} com valor inválido.");
        }
        return $this;
    }

    /**
     * Dois campos devem ter o mesmo valor (ex.: senha e confirmação).
     */
    public function igual(string $campo, string $rotulo, string $outroCampo): self
    {
        if (!$this->vazio($campo) && ($this->dados[$campo] !== ($this->dados[$outroCampo] ?? null))) {
            $this->adicionarErro($campo, "{$rotulo} não confere.");
        }
        return $this;
    }

    /**
     * CPF válido: 11 dígitos, não todos iguais, dígitos verificadores corretos.
     */
    public function cpf(string $campo, string $rotulo): self
    {
        if ($this->vazio($campo)) {
            return $this;
        }
        $cpf = preg_replace('/\D/', '', (string) $this->dados[$campo]);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            $this->adicionarErro($campo, "{$rotulo} inválido.");
            return $this;
        }

        // Calcula os dois dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$t] !== $digito) {
                $this->adicionarErro($campo, "{$rotulo} inválido.");
                return $this;
            }
        }
        return $this;
    }

    // ----------------------------------------------------------- internos

    private function vazio(string $campo): bool
    {
        return !isset($this->dados[$campo]) || trim((string) $this->dados[$campo]) === '';
    }

    /**
     * Guarda apenas o primeiro erro de cada campo.
     */
    private function adicionarErro(string $campo, string $mensagem): void
    {
        if (!isset($this->erros[$campo])) {
            $this->erros[$campo] = $mensagem;
        }
    }
}
