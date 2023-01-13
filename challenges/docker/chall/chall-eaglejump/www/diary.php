<table class="table table-hover">
	<thead>
		<tr>
			<th width=20%>no</th>
			<th width=80%>title</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$q = "SELECT * FROM board order by no desc";
		$res = mysqli_query($dbconn,$q);
		while($row = mysqli_fetch_assoc($res))
		{
			echo "<tr>";
			echo "<td><a href='?p=read.php&no={$row['no']}'>".$row['no']."</a></td>";
			echo "<td><a href='?p=read.php&no={$row['no']}'>".$row['title']."</a></td>";
			echo "</tr>";
		}
		unset ($q);
		unset ($res);
		unset ($row);
	?>
	</tbody>
</table>
