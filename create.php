<?php

include 'connect.php';
/*
if(isset($_GET['add_produit']) && isset($_POST['nom']))
{
	try
	{
		$req = $bdd->prepare('INSERT INTO produits (nom) VALUES(?)');
		$req->execute(array($_POST['nom']));

		echo 'Le produit a bien été ajouté !!!<br/>';
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$req->closeCursor();
}

elseif(isset($_GET['add_question']) && isset($_POST['question']))
{
	try
	{
		$req = $bdd->prepare('INSERT INTO questions(question) VALUES(?)');
		$req->execute(array($_POST['question']));

		echo 'La question a bien été ajouté !<br/>';
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$req->closeCursor();
}

elseif(isset($_GET['add_reponse']))
{

print_r($_POST);
*/
foreach($_POST as $key => $value)
{
	$identifiants = explode('-', $key) ;
	$idP = $identifiants[0] ;
	$idQ = $identifiants[1] ;
	if (($value == -1) || ($value == 0) || ($value == 1))
	try
	{
		$req = $bdd->prepare('INSERT INTO reponses(idP,idQ,reponse) VALUES(?,?,?)');
		$req->execute(array($idP,$idQ,$value));

		echo 'La réponse a bien été ajouté !<br/>';
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$req->closeCursor();

}

/*
/*
for ($i = 1; $i <= 10; $i++) {
    echo $i;
}

	try
	{
		for(
		$req = $bdd->prepare('INSERT INTO liens(idQ,idP,valeur) VALUES(?,?,?)');
		$req->execute(array($_POST['idQ'],$_POST['idP'],$_POST['valeur']));

		echo 'La réponse a bien été ajouté !<br/>';
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
*//*
}
*/
/*
if (isset($_GET['add_produit']))
{
?>

		<form method="post" action="?add_produit">
		<p>
			<label for="nom">Nom :</label>
			<input type="text" name="nom" id="nom" />

		</p>
		<p>
			<input type="submit" />
		</p>
	</form>
<?php
}

if (isset($_GET['add_question']))
{
?>
	<form method="post" action="?add_question">
		<p>
			<label for="question">Nom :</label>
			<input type="text" name="question" id="question" />

		</p>
		<p>
			<input type="submit" />
		</p>
	</form>
<?php

}

if (isset($_GET['add_reponse']))
{

	if (isset($_GET['idP']))
	{
						
		$req = $bdd->prepare('SELECT q.idQ, p.idP, q.question question, p.nom produit
							FROM produits p
							INNER JOIN questions q
							WHERE p.idP = ?
							AND NOT EXISTS 
								(
								SELECT *
								FROM reponses r
								WHERE
								r.idP=p.idP AND 
								r.idQ=q.idQ
								)
							order by p.idP * rand()
							LIMIT 0, 30');
						//WHERE l.valeur=0 AND l.pertinence=-1 AND idP = ?
		$req->execute(array($_GET['idP']));

	}
	elseif (isset($_GET['idQ']))
	{
						
		$req = $bdd->prepare('SELECT q.idQ, p.idP, q.question question, p.nom produit
							FROM questions q
							INNER JOIN produits p
							WHERE q.idQ = ?
							AND NOT EXISTS 
								(
								SELECT *
								FROM reponses r
								WHERE
								r.idP=p.idP AND 
								r.idQ=q.idQ
								)
							order by p.idP * rand()
							LIMIT 0, 30');
						//WHERE l.valeur=0 AND l.pertinence=-1 AND idP = ?
		$req->execute(array($_GET['idQ']));
	}
	else
	{*/
		$req = $bdd->query('SELECT q.idQ, p.idP, q.question question, p.nom produit
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
							order by p.idP * rand()
							LIMIT 0, 30');
							
		
							

/*	}*/
	if (isset($req))
	{
?>

	<form method="post" action="?add_reponse">

<?php
	while ($donnees = $req->fetch())
	{
		$name = $donnees['idP'] . '-' . $donnees['idQ'] ;
?>
		<p>	Produit : <strong><?php echo htmlspecialchars($donnees['produit']); ?></strong><br/>
			Question : <strong><?php echo htmlspecialchars($donnees['question']); ?> ?</strong>
		</p>
		<p>
			<label for="<?php echo $name; ?>">Reponse :</label>
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" />
		</p>
<?php
	}
	$req->closeCursor();
?>

	<input type="submit" />

	</form>
<?php
	}
/*}*/

?>
<?php
include 'pub.php' ;
?>