<?php
include 'connect.php';

$req = $bdd->query('SELECT COUNT(*)
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
						');
$n = $req->fetchColumn() ;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trouve Tout</title>
<link rel="stylesheet" href="css.css" media="all">
</head>

<body>
<div class="banner">
	<h1 class="banner-head">
		Trouve Tout
    </h1>
</div>
<div class="content">
<div class="chercomp">
<?php if ($n > 0) { ?>
	<div class="grid2-3">

<?php } else { ?>
	<div class="grid1">
<?php } ?>

	<div class="ingrid">
	<a href="cherche.php" class="borded">
		<div class="gianticbuttons cherche">
			<h2>Chercher</h2>
		</div>
		<p><strong>Nous pouvons trouver toutes sortes de choses.<br/>Répondez à nos questions et nous ferons de notre mieux.</strong></p>
		<p><strong>Si par malheur notre objet n'est pas dans la base de donnée,<br/>vous pourrez l'y ajouter !</strong></p>
	</a>
	</div>
	</div>

<?php if ($n > 0) { ?>
	<div class="grid1-3">
	<div class="ingrid">
	<a href="complete.php" class="borded">
		 <div class="gianticbuttons complete">
			<h2>Aider</h2>
		</div>
		<p><strong>Il existe <stronger><?php echo $n ?></stronger> questions non répondues.<br/>Aidez-nous !</strong></p>
	</a>
	</div>
	</div>
<?php } ?>
</div>
<?php
include 'pub.php' ;
?>
</div>
</body>
</html>
