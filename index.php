<?php

    // File to include
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    // Variables
    $display = NULL; // To avoid "undefined variable".
    $numberOfComments = NULL;

    // Pagination, display 2 posts per page
    // TODO: change to 5
    $postPage = 5;

    // Query to check number of rows in table posts
    $query = "SELECT id FROM posts WHERE published = 1";

    // Execute query.
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $rows = $stmt->num_rows; // Number of rows in posts
    }
    else {
        $errorMessage = "Fel på query.";
    }
    $last = ceil($rows/$postPage); // Round up
    // If less posts than number on each page
    if ($last < 1) {
        $last = 1;
    }

    $pagenum = 1; // First page
    if(isset($_GET['pn'])){
        $pagenum = $_GET['pn'];
    }
    if ($pagenum < 1) {
        $pagenum = 1;
    } else if ($pagenum > $last) {
        $pagenum = $last;
    }
    $limit = 'LIMIT ' .($pagenum - 1) * $postPage .',' .$postPage; //  => LIMIT 0, 5 or

    // SQL statement with LEFT JOIN table -> posts & categories. Latest post is shown first.
    $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC $limit";

    // If GET request "display" is set.
    if (isset($_GET["display"])) {
        $display = $_GET["display"];

        // New SQL statement WHERE categories.category = $display.
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}' AND published = 1 ORDER BY created DESC $limit";
    }

    // Execute query.
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

    $paginationCtrls = '';
    if($last != 1){
	/* If page one then we don't need a link to the previous page.
        Otherwise generate link to previous page. */
        if ($pagenum > 1) {
            $previous = $pagenum - 1;
            $paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'">&lt; &nbsp; Föregående</a> &nbsp; &nbsp; ';
        }

        // This does the same as above, only checking if we are on the last page
        if ($pagenum != $last) {
            $next = $pagenum + 1;
            $paginationCtrls .= ' &nbsp; &nbsp; <a href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'">Nästa &nbsp; &gt;</a> ';
        }
    }

    $posts = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($posts, array(
            "id" => $id,
            "image" => $image,
            "title" => $title,
            "content" => $content,
            "categoryId" => $categoryId,
            "created" => $created,
            "categoryName" => $categoryName
        ));
        // TODO: Trim $created so that only date is shown.
    } ?>

    <div class="content-slides-in">

        <div class="pagination-wrapper">
            <div class="pagination-wrapper__text">
            <?php
                echo $paginationCtrls;
            ?>
            </div>
        </div>

    <?php for ($i=0; $i < count($posts); $i++):
        $post = $posts[$i];
    ?>
    <article class="list">
        <div class="blogpost-wrapper">
            <a href="post.php?getpost=<?php echo $post["id"] ?>"><img src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>"></a>
            <div class="blogpost-wrapper__text">
                <h2><?php echo formatInnerHtml($post["title"]); ?></h2>
                <p class="tag">[Tags: <a href="?display=<?php echo $post["categoryId"] ?>"><?php echo str_replace(' ', '', $post["categoryName"]); ?>]</a> [<?php echo $post["created"] ?>]</p>
                <div class="comment-bubble">
                    <div class="comment-bubble__show-comments">
                        <?php // START OF COMMENTS
                        // TODO: Right now, this div is not used. Delete if we don't want it.

                        $totalNumberOfComments = 0;
                        $errorMessage = NULL;

                        $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

                        if ($stmt->prepare($query)) {
                            $stmt->execute();
                            $stmt->bind_result($commentId, $userId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $postId);
                        } else {
                            $errorMessage = "Något gick fel.";
                        }

                        while (mysqli_stmt_fetch($stmt)):
                            $stmt->store_result();
                            $numberOfComments = mysqli_stmt_num_rows($stmt);

                            if ($post["id"] == $postId) {
                                // TODO: If we want to show these, use styling from utilities.scss.
                                echo "<p class=\"comment-bubble__comment-content\">$commentContent</p>";
                                echo "<p class=\"comment-bubble__comment-author\">$commentAuthor</p>";
                                $totalNumberOfComments++;
                            }

                        endwhile;
                        ?>
                </div>
                    <a href="post.php?getpost=<?php echo $post["id"] ?>"><i class="fa fa-comment comment-bubble__offset-text" aria-hidden="true"></i>
                    <p class="comment-bubble__number"><?php echo "$totalNumberOfComments" ?></p></a>
                </div>
            </div>
        </div>
    </article>
<?php endfor; ?>
</div>

<?php
    if ($errorMessage) { echo $errorMessage; }
?>
<div class="pagination-wrapper">
    <div class="pagination-wrapper__text">
<?php
    echo $paginationCtrls;
?>
    </div>
</div>
<?php
    require_once "./templates/footer.php";
?>
