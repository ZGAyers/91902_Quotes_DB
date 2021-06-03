<?php

// check if user is logged in 
if (isset($_SESSION['admin'])) {

    $author_ID = $_SESSION['Add_Quote'];
    echo "AuthorID: ".$author_ID;

    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC ";
    $all_subjects = autocomplete_list($dbconnect, $all_tags_sql, 'Subject');

    // initialise form variables for quote
    $quote = "Please type your quote here";
    $notes = "";
    $tag_1 = "";
    $tag_2 = "";
    $tag_3 = "";

// initialise tag ID's
$tag_1_ID = $tag_2_ID = $tag_3_ID = 0;

$has_errors = "no";

// set up error fields / visibility
$quote_error = $tag_1_error = "no-error";
$quote_field = "form-ok";
$tag_1_field = "tag-ok";

} // end user logged in if

else {

    $login_error = 'Please login to access this page';
    header("Location: index.php?page=../admin/login&error=$login_error");

} // end user not logged in else

?>

<h1>Add Quote...</h1>

<form autocomplete="off" method="post" action="action=<?php 
echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/add_entry");?>">

    <!-- Quote text area -->
    <div class="<?php echo $quote_error; ?>">
    This field can't be blank
    </div>

    <textarea class="add-field <?php echo $quote_field?>" name="quote" rows="6">
    <?php echo $quote; ?></textarea>
    <br /> <br />

    <!-- Submit Button -->
    <p>
        <input type="submit" vlaue="Submit" />
    </p>


</form>