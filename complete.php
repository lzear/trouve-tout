<?php
include 'connect.php';

$manqueQuestions = false ;
$added = false ;
if (isset($_POST['q']) && isset($_POST['p']) && isset($_POST['r']))
{
	$idQ = $_POST['q'] ;
	$idP = $_POST['p'] ;
	$rep = $_POST['r'] ;
	$req = $bdd->prepare('SELECT
							idQ
						FROM reponses
						WHERE idP = ? 
							AND idQ = ?
						LIMIT 1');
		$req->execute(array($idP,$idQ));

	$resultats = $req->fetch();

	if(!$resultats)
	{
		$req->closeCursor();

		try
		{
			$req = $bdd->prepare('INSERT INTO reponses(idP,idQ,reponse) VALUES(?,?,?)');
			$req->execute(array($idP,$idQ,$rep));
			$added = true ;
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		$req->closeCursor();
	}
	else
	{
		$req->closeCursor();
	}
}

	$req = $bdd->query('SELECT
							q.idQ,
							q.question,
							p.idP,
							p.nom
						FROM questions q
						INNER JOIN produits p
						WHERE NOT EXISTS 
							(
							SELECT *
							FROM reponses r
							WHERE
							r.idP=p.idP AND 
							r.idQ=q.idQ
							)
						order by rand()
						LIMIT 1');
						

		$manqueQuestions = false ;
		while ($donnees = $req->fetch())
		{
			$idQ = $donnees['idQ'] ;
			$idP = $donnees['idP'] ;
			$question = $donnees['question'] ;
			$produit = $donnees['nom'] ;
			$manqueQuestions = true ;
			
		}
		

	
	$req->closeCursor();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trouve Tout | Ajout de réponses</title>
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
if($added == true)
{
?>
	<div class="propositions">

	<div class="grid1-2 info">
	<div class="ingrid">
	<div class="borded">
		 <div>
	<p>La réponse a été ajoutée</p>
		</div>
	</div>
	</div>
	</div>

	</div>
<?php
}
if( $manqueQuestions)
{
?>
<div class="propositions">
<div class="grid1-2">
<div class="ingrid">
<div class="borded">
	<h1><?php echo htmlspecialchars($produit); ?></h1>
</div>
</div>
</div>
<div class="grid1-2">
<div class="ingrid">
<div class="borded">
	<h1><?php echo htmlspecialchars($question); ?> ?</h1>
</div>
</div>
</div>
</div>
<div class="reponse">

	<div class="grid1-3">
	<form method="post" action="complete.php" class="ingrid">
	<input type="hidden" name="p" value="<?php echo $idP ; ?>">
	<input type="hidden" name="q" value="<?php echo $idQ ; ?>">
	<input type="hidden" name="r" value="1">
	
	<button type="submit" class="borded gianticbuttons oui">
			<h2>Oui</h2>
	</button>

	</form>
	</div>
	
	<div class="grid1-3">
	<form method="post" action="complete.php" class="ingrid">
	<input type="hidden" name="p" value="<?php echo $idP ; ?>">
	<input type="hidden" name="q" value="<?php echo $idQ ; ?>">
	<input type="hidden" name="r" value="0">

	<button type="submit" class="borded gianticbuttons bof">
			<h2>Bof</h2>
	</button>

	</form>
	</div>
	
	<div class="grid1-3">
	<form method="post" action="complete.php" class="ingrid">
	<input type="hidden" name="p" value="<?php echo $idP ; ?>">
	<input type="hidden" name="q" value="<?php echo $idQ ; ?>">
	<input type="hidden" name="r" value="-1">

	<button type="submit" class="borded gianticbuttons non">
			<h2>Non</h2>
	</button>
	</form>
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
</div>
<?php
include 'pub.php' ;
?>
</div>
</body>
</html>
