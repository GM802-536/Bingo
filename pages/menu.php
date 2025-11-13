<?php
session_start();
require_once "../src/tabela/funcoesTabela.php";
require_once "../src/prompt/funcoesPrompt.php";
require_once "../src/cartela/funcoesCartela.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../pages/login.php" . $tabela_id);
    exit;
}

$user_id = $_SESSION['usuario'] ?? null;

$tabelas = buscarTabelasUsuario($user_id);
$tabela_id = $_GET['tabela'] ?? null;

$tabela = buscarTabela($tabela_id);
$tamanho = $tabela['tamanho'] ?? 5;

$prompts = buscarPromptsTabela($tabela_id);

$cartelas = buscarCartelasTabela($tabela_id);

$erro = $_SESSION['erro'] ?? '';
$msg = $_SESSION['msg'] ?? '';

unset($_SESSION['erro']);
unset($_SESSION['msg']);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bingo Dashboard</title>
    <link rel="stylesheet" href="../assets/css/menu.css">
</head>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const tabelaId = "<?= $tabela_id ?>"; // pega a tabela atual

        document.querySelectorAll(".cell").forEach(cell => {
            cell.addEventListener("click", () => {

                const id = cell.dataset.id;
                if (!id) return; // ignora células vazias

                // ---- 1) Marcar ou desmarcar a célula ----
                fetch("../src/cartela/marcarCartela.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "cartela_id=" + encodeURIComponent(id)
                })
                    .then(res => res.json())
                    .then(data => {

                        if (!data.success) {
                            throw new Error(data.message);
                        }

                        // Atualiza visualmente
                        if (data.marcada === 1) {
                            cell.classList.add("marcada");
                        } else {
                            cell.classList.remove("marcada");
                        }

                        // ---- 2) Depois de marcar, checar vitória ----
                        return fetch("../src/tabela/checarVitoria.php?tabela=" + encodeURIComponent(tabelaId));
                    })
                    .then(res => res.json())
                    .then(result => {

                        if (!result.success) {
                            throw new Error(result.message);
                        }

                        if (result.vitoria) {
                            alert("🎉 BINGO! Você venceu!");
                        }
                    })
                    .catch(err => {
                        alert("Erro: " + err.message);
                    });

            });
        });

    });
</script>


<body>

    <div class="layout">

        <!-- Barra lateral esquerda -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Bingo</h2>
            </div>

            <div class="sidebar-section">

                <!-- Criar tabela -->
                <button type="button" class="btn-create" id="btnNovaTabela">+ Nova Tabela</button>

                <!-- Modal de criação -->
                <div id="modal-tabela" class="modal">
                    <div class="modal-content">
                        <h3>Criar nova tabela</h3>
                        <form action="../src/tabela/criarTabela.php" method="POST">
                            <label for="nome">Nome da tabela:</label>
                            <input type="text" name="nome" id="nome" required>

                            <label for="tamanho">Tamanho:</label>
                            <input type="number" name="tamanho" id="tamanho" min="3" max="10" value="5" required>

                            <div class="modal-actions">
                                <button type="submit" class="btn-salvar">Criar</button>
                                <button type="button" class="btn-cancelar" id="btnFecharModal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Lista das tabelas -->
                <ul class="table-list">
                    <?php if (!empty($tabelas)): ?>
                        <?php foreach ($tabelas as $tabela): ?>
                            <li class="table-item">
                                <div class="table-line">
                                    <!--Selecao da tabela atraves de GET -->
                                    <a href="?tabela=<?= $tabela['id'] ?>"
                                        class="<?= (isset($_GET['tabela']) && $_GET['tabela'] == $tabela['id']) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($tabela['nome']) ?>
                                        <small class="data"><?= date('d/m/Y', strtotime($tabela['criado_em'])) ?></small>

                                    </a>

                                    <form
                                        action="../src/tabela/excluirTabela.php?tabela=<?= htmlspecialchars($_GET['tabela'] ?? '') ?>"
                                        method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta tabela?');"
                                        style="display:inline;">
                                        <input type="hidden" name="excluirTabela_id" value="<?= $tabela['id'] ?>">
                                        <button type="submit" class="btn-delete-tabela">🗑️</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="table-item disabled">Nenhuma tabela criada</li>
                    <?php endif; ?>
                </ul>
            </div>

        </aside>

        <!-- Conteúdo central -->
        <main class="main">
            <h1 class="main-title">Minha Cartela</h1>

            <?php if (!empty($erro) || !empty($msg)): ?>
                <div class="alert-container">
                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-error">
                            <?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($msg)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($msg) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


            <div class="grid" style="grid-template-columns: repeat(<?= $tamanho ?>, 1fr);">
                <?php
                if (!empty($cartelas) && count($cartelas) >= pow($tamanho, 2)) {
                    foreach ($cartelas as $cartela) {
                        $prompt = buscarPrompt($cartela['prompt_id']);
                        $marcada = $cartela['marcada'] ? 'marcada' : '';
                        echo "<div class='cell $marcada' data-id='{$cartela['id']}'>" . htmlspecialchars($prompt['texto']) . "</div>";
                    }
                } else {
                    for ($i = 0; $i < pow($tamanho, 2); $i++) {
                        echo "<div class='cell empty'></div>";
                    }
                }
                ?>
            </div>

            <div class="acoes-centro">
                <form action="../src/cartela/preencherCartelas.php" method="GET">
                    <input type="hidden" name="tabela" value="<?= htmlspecialchars($_GET['tabela'] ?? '') ?>">
                    <button type="submit" class="btn-preencher">Preencher Cartelas / Atualizar</button>
                </form>
            </div>
        </main>

        <!-- Painel lateral direito -->
        <aside class="editor">
            <h2>Gerenciar Tabela</h2>

            <!-- Form de configuração da tabela -->
            <form action="../src/tabela/alterarTamanho.php" method="POST" class="config-form">
                <input type="hidden" name="tabela_id" value="<?= htmlspecialchars($_GET['tabela'] ?? '') ?>">

                <div class="form-group">
                    <label for="tamanho">Tamanho da Tabela</label>
                    <input type="number" name="tamanho" id="tamanho" min="3" max="10" placeholder="5" required>
                </div>

                <button type="submit" class="btn-tamanho">Atualizar Tamanho</button>
            </form>

            <hr>

            <!-- Form de prompts -->
            <form action="../src/prompt/adicionarPrompt.php" method="POST" class="prompt-form">
                <input type="hidden" name="tabela_id" value="<?= htmlspecialchars($_GET['tabela'] ?? '') ?>">

                <div class="form-group">
                    <label for="novoPrompt">Novo Prompt</label>
                    <input type="text" id="novoPrompt" name="texto" placeholder="Digite um novo prompt..." required>
                </div>

                <button type="submit" class="btn-add">Adicionar Prompt</button>
            </form>

            <hr>

            <button class="btn-editar" id="btnEditarPrompts">
                Editar Prompts
                <span class="circle-count"><?= count($prompts) ?></span>
            </button>

            <!-- Modal de editar prompts-->
            <div id="modal-prompts" class="modal">
                <div class="modal-prompts-content" style="max-width: 1000px">
                    <h3>Editar prompts (Total: <?= count($prompts) ?>)</h3>
                    <?php if (!empty($prompts)): ?>
                        <ul class="prompt-list">
                            <?php foreach ($prompts as $p): ?>
                                <li class="prompt-item">
                                    <span><?= htmlspecialchars($p['texto']) ?></span>

                                    <div class="prompt-actions">
                                        <!-- EDITAR -->
                                        <form action="../src/prompt/editarPrompt.php" method="POST" class="inline-form">
                                            <input type="hidden" name="prompt_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="tabela_id" value="<?= $tabela_id ?>">
                                            <input type="text" name="novo_texto" placeholder="Novo texto" required>
                                            <button class="btn-small btn-edit" type="submit">Salvar</button>
                                        </form>

                                        <!-- EXCLUIR -->
                                        <form action="../src/prompt/excluirPrompt.php" method="POST" class="inline-form">
                                            <input type="hidden" name="prompt_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="tabela_id" value="<?= $tabela_id ?>">
                                            <button class="btn-small btn-delete" type="submit">X</button>
                                        </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nenhum prompt cadastrado</p>
                    <?php endif; ?>
                    <button type="button" class="btn-cancelar" id="btnFecharPrompts">Fechar</button>
                </div>
            </div>

            <hr>

            <div class="logout-container">
                <form action="../src/user/logout.php" method="POST">
                    <button type="submit" class="btn-logout">Sair</button>
                </form>
            </div>



        </aside>


    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("modal-tabela");
            const btnOpen = document.getElementById("btnNovaTabela");
            const btnClose = document.getElementById("btnFecharModal");

            btnOpen.addEventListener("click", () => {
                modal.style.display = "flex";
            });

            btnClose.addEventListener("click", () => {
                modal.style.display = "none";
            });

            // Fecha o modal clicando fora
            window.addEventListener("click", (event) => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // ----- EDITAR PROMPTS -----
            const btnEditar = document.getElementById("btnEditarPrompts");
            const modalPrompts = document.getElementById("modal-prompts");
            const btnFecharPrompts = document.getElementById("btnFecharPrompts");

            btnEditar.addEventListener("click", () => {
                modalPrompts.style.display = "flex";
            });

            btnFecharPrompts.addEventListener("click", () => {
                modalPrompts.style.display = "none";
            });

            window.addEventListener("click", (e) => {
                if (e.target === modalPrompts) {
                    modalPrompts.style.display = "none";
                }
            });

        });
    </script>

</body>

</html>