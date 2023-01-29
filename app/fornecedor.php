<?php

//academic_standard.php

include('database_connection.php');

$error = '';

if(isset($_POST['add_fornecedor']))
{
    $formdata = array();

    if(empty($_POST['nomeFornecedor']))
    {
        $error .= '<li>Nome do Fornecedor é necessário</li>';
    }
    else
    {
        $formdata['nomeFornecedor'] = trim($_POST['nomeFornecedor']);
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
		SELECT * FROM fornecedor 
		WHERE nomeFornecedor = '".$formdata['nomeFornecedor']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data de Fornecedor já existe</li>';
        }
        else
        {
            $data = array(
                ':nomeFornecedor'		=>	$formdata['nomeFornecedor'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'		=>	$formdata['email']
            );

            $query = "
			INSERT INTO fornecedor 
			(nomeFornecedor, telefone, email) VALUES (:nomeFornecedor, :telefone, :email)
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:fornecedor.php?msg=add');
        }
    }
}

if(isset($_POST['editar_fornecedor']))
{
    $formdata = array();

    if(empty($_POST['nomeFornecedor']))
    {
        $error .= '<li>Nome do Fornecedor é necessário</li>';
    }
    else
    {
        $formdata['nomeFornecedor'] = trim($_POST['nomeFornecedor']);
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
		SELECT * FROM fornecedor 
		WHERE nomeFornecedor = '".$formdata['nomeFornecedor']."' 
		AND telefone = '".$formdata['telefone']."' 
		AND email = '".$formdata['email']."' 
        AND idFornecedor != '".$_POST['idFornecedor']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data de Fornecedor já existe</li>';
        }
        else
        {
            $data = array(
                ':nomeFornecedor'		=>	$formdata['nomeFornecedor'],
                ':telefone'	=>	$formdata['telefone'],
                ':email'	=>	$formdata['email'],
                ':idFornecedor'			=>	$_POST['idFornecedor']
            );

            $query = "
			UPDATE fornecedor 
			SET nomeFornecedor = :nomeFornecedor, 
			telefone = :telefone,
			email = :email 
			WHERE idFornecedor = :idFornecedor
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:fornecedor.php?msg=edit');
        }
    }
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
    $acedemic_standard_id = $_GET['id'];

    $status = trim($_GET["status"]);

    $data = array(
        ':idFornecedor'			=>	$acedemic_standard_id
    );

    $query = "
	DELETE FROM fornecedor 
	WHERE idFornecedor = :idFornecedor
	";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    header('location:fornecedor.php?msg=delete');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestão de Fornecedores</h1>
    <?php
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
            ?>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="fornecedor.php">Gestão de Fornecedores</a></li>
                <li class="breadcrumb-item active">Adicionar Fornecedor</li>
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
                            <i class="fas fa-user-plus"></i> Adicionar Fornecedor
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Introduz o Nome do Fornecedor <span class="text-danger">*</span></label>
                                    <input type="text" name="nomeFornecedor" class="form-control" />
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
                                    <input type="submit" name="add_fornecedor" class="btn btn-success" value="Add" />
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
				SELECT * FROM fornecedor 
				WHERE idFornecedor = '".$_GET["id"]."'
				";

                $academic_standard_result = $connect->query($query, PDO::FETCH_ASSOC);

                foreach($academic_standard_result as $academic_standard_row)
                {
                    ?>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="fornecedor.php">Gestão de Fornecedor</a></li>
                        <li class="breadcrumb-item active">Editar Fornecedor</li>
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
                                    <i class="fas fa-user-edit"></i> Editar Fornecedor
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label>Introduz o Nome do Fornecedor <span class="text-danger">*</span></label>
                                            <input type="text" name="nomeFornecedor" class="form-control" value="<?php echo $academic_standard_row["nomeFornecedor"]; ?>" />
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
                                            <input type="hidden" name="idFornecedor" value="<?php echo $academic_standard_row['idFornecedor'];?>" />
                                            <input type="submit" name="editar_fornecedor" class="btn btn-success" value="Edit" />
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
            <li class="breadcrumb-item active">Gestão de Fornecedores</li>
        </ol>
        <?php

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Forcenedor Adicionado Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'edit')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Fornecedor Editado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET['msg'] == 'delete')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Fornecedor Apagado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Fornecedores
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="fornecedor.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="fornecedor_data" class="table table-bordered table-striped">
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

var dataTable = $('#fornecedor_data').DataTable({
	"processing" : true,
	"serverSide" : true,
	"order" : [],
	"ajax" : {
		url : "action.php",
		type : "POST",
		data : {action : "fetch_fornecedor"}
	},
    "language": {
    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
}
});

function delete_data(id)
{

	if(confirm("Quer mesmo apagar este Fornecedor?"))
	{
		window.location.href = 'fornecedor.php?action=delete&id='+id+'';
	}
}

</script>