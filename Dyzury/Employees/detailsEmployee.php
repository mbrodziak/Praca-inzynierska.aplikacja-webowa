<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/../connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
		

	$employees = [];
	try
	{
		$connection = new mysqli($host, $db_user, $db_password, $db_name);
		$connection -> query ('SET NAMES utf8');
		$connection -> query ('SET CHARACTER_SET utf8_unicode_ci');

		if ($connection->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{
			parse_str($_SERVER['QUERY_STRING'], $qs);
			$id = mysqli_real_escape_string($connection, $qs['employee_id']);
			
			$query = "SELECT imie, nazwisko, data_urodzenia, numer_telefonu, login FROM pracownicy WHERE id_pracownika = " . $id . "";
			
			$result = $connection->query($query);
			if (!$result) throw new Exception($connection->error);
			
			$row = $result->fetch_assoc();
			
			$name = $row['imie'];						
			$surname = $row['nazwisko'];						
			$birthday = $row['data_urodzenia'];						
			$phone = $row['numer_telefonu'];						
			$login = $row['login'];						
			
		}
		$connection->close();
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
	
	
?>



<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" >
	<title>Zalogowany</title>
	
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	
	
</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="/">NA61 HW Shift</a>
	  
	  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu">
			<span class="navbar-toggler-icon"></span>
		</button>

	  <div class="collapse navbar-collapse" id="mainmenu">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/">Strona główna</a>
			</li>
			<li class="nav-item active">
				<a class="nav-link" href="/Shifts/shift.php">Zarządzaj dyżurami</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/Employees/cadre.php">Zarządzaj pracownikami</a>
			</li>
			
		</ul>
		<ul class="navbar-nav">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
					<?php echo $_SESSION['name']." ".$_SESSION['surname']; ?>
				</a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="/Employees/profil.php">Profil</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/logout.php">Wyloguj się</a>
				</div>
			</li>
		</ul>
	  </div>
	</nav>
	
	
	
	<div class="container">
		<div class="row">
			<div class="col">
				<h3 class="d-flex flex-row justify-content-between my-3">
					<div>Szczegóły pracownika - <?php echo $name . " " .$surname?></div>
				</h3>
				<?php
					echo "Data urodzenia: " . $birthday;
					echo "<br />";
					echo "Numer telefonu: " . $phone;					
					echo "<br />";
					echo "Login: " . $login;
				?>
				

			</div>
		</div>
	</div>
</body>
</html>