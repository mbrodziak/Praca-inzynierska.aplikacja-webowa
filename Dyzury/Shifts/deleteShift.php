<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	$next_week_date = date("Y-m-d", strtotime("+1 week"));
	require_once __DIR__ . "/../connect.php";	
	mysqli_report(MYSQLI_REPORT_STRICT);
			
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
			$id = mysqli_real_escape_string($connection, $qs['shift_id']);
			
			$result = $connection->query("select data_dyzuru from dyzury where id_dyzuru = '$id' limit 1");
			if (!$result) throw new Exception($connection->error);
			$row = $result->fetch_assoc();
			$shift_date = $row['data_dyzuru'];
			
			if($shift_date >= $next_week_date)
			{
				$login = $_SESSION['login'];
				$result = $connection->query("SELECT haslo FROM pracownicy where login = '$login'");
				
				if (!$result) throw new Exception($connection->error);
					
				$row = $result->fetch_assoc();
				$can_edit = true;
				
				if(isset($_POST['confirm_pass']))
				{
					$password = $_POST['confirm_pass'];
					if(!empty($password))
					{
						if(!password_verify($password, $row['haslo'])) $_SESSION['e_password'] = "Błędne hasło!";
						
						else
						{
							$result = $connection->query("delete from dyzury where id_dyzuru = '$id' limit 1");
						
							if (!$result) throw new Exception($connection->error);
						
							$result = $connection->query("delete from dyzury_pracownikow where id_dyzuru = '$id' limit 1");
						
							if (!$result) throw new Exception($connection->error);
							
							header('Location: /Shifts/shift.php');
						}
					}
					else $_SESSION['e_password'] = "Proszę potwierdzić hasłem!";
				}			
			}
			else
			{
				//$_SESSION['errorr'] = "<div class='alert alert-danger' role='alert'>Nie można usunąć dyżuru!</div>";
				//header('Location: shift.php');
				$can_edit = false;
			}								
		}
		$connection->close();
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie praocownika w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
?>

<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" >
	<title>Potwierdzanie usunięcia dyżuru</title>

	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	
</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="/">Nazwa aplikacji</a>
	  
	  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Przełącznik nawigacji">
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
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php echo $_SESSION['name']." ".$_SESSION['surname']; ?>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
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
					<div>Potwierdzanie usunięcia dyżuru</div>
				</h3>
				
				<form method="post">
					<div class="form-group">
						<label>Potwierdź usunięcie dyżuru</label>
						<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło" 					
					<?php 
						echo !$can_edit ? "disabled" : "";
					?> />	
					</div>
					  
					<?php
					if (isset($_SESSION['e_password']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_password'] . "</div>";
						unset ($_SESSION['e_password']);
					}
					?> 
					
					<button type="submit" class="btn btn-primary"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>>ZATWIERDŹ</button>
				</form>	
				
				<a href="/Shifts/shift.php" class="btn btn-primary" role="button" id="cancelDelete">ANULUJ</a>
				
				<?php 
					echo $can_edit ? "" : "<div class='alert alert-danger' role='alert'>
						Nie można usunąć dyżuru!
					</div>";			
				?>
				
			</div>
		</div>
	</div>
</body>
</html>


