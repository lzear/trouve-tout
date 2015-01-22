<?php
$isResults = false ;
$bestScoreP = -1 ;
$scoreV = -100 ;
include 'connect.php';

if (isset($_GET['r']) && isset($_GET['p']))
{
	$idR = $_GET['r'] ;
	$idP = $_GET['p'] ;
	
	// TROUVER LE PRODUIT
	try 
	{
		$req = $bdd->prepare('SELECT
				nom
			FROM produits
			WHERE idP = ?');
		$req->execute(array($idP));
	
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	
	while ($donnees = $req->fetch())
	{
		$nomP = $donnees['nom'] ;
	}
	
	// TROUVER LES REPONSES / LES COUPS
	try 
	{
		$req = $bdd->prepare('SELECT
				c.idQ,
				q.question,
				c.reponse AS cRep,
				r.reponse AS rRep
			FROM coups c
			INNER JOIN reponses r
				ON  r.idP = ?
				AND r.idQ = c.idQ
			INNER JOIN questions q
				ON  q.idq = c.idQ
			WHERE c.idR = ?');
		$req->execute(array($idP,$idR));
	
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	
	while ($donnees = $req->fetch())
	{
		$arreponses[] = array(	$donnees['idQ'],
								$donnees['question'],
								$donnees['rRep'],
								$donnees['cRep']) ;
		$isResults = true ;
	}
	
	//TROUVER COUSINS
	try 
	{
		$req = $bdd->prepare('SELECT
				r2.idP AS idP2,
				p.nom,
				COUNT(*) AS N
			FROM reponses r
			INNER JOIN reponses r2
				ON  r2.idQ = r.idQ
				AND r2.reponse = r.reponse
			INNER JOIN produits p
				ON p.idP = r2.idP
			WHERE r.idP = ?
				AND r.reponse <> 0
			GROUP BY r2.idP
			ORDER BY N DESC');
		$req->execute(array($idP));
	
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$v = false ;
	$isSP = false ;
	while ($donnees = $req->fetch())
	{
		if ($donnees['idP2'] == $idP)
		{
			$scoreP = $donnees['N'] ;
			$isSP = true;
		}
		elseif ($donnees['N'] > $scoreV)
		{
			$scoreV = $donnees['N'] ;
			$nomV = $donnees['nom'] ;
			$idV = $donnees['idP2'] ;
			$v = true ;
		}
	}
	

	$req->closeCursor();
	
	$w = false ;
	if ($v && $isSP)
	{if ($scoreV >= $scoreP)
	{
		$w = true ;
	}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trouve Tout | Recherche terminée</title>
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
<div class="question">
<div class="grid1">
<div class="ingrid">
<div class="borded">
	<h2><?php echo htmlspecialchars($nomP); ?></h2>
</div>
</div>
</div>
</div>

<div class="propositions">


	<div class="grid1">
	<div class="ingrid">

	<table >
    <thead>
        <tr>
            <th>Question</th>
            <th>Ta réponse</th>
            <th>Ma réponse</th>
        </tr>
    </thead>
    <tbody>
<?php
	foreach ($arreponses as $key => $value)
	{
		$rRep = ($value[2] == 1) ? 'Oui' : (($value[2] == -1) ? 'Non' : 'Bof') ;
		$cRep = ($value[3] == 1) ? 'Oui' : (($value[3] == -1) ? 'Non' : 'Bof') ;
		if ($rRep != $cRep) {
			$rRep = '<strong style="color:#b66">' . $rRep . '</strong>' ;
			$cRep = '<strong style="color:#b66">' . $cRep . '</strong>' ;
		}
		else {
			$rRep = '<strong style="color:#6b6">' . $rRep . '</strong>' ;
			$cRep = '<strong style="color:#6b6">' . $cRep . '</strong>' ;
		}
		
?>
        <tr>
            <td><?php echo htmlspecialchars($value[1]) ; ?> ?</td>
			<td><?php echo $cRep ; ?></td>
			<td><?php echo $rRep ; ?></td>
        </tr>
<?php
	}
?>

    </tbody>
</table>

	</div>
	</div></div>

	
<?php
	if ($w)
	{
?>

	<div class="propositions">

	<div class="grid1 retour">
	<div class="ingrid">
	<a href="add.php?question" class="borded">
		 <div class="gianticbuttons addproduit">
		<p>Aïe !<br/>Je ne sais pas su différencier <strong><?php echo htmlspecialchars($nomV); ?></strong> et <strong><?php echo htmlspecialchars($nomP); ?></strong></p><p>Ajoute une question qui peut les distinguer.</p>
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

</div>

<?php
include 'pub.php' ;
?>

</div>

</body>
</html>