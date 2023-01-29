<?php


include('database_connection.php');

if(isset($_POST['action']))
{
    if($_POST['action'] == 'fetch_fornecedor')
    {
        $query = "
		SELECT * FROM fornecedor 
		";

        if(isset($_POST["search"]["value"]))
        {
            $query .= 'WHERE nomeFornecedor LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR email LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR telefone LIKE "%'.$_POST["search"]["value"].'%" ';
        }

        if(isset($_POST["order"]))
        {
            $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
            $query .= 'ORDER BY idFornecedor DESC ';
        }

        $query1 = '';

        if($_POST["length"] != -1)
        {
            $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $filtered_rows = $statement->rowCount();

        $result = $connect->query($query . $query1);

        //echo $query . $query1;

        $data = array();

        foreach($result as $row)
        {
            $sub_array = array();

            $status = '';

            $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["idFornecedor"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Apagar</button>';



            $sub_array[] = $row['nomeFornecedor'];

            $sub_array[] = $row['email'];

            $sub_array[] = $row['telefone'];

            $sub_array[] = '<a href="fornecedor.php?action=edit&id='.$row["idFornecedor"].'" class="btn btn-sm btn-primary">Editar</a>&nbsp;' . $delete_button;

            $data[] = $sub_array;
        }

        $output = array(
            'draw'		=>	intval($_POST['draw']),
            'recordsTotal'	=>	get_total_fornecedores($connect),
            'recordsFiltered'	=>	$filtered_rows,
            'data'			=>	$data
        );

        echo json_encode($output);

    }



    if($_POST['action'] == 'fetch_empresa')
    {
        $query = "
		SELECT * FROM empresa 
		";

        if(isset($_POST["search"]["value"]))
        {
            $query .= 'WHERE nomeEmpresa LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR email LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR telefone LIKE "%'.$_POST["search"]["value"].'%" ';
        }

        if(isset($_POST["order"]))
        {
            $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
            $query .= 'ORDER BY idEmpresa DESC ';
        }

        $query1 = '';

        if($_POST["length"] != -1)
        {
            $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $filtered_rows = $statement->rowCount();

        $result = $connect->query($query . $query1);

        //echo $query . $query1;

        $data = array();

        foreach($result as $row)
        {
            $sub_array = array();

            $status = '';

            $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["idEmpresa"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Apagar</button>';



            $sub_array[] = $row['nomeEmpresa'];

            $sub_array[] = $row['email'];

            $sub_array[] = $row['telefone'];

            $sub_array[] = '<a href="empresa.php?action=edit&id='.$row["idEmpresa"].'" class="btn btn-sm btn-primary">Editar</a>&nbsp;' . $delete_button;

            $data[] = $sub_array;
        }

        $output = array(
            'draw'		=>	intval($_POST['draw']),
            'recordsTotal'	=>	get_total_empresas($connect),
            'recordsFiltered'	=>	$filtered_rows,
            'data'			=>	$data
        );

        echo json_encode($output);

    }

    if($_POST['action'] == 'fetch_tipoContrato')
    {
        $query = "
		SELECT * FROM tipoContrato 
		";

        if(isset($_POST["search"]["value"]))
        {
            $query .= 'WHERE tipoContrato LIKE "%'.$_POST["search"]["value"].'%" ';
        }

        if(isset($_POST["order"]))
        {
            $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
            $query .= 'ORDER BY idTipoContrato DESC ';
        }

        $query1 = '';

        if($_POST["length"] != -1)
        {
            $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $filtered_rows = $statement->rowCount();

        $result = $connect->query($query . $query1);

        //echo $query . $query1;

        $data = array();

        foreach($result as $row)
        {
            $sub_array = array();

            $status = '';

            $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["idTipoContrato"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Apagar</button>';



            $sub_array[] = $row['tipoContrato'];


            $sub_array[] = '<a href="tipoContrato.php?action=edit&id='.$row["idTipoContrato"].'" class="btn btn-sm btn-primary">Editar</a>&nbsp;' . $delete_button;

            $data[] = $sub_array;
        }

        $output = array(
            'draw'		=>	intval($_POST['draw']),
            'recordsTotal'	=>	get_total_tiposContrato($connect),
            'recordsFiltered'	=>	$filtered_rows,
            'data'			=>	$data
        );

        echo json_encode($output);

    }


    if($_POST['action'] == 'fetch_responsavel')
    {
        $query = "
		SELECT * FROM responsavel 
		";

        if(isset($_POST["search"]["value"]))
        {
            $query .= 'WHERE nomeResponsavel LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR email LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR telefone LIKE "%'.$_POST["search"]["value"].'%" ';
        }

        if(isset($_POST["order"]))
        {
            $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
            $query .= 'ORDER BY idResponsavel DESC ';
        }

        $query1 = '';

        if($_POST["length"] != -1)
        {
            $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $filtered_rows = $statement->rowCount();

        $result = $connect->query($query . $query1);

        //echo $query . $query1;

        $data = array();

        foreach($result as $row)
        {
            $sub_array = array();

            $status = '';

            $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["idResponsavel"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Apagar</button>';

            $sub_array[] = $row['nomeResponsavel'];

            $sub_array[] = $row['email'];

            $sub_array[] = $row['telefone'];

            $sub_array[] = '<a href="responsavel.php?action=edit&id='.$row["idResponsavel"].'" class="btn btn-sm btn-primary">Editar</a>&nbsp;' . $delete_button;

            $data[] = $sub_array;
        }

        $output = array(
            'draw'		=>	intval($_POST['draw']),
            'recordsTotal'	=>	get_total_responsaveis($connect),
            'recordsFiltered'	=>	$filtered_rows,
            'data'			=>	$data
        );

        echo json_encode($output);

    }

    if($_POST['action'] == 'fetch_setorResponsavel')
    {
        $query = "
		SELECT * FROM setorResponsavel 
		";

        if(isset($_POST["search"]["value"]))
        {
            $query .= 'WHERE setorResponsavel LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR email LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR telefone LIKE "%'.$_POST["search"]["value"].'%" ';
        }

        if(isset($_POST["order"]))
        {
            $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
            $query .= 'ORDER BY idSetorResponsavel DESC ';
        }

        $query1 = '';

        if($_POST["length"] != -1)
        {
            $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $filtered_rows = $statement->rowCount();

        $result = $connect->query($query . $query1);

        //echo $query . $query1;

        $data = array();

        foreach($result as $row)
        {
            $sub_array = array();

            $status = '';

            $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["idSetorResponsavel"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Apagar</button>';

            $sub_array[] = $row['setorResponsavel'];

            $sub_array[] = $row['email'];

            $sub_array[] = $row['telefone'];

            $sub_array[] = '<a href="setorResponsavel.php?action=edit&id='.$row["idSetorResponsavel"].'" class="btn btn-sm btn-primary">Editar</a>&nbsp;' . $delete_button;

            $data[] = $sub_array;
        }

        $output = array(
            'draw'		=>	intval($_POST['draw']),
            'recordsTotal'	=>	get_total_setores($connect),
            'recordsFiltered'	=>	$filtered_rows,
            'data'			=>	$data
        );

        echo json_encode($output);

    }

    if($_POST['action'] == 'fetch_contrato')
    {
        $query = "
		SELECT * FROM contrato 
		INNER JOIN empresa ON empresa.idEmpresa = contrato.idEmpresa 
		INNER JOIN fornecedor ON fornecedor.idFornecedor = contrato.idFornecedor
		INNER JOIN tipoContrato ON tipoContrato.idTipoContrato = contrato.idTipoContrato
		INNER JOIN responsavel ON responsavel.idResponsavel = contrato.idResponsavel
		INNER JOIN setorResponsavel ON setorResponsavel.idSetorResponsavel = contrato.idSetorResponsavel 
		";

        if(isset($_POST["search"]["value"]))
        {
            $query .= 'WHERE (contrato.numeroContrato LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR empresa.nomeEmpresa LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR fornecedor.nomeFornecedor LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR tipoContrato.tipoContrato LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR responsavel.nomeResponsavel LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR setorResponsavel.setorResponsavel LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR contrato.valorGlobal LIKE "%'.$_POST["search"]["value"].'%" ';
            $query .= 'OR contrato.qtdParcelas LIKE "%'.$_POST["search"]["value"].'%") ';
        }

        if(isset($_POST["order"]))
        {
            $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
            $query .= 'ORDER BY contrato.idContrato DESC ';
        }

        $query1 = '';

        if($_POST['length'] != -1)
        {
            $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $filtered_rows = $statement->rowCount();

        $result = $connect->query($query . $query1);

        $data = array();

        foreach($result as $row)
        {
            $sub_array = array();

            $status = '';

            $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["idContrato"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Apagar</button>';

           /* if($row['statusContrato'] == 'Enable')
            {
                $status = '<div class="badge bg-success">Enable</div>';

                $delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$row["student_standard_id"].'`, `'.$row["student_standard_status"].'`)"><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
            }*/

            $sub_array[] = $row['numeroContrato'];

            $sub_array[] = $row['nomeEmpresa'];

            $sub_array[] = $row['nomeFornecedor'];

            $sub_array[] = $row['tipoContrato'];

            $sub_array[] = $row['nomeResponsavel'];

            $sub_array[] = $row['setorResponsavel'];


            $sub_array[] = $row['qtdParcelas'];
            $sub_array[] = $row['dataTerminoVigencia'];

            if($row['statusContrato'] == 'Ativo')
            {
                $status = '<div class="badge bg-success">Ativo</div>';

                $sub_array[] = $status;
            }

            if($row['statusContrato'] == 'Cancelado')
            {
                $status = '<div class="badge bg-secondary">Cancelado</div>';

                $sub_array[] = $status;

            }

            if($row['statusContrato'] == 'Prazo Indeterminado')
            {
                $status = '<div class="badge bg-light">Prazo Indeterminado</div>';

                $sub_array[] = $status;

            }

            if($row['statusContrato'] == 'Renovado')
            {
                $status = '<div class="badge bg-success">Renovado</div>';

                $sub_array[] = $status;
            }

            if($row['statusContrato'] == 'Não Renovado')
            {
                $status = '<div class="badge bg-warning">Não Renovado</div>';

                $sub_array[] = $status;
            }

            if($row['statusContrato'] == 'Pendente')
            {
                $status = '<div class="badge bg-warning">Pendente</div>';

                $sub_array[] = $status;

            }

            if($row['statusContrato'] == 'Suspenso')
            {
                $status = '<div class="badge bg-danger">Suspenso</div>';

                $sub_array[] = $status;

            }


            $sub_array[] = '<a href="index.php?action=edit&id='.$row["idContrato"].'" class="btn btn-sm btn-primary">Editar</a>&nbsp;' . $delete_button;

            $data[] = $sub_array;
        }

        $output = array(
            "draw" 		=>	intval($_POST['draw']),
            "recordsTotal"	=>	get_total_contratos($connect),
            "recordsFiltered"	=>	$filtered_rows,
            "data"	=>	$data
        );

        echo json_encode($output);
    }

}

?>
