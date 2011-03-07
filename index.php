<?php
include('system/loading.php');

timegen('allsite');

$GLOBALS['data'] = new SimpleData();

include(DIR.'/system/starter.php');

/* super god-mode check */
if (@$_SESSION['user']['type'] == 'a') {
    $bGodmodeSuspended = @$_SESSION['godmode_suspended'] ? 'true' : ' false';
    echo '<script type="text/javascript">var g_bGodmode = true; g_bGodmodeSuspended = '.$bGodmodeSuspended.'</script>';
    echo '<script type="text/javascript" src="/js/godmode.js"></script>';
}

timegen_result();

?>