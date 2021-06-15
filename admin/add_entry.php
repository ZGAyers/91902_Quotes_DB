<?php

// check if user is logged in 
if (isset($_SESSION['admin'])) {

    $author_ID = $_SESSION['Add_Quote'];

    if($author_ID=="unknown") {
        // get country & occupation lists from database
        $all_countiers_sql="SELECT * FROM `country` ORDER BY `Country` ASC ";
        $all_countries = autocomplete_list($dbconnect, $all_countiers_sql, 'Country');

        $all_occupations_sql="SELECT * FROM `career`ORDER BY `Career` ASC ";
        $all_occupations = autocomplete_list($dbconnect, $all_occupations_sql, 'Career');

    }

    // // initialise author variables
    $first = "";
    $middle = "";
    $last = "";
    $yob = "";
    $gender_code = "";
    $gender = "";
    $country_1 = "";
    $country_2 = "";
    $occupation_1 = "";
    $occupation_2 = "";

    // // Initialise country and occpuation ID's
    $country_1_ID = $country_2_ID = $occuptaion_1_ID = $occupation_2_ID = 0;

    // // set up error fields / visibility
    $last_error = $yob_error = $gender_error = 
    $country_1_error = $occupation_1_error = "no-error";

    $last_field = $yob_field = $gender_field = "form-ok";
    $country_1_field = $occupation_1_field = "tag-ok";


    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC";
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

    //  if author is unknown, get values from author part of form
    if($author_ID=="unknown") {
        $first = mysqli_real_escape_string($dbconnect, $_POST['first']);
        $middle = mysqli_real_escape_string($dbconnect, $_POST['middle']);
        $last = mysqli_real_escape_string($dbconnect, $_POST['last']);
        $yob = mysqli_real_escape_string($dbconnect, $_POST['yob']);

        $gender_code = mysqli_real_escape_string($dbconnect, $_POST['gender']);
        if ($gender_code=="F") {
            $gender = "Female";
        }
        else if ($gender_code=="M") {
            $gender = "Male";
        }

        else {
            $gender = "";
        }

        $country_1 = mysqli_real_escape_string($dbconnect, $_POST['country1']);
        $country_2 = mysqli_real_escape_string($dbconnect, $_POST['country2']);
        $occupation_1 = mysqli_real_escape_string($dbconnect, $_POST['occupation1']);
        $occupation_2 = mysqli_real_escape_string($dbconnect, $_POST['occupation2']);

        // Error checking goes here
        
        // check last name is not blank
        if ($last == "") {
            $has_errors = "yes";
            $last_error = "error-text";
            $last_field = "form-error";
        }

        // check year of birth is valid
        $valid_yob = isValidYear($yob);

        if($yob < 0 || $valid_yob != 1 || !preg_match('/^\d{1,4}$/', $yob))
        {
            $has_errors = "yes";
            $yob_error = "error-text";
            $yob_field = "form-error";
        }

        // check first country is not blank
        if ($country_1 == "") {
            $has_errors = "yes";
            $country_1_error = "error-text";
            $country_1_field = "tag-error";
        }

        // check occupation is not blank
        if ($occupation_1 == "") {
            $has_errors = "yes";
            $occupation_1_error = "error-text";
            $occupation_1__field = "tag-error";
        }




    } // end getting author values if 

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

        // add author to database if we have a new author... 
        if ($author_ID=="unknown")
        {
            // add author to database
            $add_author_sql = "";
            $add_author_query = mysqli_query($dbconnect, $add_author_sql);   
        }

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

<h1>Add Quote...</h1>

<form autocomplete="off" method="post" action="<?php 
echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/add_entry");?>">

    <?php
    // fields to add new author information

    if ($author_ID=="unknown") {
        ?>
        <!-- Author's first name, optional -->
        <input class="add-field" type="text" name="first" value="<?php echo 
        $first; ?>" placeholder="Author's First Name" />

        <br /> <br />

        <input class="add-field" type="text" name="middle" value="<?php echo 
        $middle ?>" placeholder="Author's Middle Name (optional)" />

        <br /> <br />

        <div class="<?php echo $last_error; ?>">
            Author's last name can't be blank
        </div>

        <input class="add-field" <?php echo $last_field; ?> type="text"
        name="last" value="<?php echo $last; ?>" placeholder="Author's Last Name"
        />

        <br /> <br />

        <select class="adv gender <?php echo $gender_field; ?>" name="gender">

            <?php 
            if($gender_code=="") {
                ?>
                <option value="" selected>Gender (Choose something)...
                </option>
                <?php
            } // end gender not chose if

            else {
                ?>
                    <option value="<?php echo $gender_code; ?>" selected>
                    <?php echo $gender; ?>
                    </option>

                <?php
            } //  end gender chosen else
            ?>
            <option value="F">Female</option>
            <option value="M">Male</option>
        
        </select>

        <br /> <br />
        
        <div class="<?php echo $yob_error; ?>">
            Please enter a valid year of birth (modern author's only).
        </div>

        <input class="add-field <?php echo $yob_field; ?>" type="text" name="yob"
        value="<?php echo $yob; ?>" placeholder="Author's year of birth" />

        <br /> <br />

        <div class="<?php echo $country_1_error; ?>">
            Please enter at least one country
        </div>

        <div class="autocomplete">
            <input class="<?php $country_1_field; ?>" id="country1" type="text"
            name="country1" value="<?php echo $country_1; ?>" placeholder="Country 1 (Required, Start Typing)...">
        </div>

        <br /> <br />

        <div class="autocomplete">
            <input id="country2" type="text" name="country2" value="<?php echo $country_2; ?>"
            placeholder="Country 2 (Start Typing)...">
        </div> 

        <br /> <br />

        <div class="<?php echo $occupation_1_error; ?>">
            Please enter at least one country
        </div>

        <div class="autocomplete">
            <input class="<?php echo $occupation_1_field; ?>" id="occupation1" type="text"
            name="occupation1" value="<?php echo $occupation_1; ?>" placeholder="Occupation 1 (Required, Start Typing)...">
        </div>
        <br /> <br />

        <div class="autocomplete">
            <input type="text" id="occupation2" name="occupation2" value="<?php echo $occupation_2; ?>"
            placeholder="Occupation 2 (Start Typing...)">
        </div>

        <br /> <br />

        <?php

    } // end new author fields


    ?>

    <!-- Quote text area -->

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
    
        <input id="subject3" type="text" name="Subject_3" placeholder="Subject 3 (Start Typing, optional)...">
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

var all_countries = <?php print("$all_countries"); ?>;
autocomplete(document.getElementById("country1"), all_countries);
autocomplete(document.getElementById("country2"), all_countries);

var all_occupations = <?php print("$all_occupations"); ?>;
autocomplete(document.getElementById("occupation1"), all_occupations);
autocomplete(document.getElementById("occupation2"), all_occupations);

</script>
