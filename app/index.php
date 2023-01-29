<?php

include('database_connection.php');


$error = '';

if(isset($_POST['add_contrato']))
{
    $formdata = array();

    if(empty($_POST['numeroContrato']))
    {
        $error .= '<li>Introduz Um Número de Contrato</li>';
    }
    else
    {
        $formdata['numeroContrato'] = trim($_POST['numeroContrato']);
    }

    if($_POST['descricao'])
    {
        $formdata['descricao'] = trim($_POST['descricao']);
    }

    if(empty($_POST['idEmpresa']))
    {
        $error .= '<li>Escolhe uma Empresa</li>';
    }
    else
    {
        $formdata['idEmpresa'] = trim($_POST['idEmpresa']);
    }

    if(empty($_POST['idFornecedor']))
    {
        $error .= '<li>Escolhe um Fornecedor</li>';
    }
    else
    {
        $formdata['idFornecedor'] = trim($_POST['idFornecedor']);
    }

    if(empty($_POST['idTipoContrato']))
    {
        $error .= '<li>Escolhe um Tipo de Contrato</li>';
    }
    else
    {
        $formdata['idTipoContrato'] = trim($_POST['idTipoContrato']);
    }

    if(empty($_POST['idResponsavel']))
    {
        $error .= '<li>Escolhe um Responsável</li>';
    }
    else
    {
        $formdata['idResponsavel'] = trim($_POST['idResponsavel']);
    }
    if(empty($_POST['idSetorResponsavel']))
    {
        $error .= '<li>Escolhe um Setor Responsável</li>';
    }
    else
    {
        $formdata['idSetorResponsavel'] = trim($_POST['idSetorResponsavel']);
    }

    if($_POST['dataInicioVigencia'])
    {
        $formdata['dataInicioVigencia'] = trim($_POST['dataInicioVigencia']);
    }

    if($_POST['dataTerminoVigencia'])
    {
        $formdata['dataTerminoVigencia'] = trim($_POST['dataTerminoVigencia']);
    }

    if($_POST['dataInicioExecucao'])
    {
        $formdata['dataInicioExecucao'] = trim($_POST['dataInicioExecucao']);
    }

    if($_POST['dataTerminoExecucao'])
    {
        $formdata['dataTerminoExecucao'] = trim($_POST['dataTerminoExecucao']);
    }

    if($_POST['dataAssinatura'])
    {
        $formdata['dataAssinatura'] = trim($_POST['dataAssinatura']);
    }

    if($_POST['valorGlobal'])
    {
        $formdata['valorGlobal'] = trim($_POST['valorGlobal']);
    }
    if($_POST['qtdParcelas'])
    {
        $formdata['qtdParcelas'] = trim($_POST['qtdParcelas']);
    }
    if($_POST['valorParcela'])
    {
        $formdata['valorParcela'] = trim($_POST['valorParcela']);
    }
    if($_POST['telefone'])
    {
        $formdata['telefone'] = trim($_POST['telefone']);
    }

    if($_POST['email'])
    {
        $formdata['email'] = trim($_POST['email']);
    }

    if($_POST['notificacaoFimVigencia'])
    {
        $formdata['notificacaoFimVigencia'] = trim($_POST['notificacaoFimVigencia']);
    }


    if(empty($_POST['statusContrato']))
    {
        $error .= '<li>Escolhe um Estado do Contrato</li>';
    }
    else
    {
        $formdata['statusContrato'] = trim($_POST['statusContrato']);
    }

    if($_POST['objetivoContrato'])
    {
        $formdata['objetivoContrato'] = trim($_POST['objetivoContrato']);
    }

    if($error == '')
    {
        //already exits
        $query = "
		SELECT * FROM contrato 
		WHERE numeroContrato = '".$formdata['numeroContrato']."' 
		AND descricao = '".$formdata['descricao']."'
		AND idEmpresa = '".$formdata['idEmpresa']."' 
		AND idFornecedor = '".$formdata['idFornecedor']."'
		AND idTipoContrato = '".$formdata['idTipoContrato']."'
		AND idResponsavel = '".$formdata['idResponsavel']."'
		AND idSetorResponsavel = '".$formdata['idSetorResponsavel']."'
		AND dataInicioVigencia = '".$formdata['dataInicioVigencia']."'
		AND dataTerminoVigencia = '".$formdata['dataTerminoVigencia']."'
		AND dataInicioExecucao = '".$formdata['dataInicioExecucao']."'
		AND dataTerminoExecucao = '".$formdata['dataTerminoExecucao']."'
		AND dataAssinatura = '".$formdata['dataAssinatura']."'
		AND valorGlobal = '".$formdata['valorGlobal']."'
		AND qtdParcelas = '".$formdata['qtdParcelas']."'
		AND valorParcela = '".$formdata['valorParcela']."'
		AND telefone = '".$formdata['telefone']."'
		AND email = '".$formdata['email']."'
		AND notificacaoFimVigencia = '".$formdata['notificacaoFimVigencia']."'
		AND statusContrato = '".$formdata['statusContrato']."'
		AND objetivoContrato = '".$formdata['objetivoContrato']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Data do Contrato Já Existe</li>';
        }
        else
        {
            $data = array(
                ':numeroContrato'			=>	$formdata['numeroContrato'],
                ':descricao'			=>	$formdata['descricao'],
                ':idEmpresa'			=>	$formdata['idEmpresa'],
                ':idFornecedor'			=>	$formdata['idFornecedor'],
                ':idTipoContrato'			=>	$formdata['idTipoContrato'],
                ':idResponsavel'			=>	$formdata['idResponsavel'],
                ':idSetorResponsavel'			=>	$formdata['idSetorResponsavel'],
                ':dataInicioVigencia'			=>	$formdata['dataInicioVigencia'],
                ':dataTerminoVigencia'			=>	$formdata['dataTerminoVigencia'],
                ':dataInicioExecucao'			=>	$formdata['dataInicioExecucao'],
                ':dataTerminoExecucao'			=>	$formdata['dataTerminoExecucao'],
                ':dataAssinatura'			=>	$formdata['dataAssinatura'],
                ':valorGlobal'			=>	$formdata['valorGlobal'],
                ':qtdParcelas'			=>	$formdata['qtdParcelas'],
                ':valorParcela'			=>	$formdata['valorParcela'],
                ':telefone'			=>	$formdata['telefone'],
                ':email'			=>	$formdata['email'],
                ':notificacaoFimVigencia'			=>	$formdata['notificacaoFimVigencia'],
                ':statusContrato'			=>	$formdata['statusContrato'],
                ':objetivoContrato'			=>	$formdata['objetivoContrato']
            );

            $query = "
			INSERT INTO contrato 
			(numeroContrato, descricao, idEmpresa, idFornecedor, idTipoContrato, idResponsavel, idSetorResponsavel,dataInicioVigencia,dataTerminoVigencia,dataInicioExecucao,dataTerminoExecucao,dataAssinatura,valorGlobal,qtdParcelas,valorParcela,telefone,email,notificacaoFimVigencia, statusContrato, objetivoContrato) VALUES (:numeroContrato, :descricao, :idEmpresa, :idFornecedor, :idTipoContrato, :idResponsavel, :idSetorResponsavel, :dataInicioVigencia,:dataTerminoVigencia, :dataInicioExecucao, :dataTerminoExecucao, :dataAssinatura, :valorGlobal, :qtdParcelas, :valorParcela, :telefone, :email, :notificacaoFimVigencia, :statusContrato, :objetivoContrato)
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:index.php?msg=add');
        }
    }
}

if(isset($_POST['editar_contrato']))
{
    $formdata = array();

    if(empty($_POST['numeroContrato']))
    {
        $error .= '<li>Introduz Um Número de Contrato</li>';
    }
    else
    {
        $formdata['numeroContrato'] = trim($_POST['numeroContrato']);
    }

    if($_POST['descricao'])
    {
        $formdata['descricao'] = trim($_POST['descricao']);
    }

    if(empty($_POST['idEmpresa']))
    {
        $error .= '<li>Escolhe uma Empresa</li>';
    }
    else
    {
        $formdata['idEmpresa'] = trim($_POST['idEmpresa']);
    }

    if(empty($_POST['idFornecedor']))
    {
        $error .= '<li>Escolhe um Fornecedor</li>';
    }
    else
    {
        $formdata['idFornecedor'] = trim($_POST['idFornecedor']);
    }

    if(empty($_POST['idTipoContrato']))
    {
        $error .= '<li>Escolhe um Tipo de Contrato</li>';
    }
    else
    {
        $formdata['idTipoContrato'] = trim($_POST['idTipoContrato']);
    }

    if(empty($_POST['idResponsavel']))
    {
        $error .= '<li>Escolhe um Responsável</li>';
    }
    else
    {
        $formdata['idResponsavel'] = trim($_POST['idResponsavel']);
    }
    if(empty($_POST['idSetorResponsavel']))
    {
        $error .= '<li>Escolhe um Setor Responsável</li>';
    }
    else
    {
        $formdata['idSetorResponsavel'] = trim($_POST['idSetorResponsavel']);
    }

    if($_POST['dataInicioVigencia'])
    {
        $formdata['dataInicioVigencia'] = trim($_POST['dataInicioVigencia']);
    }

    if($_POST['dataTerminoVigencia'])
    {
        $formdata['dataTerminoVigencia'] = trim($_POST['dataTerminoVigencia']);
    }

    if($_POST['dataInicioExecucao'])
    {
        $formdata['dataInicioExecucao'] = trim($_POST['dataInicioExecucao']);
    }

    if($_POST['dataTerminoExecucao'])
    {
        $formdata['dataTerminoExecucao'] = trim($_POST['dataTerminoExecucao']);
    }

    if($_POST['dataAssinatura'])
    {
        $formdata['dataAssinatura'] = trim($_POST['dataAssinatura']);
    }

    if($_POST['valorGlobal'])
    {
        $formdata['valorGlobal'] = trim($_POST['valorGlobal']);
    }
    if($_POST['qtdParcelas'])
    {
        $formdata['qtdParcelas'] = trim($_POST['qtdParcelas']);
    }
    if($_POST['valorParcela'])
    {
        $formdata['valorParcela'] = trim($_POST['valorParcela']);
    }
    if($_POST['telefone'])
    {
        $formdata['telefone'] = trim($_POST['telefone']);
    }

    if($_POST['email'])
    {
        $formdata['email'] = trim($_POST['email']);
    }

    if($_POST['notificacaoFimVigencia'])
    {
        $formdata['notificacaoFimVigencia'] = trim($_POST['notificacaoFimVigencia']);
    }


    if(empty($_POST['statusContrato']))
    {
        $error .= '<li>Escolhe um Estado do Contrato</li>';
    }
    else
    {
        $formdata['statusContrato'] = trim($_POST['statusContrato']);
    }

    if($_POST['objetivoContrato'])
    {
        $formdata['objetivoContrato'] = trim($_POST['objetivoContrato']);
    }

    if($error == '')
    {
        $query = "
		SELECT * FROM contrato 
		WHERE numeroContrato = '".$formdata['numeroContrato']."' 
		AND descricao = '".$formdata['descricao']."'
		AND idEmpresa = '".$formdata['idEmpresa']."' 
		AND idFornecedor = '".$formdata['idFornecedor']."'
		AND idTipoContrato = '".$formdata['idTipoContrato']."'
		AND idResponsavel = '".$formdata['idResponsavel']."'
		AND idSetorResponsavel = '".$formdata['idSetorResponsavel']."'
		AND dataInicioVigencia = '".$formdata['dataInicioVigencia']."'
		AND dataTerminoVigencia = '".$formdata['dataTerminoVigencia']."'
		AND dataInicioExecucao = '".$formdata['dataInicioExecucao']."'
		AND dataTerminoExecucao = '".$formdata['dataTerminoExecucao']."'
		AND dataAssinatura = '".$formdata['dataAssinatura']."'
		AND valorGlobal = '".$formdata['valorGlobal']."'
		AND qtdParcelas = '".$formdata['qtdParcelas']."'
		AND valorParcela = '".$formdata['valorParcela']."'
		AND telefone = '".$formdata['telefone']."'
		AND email = '".$formdata['email']."'
		AND notificacaoFimVigencia = '".$formdata['notificacaoFimVigencia']."'
		AND statusContrato = '".$formdata['statusContrato']."'
		AND objetivoContrato = '".$formdata['objetivoContrato']."'
		AND idContrato != '".$_POST['idContrato']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Student Standard Data Already Exists</li>';
        }
        else
        {
            $data = array(
                ':numeroContrato'			=>	$formdata['numeroContrato'],
                ':descricao'			=>	$formdata['descricao'],
                ':idEmpresa'			=>	$formdata['idEmpresa'],
                ':idFornecedor'			=>	$formdata['idFornecedor'],
                ':idTipoContrato'			=>	$formdata['idTipoContrato'],
                ':idResponsavel'			=>	$formdata['idResponsavel'],
                ':idSetorResponsavel'			=>	$formdata['idSetorResponsavel'],
                ':dataInicioVigencia'			=>	$formdata['dataInicioVigencia'],
                ':dataTerminoVigencia'			=>	$formdata['dataTerminoVigencia'],
                ':dataInicioExecucao'			=>	$formdata['dataInicioExecucao'],
                ':dataTerminoExecucao'			=>	$formdata['dataTerminoExecucao'],
                ':dataAssinatura'			=>	$formdata['dataAssinatura'],
                ':valorGlobal'			=>	$formdata['valorGlobal'],
                ':qtdParcelas'			=>	$formdata['qtdParcelas'],
                ':valorParcela'			=>	$formdata['valorParcela'],
                ':telefone'			=>	$formdata['telefone'],
                ':email'			=>	$formdata['email'],
                ':notificacaoFimVigencia'			=>	$formdata['notificacaoFimVigencia'],
                ':statusContrato'			=>	$formdata['statusContrato'],
                ':objetivoContrato'			=>	$formdata['objetivoContrato'],
                ':idContrato'	=>	$_POST['idContrato']
            );

            $query = "
			UPDATE contrato 
			SET idContrato = :idContrato, 
			numeroContrato = :numeroContrato, 
			descricao = :descricao, 
			idEmpresa = :idEmpresa,
			idFornecedor = :idFornecedor,
			idTipoContrato = :idTipoContrato,
			idResponsavel = :idResponsavel,
			idSetorResponsavel = :idSetorResponsavel,
			dataInicioVigencia = :dataInicioVigencia,
			dataTerminoVigencia = :dataTerminoVigencia,
			dataInicioExecucao = :dataInicioExecucao,
			dataTerminoExecucao = :dataTerminoExecucao,
			dataAssinatura = :dataAssinatura,
			valorGlobal = :valorGlobal,
			qtdParcelas = :qtdParcelas,
			valorParcela = :valorParcela,
			telefone = :telefone,
			email = :email,
			notificacaoFimVigencia = :notificacaoFimVigencia,
			statusContrato = :statusContrato,
			objetivoContrato = :objetivoContrato
			WHERE idContrato = :idContrato
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:index.php?msg=edit');
        }
    }
}


if(isset($_POST['editar_contratoSuspendido']))
{
    $formdata = array();

    if(empty($_POST['statusContrato']))
    {
        $error .= '<li>Escolhe um Estado do Contrato</li>';
    }
    else
    {
        $formdata['statusContrato'] = trim($_POST['statusContrato']);
    }


    if($error == '')
    {
        $query = "
		SELECT * FROM contrato 
		WHERE statusContrato = '".$formdata['statusContrato']."'
		AND idContrato != '".$_POST['idContrato']."'
		";

        $statement = $connect->prepare($query);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
            $error = '<li>Student Standard Data Already Exists</li>';
        }
        else
        {
            $data = array(
                ':statusContrato'			=>	$formdata['statusContrato'],
                ':idContrato'	=>	$_POST['idContrato']
            );

            $query = "
			UPDATE contrato 
			SET statusContrato = :statusContrato
			WHERE idContrato = :idContrato
			";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            header('location:index.php?msg=edit');
        }
    }
}



if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
    $student_standard_id = $_GET["id"];


    $data = array(
        ':idContrato'			=>	$student_standard_id
    );

    $query = "
	DELETE FROM contrato 
	WHERE idContrato = :idContrato
	";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    header('location:index.php?msg=delete');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestão de Contratos</h1>
    <?php
    if(isset($_GET["action"]))
    {
    if($_GET['action'] == 'add')
    {
        ?>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Gestão de Contratos</a></li>
            <li class="breadcrumb-item active">Adicionar Contrato</li>
        </ol>
        <div class="row">
            <div class="col-md-10">
                <?php
                if($error != '')
                {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }
                ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-plus"></i> Adicionar Contrato
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row" class="mb-3">
                                <div class="col-md-3">
                                    <label><b>Número Contrato</b> <span class="text-danger">*</span></label>
                                    <input type="text" name="numeroContrato" class="form-control" />
                                </div>
                                <div class="col-md-9">
                                    <label><b>Descrição</b></label>
                                    <input type="text" name="descricao" class="form-control" />
                                </div>
                            </div>
                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-6">
                                    <label><b>Empresa</b> <span class="text-danger">*</span></label><br>
                                    <select name="idEmpresa" class="form-control">
                                        <option value="">Selecione a Empresa</option>
                                        <?php echo Empresa_lista($connect); ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label><b>Fornecedor</b> <span class="text-danger">*</span></label><br>
                                    <select name="idFornecedor" class="form-control">
                                        <option value="">Selecione o Fornecedor</option>
                                        <?php echo Fornecedor_lista($connect); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-4">
                                    <label><b>Tipo de Contrato</b> <span class="text-danger">*</span></label><br>
                                    <select name="idTipoContrato" class="form-control">
                                        <option value="">Selecione o Tipo de Contrato</option>
                                        <?php echo TipoContrato_lista($connect); ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label><b>Responsável Contrato</b> <span class="text-danger">*</span></label><br>
                                    <select name="idResponsavel" class="form-control">
                                        <option value="">Selecione o Responsável pelo Contrato</option>
                                        <?php echo Responsavel_lista($connect); ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label><b>Setor Responsável</b> <span class="text-danger">*</span></label><br>
                                    <select name="idSetorResponsavel" class="form-control">
                                        <option value="">Selecione o Setor Responsável</option>
                                        <?php echo SetorResponsavel_lista($connect); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-3">
                                    <label><b>Data Início Vigência</b></label><br>
                                    <input type="date" name="dataInicioVigencia" class="form-control select_date" />
                                </div>
                                <div class="col-md-3">
                                    <label><b>Data Término Vigência</b> </label><br>
                                    <input type="date" name="dataTerminoVigencia" class="form-control select_date" />
                                </div>
                                <div class="col-md-3">
                                    <label><b>Data Início Execução</b> </label><br>
                                    <input type="date" name="dataInicioExecucao" class="form-control select_date" />
                                </div>
                                <div class="col-md-3">
                                    <label><b>Data Término Execução</b> </label><br>
                                    <input type="date" name="dataTerminoExecucao" class="form-control select_date" />
                                </div>
                            </div>

                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-3">
                                    <label><b>Data Assinatura</b></label><br>
                                    <input type="date" name="dataAssinatura" class="form-control select_date" />
                                </div>
                                <div class="col-md-3">
                                    <label><b>Valor Global</b> </label><br>
                                    <input type="text" name="valorGlobal" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label><b>Quantidade de Parcelas</b> </label><br>
                                    <input type="text" name="qtdParcelas" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label><b>Valor Parcela</b> </label><br>
                                    <input type="text" name="valorParcela" class="form-control" />
                                </div>
                            </div>

                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-4">
                                    <label><b>Telefone</b></label><br>
                                    <input type="text" name="telefone" class="form-control" />
                                </div>
                                <div class="col-md-4">
                                    <label><b>Email</b></label><br>
                                    <input type="text" name="email" class="form-control" />
                                </div>
                                <div class="col-md-4">
                                    <label><b>Notificação Fim Vigência</b> </label><br>
                                    <input type="text" name="notificacaoFimVigencia" class="form-control" />
                                </div>
                            </div>

                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-4">
                                    <label><b>Status Contrato</b><span class="text-danger">*</span></label><br>
                                    <select name="statusContrato" class="form-control">
                                        <option value="">Status do Contrato</option>
                                        <option value="Ativo">Ativo</option>
                                        <option value="Cancelado">Cancelado</option>
                                        <option value="Prazo Indeterminado">Prazo Indeterminado</option>
                                        <option value="Renovado">Renovado</option>
                                        <option value="Não Renovado">Não Renovado</option>
                                        <option value="Pendente">Pendente</option>
                                        <option value="Suspenso">Suspenso</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-12">
                                    <label><b>Objeto do Contrato (Max 5000 Caracteres)</b></label>
                                    <textarea name="objetivoContrato" rows="5" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="mt-4 mb-0">
                                <input type="submit" name="add_contrato" class="btn btn-success" value="Adicionar" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    else if($_GET["action"] == "edit")
    {
    if(isset($_GET["id"]))
    {
    $query = "
				SELECT * FROM contrato 
				WHERE idContrato = '".$_GET["id"]."'
				";

    $student_standard_result = $connect->query($query, PDO::FETCH_ASSOC);

    foreach($student_standard_result as $student_standard_result_row)
    {
    ?>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Gestão de Contratos</a></li>
            <li class="breadcrumb-item active">Editar Contrato</li>
        </ol>
        <div class="row">
            <div class="col-md-9">
                <?php

                if($error != '')
                {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }

                ?>

                <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Adicionar Contrato
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="row" class="mb-3">
                            <div class="col-md-3">
                                <label><b>Número Contrato</b> <span class="text-danger">*</span></label>
                                <input type="text" name="numeroContrato" class="form-control" value="<?php echo $student_standard_result_row["numeroContrato"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-9">
                                <label><b>Descrição</b></label>
                                <input type="text" name="descricao" class="form-control" value="<?php echo $student_standard_result_row["descricao"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-6">
                                <label><b>Empresa</b> <span class="text-danger">*</span></label><br>
                                <select name="idEmpresa" class="form-control" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>>
                                    <option value="">Selecione a Empresa</option>
                                    <?php echo Empresa_lista($connect); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label><b>Fornecedor</b> <span class="text-danger">*</span></label><br>
                                <select name="idFornecedor" class="form-control" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>>
                                    <option value="">Selecione o Fornecedor</option>
                                    <?php echo Fornecedor_lista($connect); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-4">
                                <label><b>Tipo de Contrato</b> <span class="text-danger">*</span></label><br>
                                <select name="idTipoContrato" class="form-control" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>>
                                    <option value="">Selecione o Tipo de Contrato</option>
                                    <?php echo TipoContrato_lista($connect); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label><b>Responsável Contrato</b> <span class="text-danger">*</span></label><br>
                                <select name="idResponsavel" class="form-control" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>>
                                    <option value="">Selecione o Responsável pelo Contrato</option>
                                    <?php echo Responsavel_lista($connect); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label><b>Setor Responsável</b> <span class="text-danger">*</span></label><br>
                                <select name="idSetorResponsavel" class="form-control" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>>
                                    <option value="">Selecione o Setor Responsável</option>
                                    <?php echo SetorResponsavel_lista($connect); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-3">
                                <label><b>Data Início Vigência</b></label><br>
                                <input type="date" name="dataInicioVigencia" class="form-control" value="<?php echo $student_standard_result_row["dataInicioVigencia"];?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-3">
                                <label><b>Data Término Vigência</b> </label><br>
                                <input type="date" name="dataTerminoVigencia" class="form-control" value="<?php echo $student_standard_result_row["dataTerminoVigencia"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-3">
                                <label><b>Data Início Execução</b> </label><br>
                                <input type="date" name="dataInicioExecucao" class="form-control" value="<?php echo $student_standard_result_row["dataInicioExecucao"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-3">
                                <label><b>Data Término Execução</b> </label><br>
                                <input type="date" name="dataTerminoExecucao" class="form-control" value="<?php echo $student_standard_result_row["dataTerminoExecucao"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-3">
                                <label><b>Data Assinatura</b></label><br>
                                <input type="date" name="dataAssinatura" class="form-control" value="<?php echo $student_standard_result_row["dataAssinatura"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-3">
                                <label><b>Valor Global</b> </label><br>
                                <input type="text" name="valorGlobal" class="form-control" value="<?php echo $student_standard_result_row["telefone"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-3">
                                <label><b>Quantidade de Parcelas</b> </label><br>
                                <input type="text" name="qtdParcelas" class="form-control" value="<?php echo $student_standard_result_row["qtdParcelas"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-3">
                                <label><b>Valor Parcela</b> </label><br>
                                <input type="text" name="valorParcela" class="form-control" value="<?php echo $student_standard_result_row["valorParcela"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-4">
                                <label><b>Telefone</b></label><br>
                                <input type="text" name="telefone" class="form-control" value="<?php echo $student_standard_result_row["telefone"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-4">
                                <label><b>Email</b></label><br>
                                <input type="text" name="email" class="form-control" value="<?php echo $student_standard_result_row["email"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                            <div class="col-md-4">
                                <label><b>Notificação Fim Vigência</b> </label><br>
                                <input type="text" name="notificacaoFimVigencia" class="form-control" value="<?php echo $student_standard_result_row["notificacaoFimVigencia"]; ?>" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>/>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-4">
                                <label><b>Status Contrato</b><span class="text-danger">*</span></label><br>
                                <select name="statusContrato" class="form-control">
                                    <option value="">Status do Contrato</option>
                                    <option value="Ativo">Ativo</option>
                                    <option value="Cancelado">Cancelado</option>
                                    <option value="Prazo Indeterminado">Prazo Indeterminado</option>
                                    <option value="Renovado">Renovado</option>
                                    <option value="Não Renovado">Não Renovado</option>
                                    <option value="Pendente">Pendente</option>
                                    <option value="Suspenso">Suspenso</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 25px;">
                            <div class="col-md-12">
                                <label><b>Objeto do Contrato (Max 5000 Caracteres)</b></label>
                                <textarea name="objetivoContrato" rows="5" class="form-control" <?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'disabled';} ?>><?php echo $student_standard_result_row["objetivoContrato"]; ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 mb-0">
                            <input type="hidden" name="idContrato" value="<?php echo $student_standard_result_row["idContrato"]; ?>" />
                            <input type="submit" name="<?php if($student_standard_result_row["statusContrato"] == 'Suspenso') { echo 'editar_contratoSuspendido'; } else { echo 'editar_contrato'; }?>" class="btn btn-success" value="Editar" />
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
            <li class="breadcrumb-item active">Gestão de Contratos</li>
        </ol>
        <?php

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Novo Contrato Adicionado Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET["msg"] == 'edit')
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Contrato Editado Com Sucesso<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            if($_GET["msg"] == 'delete' )
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Contrato Apagado Com Sucesso <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }

        ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Contratos
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="index.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="contrato_data" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Número Contrato</th>
                        <th>Empresa</th>
                        <th>Fornecedor</th>
                        <th>Tipo</th>
                        <th>Responsável</th>
                        <th>Setor</th>
                        <th>Qtd. Parcelas</th>
                        <th>Data Termino Vigencia</th>
                        <th>Status</th>
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

var datatable = $('#contrato_data').DataTable({

	"processing" : true,
	"serverSide" : true,
	"order" : [],
	"ajax" : {
		url : "action.php",
		type : "POST",
		data : {action : 'fetch_contrato'}
	},
    "language": {
    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
}

});

function delete_data(id)
{

	if(confirm("Quer mesmo eliminar este contrato?"))
	{
		window.location.href = 'index.php?action=delete&id='+id+'';
	}
}

</script>

