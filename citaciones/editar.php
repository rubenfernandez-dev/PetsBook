<?php
// Redirección a citaciones.php con acción editar
$id = $_GET['id'] ?? '';
header('Location: citaciones.php?accion=editar&id=' . $id);
exit();
?>
