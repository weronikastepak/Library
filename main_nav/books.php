<?php
session_start();

if(isset($_POST['sort']))
{
    $sort = filter_input(INPUT_POST, 'a', FILTER_SANITIZE_STRING);
    if($sort == 'ASC'){  // Ascending Order
        $sql = "SELECT publishers.PUBLISHER, nationalities_languages.NAME, genres.GENRE, books.* FROM books "
                . "INNER JOIN nationalities_languages ON books.LANGUAGE=nationalities_languages.ID "
                . "INNER JOIN publishers ON books.PUBLISHER_ID=publishers.ID "
                . "INNER JOIN genres ON books.GENRE_ID=genres.ID "
                . "ORDER BY books.TITLE ASC";
    }
    else {  // Descending Order
        $sql = "SELECT publishers.PUBLISHER, nationalities_languages.NAME, genres.GENRE, books.* FROM books "
                . "INNER JOIN nationalities_languages ON books.LANGUAGE=nationalities_languages.ID "
                . "INNER JOIN publishers ON books.PUBLISHER_ID=publishers.ID "
                . "INNER JOIN genres ON books.GENRE_ID=genres.ID "
                . "ORDER BY books.TITLE DESC";        
    }
             
}
 else {      // Default Order
     $sql = "SELECT publishers.PUBLISHER, nationalities_languages.NAME, genres.GENRE, books.* FROM books "
             . "INNER JOIN nationalities_languages ON books.LANGUAGE=nationalities_languages.ID "
             . "INNER JOIN publishers ON books.PUBLISHER_ID=publishers.ID "
             . "INNER JOIN genres ON books.GENRE_ID=genres.ID";
 }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Library - Books</title>
        <link rel="stylesheet" href="../style/main_style.css" type="text/css">
        <link rel="stylesheet" href="../style/mysql_style.css" type="text/css">
        <!-- <script> - JavaScript -->
        <meta charset="UTF-8">
        <meta name="author" content="Niki"><!-- Author of a page -->
        <!--<meta http-equiv="refresh" content="30"> Refresh document every 30s -->       
        <meta name="viewport" content="width-device-width, initial-scale=1.0"><!-- setting the viewport to make website look good on all devices -->                
    </head>
    <body>
        <nav class="sign">
            <ol>
                <?php
                if((isset($_SESSION['logged'])) && ($_SESSION['logged']==true)){
                    echo'<li><a href="#">'.$_SESSION['name'].'</a>';
                    echo '<ul>';
                    echo '<li><a href="../sign_nav/account.php">my account</a></li>' ;
                    echo '<li><a href="../sign_nav/signOut.php">Sign Out</a></li>' ;
                    echo '</ul></li>';
                }
                elseif ((isset($_SESSION['librarian'])) && ($_SESSION['librarian']==true)) {
                    echo'<li><a href="#">'.$_SESSION['name'].'</a>';
                    echo '<ul>';
                    echo '<li><a href="sign_nav/signOut.php">Sign Out</a></li>' ;
                    echo '</ul></li>';
                }
                else {
                    echo '<li><a href="../sign_nav/signUp.php">Sign up</a></li>';
                    echo '<li><a href="../sign_nav/signIn.php">Sign in</a></li>';                      
                }
                ?>
            </ol>
        </nav>
        
        <header>
            <img src="../image/logo.png" alt="Logo">LIBRARY     
        </header>
        
        <nav class="main">
            <ol>
                <li><a href="#">books</a></li>
                <li><a href="authors.php">authors</a></li>
                <?php
                if((isset($_SESSION['librarian'])) && ($_SESSION['librarian']==true)){
                    echo '<li><a href="#">librarian</a>';
                    echo '<ul>';
                    echo '<li><a href="signLibrarian.php">Add librarian</a></li>' ;
                    echo '<li><a href="addBook.php">Add book</a></li>' ;
                    echo '</ul></li>';
                }
                ?>
                <li><a href="#">contact</a></li>
            </ol>
        </nav>
        
        <div id="container_main">
            <form method="post">
                <select name="a" id="a">
                    <option value="ASC">Title (A -> Z)</option>
                    <option value="DESC">Title (Z -> A)</option>
                </select>
                <input type="submit" name="sort" value="Sort">
            </form>
            
            <?php
            require_once '../connect.php';
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            try {
                $link = mysqli_connect($host, $db_user, $db_password, $db_name);
                
                if(mysqli_connect_errno()){
                    throw new Exception(mysqli_connect_errno());
                }
                else{
                    
                    $result = mysqli_query($link, $sql);
                    if(!$result){
                        throw new Exception(mysqli_error($link));
                    }
                    
                    if(mysqli_num_rows($result)>0){
                        while($row = mysqli_fetch_assoc($result)){
                            echo '<div class="row">';
                            echo '<div class="column">';
                            echo '<img src="../image.php?id='.$row['ID'].'&table=books" height=250px>';
                            echo '</div>';
                            echo '<div class="column">';
                            echo '<h1>'.$row['TITLE'].'</h1>';
                            
                            echo '<p><span>Author:</span> ';
                            $sql = "SELECT authors.FULL_NAME FROM `book_author`INNER JOIN authors ON AUTHOR_ID=authors.ID WHERE BOOK_ID=".$row['ID'];
                            $author_result = mysqli_query($link, $sql);
                            if(!$author_result){
                                throw new Exception(mysqli_error($link));
                            }
                            if(mysqli_num_rows($author_result)>0){
                                while($author = mysqli_fetch_assoc($author_result)){
                                    echo ' '.$author['FULL_NAME'].', ';
                                }
                            }
                            mysqli_free_result($author_result);

                            
                            echo '<br><span>Original title:</span> '.$row['ORIGINAL_TITLE'].'<br>';
                            
                            echo '<span>Genre:</span> '.$row['GENRE'].'<br>';               
                            echo '<span>Publisher:</span> '.$row['PUBLISHER'].'<br>';
                            echo '<span>ISBN:</span> '.$row['ISBN'].'<br>';
                            echo '<span>Language:</span> '.$row['NAME'].'<br>';
                            
                            if((isset($_SESSION['librarian'])) && ($_SESSION['librarian']==true)){
                                 echo '<button type="submit">Edit</button>';                                                                                               
                            }
                            else if((isset($_SESSION['logged'])) && ($_SESSION['logged']==true)){
                                echo '<button type="submit">Book</button>';                                
                            }
                            echo '</p></div></div>';
                        }
                        mysqli_free_result($result);
                    }
                    
                    mysqli_close($link);
                }
            } catch (Exception $ex) {
                echo 'Error! Failed to connect. Try again later.';
                echo 'Developer information: '.$ex;
            }
            ?>
        </div>

    </body>
</html>