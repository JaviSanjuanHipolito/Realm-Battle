<?php
session_start();
require_once __DIR__ . '/../src/Partida.php';

// Si no hay sesión activa, al login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

// Si no hay personajes elegidos, a selección
if (!isset($_SESSION['personaje_j1']) || !isset($_SESSION['personaje_j2'])) {
    header('Location: seleccion.php');
    exit;
}

$partida = new Partida();

// Procesar acción del turno actual
if (isset($_POST['accion'])) {
    $turno = $_SESSION['turno'];
    $accion = $_POST['accion'];

    if ($turno === 1) {
        $atacante = ['ataque' => $_SESSION['ataque_j1'], 'defensa' => $_SESSION['defensa_j1']];
        $defensor = ['ataque' => $_SESSION['ataque_j2'], 'defensa' => $_SESSION['defensa_j2']];
    } else {
        $atacante = ['ataque' => $_SESSION['ataque_j2'], 'defensa' => $_SESSION['defensa_j2']];
        $defensor = ['ataque' => $_SESSION['ataque_j1'], 'defensa' => $_SESSION['defensa_j1']];
    }

    switch ($accion) {
        case 'atacar':
            $dano = $partida->atacar($atacante, $defensor);
            if ($turno === 1) {
                $_SESSION['hp_j2'] -= $dano;
                $_SESSION['log'][] = "⚔ Jugador 1 ataca y hace {$dano} de daño";
            } else {
                $_SESSION['hp_j1'] -= $dano;
                $_SESSION['log'][] = "⚔ Jugador 2 ataca y hace {$dano} de daño";
            }
            break;

        case 'curar':
            if ($turno === 1) {
                $_SESSION['hp_j1'] = $partida->curarse($_SESSION['hp_j1'], $_SESSION['salud_max_j1']);
                $_SESSION['log'][] = "💚 Jugador 1 se cura 20 de salud";
            } else {
                $_SESSION['hp_j2'] = $partida->curarse($_SESSION['hp_j2'], $_SESSION['salud_max_j2']);
                $_SESSION['log'][] = "💚 Jugador 2 se cura 20 de salud";
            }
            break;

        case 'defensa':
            if ($turno === 1) {
                $_SESSION['defensa_j1'] = $partida->subirDefensa($_SESSION['defensa_j1']);
                $_SESSION['log'][] = "🛡️ Jugador 1 sube su defensa +5";
            } else {
                $_SESSION['defensa_j2'] = $partida->subirDefensa($_SESSION['defensa_j2']);
                $_SESSION['log'][] = "🛡️ Jugador 2 sube su defensa +5";
            }
            break;
    }

   // Comprobar si alguien ha ganado
if ($partida->estaKO($_SESSION['hp_j1'])) {
  $_SESSION['ganador'] = 2;
  $partida->cerrarPartida($_SESSION['id_partida'], $_SESSION['id_usuario']);
  header('Location: resultado.php');
  exit;
}
if ($partida->estaKO($_SESSION['hp_j2'])) {
  $_SESSION['ganador'] = 1;
  $partida->cerrarPartida($_SESSION['id_partida'], $_SESSION['id_usuario']);
  header('Location: resultado.php');
  exit;
}

    // Cambiar turno
    $_SESSION['turno'] = $_SESSION['turno'] === 1 ? 2 : 1;
}

// Inicializar log si no existe
if (!isset($_SESSION['log'])) {
    $_SESSION['log'] = ['▸ ¡Que empiece la batalla!'];
}

$pct1 = ($_SESSION['hp_j1'] / $_SESSION['salud_max_j1']) * 100;
$pct2 = ($_SESSION['hp_j2'] / $_SESSION['salud_max_j2']) * 100;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Combate</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>

<div class="world">
  <div class="bg-battle"></div>
  <div class="stars"></div>

  <div class="container">

    <div class="turn-banner">
      <div class="turn-pill">⚔ TURNO DEL JUGADOR <?= $_SESSION['turno'] ?></div>
    </div>

    <div class="fighters">

      <!-- Jugador 1 -->
      <div class="fighter <?= $_SESSION['turno'] === 1 ? 'active' : '' ?>">
        <div class="corner tl"></div><div class="corner tr"></div>
        <div class="corner bl"></div><div class="corner br"></div>
        <div class="fighter-label">JUGADOR 1</div>
        <div class="fighter-name"><?= htmlspecialchars($_SESSION['personaje_j1']) ?></div>
        <span class="fighter-sprite">⚔️</span>
        <div class="hp-label">HP</div>
        <div class="hp-bar-bg">
          <div class="hp-bar <?= $pct1 < 25 ? 'low' : ($pct1 < 50 ? 'mid' : '') ?>" 
               style="width:<?= max(0, $pct1) ?>%"></div>
        </div>
        <div class="hp-text"><?= max(0, $_SESSION['hp_j1']) ?> / <?= $_SESSION['salud_max_j1'] ?></div>
        <div class="stat-mini">
          <div><span>ATK </span><b><?= $_SESSION['ataque_j1'] ?></b></div>
          <div><span>DEF </span><b><?= $_SESSION['defensa_j1'] ?></b></div>
        </div>
      </div>

      <div class="vs">VS</div>

      <!-- Jugador 2 -->
      <div class="fighter <?= $_SESSION['turno'] === 2 ? 'active' : '' ?>">
        <div class="corner tl"></div><div class="corner tr"></div>
        <div class="corner bl"></div><div class="corner br"></div>
        <div class="fighter-label">JUGADOR 2</div>
        <div class="fighter-name"><?= htmlspecialchars($_SESSION['personaje_j2']) ?></div>
        <span class="fighter-sprite">🔮</span>
        <div class="hp-label">HP</div>
        <div class="hp-bar-bg">
          <div class="hp-bar <?= $pct2 < 25 ? 'low' : ($pct2 < 50 ? 'mid' : '') ?>"
               style="width:<?= max(0, $pct2) ?>%"></div>
        </div>
        <div class="hp-text"><?= max(0, $_SESSION['hp_j2']) ?> / <?= $_SESSION['salud_max_j2'] ?></div>
        <div class="stat-mini">
          <div><span>ATK </span><b><?= $_SESSION['ataque_j2'] ?></b></div>
          <div><span>DEF </span><b><?= $_SESSION['defensa_j2'] ?></b></div>
        </div>
      </div>

    </div>

    <!-- Log de batalla -->
    <div class="log-box">
      <?php foreach (array_slice($_SESSION['log'], -6) as $linea): ?>
        <div class="log-line"><?= htmlspecialchars($linea) ?></div>
      <?php endforeach; ?>
    </div>

    <!-- Acciones -->
    <form method="POST">
      <div class="actions">
        <button class="btn-action" name="accion" value="atacar">
          <span class="btn-icon">⚔️</span>ATACAR
        </button>
        <button class="btn-action" name="accion" value="curar">
          <span class="btn-icon">💚</span>CURARSE
        </button>
        <button class="btn-action" name="accion" value="defensa">
          <span class="btn-icon">🛡️</span>SUBIR<br>DEFENSA
        </button>
      </div>
    </form>

  </div>
</div>

</body>
</html>