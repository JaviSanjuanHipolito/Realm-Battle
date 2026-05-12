<?php
session_start();
require_once __DIR__ . '/../src/Usuario.php';

$error = '';
$exito = '';

// Procesar login
if (isset($_POST['login'])) {
    $usuario = new Usuario();
    $resultado = $usuario->login($_POST['nombre'], $_POST['contrasena']);

    if ($resultado) {
        $_SESSION['id_usuario'] = $resultado['id_usuario'];
        $_SESSION['nombre']     = $resultado['nombre'];
        header('Location: seleccion.php');
        exit;
    } else {
        $error = 'Nombre o contraseña incorrectos';
    }
}

// Procesar registro
if (isset($_POST['registro'])) {
    if ($_POST['contrasena'] !== $_POST['confirmar']) {
        $error = 'Las contraseñas no coinciden';
    } else {
        $usuario = new Usuario();
        if ($usuario->registro($_POST['nombre'], $_POST['contrasena'])) {
            $exito = 'Héroe creado correctamente, ya puedes entrar';
        } else {
            $error = 'Error al crear el héroe';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realm Battles</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>

<div class="world">
  <div class="stars"></div>
  <div class="mountains"></div>

  <div class="card">
    <div class="corner tl"></div>
    <div class="corner tr"></div>
    <div class="corner bl"></div>
    <div class="corner br"></div>

    <div class="card-header">
      <div style="margin-bottom:8px">
        <span class="torch">🔥</span>
        <span style="font-size:13px;color:#8b5e3c;margin:0 8px">⚔</span>
        <span class="torch" style="animation-delay:.3s">🔥</span>
      </div>
      <div class="game-title">REALM<br>BATTLES</div>
      <div class="subtitle">▸ ELIGE TU DESTINO ◂</div>
    </div>

    <div class="divider">✦ ✦ ✦</div>

    <?php if ($error): ?>
      <div class="msg-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($exito): ?>
      <div class="msg-exito">✔ <?= htmlspecialchars($exito) ?></div>
    <?php endif; ?>

    <div class="tabs">
      <button class="tab active" onclick="switchTab('login')">⚔ ENTRAR</button>
      <button class="tab" onclick="switchTab('registro')">📜 REGISTRO</button>
    </div>

    <div id="panel-login" class="panel active">
      <form method="POST">
        <div class="form-group">
          <label class="form-label">▸ NOMBRE DE HÉROE</label>
          <input class="form-input" type="text" name="nombre" placeholder="Tu nombre..." required>
        </div>
        <div class="form-group">
          <label class="form-label">▸ CONTRASEÑA SECRETA</label>
          <input class="form-input" type="password" name="contrasena" placeholder="••••••••" required>
        </div>
        <button class="btn" name="login">⚔ INICIAR AVENTURA</button>
      </form>
    </div>

    <div id="panel-registro" class="panel">
      <form method="POST">
        <div class="form-group">
          <label class="form-label">▸ NOMBRE DE HÉROE</label>
          <input class="form-input" type="text" name="nombre" placeholder="Elige tu nombre..." required>
        </div>
        <div class="form-group">
          <label class="form-label">▸ CONTRASEÑA SECRETA</label>
          <input class="form-input" type="password" name="contrasena" placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label class="form-label">▸ CONFIRMAR CONTRASEÑA</label>
          <input class="form-input" type="password" name="confirmar" placeholder="••••••••" required>
        </div>
        <button class="btn" name="registro">📜 CREAR HÉROE</button>
      </form>
    </div>

    <div class="footer-text">⚔ QUE COMIENCEN LOS COMBATES ⚔</div>
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