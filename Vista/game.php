<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Juego Dinosaurios</title>
    <style>
        * {
            box-sizing: border-box;
            font-size: x-small;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url("../img/fondo.png") no-repeat center center fixed;
            background-size: cover;
            overflow: hidden;
        }

        .game-container {
            display: grid;
            grid-template-columns: 250px 1fr 250px;
            grid-template-rows: 150px 1fr 200px;
            height: 100vh;
            gap: 10px;
            padding: 10px;
        }

        .player-card {
            background: linear-gradient(145deg, #fff, #e6e6e6);
            padding: 15px;
            border: 2px solid #999;
            border-radius: 12px;
            height: 150px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            font-weight: bold;
            transition: .3s ease;
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

        .board-container {
            grid-column: 2;
            grid-row: 1 / 4;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }

        .board-bg,
        .board-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .board-img {
            object-fit: contain;
            image-rendering: auto;
            z-index: 1;
            pointer-events: none;
        }

        .dino-panel {
            grid-column: 1;
            grid-row: 3;
            background: url('../img/bandeja.png') no-repeat center center;
            background-size: 200% 180%;
            padding: 20px;
            border-radius: 12px;
            overflow: visible;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .dino-panel h3 {
            margin: 0 0 15px;
            font-size: 1.1rem;
            color: #333;
            text-align: center;
            font-weight: bold;
        }

        .dino-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            flex: 1;
        }

        .dino-item {
            display: flex;
            align-items: center;
            cursor: grab;
            gap: 5px;
            transition: .2s ease;
            aspect-ratio: 1;
            background: transparent;
            flex-direction: column;
        }

        .dino-item:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, .12);
            background: rgba(255, 255, 255, 0.9);
        }

        .dino-item img.dino-thumb {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        @media (max-width:600px) {
            .dino-item img.dino-thumb {
                width: 48px;
                height: 48px;
            }
        }

        .dice-container {
            grid-column: 3;
            grid-row: 3;
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
            background: rgba(0, 0, 0, .3);
            padding: 5px 10px;
            border-radius: 8px;
        }

        .dice-small {
            width: 90px;
            height: 90px;
            background: white;
            border: 3px solid #666;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            user-select: none;
            cursor: pointer;
            transition: .3s ease;
            box-shadow: 0 6px 12px rgba(0, 0, 0, .2);
            position: relative;
        }

        .dice-small:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, .3);
        }

        .dice-small:active {
            transform: scale(.95);
        }

        .dice-small-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .dice-small-label {
            position: absolute;
            bottom: -30px;
            font-size: 0.7rem;
            color: #fff;
            background: rgba(0, 0, 0, .6);
            padding: 3px 8px;
            border-radius: 5px;
            white-space: nowrap;
            font-weight: bold;
            max-width: 150px;
            text-align: center;
        }

        .dice-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .dice-overlay.active {
            display: flex;
        }

        .dice-modal {
            position: relative;
            width: 90%;
            max-width: 600px;
            background-image: url('../img/pergamino.png');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            padding: 60px 40px 40px;
            border-radius: 20px;
            text-align: center;
        }

        .dice-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, .7);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
        }

        .dice-close:hover {
            background: rgba(0, 0, 0, .9);
            transform: scale(1.1);
        }

        .dice-big-container {
            width: 300px;
            height: 300px;
            margin: 20px auto;
            perspective: 1000px;
        }

        .dice-big {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 1s ease-out;
        }

        .dice-big.rolling {
            animation: rollBig 1s ease-out;
        }

        @keyframes rollBig {
            0% {
                transform: rotateX(0deg) rotateY(0deg);
            }

            100% {
                transform: rotateX(720deg) rotateY(720deg);
            }
        }

        .dice-face {
            position: absolute;
            width: 300px;
            height: 300px;
            border: 4px solid #333;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .dice-face img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dice-face-1 {
            transform: rotateY(0deg) translateZ(150px);
        }

        .dice-face-2 {
            transform: rotateY(90deg) translateZ(150px);
        }

        .dice-face-3 {
            transform: rotateY(180deg) translateZ(150px);
        }

        .dice-face-4 {
            transform: rotateY(-90deg) translateZ(150px);
        }

        .dice-face-5 {
            transform: rotateX(90deg) translateZ(150px);
        }

        .dice-face-6 {
            transform: rotateX(-90deg) translateZ(150px);
        }

        .dice-result-text {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            margin: 40px;
        }

        .dice-countdown {
            font-size: 1.2rem;
            color: #666;
        }

        .zone {
            position: absolute;
            border: 3px dashed #333;
            background: rgba(255, 255, 255, .1);
            z-index: 2;
            transition: .1s ease;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            padding: 5px;
            border-radius: 6px;
            cursor: grab;
        }

        .zone.drag-over {
            background: rgba(76, 175, 80, .3);
            border-color: #4CAF50;
            border-style: solid;
        }

        .zone.restricted {
            background: rgba(255, 0, 0, .2);
            border-color: #ff0000;
            border-style: solid;
        }

        .zone-label {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(0, 0, 0, .7);
            color: #fff;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: .8rem;
            font-weight: bold;
        }

        .zone-dino {
            font-size: 2rem;
            margin: 3px;
            animation: pop .3s ease;
        }

        @keyframes pop {
            0% {
                transform: scale(0)
            }

            50% {
                transform: scale(1.2)
            }

            100% {
                transform: scale(1)
            }
        }

        .status-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .8);
            color: #fff;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            z-index: 1000;
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

        .edit-btn {
            padding: 8px 12px;
            border: 2px solid #444;
            border-radius: 8px;
            font-weight: 700;
            background: #fff;
            cursor: pointer;
        }

        .edit-btn.active {
            background: #c7ffd1;
            border-color: #2e7d32;
        }

        .zone.moving {
            cursor: grabbing;
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
            z-index: 3;
        }

        /* === OVERLAYS MODO OFFLINE === */
        .setup-overlay {
            display: flex;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .setup-overlay.hidden {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .setup-modal {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            padding: 50px;
            border-radius: 25px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.7);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .setup-modal h2 {
            font-size: 2.5rem;
            margin: 0 0 15px;
            color: #2c3e50;
            font-weight: 900;
        }

        .setup-modal p {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 35px;
        }

        .player-selector {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 40px 0;
        }

        .player-btn {
            padding: 25px 35px;
            font-size: 2rem;
            border: 4px solid #ddd;
            background: white;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            color: #555;
        }

        .player-btn:hover {
            background: #f8f8f8;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .player-btn.selected {
            background: linear-gradient(145deg, #4CAF50, #45a049);
            color: white;
            border-color: #4CAF50;
            transform: scale(1.1);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
        }

        .start-btn {
            padding: 18px 50px;
            font-size: 1.4rem;
            background: linear-gradient(145deg, #667eea, #5568d3);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .start-btn:hover {
            background: linear-gradient(145deg, #5568d3, #4557c2);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
        }

        .tray-overlay {
            display: flex;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.4s ease;
        }

        .tray-overlay.hidden {
            display: none;
        }

        .tray-container {
            position: relative;
            width: 60%;
            height: 60%;
            background: url('../img/bandeja.png') no-repeat center center;
            background-size: contain;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: slideInTray 0.6s ease;
            padding: 40px;
        }

        @keyframes slideInTray {
            from {
                transform: translateY(-150px) scale(0.8);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .tray-overlay.leaving .tray-container {
            animation: slideOutTray 0.6s ease;
        }

        @keyframes slideOutTray {
            from {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            to {
                transform: translateY(150px) scale(0.8);
                opacity: 0;
            }
        }

        .tray-title {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 30px;
            font-weight: 900;
            text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.9);
            letter-spacing: 2px;
        }

        .tray-dinos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 30px;
            border-radius: 15px;
        }

        .tray-dino {
            width: 90px;
            height: 90px;
            object-fit: contain;
            animation: popDino 0.5s ease;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }

        @keyframes popDino {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }

            60% {
                transform: scale(1.2) rotate(10deg);
            }

            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .continue-btn {
            margin-top: 40px;
            padding: 15px 40px;
            font-size: 1.3rem;
            background: linear-gradient(145deg, #4CAF50, #45a049);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
        }

        .continue-btn:hover {
            background: linear-gradient(145deg, #45a049, #3d8b40);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(76, 175, 80, 0.6);
        }

        .turn-announcement {
            display: flex;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .turn-announcement.hidden {
            display: none;
        }

        .turn-text {
            font-size: 5rem;
            color: white;
            font-weight: 900;
            text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            animation: pulseGlow 0.8s ease infinite;
            letter-spacing: 5px;
        }

        @keyframes pulseGlow {
            0%,
            100% {
                transform: scale(1);
                text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            }

            50% {
                transform: scale(1.05);
                text-shadow: 0 0 50px rgba(255, 255, 255, 0.8),
                    0 0 80px rgba(102, 126, 234, 0.6);
            }
        }

        .round-info {
            font-size: 1rem;
            color: #fff;
            background: rgba(255, 165, 0, .7);
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <!-- Setup inicial: selección de jugadores -->
    <div class="setup-overlay" id="setup-overlay">
        <div class="setup-modal">
            <h2>🦖 Modo Offline 🦕</h2>
            <p>Selecciona la cantidad de jugadores</p>
            <div class="player-selector">
                <button class="player-btn selected" onclick="selectPlayers(2)">2</button>
                <button class="player-btn" onclick="selectPlayers(3)">3</button>
                <button class="player-btn" onclick="selectPlayers(4)">4</button>
                <button class="player-btn" onclick="selectPlayers(5)">5</button>
            </div>
            <button class="start-btn" onclick="startDinoDistribution()">¡Listo!</button>
        </div>
    </div>

    <!-- Overlay de bandejas con dinosaurios -->
    <div class="tray-overlay hidden" id="tray-overlay">
        <div class="tray-container">
            <div class="tray-title" id="tray-title">Jugador 1</div>
            <div class="tray-dinos" id="tray-dinos"></div>
            <button class="continue-btn" id="continue-btn" onclick="nextTrayOrStart()">Continuar</button>
        </div>
    </div>

    <!-- Anuncio de turno -->
    <div class="turn-announcement hidden" id="turn-announcement">
        <div class="turn-text" id="turn-text">Jugador 1</div>
    </div>

    <div class="game-container">
        <!-- Jugadores -->
        <div class="player-card active" id="player1">
            <div class="player-name">Jugador 1</div>
            <div class="player-score">Puntos: <span id="score1">0</span></div>
        </div>
        <div class="player-card" id="player2" style="grid-column:3;">
            <div class="player-name">Jugador 2</div>
            <div class="player-score">Puntos: <span id="score2">0</span></div>
        </div>

        <!-- Tablero -->
        <div class="board-container" id="board">
            <picture class="board-bg">
                <img class="board-img" src="img/tablero.png" alt="Tablero" decoding="async" fetchpriority="high"
                    draggable="false" />
            </picture>
        </div>

        <!-- Jugadores abajo -->
        <div class="player-card" id="player3" style="grid-column:1; grid-row:2;">
            <div class="player-name">Jugador 3</div>
            <div class="player-score">Puntos: <span id="score3">0</span></div>
        </div>
        <div class="player-card" id="player4" style="grid-column:3; grid-row:2;">
            <div class="player-name">Jugador 4</div>
            <div class="player-score">Puntos: <span id="score4">0</span></div>
        </div>

        <!-- Jugador 5 (oculto por defecto) -->
        <div class="player-card" id="player5" style="grid-column:2; grid-row:1; display:none;">
            <div class="player-name">Jugador 5</div>
            <div class="player-score">Puntos: <span id="score5">0</span></div>
        </div>

        <!-- Panel dinosaurios -->
        <div class="dino-panel">
            <h3>🦖 Tus Dinosaurios 🦕</h3>
            <div class="dino-grid" id="dino-grid"></div>
        </div>

        <!-- Dado pequeño + info -->
        <div class="dice-container">
            <button id="toggle-edit" class="edit-btn">Editar zonas: OFF</button>
            <div class="round-info" id="round-info">Ronda: 1</div>
            <div class="turn-info" id="turn-info">Turno: Jugador 1</div>
            <div class="dice-small" id="dice-small">
                <img src="img/dado1.png" alt="Dado" class="dice-small-img" id="dice-small-img">
                <div class="dice-small-label" id="dice-label">Sin restricción</div>
            </div>
        </div>
    </div>

    <!-- Overlay del dado grande -->
    <div class="dice-overlay" id="dice-overlay">
        <div class="dice-modal">
            <button class="dice-close" id="dice-close">×</button>

            <div class="dice-big-container">
                <div class="dice-big" id="dice-big">
                    <div class="dice-face dice-face-1">
                        <img src="../img/dado1.png" alt="Zona 1">
                    </div>
                    <div class="dice-face dice-face-2">
                        <img src="../img/dado2.png" alt="Zona 2">
                    </div>
                    <div class="dice-face dice-face-3">
                        <img src="../img/dado3.png" alt="Zona 3">
                    </div>
                    <div class="dice-face dice-face-4">
                        <img src="../img/dado4.png" alt="Zona 4">
                    </div>
                    <div class="dice-face dice-face-5">
                        <img src="../img/dado5.png" alt="Zona 5">
                    </div>
                    <div class="dice-face dice-face-6">
                        <img src="../img/dado6.png" alt="Zona 6">
                    </div>
                </div>
            </div>

            <div class="dice-result-text" id="dice-result-text">🎲 Lanzando...</div>
            <div class="dice-countdown" id="dice-countdown"></div>
        </div>
    </div>

    <script>
        // ==================== CONFIGURACIÓN ====================
        const DINO_TYPES = [
            { id: 1, name: 'T-Rex', img: '../img/dino1.png', points: 10 },
            { id: 2, name: 'Triceratops', img: '../img/dino2.png', points: 8 },
            { id: 3, name: 'Velociraptor', img: '../img/dino3.png', points: 6 },
            { id: 4, name: 'Brachiosaurus', img: '../img/dino4.png', points: 12 },
            { id: 5, name: 'Pterodactyl', img: '../img/dino5.png', points: 7 },
            { id: 6, name: 'Stegosaurus', img: '../img/dino6.png', points: 9 },
        ];

        const DEFAULT_ZONES = [
            { id: 1, name: 'Bosque de la Semejanza', left: '5%', top: '5%', width: '25%', height: '35%' },
            { id: 2, name: 'El Trio Fondoso', left: '38%', top: '5%', width: '25%', height: '35%' },
            { id: 3, name: 'La Pradera del amor', left: '70%', top: '5%', width: '25%', height: '35%' },
            { id: 4, name: 'El Rey de la Selva', left: '5%', top: '50%', width: '25%', height: '35%' },
            { id: 5, name: 'El Prado de la Diferencia', left: '38%', top: '50%', width: '25%', height: '35%' },
            { id: 6, name: 'La Isla Solitaria', left: '70%', top: '50%', width: '25%', height: '35%' },
        ];

        const DICE_OPTIONS = [
            { id: 1, name: "Zona 1", image: "../img/dado1.png" },
            { id: 2, name: "Zona 2", image: "../img/dado2.png" },
            { id: 3, name: "Zona 3", image: "../img/dado3.png" },
            { id: 4, name: "Zona 4", image: "../img/dado4.png" },
            { id: 5, name: "Zona 5", image: "../img/dado5.png" },
            { id: 6, name: "Zona 6", image: "../img/dado6.png" }
        ];

        const DICE_ROTATIONS = {
            1: 'rotateY(0deg) rotateX(0deg)',
            2: 'rotateY(-90deg) rotateX(0deg)',
            3: 'rotateY(-180deg) rotateX(0deg)',
            4: 'rotateY(90deg) rotateX(0deg)',
            5: 'rotateY(0deg) rotateX(-90deg)',
            6: 'rotateY(0deg) rotateX(90deg)'
        };

        // ==================== VARIABLES GLOBALES ====================
        let ZONES = [];
        let totalPlayers = 2;
        let playerDinos = {}; // {1: [dino, dino, ...], 2: [...], ...}
        let gameStarted = false;
        let currentRound = 1;
        let playersWhoPlacedThisRound = new Set();
        let diceRestriction = null; // zona ID restringida por el dado
        let currentTrayPlayer = 1;
        
        let gameState = {
            zones: {},
            currentPlayer: 1,
            scores: [0, 0, 0, 0, 0],
            lastDiceRoll: null,
            currentDiceOption: 1
        };
        
        let draggedDino = null;
        let editMode = false;
        let isRolling = false;
        let countdownInterval = null;

        // ==================== MODO OFFLINE - SETUP ====================
        function selectPlayers(num) {
            totalPlayers = num;
            document.querySelectorAll('.player-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.classList.add('selected');
        }

        function generateRandomDinos() {
            const dinos = [];
            for (let i = 0; i < 6; i++) {
                const randomDino = DINO_TYPES[Math.floor(Math.random() * DINO_TYPES.length)];
                dinos.push({ ...randomDino });
            }
            return dinos;
        }

        function startDinoDistribution() {
            // Generar dinosaurios para cada jugador
            for (let i = 1; i <= totalPlayers; i++) {
                playerDinos[i] = generateRandomDinos();
            }

            // Ocultar setup y mostrar primer jugador
            const setupOverlay = document.getElementById('setup-overlay');
            setupOverlay.classList.add('hidden');

            showTrayForPlayer(1);
        }

        function showTrayForPlayer(playerNum) {
            currentTrayPlayer = playerNum;
            const trayOverlay = document.getElementById('tray-overlay');
            const trayTitle = document.getElementById('tray-title');
            const trayDinosContainer = document.getElementById('tray-dinos');
            const continueBtn = document.getElementById('continue-btn');

            trayTitle.textContent = `Jugador ${playerNum}`;
            trayDinosContainer.innerHTML = '';

            // Mostrar los 6 dinosaurios del jugador
            playerDinos[playerNum].forEach((dino, index) => {
                const img = document.createElement('img');
                img.src = dino.img;
                img.alt = dino.name;
                img.className = 'tray-dino';
                img.style.animationDelay = `${index * 0.1}s`;
                trayDinosContainer.appendChild(img);
            });

            // Cambiar botón en el último jugador
            if (playerNum === totalPlayers) {
                continueBtn.textContent = '🎮 Comenzar Partida';
            } else {
                continueBtn.textContent = 'Continuar ➜';
            }

            trayOverlay.classList.remove('hidden', 'leaving');
        }

        function nextTrayOrStart() {
            const trayOverlay = document.getElementById('tray-overlay');
            
            // Animación de salida
            trayOverlay.classList.add('leaving');

            setTimeout(() => {
                if (currentTrayPlayer < totalPlayers) {
                    showTrayForPlayer(currentTrayPlayer + 1);
                } else {
                    // Comenzar partida
                    trayOverlay.classList.add('hidden');
                    startGame();
                }
            }, 600);
        }

        function startGame() {
            gameStarted = true;
            gameState.currentPlayer = 1;
            
            // Mostrar/Ocultar jugadores según cantidad seleccionada
            for (let i = 1; i <= 5; i++) {
                const card = document.getElementById(`player${i}`);
                if (card) {
                    if (i <= totalPlayers) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            }

            renderDinoGrid();
            renderZones();
            setActive(gameState.currentPlayer);
            updateRoundInfo();

            // Mostrar anuncio de turno
            showTurnAnnouncement(1);
        }

        function showTurnAnnouncement(playerNum) {
            const announcement = document.getElementById('turn-announcement');
            const turnText = document.getElementById('turn-text');

            turnText.textContent = `Jugador ${playerNum}`;
            announcement.classList.remove('hidden');

            setTimeout(() => {
                announcement.classList.add('hidden');
            }, 3500);
        }

        // ==================== PERSISTENCIA EN SERVIDOR ====================
        async function loadZonesFromServer() {
            try {
                const r = await fetch('Controlador/get_zones.php', {
                    cache: 'no-store'
                });
                const data = await r.json();

                if (Array.isArray(data) && data.length > 0) {
                    const hasNames = data.every(z => z.name && z.name.trim() !== '');
                    if (hasNames) {
                        return data;
                    }
                }

                return structuredClone(DEFAULT_ZONES);
            } catch {
                return structuredClone(DEFAULT_ZONES);
            }
        }

        async function saveZonesToServer() {
            try {
                await fetch('Controlador/save_zones.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(ZONES)
                });
            } catch (e) {
                console.error('No se pudo guardar en servidor', e);
            }
        }

        // ==================== ARRANQUE ====================
        (async function bootstrap() {
            ZONES = await loadZonesFromServer();
            gameState.zones = {};
            ZONES.forEach(z => gameState.zones[z.id] = []);

            updateSmallDice(1);

            document.getElementById('toggle-edit').addEventListener('click', toggleEditMode);
            document.getElementById('dice-small').addEventListener('click', rollDice);
            document.getElementById('dice-close').addEventListener('click', closeDiceOverlay);
            document.getElementById('dice-overlay').addEventListener('click', (e) => {
                if (e.target.id === 'dice-overlay') {
                    closeDiceOverlay();
                }
            });

            // Inicializar dado grande en cara 1
            document.getElementById('dice-big').style.transform = DICE_ROTATIONS[1];
        })();

        // ==================== UI JUGADORES ====================
        function setActive(player) {
            document.querySelectorAll('.player-card').forEach(c => c.classList.remove('active'));
            const card = document.getElementById(`player${player}`);
            if (card) card.classList.add('active');
            document.getElementById('turn-info').textContent = `Turno: Jugador ${player}`;
        }

        function nextTurn() {
            let nextPlayer = (gameState.currentPlayer % totalPlayers) + 1;
            gameState.currentPlayer = nextPlayer;
            setActive(gameState.currentPlayer);
            renderDinoGrid();
        }

        function updateScore(player, score) {
            document.getElementById(`score${player}`).textContent = score;
        }

        function updateRoundInfo() {
            document.getElementById('round-info').textContent = `Ronda: ${currentRound}`;
        }

        // ==================== DINOSAURIOS ====================
        function renderDinoGrid() {
            const container = document.getElementById('dino-grid');
            container.innerHTML = '';
            
            if (!gameStarted) return;

            const currentPlayerDinos = playerDinos[gameState.currentPlayer] || [];
            
            currentPlayerDinos.forEach((dino, index) => {
                const item = document.createElement('div');
                item.className = 'dino-item';
                item.draggable = true;
                item.title = `${dino.name} (${dino.points} puntos)`;

                const img = document.createElement('img');
                img.src = dino.img;
                img.alt = dino.name;
                img.className = 'dino-thumb';
                item.appendChild(img);

                const sr = document.createElement('span');
                sr.className = 'visually-hidden';
                sr.textContent = dino.name;
                item.appendChild(sr);

                item.addEventListener('dragstart', (e) => {
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

        // ==================== ZONAS ====================
        function renderZones() {
            const container = document.getElementById('board');
            container.querySelectorAll('.zone').forEach(el => el.remove());

            ZONES.forEach(zone => {
                const zoneEl = document.createElement('div');
                zoneEl.className = 'zone';
                zoneEl.style.left = zone.left;
                zoneEl.style.top = zone.top;
                zoneEl.style.width = zone.width;
                zoneEl.style.height = zone.height;

                // Marcar zona restringida
                if (diceRestriction === zone.id) {
                    zoneEl.classList.add('restricted');
                }

                const label = document.createElement('div');
                label.className = 'zone-label';
                label.textContent = zone.name;
                if (diceRestriction === zone.id) {
                    label.textContent += ' 🚫';
                }
                zoneEl.appendChild(label);

                if (!editMode) {
                    zoneEl.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        if (diceRestriction !== zone.id) {
                            zoneEl.classList.add('drag-over');
                        }
                    });
                    zoneEl.addEventListener('dragleave', () => zoneEl.classList.remove('drag-over'));
                    zoneEl.addEventListener('drop', (e) => {
                        e.preventDefault();
                        zoneEl.classList.remove('drag-over');
                        
                        if (draggedDino && draggedDino.playerIndex !== undefined) {
                            // Verificar si el jugador ya colocó un dino esta ronda
                            if (playersWhoPlacedThisRound.has(gameState.currentPlayer)) {
                                showMessage('⚠️ Ya colocaste un dinosaurio esta ronda. Debes tirar el dado.');
                                return;
                            }

                            // Verificar restricción del dado
                            if (diceRestriction && zone.id === diceRestriction) {
                                showMessage('🚫 Esta zona está restringida por el dado. Elige otra.');
                                return;
                            }

                            // Colocar dinosaurio
                            gameState.zones[zone.id] = gameState.zones[zone.id] || [];
                            gameState.zones[zone.id].push(draggedDino);
                            
                            // Remover dino del jugador
                            playerDinos[gameState.currentPlayer].splice(draggedDino.playerIndex, 1);
                            
                            gameState.scores[gameState.currentPlayer - 1] += draggedDino.points;
                            updateScore(gameState.currentPlayer, gameState.scores[gameState.currentPlayer - 1]);
                            
                            showMessage(`✅ ${draggedDino.name} colocado en ${zone.name}! +${draggedDino.points} puntos`);
                            
                            playersWhoPlacedThisRound.add(gameState.currentPlayer);
                            
                            renderZones();
                            renderDinoGrid();
                            
                            // Mostrar mensaje para tirar el dado
                            setTimeout(() => {
                                showMessage('🎲 ¡Ahora debes tirar el dado!');
                            }, 1500);
                        }
                    });
                } else {
                    enableEditUI(zoneEl);
                    wireZoneDragAndResize(zoneEl, zone);
                }

                (gameState.zones[zone.id] || []).forEach(dino => {
                    const dEl = document.createElement('div');
                    dEl.className = 'zone-dino';
                    dEl.textContent = '🦖';
                    dEl.title = dino.name;
                    zoneEl.appendChild(dEl);
                });

                container.appendChild(zoneEl);
            });
        }

        // ==================== DADO ====================
        function rollDice() {
            if (isRolling) return;
            
            // Verificar que el jugador haya colocado un dino esta ronda
            if (!playersWhoPlacedThisRound.has(gameState.currentPlayer)) {
                showMessage('⚠️ Primero debes colocar un dinosaurio antes de tirar el dado');
                return;
            }
            
            isRolling = true;

            const overlay = document.getElementById('dice-overlay');
            const diceBig = document.getElementById('dice-big');
            const resultText = document.getElementById('dice-result-text');
            const countdown = document.getElementById('dice-countdown');

            overlay.classList.add('active');
            diceBig.classList.add('rolling');
            resultText.textContent = '🎲 Lanzando...';
            countdown.textContent = '';

            // Generar resultado aleatorio (1-6 se mapea a zona 1-6)
            const result = Math.floor(Math.random() * 6) + 1;

            setTimeout(() => {
                diceBig.classList.remove('rolling');
                diceBig.style.transform = DICE_ROTATIONS[result];

                const option = DICE_OPTIONS[result - 1];
                const zoneName = ZONES.find(z => z.id === result)?.name || `Zona ${result}`;
                resultText.textContent = `🎲 Restricción para próxima ronda: ${zoneName}`;

                gameState.lastDiceRoll = result;
                gameState.currentDiceOption = result;

                // Actualizar dado pequeño
                updateSmallDice(result);

                // Iniciar countdown de 10 segundos
                let seconds = 10;
                countdown.textContent = `Se cerrará en ${seconds} segundos`;

                countdownInterval = setInterval(() => {
                    seconds--;
                    if (seconds > 0) {
                        countdown.textContent = `Se cerrará en ${seconds} segundos`;
                    } else {
                        clearInterval(countdownInterval);
                        closeDiceOverlay();
                    }
                }, 1000);

                isRolling = false;
                showMessage(`🎲 ${zoneName} estará restringida la próxima ronda`);
            }, 1000);
        }

        function updateSmallDice(optionId) {
            const option = DICE_OPTIONS[optionId - 1];
            const smallImg = document.getElementById('dice-small-img');
            const label = document.getElementById('dice-label');
            const zoneName = ZONES.find(z => z.id === optionId)?.name || `Zona ${optionId}`;

            smallImg.src = option.image;
            if (diceRestriction) {
                label.textContent = `🚫 ${zoneName}`;
            } else {
                label.textContent = 'Sin restricción';
            }
        }

        function closeDiceOverlay() {
            const overlay = document.getElementById('dice-overlay');
            overlay.classList.remove('active');
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            
            // Llamar a afterDiceRoll si el overlay se cierra
            if (playersWhoPlacedThisRound.has(gameState.currentPlayer) && gameStarted) {
                afterDiceRoll();
            }
        }

        function afterDiceRoll() {
            // Verificar si terminó la ronda ANTES de cambiar turno
            const roundComplete = playersWhoPlacedThisRound.size === totalPlayers;
            
            if (roundComplete) {
                // Aplicar restricción del dado para la PRÓXIMA ronda
                diceRestriction = gameState.lastDiceRoll;
                
                // Nueva ronda
                currentRound++;
                playersWhoPlacedThisRound.clear();
                updateRoundInfo();
                updateSmallDice(diceRestriction);
                renderZones(); // Actualizar zonas con restricción
                
                showMessage(`🔄 ¡Ronda ${currentRound}! Zona ${ZONES.find(z => z.id === diceRestriction)?.name} restringida`);
            }
            
            // Pasar al siguiente turno
            nextTurn();
            
            // Mostrar anuncio del siguiente jugador
            setTimeout(() => {
                showTurnAnnouncement(gameState.currentPlayer);
            }, 1000);
        }

        // ==================== MENSAJES ====================
        function showMessage(text) {
            const exist = document.querySelector('.status-message');
            if (exist) exist.remove();
            const msg = document.createElement('div');
            msg.className = 'status-message';
            msg.textContent = text;
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 3000);
        }

        // ==================== EDICIÓN DE ZONAS ====================
        function toggleEditMode() {
            editMode = !editMode;
            const btn = document.getElementById('toggle-edit');
            btn.classList.toggle('active', editMode);
            btn.textContent = `Editar zonas: ${editMode ? 'ON' : 'OFF'}`;
            renderZones();
        }

        function pxToPercent(x, total) {
            return (x / total) * 100;
        }

        function clamp(v, min, max) {
            return Math.max(min, Math.min(max, v));
        }

        function enableEditUI(zoneEl) {
            const handle = document.createElement('div');
            handle.className = 'zone-handle';
            zoneEl.appendChild(handle);
        }

        function wireZoneDragAndResize(zoneEl, zone) {
            const board = document.getElementById('board');
            let startX, startY, startLeft, startTop;
            let resizing = false, moving = false;

            const onMove = (e) => {
                if (!editMode) return;
                const rect = board.getBoundingClientRect();
                const mx = e.clientX - rect.left;
                const my = e.clientY - rect.top;

                if (resizing) {
                    const newW = clamp(mx - startLeft, 40, rect.width);
                    const newH = clamp(my - startTop, 40, rect.height);
                    zone.width = `${clamp(pxToPercent(newW, rect.width), 1, 100)}%`;
                    zone.height = `${clamp(pxToPercent(newH, rect.height), 1, 100)}%`;
                    zoneEl.style.width = zone.width;
                    zoneEl.style.height = zone.height;
                } else if (moving) {
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    let nLeft = startLeft + dx;
                    let nTop = startTop + dy;

                    const zRect = zoneEl.getBoundingClientRect();
                    const maxLeft = rect.width - zRect.width;
                    const maxTop = rect.height - zRect.height;
                    nLeft = clamp(nLeft, 0, maxLeft);
                    nTop = clamp(nTop, 0, maxTop);

                    zone.left = `${pxToPercent(nLeft, rect.width)}%`;
                    zone.top = `${pxToPercent(nTop, rect.height)}%`;
                    zoneEl.style.left = zone.left;
                    zoneEl.style.top = zone.top;
                }
            };

            const onUp = () => {
                if (!editMode) return;
                moving = false;
                resizing = false;
                zoneEl.classList.remove('moving', 'resizing');
                saveZonesToServer();
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onUp);
            };

            zoneEl.addEventListener('pointerdown', (e) => {
                if (!editMode) return;
                if (e.target.classList.contains('zone-handle')) return;
                moving = true;
                resizing = false;
                zoneEl.classList.add('moving');

                const boardRect = board.getBoundingClientRect();
                const zRect = zoneEl.getBoundingClientRect();
                startX = e.clientX;
                startY = e.clientY;
                startLeft = zRect.left - boardRect.left;
                startTop = zRect.top - boardRect.top;

                window.addEventListener('pointermove', onMove);
                window.addEventListener('pointerup', onUp);
            });

            zoneEl.querySelector('.zone-handle').addEventListener('pointerdown', (e) => {
                if (!editMode) return;
                e.stopPropagation();
                resizing = true;
                moving = false;
                zoneEl.classList.add('resizing');

                const boardRect = board.getBoundingClientRect();
                const zRect = zoneEl.getBoundingClientRect();
                startX = e.clientX;
                startY = e.clientY;
                startLeft = zRect.left - boardRect.left;
                startTop = zRect.top - boardRect.top;

                window.addEventListener('pointermove', onMove);
                window.addEventListener('pointerup', onUp);
            });
        }
    </script>
</body>

</html>