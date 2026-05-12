<?php
session_start();
require_once __DIR__ . '/../src/Usuario.php';

// Si no hay ganador, al login
if (!isset($_SESSION['ganador'])) {
    header('Location: index.php');
    exit;
}

$ganador     = $_SESSION['ganador'];
$personaje   = $ganador === 1 ? $_SESSION['personaje_j1'] : $_SESSION['personaje_j2'];
$hpRestante  = $ganador === 1 ? $_SESSION['hp_j1'] : $_SESSION['hp_j2'];
$hpMax       = $ganador === 1 ? $_SESSION['salud_max_j1'] : $_SESSION['salud_max_j2'];
$turnos      = $_SESSION['turno'] ?? 0;

// Cargar ranking
$usuario = new Usuario();
$ranking = $usuario->getRanking();

// Si pulsa revancha, limpiar sesión de combate y volver a selección
if (isset($_POST['revancha'])) {
    unset(
        $_SESSION['personaje_j1'], $_SESSION['personaje_j2'],
        $_SESSION['hp_j1'], $_SESSION['hp_j2'],
        $_SESSION['ataque_j1'], $_SESSION['ataque_j2'],
        $_SESSION['defensa_j1'], $_SESSION['defensa_j2'],
        $_SESSION['salud_max_j1'], $_SESSION['salud_max_j2'],
        $_SESSION['turno'], $_SESSION['log'],
        $_SESSION['ganador'], $_SESSION['id_partida']
    );
    header('Location: seleccion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>

<div class="world">
  <div class="stars"></div>
  <div class="mountains"></div>

  <div class="container">

    <div class="tabs">
      <button class="tab active" onclick="switchTab('resultado')">🏆 RESULTADO</button>
      <button class="tab" onclick="switchTab('ranking')">👑 RANKING</button>
    </div>

    <!-- Panel resultado -->
    <div id="panel-resultado" class="panel active">
      <div class="resultado-box">
        <div class="corner tl"></div><div class="corner tr"></div>
        <div class="corner bl"></div><div class="corner br"></div>

        <span class="trophy">🏆</span>
        <div class="winner-label">VICTORIA</div>
        <div class="winner-name">JUGADOR <?= $ganador ?></div>
        <div class="winner-sub">▸ <?= htmlspecialchars($personaje) ?> ◂</div>

        <div class="divider">✦ ✦ ✦</div>

        <div class="match-summary">
          <div class="summary-item">
            <span class="summary-val"><?= $turnos ?></span>
            <span class="summary-lbl">turnos</span>
          </div>
          <div class="summary-item">
            <span class="summary-val">HP restante</span>
            <span class="summary-lbl"><?= max(0, $hpRestante) ?> / <?= $hpMax ?></span>
          </div>
        </div>

        <div class="btn-row">
          <button class="btn" onclick="switchTab('ranking')">👑 VER RANKING</button>
          <form method="POST" style="flex:1">
            <button class="btn secondary" name="revancha" style="width:100%">⚔ REVANCHA</button>
          </form>
        </div>

      </div>
    </div>

    <!-- Panel ranking -->
    <div id="panel-ranking" class="panel">
      <div class="ranking-title">⚔ TABLA DE CAMPEONES ⚔</div>
      <div class="ranking-box">
        <div class="corner tl"></div><div class="corner tr"></div>
        <div class="corner bl"></div><div class="corner br"></div>
        <div class="ranking-header">
          <span>#</span><span>HÉROE</span><span style="text-align:right">VICTORIAS</span>
        </div>
        <?php foreach ($ranking as $i => $fila): ?>
          <div class="ranking-row">
            <span class="pos <?= $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : '')) ?>">
              <?= $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i + 1)) ?>
            </span>
            <span class="player-name"><?= htmlspecialchars($fila['nombre']) ?></span>
            <span class="wins"><?= $fila['victorias'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel-' + tab).classList.add('active');
  event.currentTarget.classList.add('active');
}
</script>

</body>
</html>