﻿<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
	
	$ready = true;
	
	if(isset($_POST['old_pass']))
	{	
		$old_pass = $_POST['old_pass'];
		$new_pass = $_POST['new_pass'];
		$repeat_pass = $_POST['repeat_pass'];
	
		if($new_pass == NULL)
		{
			$ready = false;
			$_SESSION['e_new_pass'] = "Podaj nowe hasło";
		}
		
		if (strlen($new_pass) < 8 || strlen($new_pass) > 20)
		{
			$ready = false;
			$_SESSION['e_new_pass'] = "Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if($new_pass != $repeat_pass)
		{
			$ready = false;
			$_SESSION['e_repeat_pass'] = "Podane hasła nie są identyczne!";
		}
			
		$hash_pass = password_hash($new_pass, PASSWORD_DEFAULT);		
	

		require_once __DIR__ . "/../../connect.php";
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
				$login = $_SESSION['login'];
				$result = $connection->query("SELECT * FROM pracownicy WHERE login = '$login'");
					
				if (!$result) throw new Exception($connection->error);
					
				$row = $result->fetch_assoc();
				
				if(!empty($old_pass))
				{
					if(!password_verify($old_pass, $row['haslo']))
					{
						$ready = false;
						$_SESSION['e_old_pass'] = "Podane hasło jest nieprawdłowe!";
					}
					
					if($old_pass == $new_pass)
					{
						$ready = false;
						$_SESSION['e_new_pass'] = "Hasło jest takie same jak poprzednie. Podaj inne hasło!";
					}							
				}
							
				if ($ready == true)
				{
					if(!empty($old_pass))
					{
						if ($connection->query("UPDATE pracownicy SET haslo = '$hash_pass' WHERE login = '$login'"))
						{
							$_SESSION['succesChanged'] = true;
							$_SESSION['passChanged'] = true;
							for($i = 0; $i < strlen($new_pass); $i++)
							{
								$new_pass[$i] = "*";
							}
							$_SESSION['pass'] = $new_pass;
							header('Location: /Employees/Edit/changedData.php');
						}
						else
						{
							throw new Exception($connection->error);
						}
					}
				}				
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera!</span>';
			echo '<br />Informacja developerska: '.$e;
		}				
	}
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" />
	<title>Zmień hasło</title>
	
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
			<li class="nav-item">
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
					<div>Zmień hasło</div>
				</h3>
			
				<form method="post">
				  <div class="form-group">
					<label>Stare hasło</label>
					<input type="password" class="form-control" name="old_pass" id="old_pass" placeholder="Stare hasło" required />	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_old_pass']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_old_pass'] . "</div>";
						unset ($_SESSION['e_old_pass']);
					}
					?>

				   <div class="form-group">
					<label>Nowe hasło</label>
					<input type="password" class="form-control" name="new_pass" id="new_pass" placeholder="Nowe hasło" required />	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_new_pass']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_new_pass'] . "</div>";
						unset ($_SESSION['e_new_pass']);
					}
					?>

				  <div class="form-group">
					<label>Powtórz hasło</label>
					<input type="password" class="form-control" name="repeat_pass" id="repeat_pass" placeholder="Powtórz hasło" required />	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_repeat_pass']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_repeat_pass'] . "</div>";
						unset ($_SESSION['e_repeat_pass']);
					}
					?> 

					<div>
						<button type="submit" class="btn btn-primary">ZMIEŃ HASŁO</button>
						<a href="/Employees/profil.php" class="btn btn-primary">ANULUJ</a>
					</div>	
					
				</form>
			</div>			
		</div>	
	</div>
</body>
</html>