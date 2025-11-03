<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draftosaurus - Juego</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect fill="%23228B22" width="100" height="100"/><circle fill="%23654321" cx="20" cy="20" r="2"/><circle fill="%23654321" cx="80" cy="80" r="2"/></svg>') center/cover;
            overflow: hidden;
            height: 100vh;
        }

        .game-container {
            display: grid;
            grid-template-columns: 250px 1fr 250px;
            grid-template-rows: 150px 150px 1fr 200px;
            height: 100vh;
            gap: 10px;
            padding: 10px;
        }

        .player-card {
            background: linear-gradient(145deg, #fff, #e6e6e6);
            padding: 15px;
            border: 2px solid #999;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            font-weight: bold;
            transition: .3s ease;
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .player-card.active {
            border-color: #4CAF50;
            box-shadow: 0 0 20px rgba(76, 175, 80, .5);
            transform: scale(1.05);
        }

        .player-name {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 8px;
        }

        .player-score {
            font-size: 1.5rem;
            color: #667eea;
        }

        /* Posiciones de jugadores */
        #player1 { grid-column: 1; grid-row: 1; }
        #player2 { grid-column: 3; grid-row: 1; }
        #player3 { grid-column: 1; grid-row: 2; }
        #player4 { grid-column: 3; grid-row: 2; }
        #player5 { 
            grid-column: 3; 
            grid-row: 3; 
            display: none;
            height: 150px;
        }

        .board-container {
            grid-column: 2;
            grid-row: 1 / 4;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        }

        .board-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .dino-panel {
            grid-column: 1;
            grid-row: 4;
            background: linear-gradient(145deg, #D2691E, #8B4513);
            padding: 15px;
            border-radius: 12px;
            border: 3px solid #654321;
            position: relative;
        }

        .dino-panel h3 {
            margin: 0 0 10px;
            font-size: 1rem;
            color: #fff;
            text-align: center;
        }

        .dino-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
        }

        .dino-item {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: grab;
            transition: .2s ease;
            aspect-ratio: 1;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 2rem;
            position: relative;
        }

        .dino-item:hover {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.4);
        }

        .dino-item:active {
            cursor: grabbing;
        }

        .dice-container {
            grid-column: 3;
            grid-row: 4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .turn-info {
            font-size: .9rem;
            color: #fff;
            text-align: center;
            font-weight: bold;
            background: rgba(0, 0, 0, .5);
            padding: 8px 12px;
            border-radius: 8px;
        }

        .turn-status {
            font-size: .75rem;
            color: #ffd700;
            text-align: center;
            margin-top: 5px;
            background: rgba(0, 0, 0, .3);
            padding: 5px 8px;
            border-radius: 5px;
        }

        .dice-info {
            font-size: .7rem;
            color: #fff;
            text-align: center;
            background: rgba(255, 0, 0, .6);
            padding: 4px 8px;
            border-radius: 5px;
            margin-top: 5px;
            max-width: 200px;
        }

        .dice-info.no-restriction {
            background: rgba(76, 175, 80, .6);
        }

        .round-info {
            font-size: 1rem;
            color: #fff;
            background: rgba(255, 165, 0, .7);
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: bold;
        }

        .dice-small {
            width: 90px;
            height: 90px;
            background: white;
            border: 3px solid #666;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            cursor: pointer;
            transition: .3s ease;
            box-shadow: 0 6px 12px rgba(0, 0, 0, .2);
        }

        .dice-small:hover {
            transform: scale(1.05);
        }

        .dice-small:active {
            transform: scale(0.95);
        }

        @keyframes spin {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.2); }
            100% { transform: rotate(360deg) scale(1); }
        }

        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            z-index: 10001;
            pointer-events: none;
        }

        /* Tutorial overlay */
        .tutorial-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .tutorial-overlay.active {
            display: flex;
        }

        .tutorial-content {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            padding: 40px;
            border-radius: 25px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .tutorial-content h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .tutorial-content h3 {
            color: #667eea;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }

        .tutorial-content p, .tutorial-content li {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .tutorial-content ul {
            margin-left: 20px;
        }

        .zone-example {
            background: rgba(102, 126, 234, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }

        .dice-example {
            display: inline-block;
            background: white;
            border: 2px solid #666;
            border-radius: 8px;
            padding: 5px 10px;
            margin: 5px;
            font-size: 1.5rem;
        }

        .tutorial-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
        }

        .tutorial-btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .tutorial-btn.primary {
            background: linear-gradient(145deg, #4CAF50, #45a049);
            color: white;
            flex: 1;
        }

        .tutorial-btn.secondary {
            background: linear-gradient(145deg, #667eea, #5568d3);
            color: white;
        }

        .tutorial-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .help-btn {
            position: fixed;
            bottom: 230px;
            left: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(145deg, #667eea, #5568d3);
            color: white;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .help-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .sound-toggle {
            position: fixed;
            bottom: 230px;
            left: 90px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(145deg, #FFA500, #FF8C00);
            color: white;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sound-toggle:hover {
            transform: scale(1.1);
        }

        .sound-toggle.muted {
            background: linear-gradient(145deg, #999, #666);
        }

        .name-input {
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1.1rem;
            font-family: Arial, sans-serif;
            transition: all 0.3s ease;
        }

        .name-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
        }

        /* Records overlay */
        .records-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .records-overlay.active {
            display: flex;
        }

        .records-content {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            padding: 40px;
            border-radius: 25px;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .records-content h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2rem;
            text-align: center;
        }

        .record-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .record-item.top {
            background: linear-gradient(145deg, #FFD700, #FFA500);
            color: white;
            font-weight: bold;
        }

        .record-name {
            font-size: 1.2rem;
        }

        .record-score {
            font-size: 1.4rem;
            color: #667eea;
        }

        .record-item.top .record-score {
            color: white;
        }

        .records-btn {
            position: fixed;
            bottom: 230px;
            left: 160px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(145deg, #FFD700, #FFA500);
            color: white;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .records-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
        }

        .clear-records-btn {
            background: linear-gradient(145deg, #ff6b6b, #ee5555);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .clear-records-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        }

        .zone {
            position: absolute;
            border: 3px dashed #333;
            background: rgba(255, 255, 255, .15);
            z-index: 2;
            transition: .2s ease;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            padding: 8px;
            border-radius: 8px;
            cursor: grab;
        }

        .zone.drag-over {
            background: rgba(76, 175, 80, .3);
            border-color: #4CAF50;
            border-style: solid;
        }

        .zone.restricted {
            background: rgba(255, 0, 0, .25);
            border-color: #ff0000;
        }

        .zone.moving {
            cursor: grabbing;
            z-index: 100;
        }

        .zone.resizing {
            cursor: nwse-resize;
        }

        .zone-handle {
            position: absolute;
            right: 4px;
            bottom: 4px;
            width: 14px;
            height: 14px;
            background: rgba(0, 0, 0, .7);
            border-radius: 3px;
            cursor: nwse-resize;
            z-index: 10;
            display: none;
        }

        .edit-mode .zone-handle {
            display: block;
        }

        .edit-btn {
            padding: 8px 16px;
            background: linear-gradient(145deg, #fff, #e0e0e0);
            border: 2px solid #666;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 5px;
            transition: .3s ease;
        }

        .edit-btn.active {
            background: linear-gradient(145deg, #4CAF50, #45a049);
            color: white;
            border-color: #2e7d32;
        }

        .zone-label {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(0, 0, 0, .8);
            color: #fff;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: .75rem;
            font-weight: bold;
            z-index: 10;
        }

        .zone-dino {
            font-size: 2rem;
            margin: 3px;
            animation: pop .3s ease;
        }

        @keyframes pop {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .status-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .9);
            color: #fff;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            z-index: 10000;
            animation: slideDown .3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        /* Overlays */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .overlay.active {
            display: flex;
        }

        .modal {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            padding: 50px;
            border-radius: 25px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal h2 {
            font-size: 2.5rem;
            margin: 0 0 15px;
            color: #2c3e50;
        }

        .player-btn {
            padding: 20px 30px;
            font-size: 1.8rem;
            border: 4px solid #ddd;
            background: white;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
        }

        .player-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .player-btn.selected {
            background: linear-gradient(145deg, #4CAF50, #45a049);
            color: white;
            border-color: #4CAF50;
        }

        .start-btn {
            padding: 18px 50px;
            font-size: 1.4rem;
            background: linear-gradient(145deg, #667eea, #5568d3);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 20px;
        }

        .start-btn:hover {
            background: linear-gradient(145deg, #5568d3, #4557c2);
        }

        /* Draft overlay */
        .draft-container {
            position: relative;
            width: 800px;
            height: 800px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tray {
            background: linear-gradient(145deg, #D2691E, #8B4513);
            padding: 20px;
            border-radius: 15px;
            border: 3px solid #654321;
            min-width: 180px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .tray-title {
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .tray-dinos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .tray-dino {
            font-size: 2rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        /* Results */
        .results-table {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .results-table th,
        .results-table td {
            padding: 15px;
            text-align: center;
        }

        .results-table th {
            background: linear-gradient(145deg, #667eea, #5568d3);
            color: white;
            font-size: 1.1rem;
        }

        .results-table tr:nth-child(even) {
            background: #f5f5f5;
        }

        .results-table tr:hover {
            background: #e8f5e9;
        }

        .winner {
            background: linear-gradient(145deg, #FFD700, #FFA500) !important;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .winner td {
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .btn-group {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Setup overlay -->
    <div class="overlay active" id="setup-overlay">
        <div class="modal">
            <h2>🦖 Draftosaurus 🦕</h2>
            <p>Selecciona la cantidad de jugadores</p>
            <div>
                <button class="player-btn selected" onclick="selectPlayers(2)">2</button>
                <button class="player-btn" onclick="selectPlayers(3)">3</button>
                <button class="player-btn" onclick="selectPlayers(4)">4</button>
                <button class="player-btn" onclick="selectPlayers(5)">5</button>
            </div>
            
            <div style="margin-top: 30px;">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Nombres de los jugadores</h3>
                <div id="player-names-container" style="display: grid; gap: 10px; max-width: 400px; margin: 0 auto;">
                    <input type="text" id="name1" placeholder="Jugador 1" class="name-input" maxlength="15">
                    <input type="text" id="name2" placeholder="Jugador 2" class="name-input" maxlength="15">
                    <input type="text" id="name3" placeholder="Jugador 3" class="name-input" maxlength="15" style="display: none;">
                    <input type="text" id="name4" placeholder="Jugador 4" class="name-input" maxlength="15" style="display: none;">
                    <input type="text" id="name5" placeholder="Jugador 5" class="name-input" maxlength="15" style="display: none;">
                </div>
            </div>
            
            <button class="start-btn" onclick="startGame()">¡Comenzar!</button>
        </div>
    </div>

    <!-- Draft overlay -->
    <div class="overlay" id="draft-overlay">
        <div class="modal" style="max-width: 90%;">
            <h2>🔄 ¡Draft de Dinosaurios!</h2>
            <p style="margin-bottom: 30px;">Los dinosaurios pasan al siguiente jugador...</p>
            <div class="draft-container" id="draft-container"></div>
            <button class="start-btn" onclick="completeDraft()">Continuar</button>
        </div>
    </div>

    <!-- Results overlay -->
    <div class="overlay" id="results-overlay">
        <div class="modal">
            <h2>🏆 Resultados Finales</h2>
            <table class="results-table" id="results-table"></table>
            <div class="btn-group">
                <button class="start-btn" onclick="resetGame()">Jugar de Nuevo</button>
                <button class="start-btn" onclick="goToMenu()">Volver al Menú</button>
            </div>
        </div>
    </div>

    <!-- Records overlay -->
    <div class="records-overlay" id="records-overlay">
        <div class="records-content">
            <h2>🏆 Mejores Puntuaciones</h2>
            <div id="records-list"></div>
            <div style="text-align: center;">
                <button class="clear-records-btn" onclick="clearRecords()">Limpiar Récords</button>
                <button class="tutorial-btn secondary" onclick="closeRecords()" style="margin-top: 10px;">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Records button -->
    <button class="records-btn" onclick="openRecords()" title="Récords">🏆</button>

    <!-- Tutorial overlay -->
    <div class="tutorial-overlay" id="tutorial-overlay">
        <div class="tutorial-content">
            <h2>Tutorial de Draftosaurus</h2>
            
            <h3>🎯 Objetivo del Juego</h3>
            <p>Consigue la mayor cantidad de puntos colocando dinosaurios estratégicamente en 7 zonas diferentes. El juego dura 2 rondas.</p>
            
            <h3>🎲 Restricciones del Dado</h3>
            <p>Antes de colocar tu dinosaurio, debes tirar el dado. El resultado determina dónde pueden jugar LOS DEMÁS jugadores (tú puedes colocar donde quieras):</p>
            
            <div class="zone-example">
                <span class="dice-example">1️⃣</span> Solo: Rey de la Selva, Prado de la Diferencia, Isla Solitaria<br>
                <span class="dice-example">2️⃣</span> Solo: Bosque de la Semejanza, Trio Frondoso, Rey de la Selva<br>
                <span class="dice-example">3️⃣</span> Solo en zonas vacías (sin dinosaurios)<br>
                <span class="dice-example">4️⃣</span> Solo: Pradera del Amor, Prado de la Diferencia, Isla Solitaria<br>
                <span class="dice-example">5️⃣</span> Solo: Bosque de la Semejanza, Trio Frondoso, Pradera del Amor<br>
                <span class="dice-example">6️⃣</span> Solo en zonas que NO tengan T-Rex (🦖)
            </div>
            
            <h3>🏞️ Las 7 Zonas y sus Reglas</h3>
            
            <div class="zone-example">
                <strong>1. Bosque de la Semejanza</strong><br>
                • Máximo 6 dinosaurios del MISMO tipo<br>
                • Puntos: 1 dino=2pts, 2=4pts, 3=8pts, 4=12pts, 5=18pts, 6=24pts
            </div>
            
            <div class="zone-example">
                <strong>2. El Trio Frondoso</strong><br>
                • Debe tener EXACTAMENTE 3 dinosaurios<br>
                • 7 puntos si tiene 3, sino 0 puntos
            </div>
            
            <div class="zone-example">
                <strong>3. La Pradera del Amor</strong><br>
                • Ganas puntos por PAREJAS del mismo tipo<br>
                • Cada pareja vale 5 puntos (2 T-Rex = 5pts, 4 T-Rex = 10pts)
            </div>
            
            <div class="zone-example">
                <strong>4. El Rey de la Selva</strong><br>
                • Solo puede tener 1 dinosaurio<br>
                • 7 puntos si ningún otro jugador tiene más dinosaurios de ese tipo
            </div>
            
            <div class="zone-example">
                <strong>5. El Prado de la Diferencia</strong><br>
                • Máximo 6 dinosaurios TODOS DIFERENTES<br>
                • Puntos: 1=1pt, 2=3pts, 3=6pts, 4=10pts, 5=15pts, 6=21pts
            </div>
            
            <div class="zone-example">
                <strong>6. La Isla Solitaria</strong><br>
                • Solo puede tener 1 dinosaurio<br>
                • 7 puntos si es el ÚNICO de su especie en todo tu tablero
            </div>
            
            <div class="zone-example">
                <strong>7. El Rio</strong><br>
                • Sin restricciones del dado<br>
                • Cada dinosaurio vale 1 punto
            </div>
            
            <h3>🔄 El Draft</h3>
            <p>Después de que todos los jugadores coloquen un dinosaurio, las bandejas rotan en sentido horario. Recibes los dinosaurios del jugador de tu izquierda.</p>
            
            <h3>🏆 Final del Juego</h3>
            <p>Después de 2 rondas completas, se cuentan los puntos de todas las zonas. ¡El jugador con más puntos gana!</p>
            
            <div class="tutorial-nav">
                <button class="tutorial-btn secondary" onclick="closeTutorial()">Cerrar</button>
                <button class="tutorial-btn primary" onclick="startFromTutorial()">¡Entendido, jugar!</button>
            </div>
        </div>
    </div>

    <!-- Help button -->
    <button class="help-btn" onclick="openTutorial()" title="Ayuda">❓</button>
    
    <!-- Sound toggle -->
    <button class="sound-toggle" id="sound-toggle" onclick="toggleSound()" title="Sonido">🔊</button>

    <audio id="dice-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFgn" type="audio/wav">
    </audio>
    <audio id="place-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFgn" type="audio/wav">
    </audio>
    <audio id="draft-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFgn" type="audio/wav">
    </audio>
    <audio id="win-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFgn" type="audio/wav">
    </audio>
    <div class="game-container">
        <div class="player-card" id="player1">
            <div class="player-name">Jugador 1</div>
            <div class="player-score">Puntos: <span id="score1">0</span></div>
        </div>
        <div class="player-card" id="player2">
            <div class="player-name">Jugador 2</div>
            <div class="player-score">Puntos: <span id="score2">0</span></div>
        </div>
        <div class="player-card" id="player3">
            <div class="player-name">Jugador 3</div>
            <div class="player-score">Puntos: <span id="score3">0</span></div>
        </div>
        <div class="player-card" id="player4">
            <div class="player-name">Jugador 4</div>
            <div class="player-score">Puntos: <span id="score4">0</span></div>
        </div>
        <div class="player-card" id="player5">
            <div class="player-name">Jugador 5</div>
            <div class="player-score">Puntos: <span id="score5">0</span></div>
        </div>

        <div class="board-container" id="board">
            <div class="board-bg"></div>
        </div>

        <!-- Contenedor para los botones de ayuda -->
        <div class="buttons-container">
            <button class="help-btn" onclick="openTutorial()" title="Ayuda">❓</button>
            <button class="sound-toggle" id="sound-toggle" onclick="toggleSound()" title="Sonido">🔊</button>
            <button class="records-btn" onclick="openRecords()" title="Récords">🏆</button>
        </div>

        <div class="dino-panel">
            <h3>Tus Dinosaurios</h3>
            <div class="dino-grid" id="dino-grid"></div>
        </div>

        <div class="dice-container">
            <button class="edit-btn" id="edit-btn" onclick="toggleEditMode()">Editar Zonas</button>
            <div class="round-info" id="round-info">Ronda: 1</div>
            <div class="turn-info" id="turn-info">Turno: Jugador 1</div>
            <div class="turn-status" id="turn-status">Esperando dado...</div>
            <div class="dice-small" id="dice-small" onclick="rollDice()">🎲</div>
            <div class="dice-info" id="dice-info" style="display: none;">Sin restricción</div>
        </div>
    </div>

    <script>
        const DINO_TYPES = [
            { id: 1, name: 'T-Rex', emoji: '🦖', points: 1 },
            { id: 2, name: 'Triceratops', emoji: '🦕', points: 1 },
            { id: 3, name: 'Velociraptor', emoji: '🦎', points: 1 },
            { id: 4, name: 'Brachiosaurus', emoji: '🦘', points: 1 },
            { id: 5, name: 'Pterodactyl', emoji: '🦅', points: 1 },
            { id: 6, name: 'Stegosaurus', emoji: '🐊', points: 1 },
        ];

        const ZONES = [
            { id: 1, name: 'Bosque de la Semejanza', left: '5%', top: '5%', width: '25%', height: '35%' },
            { id: 2, name: 'El Trio Frondoso', left: '38%', top: '5%', width: '25%', height: '35%' },
            { id: 3, name: 'La Pradera del Amor', left: '70%', top: '5%', width: '25%', height: '35%' },
            { id: 4, name: 'El Rey de la Selva', left: '5%', top: '50%', width: '25%', height: '35%' },
            { id: 5, name: 'El Prado de la Diferencia', left: '38%', top: '50%', width: '25%', height: '35%' },
            { id: 6, name: 'La Isla Solitaria', left: '70%', top: '50%', width: '25%', height: '35%' },
            { id: 7, name: 'El Rio', left: '14%', top: '88%', width: '72%', height: '8%' },
        ];

        const DICE_RESTRICTIONS = {
            1: [4, 5, 6], // Solo rey, prado, isla
            2: [1, 2, 4], // Solo bosque, trio, rey
            3: 'empty',   // Solo donde no hay dinos
            4: [3, 5, 6], // Solo pradera, prado, isla
            5: [1, 2, 3], // Solo bosque, trio, pradera
            6: 'no-trex'  // Donde no haya T-Rex
        };

        let totalPlayers = 2;
        let currentPlayer = 1;
        let currentRound = 1;
        let currentTurn = 1;
        let hasRolledDice = false;
        let currentRestriction = null;
        let diceRollerPlayer = null;
        let playerDinos = {};
        let playerBoards = {};
        let draggedDino = null;
        let editMode = false;
        let movingZone = null;
        let resizingZone = null;
        let startX, startY, startLeft, startTop, startWidth, startHeight;
        let soundEnabled = true;
        let tutorialShown = false;
        let playerNames = {};
        let gameRecords = [];

        function selectPlayers(num) {
            totalPlayers = num;
            document.querySelectorAll('.player-btn').forEach(btn => btn.classList.remove('selected'));
            event.target.classList.add('selected');
            
            // Mostrar/ocultar inputs de nombres
            for (let i = 1; i <= 5; i++) {
                const input = document.getElementById(`name${i}`);
                if (input) {
                    input.style.display = i <= num ? 'block' : 'none';
                }
            }
        }

        function startGame() {
            // Guardar nombres de jugadores
            for (let i = 1; i <= totalPlayers; i++) {
                const input = document.getElementById(`name${i}`);
                playerNames[i] = input.value.trim() || `Jugador ${i}`;
            }
            
            document.getElementById('setup-overlay').classList.remove('active');
            
            // Mostrar tutorial si es la primera vez
            if (!tutorialShown) {
                tutorialShown = true;
                setTimeout(() => {
                    document.getElementById('tutorial-overlay').classList.add('active');
                }, 500);
            }
            
            // Mostrar jugadores según cantidad
            for (let i = 1; i <= 5; i++) {
                const card = document.getElementById(`player${i}`);
                card.style.display = i <= totalPlayers ? 'block' : 'none';
                
                // Actualizar nombre en la tarjeta
                const nameEl = card.querySelector('.player-name');
                if (nameEl && playerNames[i]) {
                    nameEl.textContent = playerNames[i];
                }
            }

            // Inicializar dinosaurios y tableros
            for (let i = 1; i <= totalPlayers; i++) {
                playerDinos[i] = generateRandomDinos(6);
                playerBoards[i] = {};
                ZONES.forEach(z => playerBoards[i][z.id] = []);
            }

            // Reiniciar variables de turno
            playersWhoPlacedThisTurn = new Set();
            hasRolledDice = false;
            currentRestriction = null;
            diceRollerPlayer = null;

            // Cargar récords
            loadRecords();

            renderZones();
            renderDinoGrid();
            updateTurnInfo();
            setActivePlayer(1);
            
            // Mensaje inicial
            setTimeout(() => {
                const firstPlayerName = playerNames[1] || 'Jugador 1';
            }, 500);
        }

        function openTutorial() {
            document.getElementById('tutorial-overlay').classList.add('active');
        }

        function closeTutorial() {
            document.getElementById('tutorial-overlay').classList.remove('active');
        }

        function startFromTutorial() {
            closeTutorial();
        }

        function generateRandomDinos(count) {
            const dinos = [];
            for (let i = 0; i < count; i++) {
                const randomDino = DINO_TYPES[Math.floor(Math.random() * DINO_TYPES.length)];
                dinos.push({ ...randomDino, uniqueId: Date.now() + Math.random() });
            }
            return dinos;
        }

        function renderZones() {
            const board = document.getElementById('board');
            board.querySelectorAll('.zone').forEach(el => el.remove());

            ZONES.forEach(zone => {
                const zoneEl = document.createElement('div');
                zoneEl.className = 'zone';
                zoneEl.style.left = zone.left;
                zoneEl.style.top = zone.top;
                zoneEl.style.width = zone.width;
                zoneEl.style.height = zone.height;
                zoneEl.dataset.zoneId = zone.id;

                const label = document.createElement('div');
                label.className = 'zone-label';
                label.textContent = zone.name;
                
                // Marcar restricción
                if (isZoneRestricted(zone.id)) {
                    zoneEl.classList.add('restricted');
                    label.textContent += ' 🚫';
                }
                
                zoneEl.appendChild(label);

                // Handle para redimensionar
                const handle = document.createElement('div');
                handle.className = 'zone-handle';
                zoneEl.appendChild(handle);

                if (!editMode) {
                    // Modo juego: drag & drop de dinos
                    zoneEl.addEventListener('dragover', e => {
                        e.preventDefault();
                        if (!isZoneRestricted(zone.id)) {
                            zoneEl.classList.add('drag-over');
                        }
                    });

                    zoneEl.addEventListener('dragleave', () => {
                        zoneEl.classList.remove('drag-over');
                    });

                    zoneEl.addEventListener('drop', e => {
                        e.preventDefault();
                        zoneEl.classList.remove('drag-over');
                        handleDrop(zone.id);
                    });
                } else {
                    // Modo edición: mover y redimensionar zonas
                    setupZoneEditing(zoneEl, zone, handle);
                }

                // Render dinos ya colocados
                if (playerBoards[currentPlayer]) {
                    playerBoards[currentPlayer][zone.id].forEach(dino => {
                        const dinoEl = document.createElement('div');
                        dinoEl.className = 'zone-dino';
                        dinoEl.textContent = dino.emoji;
                        dinoEl.title = dino.name;
                        zoneEl.appendChild(dinoEl);
                    });
                }

                board.appendChild(zoneEl);
            });

            // Añadir/quitar clase edit-mode al board
            if (editMode) {
                board.classList.add('edit-mode');
            } else {
                board.classList.remove('edit-mode');
            }
        }

        function setupZoneEditing(zoneEl, zone, handle) {
            const board = document.getElementById('board');

            // Mover zona
            zoneEl.addEventListener('pointerdown', e => {
                if (e.target === handle) return;
                e.preventDefault();
                
                movingZone = zone;
                zoneEl.classList.add('moving');
                
                const boardRect = board.getBoundingClientRect();
                const zoneRect = zoneEl.getBoundingClientRect();
                
                startX = e.clientX;
                startY = e.clientY;
                startLeft = zoneRect.left - boardRect.left;
                startTop = zoneRect.top - boardRect.top;
                
                window.addEventListener('pointermove', onZoneMove);
                window.addEventListener('pointerup', onZoneMoveEnd);
            });

            // Redimensionar zona
            handle.addEventListener('pointerdown', e => {
                e.preventDefault();
                e.stopPropagation();
                
                resizingZone = zone;
                zoneEl.classList.add('resizing');
                
                const boardRect = board.getBoundingClientRect();
                const zoneRect = zoneEl.getBoundingClientRect();
                
                startX = e.clientX;
                startY = e.clientY;
                startLeft = zoneRect.left - boardRect.left;
                startTop = zoneRect.top - boardRect.top;
                startWidth = zoneRect.width;
                startHeight = zoneRect.height;
                
                window.addEventListener('pointermove', onZoneResize);
                window.addEventListener('pointerup', onZoneResizeEnd);
            });
        }

        function onZoneMove(e) {
            if (!movingZone) return;
            
            const board = document.getElementById('board');
            const boardRect = board.getBoundingClientRect();
            
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            
            let newLeft = startLeft + dx;
            let newTop = startTop + dy;
            
            // Límites
            newLeft = Math.max(0, Math.min(newLeft, boardRect.width - 100));
            newTop = Math.max(0, Math.min(newTop, boardRect.height - 100));
            
            movingZone.left = `${(newLeft / boardRect.width * 100).toFixed(1)}%`;
            movingZone.top = `${(newTop / boardRect.height * 100).toFixed(1)}%`;
            
            const zoneEl = document.querySelector(`.zone[data-zone-id="${movingZone.id}"]`);
            if (zoneEl) {
                zoneEl.style.left = movingZone.left;
                zoneEl.style.top = movingZone.top;
            }
        }

        function onZoneMoveEnd() {
            if (movingZone) {
                const zoneEl = document.querySelector(`.zone[data-zone-id="${movingZone.id}"]`);
                if (zoneEl) zoneEl.classList.remove('moving');
                movingZone = null;
            }
            
            window.removeEventListener('pointermove', onZoneMove);
            window.removeEventListener('pointerup', onZoneMoveEnd);
        }

        function onZoneResize(e) {
            if (!resizingZone) return;
            
            const board = document.getElementById('board');
            const boardRect = board.getBoundingClientRect();
            
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            
            let newWidth = startWidth + dx;
            let newHeight = startHeight + dy;
            
            // Límites mínimos y máximos
            newWidth = Math.max(50, Math.min(newWidth, boardRect.width - startLeft));
            newHeight = Math.max(50, Math.min(newHeight, boardRect.height - startTop));
            
            resizingZone.width = `${(newWidth / boardRect.width * 100).toFixed(1)}%`;
            resizingZone.height = `${(newHeight / boardRect.height * 100).toFixed(1)}%`;
            
            const zoneEl = document.querySelector(`.zone[data-zone-id="${resizingZone.id}"]`);
            if (zoneEl) {
                zoneEl.style.width = resizingZone.width;
                zoneEl.style.height = resizingZone.height;
            }
        }

        function onZoneResizeEnd() {
            if (resizingZone) {
                const zoneEl = document.querySelector(`.zone[data-zone-id="${resizingZone.id}"]`);
                if (zoneEl) zoneEl.classList.remove('resizing');
                resizingZone = null;
            }
            
            window.removeEventListener('pointermove', onZoneResize);
            window.removeEventListener('pointerup', onZoneResizeEnd);
        }

        function toggleEditMode() {
            editMode = !editMode;
            const btn = document.getElementById('edit-btn');
            btn.classList.toggle('active', editMode);
            btn.textContent = editMode ? 'Modo Juego' : 'Editar Zonas';
            
            renderZones();
            
            if (editMode) {
                showMessage('✏️ Modo edición activado - Arrastra para mover, usa el handle para redimensionar');
            } else {
                showMessage('🎮 Modo juego activado');
            }
        }

        function isZoneRestricted(zoneId) {
            if (!currentRestriction || currentPlayer === diceRollerPlayer) return false;
            if (zoneId === 7) return false; // El río nunca tiene restricción

            const restriction = DICE_RESTRICTIONS[currentRestriction];
            
            if (Array.isArray(restriction)) {
                return !restriction.includes(zoneId);
            }
            
            if (restriction === 'empty') {
                return playerBoards[currentPlayer][zoneId].length > 0;
            }
            
            if (restriction === 'no-trex') {
                return playerBoards[currentPlayer][zoneId].some(d => d.id === 1);
            }
            
            return false;
        }

        function canPlaceDinoInZone(zoneId, dino) {
            const zone = playerBoards[currentPlayer][zoneId];
            
            // Zona 1: Bosque de la Semejanza
            if (zoneId === 1) {
                if (zone.length >= 6) return false;
                if (zone.length > 0 && zone[0].id !== dino.id) return false;
            }
            
            // Zona 2: El Trio Frondoso
            if (zoneId === 2) {
                if (zone.length >= 3) return false;
            }
            
            // Zona 4: El Rey de la Selva
            if (zoneId === 4) {
                if (zone.length >= 1) return false;
            }
            
            // Zona 5: El Prado de la Diferencia
            if (zoneId === 5) {
                if (zone.length >= 6) return false;
                if (zone.some(d => d.id === dino.id)) return false;
            }
            
            // Zona 6: La Isla Solitaria
            if (zoneId === 6) {
                if (zone.length >= 1) return false;
            }
            
            return true;
        }

        function renderDinoGrid() {
            const container = document.getElementById('dino-grid');
            container.innerHTML = '';
            
            const dinos = playerDinos[currentPlayer] || [];
            
            dinos.forEach((dino, index) => {
                const item = document.createElement('div');
                item.className = 'dino-item';
                item.draggable = true;
                item.textContent = dino.emoji;
                item.title = dino.name;

                item.addEventListener('dragstart', e => {
                    draggedDino = { ...dino, playerIndex: index };
                    item.style.opacity = '0.5';
                });

                item.addEventListener('dragend', () => {
                    draggedDino = null;
                    item.style.opacity = '1';
                });

                container.appendChild(item);
            });
        }

        function handleDrop(zoneId) {
            if (!draggedDino) return;
            
            if (isZoneRestricted(zoneId)) {
                showMessage('🚫 Esta zona está restringida por el dado');
                return;
            }
            
            if (!canPlaceDinoInZone(zoneId, draggedDino)) {
                showMessage('❌ No puedes colocar este dinosaurio aquí');
                return;
            }
            
            // Verificar si se tiró el dado
            if (!hasRolledDice) {
                showMessage('⚠️ Primero debes tirar el dado');
                return;
            }
            
            // Verificar si este jugador ya colocó
            if (playersWhoPlacedThisTurn.has(currentPlayer)) {
                showMessage('⚠️ Ya colocaste tu dinosaurio este turno');
                return;
            }
            
            playSound('place-sound');
            
            // Colocar dinosaurio
            playerBoards[currentPlayer][zoneId].push(draggedDino);
            playerDinos[currentPlayer].splice(draggedDino.playerIndex, 1);
            
            playersWhoPlacedThisTurn.add(currentPlayer);
            
            const zoneName = ZONES.find(z => z.id === zoneId).name;
            const playerName = playerNames[currentPlayer] || `Jugador ${currentPlayer}`;
            showMessage(`✅ ${playerName}: ${draggedDino.name} → ${zoneName}`);
            
            renderZones();
            renderDinoGrid();
            updateTurnInfo();
            
            // Verificar si todos los jugadores colocaron su dinosaurio
            if (playersWhoPlacedThisTurn.size === totalPlayers) {
                // Todos colocaron, hacer draft
                showMessage('🔄 Todos colocaron. Iniciando Draft...');
                setTimeout(() => {
                    performDraft();
                }, 1500);
            } else {
                // Pasar al siguiente jugador
                setTimeout(() => {
                    nextTurn();
                }, 800);
            }
        }

        function rollDice() {
            // Solo puede tirar el dado si ningún jugador ha colocado aún (inicio de turno)
            if (playersWhoPlacedThisTurn.size > 0) {
                showMessage('⚠️ Ya se tiró el dado este turno. Todos deben colocar primero.');
                return;
            }
            
            playSound('dice-sound');
            
            // Animación del dado
            const diceEl = document.getElementById('dice-small');
            diceEl.style.animation = 'spin 0.5s ease';
            
            setTimeout(() => {
                const result = Math.floor(Math.random() * 6) + 1;
                const diceEmojis = ['🎲', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣'];
                diceEl.textContent = diceEmojis[result];
                diceEl.style.animation = '';
                
                currentRestriction = result;
                diceRollerPlayer = currentPlayer;
                hasRolledDice = true;
                
                // Registrar que el jugador que tiró el dado ya puede colocar
                // (No agregarlo al Set aún, solo marcar que el dado fue tirado)
                
                const restrictionMsg = getRestrictionMessage(result);
                const playerName = playerNames[currentPlayer] || `Jugador ${currentPlayer}`;
                showMessage(`🎲 ${playerName} lanzó el dado: ${result}`);
                
                setTimeout(() => {
                    showMessage(`📋 ${restrictionMsg}`);
                    setTimeout(() => {
                        showMessage(`🦖 ${playerName}, coloca tu dinosaurio`);
                    }, 1500);
                }, 1000);
                
                renderZones();
                updateTurnInfo();
            }, 500);
        }

        function getRestrictionMessage(dice) {
            switch(dice) {
                case 1: return 'Solo Rey de la Selva, Prado de la Diferencia e Isla Solitaria';
                case 2: return 'Solo Bosque de la Semejanza, Trio Frondoso y Rey de la Selva';
                case 3: return 'Solo en zonas vacías (sin dinosaurios)';
                case 4: return 'Solo Pradera del Amor, Prado de la Diferencia e Isla Solitaria';
                case 5: return 'Solo Bosque de la Semejanza, Trio Frondoso y Pradera del Amor';
                case 6: return 'Solo en zonas sin T-Rex previo';
                default: return '';
            }
        }

        function nextTurn() {
            // Solo avanzar al siguiente jugador si es necesario
            currentPlayer = (currentPlayer % totalPlayers) + 1;
            
            setActivePlayer(currentPlayer);
            renderDinoGrid();
            renderZones();
            updateTurnInfo();
            
            // Anunciar turno del jugador
            const playerName = playerNames[currentPlayer] || `Jugador ${currentPlayer}`;
            if (playersWhoPlacedThisTurn.has(currentPlayer)) {
                showMessage(`⏭️ ${playerName} ya colocó, esperando...`);
            } else {
                showMessage(`➡️ Turno de ${playerName}`);
            }
        }

        function performDraft() {
            // Verificar si quedan dinosaurios
            const anyDinosLeft = Object.values(playerDinos).some(dinos => dinos.length > 0);
            
            if (!anyDinosLeft) {
                currentRound++;
                
                if (currentRound > 2) {
                    endGame();
                    return;
                }
                
                // Nueva ronda con nuevos dinosaurios
                for (let i = 1; i <= totalPlayers; i++) {
                    playerDinos[i] = generateRandomDinos(6);
                }
                
                showMessage(`🔄 ¡Ronda ${currentRound} comienza!`);
                
                // Reiniciar variables de turno
                currentTurn = 1;
                currentPlayer = 1;
                currentRestriction = null;
                diceRollerPlayer = null;
                hasRolledDice = false;
                playersWhoPlacedThisTurn.clear();
                
                setActivePlayer(1);
                renderDinoGrid();
                renderZones();
                updateTurnInfo();
                return;
            }
            
            showDraftAnimation();
            
            setTimeout(() => {
                // Rotar dinosaurios (sentido horario)
                const temp = {};
                for (let i = 1; i <= totalPlayers; i++) {
                    temp[i] = [...playerDinos[i]];
                }
                
                for (let i = 1; i <= totalPlayers; i++) {
                    const prevPlayer = i === 1 ? totalPlayers : i - 1;
                    playerDinos[i] = temp[prevPlayer];
                }
                
                completeDraft();
            }, 2500);
        }

        function showDraftAnimation() {
            playSound('draft-sound');
            
            const overlay = document.getElementById('draft-overlay');
            const container = document.getElementById('draft-container');
            container.innerHTML = '';
            
            // Crear bandejas en círculo
            const angleStep = (Math.PI * 2) / totalPlayers;
            const radius = 250;
            
            for (let i = 1; i <= totalPlayers; i++) {
                const tray = document.createElement('div');
                tray.className = 'tray';
                tray.id = `tray-${i}`;
                
                // Posicionar en círculo
                const angle = angleStep * (i - 1) - Math.PI / 2;
                const x = Math.cos(angle) * radius;
                const y = Math.sin(angle) * radius;
                
                tray.style.position = 'absolute';
                tray.style.transform = `translate(${x}px, ${y}px)`;
                
                const title = document.createElement('div');
                title.className = 'tray-title';
                title.textContent = playerNames[i] || `Jugador ${i}`;
                tray.appendChild(title);
                
                const dinosContainer = document.createElement('div');
                dinosContainer.className = 'tray-dinos';
                
                playerDinos[i].forEach(dino => {
                    const dinoEl = document.createElement('div');
                    dinoEl.className = 'tray-dino';
                    dinoEl.textContent = dino.emoji;
                    dinosContainer.appendChild(dinoEl);
                });
                
                tray.appendChild(dinosContainer);
                container.appendChild(tray);
            }
            
            overlay.classList.add('active');
            
            // Animar rotación después de un momento
            setTimeout(() => {
                for (let i = 1; i <= totalPlayers; i++) {
                    const tray = document.getElementById(`tray-${i}`);
                    const nextAngle = angleStep * i - Math.PI / 2;
                    const nextX = Math.cos(nextAngle) * radius;
                    const nextY = Math.sin(nextAngle) * radius;
                    
                    tray.style.transition = 'transform 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                    tray.style.transform = `translate(${nextX}px, ${nextY}px) rotate(360deg)`;
                }
            }, 100);
        }

        function completeDraft() {
            document.getElementById('draft-overlay').classList.remove('active');
            
            // Verificar si terminó la ronda
            const allEmpty = Object.values(playerDinos).every(dinos => dinos.length === 0);
            
            if (allEmpty) {
                currentRound++;
                
                if (currentRound > 2) {
                    endGame();
                    return;
                }
                
                // Nueva ronda
                for (let i = 1; i <= totalPlayers; i++) {
                    playerDinos[i] = generateRandomDinos(6);
                }
                
                showMessage(`🎉 ¡Ronda ${currentRound} comienza!`);
            }
            
            // Reiniciar turno - el siguiente jugador después del que tiró el dado
            const nextPlayer = diceRollerPlayer ? ((diceRollerPlayer % totalPlayers) + 1) : 1;
            
            currentTurn = 1;
            currentPlayer = nextPlayer;
            currentRestriction = null;
            diceRollerPlayer = null;
            hasRolledDice = false;
            playersWhoPlacedThisTurn.clear();
            
            setActivePlayer(currentPlayer);
            renderDinoGrid();
            renderZones();
            updateTurnInfo();
            
            const nextPlayerName = playerNames[currentPlayer] || `Jugador ${currentPlayer}`;
            setTimeout(() => {
                showMessage(`➡️ Turno de ${nextPlayerName} - ¡Tira el dado!`);
            }, 500);
        }

        function calculateScore(playerId) {
            let total = 0;
            const board = playerBoards[playerId];
            
            // Zona 1: Bosque de la Semejanza (mismo tipo)
            const zone1 = board[1];
            const zone1Points = [0, 2, 4, 8, 12, 18, 24];
            total += zone1Points[zone1.length] || 0;
            
            // Zona 2: El Trio Frondoso (exactamente 3)
            const zone2 = board[2];
            if (zone2.length === 3) total += 7;
            
            // Zona 3: La Pradera del Amor (parejas)
            const zone3 = board[3];
            const typeCounts3 = {};
            zone3.forEach(d => typeCounts3[d.id] = (typeCounts3[d.id] || 0) + 1);
            Object.values(typeCounts3).forEach(count => {
                total += Math.floor(count / 2) * 5;
            });
            
            // Zona 4: El Rey de la Selva (7 pts si nadie tiene más de ese tipo)
            const zone4 = board[4];
            if (zone4.length === 1) {
                const dinoType = zone4[0].id;
                let hasMore = false;
                
                for (let i = 1; i <= totalPlayers; i++) {
                    if (i === playerId) continue;
                    const otherCount = Object.values(playerBoards[i])
                        .flat()
                        .filter(d => d.id === dinoType).length;
                    const myCount = Object.values(board)
                        .flat()
                        .filter(d => d.id === dinoType).length;
                    
                    if (otherCount > myCount) {
                        hasMore = true;
                        break;
                    }
                }
                
                if (!hasMore) total += 7;
            }
            
            // Zona 5: El Prado de la Diferencia (todos diferentes)
            const zone5 = board[5];
            const zone5Points = [0, 1, 3, 6, 10, 15, 21];
            total += zone5Points[zone5.length] || 0;
            
            // Zona 6: La Isla Solitaria (único de su especie)
            const zone6 = board[6];
            if (zone6.length === 1) {
                const dinoType = zone6[0].id;
                const totalOfType = Object.values(board)
                    .flat()
                    .filter(d => d.id === dinoType).length;
                
                if (totalOfType === 1) total += 7;
            }
            
            // Zona 7: El Rio (1 punto por dino)
            const zone7 = board[7];
            total += zone7.length;
            
            return total;
        }

        function endGame() {
            playSound('win-sound');
            createConfetti();
            
            const results = [];
            
            for (let i = 1; i <= totalPlayers; i++) {
                const score = calculateScore(i);
                const breakdown = getScoreBreakdown(i);
                results.push({ 
                    player: i, 
                    name: playerNames[i] || `Jugador ${i}`,
                    score, 
                    breakdown 
                });
                document.getElementById(`score${i}`).textContent = score;
            }
            
            results.sort((a, b) => b.score - a.score);
            
            // Guardar récord del ganador
            saveRecord(results[0].name, results[0].score);
            
            const table = document.getElementById('results-table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Jugador</th>
                        <th>Puntos</th>
                        <th>Desglose</th>
                    </tr>
                </thead>
                <tbody>
                    ${results.map((r, i) => `
                        <tr class="${i === 0 ? 'winner' : ''}">
                            <td>${i === 0 ? '🏆' : i === 1 ? '🥈' : i === 2 ? '🥉' : ''} ${i + 1}º</td>
                            <td>${r.name}</td>
                            <td><strong>${r.score}</strong> puntos</td>
                            <td style="font-size: 0.85rem; text-align: left;">
                                ${r.breakdown.map(b => `${b.zone}: ${b.points}pts`).join('<br>')}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            `;
            
            document.getElementById('results-overlay').classList.add('active');
        }

        function loadRecords() {
            try {
                const saved = localStorage.getItem('draftosaurus_records');
                if (saved) {
                    gameRecords = JSON.parse(saved);
                }
            } catch (e) {
                gameRecords = [];
            }
        }

        function saveRecord(name, score) {
            gameRecords.push({
                name,
                score,
                date: new Date().toLocaleDateString()
            });
            
            // Ordenar y mantener solo top 10
            gameRecords.sort((a, b) => b.score - a.score);
            gameRecords = gameRecords.slice(0, 10);
            
            try {
                localStorage.setItem('draftosaurus_records', JSON.stringify(gameRecords));
            } catch (e) {
                console.error('No se pudo guardar el récord');
            }
        }

        function openRecords() {
            loadRecords();
            const list = document.getElementById('records-list');
            
            if (gameRecords.length === 0) {
                list.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">No hay récords todavía. ¡Sé el primero!</p>';
            } else {
                list.innerHTML = gameRecords.map((record, i) => `
                    <div class="record-item ${i === 0 ? 'top' : ''}">
                        <div>
                            <div class="record-name">${i + 1}. ${record.name}</div>
                            <div style="font-size: 0.9rem; opacity: 0.8;">${record.date}</div>
                        </div>
                        <div class="record-score">${record.score} pts</div>
                    </div>
                `).join('');
            }
            
            document.getElementById('records-overlay').classList.add('active');
        }

        function closeRecords() {
            document.getElementById('records-overlay').classList.remove('active');
        }

        function clearRecords() {
            if (confirm('¿Estás seguro de que quieres borrar todos los récords?')) {
                gameRecords = [];
                localStorage.removeItem('draftosaurus_records');
                openRecords();
            }
        }

        function createConfetti() {
            const colors = ['#FFD700', '#FFA500', '#FF6347', '#4CAF50', '#667eea', '#FF69B4'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animation = `confetti ${2 + Math.random() * 3}s linear`;
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 5000);
                }, i * 30);
            }
        }

        function getScoreBreakdown(playerId) {
            const board = playerBoards[playerId];
            const breakdown = [];
            
            // Zona 1
            const zone1 = board[1];
            const zone1Points = [0, 2, 4, 8, 12, 18, 24];
            breakdown.push({ zone: 'Bosque', points: zone1Points[zone1.length] || 0 });
            
            // Zona 2
            const zone2 = board[2];
            breakdown.push({ zone: 'Trio', points: zone2.length === 3 ? 7 : 0 });
            
            // Zona 3
            const zone3 = board[3];
            const typeCounts3 = {};
            zone3.forEach(d => typeCounts3[d.id] = (typeCounts3[d.id] || 0) + 1);
            let zone3Points = 0;
            Object.values(typeCounts3).forEach(count => {
                zone3Points += Math.floor(count / 2) * 5;
            });
            breakdown.push({ zone: 'Pradera', points: zone3Points });
            
            // Zona 4
            const zone4 = board[4];
            let zone4Points = 0;
            if (zone4.length === 1) {
                const dinoType = zone4[0].id;
                let hasMore = false;
                for (let i = 1; i <= totalPlayers; i++) {
                    if (i === playerId) continue;
                    const otherCount = Object.values(playerBoards[i]).flat().filter(d => d.id === dinoType).length;
                    const myCount = Object.values(board).flat().filter(d => d.id === dinoType).length;
                    if (otherCount > myCount) {
                        hasMore = true;
                        break;
                    }
                }
                if (!hasMore) zone4Points = 7;
            }
            breakdown.push({ zone: 'Rey', points: zone4Points });
            
            // Zona 5
            const zone5 = board[5];
            const zone5Points = [0, 1, 3, 6, 10, 15, 21];
            breakdown.push({ zone: 'Prado', points: zone5Points[zone5.length] || 0 });
            
            // Zona 6
            const zone6 = board[6];
            let zone6Points = 0;
            if (zone6.length === 1) {
                const dinoType = zone6[0].id;
                const totalOfType = Object.values(board).flat().filter(d => d.id === dinoType).length;
                if (totalOfType === 1) zone6Points = 7;
            }
            breakdown.push({ zone: 'Isla', points: zone6Points });
            
            // Zona 7
            const zone7 = board[7];
            breakdown.push({ zone: 'Rio', points: zone7.length });
            
            return breakdown;
        }

        function resetGame() {
            currentPlayer = 1;
            currentRound = 1;
            currentTurn = 1;
            hasRolledDice = false;
            currentRestriction = null;
            diceRollerPlayer = null;
            playerDinos = {};
            playerBoards = {};
            playerNames = {};
            playersWhoPlacedThisTurn.clear();
            
            // Resetear dado visual
            document.getElementById('dice-small').textContent = '🎲';
            
            // Resetear nombres en inputs
            for (let i = 1; i <= 5; i++) {
                const input = document.getElementById(`name${i}`);
                if (input) input.value = '';
            }
            
            document.getElementById('results-overlay').classList.remove('active');
            document.getElementById('setup-overlay').classList.add('active');
        }

        function goToMenu() {
            // Si hay un menu.php, redirigir
            if (window.location.pathname.includes('.php')) {
                window.location.href = 'menu.php';
            } else {
                // Si no, simplemente recargar
                window.location.reload();
            }
        }

        function setActivePlayer(player) {
            document.querySelectorAll('.player-card').forEach(c => c.classList.remove('active'));
            document.getElementById(`player${player}`).classList.add('active');
        }

        function updateTurnInfo() {
            const name = playerNames[currentPlayer] || `Jugador ${currentPlayer}`;
            document.getElementById('turn-info').textContent = `Turno: ${name}`;
            document.getElementById('round-info').textContent = `Ronda: ${currentRound}`;
            
            // Actualizar estado del turno
            const statusEl = document.getElementById('turn-status');
            const diceInfoEl = document.getElementById('dice-info');
            
            if (!statusEl || !diceInfoEl) return;
            
            if (playersWhoPlacedThisTurn.size === 0 && !hasRolledDice) {
                // Nadie ha colocado y no se tiró el dado
                statusEl.textContent = '🎲 Tira el dado para comenzar';
                statusEl.style.background = 'rgba(255, 165, 0, .6)';
                diceInfoEl.style.display = 'none';
            } else if (playersWhoPlacedThisTurn.size === 0 && hasRolledDice) {
                // Se tiró el dado pero nadie ha colocado aún
                statusEl.textContent = '🦖 Coloca tu dinosaurio';
                statusEl.style.background = 'rgba(102, 126, 234, .6)';
            } else if (playersWhoPlacedThisTurn.has(currentPlayer)) {
                // Jugador actual ya colocó
                statusEl.textContent = `✅ Esperando otros jugadores (${playersWhoPlacedThisTurn.size}/${totalPlayers})`;
                statusEl.style.background = 'rgba(76, 175, 80, .6)';
            } else {
                // Jugador debe colocar dinosaurio
                statusEl.textContent = '🦖 Coloca tu dinosaurio';
                statusEl.style.background = 'rgba(102, 126, 234, .6)';
            }
            
            // Mostrar info de restricción
            if (currentRestriction && diceRollerPlayer) {
                const rollerName = playerNames[diceRollerPlayer] || `Jugador ${diceRollerPlayer}`;
                diceInfoEl.style.display = 'block';
                
                if (currentPlayer === diceRollerPlayer) {
                    diceInfoEl.textContent = '✓ Sin restricción';
                    diceInfoEl.className = 'dice-info no-restriction';
                } else {
                    diceInfoEl.textContent = `🚫 Dado ${currentRestriction} (${rollerName})`;
                    diceInfoEl.className = 'dice-info';
                }
            } else {
                diceInfoEl.style.display = 'none';
            }
        }

        function showMessage(text) {
            const exist = document.querySelector('.status-message');
            if (exist) exist.remove();
            
            const msg = document.createElement('div');
            msg.className = 'status-message';
            msg.textContent = text;
            document.body.appendChild(msg);
            
            setTimeout(() => msg.remove(), 3000);
        }
    </script>
</body>
</html>