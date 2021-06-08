<?php

// check if user is logged in 
if (isset($_SESSION['admin'])) {

    $author_ID = $_SESSION['Add_Quote'];

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

// Code below excutes when the form is submitted...
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get data from form
    $quote = mysqli_real_escape_string($dbconnect, $_POST['quote']);
    $notes = mysqli_real_escape_string($dbconnect, $_POST['notes']);
    $tag_1 = mysqli_real_escape_string($dbconnect, $_POST['Subject_1']);
    $tag_2 = mysqli_real_escape_string($dbconnect, $_POST['Subject_2']);
    $tag_3 = mysqli_real_escape_string($dbconnect, $_POST['Subject_3']);

    // check data is valid

    // check quote is not blank
    if ($quote == "Please type your quote here") {
        $has_errors = "yes";
        $quote_error = "error-text";
        $quote_field = "form-error";
    }

    if ($tag_1 == "") {
        $has_errors = "yes";
        $tag_1_error = "error-text";
        $tag_1_field = "tag-error";
    }

    if($has_errors != "yes") {
        $subjectID_1 = get_ID($dbconnect, 'subject', 'SubjectID', 'Subject', $tag_1);
        $subjectID_2 = get_ID($dbconnect, 'subject', 'SubjectID', 'Subject', $tag_2);
        $subjectID_3 = get_ID($dbconnect, 'subject', 'SubjectID', 'Subject', $tag_3);

        // add entry to database
        $addentry_sql = "INSERT INTO `quotes` (`ID`, `Author_ID`, `Quote`, 
        `Notes`, `Subject1_ID`, `Subject2_ID`, `Subject3_ID`) VALUES 
        (NULL, '$author_ID', '$quote', '$notes', '$subjectID_1', '$subjectID_2', '$subjectID_3')";
        $addentry_query = mysqli_query($dbconnect, $addentry_sql);

        // get wuote ID for next page
        $get_quote_sql = "SELECT * FROM `quotes` WHERE `Quote` = '$quote'";
        $get_quote_query = mysqli_query($dbconnect, $get_quote_sql);
        $get_quote_rs = mysqli_fetch_assoc($get_quote_query);

        $quote_ID = $get_quote_rs['ID'];
        $_SESSION['Quote_Success']=$quote_ID;


    } // end has errors if

} // end submit button if

} // end user logged in if

else {

    $login_error = 'Please login to access this page';
    header("Location: index.php?page=../admin/login&error=$login_error");

} // end user not logged in else

?>

<h1>Add Quote...</h1>

<form autocomplete="off" method="post" action="<?php 
echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/add_entry");?>">

    <!-- Quote text area -->

    <!-- Quote entry in add entry - Required -->
    <div class="<?php echo $quote_error; ?>">
    This field can't be blank
    </div>

    <textarea class="add-field <?php echo $quote_field?>" name="quote" rows="6">
    <?php echo $quote; ?></textarea>

    <!-- Notes section in add entry -->
    <input class="add-field <?php echo $notes; ?>" type="text" name="notes" value="<?php
    echo $notes; ?>" placeholder="Notes (optional) ..."/>
    <br /> <br />

    <!-- Subject 1 entry in add entry - Required -->
    <div class="<?php echo $tag_1_error ?>">
        Please enter at least one subject tag
    </div>
    <div class="autocomplete">
        <input class="<?php echo $tag_1_field; ?>" id="subject1" type="text" name="Subject_1" 
        placeholder="Subject 1 (Start Typing...)">
    </div>

    <br /> <br />

    <!-- Subject 2 entry in add entry -->
    <div class="autocomplete">
    
        <input id="subject2" type="text" name="Subject_2" 
        placeholder="Subject 2 (Start Typing, optional)...">
    </div>

    <br /> <br />

    <!-- Subject 3 entry in add entry -->
    <div class="autocomplete">
    
        <input id="subject3" type="text" name="Subject_3" 
        placeholder="Subject 3 (Start Typing, optional)...">
    </div>

    <br /> <br />

    <!-- Submit Button -->
    <p>
        <input type="submit" vlaue="Submit" />
    </p>


</form>

<!-- script to make autocomplete work -->
<script>
<?php include("autocomplete.php"); ?>

/* Arrays containing lists */
var all_tags = <?php print("$all_subjects"); ?>;
autocomplete(document.getElementById("subject1"), all_tags);
autocomplete(document.getElementById("subject2"), all_tags);
autocomplete(document.getElementById("subject3"), all_tags);
</script>
