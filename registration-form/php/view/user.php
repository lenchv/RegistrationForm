<?php
$myDB = new MyDatabase();
$info_user = $myDB->getField($_SESSION['id']); 
?>
<div id="user-page">
	<img src=<?echo "'".$info_user['photo']."'";?> alt="avatar"/>
	<div id="info">
		<a href="?exit" class="exit"><?echo $lang["user"]["exit"];?></a>
		<h1><?echo $info_user['name'];?></h1>
		<hr/>
		<div style="min-width: 370px">
			<div>
				<p><?echo $lang["user"]["birthdate"];?></p>
				<p><?echo $lang["user"]["gender"]['title'];?></p>
				<p><?echo $lang["user"]["country"];?></p>
				<p><?echo $lang["user"]["city"];?></p>
			</div>

			<div>
				<p><?echo $info_user['birth_date'];?></p>
				<p><?echo $lang["user"]["gender"][$info_user['gender']];?></p>
				<p><?echo $info_user['country'];?></p>
				<p><?echo $info_user['city'];?></p>
			</div>
		</div>
	</div>
</div>