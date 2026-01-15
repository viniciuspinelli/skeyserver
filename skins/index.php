<?php
require_once 'class/config.php';
require_once 'class/database.php';
require_once 'steamauth/steamauth.php';
require_once 'class/utils.php';

$db = new DataBase();
if (isset($_SESSION['steamid'])) {

	$steamid = $_SESSION['steamid'];

	$weapons = UtilsClass::getWeaponsFromArray();
	$skins = UtilsClass::skinsFromJson();
    $querySelected = $db->select("
        SELECT `weapon_defindex`, MAX(`weapon_paint_id`) AS `weapon_paint_id`, MAX(`weapon_wear`) AS `weapon_wear`, MAX(`weapon_seed`) AS `weapon_seed`
        FROM `wp_player_skins`
        WHERE `steamid` = :steamid
        GROUP BY `weapon_defindex`, `steamid`
    ", ["steamid" => $steamid]);
	$selectedSkins = UtilsClass::getSelectedSkins($querySelected);
	$selectedKnife = $db->select("SELECT * FROM `wp_player_knife` WHERE `wp_player_knife`.`steamid` = :steamid LIMIT 1", ["steamid" => $steamid]);
	$knifes = UtilsClass::getKnifeTypes();

	if (isset($_POST['forma'])) {
		$ex = explode("-", $_POST['forma']);

		if ($ex[0] == "knife") {
			$db->query("INSERT INTO `wp_player_knife` (`steamid`, `knife`, `weapon_team`) VALUES(:steamid, :knife, 2) ON DUPLICATE KEY UPDATE `knife` = :knife", ["steamid" => $steamid, "knife" => $knifes[$ex[1]]['weapon_name']]);
			$db->query("INSERT INTO `wp_player_knife` (`steamid`, `knife`, `weapon_team`) VALUES(:steamid, :knife, 3) ON DUPLICATE KEY UPDATE `knife` = :knife", ["steamid" => $steamid, "knife" => $knifes[$ex[1]]['weapon_name']]);
		} else {
			if (array_key_exists($ex[1], $skins[$ex[0]]) && isset($_POST['wear']) && $_POST['wear'] >= 0.00 && $_POST['wear'] <= 1.00 && isset($_POST['seed'])) {
				$wear = floatval($_POST['wear']);
				$seed = intval($_POST['seed']);
				if (array_key_exists($ex[0], $selectedSkins)) {
					$db->query("UPDATE wp_player_skins SET weapon_paint_id = :weapon_paint_id, weapon_wear = :weapon_wear, weapon_seed = :weapon_seed WHERE steamid = :steamid AND weapon_defindex = :weapon_defindex", ["steamid" => $steamid, "weapon_defindex" => $ex[0], "weapon_paint_id" => $ex[1], "weapon_wear" => $wear, "weapon_seed" => $seed]);
				} else {
					$db->query("INSERT INTO wp_player_skins (`steamid`, `weapon_defindex`, `weapon_paint_id`, `weapon_wear`, `weapon_seed`, `weapon_team`) VALUES (:steamid, :weapon_defindex, :weapon_paint_id, :weapon_wear, :weapon_seed, 2)", ["steamid" => $steamid, "weapon_defindex" => $ex[0], "weapon_paint_id" => $ex[1], "weapon_wear" => $wear, "weapon_seed" => $seed]);
					$db->query("INSERT INTO wp_player_skins (`steamid`, `weapon_defindex`, `weapon_paint_id`, `weapon_wear`, `weapon_seed`, `weapon_team`) VALUES (:steamid, :weapon_defindex, :weapon_paint_id, :weapon_wear, :weapon_seed, 3)", ["steamid" => $steamid, "weapon_defindex" => $ex[0], "weapon_paint_id" => $ex[1], "weapon_wear" => $wear, "weapon_seed" => $seed]);
				}
			}
		}
		header("Location: {$_SERVER['PHP_SELF']}");
	}
}
?>

<!DOCTYPE html>
<html lang="en"<?php if(WEB_STYLE_DARK) echo ' data-bs-theme="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="style.css">
	<title>Skey Server - Weapon Paints</title>
	<link rel="icon" type="image/png" href="../favicon.png">
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
		<div class="container">
			<a class="navbar-brand" href="../">
				<img src="../logo.png" alt="Skey Server" height="40">
			</a>
			<span class="navbar-text">Weapon Paints</span>
		</div>
	</nav>

	<div class="container">
	<?php
	if (!isset($_SESSION['steamid'])) {
		echo "<div class='bg-primary p-4 rounded text-center'><h2>Para escolher suas skins, faça login com a Steam ";
		loginbutton("rectangle");
		echo "</h2></div>";
	} else {
		require 'steamauth/userInfo.php';
		echo "<div class='bg-primary p-3 rounded mb-3'>";
		echo "<div class='d-flex align-items-center justify-content-between'>";
		echo "<div class='d-flex align-items-center'>";
		echo "<img src='{$steamprofile['avatarmedium']}' class='rounded me-3' style='width: 64px;'>";
		echo "<div>";
		echo "<h4 class='mb-0'>{$steamprofile['personaname']}</h4>";
		echo "<small class='text-muted'>SteamID: {$steamprofile['steamid']}</small>";
		echo "</div>";
		echo "</div>";
		echo "<a class='btn btn-danger' href='{$_SERVER['PHP_SELF']}?logout'>Logout</a>";
		echo "</div>";
		echo "</div>";
		echo "<p class='text-muted mb-3'>Use <code>!wp</code> no servidor para atualizar suas skins após selecionar.</p>";
		echo "<div class='row'>";
	?>

		<div class="col-md-3 col-sm-6 mb-3">
			<div class="card text-center border-warning">
				<div class="card-header bg-warning text-dark">
					<h6 class="mb-0">🔪 Faca</h6>
				</div>
				<div class="card-body">
					<?php
					$actualKnife = $knifes[0] ?? ['paint_name' => 'Default', 'image_url' => ''];
					if ($selectedKnife != null && !empty($knifes)) {
						foreach ($knifes as $knife) {
							if (isset($selectedKnife[0]['knife']) && $selectedKnife[0]['knife'] == $knife['weapon_name']) {
								$actualKnife = $knife;
								break;
							}
						}
					}
					echo "<h5 class='card-title'>{$actualKnife['paint_name']}</h5>";
					if (!empty($actualKnife['image_url'])) {
						echo "<img src='{$actualKnife['image_url']}' class='skin-image'>";
					}
					?>
				</div>
				<div class="card-footer">
					<form action="" method="POST">
						<select name="forma" class="form-control select" onchange="this.form.submit()">
							<option disabled selected>Selecionar faca</option>
							<?php
							if (!empty($knifes)) {
								foreach ($knifes as $knifeKey => $knife) {
									$selected = (isset($selectedKnife[0]['knife']) && $selectedKnife[0]['knife'] == $knife['weapon_name']) ? 'selected' : '';
									echo "<option {$selected} value=\"knife-{$knifeKey}\">{$knife['paint_name']}</option>";
								}
							}
							?>
						</select>
					</form>
				</div>
			</div>
		</div>

		<?php
		if (!empty($weapons)) {
			foreach ($weapons as $defindex => $default) { 
				// Pular facas na lista de armas
				if (in_array($defindex, [500, 503, 505, 506, 507, 508, 509, 512, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 525, 526])) continue;
		?>
			<div class="col-md-3 col-sm-6 mb-3">
				<div class="card text-center">
					<div class="card-body">
						<?php
						if (array_key_exists($defindex, $selectedSkins) && isset($skins[$defindex][$selectedSkins[$defindex]['weapon_paint_id']])) {
							echo "<div class='card-header'>";
							echo "<h6 class='card-title'>{$skins[$defindex][$selectedSkins[$defindex]['weapon_paint_id']]['paint_name']}</h6>";
							echo "</div>";
							echo "<img src='{$skins[$defindex][$selectedSkins[$defindex]['weapon_paint_id']]['image_url']}' class='skin-image'>";
						} else {
							echo "<div class='card-header'>";
							echo "<h6 class='card-title'>{$default['paint_name']}</h6>";
							echo "</div>";
							echo "<img src='{$default['image_url']}' class='skin-image'>";
						}
						?>
					</div>
					<div class="card-footer">
						<form action="" method="POST">
							<input type="hidden" name="wear" value="<?php echo $selectedSkins[$defindex]['weapon_wear'] ?? '0.01'; ?>">
							<input type="hidden" name="seed" value="<?php echo $selectedSkins[$defindex]['weapon_seed'] ?? '0'; ?>">
							<select name="forma" class="form-control select" onchange="this.form.submit()">
								<option disabled selected>Selecionar skin</option>
								<?php
								if (isset($skins[$defindex])) {
									foreach ($skins[$defindex] as $paintKey => $paint) {
										$selected = (array_key_exists($defindex, $selectedSkins) && $selectedSkins[$defindex]['weapon_paint_id'] == $paintKey) ? 'selected' : '';
										echo "<option {$selected} value=\"{$defindex}-{$paintKey}\">{$paint['paint_name']}</option>";
									}
								}
								?>
							</select>
						</form>
					</div>
				</div>
			</div>
		<?php } } ?>
	</div>
	<?php } ?>
	</div>
	
	<div class="container">
		<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
			<div class="col-md-4 d-flex align-items-center">
				<span class="mb-3 mb-md-0 text-body-secondary">© 2024 <a href="../">Skey Server</a> | Plugin by <a href="https://github.com/Nereziel/cs2-WeaponPaints">Nereziel</a></span>
			</div>
		</footer>
	</div>
</body>

</html>
