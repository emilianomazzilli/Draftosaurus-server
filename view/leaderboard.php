<?php
require __DIR__ . '/../lang/boot.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clasificación Prehistórica</title>
    <link rel="stylesheet" href="../Css/leaderboard.css">
    <link rel="stylesheet" href="../Css/styles.css">
    <link rel="stylesheet" href="../Css/servers.css">
    <link rel="stylesheet" href="../Css/media.css">
</head>

<body>
    <button class="back-btn" onclick="handleBackButton()" title="Volver al menú">
      <img src="../img/botonatras.png" alt="Volver al menú" class="back-btn-img">
    </button>

    <!-- Hojas cayendo -->
    <div class="leaves" id="leaves"></div>

    <div class="borde top"></div>
    <div class="borde bottom"></div>
    <div class="borde left"></div>
    <div class="borde right"></div>

    <div class="container">
        
        <!-- Header -->
        <div class="header">
            <h1 class="main-title"><?= t('leaderboard.leaderboard') ?></h1>
            <p class="subtitle"><?= t('leaderboard.subtitle') ?></p>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="switchTab('monthly')">📅<?= t('leaderboard.monthly') ?></div>
            <div class="tab" onclick="switchTab('annual')">🏆<?= t('leaderboard.annual') ?></div>
        </div>

        <!-- Contenido Mensual -->
        <div class="tab-content active" id="monthly">
            <!-- Podio -->
            <div class="podium-section">
                <div class="podium">
                    <div class="podium-place place-2">
                        <div class="avatar">
                            🦕
                            <div class="medal">2</div>
                        </div>
                        <div class="pedestal">
                            <div class="player-name">BrontoKing</div>
                            <div class="player-score">8,450</div>
                            <div class="player-level"><?= t('leaderboard.level') ?>42</div>
                        </div>
                    </div>

                    <div class="podium-place place-1">
                        <div class="avatar">
                            🦖
                            <div class="medal">1</div>
                        </div>
                        <div class="pedestal">
                            <div class="player-name">T-Rex_Pro</div>
                            <div class="player-score">12,380</div>
                            <div class="player-level"><?= t('leaderboard.level') ?>58</div>
                        </div>
                    </div>

                    <div class="podium-place place-3">
                        <div class="avatar">
                            🦎
                            <div class="medal">3</div>
                        </div>
                        <div class="pedestal">
                            <div class="player-name">RaptorQueen</div>
                            <div class="player-score">7,920</div>
                            <div class="player-level"><?= t('leaderboard.level') ?>39</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="leaderboard">
                <h2 class="leaderboard-title">🌿 Top 10 <?= t('leaderboard.october') ?></h2>
                <div class="leaderboard-header">
                    <div><?= t('leaderboard.rank') ?></div>
                    <div><?= t('leaderboard.profile') ?></div>
                    <div><?= t('leaderboard.player') ?></div>
                    <div><?= t('leaderboard.points') ?></div>
                </div>
                <div class="leaderboard-row">
                    <div class="rank">4°</div>
                    <div class="player-avatar">🦖</div>
                    <div class="player-info">
                        <div class="name">Stego_Master</div>
                        <div class="stats"><?= t('leaderboard.level') ?>35 • 245 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">6,840</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">5°</div>
                    <div class="player-avatar">🦕</div>
                    <div class="player-info">
                        <div class="name">DinoHunter99</div>
                        <div class="stats"><?= t('leaderboard.level') ?>33 • 198 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">6,120</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">6°</div>
                    <div class="player-avatar">🦎</div>
                    <div class="player-info">
                        <div class="name">TriceraTop</div>
                        <div class="stats"><?= t('leaderboard.level') ?>31 • 180 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">5,890</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">7°</div>
                    <div class="player-avatar">🦖</div>
                    <div class="player-info">
                        <div class="name">VelociBoss</div>
                        <div class="stats"><?= t('leaderboard.level') ?>29 • 165 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">5,340</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">8°</div>
                    <div class="player-avatar">🦕</div>
                    <div class="player-info">
                        <div class="name">PteroDactyl_X</div>
                        <div class="stats"><?= t('leaderboard.level') ?>28 • 152 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">5,100</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">9°</div>
                    <div class="player-avatar">🦎</div>
                    <div class="player-info">
                        <div class="name">MegaRex_77</div>
                        <div class="stats"><?= t('leaderboard.level') ?>26 • 145 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">4,860</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">10°</div>
                    <div class="player-avatar">🦖</div>
                    <div class="player-info">
                        <div class="name">DiploDominator</div>
                        <div class="stats"><?= t('leaderboard.level') ?>25 • 138 <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">4,620</div>

                </div>
            </div>
        </div>

        <!-- Contenido Anual -->
        <div class="tab-content" id="annual">
            <!-- Podio -->
            <div class="podium-section">
                <div class="podium">
                    <div class="podium-place place-2">
                        <div class="avatar">
                            🦎
                            <div class="medal">2</div>
                        </div>
                        <div class="pedestal">
                            <div class="player-name">RaptorQueen</div>
                            <div class="player-score">98,750</div>
                            <div class="player-level"><?= t('leaderboard.level') ?>85</div>
                        </div>
                    </div>

                    <div class="podium-place place-1">
                        <div class="avatar">
                            🦖
                            <div class="medal">1</div>
                        </div>
                        <div class="pedestal">
                            <div class="player-name">T-Rex_Pro</div>
                            <div class="player-score">145,820</div>
                            <div class="player-level"><?= t('leaderboard.level') ?>99</div>
                        </div>
                    </div>

                    <div class="podium-place place-3">
                        <div class="avatar">
                            🦕
                            <div class="medal">3</div>
                        </div>
                        <div class="pedestal">
                            <div class="player-name">DinoHunter99</div>
                            <div class="player-score">87,340</div>
                            <div class="player-level"><?= t('leaderboard.level') ?>78</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="leaderboard">
                <h2 class="leaderboard-title">👑 Top 10 2025</h2>
                <div class="leaderboard-header">
                    <div>Rank</div>
                    <div>Dino</div>
                    <div>Jugador</div>
                    <div>Puntos</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">4°</div>
                    <div class="player-avatar">🦖</div>
                    <div class="player-info">
                        <div class="name">BrontoKing</div>
                        <div class="stats"><?= t('leaderboard.level') ?>72 • 2.8k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">76,480</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">5°</div>
                    <div class="player-avatar">🦕</div>
                    <div class="player-info">
                        <div class="name">Stego_Master</div>
                        <div class="stats"><?= t('leaderboard.level') ?>68 • 2.5k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">68,920</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">6°</div>
                    <div class="player-avatar">🦎</div>
                    <div class="player-info">
                        <div class="name">TriceraTop</div>
                        <div class="stats"><?= t('leaderboard.level') ?>65 • 2.3k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">64,750</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">7°</div>
                    <div class="player-avatar">🦖</div>
                    <div class="player-info">
                        <div class="name">PteroDactyl_X</div>
                        <div class="stats"><?= t('leaderboard.level') ?>62 • 2.1k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">59,340</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">8°</div>
                    <div class="player-avatar">🦕</div>
                    <div class="player-info">
                        <div class="name">VelociBoss</div>
                        <div class="stats"><?= t('leaderboard.level') ?>58 • 1.9k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">54,820</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">9°</div>
                    <div class="player-avatar">🦎</div>
                    <div class="player-info">
                        <div class="name">MegaRex_77</div>
                        <div class="stats"><?= t('leaderboard.level') ?>55 • 1.8k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">51,960</div>

                </div>
                <div class="leaderboard-row">
                    <div class="rank">10°</div>
                    <div class="player-avatar">🦖</div>
                    <div class="player-info">
                        <div class="name">DiploDominator</div>
                        <div class="stats"><?= t('leaderboard.level') ?>52 • 1.7k <?= t('leaderboard.games') ?></div>
                    </div>
                    <div class="score">48,230</div>

                </div>
            </div>
        </div>
    </div>
    <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>

    <script src="../js/script.js"></script>
</body>

</html>