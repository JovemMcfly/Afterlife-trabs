<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afterlife - Sistema Integrado</title>
    <style>
        /* (MANTIVE O MESMO ESTILO ANTERIOR COM PEQUENAS ADIÇÕES PARA O ADMIN) */
        :root { --primary: #8a2be2; --secondary: #00ff00; --bg-dark: #121212; --bg-card: #1e1e1e; --text-light: #e0e0e0; }
        * { box-sizing: border-box; transition: all 0.3s ease; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: var(--bg-dark); color: var(--text-light); min-height: 100vh; display: flex; flex-direction: column; }
        
        header { background: #1f1f1f; padding: 20px; text-align: center; border-bottom: 2px solid var(--primary); }
        h1 { color: var(--primary); text-transform: uppercase; margin: 0; }
        
        nav { background: #000; position: sticky; top: 0; z-index: 100; text-align: center; padding: 10px; }
        nav button { background: none; border: none; color: white; padding: 10px 20px; font-weight: bold; cursor: pointer; }
        nav button.active { color: var(--primary); border-bottom: 2px solid var(--primary); }

        main { max-width: 1200px; margin: 0 auto; padding: 20px; width: 100%; flex: 1; }
        .page-section { display: none; animation: fadeIn 0.5s; }
        .page-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Estilos Cards */
        .agenda-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .event-card { background: var(--bg-card); border: 1px solid #333; border-radius: 8px; overflow: hidden; position: relative; }
        .event-card img { width: 100%; height: 180px; object-fit: cover; }
        .event-body { padding: 15px; }
        .price { color: var(--secondary); font-size: 1.2em; font-weight: bold; }
        .btn { width: 100%; padding: 10px; background: var(--primary); color: white; border: none; cursor: pointer; margin-top: 10px; }
        
        /* Painel Admin (CRUD) */
        .admin-panel { background: #2c2c2c; padding: 20px; border: 1px solid var(--primary); margin-bottom: 30px; border-radius: 8px; }
        .admin-panel input, .admin-panel select { padding: 8px; margin: 5px; background: #121212; color: white; border: 1px solid #555; }
        .btn-delete { background: #ff4444; color: white; border: none; padding: 5px 10px; cursor: pointer; float: right; }

        /* Carrinho */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #333; }
    </style>
</head>
<body>

    <header>
        <h1>Afterlife</h1>
        <p>Sistema de Gestão de Eventos</p>
    </header>

    <nav>
        <button onclick="router('home')" class="active" id="btn-home">Home</button>
        <button onclick="router('agenda')" id="btn-agenda">Agenda</button>
        <button onclick="router('admin')" id="btn-admin" style="color: orange;">Area Admin (CRUD)</button>
        <button onclick="router('carrinho')" id="btn-carrinho">Carrinho <span id="cart-count">(0)</span></button>
    </nav>

    <main>
        <section id="home" class="page-section active">
            <div style="text-align: center; padding: 50px;">
                <h2>Bem-vindo ao Afterlife</h2>
                <p>A melhor casa de shows de Belém.</p>
                <img src="https://images.unsplash.com/photo-1533174072545-e8d4aa97edf9?w=800" style="max-width:100%; border-radius:10px; margin-top:20px;">
            </div>
        </section>

        <section id="agenda" class="page-section">
            <h2>Próximos Shows</h2>
            <div id="agenda-container" class="agenda-grid">Carregando...</div>
        </section>

        <section id="admin" class="page-section">
            <h2>Gestão de Produtos (CRUD)</h2>
            
            <div class="admin-panel">
                <h3>Cadastrar Novo Show</h3>
                <form id="form-create">
                    <input type="text" id="novo-titulo" placeholder="Nome do Show" required>
                    <select id="novo-dia">
                        <option value="Sexta-feira">Sexta-feira</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                    <input type="text" id="novo-desc" placeholder="Descrição" required>
                    <input type="number" id="novo-preco" placeholder="Preço (R$)" step="0.01" required>
                    <input type="text" id="novo-img" placeholder="URL da Imagem" value="https://via.placeholder.com/400">
                    <button type="submit" class="btn" style="width: auto;">Salvar no Banco</button>
                </form>
            </div>

            <h3>Lista Atual (Banco de Dados)</h3>
            <div id="admin-list" class="agenda-grid"></div>
        </section>

        <section id="carrinho" class="page-section">
            <h2>Carrinho de Compras</h2>
            <div id="cart-items"></div>
            <div style="text-align: right; margin-top: 20px;">
                <h3>Total: R$ <span id="cart-total">0.00</span></h3>
                <button onclick="checkout()" class="btn" style="background: var(--secondary); color: black;">Finalizar Compra</button>
            </div>
        </section>
    </main>

    <script>
        let carrinho = [];

        // --- ROTEAMENTO ---
        function router(page) {
            document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
            document.getElementById(page).classList.add('active');
            document.getElementById('btn-'+page).classList.add('active');

            if(page === 'agenda') carregarEventos('agenda');
            if(page === 'admin') carregarEventos('admin');
            if(page === 'carrinho') renderizarCarrinho();
        }

        // --- INTEGRAÇÃO COM PHP (FETCH API) ---
        async function carregarEventos(modo) {
            const container = document.getElementById(modo === 'agenda' ? 'agenda-container' : 'admin-list');
            container.innerHTML = 'Carregando do banco de dados...';

            try {
                // [cite: 36] Leitura do banco via API
                const response = await fetch('api.php?acao=listar');
                const eventos = await response.json();

                container.innerHTML = '';
                eventos.forEach(evento => {
                    const card = document.createElement('article');
                    card.className = 'event-card';
                    
                    // Renderização condicional (Botão Comprar vs Botão Deletar)
                    let btnHTML = modo === 'agenda' 
                        ? `<button class="btn" onclick="addCarrinho(${evento.id}, '${evento.titulo}', ${evento.preco})">Comprar</button>`
                        : `<button class="btn-delete" onclick="deletarEvento(${evento.id})">Excluir (CRUD)</button>`;

                    card.innerHTML = `
                        <img src="${evento.imagem}">
                        <div class="event-body">
                            <small>${evento.dia}</small>
                            <h3>${evento.titulo}</h3>
                            <p>${evento.descricao}</p>
                            <div class="price">R$ ${parseFloat(evento.preco).toFixed(2)}</div>
                            ${btnHTML}
                        </div>
                    `;
                    container.appendChild(card);
                });
            } catch (error) {
                console.error("Erro:", error);
                container.innerHTML = "Erro ao conectar com o banco de dados.";
            }
        }

        // --- CREATE (SALVAR NO BANCO) ---
        document.getElementById('form-create').addEventListener('submit', async (e) => {
            e.preventDefault();
            const dados = {
                titulo: document.getElementById('novo-titulo').value,
                dia: document.getElementById('novo-dia').value,
                descricao: document.getElementById('novo-desc').value,
                preco: document.getElementById('novo-preco').value,
                imagem: document.getElementById('novo-img').value
            };

            // [cite: 36, 37] Envio seguro via POST
            await fetch('api.php?acao=criar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });

            alert('Produto cadastrado com sucesso!');
            e.target.reset();
            carregarEventos('admin'); // Atualiza a lista
        });

        // --- DELETE (REMOVER DO BANCO) ---
        async function deletarEvento(id) {
            if(!confirm('Tem certeza que deseja excluir este show?')) return;

            await fetch(`api.php?acao=deletar&id=${id}`);
            alert('Item excluído!');
            carregarEventos('admin');
        }

        // --- LÓGICA DO CARRINHO ---
        function addCarrinho(id, titulo, preco) {
            carrinho.push({ id, titulo, preco });
            document.getElementById('cart-count').innerText = `(${carrinho.length})`;
            alert('Adicionado ao carrinho!');
        }

        function renderizarCarrinho() {
            const container = document.getElementById('cart-items');
            if(carrinho.length === 0) {
                container.innerHTML = "<p>Carrinho vazio.</p>";
                return;
            }
            
            let html = "<table><tr><th>Show</th><th>Preço</th></tr>";
            let total = 0;
            carrinho.forEach(item => {
                html += `<tr><td>${item.titulo}</td><td>R$ ${item.preco}</td></tr>`;
                total += parseFloat(item.preco);
            });
            html += "</table>";
            
            container.innerHTML = html;
            document.getElementById('cart-total').innerText = total.toFixed(2);
        }

        // --- FINALIZAR PEDIDO (SALVAR NO BANCO)  ---
        async function checkout() {
            if(carrinho.length === 0) return alert("Carrinho vazio!");

            const total = document.getElementById('cart-total').innerText;
            const cliente = prompt("Digite seu nome para o ingresso:");
            const email = prompt("Digite seu email:");

            if(cliente && email) {
                await fetch('api.php?acao=finalizar_pedido', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome: cliente, email: email, total: total })
                });

                alert(`Obrigado ${cliente}! Pedido registrado no banco de dados.`);
                carrinho = [];
                router('home');
                document.getElementById('cart-count').innerText = "(0)";
            }
        }
        
        // Carregar home inicial
        router('home');
    </script>
</body>
</html>