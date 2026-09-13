-- BrutalCinema - Dados iniciais para desenvolvimento e testes
-- Executar depois do schema.sql, no mesmo banco:
--   mariadb -u brutal -p brutalcinema < database/seed.sql
-- Senha de todos os usuários: 123456

INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES
('Administrador', 'admin@brutalcinema.com', '$2y$10$CWJfKyTjt0iwPqvNqaMl8O.vAK9r326YoMqhuo2VBfRmzaLmw.ZDO', 'admin'),
('Funcionário',   'func@brutalcinema.com',  '$2y$10$CWJfKyTjt0iwPqvNqaMl8O.vAK9r326YoMqhuo2VBfRmzaLmw.ZDO', 'funcionario'),
('Diego', 'diego@brutalcinema.com', '$2y$10$CWJfKyTjt0iwPqvNqaMl8O.vAK9r326YoMqhuo2VBfRmzaLmw.ZDO', 'cliente');

INSERT INTO filmes (titulo, sinopse, duracao_min, classificacao, genero, ano_lancamento) VALUES
('Conrado, Dedos de Morsa', 'Conrado com toda sua força quebra todas morsas que toca.', 111, 'L', 'Terror', 2026),
('Murilo, em busca da fruta perfeita', 'Murilo parte em uma jornada em busca da fruta cortada em cubinhos geladihas perfeitas.', 96, 'L', 'Animação', 2025),
('Bruno e a Voz que o persegue', 'A história do Bruno que escuta em todos lugares a frase "OI bruno" com uma voz fina.', 180, '18', 'Suspense', 2023);

INSERT INTO salas (nome, fileiras, assentos_por_fileira, tipo) VALUES
('Sala 1', 8, 12, '2D'),
('Sala 2', 6, 10, '3D'),
('Sala IMAX', 10, 16, 'IMAX');
