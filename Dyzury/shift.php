<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
?>



<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zalogowany</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
</head>

<body>
	
	<div class="header">
		Dyżury
	</div>
	
	<div class="container">
	
		<div class="list"> 
			<div class="fulfillment"></div>
			
			<a href="signed.php" class="choose_option">
				<div class="option">
					Strona główna
				</div>
			</a>
			
			<a href="profil.php" class="choose_option">
				<div class="option">
					Profil
				</div>
			</a>
			
			<a href="shift.php" class="choose_option">
				<div class="option">
					Dyżury
				</div>
			</a>

			<?php
				if($_SESSION['admin'] == 1)
				{
					echo '<a href="newShift.php" class="choose_option">
							<div class="option">
								Dodaj dyżur
							</div>
						</a>
						
						<a href="newEmployee.php" class="choose_option">
							<div class="option">
								Dodaj pracownika
							</div class="option">
						</a>
						
						<a href="noAdmin.php" class="choose_option">
							<div class="option">
								Nadaj uprawnienia
							</div class="option">
						</a>
						
						<a href="Admin.php" class="choose_option">
							<div class="option">
								Odbierz uprawnienia
							</div class="option">
						</a>';	
				}			
			?>
						
			<a href="cadre.php" class="choose_option">
				<div class="option">
					Kadra
				</div>
			</a>
			
			<a href="logout.php" class="logout">
				<div class="logOut">
					Wyloguj się 
				</div>
			</a>
			
		</div>
	
		<div class="no_name_yet">
			Tu będzie kalendarz z dyżurami
			
			<div id="editShift"><a href="editShift.php"><input type="submit" id="editShift" value="EDYTUJ DYŻUR" /></a></div>
			
		</div>	
	
		<div style="clear:both"></div>
		
	</div>
</body>


</html>