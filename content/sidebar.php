<div id="sidebar" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" id="navaction" onclick="closeNav();">&times;</a>
    <center>
        <img src="img/Logo.png" height="100px" id="logo">
        <span style="font-size:x-large;">
            <a href="/?cp">Home</a>
            <a href="/?cp=profile">Profile</a>
            <a href="/?cp=settings">Settings</a>
            <a href="/?cp=about">About</a>
			<a href="/?cp=rate">Rate</a>

            <?php if (getAdminPrivs($mysqli)) : ?>
                <hr/>
                <a href="/?cp=adminpanel">Administration panel</a>
            <?php endif; ?>
        </span>
        <br/>
        <?php if (login_check($mysqli)) : ?>
            <h4>Last Logout-Picture:</h4> <br/>
            <img src="account/getCard.php?img=<?php echo ($_SESSION["sec_card"] != -1 ? $_SESSION["sec_card"] : "nocard"); ?>" width="76" height="105">
        <?php endif; ?>
    </center>
</div>

<!-- after the navigation bar has been load(ed?), prepare for closing on too damn small devices or check for resizing -->
<script type="text/javascript">
    window.onload = initializeNavBar;
</script>
