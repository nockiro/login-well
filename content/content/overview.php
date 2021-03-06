<?php
sec_session_start();

$usercount = get_usercount($mysqli);

$widgets = $_COOKIE["widget"];
$widgetsEnabled = false;
// loop through 
foreach ($widgets as $widgetname => $enabled) {
    if ($enabled == 1) {
        $widgetsEnabled = true;
        break;
    }
}
?>

<style>
    .flippinright 
    {
        float:left;
        margin-right: 20px;
    }

    .ratingDiv {
        transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
    }

    tr {
        border-bottom: 1px solid #bbb;
    }

    tr:last-child {
        border-bottom: none;
    }
</style>
<script>
    function showRate(pid) {
        // if the rating dialog isn't visible, show it and hide the current rating
        if (document.getElementById('rate-' + pid).style.display == 'none') {
            document.getElementById('rate-' + pid).style.display = 'block';
            document.getElementById('rated-' + pid).style.display = 'none';
        } else {
            document.getElementById('rate-' + pid).style.display = 'none';
            document.getElementById('rated-' + pid).style.display = 'block';
        }
    }

    function rate(pid, rating) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('rate-' + pid).style.display = 'none';
                document.getElementById('rated-' + pid).style.display = 'block';
                document.getElementById('LinkRated-' + pid).innerHTML = rating;
            } else if (this.status >= 500) { // if its a server error 5xx (like 500), print the problem
                document.getElementById('rate-' + pid).innerHTML = "Ein Fehler ist aufgetreten: " + this.responseText;
            }
        };
        xhttp.open("POST", "api/ratePage.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("pid=" + pid + "&rating=" + rating);
    }

    function deleteUPage(pid) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('userPageTable').innerHTML = this.responseText;
            } else if (this.status >= 500) { // if its a server error 5xx (like 500), print the problem
                document.getElementById('userPageTable').innerHTML = "<div class=\"content error\">There.. was a problem. " + this.responseText + "</div>";
            }
        };
        xhttp.open("POST", "api/deleteUserPage.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("pid=" + pid);
    }

    function openCategory(catName) {
        var i;
        var x = document.getElementsByClassName("cat");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }

        var x = document.getElementsByClassName("tablinks");
        for (i = 0; i < x.length; i++) {
            x[i].style.fontWeight = "normal";
        }
        document.getElementById(catName).style.display = "block";
        document.getElementById("tab" + catName).style.fontWeight = "bold";

        document.getElementById("ranktitle").textContent = "Ranking (" + catName + ")";
    }

    function getUserStats()
    {
        document.getElementById("users").innerHTML = '<img alt="" height="25" src="/img/ladekreis.gif" width="25">';

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4) {
                document.getElementById("users").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "/api/getUserRanking.php");
        xmlhttp.send();
        return;
    }

    // selects tab as soon as the page is loaded
    window.addEventListener("load", function () {
        openCategory("worldwide");
        getUserStats();
    });

</script>
<?php if (login_check($mysqli)) : ?>
    <div class="content">
        <p>Welcome <?php echo htmlentities($_SESSION['username']); ?>! 	
            Would you like to <a href="logout.php">logout</a>?</p>
    </div>
    <div class="content">
        <h3>Your websites <a href="/?cp=addpage"><b>(+)</b></a></h3>
        <hr/>
        <span id="userPageTable">
            <?php printUserPageTable(getShortURLStats($mysqli)); ?>
        </span>
    </div>
    <?php if ($widgetsEnabled) : ?>
        <div class="content">
            <h3>Widgets</h3>
            <hr/>
            <?php
            include_once file_build_path("content", "widgets", "widgets.php");
            $allwidgets = \loginwell_widgets\widgetCategories::getClasses();

            // loop through each cookie of us for getting
            foreach ($widgets as $widgetname => $enabled) {
                if ($enabled) {
                    ?>

                    <div class="content" style="display: table !important;">
                        <span id="<?php echo $widgetname; ?>">
                            <?php \loginwell_widgets\widgetCategories::getWidgetCode($widgetname); ?>
                        </span>
                    </div>
                    <?php
                }
            }
            ?>

        </div>
    <?php endif; ?>
    <div class="content">
        <h3 id="ranktitle">Ranking (worldwide)</h3>
        <hr/>

        <div class="tab">
            <input type="button" class="tablinks" id="tabusers" onclick="openCategory('users')" value="Nutzer">
            &#124;
            <input type="button" class="tablinks" id="tabworldwide" onclick="openCategory('worldwide')" value="Alle Kategorien">
            &#124;
            <?php
            $allCategories = getAllCategories($mysqli);

            // loop trough each category and make switch buttons
            foreach ($allCategories as $category) {
                $title = $category["title"];
                echo '<input type="button" class="tablinks" id="tab' . $title . '" onclick="openCategory(\'' . $title . '\')" value="' . $title . '">';
            }
            ?>
        </div>


        <div id="worldwide" class="cat" style="display: block;">
            <ol class="flippinright">
                <?php printTopRanking(getTopRankings($mysqli)); ?>
        </div>

        <div id="users" class="cat" style="display: none;">
        </div>
        <?php foreach ($allCategories as $category) { ?>
            <div id="<?php echo $category["title"]; ?>" class="cat" style="display: none">
                <ol class="flippinright">
                    <?php printTopRanking(getTopRankings($mysqli, $category["catID"])); ?>
            </div>
        <?php } ?>

    </div>
<?php else : ?>
    <div class="content">
        <form method="post" action="account/process_login.php" name="login_form">
            <div>
                <label for="email">E-Mail</label>
                <input type="text" name="email" id="email" value="" /></div>

            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" value="" /></div>

            <div>
                <input type="button" onclick="formhash(this.form, this.form.password);" value="Login" /></div>
        </form>
    </div>

    <div class="content">
        <p>If you don't have a login, please <a href=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?cp=register">register</a>. (<a style="font-size: 14px;" href=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?cp=reset_pw">Forgot your password?</a>)</p>
        <p style="font-size: larger;">There <?php echo ($usercount > 1 ? "are" : "is") ?> currently <?php echo $usercount . ($usercount > 1 ? " Users" : " User"); ?> registered.</p>	
    </div>
<?php endif; ?>
