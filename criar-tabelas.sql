-- ------------------------------------------------------------
-- Banco de Dados: bingo_db
-- Estrutura completa sem inserts
-- ------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS bingo_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE bingo_db;

-- ------------------------------------------------------------
-- Tabela: usuarios
-- ------------------------------------------------------------
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela: tabelas_bingo
-- ------------------------------------------------------------
CREATE TABLE tabelas_bingo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  user_id INT NOT NULL,
  tamanho INT NOT NULL DEFAULT 5,
  vitoria BOOLEAN DEFAULT 0,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES usuarios(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela: prompts
-- ------------------------------------------------------------
CREATE TABLE prompts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  texto VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela: tabela_prompts
-- ------------------------------------------------------------
CREATE TABLE tabela_prompts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tabela_id INT NOT NULL,
  prompt_id INT NOT NULL,
  FOREIGN KEY (tabela_id) REFERENCES tabelas_bingo(id) ON DELETE CASCADE,
  FOREIGN KEY (prompt_id) REFERENCES prompts(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Tabela: cartelas
-- ------------------------------------------------------------
CREATE TABLE cartelas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tabela_id INT NOT NULL,
  prompt_id INT NOT NULL,
  pos_x INT NOT NULL,
  pos_y INT NOT NULL,
  marcada BOOLEAN DEFAULT 0,
  FOREIGN KEY (tabela_id) REFERENCES tabelas_bingo(id)
    ON DELETE CASCADE,
  FOREIGN KEY (prompt_id) REFERENCES prompts(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Fim do script
-- ------------------------------------------------------------
