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

    include_once 'Class/autoload.php';
    include_once 'config.php';

    $settings = new Class_Settings();

    include_once 'functions.php';

    $msgService = new \Message\Service($cfg['user']);

    $delete = getRequestVar('delete');
    if (!empty($delete)) {
        $msgService->delete($delete);
        header('location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    include_once 'header.php';

$mid = getRequestVar('mid');
if (!empty($mid) && is_numeric($mid)) {
    list($from_user, $message, $ip, $time, $isnew, $force_read) = GetMessage($mid);

    if (!empty($from_user) && $isnew == 1) { 
        // We have a Message that is being seen
        // Mark it as NOT new.
        MarkMessageRead($mid);
    }
?>

<div style="text-align:center">[<a href="?"><?php echo _RETURNTOMESSAGES ?></a>]</div>


<div class="container">
	<div class="row">
		<div class="col-sm-12 bd-example">
			<?php
				$message = check_html($message, "nohtml");
    			$message = str_replace("\n", "<br>", $message);
    		?>
    		<table class="table table-striped">
    			<tr>
					<td>
						<?php echo _FROM ?>: 
						<strong><?php echo $from_user ?></strong>
					</td>
					<td style="text-align:right">
					    <?php if (IsUser($from_user)) { ?>
					        <a href="message.php?to_user=<?php echo $from_user ?>&rmid=<?php echo $mid ?>">
								<img src="images/reply.gif" title="<?php echo _REPLY ?>" alt="" />
					        </a>
					    <?php } ?>
    					<a href="<?php echo $_SERVER['PHP_SELF'] ?>?delete=<?php echo $mid ?>">
   							<img src="images/delete_on.gif" title="<?php echo _DELETE ?>" alt="" />
   						</a>
   					</td>
   				</tr>
   				<tr>
   					<td colspan=2>
   						<?php echo _DATE ?>:  
   						<strong><?php echo date(_DATETIMEFORMAT, $time) ?></strong>
   					</td>
   				</tr>
   				<tr>
   					<td colspan=2>
   						<?php echo _MESSAGE ?>:
   						<blockquote><strong><?php echo $message ?></strong></blockquote>
   					</td>
   				</tr>
   			</table>
		</div>
	</div>
</div>

<?php } else { ?>

<script src="<?=$settings->get('base_url')?>/plugins/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?=$settings->get('base_url')?>/plugins/datatables/datatables/media/js/dataTables.bootstrap4.min.js"></script>

<link href="<?=$settings->get('base_url')?>/plugins/datatables/datatables/media/css/dataTables.bootstrap4.min.css" type="text/css" rel="stylesheet" />
<link href="<?=$settings->get('base_url')?>/plugins/twitter/bootstrap/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet" />

<div class="container">
    <div class="row">

        <div class="col-sm-12 bd-example" style="border:none;padding-right:0px;">
            <a class="btn btn-primary pull-right" href="message.php">
                <span class="btn-label icon fa fa-plus"></span>
                <?=_SENDMESSAGETO?>
            </a>
        </div>

        <div class="col-sm-12 bd-example" style="padding:16px;">
            <table id="message-list" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo _FROM ?></th>
                    <th><?php echo _MESSAGE ?></th>
                    <th><?php echo _DATE ?></th>
                    <th><?php echo _ADMIN ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $messageList = $msgService->getMessages();
                    foreach ((array)$messageList as $message) {

                        $mailIcon = ($message->getIsNew()) ? 'fa-envelope-o' : 'fa-envelope-open-o';

                        $messageContent = check_html($message->getMessage(), "nohtml");
                        if (strlen($messageContent) >= 40) { // needs to be trimmed
                            $messageContent = substr($messageContent, 0, 39);
                            $messageContent .= '...';
                        }

                        $link = $_SERVER['PHP_SELF'] . '?mid=' . $message->getMessageId();
                    ?>
                    <tr>
                        <td>
                            <a href="<?=$link?>"><i class="fa <?=$mailIcon?>" aria-hidden="true"></i></a>
                            <a href="<?=$link?>"><?=$message->getSender()?></a>
                        </td>
                        <td>
                            <a href="<?=$link?>"><?=$messageContent?></a>
                        </td>
                        <td style="text-align:center">
                            <a href="<?php echo $link ?>"><?php echo date(_DATETIMEFORMAT, $message->getTime()) ?></a>
                        </td>
                        <td style="text-align:center">
                        <?php
                            // Is this a force_read from an admin?
                            if ($message->getForceRead()) {
                                // Yes, then don't let them delete the message yet
                                echo '<img src="images/delete_off.gif" alt="" title="" />';
                            } else {
                                // No, let them reply or delete it
                                if (IsUser($message->getSender())) {
                                    echo "<a href=\"message.php?to_user=".$message->getSender()."&rmid=".$message->getMessageId()."\"><i class=\"fa fa-reply\" aria-hidden=\"true\"></i></a>";
                                }
                                echo "<a href=\"".$_SERVER['PHP_SELF']."?delete=".$message->getMessageId()."\" style=\"margin-left:6px;\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></a>";
                            }
                        ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <?php
                if (!count($messageList)) {
                    echo "<div style=\"text-align:center\"><strong>-- "._NORECORDSFOUND." --</strong></div>";
                }
            ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#message-list').dataTable( {
        // lengthChange: false,
    });
});
</script>

<?php } ?>

<div style="text-align:center">[<a href="index.php"><?php echo _RETURNTOTORRENTS ?></a>]</div>
