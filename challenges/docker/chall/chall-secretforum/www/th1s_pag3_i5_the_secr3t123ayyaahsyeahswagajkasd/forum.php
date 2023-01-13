<?php
if (!defined('__DIR__')) die('');
?>
	<main>
		<table>
			<thead>
			<tr>
				<th data-sort="int">#</th>
				<th data-sort="string">Name</th>
				<th data-sort="string">Title</th>
				<th data-sort="int">Time</th>
				<th data-sort="int">Hit</th>
			</tr>
			</thead>
			<tbody>
			<?php
				$page = intval($_GET[ 'page2' ]);
				if (!$page) $page = 1;

				$end_row = 5;
				$start_row = ($page - 1) * $end_row;

				$query = $db->dbQuery("SELECT `idx` FROM `simple_board`;");
				$row_cnt = mysql_num_rows($query);

				$max_page = ceil($row_cnt / $end_row);

				$query = $db->dbQuery(sprintf("SELECT * FROM `simple_board` ORDER BY `idx` DESC LIMIT %d, %d;", $start_row, $end_row));
				while($result = mysql_fetch_array($query)){
					$date = explode(" ", $result[ 'date' ]);
					$name = $result[ 'ip' ] == $_SERVER[ 'HTTP_CF_CONNECTING_IP' ] ? $result[ 'username' ] : 'SECRET';
					$subject = $result[ 'ip' ] == $_SERVER[ 'HTTP_CF_CONNECTING_IP' ] ? $result[ 'subject' ] : 'SECRET';
			?>
				<tr class="notice">
					<td><?php echo $result[ 'idx' ]; ?></td>
					<td data-sort-value="<?php echo $name; ?>"><a href="./?page=view&no=<?php echo $result[ 'idx' ]; ?>"><?php echo $name; ?></a></td>
					<td data-sort-value="<?php echo $subject; ?>"><a href="./?page=view&no=<?php echo $result[ 'idx' ]; ?>"><?php echo $subject; ?></a></td>
					<td data-sort-value="<?php echo $result[ 'idx' ]; ?>"><?php echo $date[ 0 ]; ?></td>
					<td><?php echo $result[ 'hit' ]; ?></td>
				</tr>
			<?php
				}
			?>
			</tbody>
		</table>
		<div style="margin-top: 10px;">
			<p id="page">
			<?php if(($page - 1) != 0 && ($page - 1) < $max_page){ ?><a class="l" href="/?page=forum&page2=<?php echo $page - 1; ?>">&Lt;</a><?php } ?>
			<?php
				for($i = 1; $i <= $max_page; $i++){
			?>
			<a class="active" href="/?page=forum&page2=<?php echo $i; ?>"><?php echo $i; ?></a>
			<?php
				}
			?>
			<?php if(($page + 1) <= $max_page){ ?><a class="l" href="/?page=forum&page2=<?php echo $page + 1; ?>">&Gt;</a><?php } ?>
			</p>
			<p class="right">
				<a class="button" href="./?page=write">Write</a>
			</p>
		</div>
	</main>
