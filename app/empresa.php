<?php

include('database_connection.php');

$error = '';

if(isset($_POST['add_empresa']))
{
    $formdata = array();

    if(empty($_POST['nomeEmpresa']))
    {
        $error .= '<li>Nome da Empresa é necessário</li>';
    }
    else
    {
        $formdata['nomeEmpresa'] = trim($_POST['nomeEmpresa']);
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
		SELECT * FROM empresa 
		WHERE nomeEmpresa = '".$formdata['nomeEmpresa']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data da Empresa já existe</li>';
        }
        else
        {
            $data = array(
                ':nomeEmpresa'		=>	$formdata['nomeEmpresa'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'		=>	$formdata['email']
            );

            $query = "
			INSERT INTO empresa 
			(nomeEmpresa, telefone, email) VALUES (:nomeEmpresa, :telefone, :email)
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:empresa.php?msg=add');
        }
    }
}

if(isset($_POST['editar_empresa']))
{
    $formdata = array();

    if(empty($_POST['nomeEmpresa']))
    {
        $error .= '<li>Nome da Empresa é necessário</li>';
    }
    else
    {
        $formdata['nomeEmpresa'] = trim($_POST['nomeEmpresa']);
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
		SELECT * FROM empresa 
		WHERE nomeEmpresa = '".$formdata['nomeEmpresa']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
        AND idEmpresa != '".$_POST['idEmpresa']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data da Empresa já existe</li>';
        }
        else
        {
            $data = array(
                ':nomeEmpresa'		=>	$formdata['nomeEmpresa'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'	=>	$formdata['email'],
                ':idEmpresa'			=>	$_POST['idEmpresa']
            );

            $query = "
			UPDATE empresa 
			SET nomeEmpresa = :nomeEmpresa, 
			telefone = :telefone,
			email = :email 
			WHERE idEmpresa = :idEmpresa
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:empresa.php?msg=edit');
        }
    }
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
    $acedemic_standard_id = $_GET['id'];


    $data = array(
        ':idEmpresa'			=>	$acedemic_standard_id
    );

    $query = "
	DELETE FROM empresa 
	WHERE idEmpresa = :idEmpresa
	";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    header('location:empresa.php?msg=delete');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestão de Empresas</h1>
    <?php
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
            ?>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="empresa.php">Gestão de Empresas</a></li>
                <li class="breadcrumb-item active">Adicionar Empresa</li>
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
                            <i class="fas fa-user-plus"></i> Adicionar Empresa
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Introduz o Nome da Empresa <span class="text-danger">*</span></label>
                                    <input type="text" name="nomeEmpresa" class="form-control" />
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
                                    <input type="submit" name="add_empresa" class="btn btn-success" value="Add" />
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
				SELECT * FROM empresa 
				WHERE idEmpresa = '".$_GET["id"]."'
				";

                $academic_standard_result = $connect->query($query, PDO::FETCH_ASSOC);

                foreach($academic_standard_result as $academic_standard_row)
                {
                    ?>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="empresa.php">Gestão de Empresas</a></li>
                        <li class="breadcrumb-item active">Editar Empresa</li>
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
                                    <i class="fas fa-user-edit"></i> Editar Empresa
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Introduz o Nome da Empresa <span class="text-danger">*</span></label>
                                            <input type="text" name="nomeEmpresa" class="form-control" value="<?php echo $academic_standard_row["nomeEmpresa"]; ?>" />
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
                                            <input type="hidden" name="idEmpresa" value="<?php echo $academic_standard_row['idEmpresa'];?>" />
                                            <input type="submit" name="editar_Empresa" class="btn btn-success" value="Edit" />
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
            <li class="breadcrumb-item active">Gestão de Empresas</li>
        </ol>
        <?php

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Empresa Adicionada Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'edit')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Empresa Editada Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'delete')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Empresa Apagada Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Empresas
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="empresa.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="empresa_data" class="table table-bordered table-striped">
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

var dataTable = $('#empresa_data').DataTable({
	"processing" : true,
	"serverSide" : true,
	"order" : [],
	"ajax" : {
		url : "action.php",
		type : "POST",
		data : {action : "fetch_empresa"}
	},
    "language": {
    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
}
});

function delete_data(id)
{

	if(confirm("Quer mesmo apagar esta Empresa?"))
	{
		window.location.href = 'empresa.php?action=delete&id='+id+'';
	}
}

</script>