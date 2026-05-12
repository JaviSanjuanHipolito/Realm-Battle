<?php
session_start();
require_once __DIR__ . '/../src/Personajes.php';
require_once __DIR__ . '/../src/Partida.php';

// Si no hay sesión activa, al login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$personajes = new Personajes();
$todos = $personajes->getTodos();

// Jugador 1 confirma su elección
if (isset($_POST['elegir_j1'])) {
    $_SESSION['personaje_j1'] = $_POST['personaje'];
}

// Jugador 2 confirma su elección → crear partida y al combate
if (isset($_POST['elegir_j2'])) {
    $_SESSION['personaje_j2'] = $_POST['personaje'];

    // Crear la partida en la BD
    $partida = new Partida();
    $idPartida = $partida->crearPartida();
    $_SESSION['id_partida'] = $idPartida;

    // Guardar elecciones en la BD
    $partida->guardarEleccion($idPartida, $_SESSION['id_usuario'], $_SESSION['personaje_j1']);
    $partida->guardarEleccion($idPartida, $_SESSION['id_usuario'], $_SESSION['personaje_j2']);
    // Cargar stats de cada personaje en sesión
    $p = new Personajes();
    $j1 = $p->getPersonaje($_SESSION['personaje_j1']);
    $j2 = $p->getPersonaje($_SESSION['personaje_j2']);

    $_SESSION['hp_j1']      = $j1['salud'];
    $_SESSION['hp_j2']      = $j2['salud'];
    $_SESSION['ataque_j1']  = $j1['ataque'];
    $_SESSION['ataque_j2']  = $j2['ataque'];
    $_SESSION['defensa_j1'] = $j1['defensa'];
    $_SESSION['defensa_j2'] = $j2['defensa'];
    $_SESSION['salud_max_j1'] = $j1['salud'];
    $_SESSION['salud_max_j2'] = $j2['salud'];
    $_SESSION['turno']      = 1;

    header('Location: combate.php');
    exit;
}

// Saber en qué turno estamos
$turno = isset($_SESSION['personaje_j1']) ? 2 : 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selección de Héroe</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>

<div class="world">
  <div class="stars"></div>
  <div class="mountains"></div>

  <div class="container">
  <div class="header">
  <div style="text-align: center; margin-bottom: 1.5rem;">
    <div class="turn-banner">
        <div class="turn-pill">⚔ TURNO DEL JUGADOR <?= $turno ?></div>
    </div>
    <div style="margin-bottom:10px;">
        <span class="torch">🔥</span>
        <span style="font-size:13px;color:#8b5e3c;margin:0 10px">⚔</span>
        <span class="torch" style="animation-delay:.3s">🔥</span>
    </div>
    <div class="main-title">ELIGE TU HEROE</div>
    <div class="main-sub">▸ EL DESTINO LLAMA ◂</div>
</div>

    <div class="divider">✦ ✦ ✦</div>

    <form method="POST">
      <div class="grid">
      <?php foreach ($todos as $p): ?>
    <?php
        // Si es turno 2, bloquear el personaje elegido por J1
        $bloqueado = ($turno === 2 && $p['nombre_personaje'] === $_SESSION['personaje_j1']);

        $iconos = [
            'Caballero Oscuro' => '🛡️',
            'Arquera Élfica'   => '🏹',
            'Mago de Runas'    => '🔮',
            'Paladín'          => '⚔️',
            'Asesino'          => '🗡️',
            'Druida'           => '🌿',
        ];
    ?>
    <label class="card <?= $bloqueado ? 'bloqueado' : '' ?>">
        <input type="radio" name="personaje"
               value="<?= htmlspecialchars($p['nombre_personaje']) ?>"
               <?= $bloqueado ? 'disabled' : '' ?>
               style="display:none">
        <span class="card-icon"><?= $iconos[$p['nombre_personaje']] ?? '⚔️' ?></span>
        <div class="card-name"><?= htmlspecialchars($p['nombre_personaje']) ?></div>
        <div class="stats">
            <div class="stat-row">
                <span class="stat-label">HP</span>
                <div class="stat-bar-bg">
                    <div class="stat-bar bar-hp" style="width:<?= $p['salud'] / 1.2 ?>%"></div>
                </div>
                <span class="stat-val"><?= $p['salud'] ?></span>
            </div>
            <div class="stat-row">
                <span class="stat-label">ATK</span>
                <div class="stat-bar-bg">
                    <div class="stat-bar bar-atk" style="width:<?= $p['ataque'] * 2.5 ?>%"></div>
                </div>
                <span class="stat-val"><?= $p['ataque'] ?></span>
            </div>
            <div class="stat-row">
                <span class="stat-label">DEF</span>
                <div class="stat-bar-bg">
                    <div class="stat-bar bar-def" style="width:<?= $p['defensa'] * 4 ?>%"></div>
                </div>
                <span class="stat-val"><?= $p['defensa'] ?></span>
            </div>
        </div>
    </label>
<?php endforeach; ?>
      </div>

      <button class="btn-confirm" name="<?= $turno === 1 ? 'elegir_j1' : 'elegir_j2' ?>">
        ⚔ CONFIRMAR ELECCIÓN
      </button>
    </form>

  </div>
</div>

<script>
// Marcar la card seleccionada al hacer clic
document.querySelectorAll('.card:not(.bloqueado)').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('input[type=radio]').checked = true;
    });
});
</script>

</body>
</html>