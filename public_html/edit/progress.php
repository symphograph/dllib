<?php

function progress($status, $percent, $step) {

	global 	$percentlast;



	switch ($status) {

		case 'start':

			echo '
				<script type="text/javascript">
					document.getElementById("progress").style.background = "red";
					document.getElementById("progress").style.height = "2px";
					document.getElementById("progress").style.width = "' . $percent . '%";
				</script>
			';
			flush();
			break;


		case 'run':

			if (@$percentlast + $step <= $percent ) {
				$percentlast = $percent;
				echo '
					<script type="text/javascript">
						document.getElementById("progress").style.width = "' . $percent . '%";
					</script>
				';
				flush();
			}
			break;


		case 'stop':

			echo '
				<script type="text/javascript">
					document.getElementById("progress").style.height = "0px";
					document.getElementById("progress").style.width = "0%";
				</script>
			';
			flush();
			break;

	}


}

?>
