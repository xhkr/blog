<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC";

    //Determine if a variable is set and is not NULL
     $sort = "";
    if(isset($_GET["sort"]) ) { //Avoids error message
        $sort = $_GET["sort"];
    }
    // Sort post by name
    if($sort == "name") {
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY title ASC";
    }
    // Sort post by lastest entry
    if($sort == "asc") {
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created ASC";
    }
    // Sort post by the last one
    if($sort == "desc") {
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC";
    }


    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

    // TODO: Seperating in different months needs to be handled by PHP and SQL.
    // TODO: Get the styling right on buttons, select and svg.
    $months = array(
        array("Januari", 01),
        array("Februari", 02),
        array("Mars", 03),
        array("April", 04),
        array("Maj", 05),
        array("Juni", 06),
        array("Juli", 07),
        array("Augusti", 08),
        array("September", 09),
        array("Oktober", 10),
        array("November", 11),
        array("December", 12),
        );

        //Determine if a variable is set and is not NULL
     $month = "";

    if(isset($_GET["month"]) ) { //Avoids error message
        $sort = $_GET["month"];
        $query = "SELECT EXTRACT(MONTH FROM created) FROM posts
        WHERE EXTRACT(MONTH FROM created) = 11";
    }


    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($monthtest);
        $stmt->fetch();
        echo $monthtest;
    }

     // $shop = array(
     //            array("monthTitle" => "Januari",
     //                  "monthNumber" => 01
     //                ),
     //           array("monthTitle" => "Februari",
     //                  "monthNumber" => 02
     //                ),
                // array("monthTitle "=> "Mars",
                //       "monthNumber" => 03
                //     ),
                // array("monthTitle "=> "April",
                //       "monthNumber" => 04
                //     ),
                // array("monthTitle" => "Maj",
                //       "monthNumber" => 05
                //     ),
                // array("monthTitle" => "Juni",
                //       "monthNumber" => 06
                //     ),
                // array("monthTitle" => "Juli",
                //       "monthNumber" => 07
                //     ),
                // array("monthTitle" => "Augusti",
                //       "monthNumber" => 08
                //     ),
                // array("monthTitle" => "September",
                //       "monthNumber" => 09
                //     ),
                // array("monthTitle" => "Oktober",
                //       "monthNumber" => 10
                //     ),
                // array("monthTitle" => "November",
                //       "monthNumber" => 11
                //     ),
                // array("monthTitle" => "December",
                //      "monthNumber" => 12
                //     ),
             // );
?>
<main>
    <h1 class="margin-bottom-l">Arkiv</h1>
    <form method="GET" action="archive.php">
        <label for="sort">Sortera arkivet</label>
        <div class="select-arrows">
          <select class="form-field form-field__select" name="sort" id="sort">
              <option value="desc">Senast publicerad</option>
              <option value="asc">Tidigast publicerad</option>
              <option value="name">Sortera efter bokstavsordning (A-Z)</option>
          </select>
          <label for="sort">Filtrera månadsvis</label>
          <select class="form-field form-field__select" name="sort" id="sort">
            <option>Alla</option>
            <?php for($i = 0; $i < 12; $i++): ?>
              <option value="month"><?php echo $months[$i][0]; ?></option>
            <?php endfor; ?>
          </select>
          <button class="button button--small border-radius margin-bottom-l" type="submit">Sortera</button>
          <!-- <svg class="icon select-arrows">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
          </svg> -->
        </div>
    </form>
   <!--  <form method="GET" action="archive.php">
        <label for="month">Filtrera arkivet månadsvis</label>
        <div class="select-arrows">
          <select class="form-field form-field__select" name="sort-month" id="sort-month">
            <option>Alla</option>
            <?php for($i = 0; $i < 12; $i++): ?>
              <option value="month"><?php echo $months[$i][0]; ?></option>
            <?php endfor; ?>
          </select>
          <button class="button button--small border-radius margin-bottom-l" type="submit">Sortera</button>
          <!-- <svg class="icon select-arrows">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
          </svg> -->
       <!-- </div>
    </form> -->
    <div class="list-wrapper">
        <!-- <h2>November 2016</h2> -->
        <ul class="no-padding">
        <!-- <?php //while (mysqli_stmt_fetch($stmt)): ?>
            <li class="list-style-none"><span class="saffron-text primary-brand-font">[<?php //echo formatDate($created); ?>]</span><a href="post.php?getpost=<?php //echo $id ?>"><?php //echo $title; ?></a></li>
        <?php //endwhile; ?> -->
        </ul>
    </div>
</main>
<?php require_once "./templates/footer.php"; ?>

