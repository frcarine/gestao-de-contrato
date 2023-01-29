<?php

include('database_connection.php');

$error = '';

if(isset($_POST['add_responsavel']))
{
    $formdata = array();

    if(empty($_POST['nomeResponsavel']))
    {
        $error .= '<li>Nome do Responsável é necessário</li>';
    }
    else
    {
        $formdata['nomeResponsavel'] = trim($_POST['nomeResponsavel']);
    }

    if(empty($_POST['telefone']))
    {
        $error .= '<li>Número de Telefone Necessário</li>';
    }
    else
    {
        $formdata['telefone'] = trim($_POST['telefone']);
    }

    if(empty($_POST['email']))
    {
        $error .= '<li>Email Necessário</li>';
    }
    else
    {
        $formdata['email'] = trim($_POST['email']);
    }

    if($error == '')
    {
        $query = "
		SELECT * FROM responsavel 
		WHERE nomeResponsavel = '".$formdata['nomeResponsavel']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data do Responsável já existe</li>';
        }
        else
        {
            $data = array(
                ':nomeResponsavel'		=>	$formdata['nomeResponsavel'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'		=>	$formdata['email']
            );

            $query = "
			INSERT INTO responsavel 
			(nomeResponsavel, telefone, email) VALUES (:nomeResponsavel, :telefone, :email)
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:responsavel.php?msg=add');
        }
    }
}

if(isset($_POST['editar_responsavel']))
{
    $formdata = array();

    if(empty($_POST['nomeResponsavel']))
    {
        $error .= '<li>Nome do Responsável é necessário</li>';
    }
    else
    {
        $formdata['nomeResponsavel'] = trim($_POST['nomeResponsavel']);
    }

    if(empty($_POST['telefone']))
    {
        $error .= '<li>Número de Telefone Necessário</li>';
    }
    else
    {
        $formdata['telefone'] = trim($_POST['telefone']);
    }

    if(empty($_POST['email']))
    {
        $error .= '<li>Email Necessário</li>';
    }
    else
    {
        $formdata['email'] = trim($_POST['email']);
    }

    if($error == '')
    {
        $query = "
		SELECT * FROM responsavel 
		WHERE nomeResponsavel = '".$formdata['nomeResponsavel']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
        AND idResponsavel != '".$_POST['idResponsavel']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data do Responsável já existe</li>';
        }
        else
        {
            $data = array(
                ':nomeResponsavel'		=>	$formdata['nomeResponsavel'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'	=>	$formdata['email'],
                ':idResponsavel'			=>	$_POST['idResponsavel']
            );

            $query = "
			UPDATE responsavel 
			SET nomeResponsavel = :nomeResponsavel, 
			telefone = :telefone,
			email = :email 
			WHERE idResponsavel = :idResponsavel
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:responsavel.php?msg=edit');
        }
    }
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
    $acedemic_standard_id = $_GET['id'];


    $data = array(
        ':idResponsavel'			=>	$acedemic_standard_id
    );

    $query = "
	DELETE FROM responsavel 
	WHERE idResponsavel = :idResponsavel
	";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    header('location:responsavel.php?msg=delete');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestão de Responsáveis</h1>
    <?php
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
            ?>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="empresa.php">Gestão de Responsáveis</a></li>
                <li class="breadcrumb-item active">Adicionar Responsável</li>
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
                            <i class="fas fa-user-plus"></i> Adicionar Responsável
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Introduz o Nome do Responsável <span class="text-danger">*</span></label>
                                    <input type="text" name="nomeResponsavel" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label>Introduz o Número de Telefone <span class="text-danger">*</span></label>
                                    <input type="text" name="telefone" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label>Introduz o Email <span class="text-danger">*</span></label>
                                    <input type="text" name="email" class="form-control" />
                                </div>
                                <div class="mt-4 mb-0">
                                    <input type="submit" name="add_responsavel" class="btn btn-success" value="Add" />
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
				SELECT * FROM responsavel 
				WHERE idResponsavel = '".$_GET["id"]."'
				";

                $academic_standard_result = $connect->query($query, PDO::FETCH_ASSOC);

                foreach($academic_standard_result as $academic_standard_row)
                {
                    ?>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="empresa.php">Gestão de Responsáveis</a></li>
                        <li class="breadcrumb-item active">Editar Responsável</li>
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
                                    <i class="fas fa-user-edit"></i> Editar Responsável
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Introduz o Nome do Responsável <span class="text-danger">*</span></label>
                                            <input type="text" name="nomeResponsavel" class="form-control" value="<?php echo $academic_standard_row["nomeResponsavel"]; ?>" />
                                        </div>
                                        <div class="mb-3">
                                            <label>Introduz o Número de Telefone <span class="text-danger">*</span></label>
                                            <input type="text" name="telefone" class="form-control" value="<?php echo $academic_standard_row["telefone"]; ?>" />
                                        </div>
                                        <div class="mb-3">
                                            <label>Introduz o Email <span class="text-danger">*</span></label>
                                            <input type="text" name="email" class="form-control" value="<?php echo $academic_standard_row["email"]; ?>" />
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="hidden" name="idResponsavel" value="<?php echo $academic_standard_row['idResponsavel'];?>" />
                                            <input type="submit" name="editar_responsavel" class="btn btn-success" value="Edit" />
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
            <li class="breadcrumb-item active">Gestão de Responsáveis</li>
        </ol>
        <?php

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Responsável Adicionado Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'edit')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Responsável Editado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'delete')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Responsável Apagado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Responsáveis
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="responsavel.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="responsavel_data" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
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

var dataTable = $('#responsavel_data').DataTable({
	"processing" : true,
	"serverSide" : true,
	"order" : [],
	"ajax" : {
		url : "action.php",
		type : "POST",
		data : {action : "fetch_responsavel"}
	},
    "language": {
    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
}
});

function delete_data(id)
{

	if(confirm("Quer mesmo apagar este Responsável?"))
	{
		window.location.href = 'responsavel.php?action=delete&id='+id+'';
	}
}

</script>