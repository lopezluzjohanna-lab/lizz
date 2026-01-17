<?php
// 1. CONFIGURACIÓN DE LA CONEXIÓN
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "proyecto_imc";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 2. LÓGICA PARA ELIMINAR
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM registros_imc WHERE id=$id");
    header("Location: index.php");
}

// 3. LÓGICA PARA AGREGAR O EDITAR
if (isset($_POST['save'])) {
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];
    $peso = $_POST['peso'];
    $estatura = $_POST['estatura'];
    $sexo = $_POST['sexo'];
    
    // CALCULO DEL IMC
    $imc = $peso / ($estatura * $estatura);
    $imc = round($imc, 2);

    // DETERMINAR ESTADO
    if ($imc < 18.5) $estado = "Bajo peso";
    elseif ($imc < 25) $estado = "Peso normal";
    elseif ($imc < 30) $estado = "Sobrepeso";
    else $estado = "Obesidad";

    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE registros_imc SET nombre='$nombre', edad=$edad, peso=$peso, estatura=$estatura, sexo='$sexo', imc=$imc, estado='$estado' WHERE id=$id";
    } else {
        $sql = "INSERT INTO registros_imc (nombre, edad, peso, estatura, sexo, imc, estado) VALUES ('$nombre', $edad, $peso, $estatura, '$sexo', $imc, '$estado')";
    }
    
    $conn->query($sql);
    header("Location: index.php");
}

// Lógica para cargar datos al editar
$u_id = $u_nombre = $u_edad = $u_peso = $u_estatura = $u_sexo = "";
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM registros_imc WHERE id=$id");
    $row = $res->fetch_assoc();
    $u_id = $row['id'];
    $u_nombre = $row['nombre'];
    $u_edad = $row['edad'];
    $u_peso = $row['peso'];
    $u_estatura = $row['estatura'];
    $u_sexo = $row['sexo'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora IMC Rosa</title>
    <style>
        :root {
            --rosa-claro: #ffe4e1;
            --rosa-medio: #ffb6c1;
            --rosa-fuerte: #ff69b4;
            --rosa-oscuro: #c71585;
            --blanco: #ffffff;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: var(--rosa-claro);
            color: #333;
        }

        .container { 
            max-width: 1000px;
            margin: auto;
            background: var(--blanco); 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(199, 21, 133, 0.2); 
        }

        h2 { 
            color: var(--rosa-oscuro); 
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Estilo del Formulario */
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            background: #fffafa;
            padding: 20px;
            border-radius: 15px;
            border: 2px dashed var(--rosa-medio);
            margin-bottom: 30px;
        }

        form input, form select { 
            padding: 12px; 
            border: 1px solid var(--rosa-medio);
            border-radius: 10px;
            outline: none;
            transition: 0.3s;
        }

        form input:focus {
            border-color: var(--rosa-fuerte);
            box-shadow: 0 0 8px var(--rosa-medio);
        }

        .btn-submit { 
            padding: 12px 25px; 
            background: var(--rosa-fuerte); 
            color: white; 
            border: none; 
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: var(--rosa-oscuro);
            transform: translateY(-2px);
        }

        /* Estilo de la Tabla */
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin-top: 20px; 
            overflow: hidden;
            border-radius: 15px;
        }

        th, td { 
            padding: 15px; 
            text-align: center; 
            border-bottom: 1px solid var(--rosa-claro);
        }

        th { 
            background-color: var(--rosa-medio); 
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:hover {
            background-color: #fff0f5;
        }

        /* Botones de acción */
        .btn { 
            padding: 6px 12px; 
            text-decoration: none; 
            color: white; 
            border-radius: 8px; 
            font-size: 13px;
            margin: 2px;
            display: inline-block;
        }

        .btn-edit { background: #5db0ff; }
        .btn-delete { background: #ff4d6d; }
        
        .badge-imc {
            background: var(--rosa-oscuro);
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>💖 Control de IMC 💖</h2>
    
    <form action="index.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $u_id; ?>">
        <input type="text" name="nombre" placeholder="Nombre completo" value="<?php echo $u_nombre; ?>" required>
        <input type="number" name="edad" placeholder="Edad" style="width: 70px;" value="<?php echo $u_edad; ?>" required>
        <input type="number" step="0.01" name="weight" placeholder="Peso (kg)" style="width: 100px;" value="<?php echo $u_peso; ?>" required>
        <input type="number" step="0.01" name="height" placeholder="Estatura (m)" style="width: 100px;" value="<?php echo $u_estatura; ?>" required>
        
        <select name="sexo">
            <option value="Masculino" <?php if($u_sexo == 'Masculino') echo 'selected'; ?>>Masculino</option>
            <option value="Femenino" <?php if($u_sexo == 'Femenino') echo 'selected'; ?>>Femenino</option>
        </select>

        <button type="submit" name="save" class="btn-submit">
            <?php echo $u_id ? "Actualizar Datos" : "Registrar Ahora"; ?>
        </button>
        <?php if($u_id): ?> 
            <a href="index.php" style="color: var(--rosa-oscuro); margin-top: 15px;">Cancelar</a> 
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Peso</th>
                <th>Estatura</th>
                <th>Sexo</th>
                <th>IMC</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $resultado = $conn->query("SELECT * FROM registros_imc ORDER BY id DESC");
            if($resultado->num_rows > 0):
                while ($row = $resultado->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><strong><?php echo $row['nombre']; ?></strong></td>
                <td><?php echo $row['edad']; ?></td>
                <td><?php echo $row['peso']; ?> kg</td>
                <td><?php echo $row['estatura']; ?> m</td>
                <td><?php echo $row['sexo']; ?></td>
                <td><span class="badge-imc"><?php echo $row['imc']; ?></span></td>
                <td><em><?php echo $row['estado']; ?></em></td>
                <td>
                    <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
                    <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">Eliminar</a>
                </td>
            </tr>
            <?php 
                endwhile; 
            else:
            ?>
            <tr>
                <td colspan="9" style="text-align: center; color: var(--rosa-fuerte);">No hay registros aún. ¡Comienza agregando uno!</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>