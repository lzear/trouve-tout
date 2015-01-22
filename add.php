<?php

include 'connect.php';

$affFormP = false ;
$affFormQ = false ;
$addedP = false ;
$addedQ = false ;
$boloss = false ;
if (isset($_GET['produit']))
{
	$affFormP = true ;
}
elseif (isset($_GET['question']))
{
	$affFormQ = true ;
}
if (isset($_POST['p']))
{
	$produit = $_POST['p'] ;
	if (strlen($produit) > 1)
	{
		// INSERT PRODUIT
		try
		{
			$req = $bdd->prepare('INSERT INTO produits (nom) VALUES(?)');
			$req->execute(array($produit));
			$idP = $bdd->lastInsertId();
			$addedP = true ;
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		$req->closeCursor();
	}
	else
	{
		$boloss = true ;
	}
}
elseif (isset($_POST['q']))
{
	$question = $_POST['q'] ;
	if (substr($question, -1) == '?')
	{
		$question = substr($question, 0, -1);
	}
	if (substr($question, -1) == ' ')
	{
		$question = substr($question, 0, -1);
	}
	if (strlen($question))
	{
		
		// INSERT PRODUIT
		try
		{
			$req = $bdd->prepare('INSERT INTO questions (question) VALUES(?)');
			$req->execute(array($question));
			$idP = $bdd->lastInsertId();
			$addedQ = true ;
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		$req->closeCursor();
	}
	else
	{
		$boloss = true ;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trouve Tout | Ajout</title>
<link rel="stylesheet" href="css.css" media="all">
</head>

<body>
<a href="index.php">
<div class="banner">
	<h1 class="banner-head">
		Trouve Tout
    </h1>
</div>
</a>
<div class="content">
<?php
if ($affFormP)
{
?>


	<div class="propositions">
	
<form method="post" action="add.php" class="ingrid form">

	<div class="grid4-5">
	<div class="input-group">
		<input type="text" class="form-control" name="p" placeholder="Truc">
	</div>
	</div>
	
	<div class="grid1-5">
	<button type="submit" class="btn">Envoyer</button>	
	</div>
</form>
</div>
<?php
}
elseif ($affFormQ)
{
?>
	<div class="propositions">

<form method="post" action="add.php" class="ingrid form">

	<div class="grid4-5">
	<div class="input-group">
		<input type="text" class="form-control" name="q" placeholder="Question">
		<div class="input-group-addon">?</div>
	</div>
	</div>

	<div class="grid1-5">
	<button type="submit" class="btn">Envoyer</button>	
	</div>

</form>
</div>
<?php
}
elseif ($addedP)
{
?>
	<div class="propositions">
	<div class="grid1-2 info">
	<div class="ingrid">
	<div class="borded">
		 <div>
	<p>Le produit a été ajoutée :<br/><strong><?php echo htmlspecialchars($produit); ?></strong></p>
		</div>
	</div>
	</div>
	</div>
	</div>
<?php
}
elseif ($addedQ)
{
?>
	<div class="propositions">
	<div class="grid1-2 info">
	<div class="ingrid">
	<div class="borded">
		 <div>
	<p>La question a été ajoutée :<br/><strong><?php echo htmlspecialchars($question); ?> ?</strong></p>
		</div>
	</div>
	</div>
	</div>
	</div>
<?php
}
elseif ($boloss)
{
?>
	<div class="propositions">
	<div class="grid1-2 info">
	<div class="ingrid">
	<div class="borded">
		 <div>
	<p>Boloss</p>
		</div>
	</div>
	</div>
	</div>
	</div>
<?php
}
else
{
?>
	<div class="propositions">
	<div class="grid1-2">
	<div class="ingrid">
	<a href="add.php?question" class="borded">
	<div class="propos" style="">
		<div class="gianticbuttons"><strong>Nouvelle Question</strong></div>
	</div>
	</a>
	</div>
	</div>
	
	<div class="grid1-2">
	<div class="ingrid">
	<a href="add.php?produit" class="borded">
	<div class="propos" style="">
		<div class="gianticbuttons"><strong>Nouveau Produit</strong></div>
	</div>
	</a>
	</div>
	</div>
	</div>
<?php
}
?>
	<div class="propositions">

	<div class="grid1-2 retour">
	<div class="ingrid">
	<a href="index.php" class="borded">
		 <div class="gianticbuttons addproduit">
	<p>Retour à l'accueil</p>
		</div>
	</a>
	</div>
	</div>

</div>
<?php
include 'pub.php' ;
?>
</div>
</body>
</html>