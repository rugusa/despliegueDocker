<?php
$servername = "db"; // Host de la BDD
$username = "usuario1"; // Nombre de usuario de MySQL
$password = "contrasenyaUsuario1"; // Contraseña de MySQL
$dbname = "cine"; // Nombre de la base de datos
// Crear la conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Lógica para eliminar el registro
if (isset($_POST['id_borrar'])) {
    $id_borrar = intval($_POST['id_borrar']);
    $sql_delete = "DELETE FROM peliculas WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id_borrar);
    if ($stmt->execute()) {
        echo "<p>Registro eliminado correctamente.</p>";
    } else {
        echo "<p>Error al eliminar el registro: " . $conn->error . "</p>";
    }
    $stmt->close();
}
// Lógica para insertar un nuevo registro
if (isset($_POST['titulo'])) {
    $titulo = $_POST['titulo'];
    $director = $_POST['director'];
    $nota = $_POST['nota'];
    $anyo = intval($_POST['anyo']);
    $presupuesto = intval($_POST['presupuesto']);
    $url_trailer = $_POST['url_trailer'];
    // Procesar la imagen si se ha cargado
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_temp = $_FILES['imagen']['tmp_name'];
        $img_data = file_get_contents($imagen_temp);
        $img_base64 = base64_encode($img_data);
    } else {
        $img_base64 = ""; // Si no se carga imagen, se guarda como vacío
    }
    $sql_insert = "INSERT INTO peliculas (titulo, director, nota, anyo, presupuesto, img_base64, url_trailer) VALUES (?,
?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sssisss", $titulo, $director, $nota, $anyo, $presupuesto, $img_base64, $url_trailer);
    if ($stmt->execute()) {
        echo "<p>Nueva película añadida correctamente.</p>";
    } else {
        echo "<p>Error al añadir la película: " . $conn->error . "</p>";
    }
    $stmt->close();
}
// Consulta SQL para seleccionar todo el contenido de la tabla peliculas
$sql = "SELECT * FROM peliculas";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Películas</title>
</head>

<body>
    <h1>Listado de Películas</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Director</th>
                <th>Nota</th>
                <th>Año</th>
                <th>Presupuesto</th>
                <th>Imagen (Base64)</th>
                <th>URL del Trailer</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Salida de cada fila de la tabla
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["titulo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["director"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nota"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["anyo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["presupuesto"]) . "</td>";
                    echo "<td><img src='data:image/jpeg;base64," . htmlspecialchars($row["img_base64"]) . "' alt='Imagen'
width='100' height='100'></td>";
                    echo "<td><a href='" . htmlspecialchars($row["url_trailer"]) . "' target='_blank'>Ver
Trailer</a></td>";
                    echo "<td>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='id_borrar' value='" . $row['id'] . "'>";
                    echo "<input type='submit' value='Eliminar' onclick='return confirm(\"¿Estás seguro de que deseas
eliminar este registro?\");'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No hay registros</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <h2>Añadir Nueva Película</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required><br><br>
        <label for="director">Director:</label><br>
        <input type="text" id="director" name="director" required><br><br>
        <label for="nota">Nota:</label><br>
        <input type="text" id="nota" name="nota" required><br><br>
        <label for="anyo">Año:</label><br>
        <input type="number" id="anyo" name="anyo" required><br><br>
        <label for="presupuesto">Presupuesto:</label><br>
        <input type="number" id="presupuesto" name="presupuesto" required><br><br>
        <label for="imagen">Imagen:</label><br>
        <input type="file" id="imagen" name="imagen" accept="image/*"><br><br>
        <label for="url_trailer">URL del Trailer:</label><br>
        <input type="text" id="url_trailer" name="url_trailer" required><br><br>
        <input type="submit" value="Añadir Película">
    </form>
</body>

</html>
<?php
$conn->close();
?>