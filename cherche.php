<?php
header('Content-type: text/html; charset=UTF-8');
$bestScoreP = -1 ;
$noQ = true ;
$nbP = 0;
$mQ = false ;
include 'connect.php';
$debug = false ;
$coup = 0 ;

if (isset($_POST['question']) && isset($_POST['reponse']) && isset($_POST['recherche']))
{
	if (isset($_POST['recherche']))
		$idR = $_POST['recherche'] ;
}

elseif (isset($_POST['question']) && isset($_POST['reponse']))
{
	try
	{
		$req = $bdd->prepare('INSERT INTO recherches() VALUES()');
		$req->execute(array());
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	
	$idR = $bdd->lastInsertId();
	
	$req->closeCursor();
}



if( isset($idR))
{
	if (isset($_GET['c']))
	{
		$coup = $_GET['c']+1 ;
	}
	else 
	{
		$coup = 1 ;
	}
	try
	{
		$req = $bdd->prepare('INSERT INTO coups(idR,idQ,reponse) VALUES(?,?,?)');
		$req->execute(array($idR, $_POST['question'], $_POST['reponse']));
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$req->closeCursor();
	
	//echo 'r:' . $idR . ' ; q:' . $_POST['question'] . ' ; r:' . $_POST['reponse'] ;

	//
	//	SCORES PRODUITS
	//
	try
	{
		$req = $bdd->prepare('SELECT
				p.idP,
				p.nom,
				(2 + SUM(0.2-ABS(r.reponse - c.reponse))) AS scoreP,
				COUNT(*)
			FROM reponses r
			INNER JOIN produits p
				ON r.idP = p.idP
			INNER JOIN coups c
				ON r.idQ = c.idQ
			WHERE c.idR = ?
			GROUP BY p.idP
			HAVING scoreP > 0
			ORDER BY scoreP DESC');
		$req->execute(array($idR));

	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$pArray = array() ;
	
	while ($donnees = $req->fetch())
	{
	if ($debug)
	{
	echo 'b<pre>' ;
	print_r($donnees) ;
	echo '</pre>' ;
	}
		$idPs[] = $donnees['idP'] ;
		$scoreP[$donnees['idP']] = $donnees['scoreP'] ;
		$pNoms[$donnees['idP']] = $donnees['nom'] ;
		if($bestScoreP < 0)
		{
			$bestScoreP = $donnees['scoreP'] ;
		}
		$nbP++ ;
	}

	$req->closeCursor();
	

	if ($nbP > 0)
	{
	//
	//	TROUVE QUESTIONS
	//	scoreQ SUM(ABS(r.reponse))-(ABS(SUM(r.reponse)))
	//	scoreP SUM(r.reponse * c.reponse)
	$req_str = 'SELECT
				r.idP,
				r.idQ,
				r.reponse
			FROM reponses r
			WHERE r.idP IN (' . implode(',', $idPs) . ')' ;
	try
	{
		$req = $bdd->prepare($req_str);
		$req->execute();
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$reps = array() ;
	$idQs = array();
	while ($donnees = $req->fetch())
	{
	
		$reps[$donnees['idP']][$donnees['idQ']] = $donnees['reponse'] ;
		if (!in_array($donnees['idQ'], $idQs))
		{
			$idQs[] = $donnees['idQ'] ;
		}
	}
	$req->closeCursor();
	
	
	$scoreQ = array() ;
	foreach($idQs as $idQ)
	{
		$a = 0 ;
		$b = 0 ;
		$nOui = 0 ;
		$nBof = 0 ;
		$nNon = 0 ;
		$nOui2 = 0 ;
		$nBof2 = 0 ;
		$nNon2 = 0 ;
		$sScoreP = 0 ;
		foreach($idPs as $idP)
		{
			if (isset($reps[$idP][$idQ]))
			{
			//$a += abs(	$reps[$idP][$idQ]) * $scoreP[$idP] ;
			//$b += 		$reps[$idP][$idQ]  * $scoreP[$idP] ;
				if ($reps[$idP][$idQ] == -1) {
					$nNon+= $scoreP[$idP] ;
					$nNon2+= pow($scoreP[$idP],2) ;
				}
				elseif ($reps[$idP][$idQ] == 0) {
					$nBof+= $scoreP[$idP] ;
					$nBof2+= pow($scoreP[$idP],2) ;
				}
				elseif ($reps[$idP][$idQ] == 1) {
					$nOui+= $scoreP[$idP] ;
					$nOui2+= pow($scoreP[$idP],2) ;
				}
				$sScoreP += $scoreP[$idP] ;
			}
			
		}
		$scoreQ[$idQ] = -max($nOui/$sScoreP,$nNon/$sScoreP,$nBof/$sScoreP);
		//$scoreQ[$idQ] = -abs($nNon-$nOui)-$nBof;
		//$scoreQ[$idQ] = min($nOui, $nNon) ;
		//$scoreQ[$idQ] = ($nOui+1) * ($nBof+1) * ($nNon+1) ;
		//echo $idQ . '-' . $scoreQ[$idQ] . ' : ' . $nOui . ',' . $nBof . ',' . $nNon . '<br/>' ;
	}
	if ($debug)
	{
	echo 'b<pre>' ;
	print_r($scoreQ) ;
	echo '</pre>' ;
		
	echo 'c<pre>' ;
	print_r($reps) ;
	echo '</pre>' ;
	}
	$bestIdQ = array_keys($scoreQ, max($scoreQ)) ;

	$idQ = $bestIdQ[array_rand($bestIdQ)] ;
	//if ($idQ == $_POST['question'])
	if (false)
	{
		$mQ = true ;
	}
	else
	{
	try
	{
		$req = $bdd->prepare('SELECT question	FROM questions WHERE idQ = ?');
		$req->execute(array($idQ));
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	
	$best = 0 ;
	while ($donnees = $req->fetch())
	{
		$noQ = false ;
		$question = $donnees['question'] ;
	}
	$req->closeCursor();
	}
	}
}

else
{
	try
	{
		$req = $bdd->query('SELECT
				r.idQ,
				COUNT(idP) AS nb,
				q.question AS question,
				SUM(r.reponse) AS somme_rep,
				SUM(ABS(r.reponse))-(ABS(SUM(r.reponse))) AS score
			FROM reponses r
			INNER JOIN questions q
				ON r.idQ = q.idQ
			GROUP BY r.idQ
			ORDER BY score DESC
			LIMIT 1');
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	
	$best = 0 ;

	while ($donnees = $req->fetch())
	{
		$noQ = false ;
		$question = $donnees['question'] ;
		$idQ = $donnees['idQ'] ;

	}
	
	$req->closeCursor();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trouve Tout | Recherche</title>
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
//if ((isset($idR) && $nbP < 1) || $mQ)
if (false)
{
?>
	<div class="propositions">

	<div class="grid1-2 info">
	<div class="ingrid">
	<div class="borded">
		 <div>
	<p>Je coince !</p>
		</div>
	</div>
	</div>
	</div>

	</div>
<?php
}
elseif ($noQ)
{
?>
	<div class="propositions">

	<div class="grid1-2 info">
	<div class="ingrid">
	<div class="borded">
		 <div>
	<p>Erreur</p>
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

<div class="question">
<div class="grid1">
<div class="ingrid">
<div class="borded">
	<h1><?php echo htmlspecialchars($question); ?> ?</h1>
</div>
</div>
</div>
</div>
<div class="reponse">

	<div class="grid1-3">
	<form method="post" action="cherche.php<?php
	if ($coup) {echo '?c=' . $coup ;} ?>" class="ingrid">
	<input type="hidden" name="reponse" value="1">
	<input type="hidden" name="question" value="<?php echo $idQ ; ?>">
	<?php if (isset($idR))	echo '	<input type="hidden" name="recherche" value="' . $idR . '">' ;	?>

	<button type="submit" class="borded gianticbuttons oui">
			<h2>Oui</h2>
	</button>

	</form>
	</div>
	
	<div class="grid1-3">
	<form method="post" action="cherche.php<?php 
	if ($coup) {echo '?c=' . $coup ;} ?>" class="ingrid">
	<input type="hidden" name="reponse" value="0">
	<input type="hidden" name="question" value="<?php echo $idQ ; ?>">
	<?php if (isset($idR))	echo '	<input type="hidden" name="recherche" value="' . $idR . '">' ;	?>
	
	<button type="submit" class="borded gianticbuttons bof">
			<h2>Bof</h2>
	</button>

	</form>
	</div>
	
	<div class="grid1-3">
	<form method="post" action="cherche.php
	<?php if ($coup) {echo '?c=' . $coup ;} ?>" class="ingrid">
	<input type="hidden" name="reponse" value="-1">
	<input type="hidden" name="question" value="<?php echo $idQ ; ?>">
	<?php if (isset($idR))	echo '	<input type="hidden" name="recherche" value="' . $idR . '">' ;	?>
	
	<button type="submit" class="borded gianticbuttons non">
			<h2>Non</h2>
	</button>
	</form>
	</div>

</div>

<?php
if (($nbP > 0) && ($coup > 2 || $nbP < 3))
{
?>

<div class="propositions">

	<div class="grid1">
	<div class="ingrid">
	<div class="borded">
	<h3>Tu peux cliquer sur le bon résultat si je l'ai trouvé.</h3>
	</div>
	</div>
	</div>
<?php
foreach ($idPs as $idP)
{
	$vert = 200-floor( ($scoreP[$idP]+1) / ($bestScoreP+1) * 100 )
?>
	<div class="grid1-5">
	<div class="ingrid">
	<a href="fini.php?r=<?php echo $idR ; ?>&p=<?php echo $idP ; ?>" class="borded">
	<div class="propos" style="background: rgb(<?php echo $vert ; ?>,200,<?php echo $vert ; ?>);color:#333">
		<div class="gianticbuttons"><strong><?php echo htmlspecialchars($pNoms[$idP]) ; ?></strong></div>
	</div>
	</a>
	</div>
	</div>
<?php
}

?>
	</p>
</div>
<?php
}
if (($coup > 3) OR ($coup > 1 && $nbP == 0))
{
?>
<div class="propositions">

	<div class="grid1-2 retour">
	<div class="ingrid">
	<a href="add.php?produit&r=<?php echo $idR ; ?>" class="borded">
		 <div class="gianticbuttons addproduit">
	<p>Je n'ai pas su trouver ?</p><p>Apprends-moi à quoi tu pensais !</p>
		</div>
	</a>
	</div>
	</div>

</div>
<?php
}
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
