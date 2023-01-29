<?php

//database_connection.php

//$connect = new PDO("mysql:host=db,dbname=norte_tech", "norte_tech", "password");

$servername = "db";
$username = "norte_tech";
$password = "password";
 
try {
      $connect = new PDO(
        "mysql:host=$servername;dbname=norte_tech",
        $username, $password);
   
      // Set the PDO error mode
      // to exception
      $connect->setAttribute(PDO::ATTR_ERRMODE,
                  PDO::ERRMODE_EXCEPTION);
   
} catch(PDOException $e) {
}
 

//session_start();


function Empresa_lista($connect)
{
	$query = "
	SELECT idEmpresa, nomeEmpresa 
	FROM empresa 
	ORDER BY idEmpresa
	";

	$result = $connect->query($query, PDO::FETCH_ASSOC);

	$output = '';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["idEmpresa"].'">'.$row["nomeEmpresa"].'</option>';
	}

	return $output;
}

function Fornecedor_lista($connect)
{
    $query = "
	SELECT idFornecedor, nomeFornecedor 
	FROM fornecedor 
	ORDER BY idFornecedor
	";

    $result = $connect->query($query, PDO::FETCH_ASSOC);

    $output = '';

    foreach($result as $row)
    {
        $output .= '<option value="'.$row["idFornecedor"].'">'.$row["nomeFornecedor"].'</option>';
    }

    return $output;
}

function TipoContrato_lista($connect)
{
    $query = "
	SELECT idTipoContrato, tipoContrato 
	FROM tipoContrato 
	ORDER BY idTipoContrato
	";

    $result = $connect->query($query, PDO::FETCH_ASSOC);

    $output = '';

    foreach($result as $row)
    {
        $output .= '<option value="'.$row["idTipoContrato"].'">'.$row["tipoContrato"].'</option>';
    }

    return $output;
}

function Responsavel_lista($connect)
{
    $query = "
	SELECT idResponsavel, nomeResponsavel 
	FROM responsavel 
	ORDER BY idResponsavel
	";

    $result = $connect->query($query, PDO::FETCH_ASSOC);

    $output = '';

    foreach($result as $row)
    {
        $output .= '<option value="'.$row["idResponsavel"].'">'.$row["nomeResponsavel"].'</option>';
    }

    return $output;
}

function SetorResponsavel_lista($connect)
{
    $query = "
	SELECT idSetorResponsavel, setorResponsavel 
	FROM setorResponsavel 
	ORDER BY idSetorResponsavel
	";

    $result = $connect->query($query, PDO::FETCH_ASSOC);

    $output = '';

    foreach($result as $row)
    {
        $output .= '<option value="'.$row["idSetorResponsavel"].'">'.$row["setorResponsavel"].'</option>';
    }

    return $output;
}

function Academic_standard_list_data($connect)
{
	$query = "
	SELECT idSetorResponsavel, setorResponsavel, telefone 
	FROM setorResponsavel
	WHERE 1 
	ORDER BY idSetorResponsavel DESC
	";

	$result = $connect->query($query, PDO::FETCH_ASSOC);

	$output = '';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["idSetorResponsavel"].'">'.$row['setorResponsavel'].' - '.$row["telefone"].'</option>';
	}
	return $output;
}


function get_total_contratos($connect)
{
    $query = "SELECT * FROM contrato";

    $statement = $connect->prepare($query);

    $statement->execute();

    return $statement->rowCount();
}

function get_total_fornecedores($connect)
{
    $query = "SELECT * FROM fornecedor";

    $statement = $connect->prepare($query);

    $statement->execute();

    return $statement->rowCount();
}

function get_total_empresas($connect)
{
    $query = "SELECT * FROM empresa";

    $statement = $connect->prepare($query);

    $statement->execute();

    return $statement->rowCount();
}

function get_total_setores($connect)
{
    $query = "SELECT * FROM setorResponsavel";

    $statement = $connect->prepare($query);

    $statement->execute();

    return $statement->rowCount();
}

function get_total_responsaveis($connect)
{
    $query = "SELECT * FROM responsavel";

    $statement = $connect->prepare($query);

    $statement->execute();

    return $statement->rowCount();
}

function get_total_tiposContrato($connect)
{
    $query = "SELECT * FROM tipoContrato";

    $statement = $connect->prepare($query);

    $statement->execute();

    return $statement->rowCount();
}

?>
