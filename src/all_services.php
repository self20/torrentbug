<?php

/*************************************************************
*  TorrentFlux - PHP Torrent Manager
*  www.torrentflux.com
**************************************************************/
/*
    This file is part of TorrentFlux.

    TorrentFlux is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    TorrentFlux is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with TorrentFlux; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
    include_once './Class/autoload.php';

include_once("config.php");
include_once("functions.php");

    $settings = new Gratbrav\Torrentbug\Settings();

if (! IsAdmin($cfg["user"])) {
    header("Location:index.php");
}

	$subMenu = 'index';
	include_once 'header.php'; 
	
	$result = shell_exec("df -h " . $settings->get('path'));
	$result2 = shell_exec("du -sh " . $settings->get('path') . "*");
	$result4 = shell_exec("w");
	$result5 = shell_exec("free -mo");
?>
<div class="container">
	<div class="row">
		<div class="col-sm-12 bd-example" style="padding: 10px;">
			<?php echo displayDriveSpaceBar(getDriveSpace($settings->get('path'))); ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-12 bd-example" style="padding: 10px;">
			<pre><?php
				echo "<strong>"._DRIVESPACE."</strong>";
				
				echo $result;
				echo "<hr>";
				echo $result2;
				
				echo "<hr>";
				
				echo "<strong>"._SERVERSTATS."</strong>";
				
				echo $result4;
				echo "<hr>";
				echo $result5;
				echo "</pre>";
			
			?>
		</div>
	</div>
</div>

<div style="text-align:center">[<a href="index.php"><?php echo _RETURNTOTORRENTS ?></a>]</div>
