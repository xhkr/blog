<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    // This redirects user to login.php if not logged in.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == false) {
        header("Location: ../login.php");
    }

/*******************************************************************************
   START OF FEEDBACK MESSAGE AND DATABASE UPDATE
*******************************************************************************/

    $feedbackMessage = NULL;
    $draftMessage = "<p class=\"relative-container__info\">Det här inlägget är inte publicerat</p>";

    if (isset($_POST["edit-post"])) {
        $postToEdit = $_POST["edit-post"];
        //Redirect to add post with current post id
        header("Location: ./posteditor.php?edit=$postToEdit");
    }

    if (isset($_POST["delete-post"])) {

        $postToDelete = $_POST["delete-post"];
        $query = "DELETE FROM posts WHERE id ='{$postToDelete}'";

        if ($stmt->prepare($query)) {
            $stmt->execute();
            $feedbackMessage = "Du har tagit bort inlägget";
        }
    }

/*******************************************************************************
   END OF FEEDBACK MESSAGE AND DATABASE UPDATE
*******************************************************************************/

/*******************************************************************************
   START OF QUERY AND STMT THAT IS USED TO PRINT POST LIST
*******************************************************************************/

    // TODO: Just get the variables you need.

    //  If logged in as super user, show all posts.
    if ($_SESSION["permission"] == 1) {

        $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id ORDER BY created DESC";
    } else {
        // If logged in as "redaktör", only show your posts.
        $userId = $_SESSION["userid"];
        $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE posts.userid = '{$userId}' ORDER BY created DESC";
    }

    // Execute query.
    if ($stmt->prepare($query)) {
       $stmt->execute();
       $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
   } else {
       $feedbackMessage = "Det går inte att ansluta till databasen just nu.";
   }

/*******************************************************************************
  END OF QUERY AND STMT THAT IS USED TO PRINT POST LIST
*******************************************************************************/
?>
<main>
    <?php if ($_SESSION["permission"] == 1): ?>
        <h2>Alla inlägg</h2>
    <?php else: ?>
        <h2>Dina inlägg</h2>
    <?php endif; ?>
    <form method="POST" action="./postlist.php">
        <table class="table-listing">
            <thead class="hidden">
                <td>Foto</td>
                <td>Rubrik</td>
                <td>Redigera</td>
                <td>Ta bort</td>
            </thead>
            <tbody>
                <?php while (mysqli_stmt_fetch($stmt)):

                    $draft = FALSE;
                    if ($published == 2) {
                        $draft = TRUE;
                        $modifier = "grayscale";
                    }
                ?>
                <tr class="table-listing__row">
                    <td class="relative-container">
                        <img src="../<?php echo $image; ?>" alt="Image of cats and space" class="full-width-img <?php if ($draft) { echo $modifier; } ?>">
                        <?php if ($draft) { echo $draftMessage; } ?>
                    </td>
                    <td class="relative-container">
                        <h3 class="table-listing__title table-listing__title--on-img"><?php echo formatInnerHtml($title); ?></h3>
                    </td>
                    <td class="relative-container">
                        <button type="submit" class="button" name="edit-post" value="<?php echo $id; ?>">Redigera</button>
                    </td>
                    <td class="relative-container">
                        <button type="submit" class="button error" name="delete-post" value="<?php echo $id; ?>">Ta bort</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
    <?php if ($feedbackMessage) { echo $feedbackMessage; } ?>
</main>
<?php require_once "../templates/footer.php"; ?>
