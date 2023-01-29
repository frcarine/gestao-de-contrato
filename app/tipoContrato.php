<?php

include('database_connection.php');

$error = '';

if(isset($_POST['add_TipoContrato']))
{
    $formdata = array();

    if(empty($_POST['tipoContrato']))
    {
        $error .= '<li>Tipo de Contrato é Necessário</li>';
    }
    else
    {
        $formdata['tipoContrato'] = trim($_POST['tipoContrato']);
    }

    if($error == '')
    {
        $query = "
		SELECT * FROM tipoContrato 
		WHERE tipoContrato = '".$formdata['tipoContrato']."' 
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Tipo de Contrato já existe</li>';
        }
        else
        {
            $data = array(
                ':tipoContrato'		=>	$formdata['tipoContrato'],
            );

            $query = "
			INSERT INTO tipoContrato 
			(tipoContrato) VALUES (:tipoContrato)
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:tipoContrato.php?msg=add');
        }
    }
}

if(isset($_POST['editar_tipoContrato']))
{
    $formdata = array();

    if(empty($_POST['tipoContrato']))
    {
        $error .= '<li>Tipo de Contrato é necessário</li>';
    }
    else
    {
        $formdata['tipoContrato'] = trim($_POST['tipoContrato']);
    }

    if($error == '')
    {
        $query = "
		SELECT * FROM tipoContrato 
		WHERE tipoContrato = '".$formdata['tipoContrato']."' 
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data do Tipo de Contrato já existe</li>';
        }
        else
        {
            $data = array(
                ':tipoContrato'		=>	$formdata['tipoContrato'],
                ':idTipoContrato'			=>	$_POST['idTipoContrato']
            );

            $query = "
			UPDATE tipoContrato 
			SET tipoContrato = :tipoContrato 
			WHERE idTipoContrato = :idTipoContrato
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:tipoContrato.php?msg=edit');
        }
    }
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
    $acedemic_standard_id = $_GET['id'];

    $data = array(
        ':idTipoContrato'			=>	$acedemic_standard_id
    );

    $query = "
	DELETE FROM tipoContrato 
	WHERE idTipoContrato = :idTipoContrato
	";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    header('location:tipoContrato.php?msg=delete');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestão de Tipos de Contrato</h1>
    <?php
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
            ?>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="tipoContrato.php">Gestão de Tipos de Contrato</a></li>
                <li class="breadcrumb-item active">Adicionar Tipo de Contrato</li>
            </ol>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    if($error != '')
                    {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    }
                    ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-plus"></i> Adicionar Tipo de Contrato
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Introduz o Tipo de Contrato <span class="text-danger">*</span></label>
                                    <input type="text" name="tipoContrato" class="form-control" />
                                </div>
                                <div class="mt-4 mb-0">
                                    <input type="submit" name="add_TipoContrato" class="btn btn-success" value="Add" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        else if($_GET['action'] == 'edit')
        {
            if(isset($_GET['id']))
            {
                $query = "
				SELECT * FROM tipoContrato 
				WHERE idTipoContrato = '".$_GET["id"]."'
				";

                $academic_standard_result = $connect->query($query, PDO::FETCH_ASSOC);

                foreach($academic_standard_result as $academic_standard_row)
                {
                    ?>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="tipoContrato.php">Gestão de Tipos de Contrato</a></li>
                        <li class="breadcrumb-item active">Editar Tipo de Contrato</li>
                    </ol>
                    <div class="row">
                        <div class="col-md-6">
                            <?php

                            if($error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-edit"></i> Editar Tipo de Contrato
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Introduz o Tipo de Contrato <span class="text-danger">*</span></label>
                                            <input type="text" name="tipoContrato" class="form-control" value="<?php echo $academic_standard_row["tipoContrato"]; ?>" />
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="hidden" name="idTipoContrato" value="<?php echo $academic_standard_row['idTipoContrato'];?>" />
                                            <input type="submit" name="editar_tipoContrato" class="btn btn-success" value="Edit" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    }
    else
    {
        ?>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Gestão de Tipos de Contrato</li>
        </ol>
        <?php

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Tipo de Contrato Adicionado Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'edit')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Tipo de Contrato Editado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'delete')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Tipo de Contrato Apagado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Tipos de Contrato
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="tipoContrato.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="tipoContrato_data" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Tipo de Contrato</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<script>

var dataTable = $('#tipoContrato_data').DataTable({
	"processing" : true,
	"serverSide" : true,
	"order" : [],
	"ajax" : {
		url : "action.php",
		type : "POST",
		data : {action : "fetch_tipoContrato"}
	},
    "language": {
    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
}
});

function delete_data(id)
{

	if(confirm("Quer mesmo apagar este tipo de contrato?"))
	{
		window.location.href = 'tipoContrato.php?action=delete&id='+id+'';
	}
}

</script>