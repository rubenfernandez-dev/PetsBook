<?php
// Redirección al proceso de borrar
$id = $_GET['id'] ?? '';
header('Location: ../procesos/borrar_cita.php?id=' . $id);
exit();
?>
