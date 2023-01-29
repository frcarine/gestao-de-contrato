<?php

include('database_connection.php');

$error = '';

if(isset($_POST['add_setorResponsavel']))
{
    $formdata = array();

    if(empty($_POST['setorResponsavel']))
    {
        $error .= '<li>Nome do Setor Responsável é necessário</li>';
    }
    else
    {
        $formdata['setorResponsavel'] = trim($_POST['setorResponsavel']);
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
		SELECT * FROM setorResponsavel 
		WHERE setorResponsavel = '".$formdata['setorResponsavel']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data do Setor Responsável já existe</li>';
        }
        else
        {
            $data = array(
                ':setorResponsavel'		=>	$formdata['setorResponsavel'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'		=>	$formdata['email']
            );

            $query = "
			INSERT INTO setorResponsavel 
			(setorResponsavel, telefone, email) VALUES (:setorResponsavel, :telefone, :email)
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:setorResponsavel.php?msg=add');
        }
    }
}

if(isset($_POST['editar_setorResponsavel']))
{
    $formdata = array();

    if(empty($_POST['setorResponsavel']))
    {
        $error .= '<li>Nome do Setor Responsável é necessário</li>';
    }
    else
    {
        $formdata['setorResponsavel'] = trim($_POST['setorResponsavel']);
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
		SELECT * FROM setorResponsavel 
		WHERE setorResponsavel = '".$formdata['setorResponsavel']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
        AND idSetorResponsavel != '".$_POST['idSetorResponsavel']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data do Setor Responsável já existe</li>';
        }
        else
        {
            $data = array(
                ':setorResponsavel'		=>	$formdata['setorResponsavel'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'	=>	$formdata['email'],
                ':idSetorResponsavel'			=>	$_POST['idSetorResponsavel']
            );

            $query = "
			UPDATE setorResponsavel 
			SET setorResponsavel = :setorResponsavel, 
			telefone = :telefone,
			email = :email 
			WHERE idSetorResponsavel = :idSetorResponsavel
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:setorResponsavel.php?msg=edit');
        }
    }
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
    $acedemic_standard_id = $_GET['id'];


    $data = array(
        ':idSetorResponsavel'			=>	$acedemic_standard_id
    );

    $query = "
	DELETE FROM setorResponsavel 
	WHERE idSetorResponsavel = :idSetorResponsavel
	";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    header('location:setorResponsavel.php?msg=delete');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestão de Setores Responsáveis</h1>
    <?php
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
            ?>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="empresa.php">Gestão de Setores Responsáveis</a></li>
                <li class="breadcrumb-item active">Adicionar Setor Responsável</li>
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
                            <i class="fas fa-user-plus"></i> Adicionar Setor Responsável
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Introduz o Nome do Setor Responsável <span class="text-danger">*</span></label>
                                    <input type="text" name="setorResponsavel" class="form-control" />
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
                                    <input type="submit" name="add_setorResponsavel" class="btn btn-success" value="Add" />
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
				SELECT * FROM setorResponsavel 
				WHERE idSetorResponsavel = '".$_GET["id"]."'
				";

                $academic_standard_result = $connect->query($query, PDO::FETCH_ASSOC);

                foreach($academic_standard_result as $academic_standard_row)
                {
                    ?>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="setorResponsavel.php">Gestão de Setores Responsáveis</a></li>
                        <li class="breadcrumb-item active">Editar Setor Responsável</li>
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
                                    <i class="fas fa-user-edit"></i> Editar Setor Responsável
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Introduz o Nome do Setor Responsável <span class="text-danger">*</span></label>
                                            <input type="text" name="setorResponsavel" class="form-control" value="<?php echo $academic_standard_row["setorResponsavel"]; ?>" />
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
                                            <input type="hidden" name="idSetorResponsavel" value="<?php echo $academic_standard_row['idSetorResponsavel'];?>" />
                                            <input type="submit" name="editar_setorResponsavel" class="btn btn-success" value="Edit" />
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
            <li class="breadcrumb-item active">Gestão de Setores Responsáveis</li>
        </ol>
        <?php

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Setor Responsável Adicionado Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'edit')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Setor Responsável Editado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'delete')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Setor Responsável Apagado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Setores Responsáveis
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="setorResponsavel.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="setorResponsavel_data" class="table table-bordered table-striped">
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

var dataTable = $('#setorResponsavel_data').DataTable({
	"processing" : true,
	"serverSide" : true,
	"order" : [],
	"ajax" : {
		url : "action.php",
		type : "POST",
		data : {action : "fetch_setorResponsavel"}
	},
    "language": {
    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
}
});

function delete_data(id)
{

	if(confirm("Quer mesmo apagar este Setor Responsável?"))
	{
		window.location.href = 'setorResponsavel.php?action=delete&id='+id+'';
	}
}

</script>