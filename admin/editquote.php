<?php

// check if user is logged in 
if (isset($_SESSION['admin'])) {

$ID = $_REQUEST['ID'];

    // Get author ID
    $find_sql = "SELECT * FROM `quotes`
    JOIN author ON (`author`.`Author_ID` = `quotes`.`Author_ID`) WHERE `quotes`.`ID` = $ID
    ";

    $find_query = mysqli_query($dbconnect, $find_sql);
    $find_rs = mysqli_fetch_assoc($find_query);

    $author_ID = $find_rs['Author_ID'];
    $first = $find_rs['First'];
    $middle = $find_rs['Middle'];
    $last = $find_rs['Last'];

    $current_author = $last." ".$first." ".$middle;

    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC";
    $all_subjects = autocomplete_list($dbconnect, $all_tags_sql, 'Subject');

    // Retireve data to populate the form... 
    $quote = $find_rs['Quote'];
    $notes = $find_rs['Notes'];

    // Get subjects to populate tags.. 
    $subject1_ID = $find_rs['Subject1_ID'];
    $subject2_ID = $find_rs['Subject2_ID'];
    $subject3_ID = $find_rs['Subject3_ID'];

    // Retrieve subject names from subject table... 
    $tag_1_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE Subject_ID = 
    $subject1_ID");
    $tag_1 = $tag_1_rs['Subject'];

    $tag_2_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE Subject_ID = 
    $subject2_ID");
    $tag_2 = $tag_2_rs['Subject'];

    $tag_3_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE Subject_ID = 
    $subject3_ID");
    $tag_3 = $tag_3_rs['Subject'];

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
    if ($quote == "Please type your quote here" || $quote == "") {
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

        // Get subject ID's via get_ID function
        $subjectID_1 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_1);
        $subjectID_2 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_2);
        $subjectID_3 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_3);

      // add entry to database
        $addentry_sql = "INSERT INTO `quotes` (`ID`, `Author_ID`, `Quote`, `Notes`, `Subject1_ID`, `Subject2_ID`, `Subject3_ID`) VALUES (NULL, '$author_ID', '$quote', '$notes', '$subjectID_1', '$subjectID_2', '$subjectID_3');";
        $addentry_query = mysqli_query($dbconnect, $addentry_sql);

        // get quote ID for next page
        $get_quote_sql = "SELECT * FROM `quotes` WHERE `Quote` = '$quote'";
        $get_quote_query = mysqli_query($dbconnect, $get_quote_sql);
        $get_quote_rs = mysqli_fetch_assoc($get_quote_query);

        $quote_ID = $get_quote_rs['ID'];
        $_SESSION['Quote_Success']=$quote_ID;

        // Go to success page...
        header('Location: index.php?page=quote_success');


    } // end has errors if

} // end submit button if

} // end user logged in if

else {

    $login_error = 'Please login to access this page';
    header("Location: index.php?page=../admin/login&error=$login_error");

} // end user not logged in else

// ?>

<h1>Edit Quote...</h1>

<form autocomplete="off" method="post" action="<?php 
echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/editquote&ID=$ID");?>">

    <b>Quote Author:</b> &nbsp;

    <select class="adv gender" name="author">
        <!-- Default option is new author -->
        <option value="<?php echo $author_ID; ?>" selected>
        <?php echo $current_author; ?>
        </option>

        <?php
        do {

        ?>

        <option value="<?php echo $author_ID; ?>">
            <?php echo $author_full; ?>
        </option>

        <?php


        } // end of author options 'do'

        while($all_authors_rs=mysqli_fetch_assoc($all_authors_query))

        ?>

    </select>


    <!-- Quote entry in add entry - Required -->
    <div class="<?php echo $quote_error; ?>">
        This field can't be blank
    </div>

    <textarea class="add-field <?php echo $quote_field?>" name="quote" rows="6"><?php echo $quote; ?></textarea>

    <!-- Notes section in add entry -->
    <input class="add-field <?php echo $notes; ?>" type="text" name="notes" value="<?php
    echo $notes; ?>" placeholder="Notes (optional) ..."/>
    <br /> <br />

    <!-- Subject 1 entry in add entry - Required -->
    <div class="<?php echo $tag_1_error; ?>">
        Please enter at least one subject tag
    </div>
    <div class="autocomplete">
        <input class="<?php echo $tag_1_field; ?>" id="subject1" type="text" name="Subject_1" 
        value="<?php echo $tag_1; ?>" placeholder="Subject 1 (Start Typing...)">
    </div>

     <br /> <br />

    <!-- Subject 2 entry in add entry -->
    <div class="autocomplete">
    
        <input id="subject2" type="text" name="Subject_2" value="<?php echo $tag_2; ?>"
        placeholder="Subject 2 (Start Typing, optional)...">
    </div>

    <br /> <br />

    <!-- Subject 3 entry in add entry -->
    <div class="autocomplete">
    
        <input id="subject3" type="text" name="Subject_3" value="<?php echo $tag_3; ?>"
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