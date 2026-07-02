// Aguarda que todo o HTML seja carregado antes de executar o código
document.addEventListener('DOMContentLoaded', () => {
    
    // Procura o tabuleiro de jogo no ecrã
    const gameBoard = document.getElementById('game-board');
    
    // Se não estivermos na página do jogo (ou a partida não tiver começado), o JS não faz nada
    if (!gameBoard) return;

    // Vai buscar as variáveis que o PHP injetou no HTML
    const jwtToken = gameBoard.getAttribute('jwt-token');
    const roomId = gameBoard.getAttribute('data-room-id');
    const apiUrl = `http://api/api/game/${roomId}/game`;

    const suitSymbols = { 'H': '♥', 'D': '♦', 'S': '♠', 'C': '♣' };

    // Função que comunica com a API Laravel
    async function fetchGameState() {
        if (!jwtToken) {
            console.error("Erro: JWT Token não encontrado. Tens sessão iniciada?");
            return;
        }

        try {
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + jwtToken,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                renderGame(data);
            } else {
                console.error("Erro ao obter dados da API (Verifique se o token é válido ou se a API está a correr).");
            }
        } catch (error) {
            console.error("Erro de rede ao ligar à API:", error);
        }
    }

    // Função que desenha os elementos no ecrã com base nos dados da API
    function renderGame(data) {
        // 1. Renderizar o Trunfo
        if (data.trump_card) {
            const trumpSuit = data.trump_card.split('-')[0];
            const trumpRank = data.trump_card.split('-')[1];
            document.getElementById('trump-card').innerText = trumpRank + suitSymbols[trumpSuit];
        }
        
        // 2. Renderizar de quem é a vez
        const turnIndicator = document.getElementById('turn-indicator');
        if (data.current_player_id === data.my_id) {
            turnIndicator.innerText = "É a tua vez de jogar!";
            turnIndicator.className = "text-success fw-bold mt-2";
        } else {
            turnIndicator.innerText = "A aguardar jogada do adversário...";
            turnIndicator.className = "text-warning mt-2";
        }

        // 3. Renderizar as Cartas na Mão
        const handContainer = document.getElementById('my-hand-container');
        handContainer.innerHTML = ''; // Limpar antes de redesenhar
        
        if (data.my_hand && Array.isArray(data.my_hand)) {
            data.my_hand.forEach(card => {
                const suit = card.split('-')[0];
                const rank = card.split('-')[1];
                
                const cardDiv = document.createElement('div');
                cardDiv.className = `playing-card suit-${suit}`;
                cardDiv.innerText = rank + suitSymbols[suit];
                
                // Evento de clique provisório para a próxima etapa
                cardDiv.onclick = () => alert("Na próxima etapa vamos enviar a carta " + card + " para a API!");
                
                handContainer.appendChild(cardDiv);
            });
        }
    }

    // Inicia o Polling [RF26] - Pede atualizações à API a cada 3 segundos
    console.log("Motor Frontend iniciado. A ligar à API Laravel...");
    fetchGameState(); 
    setInterval(fetchGameState, 3000);
});